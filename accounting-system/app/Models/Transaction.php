<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'client_id',
        'user_id',
        'type',
        'currency',
        'amount',
        'amount_usd',
        'amount_iqd',
        'exchange_rate_usd_to_iqd',
        'description',
        'reference_number',
        'transaction_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_usd' => 'decimal:2',
        'amount_iqd' => 'decimal:2',
        'exchange_rate_usd_to_iqd' => 'decimal:4',
        'transaction_date' => 'date',
    ];

    // Transaction types
    const TYPE_SALE = 'sale';
    const TYPE_PURCHASE = 'purchase';
    const TYPE_DEBIT = 'debit';
    const TYPE_CREDIT = 'credit';

    const TYPES = [
        self::TYPE_SALE     => 'فرۆشتن',
        self::TYPE_PURCHASE => 'کڕین',
        self::TYPE_DEBIT    => 'پارەی بردراو / قەرز',
        self::TYPE_CREDIT   => 'پارەی هێنراو / دانەوەی قەرز',
    ];

    const CURRENCIES = ['USD', 'IQD'];

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

    public function getTypeColorAttribute()
    {
        return match($this->type) {
            self::TYPE_SALE     => 'text-emerald-400',
            self::TYPE_PURCHASE => 'text-red-400',
            self::TYPE_DEBIT    => 'text-amber-400',
            self::TYPE_CREDIT   => 'text-blue-400',
            default             => 'text-gray-400',
        };
    }

    public function getTypeBadgeAttribute()
    {
        return match($this->type) {
            self::TYPE_SALE     => 'badge-sale',
            self::TYPE_PURCHASE => 'badge-purchase',
            self::TYPE_DEBIT    => 'badge-debit',
            self::TYPE_CREDIT   => 'badge-credit',
            default             => 'badge-gray',
        };
    }

    /**
     * Auto-generate reference number before creating
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->reference_number)) {
                $transaction->reference_number = 'TXN-' . strtoupper(uniqid());
            }
        });
    }
}
