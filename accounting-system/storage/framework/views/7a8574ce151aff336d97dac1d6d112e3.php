<?php
    $isPurchase = $type === 'purchase';
    $pageTitle = $isPurchase ? 'کڕینی مەواد' : 'فرۆشتنی مەواد';
    $partyLabel = $isPurchase ? 'دابینکەر' : 'کڕیار';
?>

<?php $__env->startSection('title', $pageTitle); ?>
<?php $__env->startSection('page-title', $pageTitle); ?>
<?php $__env->startSection('page-subtitle', $isPurchase ? 'تۆمارکردنی کڕینی مەواد بۆ کۆگا' : 'تۆمارکردنی فرۆشتنی مەواد لە کۆگا'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl">
    <div class="card p-5">
        <form method="POST" action="<?php echo e(route('movements.store')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="type" value="<?php echo e($type); ?>">

            <div>
                <label class="label" for="material_id">مەواد <span class="text-red-500">*</span></label>
                <select id="material_id" name="material_id" class="input-field" required>
                    <option value="">— هەڵبژێرە —</option>
                    <?php $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($material->id); ?>" <?php echo e(old('material_id') == $material->id ? 'selected' : ''); ?>>
                            <?php echo e($material->name); ?> (<?php echo e(number_format((float) $material->current_stock, 0)); ?> <?php echo e($material->unit); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['material_id'];
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
                    <label class="label" for="quantity">بڕ <span class="text-red-500">*</span></label>
                    <input type="number" step="0.001" id="quantity" name="quantity" value="<?php echo e(old('quantity')); ?>" class="input-field" required>
                    <?php $__errorArgs = ['quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="label" for="unit_price">نرخی یەکە <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" id="unit_price" name="unit_price" value="<?php echo e(old('unit_price')); ?>" class="input-field" required>
                    <?php $__errorArgs = ['unit_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            
            <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 border border-slate-200">
                <span class="text-sm text-slate-600">کۆی گشتی</span>
                <span id="liveTotal" class="text-lg font-extrabold text-green-600">0</span>
            </div>

            <div>
                <label class="label" for="currency">دراو</label>
                <select id="currency" name="currency" class="input-field">
                    <option value="IQD" <?php echo e(old('currency', 'IQD') === 'IQD' ? 'selected' : ''); ?>>دینار (IQD)</option>
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
                <p class="text-xs text-slate-400 mt-1">ڕێژەی ئێستا: <?php echo e(number_format((float) $currentRate, 0)); ?> دینار بۆ ١ دۆلار</p>
            </div>

            <div>
                <label class="label" for="party_name"><?php echo e($partyLabel); ?></label>
                <input type="text" id="party_name" name="party_name" value="<?php echo e(old('party_name')); ?>" class="input-field">
                <?php $__errorArgs = ['party_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="label" for="client_id">کڕیار (ئەگەر هەبێت)</label>
                <select id="client_id" name="client_id" class="input-field">
                    <option value="">— هیچ —</option>
                    <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($client->id); ?>" <?php echo e(old('client_id') == $client->id ? 'selected' : ''); ?>><?php echo e($client->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['client_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="label" for="movement_date">بەروار <span class="text-red-500">*</span></label>
                <input type="date" id="movement_date" name="movement_date" value="<?php echo e(old('movement_date', date('Y-m-d'))); ?>" class="input-field" required>
                <?php $__errorArgs = ['movement_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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

            <div class="flex items-center gap-2 pt-2">
                <button type="submit" class="btn-primary">تۆمارکردن</button>
                <a href="<?php echo e(route('materials.index')); ?>" class="btn-outline">گەڕانەوە</a>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    (function () {
        const qty = document.getElementById('quantity');
        const price = document.getElementById('unit_price');
        const currency = document.getElementById('currency');
        const out = document.getElementById('liveTotal');

        function recalc() {
            const total = (parseFloat(qty.value) || 0) * (parseFloat(price.value) || 0);
            const symbol = currency.value === 'USD' ? '$' : ' د';
            const formatted = currency.value === 'USD'
                ? total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                : Math.round(total).toLocaleString('en-US');
            out.textContent = currency.value === 'USD' ? ('$' + formatted) : (formatted + ' د');
        }

        [qty, price, currency].forEach(el => el.addEventListener('input', recalc));
        currency.addEventListener('change', recalc);
        recalc();
    })();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/workspace/accounting-system/resources/views/movements/create.blade.php ENDPATH**/ ?>