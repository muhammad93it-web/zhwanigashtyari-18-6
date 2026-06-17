@extends('layouts.app')

@section('title', 'کەشف حساب')
@section('page-title', 'کەشف حساب')
@section('page-subtitle', 'ناوی کەسەکە هەڵبژێرە بۆ بینینی کەشف حسابەکەی')

@section('content')
@php $num = fn($v) => number_format((float) $v, 0); @endphp

{{-- هەڵبژاردنی خێرای ناو --}}
<form method="POST" action="{{ route('suppliers.statement-go') }}" class="card p-5 mb-4">
    @csrf
    <label class="label">ناوی کەس هەڵبژێرە</label>
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 mt-1">
        <div class="sm:col-span-3">
            <select name="supplier_id" class="input-field" required>
                <option value="">— ناوێک هەڵبژێرە —</option>
                @foreach($allSuppliers as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
            </select>
            @error('supplier_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <button type="submit" class="btn-primary">بینینی کەشف حساب</button>
    </div>
</form>

<div class="grid grid-cols-2 gap-4 mb-4">
    <div class="stat-card">
        <div class="text-xs text-slate-400">کۆی قەرزمان (دینار)</div>
        <div class="text-lg font-extrabold {{ $totals['IQD'] > 0 ? 'text-red-600' : 'text-green-700' }}">{{ $num($totals['IQD']) }} <span class="text-xs text-slate-400">د.ع</span></div>
    </div>
    <div class="stat-card">
        <div class="text-xs text-slate-400">کۆی قەرزمان (دۆلار)</div>
        <div class="text-lg font-extrabold {{ $totals['USD'] > 0 ? 'text-red-600' : 'text-green-700' }}">{{ $num($totals['USD']) }} <span class="text-xs text-slate-400">$</span></div>
    </div>
</div>

<form method="GET" action="{{ route('suppliers.statements') }}" class="card p-4 mb-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
    <div class="sm:col-span-2">
        <label class="label">گەڕان</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="ناو یان مۆبایل" class="input-field">
    </div>
    <div class="flex items-end gap-2">
        <button type="submit" class="btn-info">گەڕان</button>
        <a href="{{ route('suppliers.statements') }}" class="btn-outline">سڕینەوە</a>
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
                    <th class="px-4 py-3 font-semibold">کردارەکان</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $s)
                    <tr class="table-row">
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $s->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $s->phone ?: '—' }}</td>
                        <td class="px-4 py-3 font-semibold {{ (float)$s->balance_iqd > 0 ? 'text-red-600' : 'text-slate-400' }}">{{ $num($s->balance_iqd) }}</td>
                        <td class="px-4 py-3 font-semibold {{ (float)$s->balance_usd > 0 ? 'text-red-600' : 'text-slate-400' }}">{{ $num($s->balance_usd) }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('suppliers.show', $s) }}" class="btn-info !px-3 !py-1.5">کەشف حساب</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-slate-400">هیچ کەسێک نییە.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $suppliers->links() }}</div>
@endsection
