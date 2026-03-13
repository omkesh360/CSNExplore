<?php
/**
 * Security Headers
 * Sets security-related HTTP headers to protect against common attacks
 */

// Prevent clickjacking
header('X-Frame-Options: SAMEORIGIN');

// Enable XSS protection
header('X-XSS-Protection: 1; mode=block');

// Prevent MIME type sniffing
header('X-Content-Type-Options: nosniff');

// Referrer policy
header('Referrer-Policy: strict-origin-when-cross-origin');

// Content Security Policy
$csp = [
    "default-src 'self'",
    "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://fonts.googleapis.com",
    "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net",
    "img-src 'self' data: https: http:",
    "font-src 'self' https://fonts.gstatic.com data:",
    "connect-src 'self'",
    "frame-ancestors 'self'",
    "base-uri 'self'",
    "form-action 'self'"
];
header('Content-Security-Policy: ' . implode('; ', $csp));

// Permissions Policy (formerly Feature Policy)
$permissions = [
    'geolocation=(self)',
    'microphone=()',
    'camera=()',
    'payment=(self)',
    'usb=()',
    'magnetometer=()',
    'gyroscope=()',
    'accelerometer=()'
];
header('Permissions-Policy: ' . implode(', ', $permissions));

// Strict Transport Security (HTTPS only)
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
}

// Remove server information
header_remove('X-Powered-By');
header_remove('Server');
