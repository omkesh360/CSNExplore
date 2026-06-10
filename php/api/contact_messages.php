<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . (defined('CORS_ORIGIN') ? CORS_ORIGIN : 'https://csnexplore.com'));
header('Vary: Origin');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

try {
    validateCsrf();
    requireAdmin();

    $db = getDB();
    $messages = $db->fetchAll("SELECT * FROM contact_messages ORDER BY created_at DESC");
    sendJson($messages);
} catch (Exception $e) {
    error_log('Contact Messages API error: ' . $e->getMessage());
    sendError('Server error', 500);
}
