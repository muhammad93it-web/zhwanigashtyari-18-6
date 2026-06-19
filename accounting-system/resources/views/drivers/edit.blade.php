@extends('layouts.app')

@section('title', 'دەستکاری شۆفێر')
@section('page-title', 'دەستکاری شۆفێر')
@section('page-subtitle', $driver->name)

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-slate-800">زانیاری شۆفێر</h2>
        <a href="{{ route('drivers.show', $driver) }}" class="btn-outline">گەڕانەوە</a>
    </div>

    <form method="POST" action="{{ route('drivers.update', $driver) }}" class="card p-5 space-y-4">
        @csrf
        @method('PUT')
        @include('drivers._form', ['driver' => $driver])
        <div class="flex items-center gap-2 pt-2">
            <button type="submit" class="btn-primary">نوێکردنەوە</button>
            <a href="{{ route('drivers.show', $driver) }}" class="btn-outline">پاشگەزبوونەوە</a>
        </div>
    </form>
</div>
@endsection
