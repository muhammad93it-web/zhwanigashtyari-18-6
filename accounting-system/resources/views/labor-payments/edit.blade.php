@extends('layouts.app')

@section('title', 'دەستکاری کرێی کار')
@section('page-title', 'دەستکاری کرێی کار')
@section('page-subtitle', $payment->worker->name ?? $payment->worker_name)

@section('content')
<div class="max-w-4xl">
    @include('labor-payments._form', [
        'payment'     => $payment,
        'action'      => route('labor-payments.update', $payment),
        'method'      => 'PUT',
        'submitLabel' => 'نوێکردنەوە',
    ])
</div>
@endsection
