<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Http;

/**
 * Thin wrapper around the Telegram Bot API.
 * Token is read (decrypted) from app_settings; never logged.
 */
class TelegramService
{
    private ?string $token;
    private ?string $chatId;

    public function __construct()
    {
        $this->token  = AppSetting::getEncrypted('telegram_bot_token');
        $this->chatId = AppSetting::get('telegram_chat_id');
    }

    public function isConfigured(): bool
    {
        return ! empty($this->token) && ! empty($this->chatId);
    }

    public function hasToken(): bool
    {
        return ! empty($this->token);
    }

    private function endpoint(string $method): string
    {
        return 'https://api.telegram.org/bot' . $this->token . '/' . $method;
    }

    /**
     * @return array{ok:bool, error?:string, result?:array}
     */
    public function getMe(): array
    {
        if (empty($this->token)) {
            return ['ok' => false, 'error' => 'تۆکنی بۆت دانەنراوە.'];
        }

        try {
            $res  = Http::timeout(20)->get($this->endpoint('getMe'));
            $json = $res->json();
            if ($res->successful() && ($json['ok'] ?? false)) {
                return ['ok' => true, 'result' => $json['result'] ?? []];
            }

            return ['ok' => false, 'error' => $json['description'] ?? ('HTTP ' . $res->status())];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $this->cleanError($e->getMessage())];
        }
    }

    /**
     * @return array{ok:bool, error?:string, result?:mixed}
     */
    public function sendMessage(string $html, ?string $chatId = null): array
    {
        $target = $chatId ?? $this->chatId;
        if (empty($this->token) || empty($target)) {
            return ['ok' => false, 'error' => 'تۆکن یان چات ئایدی دانەنراوە.'];
        }

        try {
            $res = Http::timeout(30)->asForm()->post($this->endpoint('sendMessage'), [
                'chat_id'                  => $target,
                'text'                     => $html,
                'parse_mode'               => 'HTML',
                'disable_web_page_preview' => true,
            ]);

            return $this->result($res);
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $this->cleanError($e->getMessage())];
        }
    }

    /**
     * @return array{ok:bool, error?:string, result?:mixed}
     */
    public function sendDocument(string $filePath, ?string $caption = null, ?string $chatId = null): array
    {
        $target = $chatId ?? $this->chatId;
        if (empty($this->token) || empty($target)) {
            return ['ok' => false, 'error' => 'تۆکن یان چات ئایدی دانەنراوە.'];
        }
        if (! is_file($filePath)) {
            return ['ok' => false, 'error' => 'فایلی نێردراو نەدۆزرایەوە.'];
        }

        try {
            $payload = ['chat_id' => $target];
            if ($caption !== null && $caption !== '') {
                $payload['caption']    = mb_substr($caption, 0, 1024);
                $payload['parse_mode'] = 'HTML';
            }

            $res = Http::timeout(180)
                ->attach('document', fopen($filePath, 'r'), basename($filePath))
                ->post($this->endpoint('sendDocument'), $payload);

            return $this->result($res);
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $this->cleanError($e->getMessage())];
        }
    }

    private function result($res): array
    {
        $json = $res->json();
        if ($res->successful() && ($json['ok'] ?? false)) {
            return ['ok' => true, 'result' => $json['result'] ?? null];
        }

        return ['ok' => false, 'error' => $json['description'] ?? ('HTTP ' . $res->status())];
    }

    /** Strip any accidental token leakage from error text. */
    private function cleanError(string $msg): string
    {
        if ($this->token) {
            $msg = str_replace($this->token, '***', $msg);
        }

        return mb_substr($msg, 0, 300);
    }
}
