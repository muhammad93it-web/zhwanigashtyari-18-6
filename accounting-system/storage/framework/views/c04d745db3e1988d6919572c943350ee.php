<?php
    $num = fn($v) => number_format((float) $v, 0);
    $qty = fn($v) => rtrim(rtrim(number_format((float) $v, 3), '0'), '.');
    $cur = fn($c) => $c === 'USD' ? '$' : 'د.ع';
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="utf-8">
    
    <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>
        <x:Name>Purchase Invoice</x:Name>
        <x:WorksheetOptions><x:DisplayRightToLeft/><x:Print><x:ValidPrinterInfo/><x:PaperSizeIndex>9</x:PaperSizeIndex></x:Print></x:WorksheetOptions>
    </x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->
    
    <style>
        table { border-collapse: collapse; }
        td, th { border: 0.5pt solid #94a3b8; padding: 5px 8px; font-family: Tahoma, sans-serif; font-size: 11pt; mso-number-format:"\@"; }
        th { background: #dcfce7; font-weight: bold; }
        .title { font-size: 16pt; font-weight: bold; color: #15803d; border: none; }
        .lbl { background: #f1f5f9; font-weight: bold; }
        .noborder { border: none; }
    </style>
</head>
<body>
<table dir="rtl">
    <tr><td class="title noborder" colspan="7">ژوانی گەشتیاری — وەسڵی کڕین #<?php echo e($invoice->id); ?></td></tr>
    <tr><td class="noborder" colspan="7">بەروار: <?php echo e(optional($invoice->date)->format('Y-m-d')); ?> <?php if($invoice->project): ?> | پڕۆژە: <?php echo e($invoice->project->name); ?> <?php endif; ?></td></tr>
    <tr><td class="noborder" colspan="7"></td></tr>
    <tr>
        <td class="lbl">دابینکەر/گەیەنەر</td><td colspan="2"><?php echo e($invoice->party_name); ?></td>
        <td class="lbl">مۆبایل</td><td><?php echo e($invoice->deliverer_phone); ?></td>
        <td class="lbl">ناونیشان</td><td><?php echo e($invoice->deliverer_address); ?></td>
    </tr>
    <tr>
        <td class="lbl">ژمارەی ئۆتۆمبێل</td><td colspan="2"><?php echo e($invoice->vehicle_number); ?></td>
        <td class="lbl">جۆری ئۆتۆمبێل</td><td colspan="3"><?php echo e($invoice->vehicle_type); ?></td>
    </tr>
    <tr><td class="noborder" colspan="7"></td></tr>
    <tr>
        <th>#</th><th>مەواد / جۆر</th><th>بڕ</th><th>یەکە</th><th>نرخی یەکە</th><th>دراو</th><th>کۆی هێڵ</th>
    </tr>
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
    <tr><td class="noborder" colspan="7"></td></tr>
    <tr><td class="lbl" colspan="2">کۆی گشتی (د.ع)</td><td colspan="2"><?php echo e($num($invoice->total_iqd)); ?></td><td class="lbl">کۆی گشتی ($)</td><td colspan="2"><?php echo e($num($invoice->total_usd)); ?></td></tr>
    <tr><td class="lbl" colspan="2">دراوە (د.ع)</td><td colspan="2"><?php echo e($num($invoice->paid_iqd)); ?></td><td class="lbl">دراوە ($)</td><td colspan="2"><?php echo e($num($invoice->paid_usd)); ?></td></tr>
    <tr><td class="lbl" colspan="2">ماوە (د.ع)</td><td colspan="2"><?php echo e($num($invoice->remaining_iqd)); ?></td><td class="lbl">ماوە ($)</td><td colspan="2"><?php echo e($num($invoice->remaining_usd)); ?></td></tr>
    <?php if($invoice->notes): ?>
        <tr><td class="noborder" colspan="7"></td></tr>
        <tr><td class="lbl">تێبینی</td><td colspan="6"><?php echo e($invoice->notes); ?></td></tr>
    <?php endif; ?>
</table>
</body>
</html>
<?php /**PATH /home/runner/workspace/accounting-system/resources/views/purchase-invoices/export-excel.blade.php ENDPATH**/ ?>