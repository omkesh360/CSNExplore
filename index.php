<?php
// Root index.php - Router for PHP built-in server

$requestUri = $_SERVER['REQUEST_URI'];
$requestUri = strtok($requestUri, '?'); // Remove query string

// API requests go to PHP backend
if (strpos($requestUri, '/api/') === 0) {
    require __DIR__ . '/php/index.php';
    exit;
}

// For root, serve index.html
if ($requestUri === '/' || $requestUri === '') {
    readfile(__DIR__ . '/public/index.html');
    exit;
}

// Try to serve static file from public directory
$publicPath = __DIR__ . '/public' . $requestUri;

// If file exists in public, serve it with proper MIME type
if (is_file($publicPath)) {
    $ext = strtolower(pathinfo($publicPath, PATHINFO_EXTENSION));
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'eot' => 'application/vnd.ms-fontobject'
    ];
    
    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
    }
    
    readfile($publicPath);
    exit;
}

// Try with .html extension
$htmlPath = $publicPath . '.html';
if (is_file($htmlPath)) {
    return false; // Let PHP serve the HTML file
}

// For HTML pages without extension, serve them
if (!preg_match('/\.(css|js|jpg|jpeg|png|gif|svg|ico|woff|woff2|ttf|eot|json)$/i', $requestUri)) {
    // Check if HTML file exists
    if (is_file(__DIR__ . '/public' . $requestUri . '.html')) {
        readfile(__DIR__ . '/public' . $requestUri . '.html');
        exit;
    }
    // Default to index.html for SPA routing
    readfile(__DIR__ . '/public/index.html');
    exit;
}

// File not found
http_response_code(404);
echo '404 Not Found';


