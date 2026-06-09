<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExchangeRateController extends Controller
{
    public function index()
    {
        $rates = ExchangeRate::latest()->paginate(20);
        $current = ExchangeRate::current();
        return view('exchange-rates.index', compact('rates', 'current'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'usd_to_iqd' => 'required|numeric|min:1',
            'notes'      => 'nullable|string|max:500',
        ]);

        ExchangeRate::create([
            'usd_to_iqd'     => $validated['usd_to_iqd'],
            'notes'          => $validated['notes'] ?? null,
            'set_by'         => Auth::user()->name,
            'effective_from' => now(),
        ]);

        return back()->with('success', 'ڕێژەی گۆڕینی دراو نوێکرایەوە. مامەڵە کۆنەکان گۆڕانی تێدا نابێت.');
    }

    public function destroy(ExchangeRate $exchangeRate)
    {
        // Prevent deleting if it's the only rate
        if (ExchangeRate::count() <= 1) {
            return back()->with('error', 'ناتوانرێت تەنها ڕێژەکە بسڕدرێتەوە.');
        }

        $exchangeRate->delete();
        return back()->with('success', 'تۆمارەکە سڕایەوە.');
    }

    public function current()
    {
        $rate = ExchangeRate::currentRate();
        return response()->json(['usd_to_iqd' => $rate]);
    }
}
