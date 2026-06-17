<?php $__env->startSection('title', 'داشبۆرد'); ?>
<?php $__env->startSection('page-title', 'سەرەکی'); ?>
<?php $__env->startSection('page-subtitle', 'هەڵبژاردنی بەشەکان'); ?>

<?php $__env->startSection('content'); ?>
<?php
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
                 'icon' => '<path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zM9 14V10.414L7.707 11.707a1 1 0 01-1.414-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 10.414V14a1 1 0 11-2 0z" clip-rule="evenodd"/>'],
                ['label' => 'قەرزەکان', 'url' => route('debts.index'), 'show' => true,
                 'icon' => '<path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/><path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>'],
            ],
        ],
        [
            'title' => 'کڕین و فرۆشتن و کۆگا',
            'show'  => $u->hasAccess('trading'),
            'accent' => 'from-emerald-600 to-teal-600',
            'icon' => '<path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3z"/>',
            'items' => [
                ['label' => 'کڕینی مەواد', 'url' => route('materials.buy'), 'show' => true,
                 'icon' => '<path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3z"/>'],
                ['label' => 'فرۆشتنی مەواد', 'url' => route('materials.sell'), 'show' => true,
                 'icon' => '<path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4z" clip-rule="evenodd"/>'],
                ['label' => 'کۆگا (مەوادەکان)', 'url' => route('materials.index'), 'show' => true,
                 'icon' => '<path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/><path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>'],
            ],
        ],
        [
            'title' => 'پڕۆژە و کڕینی بیناسازی',
            'show'  => $u->hasAccess('projects') || $u->hasAccess('suppliers'),
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
            ],
        ],
    ];
?>


<div class="card overflow-hidden mb-5 bg-gradient-to-l from-green-600 to-emerald-600 text-white">
    <div class="px-5 py-4 sm:py-5">
        <div class="text-lg sm:text-2xl font-extrabold leading-tight">بەخێربێیتەوە، <?php echo e($u->name); ?></div>
        <div class="text-xs sm:text-sm text-white/85 mt-1">سیستەمی ژمێریاری ژوانی گەشتیاری — بەشێک هەڵبژێرە بۆ دەستپێکردن</div>
    </div>
</div>


<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5">
    <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(! $group['show']) continue; ?>
        <?php $visibleItems = array_values(array_filter($group['items'], fn ($i) => $i['show'])); ?>
        <?php if(count($visibleItems) === 0) continue; ?>
        <div class="card overflow-hidden flex flex-col">
            <div class="px-4 py-3 bg-gradient-to-l <?php echo e($group['accent']); ?> text-white flex items-center gap-2.5">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><?php echo $group['icon']; ?></svg>
                <span class="font-bold text-sm"><?php echo e($group['title']); ?></span>
                <span class="ms-auto text-[11px] font-semibold bg-white/20 rounded-full px-2 py-0.5"><?php echo e(count($visibleItems)); ?></span>
            </div>
            <div class="p-3 space-y-2">
                <?php $__currentLoopData = $visibleItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($item['url']); ?>"
                       class="flex items-center gap-3 px-3 py-3 rounded-xl border border-slate-200 bg-white hover:bg-green-50 hover:border-green-300 active:scale-[0.98] transition-all duration-150">
                        <span class="w-9 h-9 rounded-lg bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><?php echo $item['icon']; ?></svg>
                        </span>
                        <span class="text-sm font-semibold text-slate-700"><?php echo e($item['label']); ?></span>
                        <svg class="w-4 h-4 text-slate-300 ms-auto flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/workspace/accounting-system/resources/views/dashboard/index.blade.php ENDPATH**/ ?>