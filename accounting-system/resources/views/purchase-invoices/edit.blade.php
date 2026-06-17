@extends('layouts.app')

@section('title', 'دەستکاری وەسڵی کڕین')
@section('page-title', 'دەستکاری وەسڵی کڕین')
@section('page-subtitle', 'وەسڵی کڕین #' . $invoice->id)

@section('content')
<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">دەستکاری وەسڵی کڕین #{{ $invoice->id }}</h2>
    <a href="{{ route('purchase-invoices.show', $invoice) }}" class="btn-outline">گەڕانەوە</a>
</div>

@include('purchase-invoices._form', [
    'invoice'     => $invoice,
    'action'      => route('purchase-invoices.update', $invoice),
    'method'      => 'PUT',
    'submitLabel' => 'نوێکردنەوەی کڕین',
])
@endsection
