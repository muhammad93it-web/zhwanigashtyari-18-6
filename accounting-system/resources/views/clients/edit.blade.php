@extends('layouts.app')
@section('title', 'دەستکاری کڕیار')
@section('page-title', 'دەستکاری زانیاری کڕیار')
@section('content')
<div class="max-w-2xl animate-fade-in">
    <div class="card p-6">
        @if($errors->any())
        <div class="mb-5 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm space-y-1">
            @foreach($errors->all() as $error)<div>• {{ $error }}</div>@endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('clients.update', $client) }}" class="space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="label">ناوی کڕیار <span class="text-red-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $client->name) }}" required class="input-field">
                </div>
                <div>
                    <label class="label">ژمارەی تەلەفۆن</label>
                    <input type="text" name="phone" value="{{ old('phone', $client->phone) }}" class="input-field">
                </div>
                <div>
                    <label class="label">ئیمەیڵ</label>
                    <input type="email" name="email" value="{{ old('email', $client->email) }}" class="input-field">
                </div>
                <div>
                    <label class="label">دۆخ</label>
                    <select name="is_active" class="input-field">
                        <option value="1" {{ old('is_active', $client->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>چالاک</option>
                        <option value="0" {{ old('is_active', $client->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>ناچالاک</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="label">ناونیشان</label>
                    <input type="text" name="address" value="{{ old('address', $client->address) }}" class="input-field">
                </div>
                <div class="sm:col-span-2">
                    <label class="label">تێبینی</label>
                    <textarea name="notes" rows="3" class="input-field">{{ old('notes', $client->notes) }}</textarea>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">پاشەکەوتکردن</button>
                <a href="{{ route('clients.show', $client) }}" class="btn-outline">پاشگەزبوونەوە</a>
            </div>
        </form>
    </div>
</div>
@endsection
