<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierPaymentController extends Controller
{
    public function create(Supplier $supplier)
    {
        return view('suppliers.pay', compact('supplier'));
    }

    public function store(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'amount'      => 'required|numeric|min:0.01',
            'date'        => 'required|date',
            'description' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($supplier, $data) {
            $locked = Supplier::lockForUpdate()->find($supplier->id);
            $newBalance = (float) $locked->balance - (float) $data['amount'];

            SupplierTransaction::create([
                'supplier_id'   => $locked->id,
                'user_id'       => Auth::id(),
                'type'          => 'payment',
                'amount'        => $data['amount'],
                'balance_after' => $newBalance,
                'date'          => $data['date'],
                'description'   => $data['description'] ?? 'پارەدان بۆ دابینکەر',
            ]);

            $locked->balance = $newBalance;
            $locked->save();
        });

        return redirect()->route('suppliers.show', $supplier)->with('success', 'پارەدان تۆمارکرا.');
    }
}
