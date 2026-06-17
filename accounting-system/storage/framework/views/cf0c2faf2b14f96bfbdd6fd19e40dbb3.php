<?php
    $invoice = $invoice ?? null;
    $oldLines = old('lines');
    if ($oldLines === null && $invoice) {
        $oldLines = $invoice->details->map(function ($d) {
            return [
                'material_id' => $d->material_id,
                'custom_type' => $d->custom_type,
                'unit'        => $d->unit,
                'quantity'    => $d->quantity,
                'unit_price'  => $d->unit_price,
                'currency'    => $d->currency ?? 'IQD',
            ];
        })->values();
    }
    $oldLines = $oldLines ?: [];
?>

<?php if($errors->any()): ?>
    <div class="card p-4 mb-4 bg-red-50 border-red-200 text-red-600 text-sm">
        <ul class="list-disc pe-5 space-y-1">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="<?php echo e($action); ?>" id="purchaseForm">
    <?php echo csrf_field(); ?>
    <?php if(($method ?? 'POST') === 'PUT'): ?>
        <?php echo method_field('PUT'); ?>
    <?php endif; ?>

    
    <div class="card p-5 mb-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div>
            <label class="label">دابینکەر (هەژماردار)</label>
            <select name="supplier_id" class="input-field">
                <option value="">— بێ هەژمار / گەیەنەری کاتی —</option>
                <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s->id); ?>" <?php if(old('supplier_id', $invoice->supplier_id ?? '')==$s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <p class="text-xs text-slate-400 mt-1">ئەگەر دابینکەر هەڵبژێردرا، باڵانس و کشف حساب نوێدەکرێتەوە.</p>
        </div>
        <div>
            <label class="label">بەروار *</label>
            <input type="date" name="date" value="<?php echo e(old('date', optional($invoice)->date?->format('Y-m-d') ?? date('Y-m-d'))); ?>" class="input-field" required>
        </div>
        <div>
            <label class="label">پڕۆژە</label>
            <select name="project_id" class="input-field">
                <option value="">— بێ پڕۆژە —</option>
                <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($p->id); ?>" <?php if(old('project_id', $invoice->project_id ?? '')==$p->id): echo 'selected'; endif; ?>><?php echo e($p->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
    </div>

    
    <div class="card p-5 mb-4">
        <div class="font-bold text-sm text-slate-700 mb-3">زانیاری گەیەنەری مەواد</div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="label">ناوی گەیەنەر</label>
                <input type="text" name="deliverer_name" value="<?php echo e(old('deliverer_name', $invoice->deliverer_name ?? '')); ?>" class="input-field" placeholder="ناوی کەسی گەیەنەر">
            </div>
            <div>
                <label class="label">ژمارەی مۆبایل</label>
                <input type="text" name="deliverer_phone" value="<?php echo e(old('deliverer_phone', $invoice->deliverer_phone ?? '')); ?>" class="input-field">
            </div>
            <div>
                <label class="label">ناونیشان</label>
                <input type="text" name="deliverer_address" value="<?php echo e(old('deliverer_address', $invoice->deliverer_address ?? '')); ?>" class="input-field">
            </div>
            <div>
                <label class="label">ژمارەی ئۆتۆمبێل</label>
                <input type="text" name="vehicle_number" value="<?php echo e(old('vehicle_number', $invoice->vehicle_number ?? '')); ?>" class="input-field">
            </div>
            <div>
                <label class="label">جۆری ئۆتۆمبێل</label>
                <input type="text" name="vehicle_type" value="<?php echo e(old('vehicle_type', $invoice->vehicle_type ?? '')); ?>" class="input-field" placeholder="نمونە: پیکاب، شاحینە">
            </div>
        </div>
    </div>

    
    <div class="card p-0 mb-4">
        <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
            <span class="font-bold text-sm text-slate-700">هێڵەکانی کڕین</span>
            <button type="button" id="addLine" class="btn-info !px-3 !py-1.5">+ زیادکردنی هێڵ</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-right text-xs text-slate-500 border-b border-slate-200 bg-slate-50">
                        <th class="px-3 py-2 font-semibold min-w-[160px]">مەواد (کۆگا)</th>
                        <th class="px-3 py-2 font-semibold min-w-[140px]">یان جۆری دەستی</th>
                        <th class="px-3 py-2 font-semibold">یەکە</th>
                        <th class="px-3 py-2 font-semibold">بڕ</th>
                        <th class="px-3 py-2 font-semibold">نرخی یەکە</th>
                        <th class="px-3 py-2 font-semibold">دراو</th>
                        <th class="px-3 py-2 font-semibold">کۆی هێڵ</th>
                        <th class="px-3 py-2 font-semibold"></th>
                    </tr>
                </thead>
                <tbody id="linesBody"></tbody>
            </table>
        </div>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
        <div class="card p-4">
            <div class="font-bold text-sm text-slate-700 mb-3">دیناری عێراقی (د.ع)</div>
            <div class="grid grid-cols-3 gap-3 items-end">
                <div>
                    <div class="text-xs text-slate-400">کۆی گشتی</div>
                    <div class="text-lg font-extrabold text-slate-800"><span id="totalIqd">0</span></div>
                </div>
                <div>
                    <label class="label">بڕی دراو</label>
                    <input type="number" step="0.01" name="paid_iqd" id="paidIqd" value="<?php echo e(old('paid_iqd', $invoice->paid_iqd ?? 0)); ?>" class="input-field">
                </div>
                <div>
                    <div class="text-xs text-slate-400">ماوە (قەرز)</div>
                    <div class="text-lg font-extrabold text-red-600"><span id="remainIqd">0</span></div>
                </div>
            </div>
        </div>
        <div class="card p-4">
            <div class="font-bold text-sm text-slate-700 mb-3">دۆلاری ئەمریکی ($)</div>
            <div class="grid grid-cols-3 gap-3 items-end">
                <div>
                    <div class="text-xs text-slate-400">کۆی گشتی</div>
                    <div class="text-lg font-extrabold text-slate-800"><span id="totalUsd">0</span></div>
                </div>
                <div>
                    <label class="label">بڕی دراو</label>
                    <input type="number" step="0.01" name="paid_usd" id="paidUsd" value="<?php echo e(old('paid_usd', $invoice->paid_usd ?? 0)); ?>" class="input-field">
                </div>
                <div>
                    <div class="text-xs text-slate-400">ماوە (قەرز)</div>
                    <div class="text-lg font-extrabold text-red-600"><span id="remainUsd">0</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card p-5 mb-4">
        <label class="label">تێبینی</label>
        <textarea name="notes" rows="2" class="input-field"><?php echo e(old('notes', $invoice->notes ?? '')); ?></textarea>
    </div>

    <div class="flex items-center gap-2">
        <button type="submit" class="btn-primary"><?php echo e($submitLabel ?? 'تۆمارکردن'); ?></button>
        <a href="<?php echo e(route('purchase-invoices.index')); ?>" class="btn-outline">پاشگەزبوونەوە</a>
    </div>
