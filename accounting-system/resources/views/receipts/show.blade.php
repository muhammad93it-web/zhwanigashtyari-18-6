@extends('layouts.app')
@section('title', 'وەسڵ — ' . $transaction->reference_number)
@section('page-title', 'وەسڵی مامەڵە')
@section('page-subtitle', $transaction->reference_number)

@section('content')
<div class="max-w-lg space-y-4 animate-fade-in">
    <div class="card p-6 text-center border border-green-300">

        <div class="mb-4">
            <div class="text-xs text-slate-500">سیستەمی ژمێریاری</div>
            <div class="text-xl font-bold text-green-600">ژوانی گەشتیاری</div>
        </div>

        <span class="{{ $transaction->type_badge }} mb-4 inline-block">{{ $transaction->type_name }}</span>

        <div class="text-4xl font-bold text-slate-800 font-mono my-4">
            @if($transaction->currency === 'USD')
                ${{ number_format($transaction->amount, 2) }}
            @else
                {{ number_format($transaction->amount, 0) }} د.ع
            @endif
        </div>

        <div class="text-sm text-slate-500 mb-6">{{ $transaction->description }}</div>

        <div class="text-right space-y-2 text-sm border-t border-slate-200 pt-4">
            <div class="flex justify-between"><span class="text-slate-500">کڕیار:</span><span class="text-slate-800 font-semibold">{{ $transaction->client?->name }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">بەروار:</span><span class="text-slate-800">{{ $transaction->transaction_date->format('Y/m/d') }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">بڕی دۆلار:</span><span class="text-green-600 font-mono">${{ number_format($transaction->amount_usd, 2) }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">بڕی دینار:</span><span class="text-amber-500 font-mono">{{ number_format($transaction->amount_iqd, 0) }} د.ع</span></div>
            <div class="flex justify-between"><span class="text-slate-500">ڕێژەی گۆڕین (تۆماركراو):</span><span class="text-slate-600 font-mono">{{ number_format($transaction->exchange_rate_usd_to_iqd, 0) }}</span></div>
        </div>

        <div class="mt-6 flex gap-2 justify-center">
            <a href="{{ route('transactions.print', $transaction) }}" target="_blank" class="btn-warning">🖨️ چاپکردن</a>
            <a href="{{ route('transactions.show', $transaction) }}" class="btn-outline">وردبوونەوە</a>
        </div>
    </div>
</div>
@endsection
