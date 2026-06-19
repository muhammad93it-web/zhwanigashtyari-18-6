<?php

namespace App\Console\Commands;

use App\Models\TelegramSchedule;
use App\Services\TelegramScheduleRunner;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TelegramDispatch extends Command
{
    protected $signature = 'telegram:dispatch';

    protected $description = 'ناردنی خۆکاری ڕاپۆرت و باکئەپ بۆ تێلێگرام بەپێی کاتە دیاریکراوەکان';

    public function handle(TelegramScheduleRunner $runner): int
    {
        $now = Carbon::now(config('app.timezone'));
        $schedules = TelegramSchedule::where('is_active', true)->get();

        $processed = 0;
        foreach ($schedules as $schedule) {
            if (! $schedule->isDue($now)) {
                continue;
            }

            $res = $runner->run($schedule, 'schedule');
            $processed++;

            $this->line(($res['ok'] ? '✓ OK   ' : '✗ FAIL ') . $schedule->content_type . ' #' . $schedule->id
                . ($res['ok'] ? '' : ' — ' . ($res['error'] ?? '')));
        }

        $this->info("Telegram dispatch finished at {$now->toDateTimeString()} — delivered: {$processed}");

        return self::SUCCESS;
    }
}
