@extends('layouts.app')

@section('title', 'ناردن بۆ تێلێگرام')
@section('page-title', 'ناردنی خۆکار بۆ تێلێگرام')
@section('page-subtitle', 'ناردنی باکئەپ و ڕاپۆرتەکان بۆ تێلێگرام')

@section('content')

@php
    $triggerLabels = ['schedule' => 'خۆکار', 'manual' => 'دەستی'];
@endphp

{{-- ===== STATUS BANNER ===== --}}
@if($isConfigured)
<div class="flex items-center gap-3 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm font-medium mb-6">
    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
    تێلێگرام ڕێکخراوە و ئامادەیە بۆ ناردن.
</div>
@else
<div class="flex items-center gap-3 px-4 py-3 rounded-lg bg-amber-50 border border-amber-200 text-amber-700 text-sm font-medium mb-6">
    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
    تێلێگرام تەواو ڕێکنەخراوە. تکایە تۆکنی بۆت و چات ئایدی دابنێ و پاشان تاقیکردنەوە بکە.
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- ===== SETTINGS ===== --}}
    <div class="card p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-sky-500 to-blue-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M9.78 18.65l.28-4.23 7.68-6.92c.34-.31-.07-.46-.52-.19L7.74 13.3 3.64 12c-.88-.25-.89-.86.2-1.3l15.97-6.16c.73-.33 1.43.18 1.15 1.3l-2.72 12.81c-.19.91-.74 1.13-1.5.71L12.6 16.3l-1.99 1.93c-.23.23-.42.42-.83.42z"/></svg>
            </div>
            <div>
                <h2 class="text-base font-bold text-slate-800">ڕێکخستنی پەیوەندی</h2>
                <p class="text-xs text-slate-500">تۆکنی بۆت و چات ئایدی</p>
            </div>
        </div>

        <form method="POST" action="{{ route('telegram.settings') }}" class="space-y-4">
            @csrf
            <div>
                <label class="label" for="telegram_bot_token">تۆکنی بۆت (Bot Token)</label>
                <input type="password" id="telegram_bot_token" name="telegram_bot_token" autocomplete="new-password"
                       placeholder="{{ $hasToken ? '•••••••••• (دانراوە — بۆ گۆڕین تۆکنی نوێ بنووسە)' : 'بۆ نموونە: 123456789:ABCdef...' }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
                @error('telegram_bot_token')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                @if($hasToken)
                <label class="flex items-center gap-2 mt-2 text-xs text-slate-500 cursor-pointer">
                    <input type="checkbox" name="clear_token" value="1" class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                    سڕینەوەی تۆکنی ئێستا
                </label>
                @endif
            </div>

            <div>
                <label class="label" for="telegram_chat_id">چات ئایدی (Chat ID)</label>
                <input type="text" id="telegram_chat_id" name="telegram_chat_id" value="{{ old('telegram_chat_id', $chatId) }}"
                       placeholder="بۆ نموونە: 123456789"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
                @error('telegram_chat_id')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn-primary w-full justify-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                پاشکەوتکردن
            </button>
        </form>

        <form method="POST" action="{{ route('telegram.test') }}" class="mt-3">
            @csrf
            <button type="submit" class="btn-outline w-full justify-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                تاقیکردنەوەی پەیوەندی (نامەیەکی تاقیکردنەوە بنێرە)
            </button>
        </form>
    </div>

    {{-- ===== ADD SCHEDULE ===== --}}
    <div class="card p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
            </div>
            <div>
                <h2 class="text-base font-bold text-slate-800">زیادکردنی کاتی ناردن</h2>
                <p class="text-xs text-slate-500">دیاریکردنی چی و کەی بنێردرێت</p>
            </div>
        </div>

        <form method="POST" action="{{ route('telegram.schedules.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="label" for="title">ناونیشان (ئیختیاری)</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}"
                       placeholder="بۆ نموونە: باکئەپی ڕۆژانە"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
                @error('title')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="label" for="content_type">چی بنێردرێت</label>
                <select id="content_type" name="content_type"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none bg-white">
                    @foreach($contentTypes as $key => $label)
                        <option value="{{ $key }}" @selected(old('content_type') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('content_type')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label" for="frequency">دووبارەبوونەوە</label>
                    <select id="frequency" name="frequency" onchange="toggleDayOfMonth()"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none bg-white">
                        @foreach($frequencies as $key => $label)
                            <option value="{{ $key }}" @selected(old('frequency') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('frequency')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="label" for="send_time">کاتی ناردن</label>
                    <input type="time" id="send_time" name="send_time" value="{{ old('send_time', '08:00') }}"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
                    @error('send_time')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
            </div>

            <div id="day-of-month-wrap" class="hidden">
                <label class="label" for="day_of_month">ڕۆژی مانگ (١ – ٣١)</label>
                <input type="number" id="day_of_month" name="day_of_month" min="1" max="31" value="{{ old('day_of_month', 1) }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
                <p class="text-xs text-slate-400 mt-1">ئەگەر مانگ ئەو ڕۆژەی تێدا نەبوو، دوایین ڕۆژی مانگ بەکاردێت.</p>
                @error('day_of_month')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            </div>

            <p class="text-xs text-slate-400">بۆ ناردنی چەند جار لە ڕۆژێکدا، چەند کاتێک زیاد بکە.</p>

            <button type="submit" class="btn-primary w-full justify-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
                زیادکردن
            </button>
        </form>
    </div>
</div>

{{-- ===== SCHEDULES LIST ===== --}}
<div class="card p-6 mt-6">
    <div class="flex items-center gap-3 mb-5">
        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-slate-600 to-slate-800 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
        </div>
        <div>
            <h2 class="text-base font-bold text-slate-800">کاتە دیاریکراوەکان</h2>
            <p class="text-xs text-slate-500">لیستی هەموو ناردنە خۆکارەکان</p>
        </div>
    </div>

    @if($schedules->isEmpty())
        <div class="text-center py-8 text-slate-400 text-sm">هێشتا هیچ کاتێک زیاد نەکراوە.</div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-slate-500 text-xs border-b border-slate-200">
                    <th class="text-right font-semibold py-2 px-2">ناونیشان</th>
                    <th class="text-right font-semibold py-2 px-2">جۆر</th>
                    <th class="text-right font-semibold py-2 px-2">کات</th>
                    <th class="text-right font-semibold py-2 px-2">دۆخ</th>
                    <th class="text-right font-semibold py-2 px-2">دوایین ناردن</th>
                    <th class="text-center font-semibold py-2 px-2">کردارەکان</th>
                </tr>
            </thead>
            <tbody>
                @foreach($schedules as $s)
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="py-3 px-2 font-medium text-slate-800">{{ $s->title ?: '—' }}</td>
                    <td class="py-3 px-2 text-slate-600">{{ $s->contentTypeLabel() }}</td>
                    <td class="py-3 px-2 text-slate-600">
                        {{ $s->frequencyLabel() }} — {{ substr($s->send_time, 0, 5) }}
                        @if($s->frequency === 'monthly')
                            <span class="text-slate-400">(ڕۆژی {{ $s->day_of_month }})</span>
                        @endif
                    </td>
                    <td class="py-3 px-2">
                        @if($s->is_active)
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-green-50 text-green-700">چالاک</span>
                        @else
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-500">ناچالاک</span>
                        @endif
                    </td>
                    <td class="py-3 px-2 text-slate-500 text-xs">{{ $s->last_sent_at ? $s->last_sent_at->format('Y-m-d H:i') : '—' }}</td>
                    <td class="py-3 px-2">
                        <div class="flex items-center justify-center gap-1.5">
                            <form method="POST" action="{{ route('telegram.schedules.send', $s) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-green-600 text-white hover:bg-green-700 text-xs font-semibold transition-colors" title="ئێستا بنێرە">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M9.78 18.65l.28-4.23 7.68-6.92c.34-.31-.07-.46-.52-.19L7.74 13.3 3.64 12c-.88-.25-.89-.86.2-1.3l15.97-6.16c.73-.33 1.43.18 1.15 1.3l-2.72 12.81c-.19.91-.74 1.13-1.5.71L12.6 16.3l-1.99 1.93c-.23.23-.42.42-.83.42z"/></svg>
                                    ئێستا بنێرە
                                </button>
                            </form>
                            <form method="POST" action="{{ route('telegram.schedules.toggle', $s) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 text-xs font-semibold transition-colors">
                                    {{ $s->is_active ? 'ناچالاککردن' : 'چالاککردن' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('telegram.schedules.destroy', $s) }}" onsubmit="return confirm('دڵنیایت لە سڕینەوەی ئەم کاتە؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 text-xs font-semibold transition-colors">
                                    سڕینەوە
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- ===== INSTRUCTIONS ===== --}}
<div class="card p-6 mt-6">
    <div class="flex items-center gap-3 mb-5">
        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
        </div>
        <div>
            <h2 class="text-base font-bold text-slate-800">ڕێنمایی ڕێکخستن</h2>
            <p class="text-xs text-slate-500">چۆن بۆت دروست بکەیت و ناردنی خۆکار چالاک بکەیت</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Bot setup --}}
        <div class="bg-slate-50 rounded-xl p-5">
            <h3 class="font-bold text-slate-800 mb-3 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold">١</span>
                دروستکردنی بۆت و وەرگرتنی تۆکن
            </h3>
            <ol class="space-y-2 text-sm text-slate-600 list-decimal pr-5">
                <li>لە تێلێگرام بگەڕێ بۆ <span class="font-mono font-bold text-slate-800">&#64;BotFather</span> و دەستی پێ بکە.</li>
                <li>فەرمانی <span class="font-mono font-bold text-slate-800">/newbot</span> بنووسە و ناوێک بۆ بۆتەکە دیاری بکە.</li>
                <li>BotFather تۆکنێکت دەداتێ (وەک <span class="font-mono">123456789:ABC...</span>) — ئەوە بیکۆپی بکە بۆ خانەی «تۆکنی بۆت».</li>
            </ol>

            <h3 class="font-bold text-slate-800 mb-3 mt-5 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold">٢</span>
                وەرگرتنی چات ئایدی (Chat ID)
            </h3>
            <ol class="space-y-2 text-sm text-slate-600 list-decimal pr-5">
                <li>لە تێلێگرام بگەڕێ بۆ بۆتەکەی خۆت و کلیک لە <span class="font-bold">Start</span> بکە (زۆر گرنگە).</li>
                <li>پاشان بگەڕێ بۆ <span class="font-mono font-bold text-slate-800">&#64;userinfobot</span> و کلیک لە Start بکە؛ ئەو ژمارەی Id پیشانت دەدات — ئەوە چات ئایدیتە.</li>
                <li>ژمارەکە بنووسە لە خانەی «چات ئایدی» و پاشکەوتی بکە، پاشان «تاقیکردنەوەی پەیوەندی» بکە.</li>
            </ol>
        </div>

        {{-- Cron setup --}}
        <div class="bg-slate-50 rounded-xl p-5">
            <h3 class="font-bold text-slate-800 mb-3 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-purple-600 text-white text-xs flex items-center justify-center font-bold">٣</span>
                چالاککردنی ناردنی خۆکار (Cron لە cPanel)
            </h3>
            <p class="text-sm text-slate-600 mb-3">بۆ ئەوەی ناردنەکان بەخۆیی لە کاتی دیاریکراودا بنێردرێن، پێویستە یەک جار Cron لە cPanel دابنێیت:</p>
            <ol class="space-y-2 text-sm text-slate-600 list-decimal pr-5">
                <li>لە cPanel بچۆ بۆ بەشی <span class="font-bold">Cron Jobs</span>.</li>
                <li>لە <span class="font-bold">Common Settings</span> هەڵبژێرە <span class="font-bold">Once Per Minute</span> (یان <span class="font-bold">Every 5 Minutes</span>).</li>
                <li>لە خانەی <span class="font-bold">Command</span> ئەم نووسینە دابنێ (ڕێچکەی پڕۆژەکەت بگۆڕە):</li>
            </ol>
            <div class="mt-3 bg-slate-900 rounded-lg p-3 overflow-x-auto">
                <code class="text-xs text-green-300 font-mono whitespace-pre" dir="ltr">/usr/local/bin/php /home/USERNAME/PATH/artisan telegram:dispatch &gt;&gt; /dev/null 2&gt;&amp;1</code>
            </div>
            <ul class="space-y-1.5 text-xs text-slate-500 mt-3 list-disc pr-5">
                <li><span class="font-bold">USERNAME</span> = ناوی هەژماری cPanel ـەکەت.</li>
                <li><span class="font-bold">PATH</span> = ڕێچکەی فۆڵدەری پڕۆژەکە (بۆ نموونە <span class="font-mono">public_html/jwani</span>).</li>
                <li>ئەگەر دڵنیا نیت لە ڕێچکەی PHP، لە cPanel یان لە هۆستەکەت بپرسە.</li>
            </ul>
            <div class="mt-3 flex items-start gap-2 bg-amber-50 border border-amber-200 rounded-lg p-3">
                <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                <p class="text-xs text-amber-700 font-medium">دوگمەی «ئێستا بنێرە» بەبێ Cron کار دەکات — هەر کاتێک بتەوێت دەستی دەتوانیت بنێریت.</p>
            </div>
        </div>
    </div>
