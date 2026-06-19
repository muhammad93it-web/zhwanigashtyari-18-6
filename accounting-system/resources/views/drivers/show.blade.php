@extends('layouts.app')

@section('title', $driver->name)
@section('page-title', $driver->name)
@section('page-subtitle', 'کەشف حساب')

@section('content')
@php
    $num = fn($v) => number_format((float) $v, 0);
    $cur = fn($c) => $c === 'USD' ? '$' : 'د.ع';
@endphp

<div class="flex items-center justify-between mb-4 flex-wrap gap-2">
    <h2 class="text-base font-bold text-slate-800">{{ $driver->name }}</h2>
    <div class="flex items-center gap-2 flex-wrap">
        <a href="{{ route('drivers.statement-print', $driver) }}" target="_blank" class="btn-info !px-3 !py-1.5">چاپ (A4)</a>
        <a href="{{ route('drivers.statement-excel', $driver) }}" class="btn-primary !px-3 !py-1.5">Excel</a>
        <a href="{{ route('drivers.statement-word', $driver) }}" class="btn-primary !px-3 !py-1.5">Word</a>
        <a href="{{ route('drivers.edit', $driver) }}" class="btn-warning !px-3 !py-1.5">دەستکاری</a>
        <a href="{{ route('drivers.statements') }}" class="btn-outline !px-3 !py-1.5">گەڕانەوە</a>
    </div>
</div>

<div class="card p-4 mb-4 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
    <div><div class="text-xs text-slate-400">مۆبایل</div><div class="font-semibold text-slate-800">{{ $driver->phone ?: '—' }}</div></div>
    <div><div class="text-xs text-slate-400">ئۆتۆمبێل</div><div class="font-semibold text-slate-800">{{ $driver->vehicle_number ?: '—' }}{{ $driver->vehicle_type ? ' / ' . $driver->vehicle_type : '' }}</div></div>
    <div><div class="text-xs text-slate-400">دۆخ</div><div class="font-semibold text-slate-800">{{ $driver->is_active ? 'چالاک' : 'ناچالاک' }}</div></div>
    @if($driver->notes)<div><div class="text-xs text-slate-400">تێبینی</div><div class="font-semibold text-slate-800">{{ $driver->notes }}</div></div>@endif
</div>

{{-- پوختەی هەردوو دراو بە جیا --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
    @foreach(['IQD' => 'دیناری عێراقی', 'USD' => 'دۆلاری ئەمریکی'] as $code => $label)
        @php $s = $summary[$code]; $bal = (float) $s['balance']; @endphp
        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="font-bold text-slate-700">{{ $label }}</span>
                <span class="text-xs text-slate-400">{{ $cur($code) }}</span>
            </div>
            <div class="flex justify-between py-1 text-sm"><span class="text-slate-500">کۆی کرێی گواستنەوە</span><span class="font-semibold text-slate-800">{{ $num($s['trip']) }}</span></div>
            <div class="flex justify-between py-1 text-sm"><span class="text-slate-500">کۆی پارەدان</span><span class="font-semibold text-green-700">{{ $num($s['payment']) }}</span></div>
            <div class="mt-3 pt-3 border-t border-slate-100">
                @if($bal > 0)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500">ئەوەی دەبێ بیدەین (قەرزمان)</span>
                        <span class="text-xl font-extrabold text-red-600">{{ $num($bal) }}</span>
                    </div>
                @elseif($bal < 0)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500">ئەوەی لای ئەوەیە بۆ ئێمە</span>
                        <span class="text-xl font-extrabold text-green-700">{{ $num(abs($bal)) }}</span>
                    </div>
                @else
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500">دۆخ</span>
                        <span class="text-lg font-extrabold text-slate-500">تەسفیە (٠)</span>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>

<div class="mb-2 text-sm font-bold text-slate-700">مامەڵەکان</div>
<div class="card p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">بەروار</th>
                    <th class="px-4 py-3 font-semibold">جۆر</th>
                    <th class="px-4 py-3 font-semibold">وەسف</th>
                    <th class="px-4 py-3 font-semibold">دراو</th>
                    <th class="px-4 py-3 font-semibold">بڕ</th>
                    <th class="px-4 py-3 font-semibold">باڵانس دوای مامەڵە</th>
                    <th class="px-4 py-3 font-semibold">بەکارهێنەر</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $t)
                    @php $tc = $t->currency === 'USD' ? '$' : 'د.ع'; @endphp
                    <tr class="table-row">
                        <td class="px-4 py-3 text-slate-500">{{ optional($t->date)->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">
                            <span class="{{ $t->type == 'payment' ? 'badge-green' : 'badge-purchase' }}">{{ $t->type_name }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $t->description ?: '—' }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $tc }}</td>
                        <td class="px-4 py-3 font-semibold {{ $t->type == 'payment' ? 'text-green-700' : 'text-red-600' }}">
                            {{ $t->type == 'payment' ? '−' : '+' }}{{ $num($t->amount) }} {{ $tc }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $num($t->balance_after) }} {{ $tc }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $t->user->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-slate-400">هیچ مامەڵەیەک نییە.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $transactions->links() }}</div>
@endsection
