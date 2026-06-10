<?php
declare(strict_types=1);
// CSNExplore – Central config

// Load .env file if exists
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value);
        // Strip surrounding quotes (single or double)
        if (strlen($value) >= 2 && (
            ($value[0] === '"'  && $value[-1] === '"') ||
            ($value[0] === "'"  && $value[-1] === "'")
        )) {
            $value = substr($value, 1, -1);
        }
        if (function_exists('putenv')) {
            putenv($key . '=' . $value);
        }
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

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

// Advanced Settings
define('MAINTENANCE_MODE', env('MAINTENANCE_MODE', false)); // Set to true to enable maintenance mode
define('WHITE_LABEL_MODE', env('WHITE_LABEL_MODE', false)); // Set to true to hide CSNExplore branding
define('CDN_URL', env('CDN_URL', '')); // Optional CDN for static assets

// CORS: restrict to your production domain (set CORS_ORIGIN in .env to override)
$_corsOrigin = env('CORS_ORIGIN', 'https://csnexplore.com');
// Allow local dev origins automatically
if (php_sapi_name() !== 'cli') {
    $requestOrigin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $allowedOrigins = array_filter(array_map('trim', explode(',', $_corsOrigin)));
    if (in_array($requestOrigin, $allowedOrigins, true)) {
        define('CORS_ORIGIN', $requestOrigin);
    } elseif (strpos($requestOrigin, 'localhost') !== false || strpos($requestOrigin, '127.0.0.1') !== false) {
        define('CORS_ORIGIN', $requestOrigin); // allow localhost in dev
    } else {
        define('CORS_ORIGIN', $allowedOrigins[0] ?? 'https://csnexplore.com');
    }
} else {
    define('CORS_ORIGIN', 'https://csnexplore.com');
}

// CSNExplore – Central config
date_default_timezone_set('Asia/Kolkata');

// ── Aggressive Performance & OPcache Checks ──────────────────────────────────
if (function_exists('opcache_is_script_cached') && function_exists('opcache_compile_file')) {
    // Basic assurance OPcache is active
    ini_set('opcache.enable', '1');
    ini_set('opcache.memory_consumption', '128');
    ini_set('opcache.interned_strings_buffer', '8');
    ini_set('opcache.max_accelerated_files', '10000');
    ini_set('opcache.revalidate_freq', '60');
}

// ── Object Caching System (Redis -> Fallback) ──────────────────────────────
class ObjectCache {
    private static $redis = null;
    private static $enabled = false;
    
    public static function init() {
        if (extension_loaded('redis')) {
            try {
                self::$redis = new Redis();
                if (self::$redis->connect(env('REDIS_HOST', '127.0.0.1'), env('REDIS_PORT', 6379))) {
                    self::$enabled = true;
                }
            } catch (Exception $e) { self::$enabled = false; }
        }
    }
    
    public static function get($key) {
        if (self::$enabled) {
            $val = self::$redis->get($key);
            return $val !== false ? json_decode($val, true) : false;
        }
        $path = __DIR__ . '/../cache/obj_' . md5($key) . '.cache';
        if (file_exists($path) && (filemtime($path) + 3600 > time())) {
            $raw = file_get_contents($path);
            return $raw !== false ? json_decode($raw, true) : false;
        }
        return false;
    }
    
    public static function set($key, $value, $ttl = 3600) {
        if (self::$enabled) return self::$redis->setex($key, $ttl, json_encode($value));
        $path = __DIR__ . '/../cache/obj_' . md5($key) . '.cache';
        return file_put_contents($path, json_encode($value));
    }
}
ObjectCache::init();

// ── Performance bootstrap MUST be first (OPcache, JIT, realpath cache, ob) ──
require_once __DIR__ . '/performance.php';
require_once __DIR__ . '/cache-headers.php';
applyCacheHeaders('page');
// Error logging path (log_errors set by performance.php, just point to the right file)
$_logDir = __DIR__ . '/../logs';
if (!is_dir($_logDir)) @mkdir($_logDir, 0755, true);
ini_set('error_log', $_logDir . '/php_errors.log');

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}



