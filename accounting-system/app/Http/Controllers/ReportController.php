<?php

namespace App\Http\Controllers;

use App\Exports\ClientTransactionsExport;
use App\Exports\TransactionsExport;
use App\Models\Client;
use App\Models\ContractorPayment;
use App\Models\Debt;
use App\Models\Expense;
use App\Models\Income;
use App\Models\MaterialMovement;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate   = $request->get('to_date', now()->format('Y-m-d'));
        $clientId = $request->get('client_id');

        $query = Transaction::with('client')
            ->whereDate('transaction_date', '>=', $fromDate)
            ->whereDate('transaction_date', '<=', $toDate);

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        $transactions = $query->latest('transaction_date')->get();

        $summary = $transactions->groupBy('type')->map(fn($group) => [
            'count'     => $group->count(),
            'total_usd' => $group->sum('amount_usd'),
            'total_iqd' => $group->sum('amount_iqd'),
        ]);

        $netUsd = $transactions->sum(fn($t) => in_array($t->type, ['sale', 'debit']) ? $t->amount_usd : -$t->amount_usd);
        $netIqd = $transactions->sum(fn($t) => in_array($t->type, ['sale', 'debit']) ? $t->amount_iqd : -$t->amount_iqd);

        $clients = Client::orderBy('name')->get(['id', 'name']);

        return view('reports.index', compact(
            'transactions', 'summary', 'netUsd', 'netIqd',
            'clients', 'fromDate', 'toDate', 'clientId'
        ));
    }

    public function clientReport(Client $client)
    {
        $transactions = $client->transactions()->with('user')->latest('transaction_date')->get();

        $balances = [
            'sales_usd'     => $transactions->where('type', 'sale')->sum('amount_usd'),
            'purchases_usd' => $transactions->where('type', 'purchase')->sum('amount_usd'),
            'debits_usd'    => $transactions->where('type', 'debit')->sum('amount_usd'),
            'credits_usd'   => $transactions->where('type', 'credit')->sum('amount_usd'),
        ];

        $balances['net_usd'] = ($balances['sales_usd'] + $balances['debits_usd'])
            - ($balances['purchases_usd'] + $balances['credits_usd']);

        return view('reports.client', compact('client', 'transactions', 'balances'));
    }

    /**
     * ڕاپۆرتی ڕۆژانە — هەموو جووڵەکانی یەک ڕۆژ
     */
    public function daily(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));

        $incomes  = Income::whereDate('income_date', $date)->latest()->get();
        $expenses = Expense::whereDate('expense_date', $date)->latest()->get();
        $purchases = MaterialMovement::with('material')->where('type', 'purchase')->whereDate('movement_date', $date)->get();
        $sales     = MaterialMovement::with('material')->where('type', 'sale')->whereDate('movement_date', $date)->get();
        $payments  = ContractorPayment::with('contractor')->whereDate('payment_date', $date)->get();

        $in = $incomes->sum('amount_iqd') + $sales->sum('amount_iqd');
        $out = $expenses->sum('amount_iqd') + $purchases->sum('amount_iqd') + $payments->sum('amount_iqd');

        $totals = [
            'in_iqd'  => $in,
            'out_iqd' => $out,
            'net_iqd' => $in - $out,
        ];

        return view('reports.daily', compact('date', 'incomes', 'expenses', 'purchases', 'sales', 'payments', 'totals'));
    }

    /**
     * کۆی هەموو بەشەکان — لە ماوەیەکی دیاریکراودا
     */
    public function summary(Request $request)
    {
        $from = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $to   = $request->get('to_date', now()->format('Y-m-d'));

        $agg = fn($q, $col) => $q->whereBetween($col, [$from, $to])
            ->selectRaw('SUM(amount_usd) usd, SUM(amount_iqd) iqd, COUNT(*) c')->first();

        $rows = [
            'incomes'   => ['label' => 'وەرگرتنی پارە',   'data' => $agg(Income::query(), 'income_date'),  'flow' => 'in'],
            'sales'     => ['label' => 'فرۆشتنی مەواد',    'data' => $agg(MaterialMovement::where('type', 'sale'), 'movement_date'), 'flow' => 'in'],
            'expenses'  => ['label' => 'خەرجکردنی پارە',   'data' => $agg(Expense::query(), 'expense_date'), 'flow' => 'out'],
            'purchases' => ['label' => 'کڕینی مەواد',      'data' => $agg(MaterialMovement::where('type', 'purchase'), 'movement_date'), 'flow' => 'out'],
            'payments'  => ['label' => 'پارەدانی وەستا',   'data' => $agg(ContractorPayment::query(), 'payment_date'), 'flow' => 'out'],
        ];

        $totalIn = $rows['incomes']['data']->iqd + $rows['sales']['data']->iqd;
        $totalOut = $rows['expenses']['data']->iqd + $rows['purchases']['data']->iqd + $rows['payments']['data']->iqd;

        $totals = [
            'in_iqd'  => $totalIn,
            'out_iqd' => $totalOut,
            'net_iqd' => $totalIn - $totalOut,
        ];

        return view('reports.summary', compact('rows', 'totals', 'from', 'to'));
    }

    /**
     * تێچووی گشتیی پڕۆژە — کۆی تەواوی خەرجییەکان
     */
    public function projectCost(Request $request)
    {
        $from = $request->get('from_date');
        $to   = $request->get('to_date');

        $apply = function ($q, $col) use ($from, $to) {
            if ($from) $q->whereDate($col, '>=', $from);
            if ($to) $q->whereDate($col, '<=', $to);
            return $q->selectRaw('SUM(amount_usd) usd, SUM(amount_iqd) iqd, COUNT(*) c')->first();
        };

        $costs = [
            'expenses'  => ['label' => 'خەرجی گشتی',     'data' => $apply(Expense::query(), 'expense_date')],
            'purchases' => ['label' => 'کڕینی مەواد',     'data' => $apply(MaterialMovement::where('type', 'purchase'), 'movement_date')],
            'payments'  => ['label' => 'پارەدانی وەستا',  'data' => $apply(ContractorPayment::query(), 'payment_date')],
        ];

        $income = [
            'incomes' => ['label' => 'وەرگرتنی پارە', 'data' => $apply(Income::query(), 'income_date')],
            'sales'   => ['label' => 'فرۆشتنی مەواد',  'data' => $apply(MaterialMovement::where('type', 'sale'), 'movement_date')],
        ];

        $totalCostIqd = collect($costs)->sum(fn($r) => (float) $r['data']->iqd);
        $totalCostUsd = collect($costs)->sum(fn($r) => (float) $r['data']->usd);
        $totalIncomeIqd = collect($income)->sum(fn($r) => (float) $r['data']->iqd);
        $totalIncomeUsd = collect($income)->sum(fn($r) => (float) $r['data']->usd);

        $totals = [
            'cost_iqd'   => $totalCostIqd,
            'cost_usd'   => $totalCostUsd,
            'income_iqd' => $totalIncomeIqd,
            'income_usd' => $totalIncomeUsd,
            'net_iqd'    => $totalIncomeIqd - $totalCostIqd,
            'net_usd'    => $totalIncomeUsd - $totalCostUsd,
        ];

        return view('reports.project-cost', compact('costs', 'income', 'totals', 'from', 'to'));
    }

    public function exportExcel(Request $request)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate   = $request->get('to_date', now()->format('Y-m-d'));
        $clientId = $request->get('client_id');

        $filename = 'مامەڵەکان_' . $fromDate . '_بۆ_' . $toDate . '.xlsx';

        return Excel::download(new TransactionsExport($fromDate, $toDate, $clientId), $filename);
    }

    public function exportClientExcel(Client $client)
    {
        $filename = 'کڕیار_' . $client->name . '_' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new ClientTransactionsExport($client), $filename);
    }
}
