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
            } }
        };
    </script>
    <style>
        * { font-family: 'Noto Kufi Arabic', sans-serif; }
        body { background: linear-gradient(160deg, #f1f5f9 0%, #ecfdf5 55%, #f8fafc 100%); background-attachment: fixed; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 shadow-xl shadow-green-500/30 mb-4">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 mb-1">گۆڕینی وشەی نهێنی</h1>
            <p class="text-green-600 text-sm font-semibold">ژوانی گەشتیاری</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-xl shadow-slate-200/60">
            <p class="text-sm text-slate-600 mb-6 text-center leading-relaxed">
                ئیمەیڵەکەت هەڵبژێرە و بەستەری گۆڕینی وشەی نهێنیت بۆ دەنێردرێت.
            </p>

            @if(session('success'))
                <div class="mb-4 p-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">بەکارهێنەر</label>
                    <select name="user_id" required
                        class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-slate-800 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500/30 transition-all text-sm">
                        <option value="" disabled {{ old('user_id') ? '' : 'selected' }}>— بەکارهێنەرێک هەڵبژێرە —</option>
                        @foreach(($users ?? []) as $u)
                            <option value="{{ $u->id }}" {{ (string) old('user_id') === (string) $u->id ? 'selected' : '' }}>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-l from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white py-3 rounded-xl font-bold text-sm transition-all duration-200 shadow-lg shadow-green-500/25">
                    ناردنی بەستەر
                </button>
            </form>

            <div class="text-center mt-6">
                <a href="{{ route('login') }}" class="text-sm text-slate-500 hover:text-green-600 transition-colors">
                    ← گەڕانەوە بۆ چوونەژوورەوە
                </a>
            </div>
        </div>
    </div>
</body>
</html>
