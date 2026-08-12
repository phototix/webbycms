<?php

declare(strict_types=1);

/*
 * WebbyCMS 2.0 - Front Controller
 *
 * Every request is routed through this file. Apache (with the included
 * .htaccess) rewrites /page/cate/action/id to this script.
 */

require __DIR__ . '/vendor/autoload.php';

use WebbyCMS\Bootstrap;

$app = Bootstrap::boot(__DIR__);

$response = $app->handle($app->request());
$response->send();
