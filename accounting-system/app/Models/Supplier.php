<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name', 'phone', 'balance', 'balance_iqd', 'balance_usd', 'notes', 'is_active',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'balance_iqd' => 'decimal:2',
        'balance_usd' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(SupplierTransaction::class);
    }

    public function purchaseInvoices()
    {
        return $this->hasMany(PurchaseInvoice::class);
    }
}
