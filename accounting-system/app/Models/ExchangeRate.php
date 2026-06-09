<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $fillable = [
        'usd_to_iqd',
        'notes',
        'set_by',
        'effective_from',
    ];

    protected $casts = [
        'usd_to_iqd' => 'decimal:4',
        'effective_from' => 'datetime',
    ];

    /**
     * Get the currently active exchange rate (most recent)
     */
    public static function current(): ?self
    {
        return static::latest('effective_from')->first();
    }

    /**
     * Get rate value for embedding in transactions
     */
    public static function currentRate(): float
    {
        $rate = static::current();
        return $rate ? (float) $rate->usd_to_iqd : 1500.0;
    }
}
