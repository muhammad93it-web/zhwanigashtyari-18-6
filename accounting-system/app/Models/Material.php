<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'name', 'unit', 'category', 'current_stock', 'min_stock', 'notes', 'is_active',
    ];

    protected $casts = [
        'current_stock' => 'decimal:3',
        'min_stock' => 'decimal:3',
        'is_active' => 'boolean',
    ];

    public function movements()
    {
        return $this->hasMany(MaterialMovement::class);
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->min_stock !== null && (float) $this->current_stock <= (float) $this->min_stock;
    }
}
