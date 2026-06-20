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

    @php $mode = old('payment_mode', $payment->payment_mode ?? ($payment && !($payment->is_hourly) ? 'fixed' : 'hourly')); @endphp
    <div class="card p-4 bg-slate-50">
        <div class="mb-3">
            <label class="label">شێوازی پارەدان *</label>
            <select name="payment_mode" id="paymentMode" class="input-field">
                <option value="fixed" @selected($mode==='fixed')>بڕی جێگیر (دەستی)</option>
                <option value="hourly" @selected($mode==='hourly')>کاتژمێری (کاتژمێر × کرێی کاتژمێر)</option>
                <option value="daily" @selected($mode==='daily')>ڕۆژانە (ڕۆژ × کرێی ڕۆژانە)</option>
            </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div id="hoursWrap">
                <label class="label">ژمارەی کاتژمێر</label>
                <input type="number" step="0.01" name="hours" id="hoursInput" value="{{ old('hours', $payment->hours ?? '') }}" class="input-field" oninput="calcAmount()">
            </div>
            <div id="hourlyRateWrap">
                <label class="label">کرێی کاتژمێر</label>
                <input type="number" step="0.01" name="hourly_rate" id="hourlyRateInput" value="{{ old('hourly_rate', $payment->hourly_rate ?? '') }}" class="input-field" oninput="calcAmount()">
            </div>
            <div id="daysWrap">
                <label class="label">ژمارەی ڕۆژ</label>
                <input type="number" step="0.01" name="days" id="daysInput" value="{{ old('days', $payment->days ?? '') }}" class="input-field" oninput="calcAmount()">
            </div>
            <div id="dailyRateWrap">
                <label class="label">کرێی ڕۆژانە</label>
                <input type="number" step="0.01" name="daily_rate" id="dailyRateInput" value="{{ old('daily_rate', $payment->daily_rate ?? '') }}" class="input-field" oninput="calcAmount()">
            </div>
            <div>
                <label class="label">بڕی گشتی *</label>
                <input type="number" step="0.01" name="amount" id="amountInput" value="{{ old('amount', $payment->amount ?? '') }}" class="input-field">
                <p class="text-xs text-slate-400 mt-1" id="amountHint">لە دۆخی کاتژمێری/ڕۆژانە خۆکارانە حیسابدەکرێت.</p>
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
    const paymentMode = document.getElementById('paymentMode');
    const hoursInput = document.getElementById('hoursInput');
    const hourlyRateInput = document.getElementById('hourlyRateInput');
    const daysInput = document.getElementById('daysInput');
    const dailyRateInput = document.getElementById('dailyRateInput');
    const amountInput = document.getElementById('amountInput');
    const hoursWrap = document.getElementById('hoursWrap');
    const hourlyRateWrap = document.getElementById('hourlyRateWrap');
    const daysWrap = document.getElementById('daysWrap');
    const dailyRateWrap = document.getElementById('dailyRateWrap');
    const amountHint = document.getElementById('amountHint');
    const workerSelect = document.getElementById('workerSelect');

    function calcAmount() {
        const mode = paymentMode.value;
        if (mode === 'hourly') {
            const h = parseFloat(hoursInput.value) || 0;
            const r = parseFloat(hourlyRateInput.value) || 0;
            amountInput.value = (Math.round(h * r * 100) / 100);
        } else if (mode === 'daily') {
            const d = parseFloat(daysInput.value) || 0;
            const r = parseFloat(dailyRateInput.value) || 0;
            amountInput.value = (Math.round(d * r * 100) / 100);
        }
    }

    function toggleMode() {
        const mode = paymentMode.value;
        const isHourly = mode === 'hourly';
        const isDaily = mode === 'daily';
        const auto = isHourly || isDaily;
        hoursWrap.style.display = isHourly ? '' : 'none';
        hourlyRateWrap.style.display = isHourly ? '' : 'none';
        daysWrap.style.display = isDaily ? '' : 'none';
        dailyRateWrap.style.display = isDaily ? '' : 'none';
        amountInput.readOnly = auto;
        amountHint.style.display = auto ? '' : 'none';
        if (auto) { calcAmount(); }
    }

    workerSelect.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        if (!this.value) { return; }
        const rate = opt.getAttribute('data-rate');
        const cur = opt.getAttribute('data-currency');
        const role = opt.getAttribute('data-role');
        if (rate && paymentMode.value === 'hourly' && !hourlyRateInput.value) { hourlyRateInput.value = rate; }
        if (cur) { document.getElementById('currencySelect').value = cur; }
        if (role && !document.getElementById('roleInput').value) { document.getElementById('roleInput').value = role; }
        calcAmount();
    });

    paymentMode.addEventListener('change', toggleMode);
    toggleMode();
</script>
@endpush
