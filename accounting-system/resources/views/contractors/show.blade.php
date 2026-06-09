@extends('layouts.app')

@section('title', 'وردەکاری وەستا')
@section('page-title', $contractor->name)
@section('page-subtitle', 'وردەکاری وەستا و پارەدانەکان')

@section('content')
@php
    $iqd = fn($v) => number_format((float) $v, 0);
@endphp

<div class="flex items-center justify-between mb-4 gap-2 flex-wrap">
    <h2 class="text-base font-bold text-slate-800">{{ $contractor->name }}</h2>
    <div class="flex items-center gap-2">
        <a href="{{ route('contractor-payments.create', ['contractor_id' => $contractor->id]) }}" class="btn-primary">+ پارەدان</a>
        <a href="{{ route('contractors.edit', $contractor) }}" class="btn-warning">دەستکاری</a>
        <a href="{{ route('contractors.index') }}" class="btn-outline">گەڕانەوە</a>
    </div>
</div>

{{-- Info --}}
<div class="card p-5 mb-4">
    <h3 class="text-sm font-bold text-slate-800 mb-4">زانیاری وەستا</h3>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <div class="text-xs text-slate-400 mb-1">مۆبایل</div>
            <div class="text-slate-800 font-medium">{{ $contractor->phone ?: '—' }}</div>
        </div>
        <div>
            <div class="text-xs text-slate-400 mb-1">جۆری کار</div>
            <div><span class="{{ $contractor->work_type == 'per_meter' ? 'badge-cyan' : 'badge-amber' }}">{{ $contractor->work_type_name }}</span></div>
        </div>
        @if($contractor->work_type == 'per_meter')
            <div>
                <div class="text-xs text-slate-400 mb-1">نرخی هەر مەترێک</div>
                <div class="text-slate-800 font-medium">{{ $iqd($contractor->rate_per_meter) }} {{ $contractor->currency }}</div>
            </div>
        @else
            <div>
                <div class="text-xs text-slate-400 mb-1">کۆی قۆنتەرات</div>
                <div class="text-slate-800 font-medium">{{ $iqd($contractor->contract_amount) }} {{ $contractor->currency }}</div>
            </div>
        @endif
        <div>
            <div class="text-xs text-slate-400 mb-1">دۆخ</div>
            <div>{!! $contractor->is_active ? '<span class="badge-green">چالاک</span>' : '<span class="badge-slate">ناچالاک</span>' !!}</div>
        </div>
        @if($contractor->notes)
            <div class="col-span-2 sm:col-span-3">
                <div class="text-xs text-slate-400 mb-1">تێبینی</div>
                <div class="text-slate-800">{{ $contractor->notes }}</div>
            </div>
        @endif
    </div>
</div>

{{-- Totals --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
    <div class="stat-card">
        <span class="text-xs text-slate-500 font-medium">کۆی پارەدراو (دینار)</span>
        <div class="text-2xl font-extrabold text-green-600">{{ $iqd($paid->iqd ?? 0) }} د</div>
    </div>
    <div class="stat-card">
        <span class="text-xs text-slate-500 font-medium">کۆی پارەدراو (دۆلار)</span>
        <div class="text-2xl font-extrabold text-cyan-600">${{ number_format((float)($paid->usd ?? 0), 2) }}</div>
    </div>
    <div class="stat-card">
        <span class="text-xs text-slate-500 font-medium">کۆی مەترەکان</span>
        <div class="text-2xl font-extrabold text-slate-800">{{ number_format((float)($paid->m ?? 0), 2) }}</div>
    </div>
</div>

{{-- Payments --}}
<div class="card p-0">
    <div class="px-5 py-4 border-b border-slate-200">
        <h3 class="text-sm font-bold text-slate-800">پارەدانەکان</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">بڕ</th>
                    <th class="px-4 py-3 font-semibold">مەتر</th>
                    <th class="px-4 py-3 font-semibold">پێناسە</th>
                    <th class="px-4 py-3 font-semibold">بەروار</th>
                    <th class="px-4 py-3 font-semibold">کردار</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr class="table-row">
                        <td class="px-4 py-3 font-semibold text-slate-800">
                            @if($payment->currency == 'USD')
                                ${{ number_format((float)$payment->amount, 2) }}
                            @else
                                {{ $iqd($payment->amount) }} د
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $payment->meters ? number_format((float)$payment->meters, 2) : '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $payment->description ?: '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $payment->payment_date?->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('contractor-payments.destroy', $payment) }}" onsubmit="return confirm('دڵنیایت لە سڕینەوە؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger !px-3 !py-1.5">سڕینەوە</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-slate-400">هیچ پارەدانێک نییە.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $payments->links() }}
</div>
@endsection
