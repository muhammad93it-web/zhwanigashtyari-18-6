@extends('layouts.app')

@section('title', 'ڕاپۆرتەکان')
@section('page-title', 'ڕاپۆرتەکان')
@section('page-subtitle', 'ڕاپۆرتی پێشکەوتوو بە فلتەری زیرەک')

@push('head')
<style>
@media print {
    .no-print { display: none !important; }
    .card { box-shadow: none !important; border: 1px solid #ddd !important; }
    header, nav { display: none !important; }
    body { background: white !important; }
    .print-title { display: block !important; }
}
.print-title { display: none; }
</style>
@endpush

@section('content')
@php
    $iqd = fn($v) => number_format((float) $v, 0);
    $usd = fn($v) => '$' . number_format((float) $v, 2);
    $cur = fn($c, $a) => $c === 'USD' ? '$' . number_format((float) $a, 2) : number_format((float) $a, 0) . ' د';

    $sectionLabels = [
        'incomes'            => 'وەرگرتنی پارە',
        'expenses'           => 'خەرجکردنی پارە',
        'material_purchases' => 'کڕینی مەواد',
        'material_sales'     => 'فرۆشتنی مەواد',
        'purchase_invoices'  => 'کڕینی بە وەسڵ',
        'contractor_payments'=> 'پارەدانی وەستا',
        'labor_payments'     => 'کرێی کارەکان',
        'transactions'       => 'مامەڵە گشتییەکان',
    ];

    $sectionIcons = [
        'incomes'            => '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v3.586L7.707 8.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 9.586V6z" clip-rule="evenodd"/>',
        'expenses'           => '<path fill-rule="evenodd" d="M10 2a8 8 0 100 16A8 8 0 0010 2zM9 14V10.414L7.707 11.707a1 1 0 01-1.414-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 10.414V14a1 1 0 11-2 0z" clip-rule="evenodd"/>',
        'material_purchases' => '<path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/><path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>',
        'material_sales'     => '<path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/><path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>',
        'purchase_invoices'  => '<path fill-rule="evenodd" d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm2.5 3a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm6.207.293a1 1 0 00-1.414 0l-6 6a1 1 0 101.414 1.414l6-6a1 1 0 000-1.414zM12.5 10a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" clip-rule="evenodd"/>',
        'contractor_payments'=> '<path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>',
        'labor_payments'     => '<path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4z"/><path d="M14 6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h8zm-4 7a3 3 0 100-6 3 3 0 000 6z"/>',
        'transactions'       => '<path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>',
    ];

    $sectionColors = [
        'incomes'            => ['from-green-500','to-emerald-600','text-green-700','bg-green-50'],
        'expenses'           => ['from-red-500','to-red-600','text-red-700','bg-red-50'],
        'material_purchases' => ['from-orange-500','to-amber-600','text-amber-700','bg-amber-50'],
        'material_sales'     => ['from-teal-500','to-teal-600','text-teal-700','bg-teal-50'],
        'purchase_invoices'  => ['from-blue-500','to-blue-600','text-blue-700','bg-blue-50'],
        'contractor_payments'=> ['from-purple-500','to-purple-600','text-purple-700','bg-purple-50'],
        'labor_payments'     => ['from-lime-500','to-lime-600','text-lime-700','bg-lime-50'],
        'transactions'       => ['from-slate-500','to-slate-600','text-slate-700','bg-slate-50'],
    ];
@endphp

{{-- Print-only title --}}
<div class="print-title text-center mb-4">
    <h1 class="text-2xl font-extrabold">{{ $sectionLabels[$section] ?? 'ڕاپۆرت' }}</h1>
    <p class="text-sm text-gray-500">{{ $fromDate }} — {{ $toDate }} | ژوانی گەشتیاری</p>
</div>

{{-- ===== SECTION PICKER ===== --}}
<div class="card p-3 mb-4 overflow-x-auto no-print">
    <div class="flex gap-1.5 min-w-max">
        @foreach($sectionLabels as $key => $label)
        @php
            $colors = $sectionColors[$key];
            $isActive = ($section === $key);
        @endphp
        <a href="{{ route('reports.advanced', ['section' => $key, 'from_date' => $fromDate, 'to_date' => $toDate]) }}"
           class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold transition-all duration-150 whitespace-nowrap
                  {{ $isActive
                      ? 'bg-gradient-to-br ' . $colors[0] . ' ' . $colors[1] . ' text-white shadow-md scale-105'
                      : 'bg-white border border-slate-200 text-slate-600 hover:border-slate-300 hover:bg-slate-50' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                {!! $sectionIcons[$key] !!}
            </svg>
            {{ $label }}
            @if($isActive && $totals['count'] > 0)
            <span class="bg-white/30 text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold">{{ $totals['count'] }}</span>
            @endif
        </a>
        @endforeach
    </div>
</div>

{{-- ===== FILTER FORM ===== --}}
<div class="card p-4 mb-4 no-print">
    <form method="GET" action="{{ route('reports.advanced') }}" id="report-form">
        <input type="hidden" name="section" value="{{ $section }}">

        <div class="flex flex-wrap items-end gap-3">
            {{-- Date range --}}
            <div class="flex-1 min-w-[150px]">
                <label class="label" for="from_date">لە بەرواری</label>
                <input type="date" id="from_date" name="from_date" value="{{ $fromDate }}" class="input-field">
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="label" for="to_date">تا بەرواری</label>
                <input type="date" id="to_date" name="to_date" value="{{ $toDate }}" class="input-field">
            </div>

            {{-- Search --}}
            @if(in_array($section, ['incomes','expenses','contractor_payments','transactions','material_purchases','material_sales','labor_payments']))
            <div class="flex-1 min-w-[180px]">
                <label class="label" for="search">گەڕان</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="ناو، جۆر، وەسف..." class="input-field">
            </div>
            @endif

            {{-- Project filter --}}
            @if(in_array($section, ['expenses','purchase_invoices','labor_payments']))
            <div class="flex-1 min-w-[150px]">
                <label class="label" for="project_id">پڕۆژە</label>
                <select id="project_id" name="project_id" class="input-field">
                    <option value="">— هەموو پڕۆژەکان —</option>
                    @foreach($filterOptions['projects'] as $p)
                    <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Supplier filter --}}
            @if($section === 'purchase_invoices')
            <div class="flex-1 min-w-[150px]">
                <label class="label" for="supplier_id">دابینکەر</label>
                <select id="supplier_id" name="supplier_id" class="input-field">
                    <option value="">— هەموو دابینکەران —</option>
                    @foreach($filterOptions['suppliers'] as $s)
                    <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Contractor filter --}}
            @if($section === 'contractor_payments')
            <div class="flex-1 min-w-[150px]">
                <label class="label" for="contractor_id">وەستا</label>
                <select id="contractor_id" name="contractor_id" class="input-field">
                    <option value="">— هەموو وەستاکان —</option>
                    @foreach($filterOptions['contractors'] as $c)
                    <option value="{{ $c->id }}" {{ request('contractor_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Worker filter --}}
            @if($section === 'labor_payments')
            <div class="flex-1 min-w-[150px]">
                <label class="label" for="worker_id">کرێکار</label>
                <select id="worker_id" name="worker_id" class="input-field">
                    <option value="">— هەموو کرێکاران —</option>
                    @foreach($filterOptions['workers'] as $w)
                    <option value="{{ $w->id }}" {{ request('worker_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Material filter --}}
            @if(in_array($section, ['material_purchases','material_sales']))
            <div class="flex-1 min-w-[150px]">
                <label class="label" for="material_id">مەواد</label>
                <select id="material_id" name="material_id" class="input-field">
                    <option value="">— هەموو مەوادەکان —</option>
                    @foreach($filterOptions['materials'] as $m)
                    <option value="{{ $m->id }}" {{ request('material_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Client filter --}}
            @if($section === 'transactions')
            <div class="flex-1 min-w-[150px]">
                <label class="label" for="client_id">کڕیار</label>
                <select id="client_id" name="client_id" class="input-field">
                    <option value="">— هەموو کڕیاران —</option>
                    @foreach($filterOptions['clients'] as $cl)
                    <option value="{{ $cl->id }}" {{ request('client_id') == $cl->id ? 'selected' : '' }}>{{ $cl->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="label" for="type">جۆری مامەڵە</label>
                <select id="type" name="type" class="input-field">
                    <option value="">— هەموو جۆرەکان —</option>
                    <option value="sale" {{ request('type') === 'sale' ? 'selected' : '' }}>فرۆشتن</option>
                    <option value="purchase" {{ request('type') === 'purchase' ? 'selected' : '' }}>کڕین</option>
                    <option value="debit" {{ request('type') === 'debit' ? 'selected' : '' }}>دەبیت</option>
                    <option value="credit" {{ request('type') === 'credit' ? 'selected' : '' }}>کریدیت</option>
                </select>
            </div>
            @endif

            <div class="flex gap-2 flex-wrap">
                <button type="submit" class="btn-primary gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/></svg>
                    پیشاندان
                </button>
                <a href="{{ route('reports.advanced', ['section' => $section]) }}" class="btn-outline gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    پاکیکردنەوە
                </a>
            </div>
        </div>
    </form>
</div>

{{-- ===== SUMMARY STATS ===== --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
    @php $colors = $sectionColors[$section]; @endphp
    <div class="stat-card">
        <span class="text-xs text-slate-500">ژمارەی تۆمار</span>
        <div class="text-2xl font-extrabold text-slate-800">{{ $totals['count'] }}</div>
    </div>
    @if($section !== 'labor_payments')
    <div class="stat-card">
        <span class="text-xs text-slate-500">کۆی دینار</span>
        <div class="text-xl font-extrabold {{ $colors[2] }}">{{ $iqd($totals['iqd']) }}</div>
        <div class="text-[11px] text-slate-400">دینار</div>
    </div>
    <div class="stat-card">
        <span class="text-xs text-slate-500">کۆی دۆلار</span>
        <div class="text-xl font-extrabold {{ $colors[2] }}">{{ $usd($totals['usd']) }}</div>
    </div>
    @endif
    <div class="stat-card col-span-1">
        <span class="text-xs text-slate-500">بەش</span>
        <div class="text-sm font-bold text-slate-800">{{ $sectionLabels[$section] ?? $section }}</div>
        <div class="text-[11px] text-slate-400">{{ $fromDate }} ← {{ $toDate }}</div>
    </div>
</div>

{{-- ===== EXPORT BUTTONS ===== --}}
@if($results->isNotEmpty())
<div class="flex flex-wrap gap-2 mb-4 no-print">
    <button type="button" onclick="window.print()"
            class="btn bg-slate-600 hover:bg-slate-700 text-white gap-2">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a1 1 0 001 1h8a1 1 0 001-1v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a1 1 0 00-1-1H6a1 1 0 00-1 1zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"/></svg>
        چاپکردن
    </button>
    <a href="{{ route('reports.advanced.excel', request()->all()) }}"
       class="btn bg-emerald-600 hover:bg-emerald-700 text-white gap-2">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
        داگرتن Excel
    </a>
    <a href="{{ route('reports.advanced.word', request()->all()) }}"
       class="btn bg-blue-600 hover:bg-blue-700 text-white gap-2">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
        داگرتن Word
    </a>
</div>
@endif

{{-- ===== RESULTS TABLE ===== --}}
<div class="card">
    @if($results->isEmpty())
    <div class="p-12 text-center">
        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
            <svg class="w-8 h-8 text-slate-300" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/>
            </svg>
        </div>
        <p class="text-slate-400 font-medium text-sm">هیچ تۆمارێک نەدۆزرایەوە</p>
        <p class="text-slate-400 text-xs mt-1">بەرواری دیکە هەڵبژێرە یان فلتەرەکان بگۆڕە</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs font-semibold text-slate-500 border-b border-slate-200 bg-slate-50">
                    {{-- Headers per section --}}
                    @if($section === 'incomes')
                    <th class="px-4 py-3">بەروار</th><th class="px-4 py-3">سەرچاوە</th><th class="px-4 py-3">جۆر</th>
                    <th class="px-4 py-3">دراو</th><th class="px-4 py-3">بڕ</th><th class="px-4 py-3">بەدینار</th>
                    <th class="px-4 py-3">تێبینی</th>

                    @elseif($section === 'expenses')
                    <th class="px-4 py-3">بەروار</th><th class="px-4 py-3">وەرگر</th><th class="px-4 py-3">جۆر</th>
                    <th class="px-4 py-3">پڕۆژە</th><th class="px-4 py-3">دراو</th><th class="px-4 py-3">بڕ</th>
                    <th class="px-4 py-3">بەدینار</th>

                    @elseif($section === 'material_purchases')
                    <th class="px-4 py-3">بەروار</th><th class="px-4 py-3">مەواد</th><th class="px-4 py-3">دابینکەر</th>
                    <th class="px-4 py-3">بڕ</th><th class="px-4 py-3">نرخی یەکە</th>
                    <th class="px-4 py-3">دراو</th><th class="px-4 py-3">کۆ</th><th class="px-4 py-3">بەدینار</th>

                    @elseif($section === 'material_sales')
                    <th class="px-4 py-3">بەروار</th><th class="px-4 py-3">مەواد</th><th class="px-4 py-3">کڕیار</th>
                    <th class="px-4 py-3">بڕ</th><th class="px-4 py-3">نرخی یەکە</th>
                    <th class="px-4 py-3">دراو</th><th class="px-4 py-3">کۆ</th><th class="px-4 py-3">بەدینار</th>

                    @elseif($section === 'purchase_invoices')
                    <th class="px-4 py-3">#</th><th class="px-4 py-3">بەروار</th><th class="px-4 py-3">دابینکەر</th>
                    <th class="px-4 py-3">پڕۆژە</th><th class="px-4 py-3">کۆی گشتی</th>
                    <th class="px-4 py-3">پارەدراو</th><th class="px-4 py-3">ماوە</th><th class="px-4 py-3">دۆخ</th>

                    @elseif($section === 'contractor_payments')
                    <th class="px-4 py-3">بەروار</th><th class="px-4 py-3">وەستا</th><th class="px-4 py-3">جۆری کار</th>
                    <th class="px-4 py-3">مەتر</th><th class="px-4 py-3">دراو</th>
                    <th class="px-4 py-3">بڕ</th><th class="px-4 py-3">بەدینار</th>

                    @elseif($section === 'labor_payments')
                    <th class="px-4 py-3">بەروار</th><th class="px-4 py-3">کرێکار</th><th class="px-4 py-3">ڕۆڵ</th>
                    <th class="px-4 py-3">پڕۆژە</th><th class="px-4 py-3">کاتژمێر</th>
                    <th class="px-4 py-3">بڕ</th><th class="px-4 py-3">دراو</th>

                    @elseif($section === 'transactions')
                    <th class="px-4 py-3">ژمارە</th><th class="px-4 py-3">بەروار</th><th class="px-4 py-3">کڕیار</th>
                    <th class="px-4 py-3">جۆر</th><th class="px-4 py-3">دراو</th>
                    <th class="px-4 py-3">بڕ</th><th class="px-4 py-3">بەدینار</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($results as $row)
                <tr class="table-row text-right">

                    @if($section === 'incomes')
                    <td class="px-4 py-3 text-slate-500 text-xs">{{ $row->income_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 font-medium text-slate-800">{{ $row->source }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $row->category ?? '—' }}</td>
                    <td class="px-4 py-3"><span class="badge-slate">{{ $row->currency }}</span></td>
                    <td class="px-4 py-3 font-semibold text-green-600">{{ $cur($row->currency, $row->amount) }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $iqd($row->amount_iqd) }} د</td>
                    <td class="px-4 py-3 text-slate-400 text-xs max-w-[120px] truncate">{{ $row->notes ?? '—' }}</td>

                    @elseif($section === 'expenses')
                    <td class="px-4 py-3 text-slate-500 text-xs">{{ $row->expense_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 font-medium text-slate-800">{{ $row->payee }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $row->category ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $row->project?->name ?? '—' }}</td>
                    <td class="px-4 py-3"><span class="badge-slate">{{ $row->currency }}</span></td>
                    <td class="px-4 py-3 font-semibold text-red-500">{{ $cur($row->currency, $row->amount) }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $iqd($row->amount_iqd) }} د</td>

                    @elseif($section === 'material_purchases')
                    <td class="px-4 py-3 text-slate-500 text-xs">{{ $row->movement_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 font-medium text-slate-800">{{ $row->material?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $row->party_name ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ number_format($row->quantity, 2) }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $cur($row->currency, $row->unit_price) }}</td>
                    <td class="px-4 py-3"><span class="badge-slate">{{ $row->currency }}</span></td>
                    <td class="px-4 py-3 font-semibold text-amber-600">{{ $cur($row->currency, $row->amount) }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $iqd($row->amount_iqd) }} د</td>

                    @elseif($section === 'material_sales')
                    <td class="px-4 py-3 text-slate-500 text-xs">{{ $row->movement_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 font-medium text-slate-800">{{ $row->material?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $row->party_name ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ number_format($row->quantity, 2) }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $cur($row->currency, $row->unit_price) }}</td>
                    <td class="px-4 py-3"><span class="badge-slate">{{ $row->currency }}</span></td>
                    <td class="px-4 py-3 font-semibold text-teal-600">{{ $cur($row->currency, $row->amount) }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $iqd($row->amount_iqd) }} د</td>

                    @elseif($section === 'purchase_invoices')
                    <td class="px-4 py-3 text-slate-400 text-xs">#{{ $row->id }}</td>
                    <td class="px-4 py-3 text-slate-500 text-xs">{{ $row->date->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 font-medium text-slate-800">{{ $row->party_name }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $row->project?->name ?? '—' }}</td>
                    <td class="px-4 py-3 font-semibold text-slate-800">{{ $iqd($row->total_iqd) }} د</td>
                    <td class="px-4 py-3 text-green-600 font-semibold">{{ $iqd($row->paid_iqd) }} د</td>
                    <td class="px-4 py-3 {{ $row->remaining_iqd > 0 ? 'text-red-500 font-semibold' : 'text-slate-400' }}">{{ $iqd($row->remaining_iqd) }} د</td>
                    <td class="px-4 py-3">
                        @if($row->remaining_iqd <= 0)
                            <span class="badge-green">تەواو</span>
                        @else
                            <span class="badge-amber">چاوەڕوان</span>
                        @endif
                    </td>

                    @elseif($section === 'contractor_payments')
                    <td class="px-4 py-3 text-slate-500 text-xs">{{ $row->payment_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 font-medium text-slate-800">{{ $row->contractor?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-500 text-xs">{{ $row->contractor?->work_type_name ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $row->meters ? number_format($row->meters, 2) : '—' }}</td>
                    <td class="px-4 py-3"><span class="badge-slate">{{ $row->currency }}</span></td>
                    <td class="px-4 py-3 font-semibold text-purple-600">{{ $cur($row->currency, $row->amount) }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $iqd($row->amount_iqd) }} د</td>

                    @elseif($section === 'labor_payments')
                    <td class="px-4 py-3 text-slate-500 text-xs">{{ $row->date->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 font-medium text-slate-800">{{ $row->worker?->name ?? $row->worker_name ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $row->role ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $row->project?->name ?? '—' }}</td>
                    @php $lmode = $row->payment_mode ?? ($row->is_hourly ? 'hourly' : 'fixed'); @endphp
                    <td class="px-4 py-3 text-slate-600">
                        @if($lmode === 'hourly'){{ number_format((float)$row->hours, 1) }} کتژ
                        @elseif($lmode === 'daily'){{ number_format((float)$row->days, 1) }} ڕۆژ
                        @else —@endif
                    </td>
                    <td class="px-4 py-3 font-semibold text-lime-700">{{ number_format($row->amount, 0) }}</td>
                    <td class="px-4 py-3"><span class="badge-slate">{{ $row->currency }}</span></td>

                    @elseif($section === 'transactions')
                    <td class="px-4 py-3 text-slate-400 text-xs truncate max-w-[80px]">{{ $row->reference_number }}</td>
                    <td class="px-4 py-3 text-slate-500 text-xs">{{ $row->transaction_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 font-medium text-slate-800">{{ $row->client?->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @php $typeClasses = ['sale'=>'badge-green','purchase'=>'badge-red','debit'=>'badge-amber','credit'=>'badge-cyan']; @endphp
                        <span class="{{ $typeClasses[$row->type] ?? 'badge-slate' }}">{{ \App\Models\Transaction::TYPES[$row->type] ?? $row->type }}</span>
                    </td>
                    <td class="px-4 py-3"><span class="badge-slate">{{ $row->currency }}</span></td>
                    <td class="px-4 py-3 font-semibold text-slate-800">{{ $cur($row->currency, $row->amount) }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $iqd($row->amount_iqd) }} د</td>
                    @endif

                </tr>
                @endforeach
            </tbody>

            {{-- Footer totals --}}
            @if($section !== 'labor_payments')
            <tfoot class="border-t-2 border-slate-200">
                <tr class="bg-slate-50 font-bold">
                    @php
                        $colCount = [
                            'incomes'=>7,'expenses'=>7,'material_purchases'=>8,'material_sales'=>8,
                            'purchase_invoices'=>8,'contractor_payments'=>7,'transactions'=>7,
                        ][$section] ?? 7;
                    @endphp
                    <td colspan="{{ $colCount - 3 }}" class="px-4 py-3 text-slate-600 text-sm">
                        کۆی گشتی ({{ $totals['count'] }} تۆمار)
                    </td>
                    <td class="px-4 py-3 text-green-700">{{ $iqd($totals['iqd']) }} د</td>
                    <td class="px-4 py-3 text-green-700">{{ $usd($totals['usd']) }}</td>
                    @if($section === 'purchase_invoices')<td></td>@endif
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    @if($results instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $results->hasPages())
    <div class="flex items-center justify-between gap-3 px-4 py-3 border-t border-slate-100 no-print">
        <div class="text-xs text-slate-500">پیشاندانی {{ $results->firstItem() }}–{{ $results->lastItem() }} لە کۆی {{ $results->total() }}</div>
        <div class="flex items-center gap-1">
            @if($results->onFirstPage())
                <span class="px-3 py-1.5 rounded-lg text-xs text-slate-300 border border-slate-100 cursor-not-allowed">پێشوو</span>
            @else
                <a href="{{ $results->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg text-xs text-slate-600 border border-slate-200 hover:bg-slate-50">پێشوو</a>
            @endif
            <span class="px-3 py-1.5 text-xs text-slate-500">لاپەڕە {{ $results->currentPage() }} / {{ $results->lastPage() }}</span>
            @if($results->hasMorePages())
                <a href="{{ $results->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg text-xs text-slate-600 border border-slate-200 hover:bg-slate-50">دواتر</a>
            @else
                <span class="px-3 py-1.5 rounded-lg text-xs text-slate-300 border border-slate-100 cursor-not-allowed">دواتر</span>
            @endif
        </div>
    </div>
    @endif
    @endif
</div>

@endsection
