<?php

declare(strict_types=1);

/*
 * WebbyCMS 2.0 - REMOVED functionality.
 *
 * The old "login" module set a forgeable cookie (webbycms_login) with any
 * value taken from the URL. That was not real authentication and has been
 * removed for security reasons.
 *
 * Use the new auth API instead:
 *
 *   use WebbyCMS\Auth\AuthManager;
 *   $auth->login($userId, ['roles' => ['admin']]);
 *   $auth->check();   $auth->logout();
 *
 * Wire a WebbyCMS\Auth\UserProviderInterface to your own user store.
 */

require_once __DIR__ . '/legacy.php';
