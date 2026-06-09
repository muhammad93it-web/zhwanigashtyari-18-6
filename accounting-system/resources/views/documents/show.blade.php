@extends('layouts.app')

@section('title', 'بینینی نووسراو')
@section('page-title', $document->title)
@section('page-subtitle', 'وردەکاری نووسراو')

@section('content')
<div class="max-w-3xl">
    <div class="flex items-center justify-between mb-4 gap-2 flex-wrap">
        <h2 class="text-base font-bold text-slate-800">{{ $document->title }}</h2>
        <div class="flex items-center gap-2">
            <a href="{{ route('documents.print', $document) }}" target="_blank" class="btn-info">چاپکردن</a>
            <a href="{{ route('documents.edit', $document) }}" class="btn-warning">دەستکاری</a>
            <form method="POST" action="{{ route('documents.destroy', $document) }}" onsubmit="return confirm('دڵنیایت لە سڕینەوە؟')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">سڕینەوە</button>
            </form>
            <a href="{{ route('documents.index') }}" class="btn-outline">گەڕانەوە</a>
        </div>
    </div>

    <div class="card p-5 mb-4">
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
            <div>
                <div class="text-xs text-slate-400 mb-1">جۆری نووسراو</div>
                <div class="text-slate-800 font-medium">{{ $document->doc_type ?: '—' }}</div>
            </div>
            <div>
                <div class="text-xs text-slate-400 mb-1">بۆ کێ</div>
                <div class="text-slate-800 font-medium">{{ $document->recipient ?: '—' }}</div>
            </div>
            <div>
                <div class="text-xs text-slate-400 mb-1">بەروار</div>
                <div class="text-slate-800 font-medium">{{ $document->doc_date?->format('Y-m-d') }}</div>
            </div>
            <div>
                <div class="text-xs text-slate-400 mb-1">ژمارەی ئاماژە</div>
                <div class="text-slate-800 font-medium">{{ $document->reference_number }}</div>
            </div>
            @if($document->notes)
                <div class="col-span-2 sm:col-span-3">
                    <div class="text-xs text-slate-400 mb-1">تێبینی</div>
                    <div class="text-slate-800">{{ $document->notes }}</div>
                </div>
            @endif
        </div>
    </div>

    <div class="card p-5">
        <h3 class="text-sm font-bold text-slate-800 mb-3">دەق</h3>
        <div class="text-slate-700 text-sm leading-relaxed whitespace-pre-line">{{ $document->body ?: '—' }}</div>
    </div>
</div>
@endsection
