<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    protected $fillable = [
        'incoming_invoice_number',
        'supplier_id', 'deliverer_name', 'deliverer_phone', 'deliverer_address',
        'vehicle_number', 'vehicle_type', 'user_id', 'project_id',
        'total_amount', 'paid_amount', 'remaining_amount',
        'total_iqd', 'total_usd', 'paid_iqd', 'paid_usd', 'remaining_iqd', 'remaining_usd',
        'date', 'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'total_iqd' => 'decimal:2',
        'total_usd' => 'decimal:2',
        'paid_iqd' => 'decimal:2',
        'paid_usd' => 'decimal:2',
        'remaining_iqd' => 'decimal:2',
        'remaining_usd' => 'decimal:2',
        'date' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
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
        return $this->hasMany(PurchaseInvoiceDetail::class);
    }

    /** ناوی فرۆشیار/گەیەنەر بۆ پیشاندان (دابینکەر یان کەسی دەستی). */
    public function getPartyNameAttribute(): string
    {
        return $this->supplier?->name ?? $this->deliverer_name ?? '—';
    }
}
