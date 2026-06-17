@extends('layouts.app')

@section('title', 'کرێی کاری نوێ')
@section('page-title', 'تۆمارکردنی کرێی کار')
@section('page-subtitle', 'کرێی کرێکار بۆ پڕۆژە')

@section('content')
<div class="max-w-4xl">
    @include('labor-payments._form', [
        'action'      => route('labor-payments.store'),
        'method'      => 'POST',
        'submitLabel' => 'تۆمارکردن',
    ])
</div>
@endsection
