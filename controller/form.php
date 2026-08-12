<?php

declare(strict_types=1);

/*
 * WebbyCMS 2.0 - Legacy shim for controller/form.php (DEPRECATED).
 *
 * Form handling now runs inside WebbyCMS\Router before the page is rendered:
 * a POST with a "form" field loads pages/{page}/forms/{form}.php, then
 * redirects (or returns JSON for AJAX requests). This file exists only so old
 * code that included it keeps working during migration.
 */

require_once __DIR__ . '/legacy.php';
