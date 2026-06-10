<?php $__env->startSection('title', 'زیادکردنی بەکارهێنەر'); ?>
<?php $__env->startSection('page-title', 'زیادکردنی بەکارهێنەری نوێ'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-2xl animate-fade-in">
    <div class="card p-6">
        <?php if($errors->any()): ?>
        <div class="mb-5 p-4 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm space-y-1">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div>• <?php echo e($error); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('users.store')); ?>" class="space-y-5">
            <?php echo csrf_field(); ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="label">ناوی بەکارهێنەر <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="<?php echo e(old('name')); ?>" required class="input-field" placeholder="ناوی تەواو...">
                </div>
                <div>
                    <label class="label">ئیمەیڵ (بۆ چوونەژوورەوە) <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" required class="input-field" dir="ltr" placeholder="user@jwani.com">
                </div>
            </div>

            <div>
                <label class="label">وشەی نهێنی <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="password" id="password" name="password" required minlength="6" class="input-field pl-11" placeholder="لانیکەم ٦ پیت">
                    <button type="button" onclick="togglePw('password', this)" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-green-600" title="پیشاندان/شاردنەوە">
                        <svg class="w-5 h-5 eye-show" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg class="w-5 h-5 eye-hide hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
                <p class="text-xs text-slate-400 mt-1">کلیک لەسەر وێنەی چاو بکە بۆ بینینی وشەی نهێنی.</p>
            </div>

            <div class="border border-slate-200 rounded-lg p-4 bg-slate-50/60">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_admin" value="1" id="isAdmin" onchange="togglePerms()" <?php echo e(old('is_admin') ? 'checked' : ''); ?> class="w-5 h-5 rounded border-slate-300 text-green-600 focus:ring-green-500">
                    <span class="text-sm font-semibold text-slate-800">بەڕێوەبەری گشتی (دەسەڵاتی تەواو بۆ هەموو بەشەکان)</span>
                </label>
            </div>

            <div id="permsBox">
                <label class="label">دەسەڵاتەکان (هەر بەشێک کە دەتەوێت کارا بێت دیاری بکە)</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer">
                        <input type="checkbox" name="permissions[]" value="<?php echo e($key); ?>" <?php echo e(in_array($key, old('permissions', [])) ? 'checked' : ''); ?> class="w-5 h-5 rounded border-slate-300 text-green-600 focus:ring-green-500">
                        <span class="text-sm text-slate-700"><?php echo e($label); ?></span>
                    </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary">پاشەکەوتکردن</button>
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
    function togglePerms() {
        const isAdmin = document.getElementById('isAdmin').checked;
        document.getElementById('permsBox').style.display = isAdmin ? 'none' : 'block';
    }
    togglePerms();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/workspace/accounting-system/resources/views/admin/users/create.blade.php ENDPATH**/ ?>