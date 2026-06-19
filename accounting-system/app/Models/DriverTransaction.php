<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverTransaction extends Model
{
    protected $fillable = [
        'driver_id', 'driver_trip_log_id', 'expense_id', 'user_id',
        'type', 'currency', 'amount', 'balance_after', 'date', 'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'date' => 'date',
    ];

    const TYPES = [
        'trip'       => 'کرێی گواستنەوە',
        'payment'    => 'پارەدان',
        'adjustment' => 'ڕاستکردنەوە',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeNameAttribute()
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}
