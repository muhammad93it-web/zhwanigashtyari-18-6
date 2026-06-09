@extends('layouts.app')
@section('title', 'ڕێژەی گۆڕینی دراو')
@section('page-title', 'ڕێژەی گۆڕینی دراو')
@section('page-subtitle', 'دیاریکردنی نرخی دینار/دۆلار')

@section('content')
<div class="space-y-6 animate-fade-in">

    <!-- Current Rate -->
    @if($current)
    <div class="card p-6 border-gold-500/20">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center shadow-lg shadow-gold-500/20">
                <svg class="w-8 h-8 text-navy-950" fill="currentColor" viewBox="0 0 20 20"><path d="M8 5a1 1 0 100 2h5.586l-1.293 1.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L13.586 5H8z"/></svg>
            </div>
            <div>
                <div class="text-xs text-teal-500 mb-1">ڕێژەی ئێستا (چالاک)</div>
                <div class="text-4xl font-bold text-white font-mono">{{ number_format($current->usd_to_iqd, 0) }} <span class="text-lg text-teal-400">دینار/دۆلار</span></div>
                <div class="text-xs text-teal-500 mt-1">
                    کارگێڕاو لە {{ $current->effective_from->format('Y/m/d H:i') }}
                    @if($current->set_by) — {{ $current->set_by }} @endif
                    @if($current->notes) — {{ $current->notes }} @endif
                </div>
            </div>
        </div>
        <div class="mt-4 p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-xs text-amber-300">
            ⚠️ گۆڕانی ڕێژەی گۆڕین تەنها سەر بە مامەڵەی داهاتوو کاریگەری دەبێت. مامەڵە کۆنەکان بە ڕێژەی خۆیاندا دەمێنن و هەرگیز گۆڕانی تێدا نابێت.
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Set New Rate -->
        <div class="card p-6">
            <h3 class="font-bold text-white text-sm mb-4">دیاریکردنی ڕێژەی نوێ</h3>

            @if(session('success'))
            <div class="mb-4 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs">
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
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-teal-500 font-semibold">
                            د.ع / $
                        </div>
                    </div>
                    @error('usd_to_iqd')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="label">تێبینی (ئارەزووی)</label>
                    <input type="text" name="notes" value="{{ old('notes') }}" class="input-field" placeholder="هۆکاری گۆڕانی ڕێژە...">
                </div>

                <button type="submit" class="btn-gold w-full">
                    پاشەکەوتکردنی ڕێژەی نوێ
                </button>
            </form>

            <div class="mt-4 p-3 rounded-xl bg-teal-900/40 text-xs text-teal-500">
                <div class="font-semibold text-teal-400 mb-1">تێبینی گرینگ:</div>
                <div>• مامەڵەی کۆن ڕێژەی خۆیاندا دەمێنن (قەفل کراوە)</div>
                <div>• تەنها مامەڵەی نوێ ئەم ڕێژەیەی بەکاردێنن</div>
                <div>• گشتی مێژووی گۆڕانەکان ئێرە دەتوانرێت ببینرێت</div>
            </div>
        </div>

        <!-- Rate History -->
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-teal-700/30">
                <h3 class="font-bold text-white text-sm">مێژووی گۆڕانی ڕێژەکان</h3>
            </div>
            <div class="overflow-y-auto max-h-96">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 bg-teal-900/60">
                        <tr>
                            <th class="px-4 py-3 text-right text-xs text-teal-500 font-semibold">ڕێژە (د.ع/$)</th>
                            <th class="px-4 py-3 text-right text-xs text-teal-500 font-semibold">بەروار</th>
                            <th class="px-4 py-3 text-right text-xs text-teal-500 font-semibold">کارگێڕا</th>
                            <th class="px-4 py-3 text-right text-xs text-teal-500 font-semibold"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-teal-800/30">
                        @forelse($rates as $rate)
                        <tr class="hover:bg-teal-800/15 transition-colors {{ $rate->id === $current?->id ? 'bg-gold-500/5' : '' }}">
                            <td class="px-4 py-3">
                                <span class="font-mono font-bold {{ $rate->id === $current?->id ? 'text-gold-400' : 'text-white' }}">
                                    {{ number_format($rate->usd_to_iqd, 0) }}
                                </span>
                                @if($rate->id === $current?->id)
                                <span class="mr-1 text-xs text-gold-500">← ئێستا</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-teal-400 text-xs font-mono">{{ $rate->effective_from->format('Y/m/d H:i') }}</td>
                            <td class="px-4 py-3 text-teal-400 text-xs">{{ $rate->set_by ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if($rate->id !== $current?->id)
                                <form method="POST" action="{{ route('exchange-rates.destroy', $rate) }}" onsubmit="return confirm('سڕینەوەی ئەم تۆمارە؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-teal-700 hover:text-red-400 transition-colors">
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
