<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query()->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('phone', 'like', "%{$s}%"));
        }

        $suppliers = $query->paginate(20)->withQueryString();
        $totalIqd = (float) Supplier::sum('balance_iqd');
        $totalUsd = (float) Supplier::sum('balance_usd');

        return view('suppliers.index', compact('suppliers', 'totalIqd', 'totalUsd'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'phone'     => 'nullable|string|max:50',
            'notes'     => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['balance'] = 0;

        Supplier::create($data);

        return redirect()->route('suppliers.index')->with('success', 'دابینکەر زیادکرا.');
    }

    public function show(Supplier $supplier)
    {
        $transactions = $supplier->transactions()
            ->with('user')
            ->latest('date')
            ->latest('id')
            ->paginate(25);

        $summary = $this->summary($supplier);

        return view('suppliers.show', compact('supplier', 'transactions', 'summary'));
    }

    /** Per-currency totals: how much purchased, paid, and the remaining balance. */
    private function summary(Supplier $supplier): array
    {
        $rows = $supplier->transactions()
            ->selectRaw("currency, type, SUM(amount) total")
            ->groupBy('currency', 'type')
            ->get();

        $out = [
            'IQD' => ['purchase' => 0.0, 'payment' => 0.0, 'balance' => (float) $supplier->balance_iqd],
            'USD' => ['purchase' => 0.0, 'payment' => 0.0, 'balance' => (float) $supplier->balance_usd],
        ];

        foreach ($rows as $r) {
            $cur = $r->currency === 'USD' ? 'USD' : 'IQD';
            $type = $r->type === 'payment' ? 'payment' : 'purchase';
            $out[$cur][$type] += (float) $r->total;
        }

        return $out;
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'phone'     => 'nullable|string|max:50',
            'notes'     => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);

        $supplier->update($data);

        return redirect()->route('suppliers.show', $supplier)->with('success', 'زانیاری نوێکرایەوە.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->transactions()->exists() || $supplier->purchaseInvoices()->exists()) {
            return back()->with('error', 'ناتوانرێت بسڕدرێتەوە چونکە مامەڵە یان وەسڵی کڕینی پێوەیە.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'سڕایەوە.');
    }

    /** کەشف حیساب: لیستی کەسەکان + هەڵبژاردنی ناو بۆ بینینی کەشف حساب. */
    public function statements(Request $request)
    {
        $query = Supplier::query()->orderBy('name');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('phone', 'like', "%{$s}%"));
        }

        $suppliers = $query->paginate(30)->withQueryString();
        $allSuppliers = Supplier::orderBy('name')->get(['id', 'name']);

        $totals = [
            'IQD' => (float) Supplier::sum('balance_iqd'),
            'USD' => (float) Supplier::sum('balance_usd'),
        ];

        return view('suppliers.statements', compact('suppliers', 'allSuppliers', 'totals'));
    }

    /** ڕێکردنی هەڵبژاردنی ناو لە فۆڕمی کەشف حساب. */
    public function statementGo(Request $request)
    {
        $request->validate(['supplier_id' => 'required|exists:suppliers,id']);

        return redirect()->route('suppliers.show', $request->supplier_id);
    }

    public function statementPrint(Supplier $supplier)
    {
        return view('suppliers.statement-print', $this->statementData($supplier) + ['logo' => $this->logoDataUri()]);
    }

    public function statementExcel(Supplier $supplier)
    {
        $html = view('suppliers.statement-excel', $this->statementData($supplier))->render();

        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="statement-' . $supplier->id . '.xls"',
        ]);
    }

    public function statementWord(Supplier $supplier)
    {
        $html = view('suppliers.statement-word', $this->statementData($supplier))->render();

        return response($html, 200, [
            'Content-Type'        => 'application/msword; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="statement-' . $supplier->id . '.doc"',
        ]);
    }

    private function statementData(Supplier $supplier): array
    {
        $transactions = $supplier->transactions()
            ->with('user')
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        return [
            'supplier'     => $supplier,
            'transactions' => $transactions,
            'summary'      => $this->summary($supplier),
        ];
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
