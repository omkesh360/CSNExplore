<?php
declare(strict_types=1);
// CSNExplore – Central config
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
    http_response_code($code);
    header('Content-Type: application/json');
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

// Security Headers
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");

function sanitizeHtml($html) {
    if (!class_exists('HTMLPurifier_Config')) return $html;
    $config = HTMLPurifier_Config::createDefault();
    $config->set('HTML.Allowed', 'p,b,i,u,em,strong,a[href|title|target],ul,ol,li,br,h1,h2,h3,h4,h5,h6,img[src|alt|width|height],span[style],div[style]');
    $purifier = new HTMLPurifier($config);
    return $purifier->purify($html);
}
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self' https:; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://challenges.cloudflare.com https://www.googletagmanager.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self' https: https://www.google-analytics.com;");

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
