@extends('layouts.app')

@section('title', 'تێچووی گشتیی پڕۆژە')
@section('page-title', 'تێچووی گشتیی پڕۆژە')
@section('page-subtitle', 'بەراوردی تێچوو و داهات')

@section('content')
@php
    $iqd = fn($v) => number_format((float) $v, 0);
    $usd = fn($v) => '$' . number_format((float) $v, 2);
@endphp

{{-- Filter + print --}}
<div class="card p-4 mb-5">
    <form method="GET" action="{{ route('reports.project-cost') }}" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[160px]">
            <label class="label" for="from_date">لە بەرواری</label>
            <input type="date" id="from_date" name="from_date" value="{{ $from }}" class="input-field">
        </div>
        <div class="flex-1 min-w-[160px]">
            <label class="label" for="to_date">تا بەرواری</label>
            <input type="date" id="to_date" name="to_date" value="{{ $to }}" class="input-field">
        </div>
        <button type="submit" class="btn-primary">پیشاندان</button>
        <button type="button" onclick="window.print()" class="btn-info">چاپکردن</button>
    </form>
</div>

{{-- Totals --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <span class="text-xs text-slate-500 font-medium">کۆی تێچوو</span>
            <span class="badge-red">-</span>
        </div>
        <div class="text-2xl font-extrabold text-red-500">{{ $iqd($totals['cost_iqd']) }}</div>
        <div class="text-[11px] text-slate-400">دینار · {{ $usd($totals['cost_usd']) }}</div>
    </div>
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <span class="text-xs text-slate-500 font-medium">کۆی داهات</span>
            <span class="badge-green">+</span>
        </div>
        <div class="text-2xl font-extrabold text-green-600">{{ $iqd($totals['income_iqd']) }}</div>
        <div class="text-[11px] text-slate-400">دینار · {{ $usd($totals['income_usd']) }}</div>
    </div>
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <span class="text-xs text-slate-500 font-medium">قازانجی ساف</span>
            <span class="{{ $totals['net_iqd'] >= 0 ? 'badge-green' : 'badge-red' }}">=</span>
        </div>
        <div class="text-2xl font-extrabold {{ $totals['net_iqd'] >= 0 ? 'text-green-600' : 'text-red-500' }}">{{ $iqd($totals['net_iqd']) }}</div>
        <div class="text-[11px] text-slate-400">دینار · {{ $usd($totals['net_usd']) }}</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    {{-- Costs --}}
    <div class="card p-5">
        <h3 class="text-sm font-bold text-red-500 mb-3">تێچووەکان</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                        <th class="px-4 py-3 font-semibold">بەش</th>
                        <th class="px-4 py-3 font-semibold">ژمارە</th>
                        <th class="px-4 py-3 font-semibold">بەدینار</th>
                        <th class="px-4 py-3 font-semibold">بەدۆلار</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($costs as $cost)
                    <tr class="table-row">
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $cost['label'] }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ (int) $cost['data']->c }}</td>
                        <td class="px-4 py-3 text-red-500 font-semibold">{{ $iqd($cost['data']->iqd) }} د</td>
                        <td class="px-4 py-3 text-red-500 font-semibold">{{ $usd($cost['data']->usd) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-slate-200 font-extrabold">
                        <td class="px-4 py-3 text-slate-800" colspan="2">کۆی تێچوو</td>
                        <td class="px-4 py-3 text-red-500">{{ $iqd($totals['cost_iqd']) }} د</td>
                        <td class="px-4 py-3 text-red-500">{{ $usd($totals['cost_usd']) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Income --}}
    <div class="card p-5">
        <h3 class="text-sm font-bold text-green-600 mb-3">داهاتەکان</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                        <th class="px-4 py-3 font-semibold">بەش</th>
                        <th class="px-4 py-3 font-semibold">ژمارە</th>
                        <th class="px-4 py-3 font-semibold">بەدینار</th>
                        <th class="px-4 py-3 font-semibold">بەدۆلار</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($income as $inc)
                    <tr class="table-row">
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $inc['label'] }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ (int) $inc['data']->c }}</td>
                        <td class="px-4 py-3 text-green-600 font-semibold">{{ $iqd($inc['data']->iqd) }} د</td>
                        <td class="px-4 py-3 text-green-600 font-semibold">{{ $usd($inc['data']->usd) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-slate-200 font-extrabold">
                        <td class="px-4 py-3 text-slate-800" colspan="2">کۆی داهات</td>
                        <td class="px-4 py-3 text-green-600">{{ $iqd($totals['income_iqd']) }} د</td>
                        <td class="px-4 py-3 text-green-600">{{ $usd($totals['income_usd']) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
