<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>لیستی مامەڵەکان — {{ $periodLabel }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, 'Noto Naskh Arabic', sans-serif; direction: rtl; color: #1e293b; background: #f8fafc; margin: 0; padding: 24px; }
        h1 { font-size: 20px; margin: 0 0 4px; color: #0f766e; }
        .sub { color: #64748b; font-size: 13px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; background: #fff; font-size: 13px; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        th, td { border: 1px solid #e2e8f0; padding: 8px 10px; text-align: right; }
        th { background: #0f766e; color: #fff; font-weight: 600; white-space: nowrap; }
        tr:nth-child(even) td { background: #f1f5f9; }
        .num { font-variant-numeric: tabular-nums; white-space: nowrap; }
        .empty { padding: 32px; text-align: center; color: #94a3b8; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; }
        tfoot td { font-weight: 700; background: #ecfeff; }
    </style>
</head>
<body>
    <h1>لیستی مامەڵە و وەسڵەکان</h1>
    <div class="sub">{{ $periodLabel }} — لە {{ $from }} بۆ {{ $to }} — کۆی گشتی: {{ $rows->count() }} مامەڵە</div>

    @if($rows->isEmpty())
        <div class="empty">هیچ مامەڵەیەک لەم ماوەیەدا نییە.</div>
    @else
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>بەروار</th>
                <th>جۆر</th>
                <th>کڕیار / کەس</th>
                <th>ژمارەی وەسڵ</th>
                <th>دۆلار ($)</th>
                <th>دینار (د.ع)</th>
                <th>تێبینی</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $i => $t)
            <tr>
                <td class="num">{{ $i + 1 }}</td>
                <td class="num">{{ $t->transaction_date?->format('Y-m-d') }}</td>
                <td>{{ $t->type_name }}</td>
                <td>{{ $t->client?->name ?? '—' }}</td>
                <td class="num">{{ $t->reference_number ?? '—' }}</td>
                <td class="num">{{ number_format((float) $t->amount_usd, 2) }}</td>
                <td class="num">{{ number_format((float) $t->amount_iqd, 0) }}</td>
                <td>{{ $t->description ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">کۆی گشتی</td>
                <td class="num">{{ number_format((float) $rows->sum('amount_usd'), 2) }}</td>
                <td class="num">{{ number_format((float) $rows->sum('amount_iqd'), 0) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    @endif
</body>
</html>
