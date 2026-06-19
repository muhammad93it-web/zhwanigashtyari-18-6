@extends('layouts.app')

@section('title', 'شۆفێری نوێ')
@section('page-title', 'شۆفێری نوێ')
@section('page-subtitle', 'زیادکردنی شۆفێر')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-slate-800">زانیاری شۆفێر</h2>
        <a href="{{ route('drivers.index') }}" class="btn-outline">گەڕانەوە</a>
    </div>

    <form method="POST" action="{{ route('drivers.store') }}" class="card p-5 space-y-4">
        @csrf
        @include('drivers._form', ['driver' => null])
        <div class="flex items-center gap-2 pt-2">
            <button type="submit" class="btn-primary">پاشەکەوتکردن</button>
            <a href="{{ route('drivers.index') }}" class="btn-outline">پاشگەزبوونەوە</a>
        </div>
    </form>
</div>
@endsection
