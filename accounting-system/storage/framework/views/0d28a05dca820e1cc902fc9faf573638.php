<?php $__env->startSection('title', 'کڕینی نوێ'); ?>
<?php $__env->startSection('page-title', 'کڕینی مەواد بە وەسڵ'); ?>
<?php $__env->startSection('page-subtitle', 'تۆمارکردنی وەسڵی کڕین لە دابینکەر'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">وەسڵی کڕینی نوێ</h2>
    <a href="<?php echo e(route('purchase-invoices.index')); ?>" class="btn-outline">گەڕانەوە</a>
</div>

<?php echo $__env->make('purchase-invoices._form', [
    'action'      => route('purchase-invoices.store'),
    'method'      => 'POST',
    'submitLabel' => 'تۆمارکردنی کڕین',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/workspace/accounting-system/resources/views/purchase-invoices/create.blade.php ENDPATH**/ ?>