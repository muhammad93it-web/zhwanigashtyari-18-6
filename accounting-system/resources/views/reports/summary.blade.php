@extends('layouts.app')

@section('title', 'کۆی هەموو بەشەکان')
@section('page-title', 'کۆی هەموو بەشەکان')
@section('page-subtitle', 'پوختەی دارایی لە ماوەیەکدا')

@section('content')
@php
    $iqd = fn($v) => number_format((float) $v, 0);
    $usd = fn($v) => '$' . number_format((float) $v, 2);
@endphp

{{-- Filter + print --}}
<div class="card p-4 mb-5">
    <form method="GET" action="{{ route('reports.summary') }}" class="flex flex-wrap items-end gap-3">
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
            <span class="text-xs text-slate-500 font-medium">کۆی داهات</span>
            <span class="badge-green">+</span>
        </div>
        <div class="text-2xl font-extrabold text-green-600">{{ $iqd($totals['in_iqd']) }}</div>
        <div class="text-[11px] text-slate-400">دینار</div>
    </div>
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <span class="text-xs text-slate-500 font-medium">کۆی خەرجی</span>
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

{{-- Rows table --}}
<div class="card p-5">
    <h3 class="text-sm font-bold text-slate-800 mb-3">پوختەی بەشەکان</h3>
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
                @foreach($rows as $row)
                <tr class="table-row">
                    <td class="px-4 py-3 font-medium text-slate-800">
                        <span class="inline-flex items-center gap-2">
                            <span class="{{ $row['flow'] === 'in' ? 'badge-green' : 'badge-red' }}">{{ $row['flow'] === 'in' ? '+' : '-' }}</span>
                            {{ $row['label'] }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-slate-600">{{ (int) $row['data']->c }}</td>
                    <td class="px-4 py-3 font-semibold {{ $row['flow'] === 'in' ? 'text-green-600' : 'text-red-500' }}">{{ $iqd($row['data']->iqd) }} د</td>
                    <td class="px-4 py-3 font-semibold {{ $row['flow'] === 'in' ? 'text-green-600' : 'text-red-500' }}">{{ $usd($row['data']->usd) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-slate-200 font-bold">
                    <td class="px-4 py-3 text-slate-800">کۆی داهات</td>
                    <td class="px-4 py-3"></td>
                    <td class="px-4 py-3 text-green-600" colspan="2">{{ $iqd($totals['in_iqd']) }} د</td>
                </tr>
                <tr class="font-bold">
                    <td class="px-4 py-3 text-slate-800">کۆی خەرجی</td>
                    <td class="px-4 py-3"></td>
                    <td class="px-4 py-3 text-red-500" colspan="2">{{ $iqd($totals['out_iqd']) }} د</td>
                </tr>
                <tr class="font-extrabold">
                    <td class="px-4 py-3 text-slate-800">ساف</td>
                    <td class="px-4 py-3"></td>
                    <td class="px-4 py-3 {{ $totals['net_iqd'] >= 0 ? 'text-green-600' : 'text-red-500' }}" colspan="2">{{ $iqd($totals['net_iqd']) }} د</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
