@extends('layouts.app')

@section('title', 'دەستکاری دابینکەر')
@section('page-title', 'دەستکاری دابینکەر')
@section('page-subtitle', $supplier->name)

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-slate-800">زانیاری دابینکەر</h2>
        <a href="{{ route('suppliers.show', $supplier) }}" class="btn-outline">گەڕانەوە</a>
    </div>

    <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="card p-5 space-y-4">
        @csrf
        @method('PUT')
        @include('suppliers._form', ['supplier' => $supplier])
        <div class="flex items-center gap-2 pt-2">
            <button type="submit" class="btn-primary">نوێکردنەوە</button>
            <a href="{{ route('suppliers.show', $supplier) }}" class="btn-outline">پاشگەزبوونەوە</a>
        </div>
    </form>
</div>
@endsection
