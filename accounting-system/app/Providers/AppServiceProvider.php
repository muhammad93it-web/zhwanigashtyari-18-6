<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Use Tailwind-styled pagination
        Paginator::defaultView('pagination::tailwind');
        Paginator::defaultSimpleView('pagination::simple-tailwind');

        // Replit preview only: make generated URLs include the proxy path
        // prefix. Gated on PREVIEW_BASE_URL, which is ONLY set by the Replit
        // preview workflow — never on the live cPanel host. No-op in production.
        // Hardened: requires a Replit environment signal (REPL_ID) AND the URL
        // host must be a *.replit.dev domain, so a stray/malicious env var on a
        // shared host can never redirect generated links to an external site.
        $previewBaseUrl = getenv('PREVIEW_BASE_URL');
        if (is_string($previewBaseUrl) && $previewBaseUrl !== '' && getenv('REPL_ID')) {
            $host = parse_url($previewBaseUrl, PHP_URL_HOST);
            if (is_string($host) && (str_ends_with($host, '.replit.dev') || str_ends_with($host, '.repl.co'))) {
                URL::forceScheme('https');
                URL::forceRootUrl($previewBaseUrl);
            }
        }
    }
}
