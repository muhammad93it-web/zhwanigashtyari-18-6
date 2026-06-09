<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Models\Income;
use App\Traits\CalculatesCurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    use CalculatesCurrency;

    public function index(Request $request)
    {
        $query = Income::query()->latest('income_date');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('source', 'like', "%{$s}%")
                ->orWhere('description', 'like', "%{$s}%")
                ->orWhere('reference_number', 'like', "%{$s}%"));
        }
        if ($request->filled('from_date')) {
            $query->whereDate('income_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('income_date', '<=', $request->to_date);
        }

        $totals = (clone $query)->selectRaw('SUM(amount_usd) usd, SUM(amount_iqd) iqd, COUNT(*) c')->first();
        $incomes = $query->paginate(20)->withQueryString();

        return view('incomes.index', compact('incomes', 'totals'));
    }

    public function create()
    {
        return view('incomes.create', ['currentRate' => ExchangeRate::currentRate()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'source'      => 'required|string|max:255',
            'category'    => 'nullable|string|max:255',
            'currency'    => 'required|in:USD,IQD',
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
            'income_date' => 'required|date',
            'notes'       => 'nullable|string|max:1000',
        ]);

        $rate = ExchangeRate::currentRate();
        $income = Income::create(array_merge($data, $this->currencyAmounts($data['currency'], $data['amount'], $rate), [
            'exchange_rate_usd_to_iqd' => $rate,
            'user_id' => Auth::id(),
        ]));

        return redirect()->route('incomes.index')->with('success', 'وەرگرتنی پارە تۆمارکرا.');
    }

    public function show(Income $income)
    {
        return view('incomes.show', compact('income'));
    }

    public function edit(Income $income)
    {
        return view('incomes.edit', ['income' => $income, 'currentRate' => ExchangeRate::currentRate()]);
    }

    public function update(Request $request, Income $income)
    {
        $data = $request->validate([
            'source'      => 'required|string|max:255',
            'category'    => 'nullable|string|max:255',
            'currency'    => 'required|in:USD,IQD',
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
            'income_date' => 'required|date',
            'notes'       => 'nullable|string|max:1000',
        ]);

        $rate = (float) $income->exchange_rate_usd_to_iqd;
        $income->update(array_merge($data, $this->currencyAmounts($data['currency'], $data['amount'], $rate)));

        return redirect()->route('incomes.show', $income)->with('success', 'زانیاری نوێکرایەوە.');
    }

    public function destroy(Income $income)
    {
        $income->delete();
        return redirect()->route('incomes.index')->with('success', 'سڕایەوە.');
    }
}
