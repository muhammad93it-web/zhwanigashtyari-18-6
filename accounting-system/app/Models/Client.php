<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get total balance in USD (positive = client owes us, negative = we owe client)
     */
    public function getBalanceUsdAttribute()
    {
        return $this->transactions()
            ->selectRaw("
                SUM(CASE
                    WHEN type IN ('sale', 'debit') THEN amount_usd
                    WHEN type IN ('purchase', 'credit') THEN -amount_usd
                    ELSE 0
                END) as balance
            ")
            ->value('balance') ?? 0;
    }

    /**
     * Get total balance in IQD
     */
    public function getBalanceIqdAttribute()
    {
        return $this->transactions()
            ->selectRaw("
                SUM(CASE
                    WHEN type IN ('sale', 'debit') THEN amount_iqd
                    WHEN type IN ('purchase', 'credit') THEN -amount_iqd
                    ELSE 0
                END) as balance
            ")
            ->value('balance') ?? 0;
    }
}
