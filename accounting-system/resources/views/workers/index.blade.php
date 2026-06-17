@extends('layouts.app')

@section('title', 'کرێکاران')
@section('page-title', 'کرێکاران')
@section('page-subtitle', 'لیستی کرێکارەکان')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">کرێکاران</h2>
    <div class="flex items-center gap-2">
        <a href="{{ route('labor-payments.index') }}" class="btn-outline">کرێی کارەکان</a>
        <a href="{{ route('workers.create') }}" class="btn-primary">+ کرێکاری نوێ</a>
    </div>
</div>

<form method="GET" action="{{ route('workers.index') }}" class="card p-4 mb-4 flex gap-3">
    <input type="text" name="search" value="{{ request('search') }}" class="input-field" placeholder="گەڕان بە ناو/پیشە/مۆبایل">
    <button type="submit" class="btn-info">گەڕان</button>
    <a href="{{ route('workers.index') }}" class="btn-outline">سڕینەوە</a>
</form>

<div class="card p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">ناو</th>
                    <th class="px-4 py-3 font-semibold">پیشە</th>
                    <th class="px-4 py-3 font-semibold">مۆبایل</th>
                    <th class="px-4 py-3 font-semibold">کرێی کاتژمێر</th>
                    <th class="px-4 py-3 font-semibold">ژمارەی پارەدان</th>
                    <th class="px-4 py-3 font-semibold">دۆخ</th>
                    <th class="px-4 py-3 font-semibold">کردارەکان</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workers as $w)
                    <tr class="table-row">
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $w->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $w->role ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $w->phone ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $w->default_hourly_rate ? number_format((float)$w->default_hourly_rate, 0) . ' ' . ($w->default_currency === 'USD' ? '$' : 'د.ع') : '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $w->labor_payments_count }}</td>
                        <td class="px-4 py-3">@if($w->is_active)<span class="badge-green">چالاک</span>@else<span class="badge-slate">ناچالاک</span>@endif</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('workers.show', $w) }}" class="btn-info !px-3 !py-1.5">بینین</a>
                                <a href="{{ route('workers.edit', $w) }}" class="btn-warning !px-3 !py-1.5">دەستکاری</a>
                                <form method="POST" action="{{ route('workers.destroy', $w) }}" onsubmit="return confirm('دڵنیایت لە سڕینەوە؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger !px-3 !py-1.5">سڕینەوە</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-slate-400">هیچ کرێکارێک نییە.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $workers->links() }}</div>
@endsection
