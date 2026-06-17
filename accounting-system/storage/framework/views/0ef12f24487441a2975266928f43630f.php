<?php $__env->startSection('title', 'داشبۆرد'); ?>
<?php $__env->startSection('page-title', 'داشبۆرد'); ?>
<?php $__env->startSection('page-subtitle', 'پوختەی گشتیی سیستەم'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $iqd = fn($v) => number_format((float) $v, 0);
?>


<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-5">
    <a href="<?php echo e(route('incomes.create')); ?>" class="card p-4 flex flex-col items-center gap-2 hover:shadow-md hover:border-green-300 transition-all text-center">
        <span class="w-11 h-11 rounded-xl bg-green-100 text-green-600 flex items-center justify-center">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
        </span>
        <span class="text-xs font-semibold text-slate-700">وەرگرتنی پارە</span>
    </a>
    <a href="<?php echo e(route('expenses.create')); ?>" class="card p-4 flex flex-col items-center gap-2 hover:shadow-md hover:border-red-300 transition-all text-center">
        <span class="w-11 h-11 rounded-xl bg-red-100 text-red-500 flex items-center justify-center">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M4 10a.75.75 0 01.75-.75h10.5a.75.75 0 010 1.5H4.75A.75.75 0 014 10z"/></svg>
        </span>
        <span class="text-xs font-semibold text-slate-700">خەرجکردنی پارە</span>
    </a>
    <a href="<?php echo e(route('materials.buy')); ?>" class="card p-4 flex flex-col items-center gap-2 hover:shadow-md hover:border-amber-300 transition-all text-center">
        <span class="w-11 h-11 rounded-xl bg-amber-100 text-amber-500 flex items-center justify-center">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3z"/></svg>
        </span>
        <span class="text-xs font-semibold text-slate-700">کڕینی مەواد</span>
    </a>
    <a href="<?php echo e(route('materials.sell')); ?>" class="card p-4 flex flex-col items-center gap-2 hover:shadow-md hover:border-cyan-300 transition-all text-center">
        <span class="w-11 h-11 rounded-xl bg-cyan-100 text-cyan-600 flex items-center justify-center">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4z" clip-rule="evenodd"/></svg>
        </span>
        <span class="text-xs font-semibold text-slate-700">فرۆشتنی مەواد</span>
    </a>
    <a href="<?php echo e(route('contractor-payments.create')); ?>" class="card p-4 flex flex-col items-center gap-2 hover:shadow-md hover:border-green-300 transition-all text-center">
        <span class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
        </span>
        <span class="text-xs font-semibold text-slate-700">پارەی وەستا</span>
    </a>
    <a href="<?php echo e(route('documents.create')); ?>" class="card p-4 flex flex-col items-center gap-2 hover:shadow-md hover:border-slate-300 transition-all text-center">
        <span class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
        </span>
        <span class="text-xs font-semibold text-slate-700">نووسراو نوێ</span>
    </a>
</div>


<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <span class="text-xs text-slate-500 font-medium">داهاتی ئەم مانگە</span>
            <span class="badge-green">+</span>
        </div>
        <div class="text-2xl font-extrabold text-green-600"><?php echo e($iqd($month['in_iqd'])); ?></div>
        <div class="text-[11px] text-slate-400">دینار (وەرگرتن + فرۆشتن)</div>
    </div>
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <span class="text-xs text-slate-500 font-medium">خەرجی ئەم مانگە</span>
            <span class="badge-red">-</span>
        </div>
        <div class="text-2xl font-extrabold text-red-500"><?php echo e($iqd($month['out_iqd'])); ?></div>
        <div class="text-[11px] text-slate-400">دینار (خەرجی + کڕین + وەستا)</div>
    </div>
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <span class="text-xs text-slate-500 font-medium">قازانجی ساف</span>
            <span class="<?php echo e($month['net_iqd'] >= 0 ? 'badge-green' : 'badge-red'); ?>">=</span>
        </div>
        <div class="text-2xl font-extrabold <?php echo e($month['net_iqd'] >= 0 ? 'text-green-600' : 'text-red-500'); ?>"><?php echo e($iqd($month['net_iqd'])); ?></div>
        <div class="text-[11px] text-slate-400">دینار</div>
    </div>
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <span class="text-xs text-slate-500 font-medium">ڕێژەی دۆلار</span>
            <span class="badge-cyan">$</span>
        </div>
        <div class="text-2xl font-extrabold text-cyan-600"><?php echo e($currentRate ? $iqd($currentRate->usd_to_iqd) : '—'); ?></div>
        <div class="text-[11px] text-slate-400">١ دۆلار = ؟ دینار</div>
    </div>
</div>


<div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
    <div class="card p-5">
        <h3 class="text-sm font-bold text-slate-800 mb-3">قەرزەکان</h3>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 rounded-lg bg-green-50">
                <span class="text-sm text-slate-600">قەرزی لای خەڵک (بۆ ئێمە)</span>
                <span class="font-bold text-green-600"><?php echo e($iqd($debts['receivable_iqd'])); ?> د</span>
            </div>
            <div class="flex items-center justify-between p-3 rounded-lg bg-red-50">
                <span class="text-sm text-slate-600">قەرزی ئێمە (لەسەر ئێمە)</span>
                <span class="font-bold text-red-500"><?php echo e($iqd($debts['payable_iqd'])); ?> د</span>
            </div>
        </div>
        <a href="<?php echo e(route('debts.index')); ?>" class="btn-outline w-full mt-3">بینینی هەموو قەرزەکان</a>
    </div>

    <div class="card p-5 lg:col-span-2">
        <h3 class="text-sm font-bold text-slate-800 mb-3">کورتەی سیستەم</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <a href="<?php echo e(route('materials.index')); ?>" class="p-4 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors">
                <div class="text-2xl font-extrabold text-slate-800"><?php echo e($counts['materials']); ?></div>
                <div class="text-xs text-slate-500">مەواد لە کۆگا</div>
            </a>
            <a href="<?php echo e(route('materials.index')); ?>" class="p-4 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors">
                <div class="text-2xl font-extrabold <?php echo e($counts['low_stock'] > 0 ? 'text-amber-500' : 'text-slate-800'); ?>"><?php echo e($counts['low_stock']); ?></div>
                <div class="text-xs text-slate-500">کۆگای کەم</div>
            </a>
            <a href="<?php echo e(route('contractors.index')); ?>" class="p-4 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors">
                <div class="text-2xl font-extrabold text-slate-800"><?php echo e($counts['contractors']); ?></div>
                <div class="text-xs text-slate-500">وەستا</div>
            </a>
            <a href="<?php echo e(route('clients.index')); ?>" class="p-4 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors">
                <div class="text-2xl font-extrabold text-slate-800"><?php echo e($counts['clients']); ?></div>
                <div class="text-xs text-slate-500">کڕیار</div>
            </a>
            <a href="<?php echo e(route('documents.index')); ?>" class="p-4 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors">
                <div class="text-2xl font-extrabold text-slate-800"><?php echo e($counts['documents']); ?></div>
                <div class="text-xs text-slate-500">نووسراو</div>
            </a>
            <a href="<?php echo e(route('reports.daily')); ?>" class="p-4 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors flex items-center justify-center text-center">
                <span class="text-sm font-semibold text-green-600">ڕاپۆرتی ڕۆژانە ←</span>
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/workspace/accounting-system/resources/views/dashboard/index.blade.php ENDPATH**/ ?>