<?php
/**
 * php/redirects.php — 301 redirect handler for old/legacy URLs
 *
 * Include at the TOP of index.php / router.php before any output:
 *   require_once 'php/redirects.php';
 */

function csn_handle_redirects(): void {
    $uri  = $_SERVER['REQUEST_URI'] ?? '/';
    $path = strtolower(trim(parse_url($uri, PHP_URL_PATH), '/'));
    $qs   = $_SERVER['QUERY_STRING'] ?? '';

    // ── 1. Old query-string URLs → clean URLs ─────────────────────────────────
    // /stays.php?id=123  → /listing/stays  (no per-item redirect since we use static HTML)
    // /cars.php?type=muv → /listing/cars
    $legacyPageMap = [
        'stays.php'       => '/listing/stays',
        'cars.php'        => '/listing/cars',
        'bikes.php'       => '/listing/bikes',
        'attractions.php' => '/listing/attractions',
        'restaurants.php' => '/listing/restaurants',
        'buses.php'       => '/listing/buses',
        'blog.php'        => '/blogs',
        'hotel.php'       => '/listing/stays',
        'vehicle.php'     => '/listing/cars',
    ];
    foreach ($legacyPageMap as $old => $new) {
        if ($path === $old || $path === ltrim($old, '/')) {
            _csn_301($new);
        }
    }

    // ── 2. Old blog-detail with query string → clean blog URL ─────────────────
    // /blog-detail.php?id=5 → /blogs/5-slug (we redirect to /blogs if no slug known)
    if (($path === 'blog-detail.php' || $path === 'blog-detail') && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        // Try to build slug from DB if config is loaded
        if (function_exists('getDB')) {
            try {
                $db   = getDB();
                $blog = $db->fetchOne("SELECT id, title FROM blogs WHERE id = ? AND status = 'published'", [$id]);
                if ($blog) {
                    $slug = $id . '-' . strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $blog['title']), '-'));
                    _csn_301('/blogs/' . $slug);
                }
            } catch (Exception $e) { /* fall through */ }
        }
        _csn_301('/blogs');
    }

    // ── 3. www → non-www (if running on www subdomain) ────────────────────────
    $host = $_SERVER['HTTP_HOST'] ?? '';
    if (strpos($host, 'www.') === 0) {
        $newHost = substr($host, 4);
        $scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        _csn_301($scheme . '://' . $newHost . $uri, true);
    }

    // ── 4. HTTP → HTTPS ───────────────────────────────────────────────────────
    // (Handled by .htaccess in production; this is a PHP fallback)
    if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            // Behind a proxy that terminates SSL — already HTTPS, skip
        }
        // Uncomment to force HTTPS via PHP (usually better done in .htaccess):
        // _csn_301('https://' . $host . $uri, true);
    }

    // ── 5. Specific legacy slug redirects ─────────────────────────────────────
    $slugRedirects = [
        // 'old-path' => 'new-path'
        'aurangabad-hotels'       => '/listing/stays',
        'aurangabad-car-rental'   => '/listing/cars',
        'aurangabad-bike-rental'  => '/listing/bikes',
        'ajanta-caves-tour'       => '/listing/attractions',
        'ellora-caves-tour'       => '/listing/attractions',
        'aurangabad-restaurants'  => '/listing/restaurants',
        'aurangabad-bus'          => '/listing/buses',
        'travel-guide'            => '/blogs',
    ];
    if (isset($slugRedirects[$path])) {
        _csn_301($slugRedirects[$path]);
    }
}

function _csn_301(string $location, bool $full_url = false): void {
    if (!$full_url && strpos($location, 'http') !== 0) {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'csnexplore.com';
        $location = $scheme . '://' . $host . $location;
    }
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $location);
    exit;
}

// Auto-run on include
csn_handle_redirects();
