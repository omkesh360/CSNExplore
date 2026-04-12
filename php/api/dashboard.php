<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

requireAdmin();
$db = getDB();

$stats = [
    'bookings' => [
        'total'   => $db->fetchOne("SELECT COUNT(*) as c FROM bookings")['c'] ?? 0,
        'pending' => $db->fetchOne("SELECT COUNT(*) as c FROM bookings WHERE status='pending'")['c'] ?? 0,
    ],
    'users'    => $db->fetchOne("SELECT COUNT(*) as c FROM users")['c'] ?? 0,
    'listings' => [
        'stays'       => $db->fetchOne("SELECT COUNT(*) as c FROM stays WHERE is_active=1")['c'] ?? 0,
        'cars'        => $db->fetchOne("SELECT COUNT(*) as c FROM cars WHERE is_active=1")['c'] ?? 0,
        'bikes'       => $db->fetchOne("SELECT COUNT(*) as c FROM bikes WHERE is_active=1")['c'] ?? 0,
        'restaurants' => $db->fetchOne("SELECT COUNT(*) as c FROM restaurants WHERE is_active=1")['c'] ?? 0,
        'attractions' => $db->fetchOne("SELECT COUNT(*) as c FROM attractions WHERE is_active=1")['c'] ?? 0,
        'buses'       => $db->fetchOne("SELECT COUNT(*) as c FROM buses WHERE is_active=1")['c'] ?? 0,
    ],
    'blogs'    => $db->fetchOne("SELECT COUNT(*) as c FROM blogs WHERE status='published'")['c'] ?? 0,
    'recent_bookings' => $db->fetchAll("SELECT * FROM bookings ORDER BY created_at DESC LIMIT 10"),
];

sendJson($stats);
