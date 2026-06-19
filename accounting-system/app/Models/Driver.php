<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = [
        'name', 'phone', 'address', 'vehicle_number', 'vehicle_type',
        'balance', 'balance_iqd', 'balance_usd', 'notes', 'is_active',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'balance_iqd' => 'decimal:2',
        'balance_usd' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(DriverTransaction::class);
    }

    public function tripLogs()
    {
        return $this->hasMany(DriverTripLog::class);
    }
}
