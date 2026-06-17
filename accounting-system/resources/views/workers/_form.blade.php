@php $worker = $worker ?? null; @endphp

@if($errors->any())
    <div class="card p-4 mb-4 bg-red-50 border-red-200 text-red-600 text-sm">
        <ul class="list-disc pe-5 space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ $action }}" class="space-y-4">
    @csrf
    @if(($method ?? 'POST') === 'PUT')@method('PUT')@endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="label">ناوی کرێکار <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $worker->name ?? '') }}" class="input-field" required autofocus>
        </div>
        <div>
            <label class="label">پیشە / جۆری کار</label>
            <input type="text" name="role" value="{{ old('role', $worker->role ?? '') }}" class="input-field" placeholder="نمونە: بەننا، کرێکاری گشتی">
        </div>
        <div>
            <label class="label">ژمارەی مۆبایل</label>
            <input type="text" name="phone" value="{{ old('phone', $worker->phone ?? '') }}" class="input-field">
        </div>
        <div>
            <label class="label">دراوی بنەڕەت <span class="text-red-500">*</span></label>
            <select name="default_currency" class="input-field">
                <option value="IQD" @selected(old('default_currency', $worker->default_currency ?? 'IQD')==='IQD')>دیناری عێراقی (د.ع)</option>
                <option value="USD" @selected(old('default_currency', $worker->default_currency ?? '')==='USD')>دۆلاری ئەمریکی ($)</option>
            </select>
        </div>
        <div>
            <label class="label">کرێی کاتژمێر (بنەڕەت)</label>
            <input type="number" step="0.01" name="default_hourly_rate" value="{{ old('default_hourly_rate', $worker->default_hourly_rate ?? '') }}" class="input-field" placeholder="بۆ حیسابی کاتژمێری">
        </div>
    </div>

    <div>
        <label class="label">تێبینی</label>
        <textarea name="notes" rows="2" class="input-field">{{ old('notes', $worker->notes ?? '') }}</textarea>
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $worker->is_active ?? true) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-green-600 focus:ring-green-500">
        <label for="is_active" class="text-sm font-semibold text-slate-700">چالاک</label>
    </div>

    <div class="flex items-center gap-2 pt-2">
        <button type="submit" class="btn-primary">{{ $submitLabel ?? 'پاشەکەوتکردن' }}</button>
        <a href="{{ route('workers.index') }}" class="btn-outline">گەڕانەوە</a>
    </div>
</form>
