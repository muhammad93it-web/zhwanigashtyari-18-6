@extends('layouts.app')

@section('title', 'پێدانی پارەی وەستا')
@section('page-title', 'پێدانی پارەی وەستا')
@section('page-subtitle', 'لیستی پارەدانەکانی وەستا')

@section('content')
@php
    $iqd = fn($v) => number_format((float) $v, 0);
@endphp

<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">پارەدانەکان</h2>
    <a href="{{ route('contractor-payments.create') }}" class="btn-primary">+ پارەدان</a>
</div>

{{-- Totals --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
    <div class="stat-card">
        <span class="text-xs text-slate-500 font-medium">کۆی پارەدراو (دینار)</span>
        <div class="text-2xl font-extrabold text-green-600">{{ $iqd($totals->iqd ?? 0) }} د</div>
    </div>
    <div class="stat-card">
        <span class="text-xs text-slate-500 font-medium">کۆی پارەدراو (دۆلار)</span>
        <div class="text-2xl font-extrabold text-cyan-600">${{ number_format((float)($totals->usd ?? 0), 2) }}</div>
    </div>
    <div class="stat-card">
        <span class="text-xs text-slate-500 font-medium">ژمارەی پارەدانەکان</span>
        <div class="text-2xl font-extrabold text-slate-800">{{ $totals->c ?? 0 }}</div>
    </div>
</div>

{{-- Filter --}}
<form method="GET" action="{{ route('contractor-payments.index') }}" class="card p-4 mb-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
    <div>
        <label class="label">وەستا</label>
        <select name="contractor_id" class="input-field">
            <option value="">هەموو</option>
            @foreach($contractors as $c)
                <option value="{{ $c->id }}" @selected(request('contractor_id')==$c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex items-end gap-2">
        <button type="submit" class="btn-info">گەڕان</button>
        <a href="{{ route('contractor-payments.index') }}" class="btn-outline">سڕینەوە</a>
    </div>
</form>

<div class="card p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">وەستا</th>
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
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $payment->contractor?->name ?: '—' }}</td>
                        <td class="px-4 py-3 text-slate-700">
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
                        <td colspan="6" class="px-4 py-10 text-center text-slate-400">هیچ پارەدانێک نییە.</td>
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
