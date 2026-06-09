@extends('layouts.app')

@section('title', $material->name)
@section('page-title', $material->name)
@section('page-subtitle', 'زانیاری مەواد و جووڵەکانی')

@section('content')
@php
    $iqd = fn($v) => number_format((float) $v, 0);
@endphp

{{-- Top actions --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('materials.buy') }}" class="btn-warning">کڕین</a>
        <a href="{{ route('materials.sell') }}" class="btn-info">فرۆشتن</a>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('materials.edit', $material) }}" class="btn-warning">دەستکاری</a>
        <a href="{{ route('materials.index') }}" class="btn-outline">گەڕانەوە</a>
    </div>
</div>

{{-- Material info --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-5">
    <div class="card p-5 lg:col-span-2">
        <h3 class="text-sm font-bold text-slate-800 mb-4">زانیاری مەواد</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <div class="text-xs text-slate-400 mb-1">ناو</div>
                <div class="font-semibold text-slate-800">{{ $material->name }}</div>
            </div>
            <div>
                <div class="text-xs text-slate-400 mb-1">یەکە</div>
                <div class="font-semibold text-slate-800">{{ $material->unit }}</div>
            </div>
            <div>
                <div class="text-xs text-slate-400 mb-1">جۆر</div>
                <div class="font-semibold text-slate-800">{{ $material->category ?: '—' }}</div>
            </div>
            <div>
                <div class="text-xs text-slate-400 mb-1">کەمترین کۆگا</div>
                <div class="font-semibold text-slate-800">{{ $material->min_stock !== null ? $iqd($material->min_stock) . ' ' . $material->unit : '—' }}</div>
            </div>
            <div>
                <div class="text-xs text-slate-400 mb-1">دۆخ</div>
                <div>
                    @if($material->is_active)
                        <span class="badge-green">چالاک</span>
                    @else
                        <span class="badge-slate">ناچالاک</span>
                    @endif
                </div>
            </div>
            @if($material->notes)
                <div class="sm:col-span-2">
                    <div class="text-xs text-slate-400 mb-1">تێبینی</div>
                    <div class="text-slate-700 whitespace-pre-line">{{ $material->notes }}</div>
                </div>
            @endif
        </div>
    </div>

    <div class="stat-card">
        <div class="flex items-center justify-between">
            <span class="text-xs text-slate-500 font-medium">کۆگای ئێستا</span>
            @if($material->is_low_stock)
                <span class="badge-amber">کۆگای کەم</span>
            @else
                <span class="badge-green">باشە</span>
            @endif
        </div>
        <div class="text-3xl font-extrabold {{ $material->is_low_stock ? 'text-amber-500' : 'text-slate-800' }}">{{ $iqd($material->current_stock) }}</div>
        <div class="text-[11px] text-slate-400">{{ $material->unit }}</div>
    </div>
</div>

{{-- Movements table --}}
<div class="card p-0">
    <div class="px-4 py-3 border-b border-slate-200">
        <h3 class="text-sm font-bold text-slate-800">جووڵەکانی کڕین و فرۆشتن</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">جۆر</th>
                    <th class="px-4 py-3 font-semibold">بڕ</th>
                    <th class="px-4 py-3 font-semibold">نرخی یەکە</th>
                    <th class="px-4 py-3 font-semibold">کۆی گشتی</th>
                    <th class="px-4 py-3 font-semibold">لایەن</th>
                    <th class="px-4 py-3 font-semibold">بەروار</th>
                    <th class="px-4 py-3 font-semibold">کردار</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $movement)
                    <tr class="table-row">
                        <td class="px-4 py-3">
                            @if($movement->type === 'purchase')
                                <span class="badge-amber">{{ $movement->type_name }}</span>
                            @else
                                <span class="badge-cyan">{{ $movement->type_name }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-800 font-medium">{{ $iqd($movement->quantity) }} {{ $material->unit }}</td>
                        <td class="px-4 py-3 text-slate-600">
                            {{ $movement->currency === 'USD' ? '$' . number_format((float) $movement->unit_price, 2) : $iqd($movement->unit_price) . ' د' }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-slate-800">
                            {{ $movement->currency === 'USD' ? '$' . number_format((float) $movement->amount, 2) : $iqd($movement->amount) . ' د' }}
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $movement->party_name ?: '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $movement->movement_date->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('movements.destroy', $movement) }}" onsubmit="return confirm('دڵنیایت لە سڕینەوە؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger !px-3 !py-1.5">سڕینەوە</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-slate-400">هیچ جووڵەیەک نییە.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $movements->links() }}
</div>
@endsection
