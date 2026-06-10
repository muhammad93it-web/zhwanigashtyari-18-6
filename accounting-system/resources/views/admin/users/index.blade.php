@extends('layouts.app')
@section('title', 'بەکارهێنەران')
@section('page-title', 'بەڕێوەبردنی بەکارهێنەران')
@section('page-subtitle', 'زیادکردن، دەسەڵات، و گۆڕینی وشەی نهێنی')
@section('content')
<div class="animate-fade-in space-y-4">

    @if($errors->any())
    <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm space-y-1">
        @foreach($errors->all() as $error)<div>• {{ $error }}</div>@endforeach
    </div>
    @endif

    <div class="flex items-center justify-between">
        <div class="text-sm text-slate-500">کۆی بەکارهێنەران: <span class="font-bold text-slate-700">{{ $users->count() }}</span></div>
        <a href="{{ route('users.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"/></svg>
            بەکارهێنەری نوێ
        </a>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs">
                        <th class="text-right font-semibold px-4 py-3">ناو</th>
                        <th class="text-right font-semibold px-4 py-3">ئیمەیڵ</th>
                        <th class="text-right font-semibold px-4 py-3">جۆر</th>
                        <th class="text-right font-semibold px-4 py-3">دەسەڵاتەکان</th>
                        <th class="text-center font-semibold px-4 py-3">کردارەکان</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr class="table-row">
                        <td class="px-4 py-3 font-semibold text-slate-800">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">{{ mb_substr($user->name, 0, 1) }}</div>
                                {{ $user->name }}
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-500" dir="ltr" style="text-align:right;">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            @if($user->is_admin)
                                <span class="badge-green">بەڕێوەبەر</span>
                            @else
                                <span class="badge-slate">بەکارهێنەر</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($user->is_admin)
                                <span class="text-xs text-slate-400">هەموو دەسەڵاتەکان</span>
                            @elseif(count($user->permissions ?? []))
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->permissions as $perm)
                                        <span class="badge-cyan text-[10px]">{{ \App\Models\User::MODULES[$perm] ?? $perm }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-xs text-red-400">هیچ دەسەڵاتێک نییە</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('users.edit', $user) }}" class="p-2 rounded-lg text-cyan-600 hover:bg-cyan-50" title="دەستکاری">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                                </a>
                                <a href="{{ route('users.password.edit', $user) }}" class="p-2 rounded-lg text-amber-600 hover:bg-amber-50" title="گۆڕینی وشەی نهێنی">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                                </a>
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('دڵنیایت لە سڕینەوەی ئەم بەکارهێنەرە؟');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg text-red-500 hover:bg-red-50" title="سڕینەوە">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
