@extends('layouts.app')

@section('title', 'دابینکەران')
@section('page-title', 'دابینکەران')
@section('page-subtitle', 'لیستی دابینکەرەکان و باڵانس')

@section('content')
@php $num = fn($v) => number_format((float) $v, 0); @endphp

<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">لیستی دابینکەران</h2>
    <a href="{{ route('suppliers.create') }}" class="btn-primary">+ دابینکەری نوێ</a>
</div>

<div class="grid grid-cols-2 gap-4 mb-4">
    <div class="stat-card">
        <div class="text-xs text-slate-400">کۆی قەرزمان (دینار)</div>
        <div class="text-lg font-extrabold {{ $totalIqd > 0 ? 'text-red-600' : 'text-green-700' }}">{{ $num($totalIqd) }} <span class="text-xs text-slate-400">د.ع</span></div>
    </div>
    <div class="stat-card">
        <div class="text-xs text-slate-400">کۆی قەرزمان (دۆلار)</div>
        <div class="text-lg font-extrabold {{ $totalUsd > 0 ? 'text-red-600' : 'text-green-700' }}">{{ $num($totalUsd) }} <span class="text-xs text-slate-400">$</span></div>
    </div>
</div>

<form method="GET" action="{{ route('suppliers.index') }}" class="card p-4 mb-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
    <div class="sm:col-span-2">
        <label class="label">گەڕان</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="ناو یان مۆبایل" class="input-field">
    </div>
    <div class="flex items-end gap-2">
        <button type="submit" class="btn-info">گەڕان</button>
        <a href="{{ route('suppliers.index') }}" class="btn-outline">سڕینەوە</a>
    </div>
</form>

<div class="card p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">ناو</th>
                    <th class="px-4 py-3 font-semibold">مۆبایل</th>
                    <th class="px-4 py-3 font-semibold">قەرز (دینار)</th>
                    <th class="px-4 py-3 font-semibold">قەرز (دۆلار)</th>
                    <th class="px-4 py-3 font-semibold">دۆخ</th>
                    <th class="px-4 py-3 font-semibold">کردارەکان</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                    <tr class="table-row">
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $supplier->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $supplier->phone ?: '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="font-bold {{ (float)$supplier->balance_iqd > 0 ? 'text-red-600' : 'text-green-700' }}">{{ $num($supplier->balance_iqd) }} <span class="text-[10px] text-slate-400">د.ع</span></span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-bold {{ (float)$supplier->balance_usd > 0 ? 'text-red-600' : 'text-green-700' }}">{{ $num($supplier->balance_usd) }} <span class="text-[10px] text-slate-400">$</span></span>
                        </td>
                        <td class="px-4 py-3">
                            @if($supplier->is_active)<span class="badge-green">چالاک</span>@else<span class="badge-slate">ناچالاک</span>@endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('suppliers.show', $supplier) }}" class="btn-info !px-3 !py-1.5">کشف حساب</a>
                                <a href="{{ route('suppliers.pay', $supplier) }}" class="btn-primary !px-3 !py-1.5">پارەدان</a>
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn-warning !px-3 !py-1.5">دەستکاری</a>
                                <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" onsubmit="return confirm('دڵنیایت لە سڕینەوە؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger !px-3 !py-1.5">سڕینەوە</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-slate-400">هیچ دابینکەرێک نییە.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $suppliers->links() }}</div>
@endsection
