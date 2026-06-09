<!DOCTYPE html>
<html lang="ku" dir="rtl" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>چوونەژوورەوە — سیستەمی ژمێریاری</title>
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
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 shadow-xl shadow-green-500/30 mb-4">
                <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 mb-1">سیستەمی ژمێریاری</h1>
            <p class="text-green-600 text-sm font-semibold">ژوانی گەشتیاری</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-xl shadow-slate-200/60">
            <h2 class="text-lg font-bold text-slate-800 mb-6 text-center">چوونەژوورەوە بۆ هەژمار</h2>

            @if($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
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

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">وشەی نهێنی</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl ps-4 pe-12 py-3 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500/30 transition-all text-sm"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePassword()" aria-label="پیشاندانی وشەی نهێنی"
                            class="absolute inset-y-0 left-0 flex items-center px-4 text-slate-400 hover:text-green-600 transition-colors">
                            <!-- eye (show) -->
                            <svg id="eyeShow" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <!-- eye-off (hide) -->
                            <svg id="eyeHide" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-green-600 focus:ring-green-500">
                        لەبیر بکەوە
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-green-600 hover:text-green-700 transition-colors">
                        وشەی نهێنیت لەبیرچووە؟
                    </a>
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-l from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white py-3 rounded-xl font-bold text-sm transition-all duration-200 shadow-lg shadow-green-500/25">
                    چوونەژوورەوە
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-slate-400 mt-6">
            © {{ date('Y') }} سیستەمی ژمێریاری ژوانی گەشتیاری
        </p>
    </div>

    <script>
        function togglePassword() {
            var input = document.getElementById('password');
            var show = document.getElementById('eyeShow');
            var hide = document.getElementById('eyeHide');
            if (input.type === 'password') {
                input.type = 'text';
                show.classList.add('hidden');
                hide.classList.remove('hidden');
            } else {
                input.type = 'password';
                hide.classList.add('hidden');
                show.classList.remove('hidden');
            }
        }
    </script>
</body>
</html>
