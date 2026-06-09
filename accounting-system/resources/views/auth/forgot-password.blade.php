<!DOCTYPE html>
<html lang="ku" dir="rtl" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>گۆڕینی وشەی نهێنی — سیستەمی ژمێریاری</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                fontFamily: { kufi: ['"Noto Kufi Arabic"', 'sans-serif'] },
                colors: { gold: { 300: '#f3d68a', 400: '#eec24f', 500: '#e0a82e', 600: '#c08e20' } },
            } }
        };
    </script>
    <style>
        * { font-family: 'Noto Kufi Arabic', sans-serif; }
        body { background: linear-gradient(160deg, #07181f 0%, #0d2530 55%, #0a1320 100%); background-attachment: fixed; }
        select option { background-color: #0d2530; color: #fff; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-gold-400 to-gold-600 shadow-2xl shadow-gold-500/30 mb-4">
                <svg class="w-10 h-10 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-white mb-1">گۆڕینی وشەی نهێنی</h1>
            <p class="text-teal-300 text-sm">ژوانی گەشتیاری</p>
        </div>

        <div class="bg-[#0d2530]/70 backdrop-blur border border-teal-700/30 rounded-2xl p-6 sm:p-8 shadow-2xl">
            <p class="text-sm text-teal-300 mb-6 text-center leading-relaxed">
                ئیمەیڵەکەت هەڵبژێرە و بەستەری گۆڕینی وشەی نهێنیت بۆ دەنێردرێت.
            </p>

            @if(session('success'))
                <div class="mb-4 p-3 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-red-500/15 border border-red-500/30 text-red-300 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-teal-200 mb-1.5">بەکارهێنەر</label>
                    <select name="user_id" required
                        class="w-full bg-[#07181f]/70 border border-teal-700/50 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-gold-400/70 focus:ring-1 focus:ring-gold-400/30 transition-all text-sm">
                        <option value="" disabled {{ old('user_id') ? '' : 'selected' }}>— بەکارهێنەرێک هەڵبژێرە —</option>
                        @foreach(($users ?? []) as $u)
                            <option value="{{ $u->id }}" {{ (string) old('user_id') === (string) $u->id ? 'selected' : '' }}>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-l from-gold-500 to-gold-600 hover:from-gold-400 hover:to-gold-500 text-slate-900 py-3 rounded-xl font-bold text-sm transition-all duration-200 shadow-lg hover:shadow-gold-500/25">
                    ناردنی بەستەر
                </button>
            </form>

            <div class="text-center mt-6">
                <a href="{{ route('login') }}" class="text-sm text-teal-400 hover:text-gold-400 transition-colors">
                    ← گەڕانەوە بۆ چوونەژوورەوە
                </a>
            </div>
        </div>
    </div>
</body>
</html>
