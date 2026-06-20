<?php

namespace App\Http\Controllers;

use App\Models\LaborPayment;
use App\Models\Project;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaborPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = LaborPayment::with(['worker', 'project'])->latest('date')->latest('id');

        if ($request->filled('worker_id')) {
            $query->where('worker_id', $request->worker_id);
        }
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        $totals = (clone $query)->reorder()
            ->selectRaw("SUM(CASE WHEN currency='IQD' THEN amount ELSE 0 END) iqd, SUM(CASE WHEN currency='USD' THEN amount ELSE 0 END) usd, COUNT(*) c")
            ->first();

        $payments = $query->paginate(20)->withQueryString();
        $workers = Worker::orderBy('name')->get(['id', 'name']);
        $projects = Project::orderBy('name')->get(['id', 'name']);

        return view('labor-payments.index', compact('payments', 'workers', 'projects', 'totals'));
    }

    public function create()
    {
        return view('labor-payments.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validatePayment($request);

        LaborPayment::create($this->prepare($data) + ['user_id' => Auth::id()]);

        return redirect()->route('labor-payments.index')->with('success', 'کرێی کار تۆمارکرا.');
    }

    public function edit(LaborPayment $laborPayment)
    {
        return view('labor-payments.edit', array_merge($this->formData(), ['payment' => $laborPayment]));
    }

    public function update(Request $request, LaborPayment $laborPayment)
    {
        $data = $this->validatePayment($request);

        $laborPayment->update($this->prepare($data));

        return redirect()->route('labor-payments.index')->with('success', 'کرێی کار نوێکرایەوە.');
    }

    public function destroy(LaborPayment $laborPayment)
    {
        $laborPayment->delete();
        return back()->with('success', 'تۆماری کرێی کار سڕایەوە.');
    }

    private function formData(): array
    {
        return [
            'workers'  => Worker::where('is_active', true)->orderBy('name')->get(),
            'projects' => Project::orderBy('name')->get(['id', 'name']),
        ];
    }

    private function validatePayment(Request $request): array
    {
        return $request->validate([
            'worker_id'    => 'nullable|exists:workers,id',
            'worker_name'  => 'nullable|string|max:255',
            'role'         => 'nullable|string|max:255',
            'project_id'   => 'nullable|exists:projects,id',
            'date'         => 'required|date',
            'payment_mode' => 'required|in:fixed,hourly,daily',
            'hours'        => 'nullable|numeric|min:0',
            'days'         => 'nullable|numeric|min:0',
            'hourly_rate'  => 'nullable|numeric|min:0',
            'daily_rate'   => 'nullable|numeric|min:0',
            'amount'       => 'nullable|numeric|min:0',
            'currency'     => 'required|in:IQD,USD',
            'notes'        => 'nullable|string|max:1000',
        ]);
    }

    private function prepare(array $data): array
    {
        $mode = $data['payment_mode'] ?? 'fixed';
        // Keep legacy is_hourly in sync for backward-compatible reads.
        $data['is_hourly'] = $mode === 'hourly';

        if ($mode === 'hourly') {
            $hours = (float) ($data['hours'] ?? 0);
            $rate = (float) ($data['hourly_rate'] ?? 0);
            $data['amount'] = round($hours * $rate, 2);
            $data['days'] = null;
            $data['daily_rate'] = null;
        } elseif ($mode === 'daily') {
            $days = (float) ($data['days'] ?? 0);
            $rate = (float) ($data['daily_rate'] ?? 0);
            $data['amount'] = round($days * $rate, 2);
            $data['hours'] = null;
            $data['hourly_rate'] = null;
        } else {
            $data['hours'] = null;
            $data['hourly_rate'] = null;
            $data['days'] = null;
            $data['daily_rate'] = null;
            $data['amount'] = (float) ($data['amount'] ?? 0);
        }

        // Snapshot worker name/role for records that reference a worker row.
        if (!empty($data['worker_id'])) {
            $worker = Worker::find($data['worker_id']);
            if ($worker) {
                $data['worker_name'] = $data['worker_name'] ?: $worker->name;
                $data['role'] = $data['role'] ?: $worker->role;
            }
        }

        return $data;
    }
}
