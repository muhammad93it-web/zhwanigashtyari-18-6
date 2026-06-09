<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $clients = $query->withCount('transactions')->latest()->paginate(15)->withQueryString();

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'phone'     => 'nullable|string|max:50',
            'email'     => 'nullable|email|max:255',
            'address'   => 'nullable|string|max:500',
            'notes'     => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $client = Client::create($validated);

        return redirect()->route('clients.show', $client)
            ->with('success', 'کڕیارەکە بە سەرکەوتوویی زیادکرا.');
    }

    public function show(Client $client)
    {
        $transactions = $client->transactions()->with('user')->latest('transaction_date')->paginate(20);

        // Balance breakdown
        $balances = $client->transactions()
            ->selectRaw("
                SUM(CASE WHEN type IN ('sale','debit') THEN amount_usd ELSE -amount_usd END) as balance_usd,
                SUM(CASE WHEN type IN ('sale','debit') THEN amount_iqd ELSE -amount_iqd END) as balance_iqd,
                SUM(CASE WHEN type = 'sale' THEN amount_usd ELSE 0 END) as sales_usd,
                SUM(CASE WHEN type = 'purchase' THEN amount_usd ELSE 0 END) as purchases_usd,
                SUM(CASE WHEN type = 'debit' THEN amount_usd ELSE 0 END) as debits_usd,
                SUM(CASE WHEN type = 'credit' THEN amount_usd ELSE 0 END) as credits_usd
            ")
            ->first();

        return view('clients.show', compact('client', 'transactions', 'balances'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'phone'     => 'nullable|string|max:50',
            'email'     => 'nullable|email|max:255',
            'address'   => 'nullable|string|max:500',
            'notes'     => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $client->update($validated);

        return redirect()->route('clients.show', $client)
            ->with('success', 'زانیاری کڕیارەکە نوێکرایەوە.');
    }

    public function destroy(Client $client)
    {
        if ($client->transactions()->exists()) {
            return back()->with('error', 'ناتوانرێت ئەم کڕیارە بسڕدرێتەوە چونکە مامەڵەی پێوەیەتی.');
        }

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'کڕیارەکە سڕایەوە.');
    }
}
