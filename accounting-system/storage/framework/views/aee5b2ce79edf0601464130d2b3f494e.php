<?php $__env->startSection('title', 'دابینکەران'); ?>
<?php $__env->startSection('page-title', 'دابینکەران'); ?>
<?php $__env->startSection('page-subtitle', 'لیستی دابینکەرەکان و باڵانس'); ?>

<?php $__env->startSection('content'); ?>
<?php $num = fn($v) => number_format((float) $v, 0); ?>

<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">لیستی دابینکەران</h2>
    <a href="<?php echo e(route('suppliers.create')); ?>" class="btn-primary">+ دابینکەری نوێ</a>
</div>

<div class="card p-4 mb-4 flex items-center justify-between text-sm">
    <span class="text-slate-500">کۆی قەرزی سەرمان بۆ دابینکەران</span>
    <span class="font-bold text-red-600 text-lg"><?php echo e($num($totalBalance)); ?></span>
</div>

<form method="GET" action="<?php echo e(route('suppliers.index')); ?>" class="card p-4 mb-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
    <div class="sm:col-span-2">
        <label class="label">گەڕان</label>
        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="ناو یان مۆبایل" class="input-field">
    </div>
    <div class="flex items-end gap-2">
        <button type="submit" class="btn-info">گەڕان</button>
        <a href="<?php echo e(route('suppliers.index')); ?>" class="btn-outline">سڕینەوە</a>
    </div>
</form>

<div class="card p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">ناو</th>
                    <th class="px-4 py-3 font-semibold">مۆبایل</th>
                    <th class="px-4 py-3 font-semibold">باڵانس (قەرز)</th>
                    <th class="px-4 py-3 font-semibold">دۆخ</th>
                    <th class="px-4 py-3 font-semibold">کردارەکان</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="table-row">
                        <td class="px-4 py-3 font-semibold text-slate-800"><?php echo e($supplier->name); ?></td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($supplier->phone ?: '—'); ?></td>
                        <td class="px-4 py-3">
                            <span class="font-bold <?php echo e((float)$supplier->balance > 0 ? 'text-red-600' : 'text-green-700'); ?>"><?php echo e($num($supplier->balance)); ?></span>
                        </td>
                        <td class="px-4 py-3">
                            <?php if($supplier->is_active): ?><span class="badge-green">چالاک</span><?php else: ?><span class="badge-slate">ناچالاک</span><?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                <a href="<?php echo e(route('suppliers.show', $supplier)); ?>" class="btn-info !px-3 !py-1.5">کشف حساب</a>
                                <a href="<?php echo e(route('suppliers.pay', $supplier)); ?>" class="btn-primary !px-3 !py-1.5">پارەدان</a>
                                <a href="<?php echo e(route('suppliers.edit', $supplier)); ?>" class="btn-warning !px-3 !py-1.5">دەستکاری</a>
                                <form method="POST" action="<?php echo e(route('suppliers.destroy', $supplier)); ?>" onsubmit="return confirm('دڵنیایت لە سڕینەوە؟')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn-danger !px-3 !py-1.5">سڕینەوە</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="px-4 py-10 text-center text-slate-400">هیچ دابینکەرێک نییە.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4"><?php echo e($suppliers->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/workspace/accounting-system/resources/views/suppliers/index.blade.php ENDPATH**/ ?>