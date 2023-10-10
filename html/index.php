<?php

namespace Ketcau;

use Ketcau\Kernel;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

// require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

$autoload = dirname(__DIR__). '/vendor/autoload.php';

require $autoload;

error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

$env = isset($_SERVER['APP_ENV']) ? $_SERVER['APP_ENV'] : 'debug';
$debug = isset($_SERVER['APP_DEBUG']) ? $_SERVER['APP_DEBUG'] : ('prod' !== $env);

if ($debug) {
    umask(0000);
    Debug::enable();
}

$request = Request::createFromGlobals();

$kernel = new Kernel($env, $debug);
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);

/*
return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
*/
