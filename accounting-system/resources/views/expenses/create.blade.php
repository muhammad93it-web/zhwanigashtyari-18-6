@extends('layouts.app')

@section('title', 'زیادکردنی خەرجی')
@section('page-title', 'زیادکردنی خەرجی')
@section('page-subtitle', 'تۆمارکردنی خەرجکردنی پارەی نوێ')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">خەرجی نوێ</h2>
    <a href="{{ route('expenses.index') }}" class="btn-outline">گەڕانەوە</a>
</div>

<div class="card p-5 max-w-3xl">
    <form method="POST" action="{{ route('expenses.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @csrf

        <div class="sm:col-span-2">
            <label class="label">وەرگر <span class="text-red-500">*</span></label>
            <input type="text" name="payee" value="{{ old('payee') }}" class="input-field" required>
            @error('payee') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">پڕۆژە (ئیختیاری)</label>
            <select name="project_id" class="input-field">
                <option value="">— بێ پڕۆژە —</option>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}" @selected(old('project_id')==$p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
            @error('project_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">جۆری خەرجی</label>
            <input type="text" name="expense_type" value="{{ old('expense_type') }}" class="input-field" placeholder="نموونە: کرێی کرێکار، گواستنەوە">
            @error('expense_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">جۆر</label>
            <input type="text" name="category" value="{{ old('category') }}" class="input-field">
            @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">بەرواری خەرجی <span class="text-red-500">*</span></label>
            <input type="date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" class="input-field" required>
            @error('expense_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">دراو</label>
            <select name="currency" class="input-field">
                <option value="IQD" {{ old('currency') === 'IQD' ? 'selected' : '' }}>دینار (IQD)</option>
                <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>دۆلار (USD)</option>
            </select>
            @error('currency') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">بڕ <span class="text-red-500">*</span></label>
            <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="input-field" required>
            <p class="text-xs text-slate-400 mt-1">ڕێژەی ئێستا: {{ number_format($currentRate, 0) }} دینار بۆ ١ دۆلار</p>
            @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
            <label class="label">وەسف</label>
            <textarea name="description" rows="2" class="input-field">{{ old('description') }}</textarea>
            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
            <label class="label">هۆکاری خەرجکردن</label>
            <textarea name="reason_description" rows="2" class="input-field" placeholder="بۆچی ئەم پارەیە خەرجکرا؟">{{ old('reason_description') }}</textarea>
            @error('reason_description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
            <label class="label">تێبینی</label>
            <textarea name="notes" rows="3" class="input-field">{{ old('notes') }}</textarea>
            @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2 flex gap-2">
            <button type="submit" class="btn-primary">پاشەکەوتکردن</button>
            <a href="{{ route('expenses.index') }}" class="btn-outline">پاشگەزبوونەوە</a>
        </div>
    </form>
</div>
@endsection
