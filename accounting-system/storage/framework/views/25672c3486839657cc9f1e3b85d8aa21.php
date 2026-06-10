<?php $__env->startSection('title', 'دەستکاری بەکارهێنەر'); ?>
<?php $__env->startSection('page-title', 'دەستکاریکردنی بەکارهێنەر'); ?>
<?php $__env->startSection('page-subtitle', $user->name); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-2xl animate-fade-in space-y-4">
    <div class="card p-6">
        <?php if($errors->any()): ?>
        <div class="mb-5 p-4 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm space-y-1">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div>• <?php echo e($error); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('users.update', $user)); ?>" class="space-y-5">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="label">ناوی بەکارهێنەر <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="<?php echo e(old('name', $user->name)); ?>" required class="input-field">
                </div>
                <div>
                    <label class="label">ئیمەیڵ (بۆ چوونەژوورەوە) <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" required class="input-field" dir="ltr">
                </div>
            </div>

            <div class="border border-slate-200 rounded-lg p-4 bg-slate-50/60">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_admin" value="1" id="isAdmin" onchange="togglePerms()" <?php echo e(old('is_admin', $user->is_admin) ? 'checked' : ''); ?> class="w-5 h-5 rounded border-slate-300 text-green-600 focus:ring-green-500">
                    <span class="text-sm font-semibold text-slate-800">بەڕێوەبەری گشتی (دەسەڵاتی تەواو بۆ هەموو بەشەکان)</span>
                </label>
            </div>

            <div id="permsBox">
                <label class="label">دەسەڵاتەکان</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer">
                        <input type="checkbox" name="permissions[]" value="<?php echo e($key); ?>" <?php echo e(in_array($key, old('permissions', $user->permissions ?? [])) ? 'checked' : ''); ?> class="w-5 h-5 rounded border-slate-300 text-green-600 focus:ring-green-500">
                        <span class="text-sm text-slate-700"><?php echo e($label); ?></span>
                    </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary">پاشەکەوتکردن</button>
                <a href="<?php echo e(route('users.index')); ?>" class="btn-outline">پاشگەزبوونەوە</a>
                <a href="<?php echo e(route('users.password.edit', $user)); ?>" class="btn-warning mr-auto">گۆڕینی وشەی نهێنی</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function togglePerms() {
        const isAdmin = document.getElementById('isAdmin').checked;
        document.getElementById('permsBox').style.display = isAdmin ? 'none' : 'block';
    }
    togglePerms();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/workspace/accounting-system/resources/views/admin/users/edit.blade.php ENDPATH**/ ?>