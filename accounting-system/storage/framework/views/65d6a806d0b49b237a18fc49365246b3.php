<?php $__env->startSection('title', 'وەسڵی کڕین #' . $purchaseInvoice->id); ?>
<?php $__env->startSection('page-title', 'وەسڵی کڕین #' . $purchaseInvoice->id); ?>
<?php $__env->startSection('page-subtitle', $purchaseInvoice->party_name); ?>

<?php $__env->startSection('content'); ?>
<?php
    $num = fn($v) => number_format((float) $v, 0);
    $inv = $purchaseInvoice;
?>

<div class="flex items-center justify-between mb-4 flex-wrap gap-2">
    <h2 class="text-base font-bold text-slate-800">وردەکاری وەسڵ</h2>
    <div class="flex items-center gap-2 flex-wrap">
        <a href="<?php echo e(route('purchase-invoices.print', $inv)); ?>" target="_blank" class="btn-info !px-3 !py-1.5">چاپ (A4)</a>
        <a href="<?php echo e(route('purchase-invoices.export-excel', $inv)); ?>" class="btn-primary !px-3 !py-1.5">Excel</a>
        <a href="<?php echo e(route('purchase-invoices.export-word', $inv)); ?>" class="btn-primary !px-3 !py-1.5">Word</a>
        <a href="<?php echo e(route('purchase-invoices.edit', $inv)); ?>" class="btn-warning !px-3 !py-1.5">دەستکاری</a>
        <a href="<?php echo e(route('purchase-invoices.index')); ?>" class="btn-outline !px-3 !py-1.5">گەڕانەوە</a>
    </div>
</div>

<div class="card p-5 mb-4 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 text-sm">
    <div><div class="text-slate-400 text-xs">دابینکەر / گەیەنەر</div><div class="font-semibold text-slate-800"><?php echo e($inv->party_name); ?></div></div>
    <div><div class="text-slate-400 text-xs">پڕۆژە</div><div class="font-semibold text-slate-800"><?php echo e($inv->project->name ?? '—'); ?></div></div>
    <div><div class="text-slate-400 text-xs">بەروار</div><div class="font-semibold text-slate-800"><?php echo e(optional($inv->date)->format('Y-m-d')); ?></div></div>
    <div><div class="text-slate-400 text-xs">تۆمارکار</div><div class="font-semibold text-slate-800"><?php echo e($inv->user->name ?? '—'); ?></div></div>
    <?php if($inv->deliverer_phone): ?><div><div class="text-slate-400 text-xs">مۆبایلی گەیەنەر</div><div class="font-semibold text-slate-800"><?php echo e($inv->deliverer_phone); ?></div></div><?php endif; ?>
    <?php if($inv->deliverer_address): ?><div><div class="text-slate-400 text-xs">ناونیشان</div><div class="font-semibold text-slate-800"><?php echo e($inv->deliverer_address); ?></div></div><?php endif; ?>
    <?php if($inv->vehicle_number): ?><div><div class="text-slate-400 text-xs">ژمارەی ئۆتۆمبێل</div><div class="font-semibold text-slate-800"><?php echo e($inv->vehicle_number); ?></div></div><?php endif; ?>
    <?php if($inv->vehicle_type): ?><div><div class="text-slate-400 text-xs">جۆری ئۆتۆمبێل</div><div class="font-semibold text-slate-800"><?php echo e($inv->vehicle_type); ?></div></div><?php endif; ?>
</div>

<div class="card p-0 mb-4">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">مەواد / جۆر</th>
                    <th class="px-4 py-3 font-semibold">بڕ</th>
                    <th class="px-4 py-3 font-semibold">نرخی یەکە</th>
                    <th class="px-4 py-3 font-semibold">دراو</th>
                    <th class="px-4 py-3 font-semibold">کۆی هێڵ</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $inv->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="table-row">
                        <td class="px-4 py-3 font-semibold text-slate-800"><?php echo e($d->material->name ?? $d->custom_type); ?></td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e(rtrim(rtrim(number_format((float)$d->quantity,3),'0'),'.')); ?> <?php echo e($d->unit); ?></td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($num($d->unit_price)); ?></td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($d->currency === 'USD' ? '$' : 'د.ع'); ?></td>
                        <td class="px-4 py-3 font-semibold text-slate-800"><?php echo e($num($d->line_total)); ?> <?php echo e($d->currency === 'USD' ? '$' : 'د.ع'); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="card p-4">
        <div class="font-bold text-sm text-slate-700 mb-2">دیناری عێراقی (د.ع)</div>
        <div class="flex justify-between py-1 text-sm"><span class="text-slate-500">کۆی گشتی</span><span class="font-bold text-slate-800"><?php echo e($num($inv->total_iqd)); ?></span></div>
        <div class="flex justify-between py-1 text-sm"><span class="text-slate-500">دراوە</span><span class="font-bold text-green-700"><?php echo e($num($inv->paid_iqd)); ?></span></div>
        <div class="flex justify-between py-1 text-sm border-t border-slate-100 mt-1 pt-2"><span class="text-slate-500">ماوە</span><span class="font-bold <?php echo e((float)$inv->remaining_iqd > 0 ? 'text-red-600' : 'text-green-700'); ?>"><?php echo e($num($inv->remaining_iqd)); ?></span></div>
    </div>
    <div class="card p-4">
        <div class="font-bold text-sm text-slate-700 mb-2">دۆلاری ئەمریکی ($)</div>
        <div class="flex justify-between py-1 text-sm"><span class="text-slate-500">کۆی گشتی</span><span class="font-bold text-slate-800"><?php echo e($num($inv->total_usd)); ?></span></div>
        <div class="flex justify-between py-1 text-sm"><span class="text-slate-500">دراوە</span><span class="font-bold text-green-700"><?php echo e($num($inv->paid_usd)); ?></span></div>
        <div class="flex justify-between py-1 text-sm border-t border-slate-100 mt-1 pt-2"><span class="text-slate-500">ماوە</span><span class="font-bold <?php echo e((float)$inv->remaining_usd > 0 ? 'text-red-600' : 'text-green-700'); ?>"><?php echo e($num($inv->remaining_usd)); ?></span></div>
    </div>
</div>

<?php if($inv->notes): ?>
    <div class="card p-4 text-sm text-slate-600 mt-4"><span class="text-slate-400">تێبینی: </span><?php echo e($inv->notes); ?></div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/workspace/accounting-system/resources/views/purchase-invoices/show.blade.php ENDPATH**/ ?>