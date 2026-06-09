<!DOCTYPE html>
<html lang="ku" dir="rtl" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دانانی وشەی نهێنی نوێ — سیستەمی ژمێریاری</title>
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
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-gold-400 to-gold-600 shadow-2xl shadow-gold-500/30 mb-4">
                <svg class="w-10 h-10 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-white mb-1">وشەی نهێنی نوێ</h1>
            <p class="text-teal-300 text-sm">ژوانی گەشتیاری</p>
        </div>

        <div class="bg-[#0d2530]/70 backdrop-blur border border-teal-700/30 rounded-2xl p-6 sm:p-8 shadow-2xl">
            @if($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-red-500/15 border border-red-500/30 text-red-300 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label class="block text-sm font-semibold text-teal-200 mb-1.5">ئیمەیڵ</label>
                    <input type="email" name="email" value="{{ old('email', $email) }}" required readonly
                        class="w-full bg-[#07181f]/70 border border-teal-700/50 rounded-xl px-4 py-3 text-white text-sm">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-teal-200 mb-1.5">وشەی نهێنی نوێ</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required minlength="6"
                            class="w-full bg-[#07181f]/70 border border-teal-700/50 rounded-xl ps-4 pe-12 py-3 text-white placeholder-teal-600 focus:outline-none focus:border-gold-400/70 focus:ring-1 focus:ring-gold-400/30 transition-all text-sm"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePassword('password','eyeShow1','eyeHide1')" aria-label="پیشاندانی وشەی نهێنی"
                            class="absolute inset-y-0 left-0 flex items-center px-4 text-teal-400 hover:text-gold-400 transition-colors">
                            <svg id="eyeShow1" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg id="eyeHide1" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-teal-200 mb-1.5">دووبارەکردنەوەی وشەی نهێنی</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation" required minlength="6"
                            class="w-full bg-[#07181f]/70 border border-teal-700/50 rounded-xl ps-4 pe-12 py-3 text-white placeholder-teal-600 focus:outline-none focus:border-gold-400/70 focus:ring-1 focus:ring-gold-400/30 transition-all text-sm"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePassword('password_confirmation','eyeShow2','eyeHide2')" aria-label="پیشاندانی وشەی نهێنی"
                            class="absolute inset-y-0 left-0 flex items-center px-4 text-teal-400 hover:text-gold-400 transition-colors">
                            <svg id="eyeShow2" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg id="eyeHide2" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-l from-gold-500 to-gold-600 hover:from-gold-400 hover:to-gold-500 text-slate-900 py-3 rounded-xl font-bold text-sm transition-all duration-200 shadow-lg hover:shadow-gold-500/25">
                    گۆڕینی وشەی نهێنی
                </button>
            </form>
        </div>
    </div>

    <script>
        function togglePassword(id, showId, hideId) {
            var input = document.getElementById(id);
            var show = document.getElementById(showId);
            var hide = document.getElementById(hideId);
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
