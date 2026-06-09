@extends('layouts.app')
@section('title', 'ڕێژەی گۆڕینی دراو')
@section('page-title', 'ڕێژەی گۆڕینی دراو')
@section('page-subtitle', 'دیاریکردنی نرخی دینار/دۆلار')

@section('content')
<div class="space-y-6 animate-fade-in">

    <!-- Current Rate -->
    @if($current)
    <div class="card p-6 border-green-300">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-md shadow-green-500/30">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M8 5a1 1 0 100 2h5.586l-1.293 1.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L13.586 5H8z"/></svg>
            </div>
            <div>
                <div class="text-xs text-slate-500 mb-1">ڕێژەی ئێستا (چالاک)</div>
                <div class="text-4xl font-bold text-slate-800 font-mono">{{ number_format($current->usd_to_iqd, 0) }} <span class="text-lg text-slate-500">دینار/دۆلار</span></div>
                <div class="text-xs text-slate-500 mt-1">
                    کارگێڕاو لە {{ $current->effective_from->format('Y/m/d H:i') }}
                    @if($current->set_by) — {{ $current->set_by }} @endif
                    @if($current->notes) — {{ $current->notes }} @endif
                </div>
            </div>
        </div>
        <div class="mt-4 p-3 rounded-lg bg-amber-50 border border-amber-200 text-xs text-amber-700">
            ⚠️ گۆڕانی ڕێژەی گۆڕین تەنها سەر بە مامەڵەی داهاتوو کاریگەری دەبێت. مامەڵە کۆنەکان بە ڕێژەی خۆیاندا دەمێنن و هەرگیز گۆڕانی تێدا نابێت.
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Set New Rate -->
        <div class="card p-6">
            <h3 class="font-bold text-slate-800 text-sm mb-4">دیاریکردنی ڕێژەی نوێ</h3>

            @if(session('success'))
            <div class="mb-4 p-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-xs">
                {{ session('success') }}
            </div>
            @endif

            <form method="POST" action="{{ route('exchange-rates.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="label">نرخی نوێ (چەند دینار = 1 دۆلار)</label>
                    <div class="relative">
                        <input type="number" name="usd_to_iqd" value="{{ old('usd_to_iqd') }}"
                            required min="1" step="0.01"
                            class="input-field font-mono text-lg pl-24"
                            placeholder="{{ $current?->usd_to_iqd ?? '1310' }}">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-500 font-semibold">
                            د.ع / $
                        </div>
                    </div>
                    @error('usd_to_iqd')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="label">تێبینی (ئارەزووی)</label>
                    <input type="text" name="notes" value="{{ old('notes') }}" class="input-field" placeholder="هۆکاری گۆڕانی ڕێژە...">
                </div>

                <button type="submit" class="btn-primary w-full">
                    پاشەکەوتکردنی ڕێژەی نوێ
                </button>
            </form>

            <div class="mt-4 p-3 rounded-lg bg-slate-50 text-xs text-slate-500">
                <div class="font-semibold text-slate-700 mb-1">تێبینی گرینگ:</div>
                <div>• مامەڵەی کۆن ڕێژەی خۆیاندا دەمێنن (قەفل کراوە)</div>
                <div>• تەنها مامەڵەی نوێ ئەم ڕێژەیەی بەکاردێنن</div>
                <div>• گشتی مێژووی گۆڕانەکان ئێرە دەتوانرێت ببینرێت</div>
            </div>
        </div>

        <!-- Rate History -->
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200">
                <h3 class="font-bold text-slate-800 text-sm">مێژووی گۆڕانی ڕێژەکان</h3>
            </div>
            <div class="overflow-y-auto max-h-96">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 bg-white">
                        <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                            <th class="px-4 py-3 font-semibold">ڕێژە (د.ع/$)</th>
                            <th class="px-4 py-3 font-semibold">بەروار</th>
                            <th class="px-4 py-3 font-semibold">کارگێڕا</th>
                            <th class="px-4 py-3 font-semibold"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rates as $rate)
                        <tr class="table-row {{ $rate->id === $current?->id ? 'bg-green-50' : '' }}">
                            <td class="px-4 py-3">
                                <span class="font-mono font-bold {{ $rate->id === $current?->id ? 'text-green-600' : 'text-slate-800' }}">
                                    {{ number_format($rate->usd_to_iqd, 0) }}
                                </span>
                                @if($rate->id === $current?->id)
                                <span class="mr-1 text-xs text-green-600">← ئێستا</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-500 text-xs font-mono">{{ $rate->effective_from->format('Y/m/d H:i') }}</td>
                            <td class="px-4 py-3 text-slate-500 text-xs">{{ $rate->set_by ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if($rate->id !== $current?->id)
                                <form method="POST" action="{{ route('exchange-rates.destroy', $rate) }}" onsubmit="return confirm('سڕینەوەی ئەم تۆمارە؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-teal-600 text-sm">هیچ ڕێژەیەک نییە</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($rates->hasPages())
            <div class="p-3 border-t border-teal-700/30">{{ $rates->links() }}</div>
            @endif
        </div>
    </div>

</div>
@endsection
