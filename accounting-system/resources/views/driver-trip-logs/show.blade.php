@extends('layouts.app')

@section('title', 'تۆماری گواستنەوە #' . $driverTripLog->id)
@section('page-title', 'تۆماری گواستنەوە #' . $driverTripLog->id)
@section('page-subtitle', $driverTripLog->driver->name ?? '')

@section('content')
@php
    $num = fn($v) => number_format((float) $v, 0);
    $qty = fn($v) => rtrim(rtrim(number_format((float) $v, 2), '0'), '.');
    $log = $driverTripLog;
@endphp

<div class="flex items-center justify-between mb-4 flex-wrap gap-2">
    <h2 class="text-base font-bold text-slate-800">وردەکاری تۆمار</h2>
    <div class="flex items-center gap-2 flex-wrap">
        <a href="{{ route('driver-trip-logs.print', $log) }}" target="_blank" class="btn-info !px-3 !py-1.5">چاپ (A4)</a>
        <a href="{{ route('driver-trip-logs.export-excel', $log) }}" class="btn-primary !px-3 !py-1.5">Excel</a>
        <a href="{{ route('driver-trip-logs.export-word', $log) }}" class="btn-primary !px-3 !py-1.5">Word</a>
        <a href="{{ route('driver-trip-logs.edit', $log) }}" class="btn-warning !px-3 !py-1.5">دەستکاری</a>
        <a href="{{ route('driver-trip-logs.index') }}" class="btn-outline !px-3 !py-1.5">گەڕانەوە</a>
    </div>
</div>

<div class="card p-5 mb-4 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 text-sm">
    <div><div class="text-slate-400 text-xs">شۆفێر</div><div class="font-semibold text-slate-800">{{ $log->driver->name ?? '—' }}</div></div>
    <div><div class="text-slate-400 text-xs">پڕۆژە</div><div class="font-semibold text-slate-800">{{ $log->project->name ?? '—' }}</div></div>
    <div><div class="text-slate-400 text-xs">بەروار</div><div class="font-semibold text-slate-800">{{ optional($log->date)->format('Y-m-d') }}</div></div>
    <div><div class="text-slate-400 text-xs">تۆمارکار</div><div class="font-semibold text-slate-800">{{ $log->user->name ?? '—' }}</div></div>
    @if($log->driver && $log->driver->vehicle_number)<div><div class="text-slate-400 text-xs">ئۆتۆمبێل</div><div class="font-semibold text-slate-800">{{ $log->driver->vehicle_number }}</div></div>@endif
</div>

<div class="card p-0 mb-4">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">جۆری کار</th>
                    <th class="px-4 py-3 font-semibold">ژمارەی سەفەر</th>
                    <th class="px-4 py-3 font-semibold">نرخی سەفەر</th>
                    <th class="px-4 py-3 font-semibold">دراو</th>
                    <th class="px-4 py-3 font-semibold">کۆی هێڵ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($log->details as $d)
                    <tr class="table-row">
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $d->work_type_name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $qty($d->trip_count) }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $num($d->price_per_trip) }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $d->currency === 'USD' ? '$' : 'د.ع' }}</td>
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $num($d->line_total) }} {{ $d->currency === 'USD' ? '$' : 'د.ع' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="card p-4">
        <div class="font-bold text-sm text-slate-700 mb-2">دیناری عێراقی (د.ع)</div>
        <div class="flex justify-between py-1 text-sm"><span class="text-slate-500">کۆی گشتی</span><span class="font-bold text-slate-800">{{ $num($log->grand_total_iqd) }}</span></div>
        <div class="flex justify-between py-1 text-sm"><span class="text-slate-500">دراوە</span><span class="font-bold text-green-700">{{ $num($log->paid_iqd) }}</span></div>
        <div class="flex justify-between py-1 text-sm border-t border-slate-100 mt-1 pt-2"><span class="text-slate-500">ماوە</span><span class="font-bold {{ (float)$log->remaining_iqd > 0 ? 'text-red-600' : 'text-green-700' }}">{{ $num($log->remaining_iqd) }}</span></div>
    </div>
    <div class="card p-4">
        <div class="font-bold text-sm text-slate-700 mb-2">دۆلاری ئەمریکی ($)</div>
        <div class="flex justify-between py-1 text-sm"><span class="text-slate-500">کۆی گشتی</span><span class="font-bold text-slate-800">{{ $num($log->grand_total_usd) }}</span></div>
        <div class="flex justify-between py-1 text-sm"><span class="text-slate-500">دراوە</span><span class="font-bold text-green-700">{{ $num($log->paid_usd) }}</span></div>
        <div class="flex justify-between py-1 text-sm border-t border-slate-100 mt-1 pt-2"><span class="text-slate-500">ماوە</span><span class="font-bold {{ (float)$log->remaining_usd > 0 ? 'text-red-600' : 'text-green-700' }}">{{ $num($log->remaining_usd) }}</span></div>
    </div>
</div>

@if($log->notes)
    <div class="card p-4 text-sm text-slate-600 mt-4"><span class="text-slate-400">تێبینی: </span>{{ $log->notes }}</div>
@endif
@endsection
