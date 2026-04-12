<?php
// hp_items.php — returns active items for a given table (used by homepage editor)
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

$allowed = ['attractions', 'bikes', 'restaurants', 'buses', 'blogs'];
$table   = $_GET['table'] ?? '';

if (!in_array($table, $allowed, true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid table']);
    exit;
}

$db = getDB();

if ($table === 'buses') {
    $rows = $db->fetchAll("SELECT id, operator AS name, bus_type AS type, '' AS image FROM buses WHERE is_active=1 ORDER BY operator ASC");
} elseif ($table === 'blogs') {
    $rows = $db->fetchAll("SELECT id, title AS name, category AS type, image FROM blogs WHERE status='published' ORDER BY created_at DESC");
} elseif ($table === 'restaurants') {
    $rows = $db->fetchAll("SELECT id, name, cuisine AS type, image FROM restaurants WHERE is_active=1 ORDER BY name ASC");
} else {
    $rows = $db->fetchAll("SELECT id, name, type, image FROM {$table} WHERE is_active=1 ORDER BY display_order ASC, name ASC");
}

echo json_encode($rows ?: []);
