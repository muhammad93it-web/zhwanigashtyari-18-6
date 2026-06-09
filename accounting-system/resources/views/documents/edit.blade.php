@extends('layouts.app')

@section('title', 'دەستکاری نووسراو')
@section('page-title', 'دەستکاری نووسراو')
@section('page-subtitle', $document->title)

@section('content')
<div class="max-w-3xl">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-slate-800">دەستکاریکردنی نووسراو</h2>
        <a href="{{ route('documents.show', $document) }}" class="btn-outline">گەڕانەوە</a>
    </div>

    <form method="POST" action="{{ route('documents.update', $document) }}" class="card p-5 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="label">ناونیشان *</label>
            <input type="text" name="title" value="{{ old('title', $document->title) }}" class="input-field" required>
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="label">جۆری نووسراو</label>
                <input type="text" name="doc_type" value="{{ old('doc_type', $document->doc_type) }}" class="input-field">
                @error('doc_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label">بۆ کێ</label>
                <input type="text" name="recipient" value="{{ old('recipient', $document->recipient) }}" class="input-field">
                @error('recipient') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="label">دەق</label>
            <textarea name="body" rows="10" class="input-field">{{ old('body', $document->body) }}</textarea>
            @error('body') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">بەروار *</label>
            <input type="date" name="doc_date" value="{{ old('doc_date', $document->doc_date?->format('Y-m-d')) }}" class="input-field" required>
            @error('doc_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">تێبینی</label>
            <textarea name="notes" rows="3" class="input-field">{{ old('notes', $document->notes) }}</textarea>
            @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-2 pt-2">
            <button type="submit" class="btn-primary">نوێکردنەوە</button>
            <a href="{{ route('documents.show', $document) }}" class="btn-outline">پاشگەزبوونەوە</a>
        </div>
    </form>
</div>
@endsection
