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
                <input type="text" id="name" name="name" value="{{ old('name', $material->name) }}" class="input-field" required autofocus>
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-slate-400 mt-1">کۆگای ئێستا: {{ number_format((float) $material->current_stock, 0) }} {{ $material->unit }} — تەنها لە ڕێگەی کڕین/فرۆشتنەوە دەگۆڕێت.</p>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <button type="submit" class="btn-primary">نوێکردنەوە</button>
                <a href="{{ route('materials.show', $material) }}" class="btn-outline">گەڕانەوە</a>
            </div>
        </form>
    </div>
</div>
@endsection
