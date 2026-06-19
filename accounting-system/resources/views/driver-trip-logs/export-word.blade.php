@php
    $num = fn($v) => number_format((float) $v, 0);
    $qty = fn($v) => rtrim(rtrim(number_format((float) $v, 2), '0'), '.');
    $cur = fn($c) => $c === 'USD' ? '$' : 'د.ع';
@endphp
<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40" lang="ckb" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>تۆماری گواستنەوە #{{ $log->id }}</title>
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
    <h1>ژوانی گەشتیاری — تۆماری گواستنەوە #{{ $log->id }}</h1>
    <p class="meta">
        شۆفێر: <b>{{ $log->driver->name ?? '—' }}</b>@if($log->driver && $log->driver->phone) — مۆبایل: <b>{{ $log->driver->phone }}</b>@endif<br>
        بەروار: <b>{{ optional($log->date)->format('Y-m-d') }}</b>@if($log->project) — پڕۆژە: <b>{{ $log->project->name }}</b>@endif
    </p>

    <h2>هێڵەکانی گواستنەوە</h2>
    <table>
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
    </table>

    <h2>پوختەی پارە</h2>
    <table>
        <tr><th>دراو</th><th>کۆی گشتی</th><th>دراوە</th><th>ماوە</th></tr>
        <tr><td>د.ع</td><td>{{ $num($log->grand_total_iqd) }}</td><td>{{ $num($log->paid_iqd) }}</td><td>{{ $num($log->remaining_iqd) }}</td></tr>
        <tr><td>$</td><td>{{ $num($log->grand_total_usd) }}</td><td>{{ $num($log->paid_usd) }}</td><td>{{ $num($log->remaining_usd) }}</td></tr>
    </table>

    @if($log->notes)
        <p class="meta"><b>تێبینی: </b>{{ $log->notes }}</p>
    @endif
</body>
</html>
