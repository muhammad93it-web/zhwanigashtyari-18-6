@extends('layouts.app')

@section('title', 'نووسراوی فەرمی')
@section('page-title', 'نووسراوی فەرمی')
@section('page-subtitle', 'لیستی نامە فەرمییەکان لەسەر لێتەرهێد')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">لیستی نووسراوەکان</h2>
    <a href="{{ route('letters.create') }}" class="btn-warning">+ نووسراوی نوێ</a>
</div>

{{-- Search --}}
<form method="GET" action="{{ route('letters.index') }}" class="card p-4 mb-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
    <div class="sm:col-span-2">
        <label class="label">گەڕان بەپێی ژمارە / بابەت / وەرگر</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="ژمارەی نووسراو..." class="input-field">
    </div>
    <div class="flex items-end gap-2">
        <button type="submit" class="btn-info">گەڕان</button>
        <a href="{{ route('letters.index') }}" class="btn-outline">سڕینەوە</a>
    </div>
</form>

<div class="card p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">ژمارە</th>
                    <th class="px-4 py-3 font-semibold">بەروار</th>
                    <th class="px-4 py-3 font-semibold">بۆ بەڕێز</th>
                    <th class="px-4 py-3 font-semibold">بابەت</th>
                    <th class="px-4 py-3 font-semibold">کردارەکان</th>
                </tr>
            </thead>
            <tbody>
                @forelse($letters as $letter)
                    <tr class="table-row">
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $letter->reference_number }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ optional($letter->letter_date)->format('Y-m-d') }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $letter->recipient ?: '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $letter->subject ?: '—' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('letters.print', $letter) }}" target="_blank" class="btn-info !px-3 !py-1.5">چاپ</a>
                                <a href="{{ route('letters.edit', $letter) }}" class="btn-warning !px-3 !py-1.5">دەستکاری</a>
                                <form method="POST" action="{{ route('letters.destroy', $letter) }}" onsubmit="return confirm('دڵنیایت لە سڕینەوە؟')">
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
    {{ $letters->links() }}
</div>
@endsection
