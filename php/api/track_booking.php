<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . (defined('CORS_ORIGIN') ? CORS_ORIGIN : 'https://csnexplore.com'));
header('Vary: Origin');

$phone = trim($_GET['phone'] ?? '');
if (!$phone) { sendError('Phone number required', 400); }

// Rate limiting to prevent booking data enumeration
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
if (!rateLimit('track_booking_' . $ip, 5, 3600)) {
    sendError('Too many tracking attempts. You can only check 5 times per hour.', 429);
}

// Normalise: strip spaces/dashes for comparison
$phone_clean = preg_replace('/[\s\-\(\)]+/', '', $phone);

try {
    $db = getDB();
    // Match by phone — try exact first, then stripped
    $rows = $db->fetchAll(
        "SELECT id, full_name, phone, email, booking_date, number_of_people, service_type, listing_id, listing_name, status, notes, created_at
         FROM bookings
         WHERE REPLACE(REPLACE(phone,' ',''),'-','') = ?
         ORDER BY created_at DESC
         LIMIT 20",
        [$phone_clean]
    );
    sendJson($rows);
} catch (Exception $e) {
    error_log('Track booking error: ' . $e->getMessage());
    sendError('Server error', 500);
}
