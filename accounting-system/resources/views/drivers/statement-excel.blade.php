@php
    $num = fn($v) => number_format((float) $v, 0);
    $cur = fn($c) => $c === 'USD' ? '$' : 'د.ع';
@endphp
<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40" lang="ckb" dir="rtl">
<head>
    <meta charset="utf-8">
    @verbatim
    <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>
    <x:Name>کەشف حساب</x:Name>
    <x:WorksheetOptions><x:DisplayRightToLeft/></x:WorksheetOptions>
    </x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->
    @endverbatim
    <style>
        table { border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 6px 10px; text-align: right; mso-number-format:"\@"; }
        th { background: #dcfce7; font-weight: bold; }
        .title { font-size: 16px; font-weight: bold; }
    </style>
</head>
<body>
    <table>
        <tr><td class="title" colspan="6">ژوانی گەشتیاری — کەشف حساب (گواستنەوە و شۆفێر)</td></tr>
        <tr><td colspan="6">شۆفێر: {{ $driver->name }}@if($driver->phone) — مۆبایل: {{ $driver->phone }}@endif</td></tr>
        <tr><td colspan="6">بەروار: {{ now()->format('Y-m-d') }}</td></tr>
        <tr><td colspan="6"></td></tr>
    </table>

    <table>
        <tr><th>دراو</th><th>کۆی گواستنەوە</th><th>کۆی پارەدان</th><th>قەرزمان (دەبێ بیدەین)</th><th>بۆ ئێمە (لای ئەوە)</th></tr>
        @foreach(['IQD','USD'] as $code)
            @php $s = $summary[$code]; $bal = (float) $s['balance']; @endphp
            <tr>
                <td>{{ $cur($code) }}</td>
                <td>{{ $num($s['trip']) }}</td>
                <td>{{ $num($s['payment']) }}</td>
                <td>{{ $bal > 0 ? $num($bal) : 0 }}</td>
                <td>{{ $bal < 0 ? $num(abs($bal)) : 0 }}</td>
            </tr>
        @endforeach
    </table>

    <table>
        <tr><td colspan="6"></td></tr>
        <tr>
            <th>بەروار</th><th>جۆر</th><th>وەسف</th><th>دراو</th><th>بڕ</th><th>باڵانس دوای مامەڵە</th>
        </tr>
        @foreach($transactions as $t)
            <tr>
                <td>{{ optional($t->date)->format('Y-m-d') }}</td>
                <td>{{ $t->type_name }}</td>
                <td>{{ $t->description ?: '—' }}</td>
                <td>{{ $cur($t->currency) }}</td>
                <td>{{ ($t->type == 'payment' ? '-' : '+') . $num($t->amount) }}</td>
                <td>{{ $num($t->balance_after) }}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>
