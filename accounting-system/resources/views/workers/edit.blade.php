@extends('layouts.app')

@section('title', 'دەستکاری کرێکار')
@section('page-title', 'دەستکاری کرێکار')
@section('page-subtitle', $worker->name)

@section('content')
<div class="max-w-2xl">
    <div class="card p-5">
        @include('workers._form', [
            'worker'      => $worker,
            'action'      => route('workers.update', $worker),
            'method'      => 'PUT',
            'submitLabel' => 'نوێکردنەوە',
        ])
    </div>
</div>
@endsection
