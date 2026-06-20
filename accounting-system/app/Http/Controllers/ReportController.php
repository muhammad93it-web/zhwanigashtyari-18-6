<?php

namespace App\Http\Controllers;

use App\Exports\AdvancedReportExport;
use App\Exports\ClientTransactionsExport;
use App\Exports\TransactionsExport;
use App\Models\Client;
use App\Models\Contractor;
use App\Models\ContractorPayment;
use App\Models\Debt;
use App\Models\Expense;
use App\Models\Income;
use App\Models\LaborPayment;
use App\Models\Material;
use App\Models\MaterialMovement;
use App\Models\Project;
use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate   = $request->get('to_date', now()->format('Y-m-d'));
        $clientId = $request->get('client_id');

        $query = Transaction::with('client')
            ->whereDate('transaction_date', '>=', $fromDate)
            ->whereDate('transaction_date', '<=', $toDate);

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        $transactions = $query->latest('transaction_date')->get();

        $summary = $transactions->groupBy('type')->map(fn($group) => [
            'count'     => $group->count(),
            'total_usd' => $group->sum('amount_usd'),
            'total_iqd' => $group->sum('amount_iqd'),
        ]);

        $netUsd = $transactions->sum(fn($t) => in_array($t->type, ['sale', 'debit']) ? $t->amount_usd : -$t->amount_usd);
        $netIqd = $transactions->sum(fn($t) => in_array($t->type, ['sale', 'debit']) ? $t->amount_iqd : -$t->amount_iqd);

        $clients = Client::orderBy('name')->get(['id', 'name']);

        return view('reports.index', compact(
            'transactions', 'summary', 'netUsd', 'netIqd',
            'clients', 'fromDate', 'toDate', 'clientId'
        ));
    }

    public function clientReport(Client $client)
    {
        $transactions = $client->transactions()->with('user')->latest('transaction_date')->get();

        $balances = [
            'sales_usd'     => $transactions->where('type', 'sale')->sum('amount_usd'),
            'purchases_usd' => $transactions->where('type', 'purchase')->sum('amount_usd'),
            'debits_usd'    => $transactions->where('type', 'debit')->sum('amount_usd'),
            'credits_usd'   => $transactions->where('type', 'credit')->sum('amount_usd'),
        ];

        $balances['net_usd'] = ($balances['sales_usd'] + $balances['debits_usd'])
            - ($balances['purchases_usd'] + $balances['credits_usd']);

        return view('reports.client', compact('client', 'transactions', 'balances'));
    }

    /** ڕاپۆرتی ڕۆژانە */
    public function daily(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));

        $incomes  = Income::whereDate('income_date', $date)->latest()->get();
        $expenses = Expense::whereDate('expense_date', $date)->latest()->get();
        $purchases = MaterialMovement::with('material')->where('type', 'purchase')->whereDate('movement_date', $date)->get();
        $sales     = MaterialMovement::with('material')->where('type', 'sale')->whereDate('movement_date', $date)->get();
        $payments  = ContractorPayment::with('contractor')->whereDate('payment_date', $date)->get();

        $in = $incomes->sum('amount_iqd') + $sales->sum('amount_iqd');
        $out = $expenses->sum('amount_iqd') + $purchases->sum('amount_iqd') + $payments->sum('amount_iqd');

        $totals = [
            'in_iqd'  => $in,
            'out_iqd' => $out,
            'net_iqd' => $in - $out,
        ];

        return view('reports.daily', compact('date', 'incomes', 'expenses', 'purchases', 'sales', 'payments', 'totals'));
    }

    /** کۆی هەموو بەشەکان */
    public function summary(Request $request)
    {
        $from = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $to   = $request->get('to_date', now()->format('Y-m-d'));

        $agg = fn($q, $col) => $q->whereBetween($col, [$from, $to])
            ->selectRaw('SUM(amount_usd) usd, SUM(amount_iqd) iqd, COUNT(*) c')->first();

        $rows = [
            'incomes'   => ['label' => 'وەرگرتنی پارە',   'data' => $agg(Income::query(), 'income_date'),  'flow' => 'in'],
            'sales'     => ['label' => 'فرۆشتنی مەواد',    'data' => $agg(MaterialMovement::where('type', 'sale'), 'movement_date'), 'flow' => 'in'],
            'expenses'  => ['label' => 'خەرجکردنی پارە',   'data' => $agg(Expense::query(), 'expense_date'), 'flow' => 'out'],
            'purchases' => ['label' => 'کڕینی مەواد',      'data' => $agg(MaterialMovement::where('type', 'purchase'), 'movement_date'), 'flow' => 'out'],
            'payments'  => ['label' => 'پارەدانی وەستا',   'data' => $agg(ContractorPayment::query(), 'payment_date'), 'flow' => 'out'],
        ];

        $totalIn = $rows['incomes']['data']->iqd + $rows['sales']['data']->iqd;
        $totalOut = $rows['expenses']['data']->iqd + $rows['purchases']['data']->iqd + $rows['payments']['data']->iqd;

        $totals = [
            'in_iqd'  => $totalIn,
            'out_iqd' => $totalOut,
            'net_iqd' => $totalIn - $totalOut,
        ];

        return view('reports.summary', compact('rows', 'totals', 'from', 'to'));
    }

    /** تێچووی گشتیی پڕۆژە */
    public function projectCost(Request $request)
    {
        $from = $request->get('from_date');
        $to   = $request->get('to_date');

        $apply = function ($q, $col) use ($from, $to) {
            if ($from) $q->whereDate($col, '>=', $from);
            if ($to) $q->whereDate($col, '<=', $to);
            return $q->selectRaw('SUM(amount_usd) usd, SUM(amount_iqd) iqd, COUNT(*) c')->first();
        };

        $costs = [
            'expenses'  => ['label' => 'خەرجی گشتی',     'data' => $apply(Expense::query(), 'expense_date')],
            'purchases' => ['label' => 'کڕینی مەواد',     'data' => $apply(MaterialMovement::where('type', 'purchase'), 'movement_date')],
            'payments'  => ['label' => 'پارەدانی وەستا',  'data' => $apply(ContractorPayment::query(), 'payment_date')],
        ];

        $income = [
            'incomes' => ['label' => 'وەرگرتنی پارە', 'data' => $apply(Income::query(), 'income_date')],
            'sales'   => ['label' => 'فرۆشتنی مەواد',  'data' => $apply(MaterialMovement::where('type', 'sale'), 'movement_date')],
        ];

        $totalCostIqd = collect($costs)->sum(fn($r) => (float) $r['data']->iqd);
        $totalCostUsd = collect($costs)->sum(fn($r) => (float) $r['data']->usd);
        $totalIncomeIqd = collect($income)->sum(fn($r) => (float) $r['data']->iqd);
        $totalIncomeUsd = collect($income)->sum(fn($r) => (float) $r['data']->usd);

        $totals = [
            'cost_iqd'   => $totalCostIqd,
            'cost_usd'   => $totalCostUsd,
            'income_iqd' => $totalIncomeIqd,
            'income_usd' => $totalIncomeUsd,
            'net_iqd'    => $totalIncomeIqd - $totalCostIqd,
            'net_usd'    => $totalIncomeUsd - $totalCostUsd,
        ];

        return view('reports.project-cost', compact('costs', 'income', 'totals', 'from', 'to'));
    }

    /* ==================== ADVANCED REPORTS ==================== */

    public function advanced(Request $request)
    {
        $section  = $request->get('section', 'incomes');
        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate   = $request->get('to_date', now()->format('Y-m-d'));

        $results = collect();
        $totals  = ['iqd' => 0.0, 'usd' => 0.0, 'count' => 0];

        /** @var \Illuminate\Database\Eloquent\Builder|null $q */
        $q       = null;
        $dateCol = null;
        $iqdCol  = 'amount_iqd';
        $usdCol  = 'amount_usd';

        switch ($section) {

            case 'incomes':
                $q = Income::with('user')
                    ->whereBetween('income_date', [$fromDate, $toDate]);
                if ($request->filled('search'))
                    $q->where(fn($qq) => $qq->where('source', 'like', '%' . $request->search . '%')
                        ->orWhere('category', 'like', '%' . $request->search . '%'));
                $dateCol = 'income_date';
                break;

            case 'expenses':
                $q = Expense::with('user', 'project')
                    ->whereBetween('expense_date', [$fromDate, $toDate]);
                if ($request->filled('search'))
                    $q->where(fn($qq) => $qq->where('payee', 'like', '%' . $request->search . '%')
                        ->orWhere('category', 'like', '%' . $request->search . '%')
                        ->orWhere('description', 'like', '%' . $request->search . '%'));
                if ($request->filled('project_id'))
                    $q->where('project_id', $request->project_id);
                $dateCol = 'expense_date';
                break;

            case 'material_purchases':
                $q = MaterialMovement::with('material', 'user')
                    ->where('type', 'purchase')
                    ->whereBetween('movement_date', [$fromDate, $toDate]);
                if ($request->filled('material_id'))
                    $q->where('material_id', $request->material_id);
                if ($request->filled('search'))
                    $q->where('party_name', 'like', '%' . $request->search . '%');
                $dateCol = 'movement_date';
                break;

            case 'material_sales':
                $q = MaterialMovement::with('material', 'user')
                    ->where('type', 'sale')
                    ->whereBetween('movement_date', [$fromDate, $toDate]);
                if ($request->filled('material_id'))
                    $q->where('material_id', $request->material_id);
                if ($request->filled('search'))
                    $q->where('party_name', 'like', '%' . $request->search . '%');
                $dateCol = 'movement_date';
                break;

            case 'purchase_invoices':
                $q = PurchaseInvoice::with('supplier', 'project', 'user')
                    ->whereBetween('date', [$fromDate, $toDate]);
                if ($request->filled('supplier_id'))
                    $q->where('supplier_id', $request->supplier_id);
                if ($request->filled('project_id'))
                    $q->where('project_id', $request->project_id);
                if ($request->filled('search'))
                    $q->where(fn($qq) => $qq->where('deliverer_name', 'like', '%' . $request->search . '%')
                        ->orWhere('notes', 'like', '%' . $request->search . '%'));
                $dateCol = 'date';
                $iqdCol  = 'total_iqd';
                $usdCol  = 'total_usd';
                break;

            case 'contractor_payments':
                $q = ContractorPayment::with('contractor', 'user')
                    ->whereBetween('payment_date', [$fromDate, $toDate]);
                if ($request->filled('contractor_id'))
                    $q->where('contractor_id', $request->contractor_id);
                if ($request->filled('search'))
                    $q->where('description', 'like', '%' . $request->search . '%');
                $dateCol = 'payment_date';
                break;

            case 'labor_payments':
                $q = LaborPayment::with('worker', 'project', 'user')
                    ->whereBetween('date', [$fromDate, $toDate]);
                if ($request->filled('worker_id'))
                    $q->where('worker_id', $request->worker_id);
                if ($request->filled('project_id'))
                    $q->where('project_id', $request->project_id);
                if ($request->filled('search'))
                    $q->where(fn($qq) => $qq->where('worker_name', 'like', '%' . $request->search . '%')
                        ->orWhere('role', 'like', '%' . $request->search . '%'));
                $dateCol = 'date';
                // labor_payments don't have amount_iqd/amount_usd — just amount + currency
                $iqdCol  = null;
                $usdCol  = null;
                break;

            case 'transactions':
                $q = Transaction::with('client', 'user')
                    ->whereBetween('transaction_date', [$fromDate, $toDate]);
                if ($request->filled('client_id'))
                    $q->where('client_id', $request->client_id);
                if ($request->filled('type'))
                    $q->where('type', $request->type);
                if ($request->filled('search'))
                    $q->where(fn($qq) => $qq->where('description', 'like', '%' . $request->search . '%')
                        ->orWhere('reference_number', 'like', '%' . $request->search . '%'));
                $dateCol = 'transaction_date';
                break;
        }

        if ($q) {
            // Totals via SQL aggregate over the FULL filtered set (not just the page).
            if ($iqdCol) {
                $agg = (clone $q)->setEagerLoads([])
                    ->selectRaw("SUM($iqdCol) iqd, SUM($usdCol) usd, COUNT(*) c")->first();
                $totals = [
                    'iqd'   => (float) ($agg->iqd ?? 0),
                    'usd'   => (float) ($agg->usd ?? 0),
                    'count' => (int) ($agg->c ?? 0),
                ];
            } else {
                $totals = ['iqd' => 0.0, 'usd' => 0.0, 'count' => (clone $q)->setEagerLoads([])->count()];
            }

            $results = $q->orderByDesc($dateCol)->paginate(50)->withQueryString();
        }

        $filterOptions = [
            'suppliers'   => Supplier::orderBy('name')->get(['id', 'name']),
            'contractors' => Contractor::orderBy('name')->get(['id', 'name']),
            'workers'     => Worker::orderBy('name')->get(['id', 'name']),
            'materials'   => Material::orderBy('name')->get(['id', 'name']),
            'projects'    => Project::orderBy('name')->get(['id', 'name']),
            'clients'     => Client::orderBy('name')->get(['id', 'name']),
        ];

        return view('reports.advanced', compact('results', 'totals', 'section', 'fromDate', 'toDate', 'filterOptions'));
    }

    public function exportAdvancedExcel(Request $request)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate   = $request->get('to_date', now()->format('Y-m-d'));

        [$rows, $headings, $columns, $title] = $this->buildExportData($request);

        $filename = $title . '_' . $fromDate . '_' . $toDate . '.xlsx';
        return Excel::download(new AdvancedReportExport($rows, $headings, $columns, $title), $filename);
    }

    public function exportAdvancedWord(Request $request)
    {
        [$rows, $headings, $columns, $title] = $this->buildExportData($request);

        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate   = $request->get('to_date', now()->format('Y-m-d'));

        $html = view('reports.advanced-word', compact('rows', 'headings', 'columns', 'title', 'fromDate', 'toDate'))->render();

        $filename = $title . '_' . $fromDate . '_' . $toDate . '.doc';

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-word; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Returns [$rows, $headings, $columns, $title] for export.
     */
    private function buildExportData(Request $request): array
    {
        $section  = $request->get('section', 'incomes');
        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate   = $request->get('to_date', now()->format('Y-m-d'));

        $fmt_iqd = fn($v) => number_format((float) $v, 0);
        $fmt_usd = fn($v) => number_format((float) $v, 2);

        switch ($section) {
            case 'incomes':
                $rows = Income::with('user')
                    ->whereBetween('income_date', [$fromDate, $toDate])
                    ->when($request->filled('search'), fn($q) => $q->where('source', 'like', '%' . $request->search . '%'))
                    ->orderByDesc('income_date')->get();
                $headings = ['بەروار', 'سەرچاوە', 'جۆر', 'دراو', 'بڕ', 'بەدینار', 'تێبینی'];
                $columns  = [
                    fn($r) => $r->income_date->format('Y-m-d'),
                    fn($r) => $r->source,
                    fn($r) => $r->category ?? '—',
                    fn($r) => $r->currency,
                    fn($r) => $fmt_usd($r->amount),
                    fn($r) => $fmt_iqd($r->amount_iqd),
                    fn($r) => $r->notes ?? '',
                ];
                $title = 'وەرگرتنی پارە';
                break;

            case 'expenses':
                $rows = Expense::with('user', 'project')
                    ->whereBetween('expense_date', [$fromDate, $toDate])
                    ->when($request->filled('project_id'), fn($q) => $q->where('project_id', $request->project_id))
                    ->when($request->filled('search'), fn($q) => $q->where('payee', 'like', '%' . $request->search . '%'))
                    ->orderByDesc('expense_date')->get();
                $headings = ['بەروار', 'وەرگر', 'جۆر', 'پڕۆژە', 'دراو', 'بڕ', 'بەدینار'];
                $columns  = [
                    fn($r) => $r->expense_date->format('Y-m-d'),
                    fn($r) => $r->payee,
                    fn($r) => $r->category ?? '—',
                    fn($r) => $r->project?->name ?? '—',
                    fn($r) => $r->currency,
                    fn($r) => $fmt_usd($r->amount),
                    fn($r) => $fmt_iqd($r->amount_iqd),
                ];
                $title = 'خەرجکردنی پارە';
                break;

            case 'material_purchases':
                $rows = MaterialMovement::with('material', 'user')
                    ->where('type', 'purchase')
                    ->whereBetween('movement_date', [$fromDate, $toDate])
                    ->when($request->filled('material_id'), fn($q) => $q->where('material_id', $request->material_id))
                    ->orderByDesc('movement_date')->get();
                $headings = ['بەروار', 'مەواد', 'دابینکەر', 'بڕ', 'نرخی یەکە', 'دراو', 'کۆ', 'بەدینار'];
                $columns  = [
                    fn($r) => $r->movement_date->format('Y-m-d'),
                    fn($r) => $r->material?->name ?? '—',
                    fn($r) => $r->party_name ?? '—',
                    fn($r) => $fmt_usd($r->quantity),
                    fn($r) => $fmt_usd($r->unit_price),
                    fn($r) => $r->currency,
                    fn($r) => $fmt_usd($r->amount),
                    fn($r) => $fmt_iqd($r->amount_iqd),
                ];
                $title = 'کڕینی مەواد';
                break;

            case 'material_sales':
                $rows = MaterialMovement::with('material', 'user')
                    ->where('type', 'sale')
                    ->whereBetween('movement_date', [$fromDate, $toDate])
                    ->when($request->filled('material_id'), fn($q) => $q->where('material_id', $request->material_id))
                    ->orderByDesc('movement_date')->get();
                $headings = ['بەروار', 'مەواد', 'کڕیار', 'بڕ', 'نرخی یەکە', 'دراو', 'کۆ', 'بەدینار'];
                $columns  = [
                    fn($r) => $r->movement_date->format('Y-m-d'),
                    fn($r) => $r->material?->name ?? '—',
                    fn($r) => $r->party_name ?? '—',
                    fn($r) => $fmt_usd($r->quantity),
                    fn($r) => $fmt_usd($r->unit_price),
                    fn($r) => $r->currency,
                    fn($r) => $fmt_usd($r->amount),
                    fn($r) => $fmt_iqd($r->amount_iqd),
                ];
                $title = 'فرۆشتنی مەواد';
                break;

            case 'purchase_invoices':
                $rows = PurchaseInvoice::with('supplier', 'project', 'user')
                    ->whereBetween('date', [$fromDate, $toDate])
                    ->when($request->filled('supplier_id'), fn($q) => $q->where('supplier_id', $request->supplier_id))
                    ->when($request->filled('project_id'), fn($q) => $q->where('project_id', $request->project_id))
                    ->orderByDesc('date')->get();
                $headings = ['بەروار', 'دابینکەر', 'پڕۆژە', 'کۆی گشتی (د)', 'پارەدراو (د)', 'ماوە (د)', 'تێبینی'];
                $columns  = [
                    fn($r) => $r->date->format('Y-m-d'),
                    fn($r) => $r->party_name,
                    fn($r) => $r->project?->name ?? '—',
                    fn($r) => $fmt_iqd($r->total_iqd),
                    fn($r) => $fmt_iqd($r->paid_iqd),
                    fn($r) => $fmt_iqd($r->remaining_iqd),
                    fn($r) => $r->notes ?? '',
                ];
                $title = 'کڕینی بە وەسڵ';
                break;

            case 'contractor_payments':
                $rows = ContractorPayment::with('contractor', 'user')
                    ->whereBetween('payment_date', [$fromDate, $toDate])
                    ->when($request->filled('contractor_id'), fn($q) => $q->where('contractor_id', $request->contractor_id))
                    ->orderByDesc('payment_date')->get();
                $headings = ['بەروار', 'وەستا', 'مەتر', 'دراو', 'بڕ', 'بەدینار'];
                $columns  = [
                    fn($r) => $r->payment_date->format('Y-m-d'),
                    fn($r) => $r->contractor?->name ?? '—',
                    fn($r) => $r->meters ? $fmt_usd($r->meters) : '—',
                    fn($r) => $r->currency,
                    fn($r) => $fmt_usd($r->amount),
                    fn($r) => $fmt_iqd($r->amount_iqd),
                ];
                $title = 'پارەدانی وەستا';
                break;

            case 'labor_payments':
                $rows = LaborPayment::with('worker', 'project', 'user')
                    ->whereBetween('date', [$fromDate, $toDate])
                    ->when($request->filled('worker_id'), fn($q) => $q->where('worker_id', $request->worker_id))
                    ->when($request->filled('project_id'), fn($q) => $q->where('project_id', $request->project_id))
                    ->orderByDesc('date')->get();
                $headings = ['بەروار', 'کرێکار', 'ڕۆڵ', 'پڕۆژە', 'شێواز', 'کاتژمێر', 'ڕۆژ', 'بڕ', 'دراو'];
                $columns  = [
                    fn($r) => $r->date->format('Y-m-d'),
                    fn($r) => $r->worker?->name ?? $r->worker_name ?? '—',
                    fn($r) => $r->role ?? '—',
                    fn($r) => $r->project?->name ?? '—',
                    fn($r) => ($r->payment_mode ?? ($r->is_hourly ? 'hourly' : 'fixed')) === 'hourly' ? 'کاتژمێری' : (($r->payment_mode ?? '') === 'daily' ? 'ڕۆژانە' : 'جێگیر'),
                    fn($r) => ($r->payment_mode ?? ($r->is_hourly ? 'hourly' : 'fixed')) === 'hourly' ? $fmt_usd($r->hours) : '—',
                    fn($r) => ($r->payment_mode ?? '') === 'daily' ? $fmt_usd($r->days) : '—',
                    fn($r) => $fmt_usd($r->amount),
                    fn($r) => $r->currency,
                ];
                $title = 'کرێی کارەکان';
                break;

            default: // transactions
                $rows = Transaction::with('client', 'user')
                    ->whereBetween('transaction_date', [$fromDate, $toDate])
                    ->when($request->filled('client_id'), fn($q) => $q->where('client_id', $request->client_id))
                    ->when($request->filled('type'), fn($q) => $q->where('type', $request->type))
                    ->orderByDesc('transaction_date')->get();
                $headings = ['بەروار', 'کڕیار', 'جۆر', 'دراو', 'بڕ', 'بەدینار'];
                $columns  = [
                    fn($r) => $r->transaction_date->format('Y-m-d'),
                    fn($r) => $r->client?->name ?? '—',
                    fn($r) => Transaction::TYPES[$r->type] ?? $r->type,
                    fn($r) => $r->currency,
                    fn($r) => $fmt_usd($r->amount),
                    fn($r) => $fmt_iqd($r->amount_iqd),
                ];
                $title = 'مامەڵە گشتییەکان';
                break;
        }

        return [$rows, $headings, $columns, $title];
    }

    /* ==================== OLD EXPORTS ==================== */

    public function exportExcel(Request $request)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate   = $request->get('to_date', now()->format('Y-m-d'));
        $clientId = $request->get('client_id');

        $filename = 'مامەڵەکان_' . $fromDate . '_بۆ_' . $toDate . '.xlsx';

        return Excel::download(new TransactionsExport($fromDate, $toDate, $clientId), $filename);
    }

    public function exportClientExcel(Client $client)
    {
        $filename = 'کڕیار_' . $client->name . '_' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new ClientTransactionsExport($client), $filename);
    }
}
