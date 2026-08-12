<?php

declare(strict_types=1);

/*
 * WebbyCMS 2.0 - Legacy shim for controller/conn.php (DEPRECATED).
 *
 * Database access is now handled by WebbyCMS\Db\Database (PDO) and configured
 * through the .env file. This file exists only so old code that included it
 * keeps working during migration.
 */

if (!defined('WEBBY_ROOT')) {
    define('WEBBY_ROOT', dirname(__DIR__));
}

require_once __DIR__ . '/legacy.php';

$GLOBALS['conn_mysql_host'] = (string) config('DB_HOST', '');
$GLOBALS['conn_mysql_username'] = (string) config('DB_USER', '');
$GLOBALS['conn_mysql_password'] = (string) config('DB_PASSWORD', '');
$GLOBALS['conn_mysql_database'] = (string) config('DB_NAME', '');
$GLOBALS['conn_mysql_host_dev'] = 'localhost';
$GLOBALS['conn_mysql_username_dev'] = 'root';
$GLOBALS['conn_mysql_password_dev'] = '';
$GLOBALS['conn_mysql_database_dev'] = '';
$GLOBALS['localhost_dev'] = '';
$GLOBALS['useLocalMySql'] = 'yes';
$GLOBALS['useDataTable'] = config('DB_ENABLED') ? 'yes' : 'no';

/* Legacy timezone kept for compatibility; set APP_TIMEZONE via .env if needed. */
date_default_timezone_set('Asia/Singapore');
