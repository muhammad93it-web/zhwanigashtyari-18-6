@extends('layouts.app')
@section('title', 'وردبوونەوەی مامەڵە')
@section('page-title', 'وردبوونەوەی مامەڵە')
@section('page-subtitle', $transaction->reference_number)

@section('content')
<div class="max-w-2xl space-y-5 animate-fade-in">

    <!-- Header Card -->
    <div class="card p-6">
        <div class="flex items-start justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="{{ $transaction->type_badge }} text-sm">{{ $transaction->type_name }}</span>
                    <span class="text-xs font-mono text-teal-500">{{ $transaction->reference_number }}</span>
                </div>
                <h2 class="text-xl font-bold text-white">{{ $transaction->description }}</h2>
                @if($transaction->notes)
                <p class="text-sm text-teal-400 mt-1">{{ $transaction->notes }}</p>
                @endif
            </div>
            <div class="flex gap-2 flex-shrink-0">
                <a href="{{ route('transactions.print', $transaction) }}" target="_blank" class="btn-gold flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"/></svg>
                    چاپی وەسڵ
                </a>
                <a href="{{ route('transactions.index') }}" class="btn-outline">← گەڕانەوە</a>
            </div>
        </div>

        <!-- Amount Display -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-5 rounded-2xl bg-teal-950/50 border border-teal-700/30">
            <div class="text-center">
                <div class="text-xs text-teal-500 mb-1">بڕی ئەسڵی</div>
                <div class="text-2xl font-bold text-white font-mono">
                    {{ $transaction->currency === 'USD' ? '$' : '' }}{{ number_format($transaction->amount, 2) }}{{ $transaction->currency === 'IQD' ? ' د.ع' : '' }}
                </div>
                <div class="text-xs text-teal-600 mt-1">{{ $transaction->currency }}</div>
            </div>
            <div class="text-center border-x border-teal-700/30">
                <div class="text-xs text-teal-500 mb-1">بڕی دۆلار</div>
                <div class="text-2xl font-bold text-emerald-400 font-mono">${{ number_format($transaction->amount_usd, 2) }}</div>
                <div class="text-xs text-teal-600 mt-1">USD</div>
            </div>
            <div class="text-center">
                <div class="text-xs text-teal-500 mb-1">بڕی دینار</div>
                <div class="text-2xl font-bold text-amber-400 font-mono">{{ number_format($transaction->amount_iqd, 0) }}</div>
                <div class="text-xs text-teal-600 mt-1">IQD</div>
            </div>
        </div>

        <!-- Locked Rate Notice -->
        <div class="mt-4 flex items-center gap-2 text-xs text-teal-600 bg-teal-900/30 rounded-lg px-4 py-2.5">
            <svg class="w-3.5 h-3.5 text-teal-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
            <span>ڕێژەی گۆڕین تۆماركراو: <strong class="text-teal-400 font-mono">{{ number_format($transaction->exchange_rate_usd_to_iqd, 2) }}</strong> دینار/دۆلار — گۆڕانی تێدا ناکرێت</span>
        </div>
    </div>

    <!-- Details -->
    <div class="card p-6 space-y-4">
        <h3 class="font-bold text-white text-sm border-b border-teal-700/30 pb-3">زانیاری مامەڵەکە</h3>

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <div class="text-xs text-teal-500 mb-1">کڕیار</div>
                <a href="{{ route('clients.show', $transaction->client) }}" class="text-white font-semibold hover:text-gold-400 transition-colors">{{ $transaction->client?->name }}</a>
            </div>
            <div>
                <div class="text-xs text-teal-500 mb-1">بەروار</div>
                <div class="text-white font-semibold">{{ $transaction->transaction_date->format('Y/m/d') }}</div>
            </div>
            <div>
                <div class="text-xs text-teal-500 mb-1">تۆمارکەر</div>
                <div class="text-teal-300">{{ $transaction->user?->name ?? '—' }}</div>
            </div>
            <div>
                <div class="text-xs text-teal-500 mb-1">کاتی تۆمارکردن</div>
                <div class="text-teal-300 font-mono text-xs">{{ $transaction->created_at->format('Y/m/d H:i') }}</div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex gap-3">
        <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" onsubmit="return confirm('دڵنیاکارتەوە دەیەت ئەم مامەڵەیە بسڕیتەوە؟ ئەمە گەڕاندنەوەی نییە.')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-danger">سڕینەوەی مامەڵە</button>
        </form>
    </div>

</div>
@endsection
