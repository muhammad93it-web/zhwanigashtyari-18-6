<div>
    <label class="label">ناو *</label>
    <input type="text" name="name" value="<?php echo e(old('name', $supplier->name ?? '')); ?>" class="input-field" required>
    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>

<div>
    <label class="label">مۆبایل</label>
    <input type="text" name="phone" value="<?php echo e(old('phone', $supplier->phone ?? '')); ?>" class="input-field">
    <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>

<div>
    <label class="label">تێبینی</label>
    <textarea name="notes" rows="3" class="input-field"><?php echo e(old('notes', $supplier->notes ?? '')); ?></textarea>
    <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>

<label class="flex items-center gap-2 text-sm text-slate-700">
    <input type="checkbox" name="is_active" value="1" <?php if(old('is_active', $supplier->is_active ?? true)): echo 'checked'; endif; ?> class="w-4 h-4 rounded border-slate-300 text-green-600 focus:ring-green-500">
    چالاکە
</label>
<?php /**PATH /home/runner/workspace/accounting-system/resources/views/suppliers/_form.blade.php ENDPATH**/ ?>