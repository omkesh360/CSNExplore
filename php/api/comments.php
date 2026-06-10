<?php
/**
 * php/api/comments.php
 * GET  ?ref_type=blog&ref_id=1          → list comments
 * POST {ref_type,ref_id,content}        → add comment (guest: also pass guest_name)
 * DELETE ?id=5                          → delete own comment or admin deletes any
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . (defined('CORS_ORIGIN') ? CORS_ORIGIN : 'https://csnexplore.com'));
header('Vary: Origin');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$method = $_SERVER['REQUEST_METHOD'];
$db     = getDB();

function getAuthUser(): ?array {
    $token = getAuthToken();
    if (!$token) return null;
    $payload = verifyJWT($token, JWT_SECRET);
    return $payload ?: null;
}

$validRefTypes = ['blog', 'listing', 'stays', 'cars', 'bikes', 'attractions', 'restaurants', 'buses'];

try {
    validateCsrf();

    if ($method === 'GET') {
        $refType = $_GET['ref_type'] ?? '';
        $refId   = (int)($_GET['ref_id'] ?? 0);

        if (!in_array($refType, $validRefTypes) || $refId < 1) sendError('Invalid parameters', 400);

        // Fetch comments — user_id may be NULL for guests
        $comments = $db->fetchAll(
            "SELECT c.id, c.content, c.created_at, c.user_id, c.guest_name,
                    u.name AS user_name
             FROM comments c
             LEFT JOIN users u ON u.id = c.user_id
             WHERE c.ref_type = ? AND c.ref_id = ? AND c.status = 'approved'
             ORDER BY c.created_at ASC",
            [$refType, $refId]
        );

        $out = array_map(function($c) {
            // Prefer registered user name; fall back to guest_name
            $displayName = $c['user_name'] ?? $c['guest_name'] ?? 'Guest';
            return [
                'id'         => (int)$c['id'],
                'content'    => htmlspecialchars($c['content'], ENT_QUOTES, 'UTF-8'),
                'user_name'  => htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8'),
                'user_id'    => $c['user_id'] ? (int)$c['user_id'] : null,
                'is_guest'   => $c['user_id'] === null,
                'created_at' => $c['created_at'],
            ];
        }, $comments);

        sendJson(['comments' => $out, 'count' => count($out)]);
    }

    elseif ($method === 'POST') {
        $user    = getAuthUser();           // null = guest
        $data    = getJsonInput();
        $refType = $data['ref_type'] ?? '';
        $refId   = (int)($data['ref_id'] ?? 0);
        $content = trim(strip_tags($data['content'] ?? ''));

        if (!in_array($refType, $validRefTypes)) sendError('Invalid ref_type', 400);
        if ($refId < 1) sendError('Invalid ref_id', 400);
        if (strlen($content) < 2)    sendError('Comment too short', 400);
        if (strlen($content) > 1000) sendError('Comment too long (max 1000 chars)', 400);

        if ($user) {
            // ── Logged-in user ────────────────────────────────────────────────
            // Rate limit: max 5 comments per user per hour per ref
            $recentCount = $db->fetchOne(
                "SELECT COUNT(*) AS cnt FROM comments 
                 WHERE user_id = ? AND ref_type = ? AND ref_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
                [(int)$user['id'], $refType, $refId]
            );
            if (($recentCount['cnt'] ?? 0) >= 5) sendError('Too many comments. Try again later.', 429);

            $newId = $db->insert('comments', [
                'user_id'    => (int)$user['id'],
                'guest_name' => null,
                'ref_type'   => $refType,
                'ref_id'     => $refId,
                'content'    => $content,
                'status'     => 'approved',
            ]);

            $comment = $db->fetchOne(
                "SELECT c.id, c.content, c.created_at, c.user_id, c.guest_name, u.name AS user_name
                 FROM comments c JOIN users u ON u.id = c.user_id
                 WHERE c.id = ?", [$newId]
            );
            $displayName = $comment['user_name'];

        } else {
            // ── Guest ─────────────────────────────────────────────────────────
            $guestName = trim(strip_tags($data['guest_name'] ?? ''));
            if (strlen($guestName) < 2)  sendError('Please enter your name (min 2 chars)', 400);
            if (strlen($guestName) > 60) sendError('Name too long (max 60 chars)', 400);

            // Rate limit by IP: max 3 per hour
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            $recentCount = $db->fetchOne(
                "SELECT COUNT(*) AS cnt FROM comments 
                 WHERE user_id IS NULL AND ref_type = ? AND ref_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
                [$refType, $refId]
            );
            if (($recentCount['cnt'] ?? 0) >= 10) sendError('Too many comments right now. Try again later.', 429);

            $newId = $db->insert('comments', [
                'user_id'    => null,
                'guest_name' => $guestName,
                'ref_type'   => $refType,
                'ref_id'     => $refId,
                'content'    => $content,
                'status'     => 'approved',
            ]);

            $comment = $db->fetchOne(
                "SELECT id, content, created_at, user_id, guest_name FROM comments WHERE id = ?", [$newId]
            );
            $displayName = $comment['guest_name'];
        }

        sendJson([
            'success' => true,
            'comment' => [
                'id'         => (int)$comment['id'],
                'content'    => htmlspecialchars($comment['content'], ENT_QUOTES, 'UTF-8'),
                'user_name'  => htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8'),
                'user_id'    => $comment['user_id'] ? (int)$comment['user_id'] : null,
                'is_guest'   => $comment['user_id'] === null,
                'created_at' => $comment['created_at'],
            ]
        ], 201);
    }

    elseif ($method === 'DELETE') {
        $user = getAuthUser();
        if (!$user) sendError('Login required to delete comments', 401);

        $commentId = (int)($_GET['id'] ?? 0);
        if ($commentId < 1) sendError('Invalid comment id', 400);

        $comment = $db->fetchOne("SELECT * FROM comments WHERE id = ?", [$commentId]);
        if (!$comment) sendError('Comment not found', 404);

        $isAdmin = ($user['role'] ?? '') === 'admin';
        if (!$isAdmin && (int)$comment['user_id'] !== (int)$user['id']) {
            sendError('Not allowed', 403);
        }

        $db->delete('comments', 'id = ?', [$commentId]);
        sendJson(['success' => true]);
    }

    else {
        sendError('Method not allowed', 405);
    }

} catch (Exception $e) {
    error_log('Comments API error: ' . $e->getMessage());
    sendError('Server error: ' . $e->getMessage(), 500);
}
