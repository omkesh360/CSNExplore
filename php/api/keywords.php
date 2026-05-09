<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = getDB();

    if ($method === 'GET') {
        $keywords = $db->fetchAll("SELECT * FROM site_keywords ORDER BY keyword ASC");
        sendJson($keywords);
    }

    elseif ($method === 'POST') {
        requireAdmin();
        $input = getJsonInput();
        $keyword = sanitize($input['keyword'] ?? '');
        
        if (empty($keyword)) sendError('Keyword cannot be empty');

        try {
            $id = $db->insert('site_keywords', ['keyword' => $keyword]);
            sendJson(['success' => true, 'id' => $id, 'keyword' => $keyword], 201);
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                // If it already exists, just return success with the existing one
                $existing = $db->fetchOne("SELECT * FROM site_keywords WHERE keyword = ?", [$keyword]);
                sendJson(['success' => true, 'id' => $existing['id'], 'keyword' => $keyword]);
            }
            throw $e;
        }
    }

    elseif ($method === 'DELETE') {
        requireAdmin();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        if (!$id) sendError('ID required');
        $db->delete('site_keywords', 'id = ?', [$id]);
        sendJson(['success' => true]);
    }

    else {
        sendError('Method not allowed', 405);
    }

} catch (Exception $e) {
    sendError('Server error: ' . $e->getMessage(), 500);
}
