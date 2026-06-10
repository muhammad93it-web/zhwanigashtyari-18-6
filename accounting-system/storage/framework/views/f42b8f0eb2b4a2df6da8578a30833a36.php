<?php $__env->startSection('title', 'گۆڕینی وشەی نهێنی'); ?>
<?php $__env->startSection('page-title', 'گۆڕینی وشەی نهێنی'); ?>
<?php $__env->startSection('page-subtitle', $user->name); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-lg animate-fade-in">
    <div class="card p-6">
        <?php if($errors->any()): ?>
        <div class="mb-5 p-4 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm space-y-1">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div>• <?php echo e($error); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>

        <div class="mb-5 flex items-center gap-3 p-3 rounded-lg bg-slate-50 border border-slate-200">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white text-sm font-bold"><?php echo e(mb_substr($user->name, 0, 1)); ?></div>
            <div>
                <div class="text-sm font-semibold text-slate-800"><?php echo e($user->name); ?></div>
                <div class="text-xs text-slate-400" dir="ltr" style="text-align:right;"><?php echo e($user->email); ?></div>
            </div>
        </div>

        <form method="POST" action="<?php echo e(route('users.password.update', $user)); ?>" class="space-y-5">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

            <div>
                <label class="label">وشەی نهێنی نوێ <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="password" id="password" name="password" required minlength="6" class="input-field pl-11" placeholder="لانیکەم ٦ پیت">
                    <button type="button" onclick="togglePw('password', this)" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-green-600" title="پیشاندان/شاردنەوە">
                        <svg class="w-5 h-5 eye-show" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg class="w-5 h-5 eye-hide hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
            </div>

            <div>
                <label class="label">دووبارەکردنەوەی وشەی نهێنی <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="password" id="password_confirmation" name="password_confirmation" required minlength="6" class="input-field pl-11" placeholder="هەمان وشەی نهێنی">
                    <button type="button" onclick="togglePw('password_confirmation', this)" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-green-600" title="پیشاندان/شاردنەوە">
                        <svg class="w-5 h-5 eye-show" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg class="w-5 h-5 eye-hide hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary">گۆڕینی وشەی نهێنی</button>
                <a href="<?php echo e(route('users.index')); ?>" class="btn-outline">پاشگەزبوونەوە</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function togglePw(id, btn) {
        const el = document.getElementById(id);
        const show = btn.querySelector('.eye-show');
        const hide = btn.querySelector('.eye-hide');
        if (el.type === 'password') { el.type = 'text'; show.classList.add('hidden'); hide.classList.remove('hidden'); }
        else { el.type = 'password'; hide.classList.add('hidden'); show.classList.remove('hidden'); }
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/workspace/accounting-system/resources/views/admin/users/password.blade.php ENDPATH**/ ?>