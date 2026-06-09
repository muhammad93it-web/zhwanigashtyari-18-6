<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExport;
use App\Exports\ClientTransactionsExport;

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

        // Aggregates by type
        $summary = $transactions->groupBy('type')->map(fn($group) => [
            'count'     => $group->count(),
            'total_usd' => $group->sum('amount_usd'),
            'total_iqd' => $group->sum('amount_iqd'),
        ]);

        // Net position
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
