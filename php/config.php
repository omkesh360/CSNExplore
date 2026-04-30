<?php
// CSNExplore – Central config
if (!ob_get_level()) ob_start(); // Output buffering for performance
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
$_logDir = __DIR__ . '/../logs';
if (!is_dir($_logDir)) @mkdir($_logDir, 0755, true);
ini_set('error_log', $_logDir . '/php_errors.log');

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
        putenv($key . '=' . $value);
    }
}

// Define APP_ENV if not set
if (!defined('APP_ENV')) {
    define('APP_ENV', getenv('APP_ENV') ?: 'production');
}

define('JWT_SECRET', getenv('JWT_SECRET') ?: 'csnexplore_secure_jwt_2025_!@#$%');
define('ADMIN_EMAIL', getenv('ADMIN_EMAIL') ?: 'travelhubadmin@gmail.com');
define('CONTACT_PHONE', getenv('CONTACT_PHONE') ?: '+91-8600968888');
define('SUPPORT_EMAIL', getenv('SUPPORT_EMAIL') ?: 'supportcsnexplore@gmail.com');

// MailerLite Email Configuration
define('MAILERLITE_API_KEY', getenv('MAILERLITE_API_KEY') ?: '');
define('MAILERLITE_FROM_EMAIL', getenv('MAILERLITE_FROM_EMAIL') ?: 'noreply@csnexplore.com');
define('MAILERLITE_FROM_NAME', getenv('MAILERLITE_FROM_NAME') ?: 'CSN Explore');
define('ADMIN_NOTIFICATION_EMAIL', getenv('ADMIN_NOTIFICATION_EMAIL') ?: 'supportcsnexplore@gmail.com');

// Cloudflare Turnstile
define('TURNSTILE_SITE_KEY',   getenv('TURNSTILE_SITE_KEY')   ?: '');
define('TURNSTILE_SECRET_KEY', getenv('TURNSTILE_SECRET_KEY') ?: '');

// SMTP Configuration for PHPMailer
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.gmail.com');
define('SMTP_PORT', getenv('SMTP_PORT') ?: 587);
define('SMTP_USERNAME', getenv('SMTP_USERNAME') ?: '');
define('SMTP_PASSWORD', getenv('SMTP_PASSWORD') ?: '');
define('SMTP_ENCRYPTION', getenv('SMTP_ENCRYPTION') ?: 'tls');

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

// Dynamic Base Path Detection
$_dir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
$_base = trim($_dir, '/\\');
$_base = str_replace(['php/api', 'php'], '', $_base);
$_base = trim($_base, '/\\');

// Normalize case: use actual filesystem folder name to prevent case mismatch
// e.g. /csNexplore2.0 → /CSNexplore2.0
if ($_base && $_base !== '.') {
    $_realBase = basename(dirname(__DIR__));
    // Only override if it's a case-insensitive match (same letters, different case)
    if (strtolower($_realBase) === strtolower($_base)) {
        $_base = $_realBase;
    }
}
define('BASE_PATH', $_base && $_base !== '.' ? '/' . $_base : '');

// Security Headers
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self' https:; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://challenges.cloudflare.com https://www.googletagmanager.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self' https: https://www.google-analytics.com;");
