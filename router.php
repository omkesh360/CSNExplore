<?php
/**
 * router.php
 * PHP built-in web server router. Serves all static files from public/ directory.
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// 1. Block sensitive directories
if (preg_match('#^/(data|php)/.*$#', $uri) && strpos($uri, '/php/index.php') === false && strpos($uri, '/php/api/') === false) {
    http_response_code(403); echo "Access Denied"; return true;
}
if ($uri === '/.env') { http_response_code(403); echo "Access Denied"; return true; }

// 2. Route API requests to PHP backend
if (strpos($uri, '/api/') === 0) {
    $_SERVER['SCRIPT_NAME'] = '/php/index.php';
    include __DIR__ . '/php/index.php';
    return true;
}

// 3. Root -> index.html
if ($uri === '/') {
    header('Content-Type: text/html');
    readfile(__DIR__ . '/public/index.html');
    return true;
}

// MIME type map
$mimeTypes = [
    'css'   => 'text/css',
    'js'    => 'application/javascript',
    'json'  => 'application/json',
    'jpg'   => 'image/jpeg',
    'jpeg'  => 'image/jpeg',
    'png'   => 'image/png',
    'gif'   => 'image/gif',
    'svg'   => 'image/svg+xml',
    'webp'  => 'image/webp',
    'ico'   => 'image/x-icon',
    'woff'  => 'font/woff',
    'woff2' => 'font/woff2',
    'html'  => 'text/html',
    'txt'   => 'text/plain',
];

function serveFile($path, $mimeTypes) {
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
    }
    $cacheable = ['jpg','jpeg','png','gif','svg','webp','ico','css','js','woff','woff2'];
    if (in_array($ext, $cacheable)) {
        header('Cache-Control: public, max-age=86400');
    } else {
        header('Cache-Control: no-cache, must-revalidate');
    }
    readfile($path);
    return true;
}

// 4. Serve static files from public/ directory
$publicFile = __DIR__ . '/public' . $uri;
if (file_exists($publicFile) && !is_dir($publicFile)) {
    return serveFile($publicFile, $mimeTypes);
}

// 5. Try .html extension (e.g. /stays -> stays.html)
$htmlFile = __DIR__ . '/public' . $uri . '.html';
if (file_exists($htmlFile)) {
    return serveFile($htmlFile, $mimeTypes);
}

// 6. SPA fallback - serve index.html
header('Content-Type: text/html');
readfile(__DIR__ . '/public/index.html');
return true;
