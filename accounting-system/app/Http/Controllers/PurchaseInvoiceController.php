<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Project;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceDetail;
use App\Models\Supplier;
use App\Models\SupplierTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceController extends Controller
{
    public function index(Request $request)
    {
        // Shared company books: everyone sees all purchase invoices.
        $query = PurchaseInvoice::query()
            ->with(['supplier', 'user', 'project'])
            ->latest('date')
            ->latest('id');

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('incoming_invoice_number', 'like', $term)
                  ->orWhere('deliverer_name', 'like', $term);
            });
        }
        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        $totals = (clone $query)->reorder()
            ->selectRaw('SUM(total_iqd) total_iqd, SUM(total_usd) total_usd, SUM(paid_iqd) paid_iqd, SUM(paid_usd) paid_usd, SUM(remaining_iqd) remaining_iqd, SUM(remaining_usd) remaining_usd')
            ->first();

        $invoices = $query->paginate(20)->withQueryString();
        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);

        return view('purchase-invoices.index', compact('invoices', 'totals', 'suppliers'));
    }

    public function create()
    {
        return view('purchase-invoices.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validateInvoice($request);

        if ($error = $this->lineDescriptionError($data['lines'])) {
            return back()->withInput()->with('error', $error);
        }

        [$totalIqd, $totalUsd] = $this->computeLineTotals($data['lines']);
        $paidIqd = round((float) ($data['paid_iqd'] ?? 0), 2);
        $paidUsd = round((float) ($data['paid_usd'] ?? 0), 2);

        if ($paidIqd > $totalIqd) {
            return back()->withInput()->with('error', 'بڕی دراوی دیناری ناتوانێت زیاتر بێت لە کۆی دیناری وەسڵەکە.');
        }
        if ($paidUsd > $totalUsd) {
            return back()->withInput()->with('error', 'بڕی دراوی دۆلاری ناتوانێت زیاتر بێت لە کۆی دۆلاری وەسڵەکە.');
        }

        DB::transaction(function () use ($data, $totalIqd, $totalUsd, $paidIqd, $paidUsd) {
            $invoice = PurchaseInvoice::create([
                'incoming_invoice_number' => $data['incoming_invoice_number'] ?? null,
                'supplier_id'      => $data['supplier_id'] ?? null,
                'deliverer_name'   => $data['deliverer_name'] ?? null,
                'deliverer_phone'  => $data['deliverer_phone'] ?? null,
                'deliverer_address' => $data['deliverer_address'] ?? null,
                'vehicle_number'   => $data['vehicle_number'] ?? null,
                'vehicle_type'     => $data['vehicle_type'] ?? null,
                'user_id'          => Auth::id(),
                'project_id'       => $data['project_id'] ?? null,
                'total_iqd'        => $totalIqd,
                'total_usd'        => $totalUsd,
                'paid_iqd'         => $paidIqd,
                'paid_usd'         => $paidUsd,
                'remaining_iqd'    => round($totalIqd - $paidIqd, 2),
                'remaining_usd'    => round($totalUsd - $paidUsd, 2),
                // Legacy single-currency columns kept = IQD figures for backward compat.
                'total_amount'     => $totalIqd,
                'paid_amount'      => $paidIqd,
                'remaining_amount' => round($totalIqd - $paidIqd, 2),
                'date'             => $data['date'],
                'notes'            => $data['notes'] ?? null,
            ]);

            $this->createDetailsAndStock($invoice, $data['lines']);

            if (! empty($invoice->supplier_id)) {
                $supplier = Supplier::lockForUpdate()->find($invoice->supplier_id);
                if ($supplier) {
                    $this->applyLedger($supplier, $invoice);
                }
            }
        });

        return redirect()->route('purchase-invoices.index')->with('success', 'وەسڵی کڕین تۆمارکرا، کۆگا و باڵانسی دابینکەر نوێکرانەوە.');
    }

    public function show(PurchaseInvoice $purchaseInvoice)
    {

        $purchaseInvoice->load(['supplier', 'user', 'project', 'details.material', 'details.project']);

        return view('purchase-invoices.show', compact('purchaseInvoice'));
    }

    public function edit(PurchaseInvoice $purchaseInvoice)
    {

        $purchaseInvoice->load(['details']);

        return view('purchase-invoices.edit', array_merge($this->formData(), ['invoice' => $purchaseInvoice]));
    }

    public function update(Request $request, PurchaseInvoice $purchaseInvoice)
    {

        $data = $this->validateInvoice($request);

        if ($error = $this->lineDescriptionError($data['lines'])) {
            return back()->withInput()->with('error', $error);
        }

        [$totalIqd, $totalUsd] = $this->computeLineTotals($data['lines']);
        $paidIqd = round((float) ($data['paid_iqd'] ?? 0), 2);
        $paidUsd = round((float) ($data['paid_usd'] ?? 0), 2);

        if ($paidIqd > $totalIqd) {
            return back()->withInput()->with('error', 'بڕی دراوی دیناری ناتوانێت زیاتر بێت لە کۆی دیناری وەسڵەکە.');
        }
        if ($paidUsd > $totalUsd) {
            return back()->withInput()->with('error', 'بڕی دراوی دۆلاری ناتوانێت زیاتر بێت لە کۆی دۆلاری وەسڵەکە.');
        }

        DB::transaction(function () use ($data, $purchaseInvoice, $totalIqd, $totalUsd, $paidIqd, $paidUsd) {
            $invoice = PurchaseInvoice::lockForUpdate()->find($purchaseInvoice->id);
            if (! $invoice) {
                return;
            }
            $invoice->load('details');

            // Fully reverse the old effects (stock + ledger), then reapply with new data.
            $this->reverseStock($invoice);
            $this->reverseLedger($invoice);
            $invoice->details()->delete();

            $invoice->update([
                'incoming_invoice_number' => $data['incoming_invoice_number'] ?? null,
                'supplier_id'      => $data['supplier_id'] ?? null,
                'deliverer_name'   => $data['deliverer_name'] ?? null,
                'deliverer_phone'  => $data['deliverer_phone'] ?? null,
                'deliverer_address' => $data['deliverer_address'] ?? null,
                'vehicle_number'   => $data['vehicle_number'] ?? null,
                'vehicle_type'     => $data['vehicle_type'] ?? null,
                'project_id'       => $data['project_id'] ?? null,
                'total_iqd'        => $totalIqd,
                'total_usd'        => $totalUsd,
                'paid_iqd'         => $paidIqd,
                'paid_usd'         => $paidUsd,
                'remaining_iqd'    => round($totalIqd - $paidIqd, 2),
                'remaining_usd'    => round($totalUsd - $paidUsd, 2),
                'total_amount'     => $totalIqd,
                'paid_amount'      => $paidIqd,
                'remaining_amount' => round($totalIqd - $paidIqd, 2),
                'date'             => $data['date'],
                'notes'            => $data['notes'] ?? null,
            ]);

            $this->createDetailsAndStock($invoice, $data['lines']);

            if (! empty($invoice->supplier_id)) {
                $supplier = Supplier::lockForUpdate()->find($invoice->supplier_id);
                if ($supplier) {
                    $this->applyLedger($supplier, $invoice);
                }
            }
        });

        return redirect()->route('purchase-invoices.show', $purchaseInvoice)->with('success', 'وەسڵی کڕین نوێکرایەوە، کۆگا و باڵانس ڕاستکرانەوە.');
    }

    public function destroy(PurchaseInvoice $purchaseInvoice)
    {

        DB::transaction(function () use ($purchaseInvoice) {
            $invoice = PurchaseInvoice::lockForUpdate()->find($purchaseInvoice->id);
            if (! $invoice) {
                return;
            }
            $invoice->load('details');

            $this->reverseStock($invoice);
            $this->reverseLedger($invoice);

            $invoice->delete();
        });

        return redirect()->route('purchase-invoices.index')->with('success', 'وەسڵی کڕین سڕایەوە و کۆگا و باڵانس ڕاستکرانەوە.');
    }

    public function print(PurchaseInvoice $purchaseInvoice)
    {

        $purchaseInvoice->load(['supplier', 'user', 'project', 'details.material', 'details.project']);

        return view('purchase-invoices.print', [
            'invoice' => $purchaseInvoice,
            'logo'    => $this->logoDataUri(),
        ]);
    }

    public function exportExcel(PurchaseInvoice $purchaseInvoice)
    {

        $purchaseInvoice->load(['supplier', 'user', 'project', 'details.material', 'details.project']);

        $html = view('purchase-invoices.export-excel', [
            'invoice' => $purchaseInvoice,
            'logo'    => $this->logoDataUri(),
        ])->render();

        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="purchase-invoice-' . $purchaseInvoice->id . '.xls"',
        ]);
    }

    public function exportWord(PurchaseInvoice $purchaseInvoice)
    {

        $purchaseInvoice->load(['supplier', 'user', 'project', 'details.material', 'details.project']);

        $html = view('purchase-invoices.export-word', [
            'invoice' => $purchaseInvoice,
            'logo'    => $this->logoDataUri(),
        ])->render();

        return response($html, 200, [
            'Content-Type'        => 'application/msword; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="purchase-invoice-' . $purchaseInvoice->id . '.doc"',
        ]);
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------

    private function formData(): array
    {
        return [
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'materials' => Material::where('is_active', true)->orderBy('name')->get(['id', 'name', 'unit']),
            'projects'  => Project::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ];
    }

    private function validateInvoice(Request $request): array
    {
        $data = $request->validate([
            'incoming_invoice_number' => 'nullable|string|max:255',
            'supplier_id'         => 'nullable|exists:suppliers,id',
            'deliverer_name'      => 'nullable|string|max:255',
            'deliverer_phone'     => 'nullable|string|max:255',
            'deliverer_address'   => 'nullable|string|max:255',
            'vehicle_number'      => 'nullable|string|max:255',
            'vehicle_type'        => 'nullable|string|max:255',
            'project_id'          => 'nullable|exists:projects,id',
            'date'                => 'required|date',
            'paid_iqd'            => 'nullable|numeric|min:0',
            'paid_usd'            => 'nullable|numeric|min:0',
            'notes'               => 'nullable|string|max:1000',
            'lines'               => 'required|array|min:1',
            'lines.*.material_id' => 'nullable|exists:materials,id',
            'lines.*.custom_type' => 'nullable|string|max:255',
            'lines.*.unit'        => 'nullable|string|max:50',
            'lines.*.quantity'    => 'required|numeric|min:0.001',
            'lines.*.unit_price'  => 'required|numeric|min:0',
            'lines.*.currency'    => 'required|in:IQD,USD',
        ]);

        // Need to know who the materials came from.
        if (empty($data['supplier_id']) && empty(trim($data['deliverer_name'] ?? ''))) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'deliverer_name' => 'پێویستە دابینکەر هەڵبژێریت یان ناوی گەیەنەری مەواد بنووسیت.',
            ]);
        }

        return $data;
    }

    private function lineDescriptionError(array $lines): ?string
    {
        foreach ($lines as $i => $line) {
            if (empty($line['material_id']) && empty(trim($line['custom_type'] ?? ''))) {
                return 'هەر هێڵێک پێویستە مەوادێک یان جۆرێکی دەستی هەبێت (هێڵی ' . ($i + 1) . ').';
            }
        }

        return null;
    }

    private function computeLineTotals(array $lines): array
    {
        $totalIqd = 0;
        $totalUsd = 0;
        foreach ($lines as $line) {
            $lineTotal = round((float) $line['quantity'] * (float) $line['unit_price'], 2);
            if (($line['currency'] ?? 'IQD') === 'USD') {
                $totalUsd += $lineTotal;
            } else {
                $totalIqd += $lineTotal;
            }
        }

        return [round($totalIqd, 2), round($totalUsd, 2)];
    }

    private function createDetailsAndStock(PurchaseInvoice $invoice, array $lines): void
    {
        foreach ($lines as $line) {
            $lineTotal = round((float) $line['quantity'] * (float) $line['unit_price'], 2);

            PurchaseInvoiceDetail::create([
                'purchase_invoice_id' => $invoice->id,
                'material_id'         => $line['material_id'] ?? null,
                'custom_type'         => $line['custom_type'] ?? null,
                'unit'                => $line['unit'] ?? null,
                'quantity'            => $line['quantity'],
                'unit_price'          => $line['unit_price'],
                'line_total'          => $lineTotal,
                'currency'            => ($line['currency'] ?? 'IQD') === 'USD' ? 'USD' : 'IQD',
                'project_id'          => $invoice->project_id,
            ]);

            if (! empty($line['material_id'])) {
                $material = Material::lockForUpdate()->find($line['material_id']);
                if ($material) {
                    $material->current_stock = (float) $material->current_stock + (float) $line['quantity'];
                    $material->save();
                }
            }
        }
    }

    private function reverseStock(PurchaseInvoice $invoice): void
    {
        foreach ($invoice->details as $detail) {
            if (! empty($detail->material_id)) {
                $material = Material::lockForUpdate()->find($detail->material_id);
                if ($material) {
                    $material->current_stock = (float) $material->current_stock - (float) $detail->quantity;
                    $material->save();
                }
            }
        }
    }

    /**
     * Add per-currency ledger entries + balances for a supplier-backed invoice.
     * IQD and USD are tracked as fully independent running balances.
     */
    private function applyLedger(Supplier $supplier, PurchaseInvoice $invoice): void
    {
        $currencies = [
            'IQD' => ['total' => (float) $invoice->total_iqd, 'paid' => (float) $invoice->paid_iqd, 'field' => 'balance_iqd'],
            'USD' => ['total' => (float) $invoice->total_usd, 'paid' => (float) $invoice->paid_usd, 'field' => 'balance_usd'],
        ];

        foreach ($currencies as $cur => $v) {
            if ($v['total'] <= 0 && $v['paid'] <= 0) {
                continue;
            }
            $field = $v['field'];
            $balance = (float) $supplier->$field;

            if ($v['total'] > 0) {
                $balance = round($balance + $v['total'], 2);
                SupplierTransaction::create([
                    'supplier_id'   => $supplier->id,
                    'user_id'       => Auth::id(),
                    'type'          => 'purchase',
                    'currency'      => $cur,
                    'amount'        => $v['total'],
                    'balance_after' => $balance,
                    'date'          => $invoice->date,
                    'description'   => 'وەسڵی کڕین #' . $invoice->id,
                ]);
            }

            if ($v['paid'] > 0) {
                $balance = round($balance - $v['paid'], 2);
                SupplierTransaction::create([
                    'supplier_id'   => $supplier->id,
                    'user_id'       => Auth::id(),
                    'type'          => 'payment',
                    'currency'      => $cur,
                    'amount'        => $v['paid'],
                    'balance_after' => $balance,
                    'date'          => $invoice->date,
                    'description'   => 'پارەدان لەگەڵ وەسڵی کڕین #' . $invoice->id,
                ]);
            }

            $supplier->$field = $balance;
        }

        // Keep legacy single-currency balance = IQD balance for backward compat.
        $supplier->balance = $supplier->balance_iqd;
        $supplier->save();
    }

    /**
     * Reverse the per-currency ledger effect of an invoice on its supplier.
     */
    private function reverseLedger(PurchaseInvoice $invoice): void
    {
        if (empty($invoice->supplier_id)) {
            return;
        }
        $supplier = Supplier::lockForUpdate()->find($invoice->supplier_id);
        if (! $supplier) {
            return;
        }

        $currencies = [
            'IQD' => ['total' => (float) $invoice->total_iqd, 'paid' => (float) $invoice->paid_iqd, 'field' => 'balance_iqd'],
            'USD' => ['total' => (float) $invoice->total_usd, 'paid' => (float) $invoice->paid_usd, 'field' => 'balance_usd'],
        ];

        foreach ($currencies as $cur => $v) {
            if ($v['total'] <= 0 && $v['paid'] <= 0) {
                continue;
            }
            $field = $v['field'];
            $balance = (float) $supplier->$field;

            // Undo the purchase: lower the debt by the full invoice total.
            if ($v['total'] > 0) {
                $balance = round($balance - $v['total'], 2);
                SupplierTransaction::create([
                    'supplier_id'   => $supplier->id,
                    'user_id'       => Auth::id(),
                    'type'          => 'payment',
                    'currency'      => $cur,
                    'amount'        => $v['total'],
                    'balance_after' => $balance,
                    'date'          => now()->toDateString(),
                    'description'   => 'گەڕاندنەوەی کڕینی #' . $invoice->id,
                ]);
            }

            // Undo the payment: raise the debt back by the paid amount.
            if ($v['paid'] > 0) {
                $balance = round($balance + $v['paid'], 2);
                SupplierTransaction::create([
                    'supplier_id'   => $supplier->id,
                    'user_id'       => Auth::id(),
                    'type'          => 'purchase',
                    'currency'      => $cur,
                    'amount'        => $v['paid'],
                    'balance_after' => $balance,
                    'date'          => now()->toDateString(),
                    'description'   => 'هەڵوەشاندنەوەی پارەدانی کڕینی #' . $invoice->id,
                ]);
            }

            $supplier->$field = $balance;
        }

        $supplier->balance = $supplier->balance_iqd;
        $supplier->save();
    }

    private function logoDataUri(): string
    {
        $path = public_path('images/logo.png');
        if (is_file($path)) {
            return 'data:image/png;base64,' . base64_encode((string) file_get_contents($path));
        }

        return '';
    }
}
