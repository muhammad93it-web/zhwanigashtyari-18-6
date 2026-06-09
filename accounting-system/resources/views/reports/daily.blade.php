@extends('layouts.app')

@section('title', 'ڕاپۆرتی ڕۆژانە')
@section('page-title', 'ڕاپۆرتی ڕۆژانە')
@section('page-subtitle', 'هەموو جووڵەکانی یەک ڕۆژ')

@section('content')
@php
    $iqd = fn($v) => number_format((float) $v, 0);
    $usd = fn($v) => '$' . number_format((float) $v, 2);
    $cur = fn($c, $a) => $c === 'USD' ? '$' . number_format((float) $a, 2) : number_format((float) $a, 0) . ' د';
@endphp

{{-- Filter + print --}}
<div class="card p-4 mb-5">
    <form method="GET" action="{{ route('reports.daily') }}" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[180px]">
            <label class="label" for="date">ڕۆژ</label>
            <input type="date" id="date" name="date" value="{{ $date }}" class="input-field">
        </div>
        <button type="submit" class="btn-primary">پیشاندان</button>
        <button type="button" onclick="window.print()" class="btn-info">چاپکردن</button>
    </form>
</div>

{{-- Totals --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <span class="text-xs text-slate-500 font-medium">داهات</span>
            <span class="badge-green">+</span>
        </div>
        <div class="text-2xl font-extrabold text-green-600">{{ $iqd($totals['in_iqd']) }}</div>
        <div class="text-[11px] text-slate-400">دینار</div>
    </div>
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <span class="text-xs text-slate-500 font-medium">خەرجی</span>
            <span class="badge-red">-</span>
        </div>
        <div class="text-2xl font-extrabold text-red-500">{{ $iqd($totals['out_iqd']) }}</div>
        <div class="text-[11px] text-slate-400">دینار</div>
    </div>
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <span class="text-xs text-slate-500 font-medium">ساف</span>
            <span class="{{ $totals['net_iqd'] >= 0 ? 'badge-green' : 'badge-red' }}">=</span>
        </div>
        <div class="text-2xl font-extrabold {{ $totals['net_iqd'] >= 0 ? 'text-green-600' : 'text-red-500' }}">{{ $iqd($totals['net_iqd']) }}</div>
        <div class="text-[11px] text-slate-400">دینار</div>
    </div>
</div>

<div class="space-y-5">
    {{-- Incomes --}}
    @if($incomes->isNotEmpty())
    <div class="card p-5">
        <h3 class="text-sm font-bold text-slate-800 mb-3">وەرگرتنی پارە</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                        <th class="px-4 py-3 font-semibold">سەرچاوە</th>
                        <th class="px-4 py-3 font-semibold">جۆر</th>
                        <th class="px-4 py-3 font-semibold">بڕ</th>
                        <th class="px-4 py-3 font-semibold">بەدینار</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($incomes as $income)
                    <tr class="table-row">
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $income->source }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $income->category ?? '—' }}</td>
                        <td class="px-4 py-3 text-green-600 font-semibold">{{ $cur($income->currency, $income->amount) }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $iqd($income->amount_iqd) }} د</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Sales --}}
    @if($sales->isNotEmpty())
    <div class="card p-5">
        <h3 class="text-sm font-bold text-slate-800 mb-3">فرۆشتنی مەواد</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                        <th class="px-4 py-3 font-semibold">مەواد</th>
                        <th class="px-4 py-3 font-semibold">بڕ</th>
                        <th class="px-4 py-3 font-semibold">کڕیار</th>
                        <th class="px-4 py-3 font-semibold">کۆ</th>
                        <th class="px-4 py-3 font-semibold">بەدینار</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
                    <tr class="table-row">
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $sale->material->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $iqd($sale->quantity) }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $sale->party_name ?? '—' }}</td>
                        <td class="px-4 py-3 text-green-600 font-semibold">{{ $cur($sale->currency, $sale->amount) }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $iqd($sale->amount_iqd) }} د</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Expenses --}}
    @if($expenses->isNotEmpty())
    <div class="card p-5">
        <h3 class="text-sm font-bold text-slate-800 mb-3">خەرجکردنی پارە</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                        <th class="px-4 py-3 font-semibold">وەرگر</th>
                        <th class="px-4 py-3 font-semibold">جۆر</th>
                        <th class="px-4 py-3 font-semibold">بڕ</th>
                        <th class="px-4 py-3 font-semibold">بەدینار</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $expense)
                    <tr class="table-row">
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $expense->payee }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $expense->category ?? '—' }}</td>
                        <td class="px-4 py-3 text-red-500 font-semibold">{{ $cur($expense->currency, $expense->amount) }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $iqd($expense->amount_iqd) }} د</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Purchases --}}
    @if($purchases->isNotEmpty())
    <div class="card p-5">
        <h3 class="text-sm font-bold text-slate-800 mb-3">کڕینی مەواد</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                        <th class="px-4 py-3 font-semibold">مەواد</th>
                        <th class="px-4 py-3 font-semibold">بڕ</th>
                        <th class="px-4 py-3 font-semibold">دابینکەر</th>
                        <th class="px-4 py-3 font-semibold">کۆ</th>
                        <th class="px-4 py-3 font-semibold">بەدینار</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchases as $purchase)
                    <tr class="table-row">
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $purchase->material->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $iqd($purchase->quantity) }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $purchase->party_name ?? '—' }}</td>
                        <td class="px-4 py-3 text-red-500 font-semibold">{{ $cur($purchase->currency, $purchase->amount) }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $iqd($purchase->amount_iqd) }} د</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Contractor payments --}}
    @if($payments->isNotEmpty())
    <div class="card p-5">
        <h3 class="text-sm font-bold text-slate-800 mb-3">پارەدانی وەستا</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                        <th class="px-4 py-3 font-semibold">وەستا</th>
                        <th class="px-4 py-3 font-semibold">مەتر</th>
                        <th class="px-4 py-3 font-semibold">بڕ</th>
                        <th class="px-4 py-3 font-semibold">بەدینار</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr class="table-row">
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $payment->contractor->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $payment->meters ? $iqd($payment->meters) : '—' }}</td>
                        <td class="px-4 py-3 text-red-500 font-semibold">{{ $cur($payment->currency, $payment->amount) }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $iqd($payment->amount_iqd) }} د</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if($incomes->isEmpty() && $sales->isEmpty() && $expenses->isEmpty() && $purchases->isEmpty() && $payments->isEmpty())
    <div class="card p-10 text-center text-slate-400 text-sm">
        هیچ جووڵەیەک نییە بۆ ئەم ڕۆژە
    </div>
    @endif
</div>
@endsection
