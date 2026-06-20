@extends('layouts.app')

@section('title', 'نووسراوی فەرمی نوێ')
@section('page-title', 'نووسراوی فەرمی نوێ')
@section('page-subtitle', 'نووسینی نامەی فەرمی لەسەر لێتەرهێد')

@section('content')
<div class="max-w-3xl">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-slate-800">زانیاری نووسراو</h2>
        <a href="{{ route('letters.index') }}" class="btn-outline">گەڕانەوە</a>
    </div>

    <form method="POST" action="{{ route('letters.store') }}" class="card p-5 space-y-4">
        @include('letters._form')

        <div class="flex items-center gap-2 pt-2">
            <button type="submit" class="btn-primary">پاشەکەوتکردن</button>
            <a href="{{ route('letters.index') }}" class="btn-outline">پاشگەزبوونەوە</a>
        </div>
    </form>
</div>
@endsection
