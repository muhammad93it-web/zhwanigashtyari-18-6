@extends('layouts.app')

@section('title', 'وردەکاری قەرز')
@section('page-title', 'وردەکاری قەرز')
@section('page-subtitle', $debt->party_name)

@section('content')
@php
    $iqd = fn($v) => number_format((float) $v, 0);
@endphp

<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">وردەکاری قەرز</h2>
    <div class="flex items-center gap-2">
        @if($debt->status === 'open')
            <form method="POST" action="{{ route('debts.mark-paid', $debt) }}">
                @csrf
                <button type="submit" class="btn-primary">نیشانکردن وەک دراوەتەوە</button>
            </form>
        @endif
        <a href="{{ route('debts.edit', $debt) }}" class="btn-warning">دەستکاری</a>
        <form method="POST" action="{{ route('debts.destroy', $debt) }}" onsubmit="return confirm('دڵنیایت لە سڕینەوە؟')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger">سڕینەوە</button>
        </form>
        <a href="{{ route('debts.index') }}" class="btn-outline">گەڕانەوە</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-5">
    <div class="stat-card">
        <span class="text-xs text-slate-500 font-medium">بڕی داخڵکراو</span>
        <div class="text-2xl font-extrabold text-slate-800">
            @if($debt->currency === 'USD')
                ${{ number_format((float)$debt->amount, 2) }}
            @else
                {{ $iqd($debt->amount) }} د
            @endif
        </div>
        <div class="text-[11px] text-slate-400">دراو: {{ $debt->currency }}</div>
    </div>
    <div class="stat-card">
        <span class="text-xs text-slate-500 font-medium">بە دینار</span>
        <div class="text-2xl font-extrabold {{ $debt->direction === 'receivable' ? 'text-green-600' : 'text-red-500' }}">{{ $iqd($debt->amount_iqd) }} د</div>
    </div>
    <div class="stat-card">
        <span class="text-xs text-slate-500 font-medium">بە دۆلار</span>
        <div class="text-2xl font-extrabold text-cyan-600">${{ number_format((float)$debt->amount_usd, 2) }}</div>
    </div>
</div>

<div class="card p-5">
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
        <div>
            <dt class="text-slate-500 mb-1">ناوی کەس</dt>
            <dd class="font-semibold text-slate-800">{{ $debt->party_name }}</dd>
        </div>
        <div>
            <dt class="text-slate-500 mb-1">ئاراستە</dt>
            <dd><span class="{{ $debt->direction === 'receivable' ? 'badge-green' : 'badge-red' }}">{{ $debt->direction_name }}</span></dd>
        </div>
        <div>
            <dt class="text-slate-500 mb-1">دۆخ</dt>
            <dd><span class="{{ $debt->status === 'open' ? 'badge-amber' : 'badge-green' }}">{{ $debt->status_name }}</span></dd>
        </div>
        <div>
            <dt class="text-slate-500 mb-1">ژمارەی بەڵگە</dt>
            <dd class="font-semibold text-slate-800">{{ $debt->reference_number ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-500 mb-1">بەرواری قەرز</dt>
            <dd class="font-semibold text-slate-800">{{ $debt->debt_date->format('Y-m-d') }}</dd>
        </div>
        <div>
            <dt class="text-slate-500 mb-1">بەرواری گەڕاندنەوە</dt>
            <dd class="font-semibold text-slate-800">{{ $debt->due_date?->format('Y-m-d') ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-500 mb-1">بەرواری دانەوە</dt>
            <dd class="font-semibold text-slate-800">{{ $debt->paid_date?->format('Y-m-d') ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-500 mb-1">ڕێژەی قفڵکراو</dt>
            <dd class="font-semibold text-slate-800">{{ $iqd($debt->exchange_rate_usd_to_iqd) }} دینار بۆ ١ دۆلار</dd>
        </div>
        <div>
            <dt class="text-slate-500 mb-1">وەسف</dt>
            <dd class="font-semibold text-slate-800">{{ $debt->description ?? '—' }}</dd>
        </div>
        <div class="sm:col-span-2">
            <dt class="text-slate-500 mb-1">تێبینی</dt>
            <dd class="font-semibold text-slate-800 whitespace-pre-line">{{ $debt->notes ?? '—' }}</dd>
        </div>
    </dl>
</div>
@endsection
