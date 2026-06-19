<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class AppSetting extends Model
{
    protected $fillable = ['key', 'value'];

    /** Get a raw setting value. */
    public static function get(string $key, $default = null)
    {
        $row = static::query()->where('key', $key)->first();

        return $row && $row->value !== null && $row->value !== '' ? $row->value : $default;
    }

    /** Create or update a raw setting value. */
    public static function set(string $key, $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /** Get a decrypted setting value (for secrets such as the bot token). */
    public static function getEncrypted(string $key, $default = null)
    {
        $val = static::get($key);

        if ($val === null || $val === '') {
            return $default;
        }

        try {
            return Crypt::decryptString($val);
        } catch (\Throwable $e) {
            return $default;
        }
    }

    /** Store an encrypted setting value. Empty/null clears it. */
    public static function setEncrypted(string $key, ?string $value): void
    {
        if ($value === null || $value === '') {
            static::set($key, '');

            return;
        }

        static::set($key, Crypt::encryptString($value));
    }
}
