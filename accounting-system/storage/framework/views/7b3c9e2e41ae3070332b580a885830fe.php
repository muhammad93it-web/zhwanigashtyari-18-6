<?php $__env->startSection('title', 'دەستکاری مەواد'); ?>
<?php $__env->startSection('page-title', 'دەستکاری مەواد'); ?>
<?php $__env->startSection('page-subtitle', $material->name); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl">
    <div class="card p-5">
        <form method="POST" action="<?php echo e(route('materials.update', $material)); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div>
                <label class="label" for="name">ناوی مەواد <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="<?php echo e(old('name', $material->name)); ?>" class="input-field" required autofocus>
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <p class="text-xs text-slate-400 mt-1">کۆگای ئێستا: <?php echo e(number_format((float) $material->current_stock, 0)); ?> <?php echo e($material->unit); ?> — تەنها لە ڕێگەی کڕین/فرۆشتنەوە دەگۆڕێت.</p>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <button type="submit" class="btn-primary">نوێکردنەوە</button>
                <a href="<?php echo e(route('materials.show', $material)); ?>" class="btn-outline">گەڕانەوە</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/workspace/accounting-system/resources/views/materials/edit.blade.php ENDPATH**/ ?>