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
                <input type="text" id="name" name="name" value="{{ old('name') }}" class="input-field" required autofocus>
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-slate-400 mt-1">تەنها ناوی مەواد پێویستە. کۆگا لە ڕێگەی کڕینەوە نوێدەبێتەوە.</p>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <button type="submit" class="btn-primary">پاشەکەوتکردن</button>
                <a href="{{ route('materials.index') }}" class="btn-outline">گەڕانەوە</a>
            </div>
        </form>
    </div>
</div>
@endsection
