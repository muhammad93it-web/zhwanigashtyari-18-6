<!DOCTYPE html>
<html lang="ku" dir="rtl" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'سیستەمی ژمێریاری') — ژوانی گەشتیاری</title>

    <!-- Google Fonts: Noto Kufi Arabic -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        kufi: ['"Noto Kufi Arabic"', 'sans-serif'],
                    },
                    colors: {
                        teal: {
                            950: '#07181f',
                            900: '#0d2530',
                            800: '#143843',
                            700: '#1f4f5e',
                            600: '#2a7184',
                            500: '#3a96a8',
                            400: '#5fb6c6',
                            300: '#92d2dd',
                        },
                        navy: {
                            950: '#0a1320',
                            900: '#0f1f35',
                            800: '#163050',
                            700: '#1f4470',
                            600: '#2b5d94',
                        },
                        gold: {
                            300: '#f3d68a',
                            400: '#eec24f',
                            500: '#e0a82e',
                            600: '#c08e20',
                        },
                    },
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
        body { background: linear-gradient(160deg, #07181f 0%, #0d2530 55%, #0a1320 100%); background-attachment: fixed; min-height: 100vh; }
        .sidebar-link { @apply flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-teal-800/60 hover:text-gold-400 transition-all duration-200 text-sm font-medium; }
        .sidebar-link.active { @apply bg-teal-800/80 text-gold-400 shadow-lg; }
        .card { @apply bg-teal-900/50 backdrop-blur border border-teal-700/30 rounded-2xl; }
        .btn-primary { @apply inline-flex items-center justify-center gap-2 bg-gradient-to-l from-teal-600 to-navy-600 hover:from-teal-500 hover:to-navy-500 text-white px-5 py-2.5 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-teal-500/25 text-sm; }
        .btn-gold { @apply inline-flex items-center justify-center gap-2 bg-gradient-to-l from-gold-500 to-gold-600 hover:from-gold-400 hover:to-gold-500 text-navy-950 px-5 py-2.5 rounded-xl font-bold transition-all duration-200 shadow-lg text-sm; }
        .btn-danger { @apply inline-flex items-center justify-center gap-2 bg-red-600/80 hover:bg-red-500 text-white px-4 py-2 rounded-xl font-semibold transition-all duration-200 text-sm; }
        .btn-outline { @apply inline-flex items-center justify-center gap-2 border border-teal-600/60 hover:border-gold-400 text-teal-300 hover:text-gold-400 px-4 py-2 rounded-xl font-medium transition-all duration-200 text-sm; }
        .input-field { @apply w-full bg-teal-950/60 border border-teal-700/50 rounded-xl px-4 py-2.5 text-white placeholder-teal-500 focus:outline-none focus:border-gold-400/70 focus:ring-1 focus:ring-gold-400/30 transition-all duration-200 text-sm; }
        .label { @apply block text-sm font-semibold text-teal-300 mb-1.5; }
        .badge-sale { @apply inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30; }
        .badge-purchase { @apply inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30; }
        .badge-debit { @apply inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-gold-500/20 text-gold-300 border border-gold-500/30; }
        .badge-credit { @apply inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-sky-500/20 text-sky-300 border border-sky-500/30; }
        .table-row { @apply border-b border-teal-800/40 hover:bg-teal-800/20 transition-colors; }
        .stat-card { @apply card p-5 flex flex-col gap-2; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #07181f; }
        ::-webkit-scrollbar-thumb { background: #1f4f5e; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #3a96a8; }
        .gold-text { background: linear-gradient(90deg, #f3d68a, #e0a82e, #eec24f); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    </style>

    @stack('head')
</head>
<body class="dark h-full">

<!-- Mobile overlay -->
<div id="sidebarOverlay" onclick="closeSidebar()" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-30 hidden lg:hidden"></div>

<div class="flex h-screen overflow-hidden">

    <!-- ========== SIDEBAR ========== -->
    <aside id="sidebar" class="fixed lg:static inset-y-0 right-0 z-40 w-72 sm:w-64 flex-shrink-0 flex flex-col bg-teal-950/95 lg:bg-teal-950/80 backdrop-blur border-l border-teal-700/30 overflow-y-auto transform translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
        <!-- Logo -->
        <div class="p-5 border-b border-teal-700/30 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center shadow-lg shadow-gold-500/30">
                    <svg class="w-5 h-5 text-navy-950" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xs text-teal-400 leading-tight">سیستەمی ژمێریاری</div>
                    <div class="text-sm font-bold text-white leading-tight">ژوانی گەشتیاری</div>
                </div>
            </div>
            <!-- Close button (mobile only) -->
            <button onclick="closeSidebar()" class="lg:hidden text-teal-400 hover:text-gold-400 p-1" aria-label="داخستن">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                داشبۆرد
            </a>

            <div class="pt-3 pb-1 px-4 text-xs text-teal-600 font-semibold uppercase tracking-wider">کڕیاران</div>

            <a href="{{ route('clients.index') }}" class="sidebar-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/></svg>
                کەسەکان (کڕیاران)
            </a>

            <a href="{{ route('clients.create') }}" class="sidebar-link {{ request()->routeIs('clients.create') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
                زیادکردنی کڕیار
            </a>

            <div class="pt-3 pb-1 px-4 text-xs text-teal-600 font-semibold uppercase tracking-wider">مامەڵەکان</div>

            <a href="{{ route('transactions.index') }}" class="sidebar-link {{ request()->routeIs('transactions.index') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
                لیستی مامەڵەکان
            </a>

            <a href="{{ route('transactions.create') }}" class="sidebar-link {{ request()->routeIs('transactions.create') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
                تۆمارکردنی مامەڵە
            </a>

            <div class="pt-3 pb-1 px-4 text-xs text-teal-600 font-semibold uppercase tracking-wider">دارایی</div>

            <a href="{{ route('exchange-rates.index') }}" class="sidebar-link {{ request()->routeIs('exchange-rates.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M8 5a1 1 0 100 2h5.586l-1.293 1.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L13.586 5H8zM12 15a1 1 0 100-2H6.414l1.293-1.293a1 1 0 10-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L6.414 15H12z"/></svg>
                ڕێژەی گۆڕینی دراو
            </a>

            <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm2 10a1 1 0 10-2 0v3a1 1 0 102 0v-3zm2-3a1 1 0 011 1v5a1 1 0 11-2 0v-5a1 1 0 011-1zm4-1a1 1 0 10-2 0v7a1 1 0 102 0V8z" clip-rule="evenodd"/></svg>
                ڕاپۆرتەکان
            </a>
        </nav>

        <!-- User -->
        <div class="p-3 border-t border-teal-700/30">
            <div class="flex items-center gap-3 px-3 py-2 rounded-xl bg-teal-800/30">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-teal-500 to-navy-600 flex items-center justify-center text-white text-xs font-bold">
                    {{ mb_substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-xs font-semibold text-white truncate">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-teal-400">بەڕێوەبەر</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-teal-500 hover:text-red-400 transition-colors" title="چوونەدەرەوە">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- ========== MAIN CONTENT ========== -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Top bar -->
        <header class="flex-shrink-0 h-14 bg-teal-950/60 backdrop-blur border-b border-teal-700/30 flex items-center justify-between px-4 sm:px-6">
            <div class="flex items-center gap-3 min-w-0">
                <!-- Hamburger (mobile only) -->
                <button onclick="openSidebar()" class="lg:hidden text-teal-300 hover:text-gold-400 p-1 -mr-1" aria-label="پێڕست">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="min-w-0">
                    <h1 class="text-sm font-bold text-white truncate">@yield('page-title', 'داشبۆرد')</h1>
                    <div class="text-xs text-teal-500 truncate">@yield('page-subtitle', '')</div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="hidden sm:block text-xs text-teal-400 font-medium">
                    {{ now()->locale('ku')->isoFormat('dddd، D MMMM YYYY') }}
                </div>
            </div>
        </header>

        <!-- Flash messages -->
        <div class="px-4 sm:px-6 pt-4 space-y-2">
            @if(session('success'))
                <div class="animate-slide-in flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 text-sm font-medium">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="animate-slide-in flex items-center gap-3 px-4 py-3 rounded-xl bg-red-500/15 border border-red-500/30 text-red-400 text-sm font-medium">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <!-- Page content -->
        <main class="flex-1 overflow-y-auto px-4 sm:px-6 py-4">
            @yield('content')
        </main>
    </div>
</div>

<script>
    function openSidebar() {
        document.getElementById('sidebar').classList.remove('translate-x-full');
        document.getElementById('sidebarOverlay').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.add('translate-x-full');
        document.getElementById('sidebarOverlay').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
    // Close drawer when a nav link is tapped (mobile)
    document.querySelectorAll('#sidebar nav a').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth < 1024) closeSidebar();
        });
    });
    // Reset state when crossing the desktop breakpoint
    window.addEventListener('resize', function () {
        if (window.innerWidth >= 1024) {
            document.getElementById('sidebarOverlay').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    });
</script>
@stack('scripts')
</body>
</html>
