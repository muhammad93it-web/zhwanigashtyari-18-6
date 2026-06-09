@extends('layouts.app')

@section('title', 'وەستای نوێ')
@section('page-title', 'وەستای نوێ')
@section('page-subtitle', 'زیادکردنی وەستا')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-slate-800">زانیاری وەستا</h2>
        <a href="{{ route('contractors.index') }}" class="btn-outline">گەڕانەوە</a>
    </div>

    <form method="POST" action="{{ route('contractors.store') }}" class="card p-5 space-y-4">
        @csrf

        <div>
            <label class="label">ناو *</label>
            <input type="text" name="name" value="{{ old('name') }}" class="input-field" required>
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">مۆبایل</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="input-field">
            @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="label">جۆری کار *</label>
                <select name="work_type" id="work_type" class="input-field" required onchange="toggleWorkType()">
                    <option value="per_meter" @selected(old('work_type','per_meter')=='per_meter')>بە مەتر</option>
                    <option value="contract" @selected(old('work_type')=='contract')>قۆنتەرات</option>
                </select>
                @error('work_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label">دراو</label>
                <select name="currency" class="input-field">
                    <option value="IQD" @selected(old('currency','IQD')=='IQD')>دینار (IQD)</option>
                    <option value="USD" @selected(old('currency')=='USD')>دۆلار (USD)</option>
                </select>
                @error('currency') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div id="field_per_meter">
            <label class="label">نرخی هەر مەترێک</label>
            <input type="number" step="0.01" name="rate_per_meter" value="{{ old('rate_per_meter') }}" class="input-field">
            @error('rate_per_meter') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div id="field_contract">
            <label class="label">کۆی قۆنتەرات</label>
            <input type="number" step="0.01" name="contract_amount" value="{{ old('contract_amount') }}" class="input-field">
            @error('contract_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">تێبینی</label>
            <textarea name="notes" rows="3" class="input-field">{{ old('notes') }}</textarea>
            @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2 text-sm text-slate-700">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="w-4 h-4 rounded border-slate-300 text-green-600 focus:ring-green-500">
            چالاکە
        </label>

        <div class="flex items-center gap-2 pt-2">
            <button type="submit" class="btn-primary">پاشەکەوتکردن</button>
            <a href="{{ route('contractors.index') }}" class="btn-outline">پاشگەزبوونەوە</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function toggleWorkType() {
        var t = document.getElementById('work_type').value;
        document.getElementById('field_per_meter').style.display = (t === 'per_meter') ? '' : 'none';
        document.getElementById('field_contract').style.display = (t === 'contract') ? '' : 'none';
    }
    toggleWorkType();
</script>
@endpush
@endsection
