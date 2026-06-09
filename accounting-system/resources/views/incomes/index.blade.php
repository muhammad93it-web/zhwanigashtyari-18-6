@extends('layouts.app')

@section('title', 'وەرگرتنی پارە')
@section('page-title', 'وەرگرتنی پارە')
@section('page-subtitle', 'لیستی هەموو داهاتەکان')

@section('content')
@php
    $iqd = fn($v) => number_format((float) $v, 0);
@endphp

<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">داهاتەکان</h2>
    <a href="{{ route('incomes.create') }}" class="btn-primary">+ زیادکردنی داهات</a>
</div>

{{-- Totals --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
    <div class="stat-card">
        <span class="text-xs text-slate-500 font-medium">کۆی گشتی (دینار)</span>
        <div class="text-2xl font-extrabold text-green-600">{{ $iqd($totals->iqd ?? 0) }} د</div>
    </div>
    <div class="stat-card">
        <span class="text-xs text-slate-500 font-medium">کۆی گشتی (دۆلار)</span>
        <div class="text-2xl font-extrabold text-cyan-600">${{ number_format((float)($totals->usd ?? 0), 2) }}</div>
    </div>
    <div class="stat-card">
        <span class="text-xs text-slate-500 font-medium">ژمارەی تۆمارەکان</span>
        <div class="text-2xl font-extrabold text-slate-800">{{ $totals->c ?? 0 }}</div>
    </div>
</div>

{{-- Filters --}}
<div class="card p-4 mb-4">
    <form method="GET" action="{{ route('incomes.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
        <div>
            <label class="label">گەڕان</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="سەرچاوە، وەسف، ژمارەی بەڵگە" class="input-field">
        </div>
        <div>
            <label class="label">لە بەرواری</label>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="input-field">
        </div>
        <div>
            <label class="label">تا بەرواری</label>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="input-field">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn-info flex-1">گەڕان</button>
            <a href="{{ route('incomes.index') }}" class="btn-outline">پاککردنەوە</a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">سەرچاوە</th>
                    <th class="px-4 py-3 font-semibold">جۆر</th>
                    <th class="px-4 py-3 font-semibold">بڕ</th>
                    <th class="px-4 py-3 font-semibold">دینار</th>
                    <th class="px-4 py-3 font-semibold">بەروار</th>
                    <th class="px-4 py-3 font-semibold">کردارەکان</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incomes as $income)
                    <tr class="table-row">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-800">{{ $income->source }}</div>
                            @if($income->description)
                                <div class="text-xs text-slate-400">{{ $income->description }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $income->category ?? '—' }}</td>
                        <td class="px-4 py-3 font-medium text-slate-700">
                            @if($income->currency === 'USD')
                                ${{ number_format((float)$income->amount, 2) }}
                            @else
                                {{ $iqd($income->amount) }} د
                            @endif
                        </td>
                        <td class="px-4 py-3 font-bold text-green-600">{{ $iqd($income->amount_iqd) }} د</td>
                        <td class="px-4 py-3 text-slate-600">{{ $income->income_date->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('incomes.show', $income) }}" class="btn-info !px-2.5 !py-1.5">بینین</a>
                                <a href="{{ route('incomes.edit', $income) }}" class="btn-warning !px-2.5 !py-1.5">دەستکاری</a>
                                <form method="POST" action="{{ route('incomes.destroy', $income) }}" onsubmit="return confirm('دڵنیایت لە سڕینەوە؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger !px-2.5 !py-1.5">سڕینەوە</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-slate-400">هیچ داهاتێک نییە.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $incomes->links() }}
</div>
@endsection
