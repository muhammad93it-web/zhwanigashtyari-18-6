<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\TelegramDeliveryLog;
use App\Models\TelegramSchedule;
use App\Services\TelegramScheduleRunner;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TelegramController extends Controller
{
    public function index(TelegramService $telegram)
    {
        $schedules    = TelegramSchedule::orderBy('send_time')->get();
        $logs         = TelegramDeliveryLog::with('schedule')->latest('id')->limit(25)->get();
        $hasToken     = $telegram->hasToken();
        $chatId       = AppSetting::get('telegram_chat_id', '');
        $isConfigured = $telegram->isConfigured();
        $contentTypes = TelegramSchedule::CONTENT_TYPES;
        $frequencies  = TelegramSchedule::FREQUENCIES;

        return view('telegram.index', compact(
            'schedules', 'logs', 'hasToken', 'chatId', 'isConfigured', 'contentTypes', 'frequencies'
        ));
    }

    public function saveSettings(Request $request)
    {
        $request->validate([
            'telegram_chat_id'   => ['nullable', 'string', 'max:64'],
            'telegram_bot_token' => ['nullable', 'string', 'max:255'],
        ], [
            'telegram_chat_id.max'   => 'چات ئایدی زۆر درێژە.',
            'telegram_bot_token.max' => 'تۆکنەکە زۆر درێژە.',
        ]);

        AppSetting::set('telegram_chat_id', trim((string) $request->input('telegram_chat_id')));

        if ($request->boolean('clear_token')) {
            AppSetting::setEncrypted('telegram_bot_token', null);
        } else {
            $token = trim((string) $request->input('telegram_bot_token'));
            if ($token !== '') {
                AppSetting::setEncrypted('telegram_bot_token', $token);
            }
        }

        return back()->with('success', 'ڕێکخستنەکانی تێلێگرام پاشکەوتکران.');
    }

    public function test(TelegramService $telegram)
    {
        $me = $telegram->getMe();
        if (! ($me['ok'] ?? false)) {
            return back()->with('error', 'پەیوەندی سەرکەوتوو نەبوو. دڵنیابە لە دروستی تۆکنی بۆت: ' . ($me['error'] ?? ''));
        }

        $botName = $me['result']['first_name'] ?? ($me['result']['username'] ?? 'بۆت');

        if (! $telegram->isConfigured()) {
            return back()->with('success', 'تۆکنی بۆت دروستە (' . $botName . '). بەڵام چات ئایدی دانەنراوە — تکایە چات ئایدی زیاد بکە و پاشکەوتی بکە.');
        }

        $send = $telegram->sendMessage(
            '✅ <b>تاقیکردنەوەی پەیوەندی</b>' . "\n" . 'سیستەمی ژمێریاری ژوانی گەشتیاری بە سەرکەوتوویی بە تێلێگرامەوە پەیوەست بوو.'
        );

        if (! ($send['ok'] ?? false)) {
            return back()->with('error', 'بۆت دۆزرایەوە (' . $botName . ') بەڵام نامەکە نەگەیشت. دڵنیابە کە لە تێلێگرام دەستت بە بۆتەکە کردووە (Start) و چات ئایدی دروستە: ' . ($send['error'] ?? ''));
        }

        return back()->with('success', 'پەیوەندی سەرکەوتوو بوو ✅ — نامەیەکی تاقیکردنەوە نێردرا بۆ تێلێگرام. ناوی بۆت: ' . $botName);
    }

    public function storeSchedule(Request $request)
    {
        $data = $request->validate([
            'title'        => ['nullable', 'string', 'max:120'],
            'content_type' => ['required', Rule::in(array_keys(TelegramSchedule::CONTENT_TYPES))],
            'frequency'    => ['required', Rule::in(array_keys(TelegramSchedule::FREQUENCIES))],
            'send_time'    => ['required', 'date_format:H:i'],
            'day_of_month' => ['nullable', 'integer', 'min:1', 'max:31', 'required_if:frequency,monthly'],
        ], [
            'content_type.required'    => 'جۆری ناردن هەڵبژێرە.',
            'content_type.in'          => 'جۆری ناردن نادروستە.',
            'frequency.required'       => 'دووبارەبوونەوە هەڵبژێرە.',
            'send_time.required'       => 'کاتی ناردن دیاری بکە.',
            'send_time.date_format'    => 'کاتەکە دەبێت بەشێوەی HH:MM بێت.',
            'day_of_month.required_if' => 'بۆ ناردنی مانگانە، ڕۆژی مانگ دیاری بکە.',
            'day_of_month.min'         => 'ڕۆژی مانگ دەبێت لە نێوان ١ بۆ ٣١ بێت.',
            'day_of_month.max'         => 'ڕۆژی مانگ دەبێت لە نێوان ١ بۆ ٣١ بێت.',
        ]);

        TelegramSchedule::create([
            'title'        => $data['title'] ?? null,
            'content_type' => $data['content_type'],
            'frequency'    => $data['frequency'],
            'send_time'    => $data['send_time'],
            'day_of_month' => $data['frequency'] === 'monthly' ? ($data['day_of_month'] ?? 1) : null,
            'is_active'    => true,
        ]);

        return back()->with('success', 'کاتی ناردنی نوێ زیادکرا.');
    }

    public function toggleSchedule(TelegramSchedule $schedule)
    {
        $schedule->is_active = ! $schedule->is_active;
        $schedule->save();

        return back()->with('success', $schedule->is_active ? 'کاتەکە چالاککرا.' : 'کاتەکە ناچالاککرا.');
    }

    public function sendNow(TelegramSchedule $schedule, TelegramScheduleRunner $runner)
    {
        $res = $runner->run($schedule, 'manual');

        if ($res['ok'] ?? false) {
            return back()->with('success', 'بە سەرکەوتوویی نێردرا بۆ تێلێگرام ✅');
        }

        return back()->with('error', 'ناردن سەرکەوتوو نەبوو: ' . ($res['error'] ?? 'هەڵەی نەناسراو'));
    }

    public function destroySchedule(TelegramSchedule $schedule)
    {
        $schedule->delete();

        return back()->with('success', 'کاتی ناردن سڕایەوە.');
    }
}
