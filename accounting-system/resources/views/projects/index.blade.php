@extends('layouts.app')

@section('title', 'پڕۆژەکان')
@section('page-title', 'پڕۆژەکان')
@section('page-subtitle', 'لیستی پڕۆژە و بیناکان')

@section('content')
@php
    $num = fn($v) => number_format((float) $v, 0);
    $statusBadge = ['active' => 'badge-green', 'completed' => 'badge-cyan', 'on_hold' => 'badge-amber'];
@endphp

<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">لیستی پڕۆژەکان</h2>
    <a href="{{ route('projects.create') }}" class="btn-primary">+ پڕۆژەی نوێ</a>
</div>

<form method="GET" action="{{ route('projects.index') }}" class="card p-4 mb-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
    <div>
        <label class="label">گەڕان</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="ناو یان شوێن" class="input-field">
    </div>
    <div>
        <label class="label">دۆخ</label>
        <select name="status" class="input-field">
            <option value="">هەموو</option>
            @foreach(\App\Models\Project::STATUSES as $k => $v)
                <option value="{{ $k }}" @selected(request('status')==$k)>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex items-end gap-2">
        <button type="submit" class="btn-info">گەڕان</button>
        <a href="{{ route('projects.index') }}" class="btn-outline">سڕینەوە</a>
    </div>
</form>

<div class="card p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">ناوی پڕۆژە</th>
                    <th class="px-4 py-3 font-semibold">کڕیار</th>
                    <th class="px-4 py-3 font-semibold">شوێن</th>
                    <th class="px-4 py-3 font-semibold">بودجە</th>
                    <th class="px-4 py-3 font-semibold">دۆخ</th>
                    <th class="px-4 py-3 font-semibold">کردارەکان</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                    <tr class="table-row">
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $project->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $project->client->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $project->location ?: '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $project->budget ? $num($project->budget) : '—' }}</td>
                        <td class="px-4 py-3"><span class="{{ $statusBadge[$project->status] ?? 'badge-slate' }}">{{ $project->status_name }}</span></td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('projects.show', $project) }}" class="btn-info !px-3 !py-1.5">بینین</a>
                                <a href="{{ route('projects.edit', $project) }}" class="btn-warning !px-3 !py-1.5">دەستکاری</a>
                                <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('دڵنیایت لە سڕینەوە؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger !px-3 !py-1.5">سڕینەوە</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-slate-400">هیچ پڕۆژەیەک نییە.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $projects->links() }}</div>
@endsection
