@extends('layouts.app')
@section('title', $client->name)
@section('page-title', $client->name)
@section('page-subtitle', 'پرۆفایل و مامەڵەکانی کڕیار')

@section('content')
<div class="space-y-6 animate-fade-in">

    <!-- Client Header -->
    <div class="card p-6">
        <div class="flex flex-col sm:flex-row items-start justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white text-xl font-bold shadow-md shadow-green-500/30">
                    {{ mb_substr($client->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-800">{{ $client->name }}</h2>
                    <div class="flex flex-wrap gap-3 mt-1 text-sm text-slate-500">
                        @if($client->phone)<span>📞 {{ $client->phone }}</span>@endif
                        @if($client->email)<span>✉️ {{ $client->email }}</span>@endif
                        @if($client->address)<span>📍 {{ $client->address }}</span>@endif
                    </div>
                    @if($client->notes)
                    <p class="text-xs text-slate-400 mt-2">{{ $client->notes }}</p>
                    @endif
                </div>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="{{ route('transactions.create', ['client_id' => $client->id]) }}" class="btn-warning">+ مامەڵەی نوێ</a>
                <a href="{{ route('clients.edit', $client) }}" class="btn-outline">دەستکاری</a>
                <a href="{{ route('reports.client', $client) }}" class="btn-outline">ڕاپۆرت</a>
            </div>
        </div>
    </div>

    <!-- Balance Summary -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card p-4">
            <div class="text-xs text-slate-500 mb-1">فرۆشتن</div>
            <div class="text-lg font-bold text-green-600">${{ number_format($balances->sales_usd ?? 0, 2) }}</div>
        </div>
        <div class="card p-4">
            <div class="text-xs text-slate-500 mb-1">کڕین</div>
            <div class="text-lg font-bold text-red-500">${{ number_format($balances->purchases_usd ?? 0, 2) }}</div>
        </div>
        <div class="card p-4">
            <div class="text-xs text-slate-500 mb-1">قەرز (بردراو)</div>
            <div class="text-lg font-bold text-amber-500">${{ number_format($balances->debits_usd ?? 0, 2) }}</div>
        </div>
        <div class="card p-4 border-green-300">
            <div class="text-xs text-green-600 mb-1">باڵانسی کۆتایی</div>
            @php $net = $balances->balance_usd ?? 0; @endphp
            <div class="text-lg font-bold {{ $net >= 0 ? 'text-green-600' : 'text-red-500' }}">${{ number_format(abs($net), 2) }}</div>
            <div class="text-xs text-slate-500 mt-1">{{ $net >= 0 ? 'کڕیار قەرزداری ئێمەیە' : 'ئێمە قەرزداری کڕیاریین' }}</div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200">
            <h3 class="font-bold text-slate-800 text-sm">مامەڵەکانی کڕیار</h3>
            <a href="{{ route('reports.client.export', $client) }}" class="btn-outline flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                Excel
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                        <th class="px-4 py-3 font-semibold">ژمارە</th>
                        <th class="px-4 py-3 font-semibold">جۆر</th>
                        <th class="px-4 py-3 font-semibold">وەسف</th>
                        <th class="px-4 py-3 font-semibold">دراو</th>
                        <th class="px-4 py-3 font-semibold">بڕی دۆلار</th>
                        <th class="px-4 py-3 font-semibold">بڕی دینار</th>
                        <th class="px-4 py-3 font-semibold">بەروار</th>
                        <th class="px-4 py-3 font-semibold">کارەکان</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                    <tr class="table-row">
                        <td class="px-4 py-3 font-mono text-xs text-slate-400">{{ $tx->reference_number }}</td>
                        <td class="px-4 py-3"><span class="{{ $tx->type_badge }}">{{ $tx->type_name }}</span></td>
                        <td class="px-4 py-3 text-slate-600 max-w-xs truncate">{{ $tx->description }}</td>
                        <td class="px-4 py-3 text-xs font-semibold {{ $tx->currency === 'USD' ? 'text-green-600' : 'text-amber-500' }}">{{ $tx->currency }}</td>
                        <td class="px-4 py-3 text-slate-800 font-mono">${{ number_format($tx->amount_usd, 2) }}</td>
                        <td class="px-4 py-3 text-slate-600 font-mono text-xs">{{ number_format($tx->amount_iqd, 0) }} د.ع</td>
                        <td class="px-4 py-3 text-slate-500 text-xs">{{ $tx->transaction_date->format('Y/m/d') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('transactions.show', $tx) }}" class="text-cyan-600 hover:text-cyan-700 transition-colors">بینین</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-10 text-center text-slate-500 text-sm">هیچ مامەڵەیەک نییە</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
        <div class="p-4 border-t border-slate-200">{{ $transactions->links() }}</div>
        @endif
    </div>

</div>
@endsection
