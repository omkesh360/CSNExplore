<?php
/**
 * Keywords API — SEO keyword management
 * GET    → list all keywords
 * POST   → add keyword
 * DELETE → remove keyword  (?id=N)
 * PUT    → increment usage  (?id=N)
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . (defined('CORS_ORIGIN') ? CORS_ORIGIN : 'https://csnexplore.com'));
header('Vary: Origin');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

try {
    requireAdmin();
} catch (Exception $e) {
    sendError('Unauthorized', 401);
}

$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

try {
    $db = getDB();

    // ── GET: list all keywords ────────────────────────────────────────────────
    if ($method === 'GET') {
        $keywords = $db->fetchAll(
            "SELECT id, keyword, usage_count, created_at
             FROM seo_keywords
             ORDER BY usage_count DESC, keyword ASC"
        );
        sendJson($keywords);
    }

    // ── POST: add keyword ─────────────────────────────────────────────────────
    if ($method === 'POST') {
        $data    = getJsonInput();
        $keyword = trim($data['keyword'] ?? '');

        if ($keyword === '') sendError('Keyword is required', 400);
        if (mb_strlen($keyword) > 200) sendError('Keyword too long (max 200 chars)', 400);

        // Return existing if duplicate
        $existing = $db->fetchOne("SELECT id, keyword FROM seo_keywords WHERE keyword = ?", [$keyword]);
        if ($existing) {
            sendJson(['success' => true, 'id' => $existing['id'], 'keyword' => $existing['keyword']]);
        }

        $newId = $db->insert('seo_keywords', ['keyword' => $keyword, 'usage_count' => 0]);
        sendJson(['success' => true, 'id' => $newId, 'keyword' => $keyword], 201);
    }

    // ── DELETE: remove keyword ────────────────────────────────────────────────
    if ($method === 'DELETE') {
        if (!$id) sendError('Keyword ID required', 400);
        $db->delete('seo_keywords', 'id = ?', [$id]);
        sendJson(['success' => true]);
    }

    // ── PUT: increment usage count ────────────────────────────────────────────
    if ($method === 'PUT') {
        if (!$id) sendError('Keyword ID required', 400);
        $data = getJsonInput();
        if (!empty($data['increment_usage'])) {
            $db->query("UPDATE seo_keywords SET usage_count = usage_count + 1 WHERE id = ?", [$id]);
        }
        sendJson(['success' => true]);
    }

    sendError('Method not allowed', 405);

} catch (Exception $e) {
    error_log('Keywords API error: ' . $e->getMessage());
    sendError('Server error', 500);
}
