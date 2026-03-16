<?php
// Enable GZIP output compression for faster API responses
if (extension_loaded('zlib') && !headers_sent()) {
    ob_start('ob_gzhandler');
} else {
    ob_start();
}

// Main router for PHP API

// Get the request URI and remove query string
$requestUri = $_SERVER['REQUEST_URI'];
$requestUri = strtok($requestUri, '?');

// Remove /api prefix if present
$requestUri = preg_replace('#^/api#', '', $requestUri);

// Provide PATH_INFO for the API scripts to read
$_SERVER['PATH_INFO'] = $requestUri;

// Route to appropriate handler
if (preg_match('#^/auth(/.*)?$#', $requestUri)) {
    require __DIR__ . '/api/auth.php';
} elseif ($requestUri === '/homepage-content') {
    require __DIR__ . '/api/homepage-dynamic.php';
} elseif ($requestUri === '/about-contact') {
    require __DIR__ . '/api/about-contact.php';
} elseif ($requestUri === '/dashboard') {
    require __DIR__ . '/api/dashboard.php';
} elseif ($requestUri === '/upload' || $requestUri === '/images') {
    require __DIR__ . '/api/upload.php';
} elseif (preg_match('#^/bookings(/.*)?$#', $requestUri)) {
    require __DIR__ . '/api/bookings.php';
} elseif (preg_match('#^/(stays|cars|bikes|restaurants|attractions|buses|users|vendors)(/.*)?$#', $requestUri)) {
    require __DIR__ . '/api/listings.php';
} elseif (preg_match('#^/blogs(/.*)?$#', $requestUri)) {
    require __DIR__ . '/api/blogs.php';
} elseif (preg_match('#^/cache(/.*)?$#', $requestUri)) {
    require __DIR__ . '/api/cache.php';
} else {
    header('Content-Type: application/json');
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
}
