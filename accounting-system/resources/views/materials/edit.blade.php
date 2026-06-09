@extends('layouts.app')

@section('title', 'دەستکاری مەواد')
@section('page-title', 'دەستکاری مەواد')
@section('page-subtitle', $material->name)

@section('content')
<div class="max-w-2xl">
    <div class="card p-5">
        <form method="POST" action="{{ route('materials.update', $material) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="label" for="name">ناوی مەواد <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $material->name) }}" class="input-field" required>
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label" for="unit">یەکە <span class="text-red-500">*</span></label>
                    <input type="text" id="unit" name="unit" value="{{ old('unit', $material->unit) }}" placeholder="مەتر / دانە / کیلۆ" class="input-field" required>
                    @error('unit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label" for="category">جۆر</label>
                    <input type="text" id="category" name="category" value="{{ old('category', $material->category) }}" class="input-field">
                    @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="label" for="min_stock">کەمترین کۆگا</label>
                <input type="number" step="0.001" id="min_stock" name="min_stock" value="{{ old('min_stock', $material->min_stock) }}" class="input-field">
                @error('min_stock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-slate-400 mt-1">کۆگای ئێستا ({{ number_format((float) $material->current_stock, 0) }} {{ $material->unit }}) تەنها لە ڕێگەی کڕین/فرۆشتنەوە دەگۆڕێت.</p>
            </div>

            <div>
                <label class="label" for="notes">تێبینی</label>
                <textarea id="notes" name="notes" rows="3" class="input-field">{{ old('notes', $material->notes) }}</textarea>
                @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $material->is_active) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-green-600 focus:ring-green-500">
                <label for="is_active" class="text-sm font-semibold text-slate-700">چالاک</label>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <button type="submit" class="btn-primary">نوێکردنەوە</button>
                <a href="{{ route('materials.show', $material) }}" class="btn-outline">گەڕانەوە</a>
            </div>
        </form>
    </div>
</div>
@endsection
