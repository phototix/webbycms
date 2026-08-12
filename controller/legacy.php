<?php

declare(strict_types=1);

/*
 * WebbyCMS 2.0 - Legacy compatibility layer (TRANSITIONAL, DEPRECATED).
 *
 * Reproduces the global variables and helper functions used by pre-2.0 pages
 * so existing pages keep working during migration. New code should use the
 * helpers in src/WebbyCMS/helpers.php instead. See UPGRADE.md.
 */

use WebbyCMS\Bootstrap;

if (!defined('WEBBY_ROOT')) {
    define('WEBBY_ROOT', dirname(__DIR__));
}

$__legacyApp = Bootstrap::booted() ? Bootstrap::instance() : null;

if ($__legacyApp !== null) {
    $__legacyReq = $__legacyApp->request();

    /*
     * Only touch the session for visitors that already carry a session cookie.
     * This keeps anonymous GET requests session-free so the page cache works.
     */
    $__legacyHasSession = $__legacyReq->hasSessionCookie();

    $GLOBALS['page'] = (string) $__legacyReq->segment('page', 'home');
    $GLOBALS['cate'] = (string) $__legacyReq->segment('cate', '');
    $GLOBALS['action'] = (string) $__legacyReq->segment('action', '');
    $GLOBALS['id'] = (string) $__legacyReq->segment('id', '');
    $GLOBALS['sub'] = (string) $__legacyReq->segment('sub', '');
    $GLOBALS['form'] = (string) $__legacyReq->input('form', '');
    $GLOBALS['token'] = (string) $__legacyReq->input('token', '');
    $GLOBALS['lang'] = (string) $__legacyReq->input('lang') ?: ($__legacyHasSession ? $__legacyApp->session()->get('lang', 'en') : 'en');
    $GLOBALS['row'] = '';
    $GLOBALS['systemError'] = $__legacyHasSession ? $__legacyApp->flash()->peek('error') : '';
    $GLOBALS['systemSucces'] = $__legacyHasSession ? $__legacyApp->flash()->peek('success') : '';
    $GLOBALS['newInsertID'] = '';
    $GLOBALS['currentURl'] = 'http://'
        . (string) $__legacyReq->server('HTTP_HOST', 'localhost')
        . (string) $__legacyReq->server('REQUEST_URI', '/');

    $GLOBALS['Today'] = date('Y-m-d');
    $GLOBALS['Day'] = date('d');
    $GLOBALS['Month'] = date('m');
    $GLOBALS['Year'] = date('Y');
    $GLOBALS['Hour'] = date('g');
    $GLOBALS['Minute'] = date('i');
    $GLOBALS['Seconds'] = date('s');
    $GLOBALS['Time'] = date('g:i A');
    $GLOBALS['Token'] = md5($GLOBALS['Today'] . 'webbycms' . uniqid() . $GLOBALS['Time']);

    $GLOBALS['strWebTitle'] = (string) $__legacyApp->config()->get('APP_NAME', 'Welcome');
    $GLOBALS['strStartDate'] = '2012-12-31';
}

if (!function_exists('initWebbyCMS')) {
    function initWebbyCMS(): string
    {
        return 'Welcome to use WebbyCMS';
    }
}

if (!function_exists('stopWeb')) {
    /**
     * Legacy no-op: the kernel owns the request lifecycle.
     */
    function stopWeb(): void
    {
    }
}
