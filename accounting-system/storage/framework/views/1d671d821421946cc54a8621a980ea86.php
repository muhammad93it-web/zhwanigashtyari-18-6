<?php $__env->startSection('title', 'پڕۆژەکان'); ?>
<?php $__env->startSection('page-title', 'پڕۆژەکان'); ?>
<?php $__env->startSection('page-subtitle', 'لیستی پڕۆژە و بیناکان'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $num = fn($v) => number_format((float) $v, 0);
    $statusBadge = ['active' => 'badge-green', 'completed' => 'badge-cyan', 'on_hold' => 'badge-amber'];
?>

<div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-bold text-slate-800">لیستی پڕۆژەکان</h2>
    <a href="<?php echo e(route('projects.create')); ?>" class="btn-primary">+ پڕۆژەی نوێ</a>
</div>

<form method="GET" action="<?php echo e(route('projects.index')); ?>" class="card p-4 mb-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
    <div>
        <label class="label">گەڕان</label>
        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="ناو یان شوێن" class="input-field">
    </div>
    <div>
        <label class="label">دۆخ</label>
        <select name="status" class="input-field">
            <option value="">هەموو</option>
            <?php $__currentLoopData = \App\Models\Project::STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($k); ?>" <?php if(request('status')==$k): echo 'selected'; endif; ?>><?php echo e($v); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="flex items-end gap-2">
        <button type="submit" class="btn-info">گەڕان</button>
        <a href="<?php echo e(route('projects.index')); ?>" class="btn-outline">سڕینەوە</a>
    </div>
</form>

<div class="card p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 font-semibold">ناوی پڕۆژە</th>
                    <th class="px-4 py-3 font-semibold">کڕیار</th>
                    <th class="px-4 py-3 font-semibold">شوێن</th>
                    <th class="px-4 py-3 font-semibold">بودجە</th>
                    <th class="px-4 py-3 font-semibold">دۆخ</th>
                    <th class="px-4 py-3 font-semibold">کردارەکان</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="table-row">
                        <td class="px-4 py-3 font-semibold text-slate-800"><?php echo e($project->name); ?></td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($project->client->name ?? '—'); ?></td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($project->location ?: '—'); ?></td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($project->budget ? $num($project->budget) : '—'); ?></td>
                        <td class="px-4 py-3"><span class="<?php echo e($statusBadge[$project->status] ?? 'badge-slate'); ?>"><?php echo e($project->status_name); ?></span></td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                <a href="<?php echo e(route('projects.show', $project)); ?>" class="btn-info !px-3 !py-1.5">بینین</a>
                                <a href="<?php echo e(route('projects.edit', $project)); ?>" class="btn-warning !px-3 !py-1.5">دەستکاری</a>
                                <form method="POST" action="<?php echo e(route('projects.destroy', $project)); ?>" onsubmit="return confirm('دڵنیایت لە سڕینەوە؟')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn-danger !px-3 !py-1.5">سڕینەوە</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="px-4 py-10 text-center text-slate-400">هیچ پڕۆژەیەک نییە.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4"><?php echo e($projects->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/workspace/accounting-system/resources/views/projects/index.blade.php ENDPATH**/ ?>