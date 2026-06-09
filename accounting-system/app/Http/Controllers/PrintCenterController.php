<?php

namespace App\Http\Controllers;

use App\Models\ContractorPayment;
use App\Models\Debt;
use App\Models\Expense;
use App\Models\Income;
use App\Models\MaterialMovement;
use App\Models\Transaction;
use Illuminate\Http\Request;

class PrintCenterController extends Controller
{
    public const SECTIONS = [
        'incomes'   => 'وەرگرتنی پارە',
        'expenses'  => 'خەرجکردنی پارە',
        'debts'     => 'قەرزەکان',
        'purchases' => 'کڕینی مەواد',
        'sales'     => 'فرۆشتنی مەواد',
        'contractor_payments' => 'پارەدانی وەستا',
        'transactions' => 'مامەڵە گشتییەکان',
    ];

    public function index()
    {
        $sections = self::SECTIONS;
        return view('print-center.index', compact('sections'));
    }

    public function print(Request $request)
    {
        $from = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $to   = $request->get('to_date', now()->format('Y-m-d'));
        $selected = $request->get('sections', array_keys(self::SECTIONS));
        if (is_string($selected)) {
            $selected = [$selected];
        }

        $data = [];
        foreach ($selected as $key) {
            if (!array_key_exists($key, self::SECTIONS)) {
                continue;
            }
            $data[$key] = [
                'label' => self::SECTIONS[$key],
                'rows'  => $this->fetchSection($key, $from, $to),
            ];
        }

        return view('print-center.print', compact('data', 'from', 'to'));
    }

    private function fetchSection(string $key, string $from, string $to)
    {
        return match ($key) {
            'incomes'   => Income::whereBetween('income_date', [$from, $to])->latest('income_date')->get(),
            'expenses'  => Expense::whereBetween('expense_date', [$from, $to])->latest('expense_date')->get(),
            'debts'     => Debt::whereBetween('debt_date', [$from, $to])->latest('debt_date')->get(),
            'purchases' => MaterialMovement::with('material')->where('type', 'purchase')->whereBetween('movement_date', [$from, $to])->latest('movement_date')->get(),
            'sales'     => MaterialMovement::with('material')->where('type', 'sale')->whereBetween('movement_date', [$from, $to])->latest('movement_date')->get(),
            'contractor_payments' => ContractorPayment::with('contractor')->whereBetween('payment_date', [$from, $to])->latest('payment_date')->get(),
            'transactions' => Transaction::with('client')->whereBetween('transaction_date', [$from, $to])->latest('transaction_date')->get(),
            default => collect(),
        };
    }
}
