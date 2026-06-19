<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramDeliveryLog extends Model
{
    protected $fillable = [
        'telegram_schedule_id',
        'content_type',
        'status',
        'trigger',
        'file_name',
        'message',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function schedule()
    {
        return $this->belongsTo(TelegramSchedule::class, 'telegram_schedule_id');
    }
}
