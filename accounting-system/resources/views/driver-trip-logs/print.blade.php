@php
    $num = fn($v) => number_format((float) $v, 0);
    $qty = fn($v) => rtrim(rtrim(number_format((float) $v, 2), '0'), '.');
    $cur = fn($c) => $c === 'USD' ? '$' : 'د.ع';
@endphp
<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>تۆماری گواستنەوە #{{ $log->id }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Tahoma', 'Segoe UI', sans-serif; color: #1e293b; margin: 0; background: #f1f5f9; }
        .page { width: 210mm; min-height: 297mm; margin: 0 auto; background: #fff; padding: 14mm; }
        .head { display: flex; align-items: center; justify-content: space-between; border-bottom: 3px solid #15803d; padding-bottom: 10px; margin-bottom: 14px; }
        .head .brand { display: flex; align-items: center; gap: 10px; }
        .head img { width: 64px; height: 64px; object-fit: contain; }
        .head .title { font-size: 20px; font-weight: 800; color: #15803d; }
        .head .sub { font-size: 12px; color: #64748b; }
        .doc-meta { text-align: left; font-size: 12px; color: #475569; }
        .doc-meta b { color: #0f172a; }
        h2.section { font-size: 14px; color: #15803d; margin: 16px 0 6px; border-right: 4px solid #15803d; padding-right: 8px; }
        .info { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 6px 16px; font-size: 12px; margin-bottom: 8px; }
        .info div span { color: #64748b; }
        .info div b { color: #0f172a; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 6px; }
        th, td { border: 1px solid #cbd5e1; padding: 7px 8px; text-align: right; }
        thead th { background: #dcfce7; color: #14532d; font-weight: 700; }
        .totals { display: flex; gap: 16px; margin-top: 14px; }
        .totals .box { flex: 1; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px 12px; }
        .totals .box h3 { margin: 0 0 6px; font-size: 13px; color: #15803d; }
        .totals .row { display: flex; justify-content: space-between; font-size: 12px; padding: 2px 0; }
        .totals .row.rem { border-top: 1px solid #e2e8f0; margin-top: 4px; padding-top: 5px; }
        .sign { display: flex; justify-content: space-between; gap: 40px; margin-top: 50px; }
        .sign .col { flex: 1; text-align: center; font-size: 12px; }
        .sign .line { border-top: 1px solid #94a3b8; margin-top: 40px; padding-top: 6px; color: #475569; }
        .notes { font-size: 12px; color: #475569; margin-top: 12px; }
        .toolbar { text-align: center; padding: 12px; }
        .toolbar button { background: #15803d; color: #fff; border: 0; padding: 8px 22px; border-radius: 6px; font-size: 13px; cursor: pointer; }
        @media print {
            body { background: #fff; }
            .page { width: auto; min-height: auto; margin: 0; padding: 10mm; }
            .toolbar { display: none; }
            @page { size: A4; margin: 8mm; }
        }
    </style>
</head>
<body>
<div class="toolbar"><button onclick="window.print()">چاپکردن</button></div>
<div class="page">
    <div class="head">
        <div class="brand">
            @if($logo)<img src="{{ $logo }}" alt="logo">@endif
            <div>
                <div class="title">ژوانی گەشتیاری</div>
                <div class="sub">تۆماری گواستنەوە و شۆفێر</div>
            </div>
        </div>
        <div class="doc-meta">
            <div>تۆماری گواستنەوە: <b>#{{ $log->id }}</b></div>
            <div>بەروار: <b>{{ optional($log->date)->format('Y-m-d') }}</b></div>
            @if($log->project)<div>پڕۆژە: <b>{{ $log->project->name }}</b></div>@endif
        </div>
    </div>

    <h2 class="section">زانیاری شۆفێر</h2>
    <div class="info">
        <div><span>شۆفێر: </span><b>{{ $log->driver->name ?? '—' }}</b></div>
        @if($log->driver && $log->driver->phone)<div><span>مۆبایل: </span><b>{{ $log->driver->phone }}</b></div>@endif
        @if($log->driver && $log->driver->vehicle_number)<div><span>ژمارەی ئۆتۆمبێل: </span><b>{{ $log->driver->vehicle_number }}</b></div>@endif
        @if($log->driver && $log->driver->vehicle_type)<div><span>جۆری ئۆتۆمبێل: </span><b>{{ $log->driver->vehicle_type }}</b></div>@endif
    </div>

    <h2 class="section">هێڵەکانی گواستنەوە</h2>
    <table>
        <thead>
            <tr>
                <th style="width:36px">#</th>
                <th>جۆری کار</th>
                <th>ژمارەی سەفەر</th>
                <th>نرخی سەفەر</th>
                <th>دراو</th>
                <th>کۆی هێڵ</th>
            </tr>
        </thead>
        <tbody>
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
        </tbody>
    </table>

    <div class="totals">
        <div class="box">
            <h3>دیناری عێراقی (د.ع)</h3>
            <div class="row"><span>کۆی گشتی</span><b>{{ $num($log->grand_total_iqd) }}</b></div>
            <div class="row"><span>دراوە</span><b>{{ $num($log->paid_iqd) }}</b></div>
            <div class="row rem"><span>ماوە (قەرز)</span><b>{{ $num($log->remaining_iqd) }}</b></div>
        </div>
        <div class="box">
            <h3>دۆلاری ئەمریکی ($)</h3>
            <div class="row"><span>کۆی گشتی</span><b>{{ $num($log->grand_total_usd) }}</b></div>
            <div class="row"><span>دراوە</span><b>{{ $num($log->paid_usd) }}</b></div>
            <div class="row rem"><span>ماوە (قەرز)</span><b>{{ $num($log->remaining_usd) }}</b></div>
        </div>
    </div>

    @if($log->notes)
        <div class="notes"><b>تێبینی: </b>{{ $log->notes }}</div>
    @endif

    <div class="sign">
        <div class="col"><div class="line">واژووی شۆفێر</div></div>
        <div class="col"><div class="line">واژووی بەرپرس (ژوانی گەشتیاری)</div></div>
    </div>
</div>
</body>
</html>
