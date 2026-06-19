<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\DriverTripDetail;
use App\Models\DriverTripLog;
use App\Models\DriverTransaction;
use App\Models\ExchangeRate;
use App\Models\Expense;
use App\Models\Project;
use App\Traits\CalculatesCurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DriverTripLogController extends Controller
{
    use CalculatesCurrency;

    public function index(Request $request)
    {
        // Shared company books: everyone sees all trip logs.
        $query = DriverTripLog::query()
            ->with(['driver', 'user', 'project'])
            ->latest('date')
            ->latest('id');

        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        $totals = (clone $query)->reorder()
            ->selectRaw('SUM(grand_total_iqd) total_iqd, SUM(grand_total_usd) total_usd, SUM(paid_iqd) paid_iqd, SUM(paid_usd) paid_usd, SUM(remaining_iqd) remaining_iqd, SUM(remaining_usd) remaining_usd')
            ->first();

        $logs = $query->paginate(20)->withQueryString();
        $drivers = Driver::orderBy('name')->get(['id', 'name']);

        return view('driver-trip-logs.index', compact('logs', 'totals', 'drivers'));
    }

    public function create()
    {
        return view('driver-trip-logs.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validateLog($request);

        [$totalIqd, $totalUsd] = $this->computeLineTotals($data['lines']);
        $paidIqd = round((float) ($data['paid_iqd'] ?? 0), 2);
        $paidUsd = round((float) ($data['paid_usd'] ?? 0), 2);

        if ($paidIqd > $totalIqd) {
            return back()->withInput()->with('error', 'بڕی دراوی دیناری ناتوانێت زیاتر بێت لە کۆی دیناری گواستنەوەکە.');
        }
        if ($paidUsd > $totalUsd) {
            return back()->withInput()->with('error', 'بڕی دراوی دۆلاری ناتوانێت زیاتر بێت لە کۆی دۆلاری گواستنەوەکە.');
        }

        DB::transaction(function () use ($data, $totalIqd, $totalUsd, $paidIqd, $paidUsd) {
            $log = DriverTripLog::create([
                'driver_id'       => $data['driver_id'],
                'user_id'         => Auth::id(),
                'project_id'      => $data['project_id'] ?? null,
                'grand_total_iqd' => $totalIqd,
                'grand_total_usd' => $totalUsd,
                'paid_iqd'        => $paidIqd,
                'paid_usd'        => $paidUsd,
                'remaining_iqd'   => round($totalIqd - $paidIqd, 2),
                'remaining_usd'   => round($totalUsd - $paidUsd, 2),
                'date'            => $data['date'],
                'notes'           => $data['notes'] ?? null,
            ]);

            $this->createDetails($log, $data['lines']);

            $driver = Driver::lockForUpdate()->find($log->driver_id);
            if ($driver) {
                $this->applyLedger($driver, $log);
            }

            $this->createPaidExpenses($log);
        });

        return redirect()->route('driver-trip-logs.index')->with('success', 'تۆماری گواستنەوە دروستکرا، باڵانسی شۆفێر و خەرجی نوێکرانەوە.');
    }

    public function show(DriverTripLog $driverTripLog)
    {
        $driverTripLog->load(['driver', 'user', 'project', 'details.project']);

        return view('driver-trip-logs.show', compact('driverTripLog'));
    }

    public function edit(DriverTripLog $driverTripLog)
    {
        $driverTripLog->load(['details']);

        return view('driver-trip-logs.edit', array_merge($this->formData(), ['log' => $driverTripLog]));
    }

    public function update(Request $request, DriverTripLog $driverTripLog)
    {
        $data = $this->validateLog($request);

        [$totalIqd, $totalUsd] = $this->computeLineTotals($data['lines']);
        $paidIqd = round((float) ($data['paid_iqd'] ?? 0), 2);
        $paidUsd = round((float) ($data['paid_usd'] ?? 0), 2);

        if ($paidIqd > $totalIqd) {
            return back()->withInput()->with('error', 'بڕی دراوی دیناری ناتوانێت زیاتر بێت لە کۆی دیناری گواستنەوەکە.');
        }
        if ($paidUsd > $totalUsd) {
            return back()->withInput()->with('error', 'بڕی دراوی دۆلاری ناتوانێت زیاتر بێت لە کۆی دۆلاری گواستنەوەکە.');
        }

        DB::transaction(function () use ($data, $driverTripLog, $totalIqd, $totalUsd, $paidIqd, $paidUsd) {
            $log = DriverTripLog::lockForUpdate()->find($driverTripLog->id);
            if (! $log) {
                return;
            }
            $log->load('details');

            // Fully reverse the old effects (ledger + auto expenses), then reapply with new data.
            $this->reverseLedger($log);
            $this->reverseExpenses($log);
            $log->details()->delete();

            $log->update([
                'driver_id'       => $data['driver_id'],
                'project_id'      => $data['project_id'] ?? null,
                'grand_total_iqd' => $totalIqd,
                'grand_total_usd' => $totalUsd,
                'paid_iqd'        => $paidIqd,
                'paid_usd'        => $paidUsd,
                'remaining_iqd'   => round($totalIqd - $paidIqd, 2),
                'remaining_usd'   => round($totalUsd - $paidUsd, 2),
                'date'            => $data['date'],
                'notes'           => $data['notes'] ?? null,
            ]);

            $this->createDetails($log, $data['lines']);

            $driver = Driver::lockForUpdate()->find($log->driver_id);
            if ($driver) {
                $this->applyLedger($driver, $log);
            }

            $this->createPaidExpenses($log);
        });

        return redirect()->route('driver-trip-logs.show', $driverTripLog)->with('success', 'تۆماری گواستنەوە نوێکرایەوە، باڵانس و خەرجی ڕاستکرانەوە.');
    }

    public function destroy(DriverTripLog $driverTripLog)
    {
        DB::transaction(function () use ($driverTripLog) {
            $log = DriverTripLog::lockForUpdate()->find($driverTripLog->id);
            if (! $log) {
                return;
            }
            $log->load('details');

            $this->reverseLedger($log);
            $this->reverseExpenses($log);

            $log->delete();
        });

        return redirect()->route('driver-trip-logs.index')->with('success', 'تۆماری گواستنەوە سڕایەوە و باڵانس و خەرجی ڕاستکرانەوە.');
    }

    public function print(DriverTripLog $driverTripLog)
    {
        $driverTripLog->load(['driver', 'user', 'project', 'details.project']);

        return view('driver-trip-logs.print', [
            'log'  => $driverTripLog,
            'logo' => $this->logoDataUri(),
        ]);
    }

    public function exportExcel(DriverTripLog $driverTripLog)
    {
        $driverTripLog->load(['driver', 'user', 'project', 'details.project']);

        $html = view('driver-trip-logs.export-excel', [
            'log'  => $driverTripLog,
            'logo' => $this->logoDataUri(),
        ])->render();

        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="driver-trip-' . $driverTripLog->id . '.xls"',
        ]);
    }

    public function exportWord(DriverTripLog $driverTripLog)
    {
        $driverTripLog->load(['driver', 'user', 'project', 'details.project']);

        $html = view('driver-trip-logs.export-word', [
            'log'  => $driverTripLog,
            'logo' => $this->logoDataUri(),
        ])->render();

        return response($html, 200, [
            'Content-Type'        => 'application/msword; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="driver-trip-' . $driverTripLog->id . '.doc"',
        ]);
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------

    private function formData(): array
    {
        return [
            'drivers'   => Driver::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'projects'  => Project::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'workTypes' => DriverTripDetail::WORK_TYPES,
        ];
    }

    private function validateLog(Request $request): array
    {
        return $request->validate([
            'driver_id'           => 'required|exists:drivers,id',
            'project_id'          => 'nullable|exists:projects,id',
            'date'                => 'required|date',
            'paid_iqd'            => 'nullable|numeric|min:0',
            'paid_usd'            => 'nullable|numeric|min:0',
            'notes'               => 'nullable|string|max:1000',
            'lines'               => 'required|array|min:1',
            'lines.*.work_type'      => 'required|in:waste_disposal,sub_base',
            'lines.*.trip_count'     => 'required|numeric|min:0.01',
            'lines.*.price_per_trip' => 'required|numeric|min:0',
            'lines.*.currency'       => 'required|in:IQD,USD',
        ]);
    }

    private function computeLineTotals(array $lines): array
    {
        $totalIqd = 0;
        $totalUsd = 0;
        foreach ($lines as $line) {
            $lineTotal = round((float) $line['trip_count'] * (float) $line['price_per_trip'], 2);
            if (($line['currency'] ?? 'IQD') === 'USD') {
                $totalUsd += $lineTotal;
            } else {
                $totalIqd += $lineTotal;
            }
        }

        return [round($totalIqd, 2), round($totalUsd, 2)];
    }

    private function createDetails(DriverTripLog $log, array $lines): void
    {
        foreach ($lines as $line) {
            $lineTotal = round((float) $line['trip_count'] * (float) $line['price_per_trip'], 2);

            DriverTripDetail::create([
                'driver_trip_log_id' => $log->id,
                'project_id'         => $log->project_id,
                'work_type'          => $line['work_type'],
                'trip_count'         => $line['trip_count'],
                'price_per_trip'     => $line['price_per_trip'],
                'line_total'         => $lineTotal,
                'currency'           => ($line['currency'] ?? 'IQD') === 'USD' ? 'USD' : 'IQD',
            ]);
        }
    }

    /**
     * Add per-currency ledger entries + balances for a driver trip log.
     * IQD and USD are tracked as fully independent running balances.
     */
    private function applyLedger(Driver $driver, DriverTripLog $log): void
    {
        $currencies = [
            'IQD' => ['total' => (float) $log->grand_total_iqd, 'paid' => (float) $log->paid_iqd, 'field' => 'balance_iqd'],
            'USD' => ['total' => (float) $log->grand_total_usd, 'paid' => (float) $log->paid_usd, 'field' => 'balance_usd'],
        ];

        foreach ($currencies as $cur => $v) {
            if ($v['total'] <= 0 && $v['paid'] <= 0) {
                continue;
            }
            $field = $v['field'];
            $balance = (float) $driver->$field;

            if ($v['total'] > 0) {
                $balance = round($balance + $v['total'], 2);
                DriverTransaction::create([
                    'driver_id'          => $driver->id,
                    'driver_trip_log_id' => $log->id,
                    'user_id'            => Auth::id(),
                    'type'               => 'trip',
                    'currency'           => $cur,
                    'amount'             => $v['total'],
                    'balance_after'      => $balance,
                    'date'               => $log->date,
                    'description'        => 'تۆماری گواستنەوە #' . $log->id,
                ]);
            }

            if ($v['paid'] > 0) {
                $balance = round($balance - $v['paid'], 2);
                DriverTransaction::create([
                    'driver_id'          => $driver->id,
                    'driver_trip_log_id' => $log->id,
                    'user_id'            => Auth::id(),
                    'type'               => 'payment',
                    'currency'           => $cur,
                    'amount'             => $v['paid'],
                    'balance_after'      => $balance,
                    'date'               => $log->date,
                    'description'        => 'پارەدان لەگەڵ گواستنەوە #' . $log->id,
                ]);
            }

            $driver->$field = $balance;
        }

        // Keep legacy single-currency balance = IQD balance for backward compat.
        $driver->balance = $driver->balance_iqd;
        $driver->save();
    }

    /**
     * Reverse the per-currency ledger effect of a trip log on its driver.
     */
    private function reverseLedger(DriverTripLog $log): void
    {
        $driver = Driver::lockForUpdate()->find($log->driver_id);
        if (! $driver) {
            return;
        }

        $currencies = [
            'IQD' => ['total' => (float) $log->grand_total_iqd, 'paid' => (float) $log->paid_iqd, 'field' => 'balance_iqd'],
            'USD' => ['total' => (float) $log->grand_total_usd, 'paid' => (float) $log->paid_usd, 'field' => 'balance_usd'],
        ];

        foreach ($currencies as $cur => $v) {
            if ($v['total'] <= 0 && $v['paid'] <= 0) {
                continue;
            }
            $field = $v['field'];
            $balance = (float) $driver->$field;

            // Undo the trip charge: lower the debt by the full trip total.
            if ($v['total'] > 0) {
                $balance = round($balance - $v['total'], 2);
                DriverTransaction::create([
                    'driver_id'          => $driver->id,
                    'driver_trip_log_id' => $log->id,
                    'user_id'            => Auth::id(),
                    'type'               => 'payment',
                    'currency'           => $cur,
                    'amount'             => $v['total'],
                    'balance_after'      => $balance,
                    'date'               => now()->toDateString(),
                    'description'        => 'گەڕاندنەوەی گواستنەوەی #' . $log->id,
                ]);
            }

            // Undo the payment: raise the debt back by the paid amount.
            if ($v['paid'] > 0) {
                $balance = round($balance + $v['paid'], 2);
                DriverTransaction::create([
                    'driver_id'          => $driver->id,
                    'driver_trip_log_id' => $log->id,
                    'user_id'            => Auth::id(),
                    'type'               => 'trip',
                    'currency'           => $cur,
                    'amount'             => $v['paid'],
                    'balance_after'      => $balance,
                    'date'               => now()->toDateString(),
                    'description'        => 'هەڵوەشاندنەوەی پارەدانی گواستنەوەی #' . $log->id,
                ]);
            }

            $driver->$field = $balance;
        }

        $driver->balance = $driver->balance_iqd;
        $driver->save();
    }

    /**
     * Create one Expense per paid currency to deduct project cash.
     * Tagged with driver_trip_log_id so they can be reversed on update/destroy.
     */
    private function createPaidExpenses(DriverTripLog $log): void
    {
        $driver = $log->driver()->first();
        $payee = $driver?->name ?? 'شۆفێر';
        $rate = ExchangeRate::currentRate();

        $paid = [
            'IQD' => (float) $log->paid_iqd,
            'USD' => (float) $log->paid_usd,
        ];

        foreach ($paid as $cur => $amount) {
            if ($amount <= 0) {
                continue;
            }

            Expense::create(array_merge(
                $this->currencyAmounts($cur, $amount, $rate),
                [
                    'user_id'                  => Auth::id(),
                    'project_id'               => $log->project_id,
                    'driver_trip_log_id'       => $log->id,
                    'payee'                    => $payee,
                    'expense_type'             => 'transportation',
                    'category'                 => 'گواستنەوە و شۆفێر',
                    'currency'                 => $cur,
                    'amount'                   => round($amount, 2),
                    'exchange_rate_usd_to_iqd' => $rate,
                    'description'              => 'پارەدانی کرێی گواستنەوە #' . $log->id . ' بۆ ' . $payee,
                    'expense_date'             => $log->date,
                ]
            ));
        }
    }

    /**
     * Delete the auto-created expenses tied to this trip log.
     */
    private function reverseExpenses(DriverTripLog $log): void
    {
        Expense::where('driver_trip_log_id', $log->id)->delete();
    }

    private function logoDataUri(): string
    {
        $path = public_path('images/logo.png');
        if (is_file($path)) {
            return 'data:image/png;base64,' . base64_encode((string) file_get_contents($path));
        }

        return '';
    }
}
