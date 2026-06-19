<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

/*
 * ناردنی خۆکار بۆ تێلێگرام — هەر خولەکێک کاتە چالاکەکان دەپشکنرێن.
 * لەسەر هۆستی cPanel پێویستە cron دابنرێت بۆ کارپێکردنی ئەمە
 * (یان ڕاستەوخۆ: `php artisan telegram:dispatch`). بڕوانە پەڕەی تێلێگرام بۆ ڕێنمایی.
 */
Schedule::command('telegram:dispatch')->everyMinute()->withoutOverlapping();
