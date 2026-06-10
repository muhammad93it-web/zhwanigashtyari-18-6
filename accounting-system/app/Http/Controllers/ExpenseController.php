<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Models\Expense;
use App\Traits\CalculatesCurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    use CalculatesCurrency;

    public function index(Request $request)
    {
        $query = Expense::query()->latest('expense_date');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('payee', 'like', "%{$s}%")
                ->orWhere('description', 'like', "%{$s}%")
                ->orWhere('reference_number', 'like', "%{$s}%"));
        }
        if ($request->filled('from_date')) {
            $query->whereDate('expense_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('expense_date', '<=', $request->to_date);
        }

        $totals = (clone $query)->reorder()->selectRaw('SUM(amount_usd) usd, SUM(amount_iqd) iqd, COUNT(*) c')->first();
        $expenses = $query->paginate(20)->withQueryString();

        return view('expenses.index', compact('expenses', 'totals'));
    }

    public function create()
    {
        return view('expenses.create', ['currentRate' => ExchangeRate::currentRate()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'payee'        => 'required|string|max:255',
            'category'     => 'nullable|string|max:255',
            'currency'     => 'required|in:USD,IQD',
            'amount'       => 'required|numeric|min:0.01',
            'description'  => 'nullable|string|max:255',
            'expense_date' => 'required|date',
            'notes'        => 'nullable|string|max:1000',
        ]);

        $rate = ExchangeRate::currentRate();
        Expense::create(array_merge($data, $this->currencyAmounts($data['currency'], $data['amount'], $rate), [
            'exchange_rate_usd_to_iqd' => $rate,
            'user_id' => Auth::id(),
        ]));

        return redirect()->route('expenses.index')->with('success', 'خەرجکردنی پارە تۆمارکرا.');
    }

    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        return view('expenses.edit', ['expense' => $expense, 'currentRate' => ExchangeRate::currentRate()]);
    }

    public function update(Request $request, Expense $expense)
    {
        $data = $request->validate([
            'payee'        => 'required|string|max:255',
            'category'     => 'nullable|string|max:255',
            'currency'     => 'required|in:USD,IQD',
            'amount'       => 'required|numeric|min:0.01',
            'description'  => 'nullable|string|max:255',
            'expense_date' => 'required|date',
            'notes'        => 'nullable|string|max:1000',
        ]);

        $rate = (float) $expense->exchange_rate_usd_to_iqd;
        $expense->update(array_merge($data, $this->currencyAmounts($data['currency'], $data['amount'], $rate)));

        return redirect()->route('expenses.show', $expense)->with('success', 'زانیاری نوێکرایەوە.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'سڕایەوە.');
    }
}
