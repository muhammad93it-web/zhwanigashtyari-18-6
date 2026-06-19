<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $query = Driver::query()->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('phone', 'like', "%{$s}%")
                ->orWhere('vehicle_number', 'like', "%{$s}%"));
        }

        $drivers = $query->paginate(20)->withQueryString();
        $totalIqd = (float) Driver::sum('balance_iqd');
        $totalUsd = (float) Driver::sum('balance_usd');

        return view('drivers.index', compact('drivers', 'totalIqd', 'totalUsd'));
    }

    public function create()
    {
        return view('drivers.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateDriver($request);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['balance'] = 0;

        Driver::create($data);

        return redirect()->route('drivers.index')->with('success', 'شۆفێر زیادکرا.');
    }

    public function show(Driver $driver)
    {
        $transactions = $driver->transactions()
            ->with('user')
            ->latest('date')
            ->latest('id')
            ->paginate(25);

        $summary = $this->summary($driver);

        return view('drivers.show', compact('driver', 'transactions', 'summary'));
    }

    /** Per-currency totals: total trips charged, paid, and the remaining balance. */
    private function summary(Driver $driver): array
    {
        $rows = $driver->transactions()
            ->selectRaw("currency, type, SUM(amount) total")
            ->groupBy('currency', 'type')
            ->get();

        $out = [
            'IQD' => ['trip' => 0.0, 'payment' => 0.0, 'balance' => (float) $driver->balance_iqd],
            'USD' => ['trip' => 0.0, 'payment' => 0.0, 'balance' => (float) $driver->balance_usd],
        ];

        foreach ($rows as $r) {
            $cur = $r->currency === 'USD' ? 'USD' : 'IQD';
            $type = $r->type === 'payment' ? 'payment' : 'trip';
            $out[$cur][$type] += (float) $r->total;
        }

        return $out;
    }

    public function edit(Driver $driver)
    {
        return view('drivers.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        $data = $this->validateDriver($request);
        $data['is_active'] = $request->boolean('is_active', true);

        $driver->update($data);

        return redirect()->route('drivers.show', $driver)->with('success', 'زانیاری نوێکرایەوە.');
    }

    public function destroy(Driver $driver)
    {
        if ($driver->transactions()->exists() || $driver->tripLogs()->exists()) {
            return back()->with('error', 'ناتوانرێت بسڕدرێتەوە چونکە مامەڵە یان تۆماری گواستنەوەی پێوەیە.');
        }

        $driver->delete();

        return redirect()->route('drivers.index')->with('success', 'سڕایەوە.');
    }

    private function validateDriver(Request $request): array
    {
        return $request->validate([
            'name'           => 'required|string|max:255',
            'phone'          => 'nullable|string|max:50',
            'address'        => 'nullable|string|max:255',
            'vehicle_number' => 'nullable|string|max:100',
            'vehicle_type'   => 'nullable|string|max:100',
            'notes'          => 'nullable|string|max:1000',
            'is_active'      => 'boolean',
        ]);
    }

    /** کەشف حساب: لیستی شۆفێرەکان + هەڵبژاردنی ناو بۆ بینینی کەشف حساب. */
    public function statements(Request $request)
    {
        $query = Driver::query()->orderBy('name');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('phone', 'like', "%{$s}%"));
        }

        $drivers = $query->paginate(30)->withQueryString();
        $allDrivers = Driver::orderBy('name')->get(['id', 'name']);

        $totals = [
            'IQD' => (float) Driver::sum('balance_iqd'),
            'USD' => (float) Driver::sum('balance_usd'),
        ];

        return view('drivers.statements', compact('drivers', 'allDrivers', 'totals'));
    }

    public function statementGo(Request $request)
    {
        $request->validate(['driver_id' => 'required|exists:drivers,id']);

        return redirect()->route('drivers.show', $request->driver_id);
    }

    public function statementPrint(Driver $driver)
    {
        return view('drivers.statement-print', $this->statementData($driver) + ['logo' => $this->logoDataUri()]);
    }

    public function statementExcel(Driver $driver)
    {
        $html = view('drivers.statement-excel', $this->statementData($driver))->render();

        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="driver-statement-' . $driver->id . '.xls"',
        ]);
    }

    public function statementWord(Driver $driver)
    {
        $html = view('drivers.statement-word', $this->statementData($driver))->render();

        return response($html, 200, [
            'Content-Type'        => 'application/msword; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="driver-statement-' . $driver->id . '.doc"',
        ]);
    }

    private function statementData(Driver $driver): array
    {
        $transactions = $driver->transactions()
            ->with('user')
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        return [
            'driver'       => $driver,
            'transactions' => $transactions,
            'summary'      => $this->summary($driver),
        ];
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
