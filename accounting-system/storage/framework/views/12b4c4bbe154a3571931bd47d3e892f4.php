<!DOCTYPE html>
<html lang="ku" dir="rtl" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'سیستەمی ژمێریاری'); ?> — ژوانی گەشتیاری</title>

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
    </style>

    <style type="text/tailwindcss">
        @layer components {
            .sidebar-link { @apply flex items-center gap-3 px-4 py-2.5 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-green-700 transition-all duration-150 text-sm font-medium; }
            .sidebar-link.active { @apply bg-green-50 text-green-700 font-semibold; }
            .sidebar-group { @apply pt-4 pb-1 px-4 text-[11px] text-slate-400 font-bold uppercase tracking-wider; }
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

    <?php echo $__env->yieldPushContent('head'); ?>
</head>
<body class="h-full text-slate-800">

<div id="sidebarOverlay" onclick="closeSidebar()" class="fixed inset-0 bg-slate-900/40 z-30 hidden lg:hidden"></div>

<div class="flex h-screen overflow-hidden">

    <!-- ========== SIDEBAR ========== -->
    <aside id="sidebar" class="fixed lg:static inset-y-0 right-0 z-40 w-72 sm:w-64 flex-shrink-0 flex flex-col bg-white border-l border-slate-200 overflow-y-auto transform translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
        <div class="p-5 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-md shadow-green-500/30">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/></svg>
                </div>
                <div>
                    <div class="text-[11px] text-slate-400 leading-tight">سیستەمی ژمێریاری</div>
                    <div class="text-sm font-bold text-slate-800 leading-tight">ژوانی گەشتیاری</div>
                </div>
            </div>
            <button onclick="closeSidebar()" class="lg:hidden text-slate-400 hover:text-slate-700 p-1" aria-label="داخستن">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <nav class="flex-1 p-3 space-y-0.5">
            <a href="<?php echo e(route('dashboard')); ?>" class="sidebar-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                داشبۆرد
            </a>

            <?php if(Auth::user()->hasAccess('finance')): ?>
            <div class="sidebar-group">دارایی</div>
            <a href="<?php echo e(route('incomes.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('incomes.*') ? 'active' : ''); ?>">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v3.586L7.707 8.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 9.586V6z" clip-rule="evenodd"/></svg>
                وەرگرتنی پارە
            </a>
            <a href="<?php echo e(route('expenses.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('expenses.*') ? 'active' : ''); ?>">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zM9 14V10.414L7.707 11.707a1 1 0 01-1.414-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 10.414V14a1 1 0 11-2 0z" clip-rule="evenodd"/></svg>
                خەرجکردنی پارە
            </a>
            <a href="<?php echo e(route('debts.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('debts.*') ? 'active' : ''); ?>">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/><path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/></svg>
                قەرزەکان
            </a>
            <?php endif; ?>

            <?php if(Auth::user()->hasAccess('trading')): ?>
            <div class="sidebar-group">کڕین و فرۆشتن و کۆگا</div>
            <a href="<?php echo e(route('materials.buy')); ?>" class="sidebar-link <?php echo e(request()->routeIs('materials.buy') || request()->routeIs('movements.store') && request('type')=='purchase' ? 'active' : ''); ?>">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3z"/></svg>
                کڕینی مەواد
            </a>
            <a href="<?php echo e(route('materials.sell')); ?>" class="sidebar-link <?php echo e(request()->routeIs('materials.sell') ? 'active' : ''); ?>">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"/></svg>
                فرۆشتنی مەواد
            </a>
            <a href="<?php echo e(route('materials.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('materials.index') || request()->routeIs('materials.show') || request()->routeIs('materials.create') || request()->routeIs('materials.edit') ? 'active' : ''); ?>">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/><path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
                کۆگا (مەوادەکان)
            </a>
            <?php endif; ?>

            <?php if(Auth::user()->hasAccess('contractors')): ?>
            <div class="sidebar-group">وەستا و بەڵێندەرایەتی</div>
            <a href="<?php echo e(route('contractors.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('contractors.*') ? 'active' : ''); ?>">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
                وەستاکان
            </a>
            <a href="<?php echo e(route('contractor-payments.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('contractor-payments.*') ? 'active' : ''); ?>">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4z"/><path d="M14 6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h8zm-4 7a3 3 0 100-6 3 3 0 000 6z"/></svg>
                پێدانی پارەی وەستا
            </a>
            <?php endif; ?>

            <?php if(Auth::user()->hasAccess('reports')): ?>
            <div class="sidebar-group">ڕاپۆرتەکان</div>
            <a href="<?php echo e(route('reports.daily')); ?>" class="sidebar-link <?php echo e(request()->routeIs('reports.daily') ? 'active' : ''); ?>">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
                ڕاپۆرتی ڕۆژانە
            </a>
            <a href="<?php echo e(route('reports.summary')); ?>" class="sidebar-link <?php echo e(request()->routeIs('reports.summary') ? 'active' : ''); ?>">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/></svg>
                کۆی هەموو بەشەکان
            </a>
            <a href="<?php echo e(route('reports.project-cost')); ?>" class="sidebar-link <?php echo e(request()->routeIs('reports.project-cost') ? 'active' : ''); ?>">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 011 1v.092a4.535 4.535 0 011.676.662C14.398 9.235 15 10.009 15 11c0 .99-.602 1.765-1.324 2.246A4.535 4.535 0 0111 13.908V14a1 1 0 11-2 0v-.092a4.535 4.535 0 01-1.676-.662C6.602 12.765 6 11.991 6 11h2c0 .342.234.74.851 1.011.6.265 1.293.265 1.893 0C11.36 11.738 12 11.342 12 11s-.234-.74-.851-1.011A4.535 4.535 0 0110 9.908V6a1 1 0 011-1z" clip-rule="evenodd"/></svg>
                تێچووی گشتیی پڕۆژە
            </a>
            <?php endif; ?>

            <?php if(Auth::user()->hasAccess('documents') || Auth::user()->hasAccess('print_center')): ?>
            <div class="sidebar-group">کارگێڕی</div>
            <?php if(Auth::user()->hasAccess('documents')): ?>
            <a href="<?php echo e(route('documents.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('documents.*') ? 'active' : ''); ?>">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                نووسراوەکان
            </a>
            <?php endif; ?>
            <?php if(Auth::user()->hasAccess('print_center')): ?>
            <a href="<?php echo e(route('print-center.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('print-center.*') ? 'active' : ''); ?>">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a1 1 0 001 1h8a1 1 0 001-1v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a1 1 0 00-1-1H6a1 1 0 00-1 1zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"/></svg>
                چاپکردنی بەشەکان
            </a>
            <?php endif; ?>
            <?php endif; ?>

            <?php if(Auth::user()->hasAccess('clients') || Auth::user()->hasAccess('transactions') || Auth::user()->hasAccess('exchange_rates') || Auth::user()->is_admin): ?>
            <div class="sidebar-group">ڕێکخستن</div>
            <?php if(Auth::user()->hasAccess('clients')): ?>
            <a href="<?php echo e(route('clients.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('clients.*') ? 'active' : ''); ?>">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/></svg>
                کڕیاران و کەسەکان
            </a>
            <?php endif; ?>
            <?php if(Auth::user()->hasAccess('transactions')): ?>
            <a href="<?php echo e(route('transactions.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('transactions.*') ? 'active' : ''); ?>">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
                مامەڵە گشتییەکان
            </a>
            <?php endif; ?>
            <?php if(Auth::user()->hasAccess('exchange_rates')): ?>
            <a href="<?php echo e(route('exchange-rates.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('exchange-rates.*') ? 'active' : ''); ?>">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M8 5a1 1 0 100 2h5.586l-1.293 1.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L13.586 5H8zM12 15a1 1 0 100-2H6.414l1.293-1.293a1 1 0 10-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L6.414 15H12z"/></svg>
                ڕێژەی گۆڕینی دراو
            </a>
            <?php endif; ?>
            <?php if(Auth::user()->is_admin): ?>
            <a href="<?php echo e(route('users.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('users.*') ? 'active' : ''); ?>">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
                بەڕێوەبردنی بەکارهێنەران
            </a>
            <?php endif; ?>
            <?php endif; ?>
        </nav>

        <div class="p-3 border-t border-slate-200">
            <div class="flex items-center gap-3 px-3 py-2 rounded-lg bg-slate-50">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white text-xs font-bold">
                    <?php echo e(mb_substr(Auth::user()->name, 0, 1)); ?>

                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-xs font-semibold text-slate-800 truncate"><?php echo e(Auth::user()->name); ?></div>
                    <div class="text-[11px] text-slate-400"><?php echo e(Auth::user()->is_admin ? 'بەڕێوەبەر' : 'بەکارهێنەر'); ?></div>
                </div>
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors" title="چوونەدەرەوە">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- ========== MAIN CONTENT ========== -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="flex-shrink-0 h-14 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6">
            <div class="flex items-center gap-3 min-w-0">
                <button onclick="openSidebar()" class="lg:hidden text-slate-500 hover:text-green-600 p-1 -mr-1" aria-label="پێڕست">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="min-w-0">
                    <h1 class="text-sm font-bold text-slate-800 truncate"><?php echo $__env->yieldContent('page-title', 'داشبۆرد'); ?></h1>
                    <div class="text-xs text-slate-400 truncate"><?php echo $__env->yieldContent('page-subtitle', ''); ?></div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="hidden sm:block text-xs text-slate-500 font-medium">
                    <?php
                        $kWeekdays = ['یەکشەممە','دووشەممە','سێشەممە','چوارشەممە','پێنجشەممە','هەینی','شەممە'];
                        $kMonths = [1=>'کانوونی دووەم',2=>'شوبات',3=>'ئازار',4=>'نیسان',5=>'ئایار',6=>'حوزەیران',7=>'تەمموز',8=>'ئاب',9=>'ئەیلوول',10=>'تشرینی یەکەم',11=>'تشرینی دووەم',12=>'کانوونی یەکەم'];
                        $kNow = now();
                    ?>
                    <?php echo e($kWeekdays[$kNow->dayOfWeek]); ?>، <?php echo e($kNow->day); ?>ی <?php echo e($kMonths[$kNow->month]); ?> <?php echo e($kNow->year); ?>

                </div>
            </div>
        </header>

        <div class="px-4 sm:px-6 pt-4 space-y-2">
            <?php if(session('success')): ?>
                <div class="animate-slide-in flex items-center gap-3 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm font-medium">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="animate-slide-in flex items-center gap-3 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm font-medium">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>
        </div>

        <main class="flex-1 overflow-y-auto px-4 sm:px-6 py-4">
            <?php echo $__env->yieldContent('content'); ?>
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
    document.querySelectorAll('#sidebar nav a').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth < 1024) closeSidebar();
        });
    });
    window.addEventListener('resize', function () {
        if (window.innerWidth >= 1024) {
            document.getElementById('sidebarOverlay').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    });
</script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /home/runner/workspace/accounting-system/resources/views/layouts/app.blade.php ENDPATH**/ ?>