</div>

{{-- ===== DELIVERY LOGS ===== --}}
<div class="card p-6 mt-6">
    <div class="flex items-center gap-3 mb-5">
        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-slate-500 to-slate-700 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
        </div>
        <div>
            <h2 class="text-base font-bold text-slate-800">تۆماری ناردنەکان</h2>
            <p class="text-xs text-slate-500">دوایین ٢٥ ناردن (سەرکەوتوو یان هەڵە)</p>
        </div>
    </div>

    @if($logs->isEmpty())
        <div class="text-center py-8 text-slate-400 text-sm">هێشتا هیچ ناردنێک نەکراوە.</div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-slate-500 text-xs border-b border-slate-200">
                    <th class="text-right font-semibold py-2 px-2">کات</th>
                    <th class="text-right font-semibold py-2 px-2">جۆر</th>
                    <th class="text-right font-semibold py-2 px-2">شێواز</th>
                    <th class="text-right font-semibold py-2 px-2">دۆخ</th>
                    <th class="text-right font-semibold py-2 px-2">وردەکاری</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="py-3 px-2 text-slate-500 text-xs whitespace-nowrap">{{ $log->sent_at ? $log->sent_at->format('Y-m-d H:i') : $log->created_at->format('Y-m-d H:i') }}</td>
                    <td class="py-3 px-2 text-slate-600">{{ \App\Models\TelegramSchedule::CONTENT_TYPES[$log->content_type] ?? $log->content_type }}</td>
                    <td class="py-3 px-2 text-slate-500 text-xs">{{ $triggerLabels[$log->trigger] ?? $log->trigger }}</td>
                    <td class="py-3 px-2">
                        @if($log->status === 'success')
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-green-50 text-green-700">سەرکەوتوو</span>
                        @else
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-600">هەڵە</span>
                        @endif
                    </td>
                    <td class="py-3 px-2 text-slate-500 text-xs max-w-xs truncate" title="{{ $log->message }}">{{ $log->message ?: '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
function toggleDayOfMonth() {
    var freq = document.getElementById('frequency').value;
    var wrap = document.getElementById('day-of-month-wrap');
    if (freq === 'monthly') {
        wrap.classList.remove('hidden');
    } else {
        wrap.classList.add('hidden');
    }
}
document.addEventListener('DOMContentLoaded', toggleDayOfMonth);
</script>
@endpush
