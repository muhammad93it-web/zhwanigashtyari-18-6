<?php $__env->startSection('title', 'زیادکردنی داهات'); ?>
<?php $__env->startSection('page-title', 'زیادکردنی داهات'); ?>
<?php $__env->startSection('page-subtitle', 'تۆمارکردنی وەرگرتنی پارەی نوێ'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">داهاتی نوێ</h2>
    <a href="<?php echo e(route('incomes.index')); ?>" class="btn-outline">گەڕانەوە</a>
</div>

<div class="card p-5 max-w-3xl">
    <form method="POST" action="<?php echo e(route('incomes.store')); ?>" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <?php echo csrf_field(); ?>

        <div class="sm:col-span-2">
            <label class="label">سەرچاوە <span class="text-red-500">*</span></label>
            <input type="text" name="source" value="<?php echo e(old('source')); ?>" class="input-field" required>
            <?php $__errorArgs = ['source'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="label">جۆر</label>
            <input type="text" name="category" value="<?php echo e(old('category')); ?>" class="input-field">
            <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="label">بەرواری داهات <span class="text-red-500">*</span></label>
            <input type="date" name="income_date" value="<?php echo e(old('income_date', date('Y-m-d'))); ?>" class="input-field" required>
            <?php $__errorArgs = ['income_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="label">دراو</label>
            <select name="currency" class="input-field">
                <option value="IQD" <?php echo e(old('currency') === 'IQD' ? 'selected' : ''); ?>>دینار (IQD)</option>
                <option value="USD" <?php echo e(old('currency') === 'USD' ? 'selected' : ''); ?>>دۆلار (USD)</option>
            </select>
            <?php $__errorArgs = ['currency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="label">بڕ <span class="text-red-500">*</span></label>
            <input type="number" step="0.01" name="amount" value="<?php echo e(old('amount')); ?>" class="input-field" required>
            <p class="text-xs text-slate-400 mt-1">ڕێژەی ئێستا: <?php echo e(number_format($currentRate, 0)); ?> دینار بۆ ١ دۆلار</p>
            <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="sm:col-span-2">
            <label class="label">وەسف</label>
            <textarea name="description" rows="2" class="input-field"><?php echo e(old('description')); ?></textarea>
            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="sm:col-span-2">
            <label class="label">تێبینی</label>
            <textarea name="notes" rows="3" class="input-field"><?php echo e(old('notes')); ?></textarea>
            <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="sm:col-span-2 flex gap-2">
            <button type="submit" class="btn-primary">پاشەکەوتکردن</button>
            <a href="<?php echo e(route('incomes.index')); ?>" class="btn-outline">پاشگەزبوونەوە</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/workspace/accounting-system/resources/views/incomes/create.blade.php ENDPATH**/ ?>