<?php
require_once __DIR__ . '/../config.php';

// Get token from query parameter
$token = $_GET['token'] ?? '';

if (!$token) {
    sendError('Unauthorized', 401);
}

// Decode JWT token
try {
    $parts = explode('.', $token);
    if (count($parts) !== 3) throw new Exception('Invalid token format');
    
    // Decode payload with proper base64 handling
    $payload_b64 = $parts[1];
    // Add padding if needed
    $padding = 4 - (strlen($payload_b64) % 4);
    if ($padding !== 4) {
        $payload_b64 .= str_repeat('=', $padding);
    }
    
    $payload = json_decode(base64_decode($payload_b64), true);
    if (!$payload || !isset($payload['id'])) throw new Exception('Invalid token payload');
    
    $user_id = $payload['id'];
} catch (Exception $e) {
    sendError('Invalid token: ' . $e->getMessage(), 401);
}

// Get user's bookings
$db = getDB();
$bookings = $db->fetchAll(
    "SELECT id, full_name, phone, email, booking_date, checkin_date, checkout_date, number_of_people, service_type, listing_id, listing_name, status, notes, created_at 
     FROM bookings 
     WHERE email = (SELECT email FROM users WHERE id = ?) 
     ORDER BY created_at DESC",
    [$user_id]
);

// Fetch listing images in bulk to fix N+1 query issue
$tableIds = [];
foreach ($bookings as $b) {
    $table = $b['service_type'];
    $lid   = (int)$b['listing_id'];
    if ($table && $lid && in_array($table, ['stays','cars','bikes','restaurants','attractions','buses'])) {
        $tableIds[$table][] = $lid;
    }
}

$images = [];
foreach ($tableIds as $table => $ids) {
    $uniqueIds = array_unique($ids);
    if (empty($uniqueIds)) continue;
    $placeholders = implode(',', array_fill(0, count($uniqueIds), '?'));
    $rows = $db->fetchAll("SELECT id, image FROM $table WHERE id IN ($placeholders)", array_values($uniqueIds));
    foreach ($rows as $row) {
        $images[$table][$row['id']] = $row['image'];
    }
}

foreach ($bookings as &$b) {
    $table = $b['service_type'];
    $lid   = (int)$b['listing_id'];
    $b['listing_image'] = $images[$table][$lid] ?? null;
}
unset($b);

sendJson($bookings);
