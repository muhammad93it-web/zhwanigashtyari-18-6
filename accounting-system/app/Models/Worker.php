<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    protected $fillable = [
        'name', 'role', 'phone', 'default_hourly_rate', 'default_currency', 'notes', 'is_active',
    ];

    protected $casts = [
        'default_hourly_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function laborPayments()
    {
        return $this->hasMany(LaborPayment::class);
    }
}
