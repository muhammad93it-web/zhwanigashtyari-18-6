@php
    $num = fn($v) => number_format((float) $v, 0);
    $cur = fn($c) => $c === 'USD' ? '$' : 'د.ع';
@endphp
<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>کەشف حساب — {{ $driver->name }}</title>
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
        .summary { display: flex; gap: 16px; margin-top: 8px; }
        .summary .box { flex: 1; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px 12px; }
        .summary .box h3 { margin: 0 0 6px; font-size: 13px; color: #15803d; }
        .summary .row { display: flex; justify-content: space-between; font-size: 12px; padding: 2px 0; }
        .summary .row.bal { border-top: 1px solid #e2e8f0; margin-top: 4px; padding-top: 5px; font-weight: 700; }
        .red { color: #dc2626; }
        .green { color: #15803d; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 6px; }
        th, td { border: 1px solid #cbd5e1; padding: 7px 8px; text-align: right; }
        thead th { background: #dcfce7; color: #14532d; font-weight: 700; }
        .toolbar { text-align: center; padding: 12px; }
        .toolbar button { background: #15803d; color: #fff; border: 0; padding: 8px 22px; border-radius: 6px; font-size: 13px; cursor: pointer; }
        @media print { body { background: #fff; } .page { width: auto; min-height: auto; padding: 0; } .toolbar { display: none; } }
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
                <div class="sub">کەشف حساب — گواستنەوە و شۆفێر</div>
            </div>
        </div>
        <div class="doc-meta">
            <div>شۆفێر: <b>{{ $driver->name }}</b></div>
            @if($driver->phone)<div>مۆبایل: <b>{{ $driver->phone }}</b></div>@endif
            @if($driver->vehicle_number)<div>ئۆتۆمبێل: <b>{{ $driver->vehicle_number }}</b></div>@endif
            <div>بەروار: <b>{{ now()->format('Y-m-d') }}</b></div>
        </div>
    </div>

    <h2 class="section">پوختەی حساب</h2>
    <div class="summary">
        @foreach(['IQD' => 'دیناری عێراقی (د.ع)', 'USD' => 'دۆلاری ئەمریکی ($)'] as $code => $label)
            @php $s = $summary[$code]; $bal = (float) $s['balance']; @endphp
            <div class="box">
                <h3>{{ $label }}</h3>
                <div class="row"><span>کۆی کرێی گواستنەوە</span><span>{{ $num($s['trip']) }}</span></div>
                <div class="row"><span>کۆی پارەدان</span><span>{{ $num($s['payment']) }}</span></div>
                <div class="row bal">
                    @if($bal > 0)
                        <span>ئەوەی دەبێ بیدەین</span><span class="red">{{ $num($bal) }}</span>
                    @elseif($bal < 0)
                        <span>ئەوەی لای ئەوەیە بۆ ئێمە</span><span class="green">{{ $num(abs($bal)) }}</span>
                    @else
                        <span>دۆخ</span><span>تەسفیە (٠)</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <h2 class="section">مامەڵەکان</h2>
    <table>
        <thead>
            <tr>
                <th>بەروار</th>
                <th>جۆر</th>
                <th>وەسف</th>
                <th>دراو</th>
                <th>بڕ</th>
                <th>باڵانس دوای مامەڵە</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $t)
                <tr>
                    <td>{{ optional($t->date)->format('Y-m-d') }}</td>
                    <td>{{ $t->type_name }}</td>
                    <td>{{ $t->description ?: '—' }}</td>
                    <td>{{ $cur($t->currency) }}</td>
                    <td>{{ ($t->type == 'payment' ? '−' : '+') . $num($t->amount) }} {{ $cur($t->currency) }}</td>
                    <td>{{ $num($t->balance_after) }} {{ $cur($t->currency) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center;color:#94a3b8;">هیچ مامەڵەیەک نییە.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
</body>
</html>
