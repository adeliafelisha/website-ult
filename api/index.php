<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$basePath = dirname(__DIR__);
$storagePath = sys_get_temp_dir().'/ult-unpad-storage';

$runtimeEnvironment = [
    'APP_CONFIG_CACHE' => sys_get_temp_dir().'/ult-config.php',
    'APP_EVENTS_CACHE' => sys_get_temp_dir().'/ult-events.php',
    'APP_PACKAGES_CACHE' => sys_get_temp_dir().'/ult-packages.php',
    'APP_ROUTES_CACHE' => sys_get_temp_dir().'/ult-routes.php',
    'APP_SERVICES_CACHE' => sys_get_temp_dir().'/ult-services.php',
    'VIEW_COMPILED_PATH' => $storagePath.'/framework/views',
];

if (getenv('VERCEL')) {
    foreach ($runtimeEnvironment as $key => $value) {
        if (! getenv($key)) {
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

register_shutdown_function(static function (): void {
    $error = error_get_last();

    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        error_log(sprintf(
            '[ULT_FATAL] %s in %s:%d',
            $error['message'],
            $error['file'],
            $error['line'],
        ));
    }
});

foreach ([
    $storagePath.'/app/public',
    $storagePath.'/framework/cache/data',
    $storagePath.'/framework/sessions',
    $storagePath.'/framework/views',
    $storagePath.'/logs',
] as $directory) {
    if (! is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
}

if (! getenv('APP_URL') && getenv('VERCEL_URL')) {
    $appUrl = 'https://'.getenv('VERCEL_URL');
    putenv("APP_URL={$appUrl}");
    $_ENV['APP_URL'] = $appUrl;
    $_SERVER['APP_URL'] = $appUrl;
}

if (! getenv('LOG_CHANNEL')) {
    putenv('LOG_CHANNEL=stderr');
    $_ENV['LOG_CHANNEL'] = 'stderr';
    $_SERVER['LOG_CHANNEL'] = 'stderr';
}

require $basePath.'/vendor/autoload.php';

$app = require_once $basePath.'/bootstrap/app.php';
$app->useStoragePath($storagePath);

$kernel = $app->make(Kernel::class);
$response = $kernel->handle($request = Request::capture());
$response->send();
$kernel->terminate($request, $response);
