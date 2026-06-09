<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    protected $fillable = [
        'user_id', 'party_name', 'direction', 'currency', 'amount',
        'amount_usd', 'amount_iqd', 'exchange_rate_usd_to_iqd', 'status',
        'description', 'reference_number', 'debt_date', 'due_date', 'paid_date', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_usd' => 'decimal:2',
        'amount_iqd' => 'decimal:2',
        'exchange_rate_usd_to_iqd' => 'decimal:4',
        'debt_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    const DIRECTIONS = [
        'receivable' => 'قەرزی لای خەڵک (بۆ ئێمە)',
        'payable'    => 'قەرزی ئێمە (لەسەر ئێمە)',
    ];

    const STATUSES = [
        'open' => 'نەدراوەتەوە',
        'paid' => 'دراوەتەوە',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDirectionNameAttribute()
    {
        return self::DIRECTIONS[$this->direction] ?? $this->direction;
    }

    public function getStatusNameAttribute()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->reference_number)) {
                $model->reference_number = 'DBT-' . strtoupper(uniqid());
            }
        });
    }
}
