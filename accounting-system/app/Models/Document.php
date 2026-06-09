<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'user_id', 'title', 'doc_type', 'reference_number', 'recipient',
        'body', 'doc_date', 'notes',
    ];

    protected $casts = [
        'doc_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->reference_number)) {
                $model->reference_number = 'DOC-' . strtoupper(uniqid());
            }
        });
    }
}
