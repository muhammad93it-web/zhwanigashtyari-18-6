@extends('layouts.app')

@section('title', 'کەسەکان')
@section('page-title', 'کەسەکان / کڕیاران')
@section('page-subtitle', 'بەڕێوەبردنی کڕیاران و کەسەکان')

@section('content')
<div class="space-y-5 animate-fade-in">

    <!-- Toolbar -->
    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
        <form method="GET" class="flex gap-2 flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="گەڕان بە ناو، تەلەفۆن..." class="input-field max-w-xs">
            <select name="status" class="input-field max-w-32">
                <option value="">هەمووی</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>چالاک</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>ناچالاک</option>
            </select>
            <button type="submit" class="btn-outline">گەڕان</button>
            @if(request()->hasAny(['search','status']))
            <a href="{{ route('clients.index') }}" class="btn-outline">پاककردنەوە</a>
            @endif
        </form>
        <a href="{{ route('clients.create') }}" class="btn-warning flex items-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
            زیادکردنی کڕیار
        </a>
    </div>

    <!-- Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[640px]">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-5 py-3 font-semibold">#</th>
                    <th class="px-5 py-3 font-semibold">ناو</th>
                    <th class="px-5 py-3 font-semibold">تەلەفۆن</th>
                    <th class="px-5 py-3 font-semibold">ئیمەیڵ</th>
                    <th class="px-5 py-3 font-semibold">مامەڵەکان</th>
                    <th class="px-5 py-3 font-semibold">دۆخ</th>
                    <th class="px-5 py-3 font-semibold">کارەکان</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                <tr class="table-row">
                    <td class="px-5 py-4 text-slate-400 font-mono text-xs">{{ $client->id }}</td>
                    <td class="px-5 py-4">
                        <a href="{{ route('clients.show', $client) }}" class="font-semibold text-slate-800 hover:text-green-600 transition-colors">{{ $client->name }}</a>
                        @if($client->address)
                        <div class="text-xs text-slate-500 mt-0.5">{{ $client->address }}</div>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-slate-600 font-mono text-sm">{{ $client->phone ?? '—' }}</td>
                    <td class="px-5 py-4 text-slate-500 text-sm">{{ $client->email ?? '—' }}</td>
                    <td class="px-5 py-4">
                        <span class="badge-slate">{{ $client->transactions_count }}</span>
                    </td>
                    <td class="px-5 py-4">
                        @if($client->is_active)
                        <span class="badge-green inline-flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> چالاک
                        </span>
                        @else
                        <span class="badge-red inline-flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> ناچالاک
                        </span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('clients.show', $client) }}" class="text-slate-400 hover:text-cyan-600 transition-colors" title="بینین">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                            </a>
                            <a href="{{ route('clients.edit', $client) }}" class="text-slate-400 hover:text-amber-500 transition-colors" title="دەستکاری">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirm('دڵنیاکارتەوە دەیەت ئەم کڕیارە بسڕیتەوە؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors" title="سڕینەوە">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center">
                        <div class="text-slate-300 mb-2">
                            <svg class="w-12 h-12 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <p class="text-slate-500 text-sm">هیچ کڕیارێک نەدۆزرایەوە</p>
                        <a href="{{ route('clients.create') }}" class="inline-block mt-3 btn-primary">زیادکردنی کڕیاری یەکەم</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($clients->hasPages())
    <div class="flex justify-center">
        {{ $clients->links() }}
    </div>
    @endif

</div>
@endsection
