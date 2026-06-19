@php
    $num = fn($v) => number_format((float) $v, 0);
    $qty = fn($v) => rtrim(rtrim(number_format((float) $v, 2), '0'), '.');
    $cur = fn($c) => $c === 'USD' ? '$' : 'د.ع';
@endphp
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="utf-8">
    @verbatim
    <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>
        <x:Name>Driver Trip</x:Name>
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
    <tr><td class="title noborder" colspan="6">ژوانی گەشتیاری — تۆماری گواستنەوە #{{ $log->id }}</td></tr>
    <tr><td class="noborder" colspan="6">بەروار: {{ optional($log->date)->format('Y-m-d') }} @if($log->project) | پڕۆژە: {{ $log->project->name }} @endif</td></tr>
    <tr><td class="noborder" colspan="6"></td></tr>
    <tr>
        <td class="lbl">شۆفێر</td><td colspan="2">{{ $log->driver->name ?? '' }}</td>
        <td class="lbl">مۆبایل</td><td colspan="2">{{ $log->driver->phone ?? '' }}</td>
    </tr>
    <tr>
        <td class="lbl">ژمارەی ئۆتۆمبێل</td><td colspan="2">{{ $log->driver->vehicle_number ?? '' }}</td>
        <td class="lbl">جۆری ئۆتۆمبێل</td><td colspan="2">{{ $log->driver->vehicle_type ?? '' }}</td>
    </tr>
    <tr><td class="noborder" colspan="6"></td></tr>
    <tr>
        <th>#</th><th>جۆری کار</th><th>ژمارەی سەفەر</th><th>نرخی سەفەر</th><th>دراو</th><th>کۆی هێڵ</th>
    </tr>
    @foreach($log->details as $i => $d)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $d->work_type_name }}</td>
            <td>{{ $qty($d->trip_count) }}</td>
            <td>{{ $num($d->price_per_trip) }}</td>
            <td>{{ $cur($d->currency) }}</td>
            <td>{{ $num($d->line_total) }} {{ $cur($d->currency) }}</td>
        </tr>
    @endforeach
    <tr><td class="noborder" colspan="6"></td></tr>
    <tr><td class="lbl" colspan="2">کۆی گشتی (د.ع)</td><td>{{ $num($log->grand_total_iqd) }}</td><td class="lbl" colspan="2">کۆی گشتی ($)</td><td>{{ $num($log->grand_total_usd) }}</td></tr>
    <tr><td class="lbl" colspan="2">دراوە (د.ع)</td><td>{{ $num($log->paid_iqd) }}</td><td class="lbl" colspan="2">دراوە ($)</td><td>{{ $num($log->paid_usd) }}</td></tr>
    <tr><td class="lbl" colspan="2">ماوە (د.ع)</td><td>{{ $num($log->remaining_iqd) }}</td><td class="lbl" colspan="2">ماوە ($)</td><td>{{ $num($log->remaining_usd) }}</td></tr>
    @if($log->notes)
        <tr><td class="noborder" colspan="6"></td></tr>
        <tr><td class="lbl">تێبینی</td><td colspan="5">{{ $log->notes }}</td></tr>
    @endif
</table>
</body>
</html>
