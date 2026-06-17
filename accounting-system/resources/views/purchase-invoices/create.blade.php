@extends('layouts.app')

@section('title', 'کڕینی نوێ')
@section('page-title', 'کڕینی مەواد بە وەسڵ')
@section('page-subtitle', 'تۆمارکردنی وەسڵی کڕین لە دابینکەر')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">وەسڵی کڕینی نوێ</h2>
    <a href="{{ route('purchase-invoices.index') }}" class="btn-outline">گەڕانەوە</a>
</div>

@include('purchase-invoices._form', [
    'action'      => route('purchase-invoices.store'),
    'method'      => 'POST',
    'submitLabel' => 'تۆمارکردنی کڕین',
])
@endsection
