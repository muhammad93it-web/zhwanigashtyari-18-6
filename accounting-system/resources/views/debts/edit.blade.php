@extends('layouts.app')

@section('title', 'دەستکاری قەرز')
@section('page-title', 'دەستکاری قەرز')
@section('page-subtitle', 'نوێکردنەوەی زانیاری قەرز')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">دەستکاری قەرز</h2>
    <a href="{{ route('debts.show', $debt) }}" class="btn-outline">گەڕانەوە</a>
</div>

<div class="card p-5 max-w-3xl">
    <form method="POST" action="{{ route('debts.update', $debt) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @csrf
        @method('PUT')

        <div class="sm:col-span-2">
            <label class="label">ناوی کەس <span class="text-red-500">*</span></label>
            <input type="text" name="party_name" value="{{ old('party_name', $debt->party_name) }}" class="input-field" required>
            @error('party_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">ئاراستە <span class="text-red-500">*</span></label>
            <select name="direction" class="input-field">
                <option value="receivable" {{ old('direction', $debt->direction) === 'receivable' ? 'selected' : '' }}>قەرزی لای خەڵک</option>
                <option value="payable" {{ old('direction', $debt->direction) === 'payable' ? 'selected' : '' }}>قەرزی ئێمە</option>
            </select>
            @error('direction') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">دراو</label>
            <select name="currency" class="input-field">
                <option value="IQD" {{ old('currency', $debt->currency) === 'IQD' ? 'selected' : '' }}>دینار (IQD)</option>
                <option value="USD" {{ old('currency', $debt->currency) === 'USD' ? 'selected' : '' }}>دۆلار (USD)</option>
            </select>
            @error('currency') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">بڕ <span class="text-red-500">*</span></label>
            <input type="number" step="0.01" name="amount" value="{{ old('amount', $debt->amount) }}" class="input-field" required>
            <p class="text-xs text-slate-400 mt-1">ڕێژەی ئێستا: {{ number_format($currentRate, 0) }} دینار بۆ ١ دۆلار</p>
            @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">بەرواری قەرز <span class="text-red-500">*</span></label>
            <input type="date" name="debt_date" value="{{ old('debt_date', $debt->debt_date->format('Y-m-d')) }}" class="input-field" required>
            @error('debt_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">بەرواری گەڕاندنەوە</label>
            <input type="date" name="due_date" value="{{ old('due_date', $debt->due_date?->format('Y-m-d')) }}" class="input-field">
            @error('due_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
            <label class="label">وەسف</label>
            <textarea name="description" rows="2" class="input-field">{{ old('description', $debt->description) }}</textarea>
            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
            <label class="label">تێبینی</label>
            <textarea name="notes" rows="3" class="input-field">{{ old('notes', $debt->notes) }}</textarea>
            @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2 flex gap-2">
            <button type="submit" class="btn-primary">نوێکردنەوە</button>
            <a href="{{ route('debts.show', $debt) }}" class="btn-outline">پاشگەزبوونەوە</a>
        </div>
    </form>
</div>
@endsection
