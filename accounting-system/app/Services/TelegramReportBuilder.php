<?php

namespace App\Services;

use App\Models\ContractorPayment;
use App\Models\Expense;
use App\Models\Income;
use App\Models\MaterialMovement;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;

/**
 * Builds Telegram content (text summaries + HTML documents) by mirroring
 * the query logic used in ReportController. All amounts in IQD (د.ع).
 */
class TelegramReportBuilder
{
    private function fmt($v): string
    {
        return number_format((float) $v, 0);
    }

    private function now(): Carbon
    {
        return Carbon::now(config('app.timezone'));
    }

    /** Daily summary (default: today) as Telegram HTML text. */
    public function dailyReport(?Carbon $date = null): string
    {
        $d = ($date ?? $this->now())->format('Y-m-d');

        $incomes   = (float) Income::whereDate('income_date', $d)->sum('amount_iqd');
        $sales     = (float) MaterialMovement::where('type', 'sale')->whereDate('movement_date', $d)->sum('amount_iqd');
        $expenses  = (float) Expense::whereDate('expense_date', $d)->sum('amount_iqd');
        $purchases = (float) MaterialMovement::where('type', 'purchase')->whereDate('movement_date', $d)->sum('amount_iqd');
        $payments  = (float) ContractorPayment::whereDate('payment_date', $d)->sum('amount_iqd');
        $txCount   = Transaction::whereDate('transaction_date', $d)->count();

        $in  = $incomes + $sales;
        $out = $expenses + $purchases + $payments;
        $net = $in - $out;

        $lines = [
            '📊 <b>ڕاپۆرتی ڕۆژانە</b>',
            "🗓 بەروار: <b>{$d}</b>",
            '',
            '🟢 داهات: <b>' . $this->fmt($in) . ' د.ع</b>',
            '   • وەرگرتنی پارە: ' . $this->fmt($incomes) . ' د.ع',
            '   • فرۆشتنی مەواد: ' . $this->fmt($sales) . ' د.ع',
            '🔴 خەرجی: <b>' . $this->fmt($out) . ' د.ع</b>',
            '   • خەرجکردن: ' . $this->fmt($expenses) . ' د.ع',
            '   • کڕینی مەواد: ' . $this->fmt($purchases) . ' د.ع',
            '   • پارەدانی وەستا: ' . $this->fmt($payments) . ' د.ع',
            '',
            ($net >= 0 ? '✅' : '⚠️') . ' پاشماوەی ڕۆژ: <b>' . $this->fmt($net) . ' د.ع</b>',
            "🧾 ژمارەی مامەڵەکان: <b>{$txCount}</b>",
        ];

        return implode("\n", $lines);
    }

    /** Monthly summary for the PREVIOUS full calendar month, as Telegram HTML text. */
    public function monthlyReport(?Carbon $ref = null): string
    {
        $ref   = $ref ?? $this->now();
        $start = $ref->copy()->subMonthNoOverflow()->startOfMonth();
        $end   = $ref->copy()->subMonthNoOverflow()->endOfMonth();
        $from  = $start->format('Y-m-d');
        $to    = $end->format('Y-m-d');

        $sum = fn ($q, $col) => (float) $q->whereBetween($col, [$from, $to])->sum('amount_iqd');

        $incomes   = $sum(Income::query(), 'income_date');
        $sales     = $sum(MaterialMovement::where('type', 'sale'), 'movement_date');
        $expenses  = $sum(Expense::query(), 'expense_date');
        $purchases = $sum(MaterialMovement::where('type', 'purchase'), 'movement_date');
        $payments  = $sum(ContractorPayment::query(), 'payment_date');

        $in  = $incomes + $sales;
        $out = $expenses + $purchases + $payments;
        $net = $in - $out;

        $lines = [
            '📈 <b>ڕاپۆرتی مانگانە (مانگی ڕابردوو)</b>',
            '🗓 مانگ: <b>' . $start->format('Y-m') . "</b> ({$from} — {$to})",
            '',
            '🟢 کۆی داهات: <b>' . $this->fmt($in) . ' د.ع</b>',
            '   • وەرگرتنی پارە: ' . $this->fmt($incomes) . ' د.ع',
            '   • فرۆشتنی مەواد: ' . $this->fmt($sales) . ' د.ع',
            '🔴 کۆی خەرجی: <b>' . $this->fmt($out) . ' د.ع</b>',
            '   • خەرجکردن: ' . $this->fmt($expenses) . ' د.ع',
            '   • کڕینی مەواد: ' . $this->fmt($purchases) . ' د.ع',
            '   • پارەدانی وەستا: ' . $this->fmt($payments) . ' د.ع',
            '',
            ($net >= 0 ? '✅' : '⚠️') . ' پاشماوەی مانگ: <b>' . $this->fmt($net) . ' د.ع</b>',
        ];

        return implode("\n", $lines);
    }

    /**
     * Transactions/receipts list as a self-contained HTML document.
     * Period follows the schedule frequency: daily=today, monthly=previous month.
     *
     * @return array{path:string, filename:string, count:int, periodLabel:string}
     */
    public function transactionsDocument(string $frequency, ?Carbon $ref = null): array
    {
        $ref = $ref ?? $this->now();

        if ($frequency === 'monthly') {
            $month = $ref->copy()->subMonthNoOverflow();
            $from  = $month->copy()->startOfMonth()->format('Y-m-d');
            $to    = $month->copy()->endOfMonth()->format('Y-m-d');
            $periodLabel = 'مانگی ' . $month->format('Y-m');
        } else {
            $from = $to = $ref->format('Y-m-d');
            $periodLabel = 'ڕۆژی ' . $from;
        }

        $rows = Transaction::with('client')
            ->whereBetween('transaction_date', [$from, $to])
            ->orderByDesc('transaction_date')
            ->get();

        $html = View::make('telegram.transactions', compact('rows', 'periodLabel', 'from', 'to'))->render();

        $dir = storage_path('app/backups');
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $filename = 'transactions_' . now()->format('Ymd_His') . '.html';
        $path     = $dir . DIRECTORY_SEPARATOR . $filename;
        file_put_contents($path, $html);

        return [
            'path'        => $path,
            'filename'    => $filename,
            'count'       => $rows->count(),
            'periodLabel' => $periodLabel,
        ];
    }
}
