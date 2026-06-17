@extends('layouts.app')

@section('title', $project->name)
@section('page-title', $project->name)
@section('page-subtitle', 'تێچووی گشتیی پڕۆژە')

@section('content')
@php $num = fn($v) => number_format((float) $v, 0); @endphp

<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">{{ $project->name }}</h2>
    <div class="flex items-center gap-2">
        <a href="{{ route('projects.edit', $project) }}" class="btn-warning">دەستکاری</a>
        <a href="{{ route('projects.index') }}" class="btn-outline">گەڕانەوە</a>
    </div>
</div>

{{-- Project info --}}
<div class="card p-5 mb-4 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
    <div><div class="text-slate-400 text-xs">کڕیار</div><div class="font-semibold text-slate-800">{{ $project->client->name ?? '—' }}</div></div>
    <div><div class="text-slate-400 text-xs">شوێن</div><div class="font-semibold text-slate-800">{{ $project->location ?: '—' }}</div></div>
    <div><div class="text-slate-400 text-xs">بودجە</div><div class="font-semibold text-slate-800">{{ $project->budget ? $num($project->budget) : '—' }}</div></div>
    <div><div class="text-slate-400 text-xs">دۆخ</div><div class="font-semibold text-slate-800">{{ $project->status_name }}</div></div>
</div>

{{-- Cost summary --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
    <div class="stat-card">
        <div class="text-xs text-slate-400">تێچووی مەواد/کڕین</div>
        <div class="text-xl font-extrabold text-emerald-600">{{ $num($purchaseCost) }}</div>
    </div>
    <div class="stat-card">
        <div class="text-xs text-slate-400">خەرجی گشتی</div>
        <div class="text-xl font-extrabold text-amber-600">{{ $num($expenseCost) }}</div>
    </div>
    <div class="stat-card border-2 border-green-200">
        <div class="text-xs text-slate-400">کۆی گشتیی تێچوون</div>
        <div class="text-xl font-extrabold text-green-700">{{ $num($totalCost) }}</div>
    </div>
</div>

@if($project->budget)
    @php $remaining = (float) $project->budget - (float) $totalCost; @endphp
    <div class="card p-4 mb-5 text-sm flex items-center justify-between {{ $remaining < 0 ? 'border-red-200' : '' }}">
        <span class="text-slate-500">ماوە لە بودجە</span>
        <span class="font-bold {{ $remaining < 0 ? 'text-red-600' : 'text-green-700' }}">{{ $num($remaining) }}</span>
    </div>
@endif

{{-- Purchased materials for this project --}}
<div class="mb-2 mt-4 text-sm font-bold text-slate-700">مەوادە کڕدراوەکان بۆ ئەم پڕۆژەیە</div>
<div class="card p-0 mb-4">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">مەواد / جۆر</th>
                    <th class="px-4 py-3 font-semibold">دابینکەر</th>
                    <th class="px-4 py-3 font-semibold">بڕ</th>
                    <th class="px-4 py-3 font-semibold">نرخی یەکە</th>
                    <th class="px-4 py-3 font-semibold">کۆ</th>
                    <th class="px-4 py-3 font-semibold">بەروار</th>
                </tr>
            </thead>
            <tbody>
                @forelse($materials as $d)
                    <tr class="table-row">
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $d->material->name ?? $d->custom_type }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $d->invoice->supplier->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ rtrim(rtrim(number_format((float)$d->quantity,3),'0'),'.') }} {{ $d->unit }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $num($d->unit_price) }}</td>
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $num($d->line_total) }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ optional($d->invoice)->date?->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">هیچ کڕینێک بۆ ئەم پڕۆژەیە نییە.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mb-5">{{ $materials->links() }}</div>

{{-- Expenses for this project --}}
<div class="mb-2 text-sm font-bold text-slate-700">خەرجییەکانی ئەم پڕۆژەیە</div>
<div class="card p-0 mb-4">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">وەرگر/پارە بۆ</th>
                    <th class="px-4 py-3 font-semibold">جۆری خەرجی</th>
                    <th class="px-4 py-3 font-semibold">بڕ (IQD)</th>
                    <th class="px-4 py-3 font-semibold">بەروار</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $e)
                    <tr class="table-row">
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $e->payee }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $e->expense_type ?: ($e->category ?: '—') }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $num($e->amount_iqd) }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ optional($e->expense_date)->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">هیچ خەرجییەک بۆ ئەم پڕۆژەیە نییە.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div>{{ $expenses->links() }}</div>
@endsection
