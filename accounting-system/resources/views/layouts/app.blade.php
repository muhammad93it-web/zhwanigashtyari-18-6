@php $logoSrc = \App\Support\Branding::logoDataUri(); @endphp
<!DOCTYPE html>
<html lang="ku" dir="rtl" class="h-full" data-theme="green" data-font-scale="1">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'سیستەمی ژمێریاری') — ژوانی گەشتیاری</title>
    <link rel="icon" type="image/png" href="{{ $logoSrc }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { kufi: ['"Noto Kufi Arabic"', 'sans-serif'] },
                    animation: {
                        'fade-in': 'fadeIn 0.3s ease-out',
                        'slide-in': 'slideIn 0.3s ease-out',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                        slideIn: { '0%': { transform: 'translateY(-8px)', opacity: '0' }, '100%': { transform: 'translateY(0)', opacity: '1' } },
                    },
                },
            },
        };
    </script>

    <style>
        * { font-family: 'Noto Kufi Arabic', sans-serif; }
        body { background-color: #eef2f6; min-height: 100vh; }
        ::-webkit-scrollbar { width: 7px; height: 7px; }
        ::-webkit-scrollbar-track { background: #e2e8f0; }
        ::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #64748b; }
        html { font-size: 16px; transition: font-size 0.2s; }
    </style>

    <style type="text/tailwindcss">
        @layer components {
            .card { @apply bg-white border border-slate-200 rounded-xl shadow-sm; }
            .btn { @apply inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg font-semibold transition-all duration-150 text-sm shadow-sm; }
            .btn-primary { @apply btn bg-green-600 hover:bg-green-700 text-white; }
            .btn-info { @apply btn bg-cyan-600 hover:bg-cyan-700 text-white; }
            .btn-danger { @apply btn bg-red-500 hover:bg-red-600 text-white; }
            .btn-warning { @apply btn bg-amber-500 hover:bg-amber-600 text-white; }
            .btn-slate { @apply btn bg-slate-600 hover:bg-slate-700 text-white; }
            .btn-outline { @apply btn bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 shadow-none; }
            .input-field { @apply w-full bg-white border border-slate-300 rounded-lg px-3.5 py-2.5 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition-all duration-150 text-sm; }
            .label { @apply block text-sm font-semibold text-slate-700 mb-1.5; }
            .table-row { @apply border-b border-slate-100 hover:bg-slate-50 transition-colors; }
            .stat-card { @apply card p-5 flex flex-col gap-1.5; }
            .badge { @apply inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold; }
            .badge-green { @apply badge bg-green-100 text-green-700; }
            .badge-red { @apply badge bg-red-100 text-red-700; }
            .badge-amber { @apply badge bg-amber-100 text-amber-700; }
            .badge-cyan { @apply badge bg-cyan-100 text-cyan-700; }
            .badge-slate { @apply badge bg-slate-100 text-slate-600; }
            .badge-sale { @apply badge bg-green-100 text-green-700; }
            .badge-purchase { @apply badge bg-red-100 text-red-700; }
            .badge-debit { @apply badge bg-amber-100 text-amber-700; }
            .badge-credit { @apply badge bg-cyan-100 text-cyan-700; }
            .badge-gray { @apply badge bg-slate-100 text-slate-600; }
        }
    </style>

    <style id="jwani-theme-override"></style>

    @stack('head')
</head>
<body class="h-full text-slate-800">

<div class="flex flex-col h-screen overflow-hidden">

    <!-- ========== TOP BAR ========== -->
    <header class="flex-shrink-0 h-16 bg-white border-b border-slate-200 flex items-center justify-between gap-3 px-4 sm:px-6">
        <div class="flex items-center gap-3 min-w-0">
            @unless(request()->routeIs('dashboard'))
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-green-50 text-green-700 hover:bg-green-100 active:bg-green-200 font-semibold text-sm transition-colors flex-shrink-0">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10.293 15.707a1 1 0 010-1.414L13.586 11H4a1 1 0 110-2h9.586l-3.293-3.293a1 1 0 111.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                <span class="hidden sm:inline">گەڕانەوە بۆ بەشەکان</span>
            </a>
            <div class="h-8 w-px bg-slate-200 flex-shrink-0"></div>
            @endunless
            <div class="min-w-0">
                <h1 class="text-sm sm:text-base font-bold text-slate-800 truncate">@yield('page-title', 'داشبۆرد')</h1>
                <div class="text-xs text-slate-400 truncate">@yield('page-subtitle', '')</div>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-shrink-0">
            <!-- Font Size Controls -->
            <div class="hidden sm:flex items-center gap-1 bg-slate-50 border border-slate-200 rounded-lg px-2 py-1">
                <button type="button" onclick="changeFontSize(-1)" class="w-7 h-7 flex items-center justify-center rounded hover:bg-slate-200 transition-colors text-slate-500 hover:text-slate-700 font-bold text-xs" title="بچووک بکەوە">ا-</button>
                <div class="w-px h-4 bg-slate-300"></div>
                <button type="button" onclick="changeFontSize(1)" class="w-7 h-7 flex items-center justify-center rounded hover:bg-slate-200 transition-colors text-slate-500 hover:text-slate-700 font-bold text-xs" title="گەورە بکەوە">ا+</button>
            </div>

            <div class="h-8 w-px bg-slate-200 hidden sm:block"></div>

            <a href="{{ route('dashboard') }}" class="hidden md:flex items-center gap-2.5" title="ژوانی گەشتیاری">
                <img src="{{ $logoSrc }}" alt="ژوانی گەشتیاری" class="w-9 h-9 object-contain">
                <div class="leading-tight text-right">
                    <div class="text-[11px] text-slate-400">سیستەمی ژمێریاری</div>
                    <div class="text-xs font-bold text-slate-800">ژوانی گەشتیاری</div>
                </div>
            </a>
            <div class="h-8 w-px bg-slate-200 hidden md:block"></div>
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                    {{ mb_substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="hidden sm:block leading-tight">
                    <div class="text-xs font-semibold text-slate-800 truncate max-w-[130px]">{{ Auth::user()->name }}</div>
                    <div class="text-[11px] text-slate-400">{{ Auth::user()->is_admin ? 'بەڕێوەبەر' : 'بەکارهێنەر' }}</div>
                </div>
            </div>
            @if(Auth::user()->hasAccess('telegram'))
            <div class="h-8 w-px bg-slate-200"></div>
            <a href="{{ route('telegram.index') }}" class="inline-flex items-center gap-1.5 text-slate-500 hover:text-green-600 transition-colors px-2 py-1.5 rounded-lg hover:bg-green-50 text-sm font-medium {{ request()->routeIs('telegram.*') ? 'text-green-600 bg-green-50' : '' }}" title="ناردن بۆ تێلێگرام">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M9.78 18.65l.28-4.23 7.68-6.92c.34-.31-.07-.46-.52-.19L7.74 13.3 3.64 12c-.88-.25-.89-.86.2-1.3l15.97-6.16c.73-.33 1.43.18 1.15 1.3l-2.72 12.81c-.19.91-.74 1.13-1.5.71L12.6 16.3l-1.99 1.93c-.23.23-.42.42-.83.42z"/></svg>
                <span class="hidden lg:inline">تێلێگرام</span>
            </a>
            @endif
            @if(Auth::user()->is_admin)
            <div class="h-8 w-px bg-slate-200"></div>
            <a href="{{ route('settings.index') }}" class="inline-flex items-center gap-1.5 text-slate-500 hover:text-green-600 transition-colors px-2 py-1.5 rounded-lg hover:bg-green-50 text-sm font-medium {{ request()->routeIs('settings.*') ? 'text-green-600 bg-green-50' : '' }}" title="ڕێکخستنی سیستەم">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
                <span class="hidden lg:inline">ڕێکخستن</span>
            </a>
            @endif
            <div class="h-8 w-px bg-slate-200"></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 text-slate-500 hover:text-red-500 transition-colors px-2 py-1.5 rounded-lg hover:bg-red-50 text-sm font-medium" title="چوونەدەرەوە">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/></svg>
                    <span class="hidden lg:inline">چوونەدەرەوە</span>
                </button>
            </form>
        </div>
    </header>

    <!-- ========== FLASH MESSAGES ========== -->
    @if(session('success') || session('error'))
    <div class="px-4 sm:px-6 pt-4">
        <div class="max-w-7xl mx-auto w-full space-y-2">
            @if(session('success'))
                <div class="animate-slide-in flex items-center gap-3 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm font-medium">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="animate-slide-in flex items-center gap-3 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm font-medium">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- ========== CONTENT ========== -->
    <main class="flex-1 overflow-y-auto px-4 sm:px-6 py-4">
        <div class="max-w-7xl mx-auto w-full">
            @yield('content')
        </div>
    </main>
</div>

@stack('scripts')

<script>
/* ==================== THEME SYSTEM ==================== */
const PALETTES = {
    green:  { name:'سووری (بنەڕەت)', 50:'#f0fdf4',100:'#dcfce7',200:'#bbf7d0',300:'#86efac',500:'#22c55e',600:'#16a34a',700:'#15803d',800:'#166534' },
    blue:   { name:'شین',           50:'#eff6ff',100:'#dbeafe',200:'#bfdbfe',300:'#93c5fd',500:'#3b82f6',600:'#2563eb',700:'#1d4ed8',800:'#1e40af' },
    purple: { name:'مۆر',           50:'#faf5ff',100:'#f3e8ff',200:'#e9d5ff',300:'#d8b4fe',500:'#a855f7',600:'#9333ea',700:'#7e22ce',800:'#6b21a8' },
    teal:   { name:'فیرۆزەیی',      50:'#f0fdfa',100:'#ccfbf1',200:'#99f6e4',300:'#5eead4',500:'#14b8a6',600:'#0d9488',700:'#0f766e',800:'#115e59' },
    orange: { name:'نارنجی',        50:'#fff7ed',100:'#ffedd5',200:'#fed7aa',300:'#fdba74',500:'#f97316',600:'#ea580c',700:'#c2410c',800:'#9a3412' },
    rose:   { name:'گوڵناری',       50:'#fff1f2',100:'#ffe4e6',200:'#fecdd3',300:'#fda4af',500:'#f43f5e',600:'#e11d48',700:'#be123c',800:'#9f1239' },
};

function buildThemeCSS(p) {
    if (!p) return '';
    return `
        .bg-green-50  { background-color: ${p[50]}  !important; }
        .bg-green-100 { background-color: ${p[100]} !important; }
        .bg-green-500 { background-color: ${p[500]} !important; }
        .bg-green-600 { background-color: ${p[600]} !important; }
        .bg-green-700 { background-color: ${p[700]} !important; }
        .bg-green-800 { background-color: ${p[800]} !important; }
        .hover\\:bg-green-50:hover  { background-color: ${p[50]}  !important; }
        .hover\\:bg-green-100:hover { background-color: ${p[100]} !important; }
        .hover\\:bg-green-200:hover { background-color: ${p[200]} !important; }
        .hover\\:bg-green-700:hover { background-color: ${p[700]} !important; }
        .active\\:bg-green-200:active { background-color: ${p[200]} !important; }
        .text-green-600 { color: ${p[600]} !important; }
        .text-green-700 { color: ${p[700]} !important; }
        .text-green-800 { color: ${p[800]} !important; }
        .border-green-100 { border-color: ${p[100]} !important; }
        .border-green-200 { border-color: ${p[200]} !important; }
        .border-green-300 { border-color: ${p[300]} !important; }
        .border-green-500 { border-color: ${p[500]} !important; }
        .hover\\:border-green-300:hover { border-color: ${p[300]} !important; }
        .focus\\:border-green-500:focus { border-color: ${p[500]} !important; }
        .focus\\:ring-green-500\\/20:focus { --tw-ring-color: ${p[500]}33 !important; }
        .ring-green-500 { --tw-ring-color: ${p[500]} !important; }
        .from-green-500 { --tw-gradient-from: ${p[500]} var(--tw-gradient-from-position) !important; }
        .from-green-600 { --tw-gradient-from: ${p[600]} var(--tw-gradient-from-position) !important; }
        .from-green-700 { --tw-gradient-from: ${p[700]} var(--tw-gradient-from-position) !important; }
        .to-green-600   { --tw-gradient-to: ${p[600]} var(--tw-gradient-to-position) !important; }
        .to-green-700   { --tw-gradient-to: ${p[700]} var(--tw-gradient-to-position) !important; }
        .to-emerald-600 { --tw-gradient-to: ${p[600]} var(--tw-gradient-to-position) !important; }
        .from-emerald-600 { --tw-gradient-from: ${p[600]} var(--tw-gradient-from-position) !important; }
        .from-emerald-700 { --tw-gradient-from: ${p[700]} var(--tw-gradient-from-position) !important; }
        .badge-green { background-color: ${p[100]} !important; color: ${p[700]} !important; }
        .badge-sale  { background-color: ${p[100]} !important; color: ${p[700]} !important; }
    `;
}

function applyTheme(name) {
    const p = PALETTES[name] || PALETTES.green;
    const el = document.getElementById('jwani-theme-override');
    if (el) el.textContent = (name === 'green') ? '' : buildThemeCSS(p);
    document.documentElement.setAttribute('data-theme', name);
    localStorage.setItem('jwani_theme', name);
}

/* ==================== FONT SIZE ==================== */
const FONT_STEPS = [12, 13, 14, 15, 16, 17, 18, 19, 20];
let fontIdx = 4;

function applyFontSize(idx) {
    idx = Math.max(0, Math.min(FONT_STEPS.length - 1, idx));
    fontIdx = idx;
    document.documentElement.style.fontSize = FONT_STEPS[idx] + 'px';
    localStorage.setItem('jwani_font', idx);
}

function changeFontSize(delta) {
    applyFontSize(fontIdx + delta);
}

/* ==================== INIT ==================== */
(function() {
    const savedTheme = localStorage.getItem('jwani_theme') || 'green';
    applyTheme(savedTheme);
    const savedFont = parseInt(localStorage.getItem('jwani_font') || '4', 10);
    applyFontSize(savedFont);
})();
</script>
</body>
</html>
