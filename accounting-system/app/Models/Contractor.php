<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{
    protected $fillable = [
        'name', 'phone', 'work_type', 'rate_per_meter', 'contract_amount',
        'currency', 'notes', 'is_active',
    ];

    protected $casts = [
        'rate_per_meter' => 'decimal:2',
        'contract_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    const WORK_TYPES = [
        'per_meter' => 'بە مەتر',
        'contract'  => 'قۆنتەرات',
    ];

    public function payments()
    {
        return $this->hasMany(ContractorPayment::class);
    }

    public function getWorkTypeNameAttribute()
    {
        return self::WORK_TYPES[$this->work_type] ?? $this->work_type;
    }
}
