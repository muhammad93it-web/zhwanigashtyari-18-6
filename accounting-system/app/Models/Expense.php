<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'user_id', 'project_id', 'driver_trip_log_id', 'payee', 'expense_type', 'category', 'currency', 'amount',
        'amount_usd', 'amount_iqd', 'exchange_rate_usd_to_iqd',
        'description', 'reason_description', 'reference_number', 'expense_date', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_usd' => 'decimal:2',
        'amount_iqd' => 'decimal:2',
        'exchange_rate_usd_to_iqd' => 'decimal:4',
        'expense_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->reference_number)) {
                $model->reference_number = 'EXP-' . strtoupper(uniqid());
            }
        });
    }
}
