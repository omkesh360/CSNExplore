<?php
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
    require __DIR__ . '/api/homepage.php';
} elseif ($requestUri === '/dashboard') {
    require __DIR__ . '/api/dashboard.php';
} elseif ($requestUri === '/upload' || $requestUri === '/images') {
    require __DIR__ . '/api/upload.php';
} elseif (preg_match('#^/(stays|cars|bikes|restaurants|attractions|buses|bookings|users|vendors)(/.*)?$#', $requestUri)) {
    require __DIR__ . '/api/listings.php';
} else {
    header('Content-Type: application/json');
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
}
