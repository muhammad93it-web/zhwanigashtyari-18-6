@extends('layouts.app')

@section('title', 'مەواد نوێ')
@section('page-title', 'مەواد نوێ')
@section('page-subtitle', 'زیادکردنی مەوادی نوێ بۆ کۆگا')

@section('content')
<div class="max-w-2xl">
    <div class="card p-5">
        <form method="POST" action="{{ route('materials.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="label" for="name">ناوی مەواد <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" class="input-field" required>
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label" for="unit">یەکە <span class="text-red-500">*</span></label>
                    <input type="text" id="unit" name="unit" value="{{ old('unit') }}" placeholder="مەتر / دانە / کیلۆ" class="input-field" required>
                    @error('unit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label" for="category">جۆر</label>
                    <input type="text" id="category" name="category" value="{{ old('category') }}" class="input-field">
                    @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label" for="current_stock">کۆگای ئێستا</label>
                    <input type="number" step="0.001" id="current_stock" name="current_stock" value="{{ old('current_stock', 0) }}" class="input-field">
                    @error('current_stock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label" for="min_stock">کەمترین کۆگا</label>
                    <input type="number" step="0.001" id="min_stock" name="min_stock" value="{{ old('min_stock') }}" class="input-field">
                    @error('min_stock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="label" for="notes">تێبینی</label>
                <textarea id="notes" name="notes" rows="3" class="input-field">{{ old('notes') }}</textarea>
                @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-green-600 focus:ring-green-500">
                <label for="is_active" class="text-sm font-semibold text-slate-700">چالاک</label>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <button type="submit" class="btn-primary">پاشەکەوتکردن</button>
                <a href="{{ route('materials.index') }}" class="btn-outline">گەڕانەوە</a>
            </div>
        </form>
    </div>
</div>
@endsection
