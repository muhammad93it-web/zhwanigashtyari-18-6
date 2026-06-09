---
name: PHP/Laravel in this environment
description: How to get PHP, Composer, and a Laravel vendor folder working in this Replit env
---

# PHP & Composer are installable here

PHP and Composer are NOT preinstalled, but they CAN be installed via the package-management
module `php-8.2` (also `php-8.1`, `php-8.4`). The `php-8.2`/`php-8.4` modules bundle Composer.
Use `installProgrammingLanguage({ language: "php-8.2" })`.

**Why:** An earlier session wrongly assumed PHP/Composer were unavailable and hand-wrote a
Node ZIP writer to avoid them. They are available — just install the module.

**How to apply:** When a task needs `composer install`, a real Laravel `vendor/` folder, or
to verify a PHP app boots, install `php-8.2` first, then run composer/artisan normally.

# Laravel skeleton vs framework version must match

The "slim" skeleton — `bootstrap/app.php` using `Application::configure(...)`, `routes/console.php`,
no `app/Http/Kernel.php` / `Console/Kernel.php` / `Exceptions/Handler.php` — is **Laravel 11+**.
If composer pins `laravel/framework ^10`, boot fails with
`Method Illuminate\Foundation\Application::configure does not exist`.

Laravel 11 also does NOT ship a base `app/Http/Controllers/Controller.php`; if controllers
`extends Controller`, you must create the abstract base class yourself or routes fail with
`Class "App\Http\Controllers\Controller" not found`.

**Why:** Mixing a L11 skeleton with the L10 framework is a silent mismatch that only surfaces
at boot/route-list time.

# Bundling a Laravel app as a ZIP without the `zip` binary

`zip`/`tar`/`python3` are unavailable; bash `zip`/`tar` time out. Use `scripts/create-zip.mjs`
(Node + zlib `deflateRawSync`) to produce a compressed ZIP. It excludes `.env`, `.git`,
`node_modules`, `bootstrap/cache/*.php`, and `storage/framework|logs`. A full Laravel vendor
(~62MB, ~8k files) compresses to ~13MB.
