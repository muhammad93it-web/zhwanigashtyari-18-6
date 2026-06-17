@extends('layouts.app')

@section('title', $supplier->name)
@section('page-title', $supplier->name)
@section('page-subtitle', 'کشف حسابی دابینکەر')

@section('content')
@php $num = fn($v) => number_format((float) $v, 0); @endphp

<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">{{ $supplier->name }}</h2>
    <div class="flex items-center gap-2">
        <a href="{{ route('suppliers.pay', $supplier) }}" class="btn-primary">پارەدان</a>
        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn-warning">دەستکاری</a>
        <a href="{{ route('suppliers.index') }}" class="btn-outline">گەڕانەوە</a>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
    <div class="stat-card"><div class="text-xs text-slate-400">مۆبایل</div><div class="font-semibold text-slate-800">{{ $supplier->phone ?: '—' }}</div></div>
    <div class="stat-card"><div class="text-xs text-slate-400">دۆخ</div><div class="font-semibold text-slate-800">{{ $supplier->is_active ? 'چالاک' : 'ناچالاک' }}</div></div>
    <div class="stat-card border-2 {{ (float)$supplier->balance > 0 ? 'border-red-200' : 'border-green-200' }}">
        <div class="text-xs text-slate-400">باڵانسی ئێستا (قەرز)</div>
        <div class="text-xl font-extrabold {{ (float)$supplier->balance > 0 ? 'text-red-600' : 'text-green-700' }}">{{ $num($supplier->balance) }}</div>
    </div>
</div>

@if($supplier->notes)
    <div class="card p-4 mb-4 text-sm text-slate-600">{{ $supplier->notes }}</div>
@endif

<div class="mb-2 text-sm font-bold text-slate-700">مامەڵەکان (کشف حساب)</div>
<div class="card p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">بەروار</th>
                    <th class="px-4 py-3 font-semibold">جۆر</th>
                    <th class="px-4 py-3 font-semibold">وەسف</th>
                    <th class="px-4 py-3 font-semibold">بڕ</th>
                    <th class="px-4 py-3 font-semibold">باڵانس دوای مامەڵە</th>
                    <th class="px-4 py-3 font-semibold">بەکارهێنەر</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $t)
                    <tr class="table-row">
                        <td class="px-4 py-3 text-slate-500">{{ optional($t->date)->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">
                            <span class="{{ $t->type == 'purchase' ? 'badge-purchase' : 'badge-green' }}">{{ $t->type_name }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $t->description ?: '—' }}</td>
                        <td class="px-4 py-3 font-semibold {{ $t->type == 'purchase' ? 'text-red-600' : 'text-green-700' }}">
                            {{ $t->type == 'purchase' ? '+' : '−' }}{{ $num($t->amount) }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $num($t->balance_after) }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $t->user->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-slate-400">هیچ مامەڵەیەک نییە.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $transactions->links() }}</div>
@endsection
