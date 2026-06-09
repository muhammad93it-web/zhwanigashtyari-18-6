@extends('layouts.app')

@section('title', 'وەستاکان')
@section('page-title', 'وەستاکان')
@section('page-subtitle', 'لیستی وەستا و بەڵێندەرەکان')

@section('content')
@php
    $iqd = fn($v) => number_format((float) $v, 0);
@endphp

<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">لیستی وەستاکان</h2>
    <a href="{{ route('contractors.create') }}" class="btn-warning">+ وەستای نوێ</a>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('contractors.index') }}" class="card p-4 mb-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
    <div>
        <label class="label">گەڕان</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="ناو یان مۆبایل" class="input-field">
    </div>
    <div>
        <label class="label">جۆری کار</label>
        <select name="work_type" class="input-field">
            <option value="">هەموو</option>
            <option value="per_meter" @selected(request('work_type')=='per_meter')>بە مەتر</option>
            <option value="contract" @selected(request('work_type')=='contract')>قۆنتەرات</option>
        </select>
    </div>
    <div class="flex items-end gap-2">
        <button type="submit" class="btn-info">گەڕان</button>
        <a href="{{ route('contractors.index') }}" class="btn-outline">سڕینەوە</a>
    </div>
</form>

<div class="card p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">ناو</th>
                    <th class="px-4 py-3 font-semibold">مۆبایل</th>
                    <th class="px-4 py-3 font-semibold">جۆری کار</th>
                    <th class="px-4 py-3 font-semibold">نرخ/کۆی قۆنتەرات</th>
                    <th class="px-4 py-3 font-semibold">پارەدان</th>
                    <th class="px-4 py-3 font-semibold">دۆخ</th>
                    <th class="px-4 py-3 font-semibold">کردارەکان</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contractors as $contractor)
                    <tr class="table-row">
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $contractor->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $contractor->phone ?: '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="{{ $contractor->work_type == 'per_meter' ? 'badge-cyan' : 'badge-amber' }}">{{ $contractor->work_type_name }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-600">
                            @if($contractor->work_type == 'per_meter')
                                {{ $iqd($contractor->rate_per_meter) }} / مەتر
                            @else
                                {{ $iqd($contractor->contract_amount) }}
                            @endif
                            <span class="text-xs text-slate-400">{{ $contractor->currency }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $contractor->payments_count }}</td>
                        <td class="px-4 py-3">
                            @if($contractor->is_active)
                                <span class="badge-green">چالاک</span>
                            @else
                                <span class="badge-slate">ناچالاک</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('contractors.show', $contractor) }}" class="btn-info !px-3 !py-1.5">بینین</a>
                                <a href="{{ route('contractors.edit', $contractor) }}" class="btn-warning !px-3 !py-1.5">دەستکاری</a>
                                <form method="POST" action="{{ route('contractors.destroy', $contractor) }}" onsubmit="return confirm('دڵنیایت لە سڕینەوە؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger !px-3 !py-1.5">سڕینەوە</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-slate-400">هیچ وەستایەک نییە.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $contractors->links() }}
</div>
@endsection
