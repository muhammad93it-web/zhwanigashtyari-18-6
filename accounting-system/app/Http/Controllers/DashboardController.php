<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contractor;
use App\Models\ContractorPayment;
use App\Models\Debt;
use App\Models\Document;
use App\Models\ExchangeRate;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Material;
use App\Models\MaterialMovement;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $thisMonth = Carbon::now()->startOfMonth();

        $sum = fn($q, $col) => $q->where($col, '>=', $thisMonth)
            ->selectRaw('SUM(amount_usd) usd, SUM(amount_iqd) iqd')->first();

        $incomeMonth   = $sum(Income::query(), 'income_date');
        $expenseMonth  = $sum(Expense::query(), 'expense_date');
        $purchaseMonth = $sum(MaterialMovement::where('type', 'purchase'), 'movement_date');
        $saleMonth     = $sum(MaterialMovement::where('type', 'sale'), 'movement_date');
        $contractorMonth = $sum(ContractorPayment::query(), 'payment_date');

        $receivable = Debt::where('direction', 'receivable')->where('status', 'open')
            ->selectRaw('SUM(amount_usd) usd, SUM(amount_iqd) iqd')->first();
        $payable = Debt::where('direction', 'payable')->where('status', 'open')
            ->selectRaw('SUM(amount_usd) usd, SUM(amount_iqd) iqd')->first();

        $inIqd  = (float) $incomeMonth->iqd + (float) $saleMonth->iqd;
        $outIqd = (float) $expenseMonth->iqd + (float) $purchaseMonth->iqd + (float) $contractorMonth->iqd;

        $month = [
            'income_iqd'   => (float) $incomeMonth->iqd,
            'expense_iqd'  => (float) $expenseMonth->iqd,
            'purchase_iqd' => (float) $purchaseMonth->iqd,
            'sale_iqd'     => (float) $saleMonth->iqd,
            'contractor_iqd' => (float) $contractorMonth->iqd,
            'in_iqd'       => $inIqd,
            'out_iqd'      => $outIqd,
            'net_iqd'      => $inIqd - $outIqd,
        ];

        $counts = [
            'materials'   => Material::count(),
            'low_stock'   => Material::whereNotNull('min_stock')->whereColumn('current_stock', '<=', 'min_stock')->count(),
            'contractors' => Contractor::count(),
            'clients'     => Client::count(),
            'documents'   => Document::count(),
        ];

        $debts = [
            'receivable_iqd' => (float) $receivable->iqd,
            'payable_iqd'    => (float) $payable->iqd,
        ];

        $currentRate = ExchangeRate::current();

        return view('dashboard.index', compact('month', 'counts', 'debts', 'currentRate'));
    }
}
