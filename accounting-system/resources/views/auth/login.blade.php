<!DOCTYPE html>
<html lang="ku" dir="rtl" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>چوونەژوورەوە — سیستەمی ژمێریاری</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { kufi: ['"Noto Kufi Arabic"', 'sans-serif'] } } }
        };
    </script>
    <style>
        * { font-family: 'Noto Kufi Arabic', sans-serif; }
        body { background: linear-gradient(135deg, #011c26 0%, #032d40 40%, #050a1a 100%); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 shadow-2xl shadow-amber-500/30 mb-4">
                <svg class="w-10 h-10 text-slate-900" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white mb-1">سیستەمی ژمێریاری</h1>
            <p class="text-teal-400 text-sm">ژوانی گەشتیاری</p>
        </div>

        <!-- Login Card -->
        <div class="bg-teal-900/40 backdrop-blur border border-teal-700/30 rounded-2xl p-8 shadow-2xl">
            <h2 class="text-lg font-bold text-white mb-6 text-center">چوونەژوورەوە بۆ هەژمار</h2>

            @if($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-red-500/15 border border-red-500/30 text-red-400 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-teal-300 mb-1.5">ئیمەیڵ</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full bg-teal-950/60 border border-teal-700/50 rounded-xl px-4 py-3 text-white placeholder-teal-600 focus:outline-none focus:border-amber-400/70 focus:ring-1 focus:ring-amber-400/30 transition-all text-sm"
                        placeholder="admin@example.com">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-teal-300 mb-1.5">وشەی نهێنی</label>
                    <input type="password" name="password" required
                        class="w-full bg-teal-950/60 border border-teal-700/50 rounded-xl px-4 py-3 text-white placeholder-teal-600 focus:outline-none focus:border-amber-400/70 focus:ring-1 focus:ring-amber-400/30 transition-all text-sm"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember" class="rounded border-teal-600">
                    <label for="remember" class="text-sm text-teal-400">لەبیر بکەوە</label>
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-900 py-3 rounded-xl font-bold text-sm transition-all duration-200 shadow-lg hover:shadow-amber-500/25">
                    چوونەژوورەوە
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-teal-700 mt-6">
            © {{ date('Y') }} سیستەمی ژمێریاری ژوانی گەشتیاری
        </p>
    </div>
</body>
</html>