// Define APP_ENV if not set
if (!defined('APP_ENV')) {
    define('APP_ENV', env('APP_ENV', 'production'));
}

// Initialize Sentry for Centralized Telemetry
$sentryDsn = env('SENTRY_DSN');
if ($sentryDsn && class_exists('\Sentry\State\Hub')) {
    \Sentry\init([
        'dsn' => $sentryDsn,
        'environment' => APP_ENV,
        // Capture 100% of transactions for performance monitoring
        'traces_sample_rate' => 1.0,
    ]);
}

$jwtSecret = env('JWT_SECRET');
if (empty($jwtSecret) || $jwtSecret === 'csnexplore_secure_jwt_2025_!@#$%') {
    if (APP_ENV === 'production') {
        throw new Exception("CRITICAL ERROR: JWT_SECRET is not configured or uses the insecure default value. System cannot boot.");
    } else {
        $jwtSecret = 'dev_fallback_secret_csnexplore_2026_!@#';
    }
}
define('JWT_SECRET', $jwtSecret);
define('ADMIN_EMAIL', env('ADMIN_EMAIL', 'travelhubadmin@gmail.com'));
define('CONTACT_PHONE', env('CONTACT_PHONE', '+91-8600968888'));
define('SUPPORT_EMAIL', env('SUPPORT_EMAIL', 'supportcsnexplore@gmail.com'));
define('SITE_URL', env('SITE_URL', 'https://csnexplore.com'));


// MailerLite Email Configuration
define('MAILERLITE_API_KEY', env('MAILERLITE_API_KEY', ''));
define('MAILERLITE_FROM_EMAIL', env('MAILERLITE_FROM_EMAIL', 'noreply@csnexplore.com'));
define('MAILERLITE_FROM_NAME', env('MAILERLITE_FROM_NAME', 'CSN Explore'));
define('ADMIN_NOTIFICATION_EMAIL', env('ADMIN_NOTIFICATION_EMAIL', 'supportcsnexplore@gmail.com'));

// Cloudflare Turnstile
define('TURNSTILE_SITE_KEY',   env('TURNSTILE_SITE_KEY', ''));
define('TURNSTILE_SECRET_KEY', env('TURNSTILE_SECRET_KEY', ''));

// SMTP Configuration for PHPMailer
define('SMTP_HOST', env('SMTP_HOST', 'smtp.gmail.com'));
define('SMTP_PORT', env('SMTP_PORT', 587));
define('SMTP_USERNAME', env('SMTP_USERNAME', ''));
define('SMTP_PASSWORD', env('SMTP_PASSWORD', ''));
define('SMTP_ENCRYPTION', env('SMTP_ENCRYPTION', 'tls'));

require_once __DIR__ . '/database.php';

// ── Helpers ──────────────────────────────────────────────────────────────────
function sendJson($data, $code = 200) {
    while (ob_get_level()) { ob_end_clean(); } // Prevent stray output
    if (php_sapi_name() !== 'cli' && !headers_sent()) {
        http_response_code($code);
        header('Content-Type: application/json');
    }
    echo json_encode($data);
    exit;
}

function sendError($msg, $code = 400) {
    sendJson(['error' => $msg], $code);
}

