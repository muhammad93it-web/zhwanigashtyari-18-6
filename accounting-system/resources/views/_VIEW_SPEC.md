# VIEW SPEC — Jwani accounting (light POS theme)

ALL views are Laravel Blade, RTL Kurdish (Sorani). Read this fully before building. Match the dashboard reference: `resources/views/dashboard/index.blade.php`.

## Blade contract — every page view starts with:
```blade
@extends('layouts.app')
@section('title', '...')
@section('page-title', '...')
@section('page-subtitle', '...')   {{-- optional --}}
@section('content')
... your content ...
@endsection
```
Print views (documents.print, print-center.print) DO NOT extend layout — they are standalone HTML with their own `<style>` and `window.print()`.

## Design system — use ONLY these component classes (already defined in layout):
- `.card` — white card (border slate-200, shadow-sm, rounded-xl). Add padding with `p-5` etc.
- `.stat-card` — card + padding + flex column for KPI tiles
- Buttons: `.btn-primary` (green=save/pay), `.btn-info` (cyan=view/print), `.btn-danger` (red=delete/cancel), `.btn-warning` (amber=add/edit-warn), `.btn-slate` (neutral), `.btn-outline` (white bordered)
- Forms: `.input-field` (inputs/selects/textarea), `.label` (field labels)
- Tables: wrap in `.card`, use `<table class="w-full text-sm">`; rows use class `table-row`; header `<thead><tr class="text-right text-xs text-slate-500 border-b border-slate-200">` with `<th class="px-4 py-3 font-semibold">`.
- Badges: `.badge-green .badge-red .badge-amber .badge-cyan .badge-slate`
- Colors: green-600 primary, cyan-600 info, red-500 danger, amber-500 add/warn, slate neutrals. bg already light slate.

