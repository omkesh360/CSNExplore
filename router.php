<?php
/**
 * router.php
 * This script serves as a router for the PHP built-in web server (php -S).
 * It mimics the behavior of .htaccess for local development.
 */

// Parse the request URI
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// 1. Prevent access to sensitive files/directories (like .htaccess does)
if (preg_match('#^/(data|php)/.*$#', $uri) && strpos($uri, '/php/index.php') === false && strpos($uri, '/php/api/') === false) {
    echo "Access Denied";
    http_response_code(403);
    return true;
}
if ($uri === '/.env') {
    echo "Access Denied";
    http_response_code(403);
    return true;
}

// 2. Redirect API requests to the PHP API index
if (strpos($uri, '/api/') === 0) {
    $_SERVER['SCRIPT_NAME'] = '/php/index.php';
    include __DIR__ . '/php/index.php';
    return true;
}

// 3. Serve static files directly if they exist at the exact path
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false; // Built-in server will serve it natively
}

// 4. Serve index.html for root
if ($uri === '/') {
    header('Content-Type: text/html');
    readfile(__DIR__ . '/public/index.html');
    return true;
}

// 5. Serve HTML files without extension (e.g., /stays -> /stays.html)
if (!preg_match('#^/api/#', $uri)) {
    $htmlFile = __DIR__ . '/public' . $uri . '.html';
    if (file_exists($htmlFile)) {
        header('Content-Type: text/html');
        readfile($htmlFile);
        return true;
    }
}

// 6. Serve other files from the public directory (images, css, js)
if (!preg_match('#^/api/#', $uri)) {
    $publicFile = __DIR__ . '/public' . $uri;
    if (file_exists($publicFile) && !is_dir($publicFile)) {
        $extension = strtolower(pathinfo($publicFile, PATHINFO_EXTENSION));
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'html' => 'text/html'
        ];
        
        if (isset($mimeTypes[$extension])) {
            header('Content-Type: ' . $mimeTypes[$extension]);
        }
        
        // Add caching headers for static assets
        $cacheableExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'css', 'js', 'json', 'woff', 'woff2'];
        if (in_array($extension, $cacheableExtensions)) {
            header('Cache-Control: public, max-age=31536000, immutable');
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        } else {
            header('Cache-Control: no-cache, must-revalidate');
        }
        
        readfile($publicFile);
        return true;
    }
}

// 7. If nothing matches, serve index.html (SPA routing fallback)
if (!preg_match('#^/api/#', $uri)) {
    header('Content-Type: text/html');
    readfile(__DIR__ . '/public/index.html');
    return true;
}

// Fallback error
http_response_code(404);
echo "404 Not Found";
return true;
