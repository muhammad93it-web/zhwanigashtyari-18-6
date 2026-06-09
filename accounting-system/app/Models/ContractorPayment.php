<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractorPayment extends Model
{
    protected $fillable = [
        'contractor_id', 'user_id', 'currency', 'amount', 'amount_usd', 'amount_iqd',
        'exchange_rate_usd_to_iqd', 'meters', 'description', 'reference_number',
        'payment_date', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_usd' => 'decimal:2',
        'amount_iqd' => 'decimal:2',
        'exchange_rate_usd_to_iqd' => 'decimal:4',
        'meters' => 'decimal:3',
        'payment_date' => 'date',
    ];

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->reference_number)) {
                $model->reference_number = 'CPY-' . strtoupper(uniqid());
            }
        });
    }
}