</form>


<template id="lineTemplate">
    <tr class="border-b border-slate-100 line-row">
        <td class="px-3 py-2">
            <select name="lines[__I__][material_id]" class="input-field material-select" onchange="onMaterialChange(this)">
                <option value="">— جۆری دەستی —</option>
                <?php $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($m->id); ?>" data-unit="<?php echo e($m->unit); ?>"><?php echo e($m->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </td>
        <td class="px-3 py-2">
            <input type="text" name="lines[__I__][custom_type]" class="input-field custom-type" placeholder="ناوی شت">
        </td>
        <td class="px-3 py-2">
            <input type="text" name="lines[__I__][unit]" class="input-field unit-input" placeholder="دانە" style="min-width:70px">
        </td>
        <td class="px-3 py-2">
            <input type="number" step="0.001" name="lines[__I__][quantity]" class="input-field qty" value="1" oninput="recalc()" style="min-width:80px">
        </td>
        <td class="px-3 py-2">
            <input type="number" step="0.01" name="lines[__I__][unit_price]" class="input-field price" value="0" oninput="recalc()" style="min-width:90px">
        </td>
        <td class="px-3 py-2">
            <select name="lines[__I__][currency]" class="input-field currency-select" onchange="recalc()" style="min-width:80px">
                <option value="IQD">د.ع</option>
                <option value="USD">$</option>
            </select>
        </td>
        <td class="px-3 py-2 font-semibold text-slate-800 line-total">0</td>
        <td class="px-3 py-2">
            <button type="button" class="btn-danger !px-2.5 !py-1.5" onclick="removeLine(this)">×</button>
        </td>
    </tr>
