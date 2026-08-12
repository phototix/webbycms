<?php

declare(strict_types=1);

/*
 * WebbyCMS 2.0 - Legacy shim for controller/define.php (DEPRECATED).
 *
 * URL parsing is now handled by WebbyCMS\Router. The legacy $page/$cate/$form
 * globals are provided by controller/legacy.php.
 */

require_once __DIR__ . '/legacy.php';
