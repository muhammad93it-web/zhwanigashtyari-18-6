<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
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
    }
}