</template>

<?php $__env->startPush('scripts'); ?>
<script>
    let lineIndex = 0;
    const tpl = document.getElementById('lineTemplate').innerHTML;
    const body = document.getElementById('linesBody');

    function addLine(data) {
        const html = tpl.replace(/__I__/g, lineIndex);
        lineIndex++;
        const wrap = document.createElement('tbody');
        wrap.innerHTML = html.trim();
        const row = wrap.firstChild;
        body.appendChild(row);
        if (data) {
            if (data.material_id) row.querySelector('.material-select').value = data.material_id;
            if (data.custom_type != null) row.querySelector('.custom-type').value = data.custom_type;
            if (data.unit != null) row.querySelector('.unit-input').value = data.unit;
            if (data.quantity != null) row.querySelector('.qty').value = data.quantity;
            if (data.unit_price != null) row.querySelector('.price').value = data.unit_price;
            if (data.currency) row.querySelector('.currency-select').value = data.currency;
        }
        recalc();
    }

    function removeLine(btn) {
        const rows = body.querySelectorAll('.line-row');
        if (rows.length <= 1) { return; }
        btn.closest('tr').remove();
        recalc();
    }

    function onMaterialChange(sel) {
        const opt = sel.options[sel.selectedIndex];
        const unit = opt.getAttribute('data-unit');
        const row = sel.closest('tr');
        const unitInput = row.querySelector('.unit-input');
        if (sel.value && unit && !unitInput.value) {
            unitInput.value = unit;
        }
    }

    function fmt(n) { return (Math.round(n * 100) / 100).toLocaleString('en-US'); }

    function recalc() {
        let iqd = 0, usd = 0;
        body.querySelectorAll('.line-row').forEach(function (row) {
            const qty = parseFloat(row.querySelector('.qty').value) || 0;
            const price = parseFloat(row.querySelector('.price').value) || 0;
            const cur = row.querySelector('.currency-select').value;
            const total = qty * price;
            row.querySelector('.line-total').textContent = fmt(total) + (cur === 'USD' ? ' $' : ' د.ع');
            if (cur === 'USD') { usd += total; } else { iqd += total; }
        });
        document.getElementById('totalIqd').textContent = fmt(iqd);
        document.getElementById('totalUsd').textContent = fmt(usd);
        const paidIqd = parseFloat(document.getElementById('paidIqd').value) || 0;
        const paidUsd = parseFloat(document.getElementById('paidUsd').value) || 0;
        document.getElementById('remainIqd').textContent = fmt(iqd - paidIqd);
        document.getElementById('remainUsd').textContent = fmt(usd - paidUsd);
    }

    document.getElementById('addLine').addEventListener('click', function () { addLine(); });
    document.getElementById('paidIqd').addEventListener('input', recalc);
    document.getElementById('paidUsd').addEventListener('input', recalc);

    const existingLines = <?php echo json_encode($oldLines, 15, 512) ?>;
    if (existingLines && existingLines.length) {
        existingLines.forEach(function (l) { addLine(l); });
    } else {
        addLine();
    }
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH /home/runner/workspace/accounting-system/resources/views/purchase-invoices/_form.blade.php ENDPATH**/ ?>