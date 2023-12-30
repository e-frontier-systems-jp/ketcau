<?php

namespace Ketcau;

use Dotenv\Dotenv;
use Ketcau\Kernel;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

// require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

$autoload = dirname(__DIR__). '/vendor/autoload.php';

require $autoload;

if (!isset($_SERVER['APP_ENV'])) {
    if (!class_exists(Dotenv::class)) {
        throw new \RuntimeException('APP_ENV environment variable is not defined. You need to define environment variables for configuration or add "symfony/dotenv" as a Composer dependency to load variables from a .env file.');
    }

    if (file_exists(__DIR__.'/../.env')) {
        (Dotenv::createUnsafeMutable(__DIR__. '/../'))->load();

        if (str_contains(getenv('DATABASE_URL'), 'sqlite') && !extension_loaded('pdo_sqlite')) {
            (Dotenv::createUnsafeMutable(__DIR__. '/../', '.env.install'))->load();
        }
    } else {
        (Dotenv::createUnsafeMutable(__DIR__. '/../', '.env.install'))->load();
    }
}
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

$env = $_SERVER['APP_ENV'] ?? 'debug';
$debug = $_SERVER['APP_DEBUG'] ?? ('prod' !== $env);

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
