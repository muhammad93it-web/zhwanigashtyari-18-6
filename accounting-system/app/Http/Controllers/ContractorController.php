<?php

namespace App\Http\Controllers;

use App\Models\Contractor;
use Illuminate\Http\Request;

class ContractorController extends Controller
{
    public function index(Request $request)
    {
        $query = Contractor::query()->withCount('payments')->orderBy('name');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('phone', 'like', "%{$s}%"));
        }
        if ($request->filled('work_type')) {
            $query->where('work_type', $request->work_type);
        }

        $contractors = $query->paginate(20)->withQueryString();

        return view('contractors.index', compact('contractors'));
    }

    public function create()
    {
        return view('contractors.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'phone'           => 'nullable|string|max:50',
            'work_type'       => 'required|in:per_meter,contract',
            'rate_per_meter'  => 'nullable|numeric|min:0',
            'contract_amount' => 'nullable|numeric|min:0',
            'currency'        => 'required|in:USD,IQD',
            'notes'           => 'nullable|string|max:1000',
            'is_active'       => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        Contractor::create($data);

        return redirect()->route('contractors.index')->with('success', 'وەستا زیادکرا.');
    }

    public function show(Contractor $contractor)
    {
        $payments = $contractor->payments()->with('user')->latest('payment_date')->paginate(20);
        $paid = $contractor->payments()->selectRaw('SUM(amount_usd) usd, SUM(amount_iqd) iqd, SUM(meters) m')->first();

        return view('contractors.show', compact('contractor', 'payments', 'paid'));
    }

    public function edit(Contractor $contractor)
    {
        return view('contractors.edit', compact('contractor'));
    }

    public function update(Request $request, Contractor $contractor)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'phone'           => 'nullable|string|max:50',
            'work_type'       => 'required|in:per_meter,contract',
            'rate_per_meter'  => 'nullable|numeric|min:0',
            'contract_amount' => 'nullable|numeric|min:0',
            'currency'        => 'required|in:USD,IQD',
            'notes'           => 'nullable|string|max:1000',
            'is_active'       => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $contractor->update($data);

        return redirect()->route('contractors.show', $contractor)->with('success', 'زانیاری نوێکرایەوە.');
    }

    public function destroy(Contractor $contractor)
    {
        if ($contractor->payments()->exists()) {
            return back()->with('error', 'ناتوانرێت بسڕدرێتەوە چونکە پارەدانی پێوەیە.');
        }
        $contractor->delete();
        return redirect()->route('contractors.index')->with('success', 'سڕایەوە.');
    }
}
