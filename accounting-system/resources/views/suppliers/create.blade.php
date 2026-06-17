@extends('layouts.app')

@section('title', 'دابینکەری نوێ')
@section('page-title', 'دابینکەری نوێ')
@section('page-subtitle', 'زیادکردنی دابینکەر')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-slate-800">زانیاری دابینکەر</h2>
        <a href="{{ route('suppliers.index') }}" class="btn-outline">گەڕانەوە</a>
    </div>

    <form method="POST" action="{{ route('suppliers.store') }}" class="card p-5 space-y-4">
        @csrf
        @include('suppliers._form', ['supplier' => null])
        <div class="flex items-center gap-2 pt-2">
            <button type="submit" class="btn-primary">پاشەکەوتکردن</button>
            <a href="{{ route('suppliers.index') }}" class="btn-outline">پاشگەزبوونەوە</a>
        </div>
    </form>
</div>
@endsection
