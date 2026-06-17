<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Replit Preview Path Prefix (development preview only)
|--------------------------------------------------------------------------
| When this app is served behind the Replit reverse proxy under a path
| prefix (e.g. "/jwani"), strip that prefix from the request URI so the
| framework routes match normally. This is gated on the PREVIEW_PREFIX
| environment variable, which is ONLY set by the Replit preview workflow.
| On the live cPanel host it is never set, so this block is a no-op and
| the production site is completely unaffected.
*/

$previewPrefix = getenv('PREVIEW_PREFIX');
if (is_string($previewPrefix) && $previewPrefix !== '' && getenv('REPL_ID')) {
    $previewPrefix = '/' . trim($previewPrefix, '/');
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    if ($requestUri === $previewPrefix || strpos($requestUri, $previewPrefix . '/') === 0) {
        $stripped = substr($requestUri, strlen($previewPrefix));
        $_SERVER['REQUEST_URI'] = ($stripped === false || $stripped === '') ? '/' : $stripped;
    }
}

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
*/

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
