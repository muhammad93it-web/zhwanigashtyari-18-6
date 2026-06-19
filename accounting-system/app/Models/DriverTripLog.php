<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverTripLog extends Model
{
    protected $fillable = [
        'driver_id', 'user_id', 'project_id', 'date',
        'grand_total_iqd', 'grand_total_usd',
        'paid_iqd', 'paid_usd', 'remaining_iqd', 'remaining_usd', 'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'grand_total_iqd' => 'decimal:2',
        'grand_total_usd' => 'decimal:2',
        'paid_iqd' => 'decimal:2',
        'paid_usd' => 'decimal:2',
        'remaining_iqd' => 'decimal:2',
        'remaining_usd' => 'decimal:2',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function details()
    {
        return $this->hasMany(DriverTripDetail::class);
    }

    public function transactions()
    {
        return $this->hasMany(DriverTransaction::class);
    }
}
