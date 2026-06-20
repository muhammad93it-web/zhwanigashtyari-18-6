<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نووسراو {{ $letter->reference_number }} — ژوانی گەشتیاری</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Noto Kufi Arabic', sans-serif; box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #e2e8f0; color: #1e293b; padding: 20px; }

        .sheet {
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 22mm 20mm 18mm;
            box-shadow: 0 1px 6px rgba(0,0,0,.15);
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .letterhead {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            border-bottom: 3px solid #16a34a;
            padding-bottom: 16px;
            margin-bottom: 6px;
        }
        .letterhead img { width: 78px; height: 78px; object-fit: contain; }
        .letterhead .titles { text-align: center; }
        .letterhead .org-ku { font-size: 25px; font-weight: 800; color: #16a34a; line-height: 1.3; }
        .letterhead .org-en { font-size: 14px; font-weight: 600; color: #64748b; letter-spacing: .5px; margin-top: 2px; }

        .meta { display: flex; justify-content: space-between; gap: 12px; font-size: 14px; color: #334155; margin: 26px 0 8px; }
        .meta .label { color: #94a3b8; }

        .recipient { font-size: 16px; font-weight: 700; margin: 18px 0 4px; }
        .subject { font-size: 15px; font-weight: 600; color: #16a34a; margin-bottom: 18px; }
        .subject .label { color: #94a3b8; font-weight: 500; }

        .body { font-size: 15.5px; line-height: 2.1; white-space: pre-line; flex: 1; text-align: justify; }

        .signature { margin-top: 48px; display: flex; justify-content: flex-start; }
        .signature .box { text-align: center; }
        .signature .role { font-size: 13px; color: #64748b; margin-bottom: 6px; }
        .signature .name { font-size: 15px; font-weight: 700; color: #1e293b; padding-top: 8px; border-top: 1px solid #94a3b8; min-width: 200px; }

        .footer {
            margin-top: 22px;
            border-top: 2px solid #16a34a;
            padding-top: 10px;
            text-align: center;
            font-size: 13px;
            color: #475569;
            font-weight: 600;
            letter-spacing: .5px;
            direction: ltr;
        }

        .print-btn { position: fixed; top: 16px; left: 16px; background: #0891b2; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
        .print-btn:hover { background: #0e7490; }

        @media print {
            body { background: #fff; padding: 0; }
            .sheet { box-shadow: none; width: 100%; min-height: 100vh; margin: 0; }
            .print-btn { display: none; }
            @page { size: A4; margin: 0; }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">چاپکردن</button>

    <div class="sheet">
        <div class="letterhead">
            @if($logo)
                <img src="{{ $logo }}" alt="ژوانی گەشتیاری">
            @endif
            <div class="titles">
                <div class="org-ku">پرۆژەی ژوانی گەشتیاری</div>
                <div class="org-en">Zhwany Tourist Project</div>
            </div>
        </div>

        <div class="meta">
            <div><span class="label">ژمارە: </span>{{ $letter->reference_number }}</div>
            <div><span class="label">بەروار: </span>{{ optional($letter->letter_date)->format('Y-m-d') }}</div>
        </div>

        @if($letter->recipient)
            <div class="recipient">بۆ بەڕێز: {{ $letter->recipient }}</div>
        @endif

        @if($letter->subject)
            <div class="subject"><span class="label">بابەت: </span>{{ $letter->subject }}</div>
        @endif

        <div class="body">{{ $letter->body }}</div>

        <div class="signature">
            <div class="box">
                <div class="role">وەبەرهێنەر</div>
                <div class="name">پیرۆت ابوبکرد حسێن</div>
            </div>
        </div>

        <div class="footer">0750 152 9702 - 0770 152 9702</div>
    </div>

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
</body>
</html>
