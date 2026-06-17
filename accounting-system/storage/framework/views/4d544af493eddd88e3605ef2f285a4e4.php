<?php $__env->startSection('title', $supplier->name); ?>
<?php $__env->startSection('page-title', $supplier->name); ?>
<?php $__env->startSection('page-subtitle', 'کشف حسابی دابینکەر'); ?>

<?php $__env->startSection('content'); ?>
<?php $num = fn($v) => number_format((float) $v, 0); ?>

<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800"><?php echo e($supplier->name); ?></h2>
    <div class="flex items-center gap-2">
        <a href="<?php echo e(route('suppliers.pay', $supplier)); ?>" class="btn-primary">پارەدان</a>
        <a href="<?php echo e(route('suppliers.edit', $supplier)); ?>" class="btn-warning">دەستکاری</a>
        <a href="<?php echo e(route('suppliers.index')); ?>" class="btn-outline">گەڕانەوە</a>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
    <div class="stat-card"><div class="text-xs text-slate-400">مۆبایل</div><div class="font-semibold text-slate-800"><?php echo e($supplier->phone ?: '—'); ?></div></div>
    <div class="stat-card"><div class="text-xs text-slate-400">دۆخ</div><div class="font-semibold text-slate-800"><?php echo e($supplier->is_active ? 'چالاک' : 'ناچالاک'); ?></div></div>
    <div class="stat-card border-2 <?php echo e((float)$supplier->balance > 0 ? 'border-red-200' : 'border-green-200'); ?>">
        <div class="text-xs text-slate-400">باڵانسی ئێستا (قەرز)</div>
        <div class="text-xl font-extrabold <?php echo e((float)$supplier->balance > 0 ? 'text-red-600' : 'text-green-700'); ?>"><?php echo e($num($supplier->balance)); ?></div>
    </div>
</div>

<?php if($supplier->notes): ?>
    <div class="card p-4 mb-4 text-sm text-slate-600"><?php echo e($supplier->notes); ?></div>
<?php endif; ?>

<div class="mb-2 text-sm font-bold text-slate-700">مامەڵەکان (کشف حساب)</div>
<div class="card p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">بەروار</th>
                    <th class="px-4 py-3 font-semibold">جۆر</th>
                    <th class="px-4 py-3 font-semibold">وەسف</th>
                    <th class="px-4 py-3 font-semibold">بڕ</th>
                    <th class="px-4 py-3 font-semibold">باڵانس دوای مامەڵە</th>
                    <th class="px-4 py-3 font-semibold">بەکارهێنەر</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="table-row">
                        <td class="px-4 py-3 text-slate-500"><?php echo e(optional($t->date)->format('Y-m-d')); ?></td>
                        <td class="px-4 py-3">
                            <span class="<?php echo e($t->type == 'purchase' ? 'badge-purchase' : 'badge-green'); ?>"><?php echo e($t->type_name); ?></span>
                        </td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($t->description ?: '—'); ?></td>
                        <td class="px-4 py-3 font-semibold <?php echo e($t->type == 'purchase' ? 'text-red-600' : 'text-green-700'); ?>">
                            <?php echo e($t->type == 'purchase' ? '+' : '−'); ?><?php echo e($num($t->amount)); ?>

                        </td>
                        <td class="px-4 py-3 font-semibold text-slate-800"><?php echo e($num($t->balance_after)); ?></td>
                        <td class="px-4 py-3 text-slate-500"><?php echo e($t->user->name ?? '—'); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="px-4 py-10 text-center text-slate-400">هیچ مامەڵەیەک نییە.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4"><?php echo e($transactions->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/workspace/accounting-system/resources/views/suppliers/show.blade.php ENDPATH**/ ?>