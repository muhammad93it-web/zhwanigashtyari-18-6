<?php $__env->startSection('title', 'مەواد نوێ'); ?>
<?php $__env->startSection('page-title', 'مەواد نوێ'); ?>
<?php $__env->startSection('page-subtitle', 'زیادکردنی مەوادی نوێ بۆ کۆگا'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl">
    <div class="card p-5">
        <form method="POST" action="<?php echo e(route('materials.store')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>

            <div>
                <label class="label" for="name">ناوی مەواد <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="<?php echo e(old('name')); ?>" class="input-field" required>
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label" for="unit">یەکە <span class="text-red-500">*</span></label>
                    <input type="text" id="unit" name="unit" value="<?php echo e(old('unit')); ?>" placeholder="مەتر / دانە / کیلۆ" class="input-field" required>
                    <?php $__errorArgs = ['unit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="label" for="category">جۆر</label>
                    <input type="text" id="category" name="category" value="<?php echo e(old('category')); ?>" class="input-field">
                    <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label" for="current_stock">کۆگای ئێستا</label>
                    <input type="number" step="0.001" id="current_stock" name="current_stock" value="<?php echo e(old('current_stock', 0)); ?>" class="input-field">
                    <?php $__errorArgs = ['current_stock'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="label" for="min_stock">کەمترین کۆگا</label>
                    <input type="number" step="0.001" id="min_stock" name="min_stock" value="<?php echo e(old('min_stock')); ?>" class="input-field">
                    <?php $__errorArgs = ['min_stock'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div>
                <label class="label" for="notes">تێبینی</label>
                <textarea id="notes" name="notes" rows="3" class="input-field"><?php echo e(old('notes')); ?></textarea>
                <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" id="is_active" name="is_active" value="1" <?php echo e(old('is_active', true) ? 'checked' : ''); ?> class="w-4 h-4 rounded border-slate-300 text-green-600 focus:ring-green-500">
                <label for="is_active" class="text-sm font-semibold text-slate-700">چالاک</label>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <button type="submit" class="btn-primary">پاشەکەوتکردن</button>
                <a href="<?php echo e(route('materials.index')); ?>" class="btn-outline">گەڕانەوە</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/workspace/accounting-system/resources/views/materials/create.blade.php ENDPATH**/ ?>