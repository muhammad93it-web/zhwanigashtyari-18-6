<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialMovement extends Model
{
    protected $fillable = [
        'material_id', 'user_id', 'client_id', 'type', 'quantity', 'unit_price',
        'currency', 'amount', 'amount_usd', 'amount_iqd', 'exchange_rate_usd_to_iqd',
        'party_name', 'reference_number', 'movement_date', 'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'amount' => 'decimal:2',
        'amount_usd' => 'decimal:2',
        'amount_iqd' => 'decimal:2',
        'exchange_rate_usd_to_iqd' => 'decimal:4',
        'movement_date' => 'date',
    ];

    const TYPES = [
        'purchase' => 'کڕین',
        'sale'     => 'فرۆشتن',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeNameAttribute()
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->reference_number)) {
                $prefix = $model->type === 'purchase' ? 'BUY-' : 'SEL-';
                $model->reference_number = $prefix . strtoupper(uniqid());
            }
        });
    }
}
