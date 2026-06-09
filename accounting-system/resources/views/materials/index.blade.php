@extends('layouts.app')

@section('title', 'کۆگا (مەوادەکان)')
@section('page-title', 'کۆگا (مەوادەکان)')
@section('page-subtitle', 'بەڕێوەبردنی مەوادەکان و کۆگا')

@section('content')
@php
    $iqd = fn($v) => number_format((float) $v, 0);
@endphp

{{-- Top actions --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('materials.create') }}" class="btn-primary">+ مەواد نوێ</a>
        <a href="{{ route('materials.buy') }}" class="btn-warning">کڕین</a>
        <a href="{{ route('materials.sell') }}" class="btn-info">فرۆشتن</a>
    </div>
    <form method="GET" action="{{ route('materials.index') }}" class="flex items-center gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="گەڕان بەناو یان جۆر..." class="input-field w-48 sm:w-64">
        <button type="submit" class="btn-slate">گەڕان</button>
    </form>
</div>

{{-- Summary stat-cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <span class="text-xs text-slate-500 font-medium">کۆی مەوادەکان</span>
            <span class="badge-slate">#</span>
        </div>
        <div class="text-2xl font-extrabold text-slate-800">{{ $totals['count'] }}</div>
    </div>
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <span class="text-xs text-slate-500 font-medium">کۆگای کەم</span>
            <span class="{{ $totals['low_stock'] > 0 ? 'badge-amber' : 'badge-green' }}">!</span>
        </div>
        <div class="text-2xl font-extrabold {{ $totals['low_stock'] > 0 ? 'text-amber-500' : 'text-slate-800' }}">{{ $totals['low_stock'] }}</div>
    </div>
</div>

{{-- Materials table --}}
<div class="card p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">ناو</th>
                    <th class="px-4 py-3 font-semibold">جۆر</th>
                    <th class="px-4 py-3 font-semibold">کۆگا</th>
                    <th class="px-4 py-3 font-semibold">کەمترین</th>
                    <th class="px-4 py-3 font-semibold">دۆخ</th>
                    <th class="px-4 py-3 font-semibold">کردارەکان</th>
                </tr>
            </thead>
            <tbody>
                @forelse($materials as $material)
                    <tr class="table-row">
                        <td class="px-4 py-3 font-semibold text-slate-800">
                            {{ $material->name }}
                            @if($material->is_low_stock)
                                <span class="badge-amber mr-1">کۆگای کەم</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $material->category ?: '—' }}</td>
                        <td class="px-4 py-3 text-slate-800 font-medium">{{ $iqd($material->current_stock) }} {{ $material->unit }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $material->min_stock !== null ? $iqd($material->min_stock) . ' ' . $material->unit : '—' }}</td>
                        <td class="px-4 py-3">
                            @if($material->is_active)
                                <span class="badge-green">چالاک</span>
                            @else
                                <span class="badge-slate">ناچالاک</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('materials.show', $material) }}" class="btn-info !px-3 !py-1.5">بینین</a>
                                <a href="{{ route('materials.edit', $material) }}" class="btn-warning !px-3 !py-1.5">دەستکاری</a>
                                <form method="POST" action="{{ route('materials.destroy', $material) }}" onsubmit="return confirm('دڵنیایت لە سڕینەوە؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger !px-3 !py-1.5">سڕینەوە</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-slate-400">هیچ مەوادێک نییە.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $materials->links() }}
</div>
@endsection
