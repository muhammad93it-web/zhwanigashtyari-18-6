<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ExchangeRate;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('client')->latest('transaction_date');

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('transaction_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('transaction_date', '<=', $request->to_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('client', fn($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
        }

        $transactions = $query->paginate(20)->withQueryString();

        $clients = Client::orderBy('name')->get(['id', 'name']);
        $types   = Transaction::TYPES;

        // Summary totals for filtered results
        $totals = Transaction::with('client')
            ->when($request->filled('client_id'), fn($q) => $q->where('client_id', $request->client_id))
            ->when($request->filled('type'), fn($q) => $q->where('type', $request->type))
            ->when($request->filled('currency'), fn($q) => $q->where('currency', $request->currency))
            ->when($request->filled('from_date'), fn($q) => $q->whereDate('transaction_date', '>=', $request->from_date))
            ->when($request->filled('to_date'), fn($q) => $q->whereDate('transaction_date', '<=', $request->to_date))
            ->selectRaw('SUM(amount_usd) as total_usd, SUM(amount_iqd) as total_iqd, COUNT(*) as total_count')
            ->first();

        return view('transactions.index', compact('transactions', 'clients', 'types', 'totals'));
    }

    public function create(Request $request)
    {
        $clients     = Client::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $types       = Transaction::TYPES;
        $currencies  = Transaction::CURRENCIES;
        $currentRate = ExchangeRate::currentRate();
        $preselectedClient = $request->filled('client_id') ? Client::find($request->client_id) : null;

        return view('transactions.create', compact('clients', 'types', 'currencies', 'currentRate', 'preselectedClient'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'        => 'required|exists:clients,id',
            'type'             => 'required|in:sale,purchase,debit,credit',
            'currency'         => 'required|in:USD,IQD',
            'amount'           => 'required|numeric|min:0.01',
            'description'      => 'required|string|max:500',
            'transaction_date' => 'required|date',
            'notes'            => 'nullable|string|max:1000',
        ]);

        // Lock the current exchange rate at creation time (IMMUTABLE)
        $lockedRate = ExchangeRate::currentRate();

        // Compute both USD and IQD amounts from the locked rate
        if ($validated['currency'] === 'USD') {
            $amountUsd = $validated['amount'];
            $amountIqd = round($validated['amount'] * $lockedRate, 2);
        } else {
            $amountIqd = $validated['amount'];
            $amountUsd = round($validated['amount'] / $lockedRate, 4);
        }

        $transaction = Transaction::create([
            'client_id'              => $validated['client_id'],
            'user_id'                => Auth::id(),
            'type'                   => $validated['type'],
            'currency'               => $validated['currency'],
            'amount'                 => $validated['amount'],
            'amount_usd'             => $amountUsd,
            'amount_iqd'             => $amountIqd,
            'exchange_rate_usd_to_iqd' => $lockedRate,   // LOCKED — never changes after save
            'description'            => $validated['description'],
            'transaction_date'       => $validated['transaction_date'],
            'notes'                  => $validated['notes'] ?? null,
        ]);

        return redirect()->route('transactions.show', $transaction)
            ->with('success', 'مامەڵەکە بە سەرکەوتوویی تۆمارکرا.');
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('client', 'user');
        return view('transactions.show', compact('transaction'));
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return redirect()->route('transactions.index')
            ->with('success', 'مامەڵەکە سڕایەوە.');
    }

    public function receipt(Transaction $transaction)
    {
        $transaction->load('client', 'user');
        return view('receipts.show', compact('transaction'));
    }

    public function printReceipt(Transaction $transaction)
    {
        $transaction->load('client', 'user');
        return view('receipts.print', compact('transaction'));
    }
}
