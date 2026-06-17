<?php

namespace App\Http\Controllers;

use App\Models\Worker;
use Illuminate\Http\Request;

class WorkerController extends Controller
{
    public function index(Request $request)
    {
        $query = Worker::query()->withCount('laborPayments')->orderBy('name');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('role', 'like', "%{$s}%")
                ->orWhere('phone', 'like', "%{$s}%"));
        }

        $workers = $query->paginate(20)->withQueryString();

        return view('workers.index', compact('workers'));
    }

    public function create()
    {
        return view('workers.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateWorker($request);
        $data['is_active'] = $request->boolean('is_active', true);

        Worker::create($data);

        return redirect()->route('workers.index')->with('success', 'کرێکار زیادکرا.');
    }

    public function show(Worker $worker)
    {
        $payments = $worker->laborPayments()->with(['project', 'user'])->latest('date')->paginate(20);

        $totals = $worker->laborPayments()
            ->selectRaw("SUM(CASE WHEN currency='IQD' THEN amount ELSE 0 END) iqd, SUM(CASE WHEN currency='USD' THEN amount ELSE 0 END) usd")
            ->first();

        return view('workers.show', compact('worker', 'payments', 'totals'));
    }

    public function edit(Worker $worker)
    {
        return view('workers.edit', compact('worker'));
    }

    public function update(Request $request, Worker $worker)
    {
        $data = $this->validateWorker($request);
        $data['is_active'] = $request->boolean('is_active', true);

        $worker->update($data);

        return redirect()->route('workers.show', $worker)->with('success', 'زانیاری کرێکار نوێکرایەوە.');
    }

    public function destroy(Worker $worker)
    {
        if ($worker->laborPayments()->exists()) {
            return back()->with('error', 'ناتوانرێت بسڕدرێتەوە چونکە پارەدانی کرێی هەیە.');
        }
        $worker->delete();
        return redirect()->route('workers.index')->with('success', 'کرێکار سڕایەوە.');
    }

    private function validateWorker(Request $request): array
    {
        return $request->validate([
            'name'               => 'required|string|max:255',
            'role'               => 'nullable|string|max:255',
            'phone'              => 'nullable|string|max:50',
            'default_hourly_rate' => 'nullable|numeric|min:0',
            'default_currency'   => 'required|in:IQD,USD',
            'notes'              => 'nullable|string|max:1000',
        ]);
    }
}
