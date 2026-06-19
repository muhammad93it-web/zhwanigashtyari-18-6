<?php

namespace App\Services;

use App\Models\TelegramDeliveryLog;
use App\Models\TelegramSchedule;

/**
 * Executes a single TelegramSchedule: builds the content, sends it, records a
 * delivery log, and (for scheduled triggers only) advances last_sent_at.
 * Shared by the telegram:dispatch command and the controller's "send now".
 */
class TelegramScheduleRunner
{
    public function __construct(
        private TelegramService $telegram,
        private BackupService $backup,
        private TelegramReportBuilder $reports,
    ) {}

    /**
     * @return array{ok:bool, error:?string}
     */
    public function run(TelegramSchedule $schedule, string $trigger = 'schedule'): array
    {
        if (! $this->telegram->isConfigured()) {
            $msg = 'تۆکن یان چات ئایدی تێلێگرام دانەنراوە.';
            $this->writeLog($schedule, $trigger, 'failed', null, $msg);

            return ['ok' => false, 'error' => $msg];
        }

        $path = null;
        try {
            $res  = $this->dispatchContent($schedule);
            $path = $res['path'] ?? null;
        } catch (\Throwable $e) {
            $res = ['ok' => false, 'error' => mb_substr($e->getMessage(), 0, 300)];
        } finally {
            if (! empty($path) && is_file($path)) {
                @unlink($path);
            }
        }

        $ok       = $res['ok'] ?? false;
        $fileName = $res['fileName'] ?? null;
        $error    = $ok ? null : ($res['error'] ?? 'هەڵەی نەناسراو');

        if ($ok && $trigger === 'schedule') {
            $schedule->last_sent_at = now();
            $schedule->save();
        }

        $this->writeLog($schedule, $trigger, $ok ? 'success' : 'failed', $fileName, $error);

        return ['ok' => $ok, 'error' => $error];
    }

    /**
     * @return array{ok:bool, error?:string, path?:string, fileName?:string}
     */
    private function dispatchContent(TelegramSchedule $schedule): array
    {
        return match ($schedule->content_type) {
            'backup'         => $this->doBackup(),
            'daily_report'   => $this->doDaily(),
            'monthly_report' => $this->doMonthly(),
            'transactions'   => $this->doTransactions($schedule),
            default          => ['ok' => false, 'error' => 'جۆری نێردراو نەناسراوە.'],
        };
    }

    private function doBackup(): array
    {
        $b        = $this->backup->generate(gzip: true);
        $path     = $b['path'];
        $fileName = $b['filename'];

        $size = @filesize($path) ?: 0;
        if ($size > 49 * 1024 * 1024) {
            return [
                'ok'       => false,
                'error'    => 'قەبارەی فایلی باکئەپ زۆر گەورەیە بۆ ناردن بە تێلێگرام (سنوور ٥٠MB).',
                'path'     => $path,
                'fileName' => $fileName,
            ];
        }

        $caption = "🗄 <b>باکئەپی داتابەیس</b>\n🕐 " . now()->format('Y-m-d H:i');
        $res = $this->telegram->sendDocument($path, $caption);

        return [
            'ok'       => $res['ok'],
            'error'    => $res['error'] ?? null,
            'path'     => $path,
            'fileName' => $fileName,
        ];
    }

    private function doDaily(): array
    {
        $res = $this->telegram->sendMessage($this->reports->dailyReport());

        return ['ok' => $res['ok'], 'error' => $res['error'] ?? null];
    }

    private function doMonthly(): array
    {
        $res = $this->telegram->sendMessage($this->reports->monthlyReport());

        return ['ok' => $res['ok'], 'error' => $res['error'] ?? null];
    }

    private function doTransactions(TelegramSchedule $schedule): array
    {
        $doc = $this->reports->transactionsDocument($schedule->frequency);

        $size = @filesize($doc['path']) ?: 0;
        if ($size > 49 * 1024 * 1024) {
            return [
                'ok'       => false,
                'error'    => 'قەبارەی فایلی مامەڵەکان زۆر گەورەیە بۆ ناردن بە تێلێگرام (سنوور ٥٠MB).',
                'path'     => $doc['path'],
                'fileName' => $doc['filename'],
            ];
        }

        $caption = "🧾 <b>لیستی مامەڵەکان</b>\n{$doc['periodLabel']} — {$doc['count']} مامەڵە";
        $res     = $this->telegram->sendDocument($doc['path'], $caption);

        return [
            'ok'       => $res['ok'],
            'error'    => $res['error'] ?? null,
            'path'     => $doc['path'],
            'fileName' => $doc['filename'],
        ];
    }

    private function writeLog(TelegramSchedule $schedule, string $trigger, string $status, ?string $fileName, ?string $message): void
    {
        TelegramDeliveryLog::create([
            'telegram_schedule_id' => $schedule->id,
            'content_type'         => $schedule->content_type,
            'status'               => $status,
            'trigger'              => $trigger,
            'file_name'            => $fileName,
            'message'              => $message ? mb_substr($message, 0, 1000) : null,
            'sent_at'              => $status === 'success' ? now() : null,
        ]);
    }
}
