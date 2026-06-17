<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaborPayment extends Model
{
    protected $fillable = [
        'user_id', 'project_id', 'worker_id', 'worker_name', 'role',
        'date', 'is_hourly', 'hours', 'hourly_rate', 'amount', 'currency', 'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'is_hourly' => 'boolean',
        'hours' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
