@extends('layouts.app')

@section('title', 'کرێی کار و کرێکاران')
@section('page-title', 'کرێی کار و کرێکاران')
@section('page-subtitle', 'تۆماری کرێی کرێکارەکان')

@section('content')
@php $num = fn($v) => number_format((float) $v, 0); @endphp

<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">تۆماری کرێی کار</h2>
    <div class="flex items-center gap-2">
        <a href="{{ route('workers.index') }}" class="btn-outline">کرێکاران</a>
        <a href="{{ route('labor-payments.create') }}" class="btn-primary">+ کرێی کاری نوێ</a>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
    <div class="stat-card"><div class="text-xs text-slate-400">کۆی تۆمارەکان</div><div class="text-lg font-extrabold text-slate-800">{{ $num($totals->c ?? 0) }}</div></div>
    <div class="stat-card"><div class="text-xs text-slate-400">کۆی کرێ (د.ع)</div><div class="text-lg font-extrabold text-slate-800">{{ $num($totals->iqd ?? 0) }}</div></div>
    <div class="stat-card"><div class="text-xs text-slate-400">کۆی کرێ ($)</div><div class="text-lg font-extrabold text-slate-800">{{ $num($totals->usd ?? 0) }}</div></div>
</div>

<form method="GET" action="{{ route('labor-payments.index') }}" class="card p-4 mb-4 grid grid-cols-1 sm:grid-cols-5 gap-3">
    <div>
        <label class="label">کرێکار</label>
        <select name="worker_id" class="input-field">
            <option value="">هەموو</option>
            @foreach($workers as $w)<option value="{{ $w->id }}" @selected(request('worker_id')==$w->id)>{{ $w->name }}</option>@endforeach
        </select>
    </div>
    <div>
        <label class="label">پڕۆژە</label>
        <select name="project_id" class="input-field">
            <option value="">هەموو</option>
            @foreach($projects as $p)<option value="{{ $p->id }}" @selected(request('project_id')==$p->id)>{{ $p->name }}</option>@endforeach
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
        <a href="{{ route('labor-payments.index') }}" class="btn-outline">سڕینەوە</a>
    </div>
</form>

<div class="card p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">بەروار</th>
                    <th class="px-4 py-3 font-semibold">کرێکار</th>
                    <th class="px-4 py-3 font-semibold">پیشە</th>
                    <th class="px-4 py-3 font-semibold">پڕۆژە</th>
                    <th class="px-4 py-3 font-semibold">جۆر</th>
                    <th class="px-4 py-3 font-semibold">کاتژمێر × کرێ</th>
                    <th class="px-4 py-3 font-semibold">بڕ</th>
                    <th class="px-4 py-3 font-semibold">کردارەکان</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $p)
                    <tr class="table-row">
                        <td class="px-4 py-3 text-slate-500">{{ optional($p->date)->format('Y-m-d') }}</td>
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $p->worker->name ?? $p->worker_name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $p->role ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $p->project->name ?? '—' }}</td>
                        <td class="px-4 py-3">@if($p->is_hourly)<span class="badge-cyan">کاتژمێری</span>@else<span class="badge-slate">جێگیر</span>@endif</td>
                        <td class="px-4 py-3 text-slate-600">{{ $p->is_hourly ? rtrim(rtrim(number_format((float)$p->hours,2),'0'),'.') . ' × ' . $num($p->hourly_rate) : '—' }}</td>
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $num($p->amount) }} {{ $p->currency === 'USD' ? '$' : 'د.ع' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('labor-payments.edit', $p) }}" class="btn-warning !px-3 !py-1.5">دەستکاری</a>
                                <form method="POST" action="{{ route('labor-payments.destroy', $p) }}" onsubmit="return confirm('دڵنیایت لە سڕینەوە؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger !px-3 !py-1.5">سڕینەوە</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-4 py-10 text-center text-slate-400">هیچ تۆمارێک نییە.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $payments->links() }}</div>
@endsection
