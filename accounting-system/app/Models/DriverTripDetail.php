<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverTripDetail extends Model
{
    protected $fillable = [
        'driver_trip_log_id', 'project_id', 'work_type',
        'trip_count', 'price_per_trip', 'currency', 'line_total',
    ];

    protected $casts = [
        'trip_count' => 'decimal:2',
        'price_per_trip' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    const WORK_TYPES = [
        'waste_disposal' => 'گواستنەوەی خۆڵ و خاشاک',
        'sub_base'       => 'تێکەڵە',
    ];

    public function tripLog()
    {
        return $this->belongsTo(DriverTripLog::class, 'driver_trip_log_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getWorkTypeNameAttribute()
    {
        return self::WORK_TYPES[$this->work_type] ?? $this->work_type;
    }
}
