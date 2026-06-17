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
        // Each user only sees their own purchase history.
        $query = PurchaseInvoice::query()
            ->where('user_id', Auth::id())
            ->with(['supplier', 'user'])
            ->latest('date')
            ->latest('id');

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        $totals = (clone $query)->reorder()
            ->selectRaw('SUM(total_amount) total, SUM(paid_amount) paid, SUM(remaining_amount) remaining')
            ->first();

        $invoices = $query->paginate(20)->withQueryString();
        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);

        return view('purchase-invoices.index', compact('invoices', 'totals', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $materials = Material::where('is_active', true)->orderBy('name')->get(['id', 'name', 'unit']);
        $projects  = Project::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('purchase-invoices.create', compact('suppliers', 'materials', 'projects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id'          => 'required|exists:suppliers,id',
            'date'                 => 'required|date',
            'paid_amount'          => 'nullable|numeric|min:0',
            'notes'                => 'nullable|string|max:1000',
            'lines'                => 'required|array|min:1',
            'lines.*.material_id'  => 'nullable|exists:materials,id',
            'lines.*.custom_type'  => 'nullable|string|max:255',
            'lines.*.unit'         => 'nullable|string|max:50',
            'lines.*.quantity'     => 'required|numeric|min:0.001',
            'lines.*.unit_price'   => 'required|numeric|min:0',
            'lines.*.project_id'   => 'nullable|exists:projects,id',
        ]);

        // Every line must describe what was bought: an existing material OR a free-text type.
        foreach ($data['lines'] as $i => $line) {
            if (empty($line['material_id']) && empty(trim($line['custom_type'] ?? ''))) {
                return back()->withInput()->with('error', 'هەر هێڵێک پێویستە مەوادێک یان جۆرێکی دەستی هەبێت (هێڵی ' . ($i + 1) . ').');
            }
        }

        $total = 0;
        foreach ($data['lines'] as $line) {
            $total += round((float) $line['quantity'] * (float) $line['unit_price'], 2);
        }
        $paid = (float) ($data['paid_amount'] ?? 0);
        if ($paid > $total) {
            return back()->withInput()->with('error', 'بڕی دراو ناتوانێت زیاتر بێت لە کۆی وەسڵەکە.');
        }
        $remaining = round($total - $paid, 2);

        DB::transaction(function () use ($data, $total, $paid, $remaining) {
            $supplier = Supplier::lockForUpdate()->find($data['supplier_id']);

            $invoice = PurchaseInvoice::create([
                'supplier_id'      => $supplier->id,
                'user_id'          => Auth::id(),
                'total_amount'     => $total,
                'paid_amount'      => $paid,
                'remaining_amount' => $remaining,
                'date'             => $data['date'],
                'notes'            => $data['notes'] ?? null,
            ]);

            foreach ($data['lines'] as $line) {
                $lineTotal = round((float) $line['quantity'] * (float) $line['unit_price'], 2);

                PurchaseInvoiceDetail::create([
                    'purchase_invoice_id' => $invoice->id,
                    'material_id'         => $line['material_id'] ?? null,
                    'custom_type'         => $line['custom_type'] ?? null,
                    'unit'                => $line['unit'] ?? null,
                    'quantity'            => $line['quantity'],
                    'unit_price'          => $line['unit_price'],
                    'line_total'          => $lineTotal,
                    'project_id'          => $line['project_id'] ?? null,
                ]);

                // Increase inventory only for lines tied to a tracked material.
                if (! empty($line['material_id'])) {
                    $material = Material::lockForUpdate()->find($line['material_id']);
                    if ($material) {
                        $material->current_stock = (float) $material->current_stock + (float) $line['quantity'];
                        $material->save();
                    }
                }
            }

            // Supplier ledger: purchase raises what we owe, payment lowers it.
            $balance = (float) $supplier->balance + $total;
            SupplierTransaction::create([
                'supplier_id'   => $supplier->id,
                'user_id'       => Auth::id(),
                'type'          => 'purchase',
                'amount'        => $total,
                'balance_after' => $balance,
                'date'          => $data['date'],
                'description'   => 'وەسڵی کڕین #' . $invoice->id,
            ]);

            if ($paid > 0) {
                $balance = round($balance - $paid, 2);
                SupplierTransaction::create([
                    'supplier_id'   => $supplier->id,
                    'user_id'       => Auth::id(),
                    'type'          => 'payment',
                    'amount'        => $paid,
                    'balance_after' => $balance,
                    'date'          => $data['date'],
                    'description'   => 'پارەدان لەگەڵ وەسڵی کڕین #' . $invoice->id,
                ]);
            }

            $supplier->balance = $balance;
            $supplier->save();
        });

        return redirect()->route('purchase-invoices.index')->with('success', 'وەسڵی کڕین تۆمارکرا، کۆگا و باڵانسی دابینکەر نوێکرانەوە.');
    }

    public function show(PurchaseInvoice $purchaseInvoice)
    {
        abort_unless($purchaseInvoice->user_id === Auth::id(), 403);

        $purchaseInvoice->load(['supplier', 'user', 'details.material', 'details.project']);

        return view('purchase-invoices.show', compact('purchaseInvoice'));
    }

    public function destroy(PurchaseInvoice $purchaseInvoice)
    {
        abort_unless($purchaseInvoice->user_id === Auth::id(), 403);

        DB::transaction(function () use ($purchaseInvoice) {
            // Lock and re-read; bail out if a concurrent request already deleted it.
            $invoice = PurchaseInvoice::lockForUpdate()->find($purchaseInvoice->id);
            if (! $invoice) {
                return;
            }
            $invoice->load('details');
            $supplier = Supplier::lockForUpdate()->find($invoice->supplier_id);

            // Reverse inventory for material-backed lines.
            foreach ($invoice->details as $detail) {
                if (! empty($detail->material_id)) {
                    $material = Material::lockForUpdate()->find($detail->material_id);
                    if ($material) {
                        $material->current_stock = (float) $material->current_stock - (float) $detail->quantity;
                        $material->save();
                    }
                }
            }

            // Reverse the supplier ledger with coherent entries so every row's
            // balance_after equals the prior balance adjusted by its own signed type.
            if ($supplier) {
                $balance = (float) $supplier->balance;

                // Undo the original purchase: lower the debt by the full invoice total.
                $balance = round($balance - (float) $invoice->total_amount, 2);
                SupplierTransaction::create([
                    'supplier_id'   => $supplier->id,
                    'user_id'       => Auth::id(),
                    'type'          => 'payment',
                    'amount'        => $invoice->total_amount,
                    'balance_after' => $balance,
                    'date'          => now()->toDateString(),
                    'description'   => 'گەڕاندنەوەی کڕینی سڕاوە #' . $invoice->id,
                ]);

                // Undo the original payment (if any): raise the debt back by the paid amount.
                if ((float) $invoice->paid_amount > 0) {
                    $balance = round($balance + (float) $invoice->paid_amount, 2);
                    SupplierTransaction::create([
                        'supplier_id'   => $supplier->id,
                        'user_id'       => Auth::id(),
                        'type'          => 'purchase',
                        'amount'        => $invoice->paid_amount,
                        'balance_after' => $balance,
                        'date'          => now()->toDateString(),
                        'description'   => 'هەڵوەشاندنەوەی پارەدانی کڕینی سڕاوە #' . $invoice->id,
                    ]);
                }

                $supplier->balance = $balance;
                $supplier->save();
            }

            $invoice->delete();
        });

        return redirect()->route('purchase-invoices.index')->with('success', 'وەسڵی کڕین سڕایەوە و کۆگا و باڵانس ڕاستکرانەوە.');
    }
}
