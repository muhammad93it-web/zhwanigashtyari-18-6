@extends('layouts.app')
@section('title', 'مامەڵەکان')
@section('page-title', 'لیستی مامەڵەکان')
@section('page-subtitle', 'دەفتەری مامەڵە و بانقبەستەکان')

@section('content')
<div class="space-y-5 animate-fade-in">

    <!-- Filters -->
    <div class="card p-4">
        <form method="GET" class="grid grid-cols-2 lg:grid-cols-6 gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="گەڕان..." class="input-field lg:col-span-2">
            <select name="client_id" class="input-field">
                <option value="">هەمووی کڕیاران</option>
                @foreach($clients as $c)
                <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
            <select name="type" class="input-field">
                <option value="">هەمووی جۆرەکان</option>
                @foreach($types as $key => $label)
                <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="input-field" title="لە بەروار">
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="input-field" title="بۆ بەروار">
            <div class="col-span-2 lg:col-span-6 flex gap-2">
                <button type="submit" class="btn-primary">فلتەرکردن</button>
                @if(request()->hasAny(['search','client_id','type','from_date','to_date']))
                <a href="{{ route('transactions.index') }}" class="btn-outline">پاككردنەوە</a>
                @endif
                <div class="flex-1"></div>
                <a href="{{ route('reports.export.excel', request()->only(['client_id','type','from_date','to_date'])) }}" class="btn-outline flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    Excel
                </a>
                <a href="{{ route('transactions.create') }}" class="btn-gold">+ مامەڵەی نوێ</a>
            </div>
        </form>
    </div>

    <!-- Totals Row -->
    @if($totals)
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card p-3 flex items-center justify-between">
            <span class="text-xs text-teal-500">کۆی مامەڵەکان</span>
            <span class="text-lg font-bold text-white">{{ number_format($totals->total_count) }}</span>
        </div>
        <div class="card p-3 flex items-center justify-between">
            <span class="text-xs text-teal-500">کۆی دۆلار</span>
            <span class="text-lg font-bold text-emerald-400">${{ number_format($totals->total_usd, 2) }}</span>
        </div>
        <div class="card p-3 flex items-center justify-between">
            <span class="text-xs text-teal-500">کۆی دینار</span>
            <span class="text-lg font-bold text-amber-400">{{ number_format($totals->total_iqd, 0) }} د.ع</span>
        </div>
    </div>
    @endif

    <!-- Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-teal-900/60 border-b border-teal-700/40">
                        <th class="px-4 py-3.5 text-right text-xs text-teal-400 font-semibold">ژمارەی مامەڵە</th>
                        <th class="px-4 py-3.5 text-right text-xs text-teal-400 font-semibold">کڕیار</th>
                        <th class="px-4 py-3.5 text-right text-xs text-teal-400 font-semibold">جۆر</th>
                        <th class="px-4 py-3.5 text-right text-xs text-teal-400 font-semibold">وەسف</th>
                        <th class="px-4 py-3.5 text-right text-xs text-teal-400 font-semibold">دراو</th>
                        <th class="px-4 py-3.5 text-right text-xs text-teal-400 font-semibold">بڕی دۆلار</th>
                        <th class="px-4 py-3.5 text-right text-xs text-teal-400 font-semibold">بڕی دینار</th>
                        <th class="px-4 py-3.5 text-right text-xs text-teal-400 font-semibold">بەروار</th>
                        <th class="px-4 py-3.5 text-right text-xs text-teal-400 font-semibold">کارەکان</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-teal-800/30">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-teal-800/15 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs text-teal-500">{{ $tx->reference_number }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('clients.show', $tx->client) }}" class="text-white font-medium hover:text-gold-400 transition-colors">{{ $tx->client?->name }}</a>
                        </td>
                        <td class="px-4 py-3"><span class="{{ $tx->type_badge }}">{{ $tx->type_name }}</span></td>
                        <td class="px-4 py-3 text-teal-300 max-w-[200px] truncate" title="{{ $tx->description }}">{{ $tx->description }}</td>
                        <td class="px-4 py-3 text-xs font-semibold {{ $tx->currency === 'USD' ? 'text-green-400' : 'text-amber-400' }}">{{ $tx->currency }}</td>
                        <td class="px-4 py-3 text-white font-mono">${{ number_format($tx->amount_usd, 2) }}</td>
                        <td class="px-4 py-3 text-teal-300 font-mono text-xs">{{ number_format($tx->amount_iqd, 0) }}</td>
                        <td class="px-4 py-3 text-teal-400 text-xs">{{ $tx->transaction_date->format('Y/m/d') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('transactions.show', $tx) }}" class="text-teal-400 hover:text-gold-400 transition-colors" title="وردبوونەوە">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                                </a>
                                <a href="{{ route('transactions.print', $tx) }}" target="_blank" class="text-teal-400 hover:text-emerald-400 transition-colors" title="چاپ">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"/></svg>
                                </a>
                                <form method="POST" action="{{ route('transactions.destroy', $tx) }}" onsubmit="return confirm('دڵنیاکارتەوە دەیەت بیسڕیتەوە؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-teal-600 hover:text-red-400 transition-colors" title="سڕینەوە">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center">
                            <div class="text-teal-600 mb-2">
                                <svg class="w-12 h-12 mx-auto opacity-30" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
                            </div>
                            <p class="text-teal-500 text-sm">هیچ مامەڵەیەک نەدۆزرایەوە</p>
                            <a href="{{ route('transactions.create') }}" class="inline-block mt-3 btn-primary">تۆمارکردنی مامەڵەی یەکەم</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
        <div class="p-4 border-t border-teal-700/30">{{ $transactions->links() }}</div>
        @endif
    </div>

</div>
@endsection
