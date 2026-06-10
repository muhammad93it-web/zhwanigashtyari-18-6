<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\ExchangeRate;
use App\Traits\CalculatesCurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DebtController extends Controller
{
    use CalculatesCurrency;

    public function index(Request $request)
    {
        $query = Debt::query()->latest('debt_date');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('party_name', 'like', "%{$s}%")
                ->orWhere('description', 'like', "%{$s}%"));
        }
        if ($request->filled('direction')) {
            $query->where('direction', $request->direction);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $debts = $query->paginate(20)->withQueryString();

        // Open balances by direction
        $receivable = Debt::where('direction', 'receivable')->where('status', 'open')
            ->selectRaw('SUM(amount_usd) usd, SUM(amount_iqd) iqd')->first();
        $payable = Debt::where('direction', 'payable')->where('status', 'open')
            ->selectRaw('SUM(amount_usd) usd, SUM(amount_iqd) iqd')->first();

        return view('debts.index', compact('debts', 'receivable', 'payable'));
    }

    public function create()
    {
        return view('debts.create', ['currentRate' => ExchangeRate::currentRate()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'party_name'  => 'required|string|max:255',
            'direction'   => 'required|in:receivable,payable',
            'currency'    => 'required|in:USD,IQD',
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'debt_date'   => 'required|date',
            'due_date'    => 'nullable|date',
            'notes'       => 'nullable|string|max:1000',
        ]);

        $rate = ExchangeRate::currentRate();
        Debt::create(array_merge($data, $this->currencyAmounts($data['currency'], $data['amount'], $rate), [
            'exchange_rate_usd_to_iqd' => $rate,
            'status'  => 'open',
            'user_id' => Auth::id(),
        ]));

        return redirect()->route('debts.index')->with('success', 'قەرز تۆمارکرا.');
    }

    public function show(Debt $debt)
    {
        return view('debts.show', compact('debt'));
    }

    public function edit(Debt $debt)
    {
        return view('debts.edit', ['debt' => $debt, 'currentRate' => ExchangeRate::currentRate()]);
    }

    public function update(Request $request, Debt $debt)
    {
        $data = $request->validate([
            'party_name'  => 'required|string|max:255',
            'direction'   => 'required|in:receivable,payable',
            'currency'    => 'required|in:USD,IQD',
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'debt_date'   => 'required|date',
            'due_date'    => 'nullable|date',
            'notes'       => 'nullable|string|max:1000',
        ]);

        $rate = (float) $debt->exchange_rate_usd_to_iqd;
        $debt->update(array_merge($data, $this->currencyAmounts($data['currency'], $data['amount'], $rate)));

        return redirect()->route('debts.show', $debt)->with('success', 'زانیاری نوێکرایەوە.');
    }

    public function markPaid(Debt $debt)
    {
        $debt->update(['status' => 'paid', 'paid_date' => now()]);
        return back()->with('success', 'قەرزەکە وەک دراوەتەوە نیشانکرا.');
    }

    public function destroy(Debt $debt)
    {
        $debt->delete();
        return redirect()->route('debts.index')->with('success', 'سڕایەوە.');
    }
}
