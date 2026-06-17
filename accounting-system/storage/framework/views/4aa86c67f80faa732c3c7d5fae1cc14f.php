<?php
    $num = fn($v) => number_format((float) $v, 0);
    $qty = fn($v) => rtrim(rtrim(number_format((float) $v, 3), '0'), '.');
    $cur = fn($c) => $c === 'USD' ? '$' : 'د.ع';
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="utf-8">
    <!--[if gte mso 9]><xml><w:WordDocument><w:View>Print</w:View><w:Zoom>100</w:Zoom></w:WordDocument></xml><![endif]-->
    <style>
        @page { size: A4; margin: 1.5cm; mso-page-orientation: portrait; }
        body { font-family: 'Tahoma', sans-serif; color: #1e293b; direction: rtl; }
        .head { border-bottom: 2pt solid #15803d; padding-bottom: 8px; margin-bottom: 12px; }
        .title { font-size: 18pt; font-weight: bold; color: #15803d; }
        .sub { font-size: 9pt; color: #64748b; }
        .meta { font-size: 10pt; color: #475569; margin-top: 4px; }
        h2 { font-size: 12pt; color: #15803d; margin: 14px 0 4px; }
        table { width: 100%; border-collapse: collapse; font-size: 10pt; margin-top: 4px; }
        td, th { border: 0.5pt solid #94a3b8; padding: 5px 7px; text-align: right; }
        th { background: #dcfce7; color: #14532d; }
        .info td { border: none; padding: 2px 6px; font-size: 10pt; }
        .sign { width: 100%; margin-top: 50px; }
        .sign td { border: none; text-align: center; font-size: 10pt; padding-top: 40px; }
        .sign .line { border-top: 1pt solid #94a3b8; padding-top: 5px; }
    </style>
</head>
<body>
    <?php if($logo): ?><img src="<?php echo e($logo); ?>" width="60" height="60" style="float:right" alt="logo"><?php endif; ?>
    <div class="head">
        <div class="title">ژوانی گەشتیاری</div>
        <div class="sub">سیستەمی ژمێریاری و بنیاتنان</div>
        <div class="meta">
            وەسڵی کڕین #<?php echo e($invoice->id); ?> | بەروار: <?php echo e(optional($invoice->date)->format('Y-m-d')); ?>

            <?php if($invoice->project): ?> | پڕۆژە: <?php echo e($invoice->project->name); ?> <?php endif; ?>
        </div>
    </div>

    <h2>زانیاری دابینکەر / گەیەنەر</h2>
    <table class="info">
        <tr>
            <td><b>دابینکەر/گەیەنەر:</b> <?php echo e($invoice->party_name); ?></td>
            <td><b>مۆبایل:</b> <?php echo e($invoice->deliverer_phone); ?></td>
        </tr>
        <tr>
            <td><b>ناونیشان:</b> <?php echo e($invoice->deliverer_address); ?></td>
            <td><b>ئۆتۆمبێل:</b> <?php echo e($invoice->vehicle_number); ?> <?php echo e($invoice->vehicle_type); ?></td>
        </tr>
    </table>

    <h2>هێڵەکانی کڕین</h2>
    <table>
        <thead>
            <tr><th>#</th><th>مەواد / جۆر</th><th>بڕ</th><th>یەکە</th><th>نرخی یەکە</th><th>دراو</th><th>کۆی هێڵ</th></tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $invoice->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($i + 1); ?></td>
                    <td><?php echo e($d->material->name ?? $d->custom_type); ?></td>
                    <td><?php echo e($qty($d->quantity)); ?></td>
                    <td><?php echo e($d->unit); ?></td>
                    <td><?php echo e($num($d->unit_price)); ?></td>
                    <td><?php echo e($cur($d->currency)); ?></td>
                    <td><?php echo e($num($d->line_total)); ?> <?php echo e($cur($d->currency)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <h2>کۆکراوەکان</h2>
    <table>
        <tr><th></th><th>کۆی گشتی</th><th>دراوە</th><th>ماوە</th></tr>
        <tr><td><b>دیناری عێراقی (د.ع)</b></td><td><?php echo e($num($invoice->total_iqd)); ?></td><td><?php echo e($num($invoice->paid_iqd)); ?></td><td><?php echo e($num($invoice->remaining_iqd)); ?></td></tr>
        <tr><td><b>دۆلاری ئەمریکی ($)</b></td><td><?php echo e($num($invoice->total_usd)); ?></td><td><?php echo e($num($invoice->paid_usd)); ?></td><td><?php echo e($num($invoice->remaining_usd)); ?></td></tr>
    </table>

    <?php if($invoice->notes): ?>
        <p style="font-size:10pt;margin-top:10px"><b>تێبینی:</b> <?php echo e($invoice->notes); ?></p>
    <?php endif; ?>

    <table class="sign">
        <tr>
            <td><div class="line">واژووی گەیەنەر / دابینکەر</div></td>
            <td><div class="line">واژووی وەرگر (ژوانی گەشتیاری)</div></td>
        </tr>
    </table>
</body>
</html>
<?php /**PATH /home/runner/workspace/accounting-system/resources/views/purchase-invoices/export-word.blade.php ENDPATH**/ ?>