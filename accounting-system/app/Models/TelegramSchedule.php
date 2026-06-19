<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TelegramSchedule extends Model
{
    protected $fillable = [
        'title',
        'content_type',
        'frequency',
        'day_of_month',
        'send_time',
        'is_active',
        'last_sent_at',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'day_of_month' => 'integer',
        'last_sent_at' => 'datetime',
    ];

    public const CONTENT_TYPES = [
        'backup'         => 'باکئەپی داتابەیس',
        'daily_report'   => 'ڕاپۆرتی ڕۆژانە',
        'monthly_report' => 'ڕاپۆرتی مانگانە (مانگی ڕابردوو)',
        'transactions'   => 'لیستی وەسڵ و مامەڵەکان',
    ];

    public const FREQUENCIES = [
        'daily'   => 'ڕۆژانە',
        'monthly' => 'مانگانە',
    ];

    public function logs()
    {
        return $this->hasMany(TelegramDeliveryLog::class);
    }

    public function contentTypeLabel(): string
    {
        return self::CONTENT_TYPES[$this->content_type] ?? $this->content_type;
    }

    public function frequencyLabel(): string
    {
        return self::FREQUENCIES[$this->frequency] ?? $this->frequency;
    }

    /**
     * Build the scheduled datetime within a given month/day reference, applying
     * send_time and clamping day_of_month to the number of days in that month.
     */
    private function occurrenceIn(Carbon $monthRef): ?Carbon
    {
        $occ = $monthRef->copy()->day(1);

        if ($this->frequency === 'monthly') {
            $day = max(1, min((int) ($this->day_of_month ?: 1), $occ->daysInMonth));
            $occ->day($day);
        } else {
            $occ->day($monthRef->day);
        }

        try {
            $occ->setTimeFromTimeString($this->send_time);
        } catch (\Throwable $e) {
            return null;
        }

        return $occ->seconds(0);
    }

    /**
     * The most recent scheduled occurrence at or before $now (in the app
     * timezone). For daily this is today's send_time, or yesterday's if today's
     * has not arrived yet. For monthly it is this month's clamped day_of_month,
     * or the previous month's if this month's has not arrived yet. Returning the
     * latest *past* occurrence (rather than the current period's) makes dispatch
     * tolerant of cron that runs late or was down across a day/month boundary.
     */
    public function latestOccurrence(Carbon $now): ?Carbon
    {
        $occ = $this->occurrenceIn($now);

        if (! $occ) {
            return null;
        }

        if ($now->lt($occ)) {
            // This period's occurrence is still in the future — step back one period.
            if ($this->frequency === 'monthly') {
                $occ = $this->occurrenceIn($now->copy()->day(1)->subMonthNoOverflow());
            } else {
                $occ = $this->occurrenceIn($now->copy()->subDay());
            }
        }

        return $occ;
    }

    /**
     * Due when the latest past occurrence has not yet been delivered.
     * Tolerant of late cron runs and missed period boundaries.
     */
    public function isDue(Carbon $now): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $occ = $this->latestOccurrence($now);

        if (! $occ || $now->lt($occ)) {
            return false;
        }

        return $this->last_sent_at === null || $this->last_sent_at->lt($occ);
    }
}
