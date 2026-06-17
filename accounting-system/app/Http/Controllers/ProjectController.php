<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Expense;
use App\Models\Project;
use App\Models\PurchaseInvoiceDetail;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::query()->with('client')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('location', 'like', "%{$s}%"));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $projects = $query->paginate(20)->withQueryString();

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $clients = Client::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('projects.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['is_active'] = $request->boolean('is_active', true);

        Project::create($data);

        return redirect()->route('projects.index')->with('success', 'پڕۆژە زیادکرا.');
    }

    public function show(Project $project)
    {
        $project->load('client');

        $purchaseCost = (float) PurchaseInvoiceDetail::where('project_id', $project->id)->sum('line_total');
        $expenseCost  = (float) Expense::where('project_id', $project->id)->sum('amount_iqd');
        $totalCost    = $purchaseCost + $expenseCost;

        $materials = PurchaseInvoiceDetail::where('project_id', $project->id)
            ->with(['material', 'invoice.supplier'])
            ->latest()
            ->paginate(15, ['*'], 'materials');

        $expenses = Expense::where('project_id', $project->id)
            ->latest('expense_date')
            ->paginate(15, ['*'], 'expenses');

        return view('projects.show', compact('project', 'purchaseCost', 'expenseCost', 'totalCost', 'materials', 'expenses'));
    }

    public function edit(Project $project)
    {
        $clients = Client::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('projects.edit', compact('project', 'clients'));
    }

    public function update(Request $request, Project $project)
    {
        $data = $this->validateData($request);
        $data['is_active'] = $request->boolean('is_active', true);

        $project->update($data);

        return redirect()->route('projects.show', $project)->with('success', 'زانیاری نوێکرایەوە.');
    }

    public function destroy(Project $project)
    {
        if ($project->purchaseDetails()->exists() || $project->expenses()->exists()) {
            return back()->with('error', 'ناتوانرێت بسڕدرێتەوە چونکە کڕین یان خەرجی پێوەیە.');
        }

        $project->delete();

        return redirect()->route('projects.index')->with('success', 'سڕایەوە.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name'      => 'required|string|max:255',
            'client_id' => 'nullable|exists:clients,id',
            'location'  => 'nullable|string|max:255',
            'budget'    => 'nullable|numeric|min:0',
            'status'    => 'required|in:active,completed,on_hold',
            'notes'     => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);
    }
}
