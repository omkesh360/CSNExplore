<?php
declare(strict_types=1);

// Modern Bootstrap file for CSNExplore

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use DI\ContainerBuilder;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\CallbackHandler;

// 1. Load Environment Variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

// Robust env helper
if (!function_exists('env')) {
    function env($key, $default = null) {
        if (isset($_ENV[$key])) return $_ENV[$key];
        if (isset($_SERVER[$key])) return $_SERVER[$key];
        $val = function_exists('getenv') ? getenv($key) : false;
        if ($val !== false) return $val;
        return $default;
    }
}

// Ensure APP_ENV is defined early
if (!defined('APP_ENV')) {
    define('APP_ENV', env('APP_ENV', 'production'));
}

// 2. Setup Logging (Monolog)
$logDir = __DIR__ . '/../logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}
$logger = new Logger('csnexplore');
$logger->pushHandler(new StreamHandler($logDir . '/app.log', Logger::DEBUG));

// 3. Setup Error Handling (Whoops)
$whoops = new Run();
if (APP_ENV === 'local') {
    // Show detailed errors in local environment
    $whoops->pushHandler(new PrettyPageHandler());
} else {
    // Log errors and show generic message in production
    $whoops->pushHandler(new CallbackHandler(function($exception, $inspector, $run) use ($logger) {
        $logger->error($exception->getMessage(), ['exception' => $exception]);
        
        // Return JSON if it's an API request or keep it simple for now
        if (php_sapi_name() !== 'cli' && !headers_sent()) {
            http_response_code(500);
            header('Content-Type: text/html; charset=UTF-8');
            echo "<h1>500 - Internal Server Error</h1><p>Something went wrong. The error has been logged.</p>";
        }
    }));
}
$whoops->register();

// 4. Setup Dependency Injection (PHP-DI)
$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions([
    // Define Logger
    Logger::class => $logger,
    
    // Define caching (Predis + Symfony Cache)
    \Symfony\Component\Cache\Adapter\AdapterInterface::class => \DI\factory(function() {
        if (extension_loaded('redis')) {
            $client = new \Redis();
            try {
                if ($client->connect(env('REDIS_HOST', '127.0.0.1'), (int)env('REDIS_PORT', 6379))) {
                    return new \Symfony\Component\Cache\Adapter\RedisAdapter($client, 'csn_');
                }
            } catch (\Exception $e) {}
        }
        // Fallback to Predis if phpredis extension is not installed
        $client = new \Predis\Client([
            'scheme'  => 'tcp',
            'host'    => env('REDIS_HOST', '127.0.0.1'),
            'port'    => env('REDIS_PORT', 6379),
            'timeout' => 1.0,
        ]);
        return new \Symfony\Component\Cache\Adapter\RedisAdapter($client, 'csn_');
    }),
    
    // Define Guzzle HTTP Client
    \GuzzleHttp\Client::class => \DI\create(\GuzzleHttp\Client::class),
    
    // Note: The Database instance (existing custom PDO wrapper) is loaded in config.php.
    // If you plan to fully migrate to illuminate/database, we configure Eloquent below.
]);

$container = $containerBuilder->build();

// Make the DI container accessible globally
if (!function_exists('app')) {
    function app($className = null) {
        global $container;
        if ($className) {
            return $container->get($className);
        }
        return $container;
    }
}

// 5. Setup Illuminate/Database (Eloquent/Query Builder)
// This runs alongside the existing custom `Database` class, allowing gradual migration.
use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;
$isProduction = APP_ENV === 'production';
$host = env($isProduction ? 'DB_HOST_PROD' : 'DB_HOST_LOCAL', '127.0.0.1');
if ($host === 'localhost') $host = '127.0.0.1';

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $host,
    'port'      => env('DB_PORT', '3306'),
    'database'  => env($isProduction ? 'DB_NAME_PROD' : 'DB_NAME_LOCAL', ''),
    'username'  => env($isProduction ? 'DB_USER_PROD' : 'DB_USER_LOCAL', ''),
    'password'  => env($isProduction ? 'DB_PASS_PROD' : 'DB_PASS_LOCAL', ''),
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

return $container;
