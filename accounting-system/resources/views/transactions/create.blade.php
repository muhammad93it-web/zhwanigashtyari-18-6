@extends('layouts.app')
@section('title', 'تۆمارکردنی مامەڵە')
@section('page-title', 'تۆمارکردنی مامەڵەی نوێ')
@section('page-subtitle', 'پڕکردنەوەی فۆرمی مامەڵە')

@section('content')
<div class="max-w-2xl animate-fade-in">
    <div class="card p-6">

        @if($errors->any())
        <div class="mb-5 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm space-y-1">
            @foreach($errors->all() as $error)<div>• {{ $error }}</div>@endforeach
        </div>
        @endif

        <!-- Current Rate Notice -->
        <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-amber-500/10 border border-amber-500/30">
            <svg class="w-4 h-4 text-amber-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <div class="text-xs text-amber-300">
                <span class="font-semibold">ڕێژەی گۆڕینی ئێستا: {{ number_format($currentRate, 0) }} دینار / دۆلار</span>
                — ئەم ڕێژەیە لەناو مامەڵەکە دابەزێنراودەبێت و دواتر گۆڕانی تێدا ناكرێت.
            </div>
        </div>

        <form method="POST" action="{{ route('transactions.store') }}" class="space-y-5" id="txForm">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                <!-- Client -->
                <div class="sm:col-span-2">
                    <label class="label">کڕیار <span class="text-red-400">*</span></label>
                    <select name="client_id" required class="input-field">
                        <option value="">— کڕیار هەڵبژێرە —</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id', $preselectedClient?->id) == $client->id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Type -->
                <div>
                    <label class="label">جۆری مامەڵە <span class="text-red-400">*</span></label>
                    <select name="type" required class="input-field" id="typeSelect">
                        <option value="">— جۆر هەڵبژێرە —</option>
                        @foreach($types as $key => $label)
                        <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Currency -->
                <div>
                    <label class="label">دراو <span class="text-red-400">*</span></label>
                    <select name="currency" required class="input-field" id="currencySelect">
                        @foreach($currencies as $c)
                        <option value="{{ $c }}" {{ old('currency', 'USD') == $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Amount -->
                <div class="sm:col-span-2">
                    <label class="label">بڕی پارە <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <input type="number" name="amount" id="amountInput" value="{{ old('amount') }}"
                            required min="0.01" step="0.01"
                            class="input-field pl-20 font-mono text-lg"
                            placeholder="0.00">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2">
                            <span id="currencySymbol" class="text-gold-400 font-bold text-sm">$</span>
                        </div>
                    </div>
                    <!-- Conversion preview -->
                    <div id="conversionPreview" class="mt-2 text-xs text-teal-400 hidden">
                        ≈ <span id="convertedAmount" class="font-mono font-semibold text-gold-400"></span>
                        <span id="convertedCurrency"></span>
                        <span class="text-teal-600">(بەپێی ڕێژەی {{ number_format($currentRate, 0) }})</span>
                    </div>
                </div>

                <!-- Transaction Date -->
                <div>
                    <label class="label">بەرواری مامەڵە <span class="text-red-400">*</span></label>
                    <input type="date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}"
                        required class="input-field">
                </div>

                <!-- Description -->
                <div class="sm:col-span-2">
                    <label class="label">وەسف / وردبوونەوە <span class="text-red-400">*</span></label>
                    <input type="text" name="description" value="{{ old('description') }}" required
                        class="input-field" placeholder="وەسفی مامەڵەکە بنووسە...">
                </div>

                <!-- Notes -->
                <div class="sm:col-span-2">
                    <label class="label">تێبینی (ئارەزووی)</label>
                    <textarea name="notes" rows="2" class="input-field" placeholder="تێبینیی زیادە...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Type indicator badge -->
            <div id="typeBadge" class="hidden p-3 rounded-xl border text-sm font-medium text-center"></div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-gold">تۆمارکردنی مامەڵە</button>
                <a href="{{ route('transactions.index') }}" class="btn-outline">پاشگەزبوونەوە</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const rate = {{ $currentRate }};
const amountInput = document.getElementById('amountInput');
const currencySelect = document.getElementById('currencySelect');
const currencySymbol = document.getElementById('currencySymbol');
const conversionPreview = document.getElementById('conversionPreview');
const convertedAmount = document.getElementById('convertedAmount');
const convertedCurrency = document.getElementById('convertedCurrency');
const typeSelect = document.getElementById('typeSelect');
const typeBadge = document.getElementById('typeBadge');

const typeColors = {
    sale:     { bg: 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400', text: '✅ فرۆشتن — داواکاری پارە لەسەر کڕیار' },
    purchase: { bg: 'bg-red-500/10 border-red-500/30 text-red-400', text: '🛒 کڕین — دابینکردنی کاڵا یان خزمەتگوزاری' },
    debit:    { bg: 'bg-amber-500/10 border-amber-500/30 text-amber-400', text: '📤 قەرز / بردراو — پارەی بردراو لە ئێمە' },
    credit:   { bg: 'bg-blue-500/10 border-blue-500/30 text-blue-400', text: '📥 دانەوەی قەرز — پارەی هێنراو بۆ ئێمە' },
};

function updatePreview() {
    const amount = parseFloat(amountInput.value) || 0;
    const currency = currencySelect.value;

    currencySymbol.textContent = currency === 'USD' ? '$' : 'د.ع';

    if (amount > 0) {
        conversionPreview.classList.remove('hidden');
        if (currency === 'USD') {
            convertedAmount.textContent = new Intl.NumberFormat().format((amount * rate).toFixed(0));
            convertedCurrency.textContent = 'دینار';
        } else {
            convertedAmount.textContent = '$' + (amount / rate).toFixed(4);
            convertedCurrency.textContent = 'دۆلار';
        }
    } else {
        conversionPreview.classList.add('hidden');
    }
}

function updateTypeBadge() {
    const type = typeSelect.value;
    if (type && typeColors[type]) {
        typeBadge.className = `p-3 rounded-xl border text-sm font-medium text-center ${typeColors[type].bg}`;
        typeBadge.textContent = typeColors[type].text;
        typeBadge.classList.remove('hidden');
    } else {
        typeBadge.classList.add('hidden');
    }
}

amountInput.addEventListener('input', updatePreview);
currencySelect.addEventListener('change', updatePreview);
typeSelect.addEventListener('change', updateTypeBadge);
updatePreview();
updateTypeBadge();
</script>
@endpush
