@extends('layouts.app')
@section('title', 'زیادکردنی کڕیار')
@section('page-title', 'زیادکردنی کڕیاری نوێ')
@section('content')
<div class="max-w-2xl animate-fade-in">
    <div class="card p-6">
        @if($errors->any())
        <div class="mb-5 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm space-y-1">
            @foreach($errors->all() as $error)<div>• {{ $error }}</div>@endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('clients.store') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="label">ناوی کڕیار <span class="text-red-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="input-field" placeholder="ناوی تەواو...">
                </div>
                <div>
                    <label class="label">ژمارەی تەلەفۆن</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="input-field" placeholder="07501234567">
                </div>
                <div>
                    <label class="label">ئیمەیڵ</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="input-field" placeholder="example@email.com">
                </div>
                <div>
                    <label class="label">دۆخ</label>
                    <select name="is_active" class="input-field">
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>چالاک</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>ناچالاک</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="label">ناونیشان</label>
                    <input type="text" name="address" value="{{ old('address') }}" class="input-field" placeholder="شار، گەڕەک...">
                </div>
                <div class="sm:col-span-2">
                    <label class="label">تێبینی</label>
                    <textarea name="notes" rows="3" class="input-field" placeholder="زانیاری زیادە...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-gold">زیادکردنی کڕیار</button>
                <a href="{{ route('clients.index') }}" class="btn-outline">پاشگەزبوونەوە</a>
            </div>
        </form>
    </div>
</div>
@endsection
