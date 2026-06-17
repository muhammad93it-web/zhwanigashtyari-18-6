<?php $__env->startSection('title', 'وەرگرتنی پارە'); ?>
<?php $__env->startSection('page-title', 'وەرگرتنی پارە'); ?>
<?php $__env->startSection('page-subtitle', 'لیستی هەموو داهاتەکان'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $iqd = fn($v) => number_format((float) $v, 0);
?>

<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">داهاتەکان</h2>
    <a href="<?php echo e(route('incomes.create')); ?>" class="btn-primary">+ زیادکردنی داهات</a>
</div>


<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
    <div class="stat-card">
        <span class="text-xs text-slate-500 font-medium">کۆی گشتی (دینار)</span>
        <div class="text-2xl font-extrabold text-green-600"><?php echo e($iqd($totals->iqd ?? 0)); ?> د</div>
    </div>
    <div class="stat-card">
        <span class="text-xs text-slate-500 font-medium">کۆی گشتی (دۆلار)</span>
        <div class="text-2xl font-extrabold text-cyan-600">$<?php echo e(number_format((float)($totals->usd ?? 0), 2)); ?></div>
    </div>
    <div class="stat-card">
        <span class="text-xs text-slate-500 font-medium">ژمارەی تۆمارەکان</span>
        <div class="text-2xl font-extrabold text-slate-800"><?php echo e($totals->c ?? 0); ?></div>
    </div>
</div>


<div class="card p-4 mb-4">
    <form method="GET" action="<?php echo e(route('incomes.index')); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
        <div>
            <label class="label">گەڕان</label>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="سەرچاوە، وەسف، ژمارەی بەڵگە" class="input-field">
        </div>
        <div>
            <label class="label">لە بەرواری</label>
            <input type="date" name="from_date" value="<?php echo e(request('from_date')); ?>" class="input-field">
        </div>
        <div>
            <label class="label">تا بەرواری</label>
            <input type="date" name="to_date" value="<?php echo e(request('to_date')); ?>" class="input-field">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn-info flex-1">گەڕان</button>
            <a href="<?php echo e(route('incomes.index')); ?>" class="btn-outline">پاککردنەوە</a>
        </div>
    </form>
</div>


<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">سەرچاوە</th>
                    <th class="px-4 py-3 font-semibold">جۆر</th>
                    <th class="px-4 py-3 font-semibold">بڕ</th>
                    <th class="px-4 py-3 font-semibold">دینار</th>
                    <th class="px-4 py-3 font-semibold">بەروار</th>
                    <th class="px-4 py-3 font-semibold">کردارەکان</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $incomes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $income): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="table-row">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-800"><?php echo e($income->source); ?></div>
                            <?php if($income->description): ?>
                                <div class="text-xs text-slate-400"><?php echo e($income->description); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($income->category ?? '—'); ?></td>
                        <td class="px-4 py-3 font-medium text-slate-700">
                            <?php if($income->currency === 'USD'): ?>
                                $<?php echo e(number_format((float)$income->amount, 2)); ?>

                            <?php else: ?>
                                <?php echo e($iqd($income->amount)); ?> د
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 font-bold text-green-600"><?php echo e($iqd($income->amount_iqd)); ?> د</td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($income->income_date->format('Y-m-d')); ?></td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                <a href="<?php echo e(route('incomes.show', $income)); ?>" class="btn-info !px-2.5 !py-1.5">بینین</a>
                                <a href="<?php echo e(route('incomes.edit', $income)); ?>" class="btn-warning !px-2.5 !py-1.5">دەستکاری</a>
                                <form method="POST" action="<?php echo e(route('incomes.destroy', $income)); ?>" onsubmit="return confirm('دڵنیایت لە سڕینەوە؟')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn-danger !px-2.5 !py-1.5">سڕینەوە</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-slate-400">هیچ داهاتێک نییە.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    <?php echo e($incomes->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/workspace/accounting-system/resources/views/incomes/index.blade.php ENDPATH**/ ?>