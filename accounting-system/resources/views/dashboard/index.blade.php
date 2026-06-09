@extends('layouts.app')

@section('title', 'داشبۆرد')
@section('page-title', 'داشبۆرد')
@section('page-subtitle', 'تێڕوانینی گشتی سیستەم')

@section('content')
<div class="space-y-6 animate-fade-in">

    <!-- Stats Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <span class="text-xs text-teal-500 font-medium">کۆی کڕیاران</span>
                <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/></svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-white">{{ number_format($stats['total_clients']) }}</div>
            <div class="text-xs text-emerald-400">{{ $stats['active_clients'] }} چالاک</div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <span class="text-xs text-teal-500 font-medium">مامەڵەی ئەمرۆ</span>
                <div class="w-8 h-8 rounded-lg bg-gold-400/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-gold-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-white">{{ number_format($stats['today_transactions']) }}</div>
            <div class="text-xs text-teal-500">مامەڵەی تۆمارکراو</div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <span class="text-xs text-teal-500 font-medium">فرۆشتنی مانگ</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/></svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-white">${{ number_format($monthlyTotals->get('sale')?->total_usd ?? 0, 0) }}</div>
            <div class="text-xs text-teal-500">{{ number_format($monthlyTotals->get('sale')?->total_iqd ?? 0, 0) }} د.ع</div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <span class="text-xs text-teal-500 font-medium">ڕێژەی گۆڕین</span>
                <div class="w-8 h-8 rounded-lg bg-purple-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-400" fill="currentColor" viewBox="0 0 20 20"><path d="M8 5a1 1 0 100 2h5.586l-1.293 1.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L13.586 5H8z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-white">{{ number_format($currentRate?->usd_to_iqd ?? 0, 0) }}</div>
            <div class="text-xs text-teal-500">دینار / دۆلار</div>
        </div>
    </div>

    <!-- Monthly Type Summary -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['sale', 'فرۆشتن', 'badge-sale', 'emerald'],
            ['purchase', 'کڕین', 'badge-purchase', 'red'],
            ['debit', 'قەرزەکان (بردراو)', 'badge-debit', 'amber'],
            ['credit', 'دانەوەی قەرز', 'badge-credit', 'blue'],
        ] as [$type, $label, $badge, $color])
        <div class="card p-4">
            <div class="flex items-center gap-2 mb-3">
                <span class="{{ $badge }} text-xs">{{ $label }}</span>
            </div>
            <div class="text-xl font-bold text-white">${{ number_format($monthlyTotals->get($type)?->total_usd ?? 0, 2) }}</div>
            <div class="text-xs text-teal-500 mt-1">{{ number_format($monthlyTotals->get($type)?->total_iqd ?? 0, 0) }} د.ع</div>
            <div class="text-xs text-teal-600 mt-1">{{ $monthlyTotals->get($type)?->count ?? 0 }} مامەڵە</div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Recent Transactions -->
        <div class="lg:col-span-2 card overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-teal-700/30">
                <h3 class="font-bold text-white text-sm">دوایین مامەڵەکان</h3>
                <a href="{{ route('transactions.index') }}" class="text-xs text-teal-400 hover:text-gold-400 transition-colors">بینینی هەمووی ←</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-teal-900/50">
                            <th class="px-4 py-3 text-right text-xs text-teal-500 font-semibold">کڕیار</th>
                            <th class="px-4 py-3 text-right text-xs text-teal-500 font-semibold">جۆر</th>
                            <th class="px-4 py-3 text-right text-xs text-teal-500 font-semibold">بڕ</th>
                            <th class="px-4 py-3 text-right text-xs text-teal-500 font-semibold">بەروار</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $tx)
                        <tr class="table-row">
                            <td class="px-4 py-3">
                                <a href="{{ route('clients.show', $tx->client) }}" class="text-white font-medium hover:text-gold-400 transition-colors">{{ $tx->client?->name ?? '—' }}</a>
                            </td>
                            <td class="px-4 py-3"><span class="{{ $tx->type_badge }}">{{ $tx->type_name }}</span></td>
                            <td class="px-4 py-3 font-mono text-white">${{ number_format($tx->amount_usd, 2) }}</td>
                            <td class="px-4 py-3 text-teal-400 text-xs">{{ $tx->transaction_date->format('Y/m/d') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-teal-600 text-sm">هیچ مامەڵەیەک نییە</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Clients + Quick actions -->
        <div class="space-y-4">
            <!-- Quick Actions -->
            <div class="card p-4">
                <h3 class="font-bold text-white text-sm mb-3">کارە خێراکان</h3>
                <div class="space-y-2">
                    <a href="{{ route('transactions.create') }}" class="flex items-center gap-3 p-3 rounded-xl bg-teal-800/40 hover:bg-teal-700/40 transition-colors text-sm text-white">
                        <span class="text-lg">📝</span> تۆمارکردنی مامەڵەی نوێ
                    </a>
                    <a href="{{ route('clients.create') }}" class="flex items-center gap-3 p-3 rounded-xl bg-teal-800/40 hover:bg-teal-700/40 transition-colors text-sm text-white">
                        <span class="text-lg">👤</span> زیادکردنی کڕیاری نوێ
                    </a>
                    <a href="{{ route('reports.index') }}" class="flex items-center gap-3 p-3 rounded-xl bg-teal-800/40 hover:bg-teal-700/40 transition-colors text-sm text-white">
                        <span class="text-lg">📊</span> بینینی ڕاپۆرتەکان
                    </a>
                    <a href="{{ route('exchange-rates.index') }}" class="flex items-center gap-3 p-3 rounded-xl bg-teal-800/40 hover:bg-teal-700/40 transition-colors text-sm text-white">
                        <span class="text-lg">💱</span> نوێکردنی ڕێژەی گۆڕین
                    </a>
                </div>
            </div>

            <!-- Top Clients -->
            <div class="card p-4">
                <h3 class="font-bold text-white text-sm mb-3">باشترین کڕیاران (ئەم مانگە)</h3>
                <div class="space-y-2">
                    @forelse($topClients as $client)
                    <div class="flex items-center justify-between py-2 border-b border-teal-800/30 last:border-0">
                        <a href="{{ route('clients.show', $client) }}" class="text-sm text-teal-200 hover:text-gold-400 transition-colors truncate">{{ $client->name }}</a>
                        <span class="text-xs text-teal-500 ml-2 flex-shrink-0">{{ $client->month_tx_count }} مامەڵە</span>
                    </div>
                    @empty
                    <p class="text-xs text-teal-600 text-center py-2">هیچ کڕیارێک نییە</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
