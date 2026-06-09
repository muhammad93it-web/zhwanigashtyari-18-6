@extends('layouts.app')

@section('title', 'چاپکردنی بەشەکان')
@section('page-title', 'چاپکردنی بەشەکان')
@section('page-subtitle', 'هەڵبژاردنی بەش و ماوەی بەروار بۆ چاپکردن')

@section('content')
<div class="max-w-3xl">
    <form method="GET" action="{{ route('print-center.print') }}" target="_blank" class="card p-5 space-y-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="label">لە بەرواری</label>
                <input type="date" name="from_date" value="{{ now()->startOfMonth()->format('Y-m-d') }}" class="input-field">
            </div>
            <div>
                <label class="label">بۆ بەرواری</label>
                <input type="date" name="to_date" value="{{ now()->format('Y-m-d') }}" class="input-field">
            </div>
        </div>

        <div>
            <label class="label mb-3">بەشەکان</label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach($sections as $key => $label)
                    <label class="flex items-center gap-2 p-3 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors cursor-pointer text-sm text-slate-700">
                        <input type="checkbox" name="sections[]" value="{{ $key }}" checked class="w-4 h-4 rounded border-slate-300 text-green-600 focus:ring-green-500">
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" class="btn-info">چاپکردن</button>
        </div>
    </form>
</div>
@endsection
