@extends('layouts.app')

@section('title', 'قەرزەکان')
@section('page-title', 'قەرزەکان')
@section('page-subtitle', 'بەڕێوەبردنی قەرزی لای خەڵک و قەرزی ئێمە')

@section('content')
@php
    $iqd = fn($v) => number_format((float) $v, 0);
@endphp

<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">قەرزەکان</h2>
    <a href="{{ route('debts.create') }}" class="btn-primary">+ زیادکردنی قەرز</a>
</div>

{{-- Summary --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <span class="text-xs text-slate-500 font-medium">قەرزی لای خەڵک (بۆ ئێمە)</span>
            <span class="badge-green">+</span>
        </div>
        <div class="text-2xl font-extrabold text-green-600">{{ $iqd($receivable->iqd ?? 0) }} د</div>
        <div class="text-[11px] text-slate-400">${{ number_format((float)($receivable->usd ?? 0), 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <span class="text-xs text-slate-500 font-medium">قەرزی ئێمە (لەسەر ئێمە)</span>
            <span class="badge-red">-</span>
        </div>
        <div class="text-2xl font-extrabold text-red-500">{{ $iqd($payable->iqd ?? 0) }} د</div>
        <div class="text-[11px] text-slate-400">${{ number_format((float)($payable->usd ?? 0), 2) }}</div>
    </div>
</div>

{{-- Filters --}}
<div class="card p-4 mb-4">
    <form method="GET" action="{{ route('debts.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
        <div>
            <label class="label">گەڕان</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="ناوی کەس، وەسف" class="input-field">
        </div>
        <div>
            <label class="label">ئاراستە</label>
            <select name="direction" class="input-field">
                <option value="">هەموو</option>
                <option value="receivable" {{ request('direction') === 'receivable' ? 'selected' : '' }}>قەرزی لای خەڵک</option>
                <option value="payable" {{ request('direction') === 'payable' ? 'selected' : '' }}>قەرزی ئێمە</option>
            </select>
        </div>
        <div>
            <label class="label">دۆخ</label>
            <select name="status" class="input-field">
                <option value="">هەموو</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>نەدراوەتەوە</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>دراوەتەوە</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn-info flex-1">گەڕان</button>
            <a href="{{ route('debts.index') }}" class="btn-outline">پاککردنەوە</a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">ناوی کەس</th>
                    <th class="px-4 py-3 font-semibold">ئاراستە</th>
                    <th class="px-4 py-3 font-semibold">بڕ</th>
                    <th class="px-4 py-3 font-semibold">دینار</th>
                    <th class="px-4 py-3 font-semibold">بەروار</th>
                    <th class="px-4 py-3 font-semibold">دۆخ</th>
                    <th class="px-4 py-3 font-semibold">کردارەکان</th>
                </tr>
            </thead>
            <tbody>
                @forelse($debts as $debt)
                    <tr class="table-row">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-800">{{ $debt->party_name }}</div>
                            @if($debt->description)
                                <div class="text-xs text-slate-400">{{ $debt->description }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="{{ $debt->direction === 'receivable' ? 'badge-green' : 'badge-red' }}">{{ $debt->direction_name }}</span>
                        </td>
                        <td class="px-4 py-3 font-medium text-slate-700">
                            @if($debt->currency === 'USD')
                                ${{ number_format((float)$debt->amount, 2) }}
                            @else
                                {{ $iqd($debt->amount) }} د
                            @endif
                        </td>
                        <td class="px-4 py-3 font-bold {{ $debt->direction === 'receivable' ? 'text-green-600' : 'text-red-500' }}">{{ $iqd($debt->amount_iqd) }} د</td>
                        <td class="px-4 py-3 text-slate-600">{{ $debt->debt_date->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">
                            <span class="{{ $debt->status === 'open' ? 'badge-amber' : 'badge-green' }}">{{ $debt->status_name }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                @if($debt->status === 'open')
                                    <form method="POST" action="{{ route('debts.mark-paid', $debt) }}">
                                        @csrf
                                        <button type="submit" class="btn-primary !px-2.5 !py-1.5">نیشانکردن وەک دراوەتەوە</button>
                                    </form>
                                @endif
                                <a href="{{ route('debts.show', $debt) }}" class="btn-info !px-2.5 !py-1.5">بینین</a>
                                <a href="{{ route('debts.edit', $debt) }}" class="btn-warning !px-2.5 !py-1.5">دەستکاری</a>
                                <form method="POST" action="{{ route('debts.destroy', $debt) }}" onsubmit="return confirm('دڵنیایت لە سڕینەوە؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger !px-2.5 !py-1.5">سڕینەوە</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-slate-400">هیچ قەرزێک نییە.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $debts->links() }}
</div>
@endsection
