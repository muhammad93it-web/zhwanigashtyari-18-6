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
        $totalBalance = (float) Supplier::sum('balance');

        return view('suppliers.index', compact('suppliers', 'totalBalance'));
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

        return view('suppliers.show', compact('supplier', 'transactions'));
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
}
