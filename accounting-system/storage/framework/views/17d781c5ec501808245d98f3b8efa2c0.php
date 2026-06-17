<?php $__env->startSection('title', 'کرێکار: ' . $worker->name); ?>
<?php $__env->startSection('page-title', $worker->name); ?>
<?php $__env->startSection('page-subtitle', $worker->role ?? 'کرێکار'); ?>

<?php $__env->startSection('content'); ?>
<?php $num = fn($v) => number_format((float) $v, 0); ?>

<div class="flex items-center justify-between mb-4 flex-wrap gap-2">
    <h2 class="text-base font-bold text-slate-800">زانیاری کرێکار</h2>
    <div class="flex items-center gap-2">
        <a href="<?php echo e(route('labor-payments.create', ['worker_id' => $worker->id])); ?>" class="btn-primary !px-3 !py-1.5">+ کرێی کار</a>
        <a href="<?php echo e(route('workers.edit', $worker)); ?>" class="btn-warning !px-3 !py-1.5">دەستکاری</a>
        <a href="<?php echo e(route('workers.index')); ?>" class="btn-outline !px-3 !py-1.5">گەڕانەوە</a>
    </div>
</div>

<div class="card p-5 mb-4 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
    <div><div class="text-slate-400 text-xs">پیشە</div><div class="font-semibold text-slate-800"><?php echo e($worker->role ?? '—'); ?></div></div>
    <div><div class="text-slate-400 text-xs">مۆبایل</div><div class="font-semibold text-slate-800"><?php echo e($worker->phone ?? '—'); ?></div></div>
    <div><div class="text-slate-400 text-xs">کرێی کاتژمێر</div><div class="font-semibold text-slate-800"><?php echo e($worker->default_hourly_rate ? $num($worker->default_hourly_rate) . ' ' . ($worker->default_currency === 'USD' ? '$' : 'د.ع') : '—'); ?></div></div>
    <div><div class="text-slate-400 text-xs">دۆخ</div><div><?php if($worker->is_active): ?><span class="badge-green">چالاک</span><?php else: ?><span class="badge-slate">ناچالاک</span><?php endif; ?></div></div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
    <div class="stat-card"><div class="text-xs text-slate-400">کۆی کرێی دراو (د.ع)</div><div class="text-lg font-extrabold text-slate-800"><?php echo e($num($totals->iqd ?? 0)); ?></div></div>
    <div class="stat-card"><div class="text-xs text-slate-400">کۆی کرێی دراو ($)</div><div class="text-lg font-extrabold text-slate-800"><?php echo e($num($totals->usd ?? 0)); ?></div></div>
</div>

<div class="card p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">بەروار</th>
                    <th class="px-4 py-3 font-semibold">پڕۆژە</th>
                    <th class="px-4 py-3 font-semibold">جۆر</th>
                    <th class="px-4 py-3 font-semibold">کاتژمێر × کرێ</th>
                    <th class="px-4 py-3 font-semibold">بڕ</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="table-row">
                        <td class="px-4 py-3 text-slate-600"><?php echo e(optional($p->date)->format('Y-m-d')); ?></td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($p->project->name ?? '—'); ?></td>
                        <td class="px-4 py-3"><?php if($p->is_hourly): ?><span class="badge-cyan">کاتژمێری</span><?php else: ?><span class="badge-slate">جێگیر</span><?php endif; ?></td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($p->is_hourly ? rtrim(rtrim(number_format((float)$p->hours,2),'0'),'.') . ' × ' . $num($p->hourly_rate) : '—'); ?></td>
                        <td class="px-4 py-3 font-semibold text-slate-800"><?php echo e($num($p->amount)); ?> <?php echo e($p->currency === 'USD' ? '$' : 'د.ع'); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="px-4 py-10 text-center text-slate-400">هیچ پارەدانێک نییە.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4"><?php echo e($payments->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/workspace/accounting-system/resources/views/workers/show.blade.php ENDPATH**/ ?>