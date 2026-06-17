@php
    $num = fn($v) => number_format((float) $v, 0);
    $qty = fn($v) => rtrim(rtrim(number_format((float) $v, 3), '0'), '.');
    $cur = fn($c) => $c === 'USD' ? '$' : 'د.ع';
@endphp
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="utf-8">
    @verbatim
    <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>
        <x:Name>Purchase Invoice</x:Name>
        <x:WorksheetOptions><x:DisplayRightToLeft/><x:Print><x:ValidPrinterInfo/><x:PaperSizeIndex>9</x:PaperSizeIndex></x:Print></x:WorksheetOptions>
    </x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->
    @endverbatim
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
    <tr><td class="title noborder" colspan="7">ژوانی گەشتیاری — وەسڵی کڕین #{{ $invoice->id }}</td></tr>
    <tr><td class="noborder" colspan="7">بەروار: {{ optional($invoice->date)->format('Y-m-d') }} @if($invoice->project) | پڕۆژە: {{ $invoice->project->name }} @endif</td></tr>
    <tr><td class="noborder" colspan="7"></td></tr>
    <tr>
        <td class="lbl">دابینکەر/گەیەنەر</td><td colspan="2">{{ $invoice->party_name }}</td>
        <td class="lbl">مۆبایل</td><td>{{ $invoice->deliverer_phone }}</td>
        <td class="lbl">ناونیشان</td><td>{{ $invoice->deliverer_address }}</td>
    </tr>
    <tr>
        <td class="lbl">ژمارەی ئۆتۆمبێل</td><td colspan="2">{{ $invoice->vehicle_number }}</td>
        <td class="lbl">جۆری ئۆتۆمبێل</td><td colspan="3">{{ $invoice->vehicle_type }}</td>
    </tr>
    <tr><td class="noborder" colspan="7"></td></tr>
    <tr>
        <th>#</th><th>مەواد / جۆر</th><th>بڕ</th><th>یەکە</th><th>نرخی یەکە</th><th>دراو</th><th>کۆی هێڵ</th>
    </tr>
    @foreach($invoice->details as $i => $d)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $d->material->name ?? $d->custom_type }}</td>
            <td>{{ $qty($d->quantity) }}</td>
            <td>{{ $d->unit }}</td>
            <td>{{ $num($d->unit_price) }}</td>
            <td>{{ $cur($d->currency) }}</td>
            <td>{{ $num($d->line_total) }} {{ $cur($d->currency) }}</td>
        </tr>
    @endforeach
    <tr><td class="noborder" colspan="7"></td></tr>
    <tr><td class="lbl" colspan="2">کۆی گشتی (د.ع)</td><td colspan="2">{{ $num($invoice->total_iqd) }}</td><td class="lbl">کۆی گشتی ($)</td><td colspan="2">{{ $num($invoice->total_usd) }}</td></tr>
    <tr><td class="lbl" colspan="2">دراوە (د.ع)</td><td colspan="2">{{ $num($invoice->paid_iqd) }}</td><td class="lbl">دراوە ($)</td><td colspan="2">{{ $num($invoice->paid_usd) }}</td></tr>
    <tr><td class="lbl" colspan="2">ماوە (د.ع)</td><td colspan="2">{{ $num($invoice->remaining_iqd) }}</td><td class="lbl">ماوە ($)</td><td colspan="2">{{ $num($invoice->remaining_usd) }}</td></tr>
    @if($invoice->notes)
        <tr><td class="noborder" colspan="7"></td></tr>
        <tr><td class="lbl">تێبینی</td><td colspan="6">{{ $invoice->notes }}</td></tr>
    @endif
</table>
</body>
</html>
