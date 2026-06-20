@extends('layouts.app')

@section('title', 'دەستکاری نووسراو')
@section('page-title', 'دەستکاری نووسراوی فەرمی')
@section('page-subtitle', $letter->reference_number)

@section('content')
<div class="max-w-3xl">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-slate-800">زانیاری نووسراو</h2>
        <a href="{{ route('letters.index') }}" class="btn-outline">گەڕانەوە</a>
    </div>

    <form method="POST" action="{{ route('letters.update', $letter) }}" class="card p-5 space-y-4">
        @method('PUT')
        @include('letters._form')

        <div class="flex items-center gap-2 pt-2">
            <button type="submit" class="btn-primary">نوێکردنەوە</button>
            <a href="{{ route('letters.print', $letter) }}" target="_blank" class="btn-info">چاپکردن</a>
            <a href="{{ route('letters.index') }}" class="btn-outline">پاشگەزبوونەوە</a>
        </div>
    </form>
</div>
@endsection
