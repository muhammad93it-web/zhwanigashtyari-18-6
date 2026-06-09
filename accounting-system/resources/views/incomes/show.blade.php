@extends('layouts.app')

@section('title', 'وردەکاری داهات')
@section('page-title', 'وردەکاری داهات')
@section('page-subtitle', $income->source)

@section('content')
@php
    $iqd = fn($v) => number_format((float) $v, 0);
@endphp

<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">وردەکاری داهات</h2>
    <div class="flex items-center gap-2">
        <a href="{{ route('incomes.edit', $income) }}" class="btn-warning">دەستکاری</a>
        <form method="POST" action="{{ route('incomes.destroy', $income) }}" onsubmit="return confirm('دڵنیایت لە سڕینەوە؟')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger">سڕینەوە</button>
        </form>
        <a href="{{ route('incomes.index') }}" class="btn-outline">گەڕانەوە</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-5">
    <div class="stat-card">
        <span class="text-xs text-slate-500 font-medium">بڕی داخڵکراو</span>
        <div class="text-2xl font-extrabold text-slate-800">
            @if($income->currency === 'USD')
                ${{ number_format((float)$income->amount, 2) }}
            @else
                {{ $iqd($income->amount) }} د
            @endif
        </div>
        <div class="text-[11px] text-slate-400">دراو: {{ $income->currency }}</div>
    </div>
    <div class="stat-card">
        <span class="text-xs text-slate-500 font-medium">بە دینار</span>
        <div class="text-2xl font-extrabold text-green-600">{{ $iqd($income->amount_iqd) }} د</div>
    </div>
    <div class="stat-card">
        <span class="text-xs text-slate-500 font-medium">بە دۆلار</span>
        <div class="text-2xl font-extrabold text-cyan-600">${{ number_format((float)$income->amount_usd, 2) }}</div>
    </div>
</div>

<div class="card p-5">
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
        <div>
            <dt class="text-slate-500 mb-1">سەرچاوە</dt>
            <dd class="font-semibold text-slate-800">{{ $income->source }}</dd>
        </div>
        <div>
            <dt class="text-slate-500 mb-1">جۆر</dt>
            <dd class="font-semibold text-slate-800">{{ $income->category ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-500 mb-1">ژمارەی بەڵگە</dt>
            <dd class="font-semibold text-slate-800">{{ $income->reference_number ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-500 mb-1">بەرواری داهات</dt>
            <dd class="font-semibold text-slate-800">{{ $income->income_date->format('Y-m-d') }}</dd>
        </div>
        <div>
            <dt class="text-slate-500 mb-1">ڕێژەی قفڵکراو</dt>
            <dd class="font-semibold text-slate-800">{{ $iqd($income->exchange_rate_usd_to_iqd) }} دینار بۆ ١ دۆلار</dd>
        </div>
        <div>
            <dt class="text-slate-500 mb-1">وەسف</dt>
            <dd class="font-semibold text-slate-800">{{ $income->description ?? '—' }}</dd>
        </div>
        <div class="sm:col-span-2">
            <dt class="text-slate-500 mb-1">تێبینی</dt>
            <dd class="font-semibold text-slate-800 whitespace-pre-line">{{ $income->notes ?? '—' }}</dd>
        </div>
    </dl>
</div>
@endsection
