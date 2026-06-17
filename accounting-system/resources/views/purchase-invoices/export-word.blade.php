@php
    $num = fn($v) => number_format((float) $v, 0);
    $qty = fn($v) => rtrim(rtrim(number_format((float) $v, 3), '0'), '.');
    $cur = fn($c) => $c === 'USD' ? '$' : 'د.ع';
@endphp
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
    @if($logo)<img src="{{ $logo }}" width="60" height="60" style="float:right" alt="logo">@endif
    <div class="head">
        <div class="title">ژوانی گەشتیاری</div>
        <div class="sub">سیستەمی ژمێریاری و بنیاتنان</div>
        <div class="meta">
            وەسڵی کڕین #{{ $invoice->id }} | بەروار: {{ optional($invoice->date)->format('Y-m-d') }}
            @if($invoice->project) | پڕۆژە: {{ $invoice->project->name }} @endif
        </div>
    </div>

    <h2>زانیاری دابینکەر / گەیەنەر</h2>
    <table class="info">
        <tr>
            <td><b>دابینکەر/گەیەنەر:</b> {{ $invoice->party_name }}</td>
            <td><b>مۆبایل:</b> {{ $invoice->deliverer_phone }}</td>
        </tr>
        <tr>
            <td><b>ناونیشان:</b> {{ $invoice->deliverer_address }}</td>
            <td><b>ئۆتۆمبێل:</b> {{ $invoice->vehicle_number }} {{ $invoice->vehicle_type }}</td>
        </tr>
    </table>

    <h2>هێڵەکانی کڕین</h2>
    <table>
        <thead>
            <tr><th>#</th><th>مەواد / جۆر</th><th>بڕ</th><th>یەکە</th><th>نرخی یەکە</th><th>دراو</th><th>کۆی هێڵ</th></tr>
        </thead>
        <tbody>
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
        </tbody>
    </table>

    <h2>کۆکراوەکان</h2>
    <table>
        <tr><th></th><th>کۆی گشتی</th><th>دراوە</th><th>ماوە</th></tr>
        <tr><td><b>دیناری عێراقی (د.ع)</b></td><td>{{ $num($invoice->total_iqd) }}</td><td>{{ $num($invoice->paid_iqd) }}</td><td>{{ $num($invoice->remaining_iqd) }}</td></tr>
        <tr><td><b>دۆلاری ئەمریکی ($)</b></td><td>{{ $num($invoice->total_usd) }}</td><td>{{ $num($invoice->paid_usd) }}</td><td>{{ $num($invoice->remaining_usd) }}</td></tr>
    </table>

    @if($invoice->notes)
        <p style="font-size:10pt;margin-top:10px"><b>تێبینی:</b> {{ $invoice->notes }}</p>
    @endif

    <table class="sign">
        <tr>
            <td><div class="line">واژووی گەیەنەر / دابینکەر</div></td>
            <td><div class="line">واژووی وەرگر (ژوانی گەشتیاری)</div></td>
        </tr>
    </table>
</body>
</html>
