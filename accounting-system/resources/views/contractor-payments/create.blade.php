@extends('layouts.app')

@section('title', 'پارەدانی نوێ بۆ وەستا')
@section('page-title', 'پارەدانی نوێ بۆ وەستا')
@section('page-subtitle', 'تۆمارکردنی پارەدان')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-slate-800">زانیاری پارەدان</h2>
        <a href="{{ route('contractor-payments.index') }}" class="btn-outline">گەڕانەوە</a>
    </div>

    <form method="POST" action="{{ route('contractor-payments.store') }}" class="card p-5 space-y-4">
        @csrf

        <div>
            <label class="label">وەستا *</label>
            <select name="contractor_id" class="input-field" required>
                <option value="">— هەڵبژێرە —</option>
                @foreach($contractors as $c)
                    <option value="{{ $c->id }}" @selected(old('contractor_id', $selected?->id)==$c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
            @error('contractor_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="label">دراو *</label>
                <select name="currency" class="input-field">
                    <option value="IQD" @selected(old('currency','IQD')=='IQD')>دینار (IQD)</option>
                    <option value="USD" @selected(old('currency')=='USD')>دۆلار (USD)</option>
                </select>
                @error('currency') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label">بڕ *</label>
                <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="input-field" required>
                @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <p class="text-xs text-slate-400 -mt-2">ڕێژەی ئێستا: {{ number_format((float)$currentRate, 0) }} دینار بۆ ١ دۆلار</p>

        <div>
            <label class="label">مەتر (بۆ وەستای بە مەتر)</label>
            <input type="number" step="0.001" name="meters" value="{{ old('meters') }}" class="input-field">
            @error('meters') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">پێناسە</label>
            <input type="text" name="description" value="{{ old('description') }}" class="input-field">
            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">بەرواری پارەدان *</label>
            <input type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" class="input-field" required>
            @error('payment_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">تێبینی</label>
            <textarea name="notes" rows="3" class="input-field">{{ old('notes') }}</textarea>
            @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-2 pt-2">
            <button type="submit" class="btn-primary">پاشەکەوتکردن</button>
            <a href="{{ route('contractor-payments.index') }}" class="btn-outline">پاشگەزبوونەوە</a>
        </div>
    </form>
</div>
@endsection
