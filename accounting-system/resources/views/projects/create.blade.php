@extends('layouts.app')

@section('title', 'پڕۆژەی نوێ')
@section('page-title', 'پڕۆژەی نوێ')
@section('page-subtitle', 'زیادکردنی پڕۆژە')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-slate-800">زانیاری پڕۆژە</h2>
        <a href="{{ route('projects.index') }}" class="btn-outline">گەڕانەوە</a>
    </div>

    <form method="POST" action="{{ route('projects.store') }}" class="card p-5 space-y-4">
        @csrf
        @include('projects._form', ['project' => null])
        <div class="flex items-center gap-2 pt-2">
            <button type="submit" class="btn-primary">پاشەکەوتکردن</button>
            <a href="{{ route('projects.index') }}" class="btn-outline">پاشگەزبوونەوە</a>
        </div>
    </form>
</div>
@endsection
