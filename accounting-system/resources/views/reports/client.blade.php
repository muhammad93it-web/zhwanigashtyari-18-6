@extends('layouts.app')
@section('title', 'ڕاپۆرتی کڕیار — ' . $client->name)
@section('page-title', 'ڕاپۆرتی کڕیار')
@section('page-subtitle', $client->name)

@section('content')
<div class="space-y-6 animate-fade-in">

    <!-- Client summary -->
    <div class="card p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white text-lg font-bold">
                {{ mb_substr($client->name, 0, 1) }}
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-800">{{ $client->name }}</h2>
                <div class="text-xs text-slate-500">{{ $client->phone }} {{ $client->address ? '— '.$client->address : '' }}</div>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('reports.client.export', $client) }}" class="btn-outline flex items-center gap-2">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                Excel ناردن
            </a>
            <a href="{{ route('clients.show', $client) }}" class="btn-outline">← پرۆفایل</a>
        </div>
    </div>

    <!-- Balances -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="card p-4">
            <div class="text-xs text-slate-500 mb-1">فرۆشتن</div>
            <div class="text-xl font-bold text-green-600 font-mono">${{ number_format($balances['sales_usd'], 2) }}</div>
        </div>
        <div class="card p-4">
            <div class="text-xs text-slate-500 mb-1">کڕین</div>
            <div class="text-xl font-bold text-red-500 font-mono">${{ number_format($balances['purchases_usd'], 2) }}</div>
        </div>
        <div class="card p-4">
            <div class="text-xs text-slate-500 mb-1">قەرز (بردراو)</div>
            <div class="text-xl font-bold text-amber-500 font-mono">${{ number_format($balances['debits_usd'], 2) }}</div>
        </div>
        <div class="card p-4">
            <div class="text-xs text-slate-500 mb-1">دانەوەی قەرز</div>
            <div class="text-xl font-bold text-cyan-600 font-mono">${{ number_format($balances['credits_usd'], 2) }}</div>
        </div>
        <div class="card p-4 border border-green-300">
            <div class="text-xs text-green-600 mb-1">باڵانسی کۆتایی</div>
            @php $net = $balances['net_usd']; @endphp
            <div class="text-xl font-bold {{ $net >= 0 ? 'text-green-600' : 'text-red-500' }} font-mono">${{ number_format(abs($net), 2) }}</div>
            <div class="text-xs text-slate-500 mt-1">{{ $net >= 0 ? 'قەرزداری ئێمە' : 'ئێمە قەرزداریم' }}</div>
        </div>
    </div>

    <!-- Transactions -->
    <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="font-bold text-slate-800 text-sm">هەموو مامەڵەکانی {{ $client->name }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                        <th class="px-4 py-3 font-semibold">بەروار</th>
                        <th class="px-4 py-3 font-semibold">جۆر</th>
                        <th class="px-4 py-3 font-semibold">وەسف</th>
                        <th class="px-4 py-3 font-semibold">دراو</th>
                        <th class="px-4 py-3 font-semibold">دۆلار</th>
                        <th class="px-4 py-3 font-semibold">دینار</th>
                        <th class="px-4 py-3 font-semibold">ڕێژەی گۆڕین</th>
                        <th class="px-4 py-3 font-semibold">وەسڵ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                    <tr class="table-row">
                        <td class="px-4 py-3 text-slate-500 text-xs font-mono">{{ $tx->transaction_date->format('Y/m/d') }}</td>
                        <td class="px-4 py-3"><span class="{{ $tx->type_badge }}">{{ $tx->type_name }}</span></td>
                        <td class="px-4 py-3 text-slate-600 max-w-[160px] truncate text-xs">{{ $tx->description }}</td>
                        <td class="px-4 py-3 text-xs font-semibold {{ $tx->currency === 'USD' ? 'text-green-600' : 'text-amber-500' }}">{{ $tx->currency }}</td>
                        <td class="px-4 py-3 text-slate-800 font-mono">${{ number_format($tx->amount_usd, 2) }}</td>
                        <td class="px-4 py-3 text-slate-600 font-mono text-xs">{{ number_format($tx->amount_iqd, 0) }}</td>
                        <td class="px-4 py-3 text-slate-400 font-mono text-xs">{{ number_format($tx->exchange_rate_usd_to_iqd, 0) }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('transactions.print', $tx) }}" target="_blank" class="text-cyan-600 hover:text-cyan-700 transition-colors text-xs">چاپ</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-10 text-center text-slate-500 text-sm">هیچ مامەڵەیەک نییە</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