function getJsonInput() {
    $raw = file_get_contents('php://input');
    return json_decode($raw, true) ?? [];
}
// Centralized Slug Generation
function generateSlug($type, $id, $name) {
    // Decode HTML entities recursively to handle double-escaped input (e.g. &amp;amp;)
    $nameDecoded = html_entity_decode($name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    while ($nameDecoded !== $name) {
        $name = $nameDecoded;
        $nameDecoded = html_entity_decode($name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    // Replace '&' with 'and' for clean, readable URLs
    $name = str_replace('&', 'and', $nameDecoded);
    
    $t = strtolower(trim($name));
    $t = preg_replace('/[^a-z0-9\s-]/', '', $t);
    $t = preg_replace('/[\s-]+/', '-', $t);
    $t = trim($t, '-');
    return $type . '-' . $id . '-' . substr($t, 0, 60);
}
function getDB() {
    return Database::getInstance();
}

function sanitize($val) {
    return htmlspecialchars(strip_tags(trim((string)$val)), ENT_QUOTES, 'UTF-8');
}

// Consistent HTML escaping wrapper
function esc($val) {
    return htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
}

// Dynamic Base Path Detection (Robust)
$projectRoot = str_replace('\\', '/', dirname(__DIR__));
$scriptFilename = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME'] ?? '');
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

$basePath = '';
if ($scriptFilename && $scriptName && strpos($scriptFilename, $projectRoot) === 0) {
    $relPath = substr($scriptFilename, strlen($projectRoot));
    if (substr($scriptName, -strlen($relPath)) === $relPath) {
        $basePath = substr($scriptName, 0, -strlen($relPath));
    }
}

// Fallback if CLI or edge case
if ($basePath === '') {
    $docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? '');
    if (!empty($docRoot) && strpos($projectRoot, $docRoot) === 0) {
        $basePath = substr($projectRoot, strlen($docRoot));
    }
}

// WAF: Basic Web Application Firewall (skip for large uploads or multipart forms)
$_waf_ct = $_SERVER['CONTENT_TYPE'] ?? '';
$_waf_cl = (int)($_SERVER['CONTENT_LENGTH'] ?? 0);
if (stripos($_waf_ct, 'multipart/form-data') === false && $_waf_cl < 65536) {
    $waf_payload = urldecode(($_SERVER['REQUEST_URI'] ?? '') . ' ' . substr(file_get_contents('php://input'), 0, 4096));
    if (preg_match('/(?:<script|%3Cscript|UNION.+SELECT|CONCAT\(|WAITFOR DELAY)/i', $waf_payload)) {
        http_response_code(403);
        die('WAF: Request blocked.');
    }
}
unset($_waf_ct, $_waf_cl);

// Ensure proper error reporting (off in production, on in dev)
$isLocalEnv = (strpos(__DIR__, 'htdocs') !== false || strpos(__DIR__, 'xampp') !== false || php_uname('s') === 'Windows NT');
if ($isLocalEnv) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
}
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');
if (php_sapi_name() === 'cli') {
    // Detect local environment (XAMPP/htdocs) vs production (Hostinger)
    if (strpos(__DIR__, 'htdocs') !== false || strpos(__DIR__, 'xampp') !== false || php_uname('s') === 'Windows NT') {
        $basePath = '/CSNExplore';
    } else {
        $basePath = '';
    }
}
$basePath = rtrim($basePath, '/');
define('BASE_PATH', $basePath);

// Generate CSRF token if not exists in cookie
if (empty($_COOKIE['csrf_token'])) {
    $csrfToken = bin2hex(random_bytes(32));
    $secure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    if (php_sapi_name() !== 'cli' && !headers_sent()) {
        if (defined('PHP_VERSION_ID') && PHP_VERSION_ID >= 70300) {
            setcookie('csrf_token', $csrfToken, [
                'expires' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => false,
                'samesite' => 'Lax'
            ]);
        } else {
            setcookie('csrf_token', $csrfToken, 0, '/; SameSite=Lax', '', $secure, false);
        }
    }
    $_COOKIE['csrf_token'] = $csrfToken;
}

// Global CSRF validation helper
function validateCsrf() {
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
        if ($method === 'OPTIONS') return;
        
        $cookieToken = $_COOKIE['csrf_token'] ?? '';
        $headerToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        
        if (empty($cookieToken) || empty($headerToken) || !hash_equals($cookieToken, $headerToken)) {
            sendError('Invalid or missing CSRF token', 403);
        }
    }
}

// Security Headers
if (php_sapi_name() !== 'cli' && !headers_sent()) {
    header("X-Frame-Options: SAMEORIGIN");
    header("X-XSS-Protection: 1; mode=block");
    header("X-Content-Type-Options: nosniff");
}

function sanitizeHtml($html) {
    if (!class_exists('HTMLPurifier_Config')) return $html;
    $config = HTMLPurifier_Config::createDefault();
    $config->set('HTML.Allowed', 'p,b,i,u,em,strong,a[href|title|target],ul,ol,li,br,h1,h2,h3,h4,h5,h6,img[src|alt|width|height],span[style],div[style]');
    $purifier = new HTMLPurifier($config);
    return $purifier->purify($html);
}
if (php_sapi_name() !== 'cli' && !headers_sent()) {
    header("Referrer-Policy: strict-origin-when-cross-origin");
    header("Content-Security-Policy: default-src 'self' https:; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://challenges.cloudflare.com https://www.googletagmanager.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self' https: https://www.google-analytics.com;");
}

