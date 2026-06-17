@extends('layouts.app')

@section('title', 'پارەدان بۆ دابینکەر')
@section('page-title', 'پارەدان بۆ دابینکەر')
@section('page-subtitle', $supplier->name)

@section('content')
@php $num = fn($v) => number_format((float) $v, 0); @endphp
<div class="max-w-xl">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-slate-800">پارەدان</h2>
        <a href="{{ route('suppliers.show', $supplier) }}" class="btn-outline">گەڕانەوە</a>
    </div>

    <div class="card p-4 mb-4 flex items-center justify-between text-sm">
        <span class="text-slate-500">باڵانسی ئێستا (قەرز)</span>
        <span class="font-bold {{ (float)$supplier->balance > 0 ? 'text-red-600' : 'text-green-700' }} text-lg">{{ $num($supplier->balance) }}</span>
    </div>

    <form method="POST" action="{{ route('suppliers.pay.store', $supplier) }}" class="card p-5 space-y-4">
        @csrf
        <div>
            <label class="label">بڕی پارەدان *</label>
            <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="input-field" required autofocus>
            @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="label">بەروار *</label>
            <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" class="input-field" required>
            @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="label">وەسف</label>
            <textarea name="description" rows="2" class="input-field" placeholder="هۆکاری پارەدان">{{ old('description') }}</textarea>
            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex items-center gap-2 pt-2">
            <button type="submit" class="btn-primary">تۆمارکردنی پارەدان</button>
            <a href="{{ route('suppliers.show', $supplier) }}" class="btn-outline">پاشگەزبوونەوە</a>
        </div>
    </form>
</div>
@endsection
