@extends('layouts.app')
@section('title', 'دەستکاری بەکارهێنەر')
@section('page-title', 'دەستکاریکردنی بەکارهێنەر')
@section('page-subtitle', $user->name)
@section('content')
<div class="max-w-2xl animate-fade-in space-y-4">
    <div class="card p-6">
        @if($errors->any())
        <div class="mb-5 p-4 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm space-y-1">
            @foreach($errors->all() as $error)<div>• {{ $error }}</div>@endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="label">ناوی بەکارهێنەر <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="input-field">
                </div>
                <div>
                    <label class="label">ئیمەیڵ (بۆ چوونەژوورەوە) <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="input-field" dir="ltr">
                </div>
            </div>

            <div class="border border-slate-200 rounded-lg p-4 bg-slate-50/60">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_admin" value="1" id="isAdmin" onchange="togglePerms()" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300 text-green-600 focus:ring-green-500">
                    <span class="text-sm font-semibold text-slate-800">بەڕێوەبەری گشتی (دەسەڵاتی تەواو بۆ هەموو بەشەکان)</span>
                </label>
            </div>

            <div id="permsBox">
                <label class="label">دەسەڵاتەکان</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    @foreach($modules as $key => $label)
                    <label class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer">
                        <input type="checkbox" name="permissions[]" value="{{ $key }}" {{ in_array($key, old('permissions', $user->permissions ?? [])) ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300 text-green-600 focus:ring-green-500">
                        <span class="text-sm text-slate-700">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary">پاشەکەوتکردن</button>
                <a href="{{ route('users.index') }}" class="btn-outline">پاشگەزبوونەوە</a>
                <a href="{{ route('users.password.edit', $user) }}" class="btn-warning mr-auto">گۆڕینی وشەی نهێنی</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePerms() {
        const isAdmin = document.getElementById('isAdmin').checked;
        document.getElementById('permsBox').style.display = isAdmin ? 'none' : 'block';
    }
    togglePerms();
</script>
@endpush
