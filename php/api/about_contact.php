<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$method  = $_SERVER['REQUEST_METHOD'];
$section = sanitize($_GET['section'] ?? 'about');

try {
    $db = getDB();

    if ($method === 'GET') {
        $row = $db->fetchOne("SELECT content FROM about_contact WHERE section = ?", [$section]);
        sendJson($row ? json_decode($row['content'], true) : []);
    }

    elseif ($method === 'PUT') {
        requireAdmin();
        $input   = getJsonInput();
        $section = sanitize($input['section'] ?? 'about');
        $data    = $input['data'] ?? [];

        $existing = $db->fetchOne("SELECT id FROM about_contact WHERE section = ?", [$section]);
        if ($existing) {
            $db->update('about_contact', ['content' => json_encode($data), 'updated_at' => date('Y-m-d H:i:s')], 'section = :s', [':s' => $section]);
        } else {
            $db->insert('about_contact', ['section' => $section, 'content' => json_encode($data)]);
        }
        sendJson(['success' => true]);
    }

    else {
        sendError('Method not allowed', 405);
    }

} catch (Exception $e) {
    error_log('About/Contact error: ' . $e->getMessage());
    sendError('Server error', 500);
}
