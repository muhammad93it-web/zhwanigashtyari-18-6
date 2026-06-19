@extends('layouts.app')

@section('title', 'تۆماری گواستنەوەی نوێ')
@section('page-title', 'تۆماری گواستنەوە و شۆفێر')
@section('page-subtitle', 'تۆمارکردنی کرێی گواستنەوە و پارەدان')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">تۆماری گواستنەوەی نوێ</h2>
    <a href="{{ route('driver-trip-logs.index') }}" class="btn-outline">گەڕانەوە</a>
</div>

@include('driver-trip-logs._form', [
    'action'      => route('driver-trip-logs.store'),
    'method'      => 'POST',
    'submitLabel' => 'تۆمارکردنی گواستنەوە',
])
@endsection
