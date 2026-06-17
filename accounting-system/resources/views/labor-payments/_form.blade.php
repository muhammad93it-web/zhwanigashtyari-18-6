@php $payment = $payment ?? null; @endphp

@if($errors->any())
    <div class="card p-4 mb-4 bg-red-50 border-red-200 text-red-600 text-sm">
        <ul class="list-disc pe-5 space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ $action }}" class="space-y-4" id="laborForm">
    @csrf
    @if(($method ?? 'POST') === 'PUT')@method('PUT')@endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div>
            <label class="label">کرێکار</label>
            <select name="worker_id" id="workerSelect" class="input-field">
                <option value="">— کرێکاری کاتی (ناو بنووسە) —</option>
                @foreach($workers as $w)
                    <option value="{{ $w->id }}"
                        data-rate="{{ $w->default_hourly_rate }}"
                        data-currency="{{ $w->default_currency }}"
                        data-role="{{ $w->role }}"
                        @selected(old('worker_id', $payment->worker_id ?? request('worker_id'))==$w->id)>{{ $w->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label">ناوی کرێکار (کاتی)</label>
            <input type="text" name="worker_name" value="{{ old('worker_name', $payment->worker_name ?? '') }}" class="input-field" placeholder="ئەگەر لە لیست نییە">
        </div>
        <div>
            <label class="label">پیشە / جۆری کار</label>
            <input type="text" name="role" id="roleInput" value="{{ old('role', $payment->role ?? '') }}" class="input-field">
        </div>
        <div>
            <label class="label">پڕۆژە</label>
            <select name="project_id" class="input-field">
                <option value="">— بێ پڕۆژە —</option>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}" @selected(old('project_id', $payment->project_id ?? '')==$p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label">بەروار *</label>
            <input type="date" name="date" value="{{ old('date', optional($payment)->date?->format('Y-m-d') ?? date('Y-m-d')) }}" class="input-field" required>
        </div>
        <div>
            <label class="label">دراو *</label>
            <select name="currency" id="currencySelect" class="input-field">
                <option value="IQD" @selected(old('currency', $payment->currency ?? 'IQD')==='IQD')>دیناری عێراقی (د.ع)</option>
                <option value="USD" @selected(old('currency', $payment->currency ?? '')==='USD')>دۆلاری ئەمریکی ($)</option>
            </select>
        </div>
    </div>

    <div class="card p-4 bg-slate-50">
        <label class="flex items-center gap-2 mb-3">
            <input type="checkbox" name="is_hourly" id="isHourly" value="1" {{ old('is_hourly', $payment->is_hourly ?? false) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-green-600 focus:ring-green-500">
            <span class="text-sm font-semibold text-slate-700">حیسابی کاتژمێری (کاتژمێر × کرێی کاتژمێر)</span>
        </label>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div id="hoursWrap">
                <label class="label">ژمارەی کاتژمێر</label>
                <input type="number" step="0.01" name="hours" id="hoursInput" value="{{ old('hours', $payment->hours ?? '') }}" class="input-field" oninput="calcAmount()">
            </div>
            <div id="rateWrap">
                <label class="label">کرێی کاتژمێر</label>
                <input type="number" step="0.01" name="hourly_rate" id="rateInput" value="{{ old('hourly_rate', $payment->hourly_rate ?? '') }}" class="input-field" oninput="calcAmount()">
            </div>
            <div>
                <label class="label">بڕی گشتی *</label>
                <input type="number" step="0.01" name="amount" id="amountInput" value="{{ old('amount', $payment->amount ?? '') }}" class="input-field">
                <p class="text-xs text-slate-400 mt-1" id="amountHint">لە دۆخی کاتژمێری خۆکارانە حیسابدەکرێت.</p>
            </div>
        </div>
    </div>

    <div>
        <label class="label">تێبینی</label>
        <textarea name="notes" rows="2" class="input-field">{{ old('notes', $payment->notes ?? '') }}</textarea>
    </div>

    <div class="flex items-center gap-2 pt-2">
        <button type="submit" class="btn-primary">{{ $submitLabel ?? 'تۆمارکردن' }}</button>
        <a href="{{ route('labor-payments.index') }}" class="btn-outline">گەڕانەوە</a>
    </div>
</form>

@push('scripts')
<script>
    const isHourly = document.getElementById('isHourly');
    const hoursInput = document.getElementById('hoursInput');
    const rateInput = document.getElementById('rateInput');
    const amountInput = document.getElementById('amountInput');
    const hoursWrap = document.getElementById('hoursWrap');
    const rateWrap = document.getElementById('rateWrap');
    const amountHint = document.getElementById('amountHint');
    const workerSelect = document.getElementById('workerSelect');

    function calcAmount() {
        if (!isHourly.checked) { return; }
        const h = parseFloat(hoursInput.value) || 0;
        const r = parseFloat(rateInput.value) || 0;
        amountInput.value = (Math.round(h * r * 100) / 100);
    }

    function toggleHourly() {
        const on = isHourly.checked;
        hoursWrap.style.display = on ? '' : 'none';
        rateWrap.style.display = on ? '' : 'none';
        amountInput.readOnly = on;
        amountHint.style.display = on ? '' : 'none';
        if (on) { calcAmount(); }
    }

    workerSelect.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        if (!this.value) { return; }
        const rate = opt.getAttribute('data-rate');
        const cur = opt.getAttribute('data-currency');
        const role = opt.getAttribute('data-role');
        if (rate && !rateInput.value) { rateInput.value = rate; }
        if (cur) { document.getElementById('currencySelect').value = cur; }
        if (role && !document.getElementById('roleInput').value) { document.getElementById('roleInput').value = role; }
        calcAmount();
    });

    isHourly.addEventListener('change', toggleHourly);
    toggleHourly();
</script>
@endpush
