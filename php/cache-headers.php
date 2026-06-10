<?php
/**
 * cache-headers.php
 * Include this at the TOP of every public-facing PHP page (before any output).
 * It reads settings.json and sends the correct Cache-Control headers.
 *
 * Usage:
 *   require_once __DIR__ . '/php/cache-headers.php';
 *   applyCacheHeaders('page');   // 'page' | 'api' | 'nocache'
 */

function applyCacheHeaders(string $type = 'page'): void {
    if (php_sapi_name() === 'cli' || headers_sent()) {
        return;
    }
    // Never cache admin pages
    if (strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/') !== false) {
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        return;
    }

    $settingsFile = __DIR__ . '/settings.json';
    $enabled      = false;
    $ttl          = ['page' => 3600, 'html' => 86400];

    if (file_exists($settingsFile)) {
        $s = json_decode(file_get_contents($settingsFile), true);
        $enabled = $s['features']['caching']['enabled'] ?? false;
        $ttl['page'] = ($s['features']['caching']['ttl']['page'] ?? 60) * 60;   // minutes → seconds
        $ttl['html'] = ($s['features']['caching']['ttl']['html'] ?? 24) * 3600; // hours → seconds
    }

    if (!$enabled || $type === 'nocache') {
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        return;
    }

    switch ($type) {
        case 'api':
            // API responses: short private cache
            header('Cache-Control: private, max-age=60');
            break;

        case 'page':
        default:
            // Public pages: cache for configured TTL, allow stale while revalidating
            $maxAge = $ttl['page'];
            $swr    = min(600, (int)($maxAge * 0.1)); // 10% of TTL for stale-while-revalidate
            header("Cache-Control: public, max-age={$maxAge}, stale-while-revalidate={$swr}");
            header('Vary: Accept-Encoding');
            break;
    }
}
