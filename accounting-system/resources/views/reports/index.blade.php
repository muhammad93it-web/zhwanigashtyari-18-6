@extends('layouts.app')
@section('title', 'ڕاپۆرتەکان')
@section('page-title', 'ڕاپۆرتەکان')
@section('page-subtitle', 'شیکردنەوەی دارایی و ئامارەکان')

@section('content')
<div class="space-y-6 animate-fade-in">

    <!-- Filter -->
    <div class="card p-4">
        <form method="GET" class="grid grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <div>
                <label class="label text-xs">لە بەروار</label>
                <input type="date" name="from_date" value="{{ $fromDate }}" class="input-field">
            </div>
            <div>
                <label class="label text-xs">بۆ بەروار</label>
                <input type="date" name="to_date" value="{{ $toDate }}" class="input-field">
            </div>
            <div>
                <label class="label text-xs">کڕیار (ئارەزووی)</label>
                <select name="client_id" class="input-field">
                    <option value="">هەمووی کڕیاران</option>
                    @foreach($clients as $c)
                    <option value="{{ $c->id }}" {{ $clientId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary w-full">نیشاندان</button>
            </div>
            <div>
                <a href="{{ route('reports.export.excel', ['from_date'=>$fromDate,'to_date'=>$toDate,'client_id'=>$clientId]) }}"
                    class="btn-gold flex items-center justify-center gap-2 w-full">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    Excel ناردن
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['sale', 'فرۆشتن', 'badge-sale', 'emerald'],
            ['purchase', 'کڕین', 'badge-purchase', 'red'],
            ['debit', 'قەرزەکان', 'badge-debit', 'amber'],
            ['credit', 'دانەوەی قەرز', 'badge-credit', 'blue'],
        ] as [$type, $label, $badge, $color])
        <div class="card p-4">
            <span class="{{ $badge }} mb-3 inline-block">{{ $label }}</span>
            <div class="text-xl font-bold text-white">${{ number_format($summary->get($type)?->total_usd ?? 0, 2) }}</div>
            <div class="text-xs text-teal-500">{{ number_format($summary->get($type)?->total_iqd ?? 0, 0) }} د.ع</div>
            <div class="text-xs text-teal-600 mt-1">{{ $summary->get($type)?->count ?? 0 }} مامەڵە</div>
        </div>
        @endforeach
    </div>

    <!-- Net Position -->
    <div class="card p-5 flex items-center justify-between">
        <div>
            <div class="text-sm font-bold text-teal-300 mb-1">دۆخی کۆتایی (نێتۆ)</div>
            <div class="text-xs text-teal-500">لە {{ $fromDate }} بۆ {{ $toDate }}</div>
        </div>
        <div class="text-center">
            <div class="text-xs text-teal-500 mb-1">دۆلار</div>
            <div class="text-3xl font-bold font-mono {{ $netUsd >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                {{ $netUsd >= 0 ? '+' : '' }}${{ number_format($netUsd, 2) }}
            </div>
        </div>
        <div class="text-center">
            <div class="text-xs text-teal-500 mb-1">دینار</div>
            <div class="text-2xl font-bold font-mono {{ $netIqd >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                {{ $netIqd >= 0 ? '+' : '' }}{{ number_format($netIqd, 0) }} د.ع
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b border-teal-700/30 flex items-center justify-between">
            <h3 class="font-bold text-white text-sm">لیستی مامەڵەکان ({{ $transactions->count() }})</h3>
        </div>
        <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
            <table class="w-full text-sm">
                <thead class="sticky top-0 bg-teal-900/90">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs text-teal-500 font-semibold">بەروار</th>
                        <th class="px-4 py-3 text-right text-xs text-teal-500 font-semibold">کڕیار</th>
                        <th class="px-4 py-3 text-right text-xs text-teal-500 font-semibold">جۆر</th>
                        <th class="px-4 py-3 text-right text-xs text-teal-500 font-semibold">وەسف</th>
                        <th class="px-4 py-3 text-right text-xs text-teal-500 font-semibold">دۆلار</th>
                        <th class="px-4 py-3 text-right text-xs text-teal-500 font-semibold">دینار</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-teal-800/20">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-teal-800/15 transition-colors">
                        <td class="px-4 py-2.5 text-teal-400 text-xs font-mono">{{ $tx->transaction_date->format('Y/m/d') }}</td>
                        <td class="px-4 py-2.5">
                            <a href="{{ route('clients.show', $tx->client) }}" class="text-white text-sm hover:text-gold-400 transition-colors">{{ $tx->client?->name }}</a>
                        </td>
                        <td class="px-4 py-2.5"><span class="{{ $tx->type_badge }} text-xs">{{ $tx->type_name }}</span></td>
                        <td class="px-4 py-2.5 text-teal-300 text-xs max-w-[180px] truncate">{{ $tx->description }}</td>
                        <td class="px-4 py-2.5 text-white font-mono text-sm">${{ number_format($tx->amount_usd, 2) }}</td>
                        <td class="px-4 py-2.5 text-teal-300 font-mono text-xs">{{ number_format($tx->amount_iqd, 0) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-teal-600 text-sm">هیچ مامەڵەیەک نەدۆزرایەوە بۆ ئەم ماوەیە</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
