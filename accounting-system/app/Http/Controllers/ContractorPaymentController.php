<?php

namespace App\Http\Controllers;

use App\Models\Contractor;
use App\Models\ContractorPayment;
use App\Models\ExchangeRate;
use App\Traits\CalculatesCurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractorPaymentController extends Controller
{
    use CalculatesCurrency;

    public function index(Request $request)
    {
        $query = ContractorPayment::with('contractor')->latest('payment_date');

        if ($request->filled('contractor_id')) {
            $query->where('contractor_id', $request->contractor_id);
        }

        $totals = (clone $query)->selectRaw('SUM(amount_usd) usd, SUM(amount_iqd) iqd, COUNT(*) c')->first();
        $payments = $query->paginate(20)->withQueryString();
        $contractors = Contractor::orderBy('name')->get(['id', 'name']);

        return view('contractor-payments.index', compact('payments', 'contractors', 'totals'));
    }

    public function create(Request $request)
    {
        $contractors = Contractor::where('is_active', true)->orderBy('name')->get();
        $currentRate = ExchangeRate::currentRate();
        $selected = $request->filled('contractor_id') ? Contractor::find($request->contractor_id) : null;

        return view('contractor-payments.create', compact('contractors', 'currentRate', 'selected'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contractor_id' => 'required|exists:contractors,id',
            'currency'      => 'required|in:USD,IQD',
            'amount'        => 'required|numeric|min:0.01',
            'meters'        => 'nullable|numeric|min:0',
            'description'   => 'nullable|string|max:500',
            'payment_date'  => 'required|date',
            'notes'         => 'nullable|string|max:1000',
        ]);

        $rate = ExchangeRate::currentRate();
        ContractorPayment::create(array_merge($data, $this->currencyAmounts($data['currency'], $data['amount'], $rate), [
            'exchange_rate_usd_to_iqd' => $rate,
            'user_id' => Auth::id(),
        ]));

        return redirect()->route('contractors.show', $data['contractor_id'])->with('success', 'پارەدان بۆ وەستا تۆمارکرا.');
    }

    public function destroy(ContractorPayment $contractorPayment)
    {
        $contractorPayment->delete();
        return back()->with('success', 'پارەدانەکە سڕایەوە.');
    }
}
