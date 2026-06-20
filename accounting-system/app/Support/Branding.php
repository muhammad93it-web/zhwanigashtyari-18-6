<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class Branding
{
    /**
     * Logo as a base64 data URI, cached across requests so the file is only
     * read and encoded once. Cache key includes filemtime so replacing the
     * logo image automatically busts the cache.
     */
    public static function logoDataUri(): string
    {
        $path = public_path('images/logo.png');

        if (! is_file($path)) {
            return '';
        }

        $key = 'jwani_logo_uri_' . filemtime($path);

        return Cache::rememberForever($key, function () use ($path) {
            return 'data:image/png;base64,' . base64_encode(file_get_contents($path));
        });
    }
}
