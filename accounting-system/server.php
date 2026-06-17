<?php

/**
 * Laravel built-in dev server router.
 *
 * Used only by PHP's built-in web server (`php -S ... server.php`) for the
 * Replit development preview. It is never invoked on the live cPanel host,
 * where Apache serves requests directly through public/index.php.
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// Serve existing static files (CSS, JS, images) directly.
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php';
