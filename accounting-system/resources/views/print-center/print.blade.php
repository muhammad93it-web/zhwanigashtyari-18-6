<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>چاپکردنی بەشەکان — ژوانی گەشتیاری</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Noto Kufi Arabic', sans-serif; box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f1f5f9; color: #1e293b; padding: 24px; }
        .sheet { background: #fff; max-width: 1000px; margin: 0 auto; padding: 40px 48px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
        .letterhead { text-align: center; border-bottom: 3px solid #16a34a; padding-bottom: 16px; margin-bottom: 12px; }
        .letterhead .org { font-size: 24px; font-weight: 800; color: #16a34a; }
        .letterhead .sub { font-size: 13px; color: #64748b; margin-top: 4px; }
        .range { text-align: center; font-size: 13px; color: #475569; margin-bottom: 28px; }
        .section { margin-bottom: 32px; }
        .section h2 { font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 10px; border-right: 4px solid #16a34a; padding-right: 10px; }
        table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
        thead th { background: #f1f5f9; text-align: right; padding: 8px 10px; font-weight: 600; color: #475569; border-bottom: 2px solid #e2e8f0; }
        tbody td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        tfoot td { padding: 8px 10px; font-weight: 700; border-top: 2px solid #e2e8f0; color: #0f172a; }
        .empty { padding: 12px; text-align: center; color: #94a3b8; font-size: 12.5px; }
        .print-btn { position: fixed; top: 16px; left: 16px; background: #0891b2; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
        .print-btn:hover { background: #0e7490; }
        @media print {
            body { background: #fff; padding: 0; }
            .sheet { box-shadow: none; max-width: 100%; padding: 16px 20px; border-radius: 0; }
            .print-btn { display: none; }
            .section { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">چاپکردن</button>

    <div class="sheet">
        <div class="letterhead">
            <div class="org">ژوانی گەشتیاری</div>
            <div class="sub">ڕاپۆرتی بەشەکان</div>
        </div>
        <div class="range">لە {{ $from }} بۆ {{ $to }}</div>

        @php
            $iqd = fn($v) => number_format((float) $v, 0);
            $usd = fn($v) => '$' . number_format((float) $v, 2);
        @endphp

        @forelse($data as $key => $section)
            @php
                $rows = $section['rows'];
                $isMaterial = in_array($key, ['purchases', 'sales']);
                $totalIqd = 0; $totalUsd = 0;
            @endphp
            <div class="section">
                <h2>{{ $section['label'] }}</h2>
                @if($rows->isEmpty())
                    <div class="empty">هیچ تۆمارێک نییە لەم ماوەیەدا.</div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>ژمارەی ئاماژە</th>
                                <th>ناو</th>
                                @if($isMaterial)
                                    <th>مەواد</th>
                                    <th>بڕ</th>
                                @endif
                                <th>دینار</th>
                                <th>دۆلار</th>
                                <th>بەروار</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $row)
                                @php
                                    $totalIqd += (float) ($row->amount_iqd ?? 0);
                                    $totalUsd += (float) ($row->amount_usd ?? 0);
                                    $name = $row->source
                                        ?? $row->payee
                                        ?? $row->party_name
                                        ?? ($row->contractor->name ?? null)
                                        ?? ($row->client->name ?? null)
                                        ?? $row->title
                                        ?? $row->description
                                        ?? '—';
                                    $date = $row->income_date
                                        ?? $row->expense_date
                                        ?? $row->debt_date
                                        ?? $row->movement_date
                                        ?? $row->payment_date
                                        ?? $row->transaction_date
                                        ?? null;
                                @endphp
                                <tr>
                                    <td>{{ $row->reference_number ?? '—' }}</td>
                                    <td>{{ $name }}</td>
                                    @if($isMaterial)
                                        <td>{{ $row->material->name ?? '—' }}</td>
                                        <td>{{ $row->quantity !== null ? number_format((float)$row->quantity, 2) : '—' }}</td>
                                    @endif
                                    <td>{{ $iqd($row->amount_iqd ?? 0) }}</td>
                                    <td>{{ $usd($row->amount_usd ?? 0) }}</td>
                                    <td>{{ $date ? \Illuminate\Support\Carbon::parse($date)->format('Y-m-d') : '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="{{ $isMaterial ? 4 : 2 }}">کۆی گشتی</td>
                                <td>{{ $iqd($totalIqd) }}</td>
                                <td>{{ $usd($totalUsd) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                @endif
            </div>
        @empty
            <div class="empty">هیچ بەشێک هەڵنەبژێردراوە.</div>
        @endforelse
    </div>

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
</body>
</html>
