<?php $__env->startSection('title', 'مێژووی کڕینەکان'); ?>
<?php $__env->startSection('page-title', 'مێژووی کڕینەکان'); ?>
<?php $__env->startSection('page-subtitle', 'وەسڵەکانی کڕینی خۆت'); ?>

<?php $__env->startSection('content'); ?>
<?php $num = fn($v) => number_format((float) $v, 0); ?>

<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">وەسڵەکانی کڕین</h2>
    <a href="<?php echo e(route('purchase-invoices.create')); ?>" class="btn-primary">+ کڕینی نوێ</a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
    <div class="stat-card"><div class="text-xs text-slate-400">کۆی کڕین</div><div class="text-lg font-extrabold text-slate-800"><?php echo e($num($totals->total ?? 0)); ?></div></div>
    <div class="stat-card"><div class="text-xs text-slate-400">دراوە</div><div class="text-lg font-extrabold text-green-700"><?php echo e($num($totals->paid ?? 0)); ?></div></div>
    <div class="stat-card"><div class="text-xs text-slate-400">ماوە (قەرز)</div><div class="text-lg font-extrabold text-red-600"><?php echo e($num($totals->remaining ?? 0)); ?></div></div>
</div>

<form method="GET" action="<?php echo e(route('purchase-invoices.index')); ?>" class="card p-4 mb-4 grid grid-cols-1 sm:grid-cols-4 gap-3">
    <div>
        <label class="label">دابینکەر</label>
        <select name="supplier_id" class="input-field">
            <option value="">هەموو</option>
            <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($s->id); ?>" <?php if(request('supplier_id')==$s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div>
        <label class="label">لە بەرواری</label>
        <input type="date" name="from_date" value="<?php echo e(request('from_date')); ?>" class="input-field">
    </div>
    <div>
        <label class="label">تا بەرواری</label>
        <input type="date" name="to_date" value="<?php echo e(request('to_date')); ?>" class="input-field">
    </div>
    <div class="flex items-end gap-2">
        <button type="submit" class="btn-info">گەڕان</button>
        <a href="<?php echo e(route('purchase-invoices.index')); ?>" class="btn-outline">سڕینەوە</a>
    </div>
</form>

<div class="card p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">#</th>
                    <th class="px-4 py-3 font-semibold">دابینکەر</th>
                    <th class="px-4 py-3 font-semibold">بەروار</th>
                    <th class="px-4 py-3 font-semibold">کۆ</th>
                    <th class="px-4 py-3 font-semibold">دراوە</th>
                    <th class="px-4 py-3 font-semibold">ماوە</th>
                    <th class="px-4 py-3 font-semibold">کردارەکان</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="table-row">
                        <td class="px-4 py-3 font-semibold text-slate-800">#<?php echo e($inv->id); ?></td>
                        <td class="px-4 py-3 text-slate-700"><?php echo e($inv->supplier->name ?? '—'); ?></td>
                        <td class="px-4 py-3 text-slate-500"><?php echo e(optional($inv->date)->format('Y-m-d')); ?></td>
                        <td class="px-4 py-3 font-semibold text-slate-800"><?php echo e($num($inv->total_amount)); ?></td>
                        <td class="px-4 py-3 text-green-700"><?php echo e($num($inv->paid_amount)); ?></td>
                        <td class="px-4 py-3 <?php echo e((float)$inv->remaining_amount > 0 ? 'text-red-600 font-semibold' : 'text-slate-500'); ?>"><?php echo e($num($inv->remaining_amount)); ?></td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                <a href="<?php echo e(route('purchase-invoices.show', $inv)); ?>" class="btn-info !px-3 !py-1.5">بینین</a>
                                <form method="POST" action="<?php echo e(route('purchase-invoices.destroy', $inv)); ?>" onsubmit="return confirm('سڕینەوەی وەسڵ کۆگا و باڵانس ڕاستدەکاتەوە. دڵنیایت؟')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn-danger !px-3 !py-1.5">سڕینەوە</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="px-4 py-10 text-center text-slate-400">هیچ کڕینێک نییە.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4"><?php echo e($invoices->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/workspace/accounting-system/resources/views/purchase-invoices/index.blade.php ENDPATH**/ ?>