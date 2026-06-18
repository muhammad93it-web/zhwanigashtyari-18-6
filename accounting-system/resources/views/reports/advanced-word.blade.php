<!DOCTYPE html>
<html dir="rtl" lang="ku">
<head>
<meta charset="UTF-8">
<style>
body { font-family: 'Calibri', 'Arial', sans-serif; font-size: 11pt; direction: rtl; }
h1 { font-size: 16pt; color: #166534; text-align: center; margin-bottom: 4px; }
p.meta { font-size: 9pt; color: #666; text-align: center; margin-bottom: 20px; }
table { width: 100%; border-collapse: collapse; font-size: 10pt; }
th { background-color: #166534; color: #fff; padding: 6px 8px; text-align: center; border: 1px solid #ccc; }
td { padding: 5px 8px; border: 1px solid #ddd; text-align: right; }
tr:nth-child(even) td { background-color: #f9fafb; }
.total-row td { font-weight: bold; background-color: #dcfce7; border-top: 2px solid #166534; }
</style>
</head>
<body>
<h1>{{ $title }}</h1>
<p class="meta">{{ $fromDate }} — {{ $toDate }} &nbsp;|&nbsp; ژوانی گەشتیاری</p>

<table>
    <thead>
        <tr>
            @foreach($headings as $h)
            <th>{{ $h }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            @foreach($columns as $col)
            <td>{{ is_callable($col) ? $col($row) : data_get($row, $col, '—') }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="{{ count($headings) - 1 }}" style="text-align:center">کۆی گشتی — {{ $rows->count() }} تۆمار</td>
            <td>—</td>
        </tr>
    </tfoot>
</table>
</body>
</html>
