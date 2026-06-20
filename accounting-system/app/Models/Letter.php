<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Letter extends Model
{
    protected $fillable = [
        'user_id', 'reference_number', 'letter_date', 'recipient', 'subject', 'body',
    ];

    protected $casts = [
        'letter_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
