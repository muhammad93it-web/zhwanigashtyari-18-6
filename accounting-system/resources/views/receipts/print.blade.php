<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>وەسڵ — {{ $transaction->reference_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Noto Kufi Arabic', sans-serif; }
        body { background: #fff; color: #111; direction: rtl; }

        @media print {
            body { width: 80mm; margin: 0; padding: 0; }
            .no-print { display: none !important; }
            @page { size: 80mm auto; margin: 0; }
        }

        .receipt {
            width: 80mm;
            margin: 0 auto;
            padding: 8mm 5mm;
            font-size: 11px;
            line-height: 1.5;
        }

        .header { text-align: center; border-bottom: 1px dashed #ccc; padding-bottom: 6mm; margin-bottom: 5mm; }
        .company-name { font-size: 16px; font-weight: 800; color: #032d40; }
        .company-sub { font-size: 10px; color: #666; margin-top: 2px; }
        .receipt-title { font-size: 13px; font-weight: 700; margin-top: 4mm; }

        .type-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            margin: 3mm auto;
        }
        .type-sale     { background: #d1fae5; color: #065f46; }
        .type-purchase { background: #fee2e2; color: #991b1b; }
        .type-debit    { background: #fef3c7; color: #92400e; }
        .type-credit   { background: #dbeafe; color: #1e40af; }

        .info-row { display: flex; justify-content: space-between; padding: 2px 0; }
        .info-label { color: #666; }
        .info-value { font-weight: 600; }

        .amount-section { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 4mm; margin: 4mm 0; text-align: center; }
        .amount-main { font-size: 22px; font-weight: 800; color: #032d40; }
        .amount-sub { font-size: 10px; color: #888; margin-top: 2px; }
        .amount-detail { display: flex; justify-content: space-between; font-size: 10px; margin-top: 3mm; padding-top: 3mm; border-top: 1px dashed #ccc; }

        .divider { border: none; border-top: 1px dashed #ccc; margin: 4mm 0; }

        .footer { text-align: center; font-size: 9px; color: #aaa; margin-top: 4mm; padding-top: 4mm; border-top: 1px dashed #ccc; }

        .rate-notice { font-size: 9px; color: #888; text-align: center; margin: 2mm 0; padding: 2mm; background: #fffbeb; border-radius: 3px; }

        .print-btn {
            display: block;
            margin: 10px auto;
            padding: 8px 24px;
            background: #032d40;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-family: 'Noto Kufi Arabic', sans-serif;
            font-size: 13px;
            font-weight: 600;
        }
    </style>
</head>
<body>

<button class="print-btn no-print" onclick="window.print()">🖨️ چاپکردن</button>

<div class="receipt">
    <!-- Header -->
    <div class="header">
        <div class="company-name">ژوانی گەشتیاری</div>
        <div class="company-sub">سیستەمی ژمێریاری — وەسڵی فەرمی</div>
        <div class="receipt-title">وەسڵی مامەڵە</div>
        <div style="text-align:center; margin-top:3mm;">
            @php
                $badgeClass = ['sale'=>'type-sale','purchase'=>'type-purchase','debit'=>'type-debit','credit'=>'type-credit'][$transaction->type] ?? '';
            @endphp
            <span class="type-badge {{ $badgeClass }}">{{ $transaction->type_name }}</span>
        </div>
    </div>

    <!-- Info -->
    <div class="info-row">
        <span class="info-label">ژمارەی وەسڵ:</span>
        <span class="info-value" style="font-size:9px;">{{ $transaction->reference_number }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">بەروار:</span>
        <span class="info-value">{{ $transaction->transaction_date->format('Y/m/d') }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">کاتی چاپ:</span>
        <span class="info-value" style="font-size:9px;">{{ now()->format('Y/m/d H:i') }}</span>
    </div>

    <hr class="divider">

    <div class="info-row">
        <span class="info-label">کڕیار:</span>
        <span class="info-value">{{ $transaction->client?->name }}</span>
    </div>
    @if($transaction->client?->phone)
    <div class="info-row">
        <span class="info-label">تەلەفۆن:</span>
        <span class="info-value">{{ $transaction->client->phone }}</span>
    </div>
    @endif

    <hr class="divider">

    <!-- Amounts -->
    <div class="amount-section">
        <div class="amount-main">
            @if($transaction->currency === 'USD')
                ${{ number_format($transaction->amount, 2) }}
            @else
                {{ number_format($transaction->amount, 0) }} د.ع
            @endif
        </div>
        <div class="amount-sub">دراوی ئەسڵی ({{ $transaction->currency }})</div>
        <div class="amount-detail">
            <span>دۆلار: ${{ number_format($transaction->amount_usd, 2) }}</span>
            <span>دینار: {{ number_format($transaction->amount_iqd, 0) }}</span>
        </div>
    </div>

    <div class="rate-notice">
        🔒 ڕێژەی گۆڕین تۆماركراو: {{ number_format($transaction->exchange_rate_usd_to_iqd, 0) }} دینار/دۆلار
    </div>

    <hr class="divider">

    <!-- Description -->
    <div class="info-row">
        <span class="info-label">وەسف:</span>
        <span class="info-value">{{ $transaction->description }}</span>
    </div>
    @if($transaction->notes)
    <div class="info-row" style="margin-top:2mm;">
        <span class="info-label">تێبینی:</span>
        <span class="info-value">{{ $transaction->notes }}</span>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div>مامەڵەکە کردە بەر: {{ $transaction->user?->name ?? 'سیستەم' }}</div>
        <div style="margin-top:2mm;">زۆر سوپاس بۆ بەکارهێنانت</div>
        <div>© {{ date('Y') }} ژوانی گەشتیاری</div>
    </div>
</div>

<button class="print-btn no-print" onclick="window.print()">🖨️ چاپکردن</button>

<script>
    // Auto print on load if ?autoprint=1
    if (new URLSearchParams(window.location.search).get('autoprint') === '1') {
        window.onload = () => setTimeout(() => window.print(), 500);
    }
</script>
</body>
</html>
