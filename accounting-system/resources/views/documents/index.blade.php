@extends('layouts.app')

@section('title', 'نووسراوەکان')
@section('page-title', 'نووسراوەکان')
@section('page-subtitle', 'لیستی نووسراو و بەڵگەنامەکان')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">لیستی نووسراوەکان</h2>
    <a href="{{ route('documents.create') }}" class="btn-warning">+ نووسراو نوێ</a>
</div>

{{-- Search --}}
<form method="GET" action="{{ route('documents.index') }}" class="card p-4 mb-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
    <div class="sm:col-span-2">
        <label class="label">گەڕان</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="ناونیشان، بۆ کێ، جۆر" class="input-field">
    </div>
    <div class="flex items-end gap-2">
        <button type="submit" class="btn-info">گەڕان</button>
        <a href="{{ route('documents.index') }}" class="btn-outline">سڕینەوە</a>
    </div>
</form>

<div class="card p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">ناونیشان</th>
                    <th class="px-4 py-3 font-semibold">جۆر</th>
                    <th class="px-4 py-3 font-semibold">بۆ کێ</th>
                    <th class="px-4 py-3 font-semibold">بەروار</th>
                    <th class="px-4 py-3 font-semibold">کردارەکان</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $document)
                    <tr class="table-row">
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $document->title }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $document->doc_type ?: '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $document->recipient ?: '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $document->doc_date?->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('documents.show', $document) }}" class="btn-info !px-3 !py-1.5">بینین</a>
                                <a href="{{ route('documents.print', $document) }}" target="_blank" class="btn-info !px-3 !py-1.5">چاپ</a>
                                <a href="{{ route('documents.edit', $document) }}" class="btn-warning !px-3 !py-1.5">دەستکاری</a>
                                <form method="POST" action="{{ route('documents.destroy', $document) }}" onsubmit="return confirm('دڵنیایت لە سڕینەوە؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger !px-3 !py-1.5">سڕینەوە</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-slate-400">هیچ نووسراوێک نییە.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $documents->links() }}
</div>
@endsection
