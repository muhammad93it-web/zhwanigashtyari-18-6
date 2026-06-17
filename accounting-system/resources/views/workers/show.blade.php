@extends('layouts.app')

@section('title', 'کرێکار: ' . $worker->name)
@section('page-title', $worker->name)
@section('page-subtitle', $worker->role ?? 'کرێکار')

@section('content')
@php $num = fn($v) => number_format((float) $v, 0); @endphp

<div class="flex items-center justify-between mb-4 flex-wrap gap-2">
    <h2 class="text-base font-bold text-slate-800">زانیاری کرێکار</h2>
    <div class="flex items-center gap-2">
        <a href="{{ route('labor-payments.create', ['worker_id' => $worker->id]) }}" class="btn-primary !px-3 !py-1.5">+ کرێی کار</a>
        <a href="{{ route('workers.edit', $worker) }}" class="btn-warning !px-3 !py-1.5">دەستکاری</a>
        <a href="{{ route('workers.index') }}" class="btn-outline !px-3 !py-1.5">گەڕانەوە</a>
    </div>
</div>

<div class="card p-5 mb-4 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
    <div><div class="text-slate-400 text-xs">پیشە</div><div class="font-semibold text-slate-800">{{ $worker->role ?? '—' }}</div></div>
    <div><div class="text-slate-400 text-xs">مۆبایل</div><div class="font-semibold text-slate-800">{{ $worker->phone ?? '—' }}</div></div>
    <div><div class="text-slate-400 text-xs">کرێی کاتژمێر</div><div class="font-semibold text-slate-800">{{ $worker->default_hourly_rate ? $num($worker->default_hourly_rate) . ' ' . ($worker->default_currency === 'USD' ? '$' : 'د.ع') : '—' }}</div></div>
    <div><div class="text-slate-400 text-xs">دۆخ</div><div>@if($worker->is_active)<span class="badge-green">چالاک</span>@else<span class="badge-slate">ناچالاک</span>@endif</div></div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
    <div class="stat-card"><div class="text-xs text-slate-400">کۆی کرێی دراو (د.ع)</div><div class="text-lg font-extrabold text-slate-800">{{ $num($totals->iqd ?? 0) }}</div></div>
    <div class="stat-card"><div class="text-xs text-slate-400">کۆی کرێی دراو ($)</div><div class="text-lg font-extrabold text-slate-800">{{ $num($totals->usd ?? 0) }}</div></div>
</div>

<div class="card p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">بەروار</th>
                    <th class="px-4 py-3 font-semibold">پڕۆژە</th>
                    <th class="px-4 py-3 font-semibold">جۆر</th>
                    <th class="px-4 py-3 font-semibold">کاتژمێر × کرێ</th>
                    <th class="px-4 py-3 font-semibold">بڕ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $p)
                    <tr class="table-row">
                        <td class="px-4 py-3 text-slate-600">{{ optional($p->date)->format('Y-m-d') }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $p->project->name ?? '—' }}</td>
                        <td class="px-4 py-3">@if($p->is_hourly)<span class="badge-cyan">کاتژمێری</span>@else<span class="badge-slate">جێگیر</span>@endif</td>
                        <td class="px-4 py-3 text-slate-600">{{ $p->is_hourly ? rtrim(rtrim(number_format((float)$p->hours,2),'0'),'.') . ' × ' . $num($p->hourly_rate) : '—' }}</td>
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $num($p->amount) }} {{ $p->currency === 'USD' ? '$' : 'د.ع' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-slate-400">هیچ پارەدانێک نییە.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $payments->links() }}</div>
@endsection