## Conventions
- Money display: IQD → `number_format($v, 0)` + ' د'; USD → `'$' . number_format($v, 2)`. Define helper at top of view: `@php $iqd = fn($v) => number_format((float)$v, 0); @endphp`
- Dates: `$model->some_date->format('Y-m-d')` (Carbon casts already set).
- Always include `@csrf` in POST forms; `@method('PUT')`/`@method('DELETE')` where needed.
- Validation errors: show with `@error('field') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror`; repopulate with `old('field', $model->field ?? '')`.
- Delete buttons: small form with `onsubmit="return confirm('دڵنیایت لە سڕینەوە؟')"`.
- Empty states: centered muted message inside the card when collection is empty.
- Pagination: `{{ $items->links() }}` below tables (Laravel default paginator; it's fine).
- Responsive: tables scroll on mobile via `<div class="overflow-x-auto">`. Grids: `grid-cols-1 sm:grid-cols-2 lg:grid-cols-3/4`.
- Page header action button (e.g. "+ زیادکردن") goes in a flex row at top of content: `<div class="flex items-center justify-between mb-4">`.

## Currency form pattern (for money create/edit forms)
Two fields: a `<select name="currency">` with USD/IQD options, and `<input name="amount" type="number" step="0.01">`. Show the current locked rate as a hint: "ڕێژەی ئێستا: {{ number_format($currentRate,0) }} دینار بۆ ١ دۆلار". `$currentRate` (float) is passed to create/edit views.

## DATA SHAPES per view (controller-provided variables)

### Finance
**incomes.index**: `$incomes` (paginator of Income), `$totals` (->usd, ->iqd, ->c). Income fields: source, category, currency, amount, amount_usd, amount_iqd, description, reference_number, income_date, notes. Routes: incomes.create, incomes.show/edit/destroy (model `income`).
**incomes.create / incomes.edit**: `$currentRate` (float); edit also `$income`. Form fields: source(required), category, currency(USD/IQD), amount(required), description, income_date(required date), notes. POST incomes.store / PUT incomes.update.
**incomes.show**: `$income`. Show all fields + ref number + locked rate + both currency amounts. Buttons: edit (warning), delete (danger), back.

**expenses.*** — identical to incomes but model `expense`, field `payee` instead of `source`, date `expense_date`. Routes expenses.*.

**debts.index**: `$debts` (paginator), `$receivable` (->usd,->iqd), `$payable` (->usd,->iqd). Debt fields: party_name, direction (receivable|payable), currency, amount, amount_usd, amount_iqd, status (open|paid), description, debt_date, due_date(nullable), paid_date(nullable). Use `$debt->direction_name`, `$debt->status_name` accessors. Show two summary stat-cards (receivable green, payable red). Each open debt row has a "نیشانکردن وەک دراوەتەوە" button → POST `debts.mark-paid` (debt). Status badge: open=amber, paid=green.
**debts.create/edit**: `$currentRate`; edit also `$debt`. Fields: party_name, direction(select: receivable=قەرزی لای خەڵک, payable=قەرزی ئێمە), currency, amount, description, debt_date, due_date, notes.
**debts.show**: `$debt` + mark-paid button if open.

### Trading & Inventory
**materials.index**: `$materials` (paginator of Material), `$totals['count'], $totals['low_stock']`. Material fields: name, unit, category, current_stock, min_stock(nullable), notes, is_active. Accessor `$material->is_low_stock` (bool). Show stock with unit; if low_stock show amber badge "کۆگای کەم". Buttons per row: show(info), edit(warning), delete(danger). Top actions: + مەواد نوێ (materials.create), کڕین (materials.buy, amber), فرۆشتن (materials.sell, cyan).
**materials.create/edit**: edit gets `$material`. Fields: name(required), unit(required, e.g. مەتر/دانە/کیلۆ), category, current_stock(create only, numeric), min_stock(numeric), notes, is_active(checkbox, default checked). POST materials.store / PUT materials.update.
**materials.show**: `$material`, `$movements` (paginator of MaterialMovement with user). Show material info + stock + a table of movements (type_name accessor, quantity, unit_price, amount with currency, party_name, movement_date). Each movement row delete → DELETE `movements.destroy` (movement). Buttons: کڕین/فرۆشتن links (materials.buy / materials.sell), edit.
**movements.create**: `$type` ('purchase'|'sale'), `$materials` (active), `$clients` (id,name), `$currentRate`. Title changes by type (کڕینی مەواد / فرۆشتنی مەواد). Form POST `movements.store` with hidden `type`. Fields: material_id(select required), quantity(required numeric), unit_price(required numeric), currency(USD/IQD), party_name(دابینکەر/کڕیار), client_id(optional select), movement_date(required), notes. Show live total = quantity*unit_price via small JS (optional).

### Contractors
**contractors.index**: `$contractors` (paginator, withCount payments → `$c->payments_count`). Contractor fields: name, phone, work_type (per_meter|contract), rate_per_meter, contract_amount, currency, notes, is_active. Accessor `$contractor->work_type_name`. Badge for work_type. Buttons: show/edit/delete + "+ وەستای نوێ".
**contractors.create/edit**: edit gets `$contractor`. Fields: name(required), phone, work_type(select: per_meter=بە مەتر, contract=قۆنتەرات), rate_per_meter(numeric, show when per_meter), contract_amount(numeric, show when contract), currency, notes, is_active(checkbox). Optional JS to toggle rate/contract fields by work_type.
**contractors.show**: `$contractor`, `$payments` (paginator with user), `$paid` (->usd,->iqd,->m = total meters). Show contractor info + totals + payments table (amount w/ currency, meters, description, payment_date) with delete per row → DELETE contractor-payments.destroy (contractorPayment param: `contractor_payment`). Button "+ پارەدان" → contractor-payments.create?contractor_id={{id}}.
**contractor-payments.index**: `$payments` (paginator with contractor), `$contractors` (id,name) for filter, `$totals`(->usd,->iqd,->c). Table: contractor name, amount w/ currency, meters, payment_date, description. Filter by contractor_id (GET form).
**contractor-payments.create**: `$contractors` (active), `$currentRate`, `$selected` (Contractor or null). Fields: contractor_id(select required, preselect $selected), currency, amount(required), meters(numeric, optional — for per_meter), description, payment_date(required), notes. POST contractor-payments.store.

### Administration
**documents.index**: `$documents` (paginator). Document fields: title, doc_type, reference_number, recipient, body, doc_date, notes. Table: title, doc_type, recipient, doc_date. Buttons: show(info), print(cyan → documents.print), edit, delete. "+ نووسراو نوێ".
**documents.create/edit**: edit gets `$document`. Fields: title(required), doc_type, recipient(بۆ کێ), body(textarea, large), doc_date(required), notes. POST documents.store / PUT documents.update.
**documents.show**: `$document`. Show all fields nicely + buttons: print(documents.print, cyan), edit, delete, back.
**documents.print**: `$document`. STANDALONE printable HTML (no layout). RTL, Noto Kufi font via Google Fonts link, clean letterhead with "ژوانی گەشتیاری" header, title, recipient, doc_date, ref number, body (preserve line breaks with nl2br/whitespace-pre-line), signature line at bottom. Auto `window.print()` on load + a "چاپکردن" button (hidden in print via @media print).
**print-center.index**: `$sections` (assoc array key=>label). A form GET to `print-center.print` with: from_date, to_date date inputs, and checkboxes for each section (name="sections[]" value=key, all checked by default). Submit button "چاپکردن" (cyan). Open in new tab (target="_blank").
**print-center.print**: `$data` (assoc: key => ['label'=>.., 'rows'=>Collection]), `$from`, `$to`. STANDALONE printable HTML (no layout). Header "ژوانی گەشتیاری" + date range. For each section render a titled table of its rows. Row columns differ per section — render a generic table: ref number, main name field, amount (amount_iqd + amount_usd), date. For purchases/sales also material name + quantity. Show per-section totals. Auto window.print(). Hide print button via @media print.

### Reports (new)
**reports.daily**: `$date` (string), `$incomes`,`$expenses`,`$purchases`(with material),`$sales`(with material),`$payments`(with contractor) collections, `$totals` (in_iqd,out_iqd,net_iqd). Top: date picker (GET form, name="date"). Three stat-cards: داهات (green), خەرجی (red), ساف (green/red). Then a section per group with a small table (only if non-empty). Print button (cyan) that does window.print(); add @media print to hide nav/sidebar — wrap printable area, but since it extends layout, just provide a "چاپکردن" button calling window.print().
**reports.summary**: `$rows` (assoc: key=>['label'=>, 'data'=>obj(->usd,->iqd,->c), 'flow'=>'in'|'out']), `$totals`(in_iqd,out_iqd,net_iqd), `$from`,`$to`. Date range GET form (from_date,to_date). Table of rows: label, count, IQD, USD, with green text for flow=in and red for flow=out. Footer totals row. Three stat-cards for totals.
**reports.project-cost**: `$costs` (assoc key=>['label','data']), `$income` (assoc key=>['label','data']), `$totals` (cost_iqd,cost_usd,income_iqd,income_usd,net_iqd,net_usd), `$from`,`$to`. Date range GET form. Two cards: تێچوو (sum of costs, red) and داهات (sum of income, green), plus net card. Tables listing each cost line and each income line.

## NOTE on existing-views redesign
The existing views (clients/*, transactions/*, exchange-rates/index, reports/index, reports/client, receipts/*, auth/*) currently use a DARK theme (bg-slate-800/900, text-white, etc). They must be converted to this LIGHT theme using the component classes above. Keep all existing functionality, route names, form fields, and variable usage EXACTLY — only restyle. Read each file first, preserve logic, swap dark classes for light card/table/btn classes.
