@extends('layouts.app')

@php
    $isPurchase = $type === 'purchase';
    $pageTitle = $isPurchase ? 'کڕینی مەواد' : 'فرۆشتنی مەواد';
    $partyLabel = $isPurchase ? 'دابینکەر' : 'کڕیار';
@endphp

@section('title', $pageTitle)
@section('page-title', $pageTitle)
@section('page-subtitle', $isPurchase ? 'تۆمارکردنی کڕینی مەواد بۆ کۆگا' : 'تۆمارکردنی فرۆشتنی مەواد لە کۆگا')

@section('content')
<div class="max-w-2xl">
    <div class="card p-5">
        <form method="POST" action="{{ route('movements.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">

            <div>
                <label class="label" for="material_id">مەواد <span class="text-red-500">*</span></label>
                <select id="material_id" name="material_id" class="input-field" required>
                    <option value="">— هەڵبژێرە —</option>
                    @foreach($materials as $material)
                        <option value="{{ $material->id }}" {{ old('material_id') == $material->id ? 'selected' : '' }}>
                            {{ $material->name }} ({{ number_format((float) $material->current_stock, 0) }} {{ $material->unit }})
                        </option>
                    @endforeach
                </select>
                @error('material_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label" for="quantity">بڕ <span class="text-red-500">*</span></label>
                    <input type="number" step="0.001" id="quantity" name="quantity" value="{{ old('quantity') }}" class="input-field" required>
                    @error('quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label" for="unit_price">نرخی یەکە <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" id="unit_price" name="unit_price" value="{{ old('unit_price') }}" class="input-field" required>
                    @error('unit_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Live total --}}
            <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 border border-slate-200">
                <span class="text-sm text-slate-600">کۆی گشتی</span>
                <span id="liveTotal" class="text-lg font-extrabold text-green-600">0</span>
            </div>

            <div>
                <label class="label" for="currency">دراو</label>
                <select id="currency" name="currency" class="input-field">
                    <option value="IQD" {{ old('currency', 'IQD') === 'IQD' ? 'selected' : '' }}>دینار (IQD)</option>
                    <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>دۆلار (USD)</option>
                </select>
                @error('currency') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-slate-400 mt-1">ڕێژەی ئێستا: {{ number_format((float) $currentRate, 0) }} دینار بۆ ١ دۆلار</p>
            </div>

            <div>
                <label class="label" for="party_name">{{ $partyLabel }}</label>
                <input type="text" id="party_name" name="party_name" value="{{ old('party_name') }}" class="input-field">
                @error('party_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label" for="client_id">کڕیار (ئەگەر هەبێت)</label>
                <select id="client_id" name="client_id" class="input-field">
                    <option value="">— هیچ —</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                    @endforeach
                </select>
                @error('client_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label" for="movement_date">بەروار <span class="text-red-500">*</span></label>
                <input type="date" id="movement_date" name="movement_date" value="{{ old('movement_date', date('Y-m-d')) }}" class="input-field" required>
                @error('movement_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label" for="notes">تێبینی</label>
                <textarea id="notes" name="notes" rows="3" class="input-field">{{ old('notes') }}</textarea>
                @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-2 pt-2">
                <button type="submit" class="btn-primary">تۆمارکردن</button>
                <a href="{{ route('materials.index') }}" class="btn-outline">گەڕانەوە</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const qty = document.getElementById('quantity');
        const price = document.getElementById('unit_price');
        const currency = document.getElementById('currency');
        const out = document.getElementById('liveTotal');

        function recalc() {
            const total = (parseFloat(qty.value) || 0) * (parseFloat(price.value) || 0);
            const symbol = currency.value === 'USD' ? '$' : ' د';
            const formatted = currency.value === 'USD'
                ? total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                : Math.round(total).toLocaleString('en-US');
            out.textContent = currency.value === 'USD' ? ('$' + formatted) : (formatted + ' د');
        }

        [qty, price, currency].forEach(el => el.addEventListener('input', recalc));
        currency.addEventListener('change', recalc);
        recalc();
    })();
</script>
@endpush
@endsection
