@extends('layouts.app')

@section('title', 'دەستکاری تۆماری گواستنەوە')
@section('page-title', 'دەستکاری تۆماری گواستنەوە')
@section('page-subtitle', 'تۆماری گواستنەوە #' . $log->id)

@section('content')
<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">دەستکاری تۆماری گواستنەوە #{{ $log->id }}</h2>
    <a href="{{ route('driver-trip-logs.show', $log) }}" class="btn-outline">گەڕانەوە</a>
</div>

@include('driver-trip-logs._form', [
    'log'         => $log,
    'action'      => route('driver-trip-logs.update', $log),
    'method'      => 'PUT',
    'submitLabel' => 'نوێکردنەوەی گواستنەوە',
])
@endsection
