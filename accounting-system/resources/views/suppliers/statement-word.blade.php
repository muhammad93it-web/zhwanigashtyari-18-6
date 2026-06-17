@php
    $num = fn($v) => number_format((float) $v, 0);
    $cur = fn($c) => $c === 'USD' ? '$' : 'د.ع';
@endphp
<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40" lang="ckb" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>کەشف حساب — {{ $supplier->name }}</title>
    <style>
        body { font-family: 'Tahoma', sans-serif; direction: rtl; color: #1e293b; }
        h1 { color: #15803d; font-size: 18px; }
        h2 { color: #15803d; font-size: 14px; border-bottom: 2px solid #15803d; padding-bottom: 3px; }
        table { border-collapse: collapse; width: 100%; font-size: 12px; }
        th, td { border: 1px solid #999; padding: 6px 9px; text-align: right; }
        th { background: #dcfce7; font-weight: bold; }
        .meta { font-size: 12px; color: #475569; }
    </style>
</head>
<body>
    <h1>ژوانی گەشتیاری — کەشف حساب</h1>
    <p class="meta">
        کەس: <b>{{ $supplier->name }}</b>@if($supplier->phone) — مۆبایل: <b>{{ $supplier->phone }}</b>@endif<br>
        بەروار: <b>{{ now()->format('Y-m-d') }}</b>
    </p>

    <h2>پوختەی حساب</h2>
    <table>
        <tr><th>دراو</th><th>کۆی کڕین</th><th>کۆی پارەدان</th><th>قەرزمان (دەبێ بیدەین)</th><th>بۆ ئێمە (لای ئەوە)</th></tr>
        @foreach(['IQD','USD'] as $code)
            @php $s = $summary[$code]; $bal = (float) $s['balance']; @endphp
            <tr>
                <td>{{ $cur($code) }}</td>
                <td>{{ $num($s['purchase']) }}</td>
                <td>{{ $num($s['payment']) }}</td>
                <td>{{ $bal > 0 ? $num($bal) : 0 }}</td>
                <td>{{ $bal < 0 ? $num(abs($bal)) : 0 }}</td>
            </tr>
        @endforeach
    </table>

    <h2>مامەڵەکان</h2>
    <table>
        <tr>
            <th>بەروار</th><th>جۆر</th><th>وەسف</th><th>دراو</th><th>بڕ</th><th>باڵانس دوای مامەڵە</th>
        </tr>
        @forelse($transactions as $t)
            <tr>
                <td>{{ optional($t->date)->format('Y-m-d') }}</td>
                <td>{{ $t->type_name }}</td>
                <td>{{ $t->description ?: '—' }}</td>
                <td>{{ $cur($t->currency) }}</td>
                <td>{{ ($t->type == 'purchase' ? '+' : '−') . $num($t->amount) }} {{ $cur($t->currency) }}</td>
                <td>{{ $num($t->balance_after) }} {{ $cur($t->currency) }}</td>
            </tr>
        @empty
            <tr><td colspan="6" style="text-align:center;">هیچ مامەڵەیەک نییە.</td></tr>
        @endforelse
    </table>
</body>
</html>
