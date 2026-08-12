<?php

declare(strict_types=1);

/*
 * Development router for the PHP built-in web server.
 *
 *   php -S 0.0.0.0:8000 router.php
 *
 * Emulates the Apache .htaccess rewrites by serving real files directly and
 * routing everything else through the front controller.
 */

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file = __DIR__ . rawurldecode($path);

if ($path !== '/' && is_file($file)) {
    return false;
}

require __DIR__ . '/index.php';
