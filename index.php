<?php
error_reporting(E_ALL);

if (PHP_SAPI == 'cli-server') {
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

session_start();

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/src/bootstrap.php';

$middleware = require __DIR__ . '/src/middleware.php';
$middleware($app);

$routes = require __DIR__ . '/src/routes.php';
$routes($app);

$app->run();
