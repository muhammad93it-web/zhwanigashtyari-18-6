<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ExchangeRate;
use App\Models\Material;
use App\Models\MaterialMovement;
use App\Traits\CalculatesCurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaterialMovementController extends Controller
{
    use CalculatesCurrency;

    public function create(Request $request)
    {
        $type = $request->route('type', 'purchase');
        $materials = Material::where('is_active', true)->orderBy('name')->get();
        $clients = Client::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $currentRate = ExchangeRate::currentRate();

        return view('movements.create', compact('type', 'materials', 'clients', 'currentRate'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'material_id'   => 'required|exists:materials,id',
            'type'          => 'required|in:purchase,sale',
            'quantity'      => 'required|numeric|min:0.001',
            'unit_price'    => 'required|numeric|min:0',
            'currency'      => 'required|in:USD,IQD',
            'party_name'    => 'nullable|string|max:255',
            'client_id'     => 'nullable|exists:clients,id',
            'movement_date' => 'required|date',
            'notes'         => 'nullable|string|max:1000',
        ]);

        $rate = ExchangeRate::currentRate();
        $amount = round($data['quantity'] * $data['unit_price'], 2);
        $amounts = $this->currencyAmounts($data['currency'], $amount, $rate);

        DB::transaction(function () use ($data, $rate, $amount, $amounts) {
            MaterialMovement::create(array_merge($data, $amounts, [
                'amount' => $amount,
                'exchange_rate_usd_to_iqd' => $rate,
                'user_id' => Auth::id(),
            ]));

            $material = Material::lockForUpdate()->find($data['material_id']);
            if ($data['type'] === 'purchase') {
                $material->current_stock = (float) $material->current_stock + (float) $data['quantity'];
            } else {
                $material->current_stock = (float) $material->current_stock - (float) $data['quantity'];
            }
            $material->save();
        });

        $msg = $data['type'] === 'purchase' ? 'کڕینی مەواد تۆمارکرا.' : 'فرۆشتنی مەواد تۆمارکرا.';
        return redirect()->route('materials.show', $data['material_id'])->with('success', $msg);
    }

    public function destroy(MaterialMovement $movement)
    {
        DB::transaction(function () use ($movement) {
            $material = Material::lockForUpdate()->find($movement->material_id);
            if ($material) {
                // Reverse the stock effect
                if ($movement->type === 'purchase') {
                    $material->current_stock = (float) $material->current_stock - (float) $movement->quantity;
                } else {
                    $material->current_stock = (float) $material->current_stock + (float) $movement->quantity;
                }
                $material->save();
            }
            $movement->delete();
        });

        return back()->with('success', 'جووڵەکە سڕایەوە و کۆگا ڕاستکرایەوە.');
    }
}