function get_working_image_url($imgSrc) {
    if (!$imgSrc) return '';
    $imgSrc = (string)$imgSrc;
    if (strpos($imgSrc, 'http://') === 0 || strpos($imgSrc, 'https://') === 0) {
        return $imgSrc;
    }
    
    // Remove hardcoded local directory from DB if it exists, so BASE_PATH handles it correctly
    $imgSrc = preg_replace('#^/?CSNExplore/#', '/', $imgSrc);
    
    $basePath = rtrim(BASE_PATH, '/');
    if ($basePath !== '') {
        if (strpos($imgSrc, $basePath) === 0) {
            return $imgSrc;
        }
    }
    return $basePath . '/' . ltrim($imgSrc, '/');
}

// ── Plugin System ────────────────────────────────────────────────────────────
$pluginDir = __DIR__ . '/../plugins';
if (is_dir($pluginDir)) {
    foreach (glob($pluginDir . '/*.php') as $pluginFile) {
        require_once $pluginFile;
    }
}

/**
 * Upgraded global rate limiter: APCu -> DB -> Filesystem fallback
 */
function rateLimit($key, $limit = 5, $period = 60) {
    // Option 1: Use APCu if available (extremely fast and stateless)
    if (extension_loaded('apcu') && ini_get('apc.enabled')) {
        $apcuKey = 'rate_limit_' . md5($key);
        $count = apcu_fetch($apcuKey);
        if ($count === false) {
            apcu_store($apcuKey, 1, $period);
            $count = 1;
        } else {
            $count = apcu_inc($apcuKey);
        }
        return $count <= $limit;
    }

    // Option 2: Database fallback
    try {
        $db = getDB();
        $now = time();
        $expires = $now + $period;
        
        // Clean up expired rate limits with 5% probability
        if (rand(1, 20) === 1) {
            $db->query("DELETE FROM rate_limits WHERE expires_at < ?", [$now]);
        }
        
        $row = $db->fetchOne("SELECT attempts, expires_at FROM rate_limits WHERE ip_key = ?", [$key]);
        
        if ($row) {
            if ($now > $row['expires_at']) {
                // Expired, reset
                $db->update('rate_limits', [
                    'attempts' => 1,
                    'first_attempt' => $now,
                    'expires_at' => $expires
                ], 'ip_key = :key', [':key' => $key]);
                $attempts = 1;
            } else {
                $db->query("UPDATE rate_limits SET attempts = attempts + 1 WHERE ip_key = ?", [$key]);
                $attempts = (int)$row['attempts'] + 1;
            }
        } else {
            $db->insert('rate_limits', [
                'ip_key' => $key,
                'attempts' => 1,
                'first_attempt' => $now,
                'expires_at' => $expires
            ]);
            $attempts = 1;
        }
        return $attempts <= $limit;
    } catch (Exception $dbEx) {
        // Log database error and fallback to filesystem rate limiting (to avoid blocking users)
        error_log('Database rate limiting error: ' . $dbEx->getMessage() . '. Falling back to file-based.');
    }

    // Option 3: Filesystem fallback
    $dir = __DIR__ . '/../logs/rate_limit';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    
    $file = $dir . '/' . md5($key) . '.json';
    $now = time();
    $data = ['count' => 0, 'first_attempt' => $now];
    
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if ($now - $data['first_attempt'] > $period) {
            $data = ['count' => 1, 'first_attempt' => $now];
        } else {
            $data['count']++;
        }
    } else {
        $data['count'] = 1;
    }
    
    file_put_contents($file, json_encode($data));
    
    if (rand(1, 100) === 1) {
        $files = glob($dir . '/*.json');
        if ($files) {
            foreach ($files as $f) {
                if ($now - filemtime($f) > 86400) @unlink($f);
            }
        }
    }

    return $data['count'] <= $limit;
}
