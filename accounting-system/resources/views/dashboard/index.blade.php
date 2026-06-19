@extends('layouts.app')

@section('title', 'داشبۆرد')
@section('page-title', 'سەرەکی')
@section('page-subtitle', 'هەڵبژاردنی بەشەکان')

@section('content')
@php
    $u = Auth::user();

    $groups = [
        [
            'title' => 'دارایی',
            'show'  => $u->hasAccess('finance'),
            'accent' => 'from-green-600 to-emerald-600',
            'icon' => '<path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/><path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>',
            'items' => [
                ['label' => 'وەرگرتنی پارە', 'url' => route('incomes.index'), 'show' => true,
                 'icon' => '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v3.586L7.707 8.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 9.586V6z" clip-rule="evenodd"/>'],
                ['label' => 'خەرجکردنی پارە', 'url' => route('expenses.index'), 'show' => true,
                 'icon' => '<path fill-rule="evenodd" d="M10 2a8 8 0 100 16A8 8 0 0010 2zM9 14V10.414L7.707 11.707a1 1 0 01-1.414-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 10.414V14a1 1 0 11-2 0z" clip-rule="evenodd"/>'],
                ['label' => 'قەرزەکان', 'url' => route('debts.index'), 'show' => true,
                 'icon' => '<path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/><path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>'],
            ],
        ],
        [
            'title' => 'پڕۆژە و کڕینی بیناسازی',
            'show'  => $u->hasAccess('projects') || $u->hasAccess('suppliers') || $u->hasAccess('trading'),
            'accent' => 'from-emerald-700 to-green-700',
            'icon' => '<path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>',
            'items' => [
                ['label' => 'پڕۆژەکان', 'url' => route('projects.index'), 'show' => $u->hasAccess('projects'),
                 'icon' => '<path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>'],
                ['label' => 'دابینکەران', 'url' => route('suppliers.index'), 'show' => $u->hasAccess('suppliers'),
                 'icon' => '<path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>'],
                ['label' => 'کڕینی مەواد بە وەسڵ', 'url' => route('purchase-invoices.create'), 'show' => $u->hasAccess('suppliers'),
                 'icon' => '<path fill-rule="evenodd" d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm2.5 3a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm6.207.293a1 1 0 00-1.414 0l-6 6a1 1 0 101.414 1.414l6-6a1 1 0 000-1.414zM12.5 10a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" clip-rule="evenodd"/>'],
                ['label' => 'مێژووی کڕینەکان', 'url' => route('purchase-invoices.index'), 'show' => $u->hasAccess('suppliers'),
                 'icon' => '<path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>'],
                ['label' => 'کەشف حساب', 'url' => route('suppliers.statements'), 'show' => $u->hasAccess('suppliers'),
                 'icon' => '<path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>'],
                ['label' => 'کۆگا (مەوادەکان)', 'url' => route('materials.index'), 'show' => $u->hasAccess('trading'),
                 'icon' => '<path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/><path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>'],
            ],
        ],
        [
            'title' => 'گواستنەوە و شۆفێر',
            'show'  => $u->hasAccess('drivers'),
            'accent' => 'from-amber-600 to-orange-700',
            'icon' => '<path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/><path d="M3 4a1 1 0 00-1 1v8a1 1 0 001 1h.05a2.5 2.5 0 014.9 0h4.1a2.5 2.5 0 014.9 0H17a1 1 0 001-1v-2.382a1 1 0 00-.105-.447l-1.5-3A1 1 0 0015.5 6H13V5a1 1 0 00-1-1H3z"/>',
            'items' => [
                ['label' => 'تۆماری گواستنەوە', 'url' => route('driver-trip-logs.create'), 'show' => $u->hasAccess('drivers'),
                 'icon' => '<path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/><path d="M3 4a1 1 0 00-1 1v8a1 1 0 001 1h.05a2.5 2.5 0 014.9 0h4.1a2.5 2.5 0 014.9 0H17a1 1 0 001-1v-2.382a1 1 0 00-.105-.447l-1.5-3A1 1 0 0015.5 6H13V5a1 1 0 00-1-1H3z"/>'],
                ['label' => 'مێژووی گواستنەوەکان', 'url' => route('driver-trip-logs.index'), 'show' => $u->hasAccess('drivers'),
                 'icon' => '<path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>'],
                ['label' => 'شۆفێرەکان', 'url' => route('drivers.index'), 'show' => $u->hasAccess('drivers'),
                 'icon' => '<path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>'],
                ['label' => 'کەشف حساب', 'url' => route('drivers.statements'), 'show' => $u->hasAccess('drivers'),
                 'icon' => '<path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>'],
            ],
        ],
        [
            'title' => 'وەستا و بەڵێندەرایەتی',
            'show'  => $u->hasAccess('contractors'),
            'accent' => 'from-teal-600 to-teal-700',
            'icon' => '<path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>',
            'items' => [
                ['label' => 'وەستاکان', 'url' => route('contractors.index'), 'show' => true,
                 'icon' => '<path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>'],
                ['label' => 'پێدانی پارەی وەستا', 'url' => route('contractor-payments.index'), 'show' => true,
                 'icon' => '<path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4z"/><path d="M14 6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h8zm-4 7a3 3 0 100-6 3 3 0 000 6z"/>'],
            ],
        ],
        [
            'title' => 'کرێی کار و کرێکاران',
            'show'  => $u->hasAccess('labor'),
            'accent' => 'from-lime-600 to-green-700',
            'icon' => '<path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>',
            'items' => [
                ['label' => 'کرێکاران', 'url' => route('workers.index'), 'show' => true,
                 'icon' => '<path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>'],
                ['label' => 'کرێی کارەکان', 'url' => route('labor-payments.index'), 'show' => true,
                 'icon' => '<path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4z"/><path d="M14 6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h8zm-4 7a3 3 0 100-6 3 3 0 000 6z"/>'],
                ['label' => 'تۆمارکردنی کرێی کار', 'url' => route('labor-payments.create'), 'show' => true,
                 'icon' => '<path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>'],
            ],
        ],
        [
            'title' => 'ڕاپۆرتەکان',
            'show'  => $u->hasAccess('reports'),
            'accent' => 'from-slate-600 to-slate-700',
            'icon' => '<path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>',
            'items' => [
                ['label' => 'ڕاپۆرتی پێشکەوتوو', 'url' => route('reports.advanced'), 'show' => true,
                 'icon' => '<path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>'],
                ['label' => 'ڕاپۆرتی ڕۆژانە', 'url' => route('reports.daily'), 'show' => true,
                 'icon' => '<path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>'],
                ['label' => 'کۆی هەموو بەشەکان', 'url' => route('reports.summary'), 'show' => true,
                 'icon' => '<path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>'],
                ['label' => 'تێچووی گشتیی پڕۆژە', 'url' => route('reports.project-cost'), 'show' => true,
                 'icon' => '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 011 1v.092a4.535 4.535 0 011.676.662C14.398 9.235 15 10.009 15 11c0 .99-.602 1.765-1.324 2.246A4.535 4.535 0 0111 13.908V14a1 1 0 11-2 0v-.092a4.535 4.535 0 01-1.676-.662C6.602 12.765 6 11.991 6 11h2c0 .342.234.74.851 1.011.6.265 1.293.265 1.893 0C11.36 11.738 12 11.342 12 11s-.234-.74-.851-1.011A4.535 4.535 0 0110 9.908V6a1 1 0 011-1z" clip-rule="evenodd"/>'],
            ],
        ],
        [
            'title' => 'کارگێڕی',
            'show'  => $u->hasAccess('documents') || $u->hasAccess('print_center'),
            'accent' => 'from-green-700 to-green-800',
            'icon' => '<path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>',
            'items' => [
                ['label' => 'نووسراوەکان', 'url' => route('documents.index'), 'show' => $u->hasAccess('documents'),
                 'icon' => '<path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>'],
                ['label' => 'چاپکردنی بەشەکان', 'url' => route('print-center.index'), 'show' => $u->hasAccess('print_center'),
                 'icon' => '<path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a1 1 0 001 1h8a1 1 0 001-1v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a1 1 0 00-1-1H6a1 1 0 00-1 1zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"/>'],
            ],
        ],
        [
            'title' => 'ڕێکخستن',
            'show'  => $u->hasAccess('clients') || $u->hasAccess('transactions') || $u->hasAccess('exchange_rates') || $u->is_admin,
            'accent' => 'from-slate-700 to-slate-800',
            'icon' => '<path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>',
            'items' => [
                ['label' => 'کڕیاران و کەسەکان', 'url' => route('clients.index'), 'show' => $u->hasAccess('clients'),
                 'icon' => '<path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>'],
                ['label' => 'مامەڵە گشتییەکان', 'url' => route('transactions.index'), 'show' => $u->hasAccess('transactions'),
                 'icon' => '<path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>'],
                ['label' => 'ڕێژەی گۆڕینی دراو', 'url' => route('exchange-rates.index'), 'show' => $u->hasAccess('exchange_rates'),
                 'icon' => '<path d="M8 5a1 1 0 100 2h5.586l-1.293 1.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L13.586 5H8zM12 15a1 1 0 100-2H6.414l1.293-1.293a1 1 0 10-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L6.414 15H12z"/>'],
                ['label' => 'بەڕێوەبردنی بەکارهێنەران', 'url' => route('users.index'), 'show' => $u->is_admin,
                 'icon' => '<path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>'],
                ['label' => 'ڕێکخستنی سیستەم', 'url' => route('settings.index'), 'show' => $u->is_admin,
                 'icon' => '<path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>'],
            ],
        ],
    ];

    $items = [];
    foreach ($groups as $g) {
        if (! $g['show']) continue;
        foreach ($g['items'] as $it) {
            if ($it['show']) $items[] = $it;
        }
    }

    $logoPath = public_path('images/logo.png');
    $logoSrc = is_file($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';
@endphp

{{-- ناونیشانی ناوەڕاست: لۆگۆ + هێڵ + ناوی سیستەم --}}
<div class="card px-5 py-6 sm:py-7 mb-6">
    <div class="flex items-center justify-center gap-4 sm:gap-6">
        @if ($logoSrc)
            <img src="{{ $logoSrc }}" alt="ژوانی گەشتیاری" class="w-16 h-16 sm:w-20 sm:h-20 object-contain flex-shrink-0">
        @endif
        <div class="w-px h-14 sm:h-16 bg-slate-200 flex-shrink-0"></div>
        <h1 class="text-xl sm:text-3xl font-extrabold text-green-700 leading-snug text-center">سیستەمی ژمێریاری ژوانی گەشتیاری</h1>
    </div>
</div>

{{-- ڕیزبەندی کارتەکان --}}
<div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-2.5 sm:gap-3">
    @foreach ($items as $item)
        <a href="{{ $item['url'] }}"
           class="group bg-white rounded-xl border border-slate-200 p-3 flex flex-col items-center text-center hover:shadow-md hover:border-green-300 hover:-translate-y-0.5 transition-all duration-150">
            <span class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 text-white flex items-center justify-center shadow-sm mb-2 group-hover:scale-105 transition-transform duration-150">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 20 20">{!! $item['icon'] !!}</svg>
            </span>
            <span class="text-[11px] sm:text-xs font-bold text-slate-700 leading-tight">{{ $item['label'] }}</span>
        </a>
    @endforeach
</div>
@endsection
