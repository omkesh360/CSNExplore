<?php
// router.php - Handles clean URLs for PHP built-in web server
// Usage: php -S localhost:8000 router.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$uri  = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);
$path = ltrim($path, '/');

// Preserve query string for PHP files
$qs = $_SERVER['QUERY_STRING'] ?? '';

// ── 301 redirects (legacy URLs) ───────────────────────────────────────────────
if (file_exists('php/redirects.php')) {
    require_once 'php/redirects.php';
}

// ── 1. Real files (images, css, js, etc.) ────────────────────────────────────
if ($path !== '' && file_exists($path) && !is_dir($path)) {
    return false; // let built-in server handle it
}

// ── 2. Root / index ───────────────────────────────────────────────────────────
if ($path === '' || $path === 'index.php') {
    include 'index.php';
    return;
}
// Redirect /index → / (canonical URL)
if ($path === 'index') {
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
    header('Location: ' . $base . '/', true, 301);
    exit;
}

// ── 3. Listing detail pages: /listing-detail/cars-5-slug ─────────────────────
if (preg_match('#^listing-detail/([^/]+)/?$#', $path, $m)) {
    $slug = $m[1];
    // Check with and without .html extension
    $htmlFile = 'listing-detail/' . $slug;
    if (!str_ends_with($slug, '.html')) $htmlFile .= '.html';
    
    if (file_exists($htmlFile)) {
        header('Content-Type: text/html; charset=UTF-8');
        include $htmlFile; // Using include instead of readfile for potential PHP snippets inside
        return;
    }
}

// ── 4. Blog static HTML pages: /blogs/1-my-slug ──────────────────────────────
if (preg_match('#^blogs/(.+)$#', $path, $m)) {
    $htmlFile = 'blogs/' . $m[1];
    // with or without .html
    if (file_exists($htmlFile)) {
        header('Content-Type: text/html; charset=UTF-8');
        readfile($htmlFile);
        return;
    }
    if (file_exists($htmlFile . '.html')) {
        header('Content-Type: text/html; charset=UTF-8');
        readfile($htmlFile . '.html');
        return;
    }
}

// ── 5. Clean URL → PHP file map ───────────────────────────────────────────────
$routes = [
    'about'       => 'about.php',
    'contact'     => 'contact.php',
    'blogs'       => 'blogs.php',
    'login'       => 'login.php',
    'register'    => 'register.php',
    'privacy'     => 'privacy.php',
    'terms'       => 'terms.php',
    'my-booking'  => 'my-booking.php',
    'bus'         => 'bus.php',
    'blog-detail' => 'blog-detail.php',
    'subscribe'   => 'subscribe.php',
    'install'     => 'install.php',
];

// Strip trailing slash for matching
$cleanPath = rtrim($path, '/');

if (isset($routes[$cleanPath])) {
    include $routes[$cleanPath];
    return;
}

// ── 6. /listing/type ─────────────────────────────────────────────────────────
if (preg_match('#^listing/([a-zA-Z-]+)/?$#', $cleanPath, $m)) {
    $_GET['type'] = $m[1];
    include 'listing.php';
    return;
}

if ($cleanPath === 'listing') {
    include 'listing.php';
    return;
}

// ── 7. Direct .php file exists ───────────────────────────────────────────────
if (file_exists($cleanPath . '.php')) {
    include $cleanPath . '.php';
    return;
}

// ── 8. Direct .html file exists or extension-less html ──────────────────────
if (file_exists($path . '.html')) {
    header('Content-Type: text/html; charset=UTF-8');
    include $path . '.html';
    return;
}
if (file_exists($cleanPath . '.html')) {
    header('Content-Type: text/html; charset=UTF-8');
    include $cleanPath . '.html';
    return;
}
if (file_exists($path) && !is_dir($path)) {
    // If it's an HTML file just serve it
    if (str_ends_with($path, '.html')) {
        header('Content-Type: text/html; charset=UTF-8');
    }
    include $path;
    return;
}

// ── 9. Admin pages (directory) ───────────────────────────────────────────────
if (file_exists($cleanPath) && is_dir($cleanPath)) {
    // Try index.php in directory
    if (file_exists($cleanPath . '/index.php')) {
        include $cleanPath . '/index.php';
        return;
    }
}

// ── 10. 404 fallback ─────────────────────────────────────────────────────────
http_response_code(404);
include 'index.php';
return;
