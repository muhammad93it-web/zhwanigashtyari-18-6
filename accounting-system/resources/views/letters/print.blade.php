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
        body {
            background: #e2e8f0;
            color: #1e293b;
            padding: 20px 20px 20px 270px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .sheet {
            --accent: #1f3a63;   /* ڕەنگی هێڵ و شێوەکان */
            --footer: #15294e;   /* ڕەنگی ژێرەوە */
            --ink:    #1a1a1a;   /* ڕەنگی دەق */
            --paper:  #ffffff;   /* ڕەنگی پاشبنەما */

            background: var(--paper);
            color: var(--ink);
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 20mm 20mm 16mm;
            box-shadow: 0 1px 8px rgba(0,0,0,.18);
            display: flex;
            flex-direction: column;
            position: relative;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ---------- سەردێڕ ---------- */
        .letterhead {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 12px;
            padding-bottom: 10px;
            border-bottom: 2.5px solid var(--accent);
        }
        .letterhead .titles { text-align: right; }
        .letterhead .org-ku { font-size: 22px; font-weight: 800; color: var(--accent); line-height: 1.35; }
        .letterhead .org-ar { font-size: 16px; font-weight: 600; color: var(--accent); line-height: 1.45; }
        .letterhead .logo-wrap { text-align: center; }
        .letterhead img { width: 86px; height: 86px; object-fit: contain; }
        .letterhead .en-title {
            text-align: left;
            font-size: 14px;
            font-weight: 700;
            color: var(--ink);
            letter-spacing: .3px;
            font-family: Georgia, 'Times New Roman', serif;
        }
        .rule2 { border-top: 1px solid var(--accent); opacity: .45; margin: 3px 0 12px; }

        /* ---------- ژمارە / بەروار ---------- */
        .meta { margin: 22px 0 6px; font-size: 15px; color: var(--ink); text-align: right; line-height: 2; }
        .meta .label { font-weight: 700; }

        /* ---------- وەرگر / بابەت (ناوەڕاست) ---------- */
        .recipient { text-align: center; font-size: 17px; font-weight: 700; color: var(--ink); margin: 28px 0 12px; }
        .subject   { text-align: center; font-size: 16px; font-weight: 700; color: var(--ink); margin-bottom: 26px; }

        .body { font-size: 15.5px; line-height: 2.15; white-space: pre-wrap; flex: 1; text-align: justify; color: var(--ink); min-height: 140px; }

        /* ---------- واژوو (لای چەپ) ---------- */
        .signature { margin-top: 50px; display: flex; justify-content: flex-end; }
        .signature .box { text-align: center; min-width: 240px; }
        .signature .sline { border-top: 1.5px dotted #555; margin-bottom: 8px; }
        .signature .role { font-size: 14px; font-weight: 700; color: var(--ink); margin-bottom: 4px; }
        .signature .name { font-size: 15px; font-weight: 700; color: var(--ink); }

        /* ---------- ژێرەوە ---------- */
        .footer {
            margin-top: 24px;
            background: var(--footer);
            color: #fff;
            border-radius: 12px;
            padding: 12px 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            direction: ltr;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: .5px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .footer .ico { width: 26px; height: 26px; border-radius: 50%; background: #fff; color: var(--footer); display: flex; align-items: center; justify-content: center; flex: none; }
        .footer .ico svg { width: 15px; height: 15px; }
        .footer .sep { opacity: .55; }

        /* ---------- دەستکاری ناوخۆیی ---------- */
        [contenteditable="true"] { outline: none; border-radius: 4px; }
        [contenteditable="true"]:hover { background: rgba(31,58,99,.05); }
        [contenteditable="true"]:focus { background: rgba(31,58,99,.08); box-shadow: 0 0 0 1px rgba(31,58,99,.25); }

        /* ---------- تابلۆی کۆنترۆڵ ---------- */
        .toolbar {
            position: fixed; top: 16px; left: 16px; width: 234px;
            background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
            box-shadow: 0 8px 28px rgba(0,0,0,.14); padding: 15px; z-index: 100; font-size: 13px;
        }
        .toolbar h3 { font-size: 14px; font-weight: 800; color: #0f172a; margin-bottom: 12px; }
        .toolbar .row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 11px; gap: 8px; }
        .toolbar .row label { color: #334155; font-weight: 600; }
        .toolbar input[type=color] { width: 44px; height: 28px; border: 1px solid #cbd5e1; border-radius: 6px; background: none; cursor: pointer; padding: 0; }
        .toolbar input[type=file] { font-size: 11px; width: 132px; }
        .toolbar .actions { display: flex; gap: 8px; margin-top: 4px; }
        .toolbar button { flex: 1; border: none; border-radius: 9px; padding: 10px 0; font-size: 13px; font-weight: 700; cursor: pointer; font-family: inherit; }
        .btn-print { background: #1f3a63; color: #fff; }
        .btn-print:hover { background: #16294a; }
        .btn-reset { background: #f1f5f9; color: #334155; }
        .btn-reset:hover { background: #e2e8f0; }
        .toolbar .hint { font-size: 11px; color: #64748b; line-height: 1.6; margin-top: 12px; border-top: 1px dashed #e2e8f0; padding-top: 10px; }
        .toolbar .back { display: block; text-align: center; margin-top: 8px; color: #1f3a63; font-weight: 700; text-decoration: none; font-size: 12px; }

        @media print {
            body { background: #fff; padding: 0; }
            .sheet { box-shadow: none; width: 100%; min-height: 100vh; margin: 0; }
            .toolbar, .no-print { display: none !important; }
            [contenteditable] { background: none !important; box-shadow: none !important; }
            @page { size: A4; margin: 0; }
        }
    </style>
</head>
<body>
    <div class="toolbar no-print">
        <h3>🎨 دەستکاری و چاپ</h3>
        <div class="row"><label>ڕەنگی دەق</label><input type="color" id="textColor"></div>
        <div class="row"><label>ڕەنگی هێڵ و شێوەکان</label><input type="color" id="accentColor"></div>
        <div class="row"><label>ڕەنگی پاشبنەما</label><input type="color" id="bgColor"></div>
        <div class="row"><label>ڕەنگی ژێرەوە</label><input type="color" id="footerColor"></div>
        <div class="row"><label>گۆڕینی لۆگۆ</label><input type="file" id="logoInput" accept="image/*"></div>
        <div class="actions">
            <button class="btn-print" onclick="window.print()">چاپکردن</button>
            <button class="btn-reset" id="resetStyle">بنەڕەت</button>
        </div>
        <div class="hint">دەتوانیت هەموو دەقەکان ڕاستەوخۆ لێرە دەستکاری بکەیت (وەک وۆرد). ڕەنگ و لۆگۆ بۆ ئەم وێبگەڕە پاشەکەوت دەکرێن.</div>
        <a class="back" href="{{ route('letters.index') }}">← گەڕانەوە بۆ لیستی نووسراوەکان</a>
    </div>

    <div class="sheet" id="sheet">
        <div class="letterhead">
            <div class="titles">
                <div class="org-ku" contenteditable="true">پرۆژەی ژوانی گەشتیاری</div>
                <div class="org-ar" contenteditable="true">مشروع ژوانی السیاحي</div>
            </div>
            <div class="logo-wrap">
                <img id="letterLogo" src="{{ $logo }}" alt="ژوانی گەشتیاری">
            </div>
            <div class="en-title" contenteditable="true">Zhwany Tourist Project</div>
        </div>
        <div class="rule2"></div>

        <div class="meta">
            <div contenteditable="true"><span class="label">ژمارە؛ </span>{{ $letter->reference_number }}</div>
            <div contenteditable="true"><span class="label">بەروار؛ </span>{{ optional($letter->letter_date)->format('Y-m-d') }}</div>
        </div>

        <div class="recipient" contenteditable="true">بۆ بەڕێز/ {{ $letter->recipient }}</div>
        <div class="subject" contenteditable="true">بابەت/ {{ $letter->subject }}</div>

        <div class="body" contenteditable="true">{{ $letter->body }}</div>

        <div class="signature">
            <div class="box">
                <div class="sline"></div>
                <div class="role" contenteditable="true">وەبەرهێنەر</div>
                <div class="name" contenteditable="true">پیرۆت ابوبکر حسێن</div>
            </div>
        </div>

        <div class="footer">
            <span class="ico">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.6 21 3 13.4 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.2.2 2.4.6 3.6.1.4 0 .8-.3 1l-2.2 2.2z"/></svg>
            </span>
            <span class="sep">|</span>
            <span class="num" contenteditable="true">0750 152 9702 - 0770 152 9702</span>
        </div>
    </div>

    <script>
        (function () {
            var sheet = document.getElementById('sheet');
            var KEY = 'jwani_letter_style_v1';
            var map = { textColor: '--ink', accentColor: '--accent', bgColor: '--paper', footerColor: '--footer' };

            function readStore() { try { return JSON.parse(localStorage.getItem(KEY)) || {}; } catch (e) { return {}; } }
            function writeStore(obj) { localStorage.setItem(KEY, JSON.stringify(obj)); }

            function toHex(c) {
                c = (c || '').trim();
                if (c.charAt(0) === '#') return c.length === 4
                    ? '#' + c[1] + c[1] + c[2] + c[2] + c[3] + c[3] : c;
                var m = c.match(/\d+/g);
                if (!m) return '#000000';
                return '#' + m.slice(0, 3).map(function (x) { return (+x).toString(16).padStart(2, '0'); }).join('');
            }

            var saved = readStore();

            Object.keys(map).forEach(function (id) {
                var input = document.getElementById(id);
                var varName = map[id];
                if (saved[varName]) {
                    sheet.style.setProperty(varName, saved[varName]);
                    input.value = saved[varName];
                } else {
                    input.value = toHex(getComputedStyle(sheet).getPropertyValue(varName));
                }
                input.addEventListener('input', function () {
                    sheet.style.setProperty(varName, input.value);
                    var s = readStore(); s[varName] = input.value; writeStore(s);
                });
            });

            if (saved.logo) document.getElementById('letterLogo').src = saved.logo;

            document.getElementById('logoInput').addEventListener('change', function (e) {
                var f = e.target.files[0];
                if (!f) return;
                var r = new FileReader();
                r.onload = function () {
                    document.getElementById('letterLogo').src = r.result;
                    var s = readStore(); s.logo = r.result; writeStore(s);
                };
                r.readAsDataURL(f);
            });

            document.getElementById('resetStyle').addEventListener('click', function () {
                localStorage.removeItem(KEY);
                location.reload();
            });
        })();
    </script>
</body>
</html>
