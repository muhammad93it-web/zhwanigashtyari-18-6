<?php $__env->startSection('title', 'کۆگا (مەوادەکان)'); ?>
<?php $__env->startSection('page-title', 'کۆگا (مەوادەکان)'); ?>
<?php $__env->startSection('page-subtitle', 'بەڕێوەبردنی مەوادەکان و کۆگا'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $iqd = fn($v) => number_format((float) $v, 0);
?>


<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
    <div class="flex flex-wrap items-center gap-2">
        <a href="<?php echo e(route('materials.create')); ?>" class="btn-primary">+ مەواد نوێ</a>
        <a href="<?php echo e(route('materials.buy')); ?>" class="btn-warning">کڕین</a>
        <a href="<?php echo e(route('materials.sell')); ?>" class="btn-info">فرۆشتن</a>
    </div>
    <form method="GET" action="<?php echo e(route('materials.index')); ?>" class="flex items-center gap-2">
        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="گەڕان بەناو یان جۆر..." class="input-field w-48 sm:w-64">
        <button type="submit" class="btn-slate">گەڕان</button>
    </form>
</div>


<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <span class="text-xs text-slate-500 font-medium">کۆی مەوادەکان</span>
            <span class="badge-slate">#</span>
        </div>
        <div class="text-2xl font-extrabold text-slate-800"><?php echo e($totals['count']); ?></div>
    </div>
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <span class="text-xs text-slate-500 font-medium">کۆگای کەم</span>
            <span class="<?php echo e($totals['low_stock'] > 0 ? 'badge-amber' : 'badge-green'); ?>">!</span>
        </div>
        <div class="text-2xl font-extrabold <?php echo e($totals['low_stock'] > 0 ? 'text-amber-500' : 'text-slate-800'); ?>"><?php echo e($totals['low_stock']); ?></div>
    </div>
</div>


<div class="card p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">ناو</th>
                    <th class="px-4 py-3 font-semibold">جۆر</th>
                    <th class="px-4 py-3 font-semibold">کۆگا</th>
                    <th class="px-4 py-3 font-semibold">کەمترین</th>
                    <th class="px-4 py-3 font-semibold">دۆخ</th>
                    <th class="px-4 py-3 font-semibold">کردارەکان</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="table-row">
                        <td class="px-4 py-3 font-semibold text-slate-800">
                            <?php echo e($material->name); ?>

                            <?php if($material->is_low_stock): ?>
                                <span class="badge-amber mr-1">کۆگای کەم</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($material->category ?: '—'); ?></td>
                        <td class="px-4 py-3 text-slate-800 font-medium"><?php echo e($iqd($material->current_stock)); ?> <?php echo e($material->unit); ?></td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($material->min_stock !== null ? $iqd($material->min_stock) . ' ' . $material->unit : '—'); ?></td>
                        <td class="px-4 py-3">
                            <?php if($material->is_active): ?>
                                <span class="badge-green">چالاک</span>
                            <?php else: ?>
                                <span class="badge-slate">ناچالاک</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="<?php echo e(route('materials.show', $material)); ?>" class="btn-info !px-3 !py-1.5">بینین</a>
                                <a href="<?php echo e(route('materials.edit', $material)); ?>" class="btn-warning !px-3 !py-1.5">دەستکاری</a>
                                <form method="POST" action="<?php echo e(route('materials.destroy', $material)); ?>" onsubmit="return confirm('دڵنیایت لە سڕینەوە؟')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn-danger !px-3 !py-1.5">سڕینەوە</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-slate-400">هیچ مەوادێک نییە.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    <?php echo e($materials->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/workspace/accounting-system/resources/views/materials/index.blade.php ENDPATH**/ ?>