<?php
/**
 * CSNExplore – Performance Bootstrap
 * Include this FIRST in config.php (before any other require_once).
 *
 * Applies runtime PHP performance tweaks that cannot be set in php.ini on
 * shared hosting:
 *   - OPcache tuning hints
 *   - Realpath cache sizing
 *   - JIT compiler activation (PHP 8.0+)
 *   - Memory limits
 *   - Output buffering + compression
 *   - Strict error handling (no display, always log)
 */

declare(strict_types=1);

/* ── 1. Error handling: silent in production, always log ─────────────────── */
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

/* ── 2. Memory & execution limits ────────────────────────────────────────── */
ini_set('memory_limit', '256M');
ini_set('max_execution_time', '60');

/* ── 3. Realpath cache – reduces costly disk stat() calls on every include ─ */
// Increase from the tiny 16K default to 4MB, and extend TTL from 120s to 2h
ini_set('realpath_cache_size', '4M');
ini_set('realpath_cache_ttl', '7200');

/* ── 4. OPcache runtime hints (safe no-ops if OPcache is unavailable) ──────  */
if (function_exists('opcache_get_status')) {
    ini_set('opcache.enable', '1');
    ini_set('opcache.memory_consumption', '128');
    ini_set('opcache.interned_strings_buffer', '16');
    ini_set('opcache.max_accelerated_files', '10000');
    ini_set('opcache.revalidate_freq', '60');   // check for file changes every 60s
    ini_set('opcache.fast_shutdown', '1');
    ini_set('opcache.save_comments', '1');      // required for PHPDoc annotations
    ini_set('opcache.enable_file_override', '1');

    /* JIT: tracing mode gives the best speedup for typical PHP code.
     * Buffer of 64MB is generous but safe. Set to 0 to disable on shared hosts. */
    if (PHP_VERSION_ID >= 80000) {
        ini_set('opcache.jit', 'tracing');
        ini_set('opcache.jit_buffer_size', '64M');
    }
}

/* ── 5. Output buffering + gzip compression ──────────────────────────────── */
// Only start a fresh buffer — never nest if config.php already called ob_start()
if (!ob_get_level()) {
    if (!ini_get('zlib.output_compression') && extension_loaded('zlib')) {
        // Let zlib handle HTTP-level gzip transparently (level 6 = good balance)
        ini_set('zlib.output_compression', '1');
        ini_set('zlib.output_compression_level', '6');
        ob_start();
    } else {
        ob_start();
    }
}

/* ── 6. Session security & performance tweaks ────────────────────────────── */
// Use cookie-only sessions (no session ID in URL — XSS safe)
ini_set('session.use_only_cookies', '1');
ini_set('session.use_strict_mode', '1');   // reject uninitialized session IDs
// Secure cookie: only send session cookie over HTTPS on production
$_isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
          || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
          || (($_SERVER['SERVER_PORT'] ?? '80') == '443');
ini_set('session.cookie_secure', $_isHttps ? '1' : '0');
ini_set('session.cookie_httponly', '1');     // JS cannot read the session cookie
ini_set('session.cookie_samesite', 'Lax');  // CSRF protection
// GC: clean up expired sessions every 100 requests (1% probability)
ini_set('session.gc_probability', '1');
ini_set('session.gc_divisor', '100');
ini_set('session.gc_maxlifetime', '3600');   // sessions expire after 1 hour idle
// Use SHA-256 for session IDs (more entropy than legacy MD5/SHA1)
ini_set('session.hash_function', '0');      // PHP 8 uses 32-byte random by default
unset($_isHttps);

/**
 * Helper: regenerate the session ID safely after login/privilege change.
 * Call this immediately after validating credentials to prevent session fixation.
 *
 * Usage:  if (session_status() === PHP_SESSION_ACTIVE) csn_regenerate_session();
 */
function csn_regenerate_session(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) return;
    session_regenerate_id(true); // true = delete old session file immediately
}

