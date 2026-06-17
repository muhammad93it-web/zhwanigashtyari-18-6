@extends('layouts.app')

@section('title', 'کرێکاری نوێ')
@section('page-title', 'کرێکاری نوێ')
@section('page-subtitle', 'زیادکردنی کرێکار')

@section('content')
<div class="max-w-2xl">
    <div class="card p-5">
        @include('workers._form', [
            'action'      => route('workers.store'),
            'method'      => 'POST',
            'submitLabel' => 'زیادکردنی کرێکار',
        ])
    </div>
</div>
@endsection
