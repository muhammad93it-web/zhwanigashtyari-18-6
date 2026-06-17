<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name', 'client_id', 'location', 'budget', 'status', 'notes', 'is_active',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    const STATUSES = [
        'active'    => 'چالاک',
        'completed' => 'تەواوبوو',
        'on_hold'   => 'ڕاگیراو',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseInvoiceDetail::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function getStatusNameAttribute()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
