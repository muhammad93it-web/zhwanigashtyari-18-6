<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ExchangeRate;
use App\Models\Transaction;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // Summary stats
        $stats = [
            'total_clients'       => Client::count(),
            'active_clients'      => Client::where('is_active', true)->count(),
            'today_transactions'  => Transaction::whereDate('transaction_date', $today)->count(),
            'month_transactions'  => Transaction::where('transaction_date', '>=', $thisMonth)->count(),
        ];

        // Monthly totals by type
        $monthlyTotals = Transaction::where('transaction_date', '>=', $thisMonth)
            ->selectRaw("
                type,
                SUM(amount_usd) as total_usd,
                SUM(amount_iqd) as total_iqd
            ")
            ->groupBy('type')
            ->get()
            ->keyBy('type');

        // Recent transactions
        $recentTransactions = Transaction::with('client')
            ->latest()
            ->take(10)
            ->get();

        // Current exchange rate
        $currentRate = ExchangeRate::current();

        // Top clients by transaction count this month
        $topClients = Client::withCount(['transactions as month_tx_count' => function ($q) use ($thisMonth) {
            $q->where('transaction_date', '>=', $thisMonth);
        }])
            ->orderByDesc('month_tx_count')
            ->take(5)
            ->get();

        // Chart data: last 7 days transaction counts
        $chartData = collect(range(6, 0))->map(function ($daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);
            return [
                'date'  => $date->format('m/d'),
                'count' => Transaction::whereDate('transaction_date', $date)->count(),
                'usd'   => Transaction::whereDate('transaction_date', $date)->sum('amount_usd'),
            ];
        });

        return view('dashboard.index', compact(
            'stats',
            'monthlyTotals',
            'recentTransactions',
            'currentRate',
            'topClients',
            'chartData'
        ));
    }
}
