<?php $__env->startSection('title', 'دابینکەری نوێ'); ?>
<?php $__env->startSection('page-title', 'دابینکەری نوێ'); ?>
<?php $__env->startSection('page-subtitle', 'زیادکردنی دابینکەر'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-slate-800">زانیاری دابینکەر</h2>
        <a href="<?php echo e(route('suppliers.index')); ?>" class="btn-outline">گەڕانەوە</a>
    </div>

    <form method="POST" action="<?php echo e(route('suppliers.store')); ?>" class="card p-5 space-y-4">
        <?php echo csrf_field(); ?>
        <?php echo $__env->make('suppliers._form', ['supplier' => null], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div class="flex items-center gap-2 pt-2">
            <button type="submit" class="btn-primary">پاشەکەوتکردن</button>
            <a href="<?php echo e(route('suppliers.index')); ?>" class="btn-outline">پاشگەزبوونەوە</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/workspace/accounting-system/resources/views/suppliers/create.blade.php ENDPATH**/ ?>