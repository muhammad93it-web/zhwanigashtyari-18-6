<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $document->title }} — ژوانی گەشتیاری</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Noto Kufi Arabic', sans-serif; box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f1f5f9; color: #1e293b; padding: 24px; }
        .sheet { background: #fff; max-width: 800px; margin: 0 auto; padding: 48px 56px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,.1); min-height: 80vh; display: flex; flex-direction: column; }
        .letterhead { text-align: center; border-bottom: 3px solid #16a34a; padding-bottom: 18px; margin-bottom: 28px; }
        .letterhead .org { font-size: 26px; font-weight: 800; color: #16a34a; }
        .letterhead .sub { font-size: 13px; color: #64748b; margin-top: 4px; }
        .meta { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 8px; font-size: 13px; color: #475569; margin-bottom: 20px; }
        .meta .label { color: #94a3b8; }
        .doc-title { text-align: center; font-size: 20px; font-weight: 700; margin: 18px 0 24px; }
        .recipient { font-size: 15px; font-weight: 600; margin-bottom: 18px; }
        .body { font-size: 15px; line-height: 2; white-space: pre-line; flex: 1; }
        .signature { margin-top: 56px; display: flex; justify-content: flex-start; }
        .signature .box { text-align: center; }
        .signature .line { width: 200px; border-top: 1px solid #94a3b8; padding-top: 8px; font-size: 13px; color: #475569; }
        .print-btn { position: fixed; top: 16px; left: 16px; background: #0891b2; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
        .print-btn:hover { background: #0e7490; }
        @media print {
            body { background: #fff; padding: 0; }
            .sheet { box-shadow: none; max-width: 100%; padding: 24px 32px; border-radius: 0; }
            .print-btn { display: none; }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">چاپکردن</button>

    <div class="sheet">
        <div class="letterhead">
            <div class="org">ژوانی گەشتیاری</div>
            <div class="sub">سیستەمی ژمێریاری</div>
        </div>

        <div class="meta">
            <div><span class="label">ژمارەی ئاماژە: </span>{{ $document->reference_number }}</div>
            <div><span class="label">بەروار: </span>{{ $document->doc_date?->format('Y-m-d') }}</div>
            @if($document->doc_type)
                <div><span class="label">جۆر: </span>{{ $document->doc_type }}</div>
            @endif
        </div>

        <div class="doc-title">{{ $document->title }}</div>

        @if($document->recipient)
            <div class="recipient">بۆ: {{ $document->recipient }}</div>
        @endif

        <div class="body">{{ $document->body }}</div>

        <div class="signature">
            <div class="box">
                <div class="line">واژوو</div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
</body>
</html>
