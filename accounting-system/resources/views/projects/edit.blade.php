@extends('layouts.app')

@section('title', 'دەستکاری پڕۆژە')
@section('page-title', 'دەستکاری پڕۆژە')
@section('page-subtitle', $project->name)

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-slate-800">زانیاری پڕۆژە</h2>
        <a href="{{ route('projects.show', $project) }}" class="btn-outline">گەڕانەوە</a>
    </div>

    <form method="POST" action="{{ route('projects.update', $project) }}" class="card p-5 space-y-4">
        @csrf
        @method('PUT')
        @include('projects._form', ['project' => $project])
        <div class="flex items-center gap-2 pt-2">
            <button type="submit" class="btn-primary">نوێکردنەوە</button>
            <a href="{{ route('projects.show', $project) }}" class="btn-outline">پاشگەزبوونەوە</a>
        </div>
    </form>
</div>
@endsection
