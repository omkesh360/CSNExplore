<?php
// php/api/debug_headers.php – SECURED: admin access only
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';
header('Content-Type: application/json');
requireAdmin();
sendJson([
    'cookies' => array_map(fn($v) => '***', $_COOKIE), // redact cookie values
    'headers' => function_exists('getallheaders') ? array_map(function($k, $v) {
        // Redact Authorization header
        return strtolower($k) === 'authorization' ? '***' : $v;
    }, array_keys(getallheaders()), getallheaders()) : [],
    'method' => $_SERVER['REQUEST_METHOD'],
    'uri'    => $_SERVER['REQUEST_URI'],
]);
