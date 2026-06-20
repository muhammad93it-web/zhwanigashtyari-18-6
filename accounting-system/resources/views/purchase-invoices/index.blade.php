@extends('layouts.app')

@section('title', 'مێژووی کڕینەکان')
@section('page-title', 'مێژووی کڕینەکان')
@section('page-subtitle', 'هەموو وەسڵەکانی کڕین')

@section('content')
@php $num = fn($v) => number_format((float) $v, 0); @endphp

<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">وەسڵەکانی کڕین</h2>
    <a href="{{ route('purchase-invoices.create') }}" class="btn-primary">+ کڕینی نوێ</a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
    <div class="stat-card">
        <div class="text-xs text-slate-400">کۆی کڕین</div>
        <div class="text-lg font-extrabold text-slate-800">{{ $num($totals->total_iqd ?? 0) }} <span class="text-xs text-slate-400">د.ع</span></div>
        <div class="text-sm font-bold text-slate-700">{{ $num($totals->total_usd ?? 0) }} <span class="text-xs text-slate-400">$</span></div>
    </div>
    <div class="stat-card">
        <div class="text-xs text-slate-400">دراوە</div>
        <div class="text-lg font-extrabold text-green-700">{{ $num($totals->paid_iqd ?? 0) }} <span class="text-xs text-slate-400">د.ع</span></div>
        <div class="text-sm font-bold text-green-700">{{ $num($totals->paid_usd ?? 0) }} <span class="text-xs text-slate-400">$</span></div>
    </div>
    <div class="stat-card">
        <div class="text-xs text-slate-400">ماوە (قەرز)</div>
        <div class="text-lg font-extrabold text-red-600">{{ $num($totals->remaining_iqd ?? 0) }} <span class="text-xs text-slate-400">د.ع</span></div>
        <div class="text-sm font-bold text-red-600">{{ $num($totals->remaining_usd ?? 0) }} <span class="text-xs text-slate-400">$</span></div>
    </div>
</div>

<form method="GET" action="{{ route('purchase-invoices.index') }}" class="card p-4 mb-4 grid grid-cols-1 sm:grid-cols-5 gap-3">
    <div>
        <label class="label">گەڕان (ژمارەی وەسڵی هاتوو / گەیەنەر)</label>
        <input type="text" name="search" value="{{ request('search') }}" class="input-field" placeholder="ژمارەی وەسڵی هاتوو...">
    </div>
    <div>
        <label class="label">دابینکەر</label>
        <select name="supplier_id" class="input-field">
            <option value="">هەموو</option>
            @foreach($suppliers as $s)
                <option value="{{ $s->id }}" @selected(request('supplier_id')==$s->id)>{{ $s->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="label">لە بەرواری</label>
        <input type="date" name="from_date" value="{{ request('from_date') }}" class="input-field">
    </div>
    <div>
        <label class="label">تا بەرواری</label>
        <input type="date" name="to_date" value="{{ request('to_date') }}" class="input-field">
    </div>
    <div class="flex items-end gap-2">
        <button type="submit" class="btn-info">گەڕان</button>
        <a href="{{ route('purchase-invoices.index') }}" class="btn-outline">سڕینەوە</a>
    </div>
</form>

<div class="card p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">#</th>
                    <th class="px-4 py-3 font-semibold">ژمارەی وەسڵی هاتوو</th>
                    <th class="px-4 py-3 font-semibold">دابینکەر / گەیەنەر</th>
                    <th class="px-4 py-3 font-semibold">پڕۆژە</th>
                    <th class="px-4 py-3 font-semibold">بەروار</th>
                    <th class="px-4 py-3 font-semibold">کۆ (د.ع)</th>
                    <th class="px-4 py-3 font-semibold">کۆ ($)</th>
                    <th class="px-4 py-3 font-semibold">ماوە</th>
                    <th class="px-4 py-3 font-semibold">کردارەکان</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                    <tr class="table-row">
                        <td class="px-4 py-3 font-semibold text-slate-800">#{{ $inv->id }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $inv->incoming_invoice_number ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $inv->party_name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $inv->project->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ optional($inv->date)->format('Y-m-d') }}</td>
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $num($inv->total_iqd) }}</td>
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $num($inv->total_usd) }}</td>
                        <td class="px-4 py-3 text-xs">
                            <span class="{{ (float)$inv->remaining_iqd > 0 ? 'text-red-600 font-semibold' : 'text-slate-400' }}">{{ $num($inv->remaining_iqd) }} د.ع</span><br>
                            <span class="{{ (float)$inv->remaining_usd > 0 ? 'text-red-600 font-semibold' : 'text-slate-400' }}">{{ $num($inv->remaining_usd) }} $</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <a href="{{ route('purchase-invoices.show', $inv) }}" class="btn-info !px-3 !py-1.5">بینین</a>
                                <a href="{{ route('purchase-invoices.edit', $inv) }}" class="btn-warning !px-3 !py-1.5">دەستکاری</a>
                                <a href="{{ route('purchase-invoices.print', $inv) }}" target="_blank" class="btn-outline !px-3 !py-1.5">چاپ</a>
                                <a href="{{ route('purchase-invoices.export-excel', $inv) }}" class="btn-primary !px-3 !py-1.5">Excel</a>
                                <a href="{{ route('purchase-invoices.export-word', $inv) }}" class="btn-primary !px-3 !py-1.5">Word</a>
                                <form method="POST" action="{{ route('purchase-invoices.destroy', $inv) }}" onsubmit="return confirm('سڕینەوەی وەسڵ کۆگا و باڵانس ڕاستدەکاتەوە. دڵنیایت؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger !px-3 !py-1.5">سڕینەوە</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="px-4 py-10 text-center text-slate-400">هیچ کڕینێک نییە.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $invoices->links() }}</div>
@endsection
