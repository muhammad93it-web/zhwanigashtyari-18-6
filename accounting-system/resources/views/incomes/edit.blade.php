@extends('layouts.app')

@section('title', 'دەستکاری داهات')
@section('page-title', 'دەستکاری داهات')
@section('page-subtitle', 'نوێکردنەوەی زانیاری داهات')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">دەستکاری داهات</h2>
    <a href="{{ route('incomes.show', $income) }}" class="btn-outline">گەڕانەوە</a>
</div>

<div class="card p-5 max-w-3xl">
    <form method="POST" action="{{ route('incomes.update', $income) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @csrf
        @method('PUT')

        <div class="sm:col-span-2">
            <label class="label">سەرچاوە <span class="text-red-500">*</span></label>
            <input type="text" name="source" value="{{ old('source', $income->source) }}" class="input-field" required>
            @error('source') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">جۆر</label>
            <input type="text" name="category" value="{{ old('category', $income->category) }}" class="input-field">
            @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">بەرواری داهات <span class="text-red-500">*</span></label>
            <input type="date" name="income_date" value="{{ old('income_date', $income->income_date->format('Y-m-d')) }}" class="input-field" required>
            @error('income_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">دراو</label>
            <select name="currency" class="input-field">
                <option value="IQD" {{ old('currency', $income->currency) === 'IQD' ? 'selected' : '' }}>دینار (IQD)</option>
                <option value="USD" {{ old('currency', $income->currency) === 'USD' ? 'selected' : '' }}>دۆلار (USD)</option>
            </select>
            @error('currency') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">بڕ <span class="text-red-500">*</span></label>
            <input type="number" step="0.01" name="amount" value="{{ old('amount', $income->amount) }}" class="input-field" required>
            <p class="text-xs text-slate-400 mt-1">ڕێژەی ئێستا: {{ number_format($currentRate, 0) }} دینار بۆ ١ دۆلار</p>
            @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
            <label class="label">وەسف</label>
            <textarea name="description" rows="2" class="input-field">{{ old('description', $income->description) }}</textarea>
            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
            <label class="label">تێبینی</label>
            <textarea name="notes" rows="3" class="input-field">{{ old('notes', $income->notes) }}</textarea>
            @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2 flex gap-2">
            <button type="submit" class="btn-primary">نوێکردنەوە</button>
            <a href="{{ route('incomes.show', $income) }}" class="btn-outline">پاشگەزبوونەوە</a>
        </div>
    </form>
</div>
@endsection
