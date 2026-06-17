@extends('layouts.app')

@section('title', 'وەسڵی کڕین #' . $purchaseInvoice->id)
@section('page-title', 'وەسڵی کڕین #' . $purchaseInvoice->id)
@section('page-subtitle', $purchaseInvoice->supplier->name ?? '')

@section('content')
@php $num = fn($v) => number_format((float) $v, 0); @endphp

<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">وردەکاری وەسڵ</h2>
    <a href="{{ route('purchase-invoices.index') }}" class="btn-outline">گەڕانەوە</a>
</div>

<div class="card p-5 mb-4 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
    <div><div class="text-slate-400 text-xs">دابینکەر</div><div class="font-semibold text-slate-800">{{ $purchaseInvoice->supplier->name ?? '—' }}</div></div>
    <div><div class="text-slate-400 text-xs">بەروار</div><div class="font-semibold text-slate-800">{{ optional($purchaseInvoice->date)->format('Y-m-d') }}</div></div>
    <div><div class="text-slate-400 text-xs">تۆمارکار</div><div class="font-semibold text-slate-800">{{ $purchaseInvoice->user->name ?? '—' }}</div></div>
    <div><div class="text-slate-400 text-xs">ماوە</div><div class="font-semibold {{ (float)$purchaseInvoice->remaining_amount > 0 ? 'text-red-600' : 'text-green-700' }}">{{ $num($purchaseInvoice->remaining_amount) }}</div></div>
</div>

<div class="card p-0 mb-4">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">مەواد / جۆر</th>
                    <th class="px-4 py-3 font-semibold">پڕۆژە</th>
                    <th class="px-4 py-3 font-semibold">بڕ</th>
                    <th class="px-4 py-3 font-semibold">نرخی یەکە</th>
                    <th class="px-4 py-3 font-semibold">کۆی هێڵ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseInvoice->details as $d)
                    <tr class="table-row">
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $d->material->name ?? $d->custom_type }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $d->project->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ rtrim(rtrim(number_format((float)$d->quantity,3),'0'),'.') }} {{ $d->unit }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $num($d->unit_price) }}</td>
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $num($d->line_total) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t border-slate-200 font-bold">
                    <td colspan="4" class="px-4 py-3 text-left">کۆی گشتی</td>
                    <td class="px-4 py-3 text-slate-900">{{ $num($purchaseInvoice->total_amount) }}</td>
                </tr>
                <tr class="text-green-700">
                    <td colspan="4" class="px-4 py-2 text-left">دراوە</td>
                    <td class="px-4 py-2">{{ $num($purchaseInvoice->paid_amount) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@if($purchaseInvoice->notes)
    <div class="card p-4 text-sm text-slate-600"><span class="text-slate-400">تێبینی: </span>{{ $purchaseInvoice->notes }}</div>
@endif
@endsection
