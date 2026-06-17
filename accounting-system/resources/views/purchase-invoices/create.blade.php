@extends('layouts.app')

@section('title', 'کڕینی نوێ')
@section('page-title', 'کڕینی مەواد بە وەسڵ')
@section('page-subtitle', 'تۆمارکردنی وەسڵی کڕین لە دابینکەر')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">وەسڵی کڕینی نوێ</h2>
    <a href="{{ route('purchase-invoices.index') }}" class="btn-outline">گەڕانەوە</a>
</div>

@if($errors->any())
    <div class="card p-4 mb-4 bg-red-50 border-red-200 text-red-600 text-sm">
        <ul class="list-disc pe-5 space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('purchase-invoices.store') }}" id="purchaseForm">
    @csrf

    {{-- Header --}}
    <div class="card p-5 mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="label">دابینکەر *</label>
            <select name="supplier_id" class="input-field" required>
                <option value="">— هەڵبژێرە —</option>
                @foreach($suppliers as $s)
                    <option value="{{ $s->id }}" @selected(old('supplier_id')==$s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label">بەروار *</label>
            <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" class="input-field" required>
        </div>
    </div>

    {{-- Lines --}}
    <div class="card p-0 mb-4">
        <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
            <span class="font-bold text-sm text-slate-700">هێڵەکانی کڕین</span>
            <button type="button" id="addLine" class="btn-info !px-3 !py-1.5">+ زیادکردنی هێڵ</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-right text-xs text-slate-500 border-b border-slate-200 bg-slate-50">
                        <th class="px-3 py-2 font-semibold min-w-[160px]">مەواد (کۆگا)</th>
                        <th class="px-3 py-2 font-semibold min-w-[140px]">یان جۆری دەستی</th>
                        <th class="px-3 py-2 font-semibold">یەکە</th>
                        <th class="px-3 py-2 font-semibold">بڕ</th>
                        <th class="px-3 py-2 font-semibold">نرخی یەکە</th>
                        <th class="px-3 py-2 font-semibold">کۆی هێڵ</th>
                        <th class="px-3 py-2 font-semibold min-w-[140px]">پڕۆژە</th>
                        <th class="px-3 py-2 font-semibold"></th>
                    </tr>
                </thead>
                <tbody id="linesBody"></tbody>
            </table>
        </div>
    </div>

    {{-- Totals --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
        <div class="stat-card">
            <div class="text-xs text-slate-400">کۆی گشتی</div>
            <div class="text-xl font-extrabold text-slate-800"><span id="grandTotal">0</span></div>
        </div>
        <div class="card p-4">
            <label class="label">بڕی دراو</label>
            <input type="number" step="0.01" name="paid_amount" id="paidAmount" value="{{ old('paid_amount', 0) }}" class="input-field">
        </div>
        <div class="stat-card">
            <div class="text-xs text-slate-400">ماوە (قەرز بۆ دابینکەر)</div>
            <div class="text-xl font-extrabold text-red-600"><span id="remaining">0</span></div>
        </div>
    </div>

    <div class="card p-5 mb-4">
        <label class="label">تێبینی</label>
        <textarea name="notes" rows="2" class="input-field">{{ old('notes') }}</textarea>
    </div>

    <div class="flex items-center gap-2">
        <button type="submit" class="btn-primary">تۆمارکردنی کڕین</button>
        <a href="{{ route('purchase-invoices.index') }}" class="btn-outline">پاشگەزبوونەوە</a>
    </div>
</form>

{{-- Template for a single line row --}}
<template id="lineTemplate">
    <tr class="border-b border-slate-100 line-row">
        <td class="px-3 py-2">
            <select name="lines[__I__][material_id]" class="input-field material-select" onchange="onMaterialChange(this)">
                <option value="">— جۆری دەستی —</option>
                @foreach($materials as $m)
                    <option value="{{ $m->id }}" data-unit="{{ $m->unit }}">{{ $m->name }}</option>
                @endforeach
            </select>
        </td>
        <td class="px-3 py-2">
            <input type="text" name="lines[__I__][custom_type]" class="input-field" placeholder="ناوی شت">
        </td>
        <td class="px-3 py-2">
            <input type="text" name="lines[__I__][unit]" class="input-field unit-input" placeholder="دانە" style="min-width:70px">
        </td>
        <td class="px-3 py-2">
            <input type="number" step="0.001" name="lines[__I__][quantity]" class="input-field qty" value="1" oninput="recalc()" style="min-width:80px">
        </td>
        <td class="px-3 py-2">
            <input type="number" step="0.01" name="lines[__I__][unit_price]" class="input-field price" value="0" oninput="recalc()" style="min-width:90px">
        </td>
        <td class="px-3 py-2 font-semibold text-slate-800 line-total">0</td>
        <td class="px-3 py-2">
            <select name="lines[__I__][project_id]" class="input-field">
                <option value="">— بێ پڕۆژە —</option>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </td>
        <td class="px-3 py-2">
            <button type="button" class="btn-danger !px-2.5 !py-1.5" onclick="removeLine(this)">×</button>
        </td>
    </tr>
</template>

@push('scripts')
<script>
    let lineIndex = 0;
    const tpl = document.getElementById('lineTemplate').innerHTML;
    const body = document.getElementById('linesBody');

    function addLine() {
        const html = tpl.replace(/__I__/g, lineIndex);
        lineIndex++;
        const wrap = document.createElement('tbody');
        wrap.innerHTML = html.trim();
        body.appendChild(wrap.firstChild);
        recalc();
    }

    function removeLine(btn) {
        const rows = body.querySelectorAll('.line-row');
        if (rows.length <= 1) { return; }
        btn.closest('tr').remove();
        recalc();
    }

    function onMaterialChange(sel) {
        const opt = sel.options[sel.selectedIndex];
        const unit = opt.getAttribute('data-unit');
        const row = sel.closest('tr');
        const unitInput = row.querySelector('.unit-input');
        if (sel.value && unit && !unitInput.value) {
            unitInput.value = unit;
        }
    }

    function fmt(n) { return Math.round(n).toLocaleString('en-US'); }

    function recalc() {
        let grand = 0;
        body.querySelectorAll('.line-row').forEach(function (row) {
            const qty = parseFloat(row.querySelector('.qty').value) || 0;
            const price = parseFloat(row.querySelector('.price').value) || 0;
            const total = qty * price;
            row.querySelector('.line-total').textContent = fmt(total);
            grand += total;
        });
        document.getElementById('grandTotal').textContent = fmt(grand);
        const paid = parseFloat(document.getElementById('paidAmount').value) || 0;
        document.getElementById('remaining').textContent = fmt(grand - paid);
    }

    document.getElementById('addLine').addEventListener('click', addLine);
    document.getElementById('paidAmount').addEventListener('input', recalc);

    // Start with one empty line.
    addLine();
</script>
@endpush
@endsection
