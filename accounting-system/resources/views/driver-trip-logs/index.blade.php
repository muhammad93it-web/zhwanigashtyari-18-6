@extends('layouts.app')

@section('title', 'تۆمارەکانی گواستنەوە')
@section('page-title', 'تۆمارەکانی گواستنەوە و شۆفێر')
@section('page-subtitle', 'لیستی کرێی گواستنەوە و پارەدانەکان')

@section('content')
@php
    $num = fn($v) => number_format((float) $v, 0);
@endphp

<div class="flex items-center justify-between mb-4 flex-wrap gap-2">
    <h2 class="text-base font-bold text-slate-800">لیستی تۆمارەکان</h2>
    <div class="flex items-center gap-2 flex-wrap">
        <a href="{{ route('drivers.statements') }}" class="btn-outline">کەشف حساب</a>
        <a href="{{ route('drivers.index') }}" class="btn-outline">شۆفێرەکان</a>
        <a href="{{ route('driver-trip-logs.create') }}" class="btn-primary">+ تۆماری گواستنەوە</a>
    </div>
</div>

<div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-4">
    <div class="stat-card">
        <div class="text-xs text-slate-400">کۆی گشتی (دینار)</div>
        <div class="text-lg font-extrabold text-slate-800">{{ $num($totals->total_iqd ?? 0) }} <span class="text-xs text-slate-400">د.ع</span></div>
    </div>
    <div class="stat-card">
        <div class="text-xs text-slate-400">دراوە (دینار)</div>
        <div class="text-lg font-extrabold text-green-700">{{ $num($totals->paid_iqd ?? 0) }} <span class="text-xs text-slate-400">د.ع</span></div>
    </div>
    <div class="stat-card">
        <div class="text-xs text-slate-400">ماوە (دینار)</div>
        <div class="text-lg font-extrabold text-red-600">{{ $num($totals->remaining_iqd ?? 0) }} <span class="text-xs text-slate-400">د.ع</span></div>
    </div>
    <div class="stat-card">
        <div class="text-xs text-slate-400">کۆی گشتی (دۆلار)</div>
        <div class="text-lg font-extrabold text-slate-800">{{ $num($totals->total_usd ?? 0) }} <span class="text-xs text-slate-400">$</span></div>
    </div>
    <div class="stat-card">
        <div class="text-xs text-slate-400">دراوە (دۆلار)</div>
        <div class="text-lg font-extrabold text-green-700">{{ $num($totals->paid_usd ?? 0) }} <span class="text-xs text-slate-400">$</span></div>
    </div>
    <div class="stat-card">
        <div class="text-xs text-slate-400">ماوە (دۆلار)</div>
        <div class="text-lg font-extrabold text-red-600">{{ $num($totals->remaining_usd ?? 0) }} <span class="text-xs text-slate-400">$</span></div>
    </div>
</div>

<form method="GET" action="{{ route('driver-trip-logs.index') }}" class="card p-4 mb-4 grid grid-cols-1 sm:grid-cols-4 gap-3">
    <div>
        <label class="label">شۆفێر</label>
        <select name="driver_id" class="input-field">
            <option value="">— هەموو —</option>
            @foreach($drivers as $d)
                <option value="{{ $d->id }}" @selected(request('driver_id')==$d->id)>{{ $d->name }}</option>
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
        <a href="{{ route('driver-trip-logs.index') }}" class="btn-outline">سڕینەوە</a>
    </div>
</form>

<div class="card p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">#</th>
                    <th class="px-4 py-3 font-semibold">بەروار</th>
                    <th class="px-4 py-3 font-semibold">شۆفێر</th>
                    <th class="px-4 py-3 font-semibold">پڕۆژە</th>
                    <th class="px-4 py-3 font-semibold">کۆی گشتی</th>
                    <th class="px-4 py-3 font-semibold">دراوە</th>
                    <th class="px-4 py-3 font-semibold">ماوە</th>
                    <th class="px-4 py-3 font-semibold">کردارەکان</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr class="table-row">
                        <td class="px-4 py-3 text-slate-500">#{{ $log->id }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ optional($log->date)->format('Y-m-d') }}</td>
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $log->driver->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $log->project->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-700">
                            @if((float)$log->grand_total_iqd){{ $num($log->grand_total_iqd) }} <span class="text-[10px] text-slate-400">د.ع</span><br>@endif
                            @if((float)$log->grand_total_usd){{ $num($log->grand_total_usd) }} <span class="text-[10px] text-slate-400">$</span>@endif
                        </td>
                        <td class="px-4 py-3 text-green-700">
                            @if((float)$log->paid_iqd){{ $num($log->paid_iqd) }} <span class="text-[10px] text-slate-400">د.ع</span><br>@endif
                            @if((float)$log->paid_usd){{ $num($log->paid_usd) }} <span class="text-[10px] text-slate-400">$</span>@endif
                        </td>
                        <td class="px-4 py-3 font-semibold {{ ((float)$log->remaining_iqd > 0 || (float)$log->remaining_usd > 0) ? 'text-red-600' : 'text-slate-400' }}">
                            @if((float)$log->remaining_iqd){{ $num($log->remaining_iqd) }} <span class="text-[10px] text-slate-400">د.ع</span><br>@endif
                            @if((float)$log->remaining_usd){{ $num($log->remaining_usd) }} <span class="text-[10px] text-slate-400">$</span>@endif
                            @if(!(float)$log->remaining_iqd && !(float)$log->remaining_usd)٠@endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <a href="{{ route('driver-trip-logs.show', $log) }}" class="btn-info !px-3 !py-1.5">بینین</a>
                                <a href="{{ route('driver-trip-logs.edit', $log) }}" class="btn-warning !px-3 !py-1.5">دەستکاری</a>
                                <form method="POST" action="{{ route('driver-trip-logs.destroy', $log) }}" onsubmit="return confirm('دڵنیایت؟ باڵانس و خەرجی ڕاستدەکرێنەوە.')">
                                    @csrf
                                    @method('DELETE')
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

<div class="mt-4">{{ $logs->links() }}</div>
@endsection
