<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

try {
    $db = getDB();

    if ($method === 'GET') {
        // Check if request is from admin (has valid JWT)
        $isAdmin = false;
        try {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
            if (preg_match('/Bearer\s+(.+)/i', $authHeader, $m)) {
                require_once __DIR__ . '/../jwt.php';
                $payload = verifyJWT($m[1], JWT_SECRET);
                $isAdmin = ($payload['role'] ?? '') === 'admin';
            }
        } catch (Exception $e) {}

        if ($id) {
            $sql = $isAdmin ? "SELECT * FROM blogs WHERE id = ?" : "SELECT * FROM blogs WHERE id = ? AND status = 'published'";
            $blog = $db->fetchOne($sql, [$id]);
            if (!$blog) sendError('Not found', 404);
            $blog['tags'] = json_decode($blog['tags'] ?? '[]', true) ?: [];
            sendJson($blog);
        } else {
            if ($isAdmin) {
                // Admin: filter by status param if provided
                $status = $_GET['status'] ?? '';
                $sql    = "SELECT * FROM blogs";
                $params = [];
                if ($status) { $sql .= " WHERE status = ?"; $params[] = $status; }
                $sql .= " ORDER BY created_at DESC";
            } else {
                // Public: only published
                $sql    = "SELECT * FROM blogs WHERE status = 'published' ORDER BY created_at DESC";
                $params = [];
            }
            $blogs = $db->fetchAll($sql, $params);
            foreach ($blogs as &$b) $b['tags'] = json_decode($b['tags'] ?? '[]', true) ?: [];
            sendJson($blogs);
        }
    }

    elseif ($method === 'POST') {
        requireAdmin();
        $data = getJsonInput();
        if (empty($data['title']) || empty($data['content'])) sendError('Title and content required', 400);

        $newId = $db->insert('blogs', [
            'title'            => sanitize($data['title']),
            'content'          => $data['content'], // allow HTML
            'author'           => sanitize($data['author'] ?? 'Admin'),
            'image'            => sanitize($data['image'] ?? ''),
            'status'           => in_array($data['status'] ?? '', ['published','draft']) ? $data['status'] : 'published',
            'category'         => sanitize($data['category'] ?? 'General'),
            'read_time'        => sanitize($data['read_time'] ?? ''),
            'tags'             => json_encode(is_array($data['tags'] ?? null) ? $data['tags'] : []),
            'meta_description' => sanitize($data['meta_description'] ?? ''),
        ]);

        $blog = $db->fetchOne("SELECT * FROM blogs WHERE id = ?", [$newId]);
        $blog['tags'] = json_decode($blog['tags'] ?? '[]', true) ?: [];
        // Auto-regenerate static HTML [A2.1]
        try { regenerateBlogHtml($newId); } catch(Exception $e) { error_log('HTML regen failed: '.$e->getMessage()); }
        sendJson($blog, 201);
    }

    elseif ($method === 'PUT' && $id) {
        requireAdmin();
        $data = getJsonInput();
        $existing = $db->fetchOne("SELECT * FROM blogs WHERE id = ?", [$id]);
        if (!$existing) sendError('Not found', 404);

        $db->update('blogs', [
            'title'            => sanitize($data['title'] ?? $existing['title']),
            'content'          => $data['content'] ?? $existing['content'],
            'author'           => sanitize($data['author'] ?? $existing['author']),
            'image'            => sanitize($data['image'] ?? $existing['image']),
            'status'           => in_array($data['status'] ?? '', ['published','draft']) ? $data['status'] : $existing['status'],
            'category'         => sanitize($data['category'] ?? $existing['category']),
            'read_time'        => sanitize($data['read_time'] ?? $existing['read_time']),
            'tags'             => json_encode(is_array($data['tags'] ?? null) ? $data['tags'] : (json_decode($existing['tags'] ?? '[]', true) ?: [])),
            'meta_description' => sanitize($data['meta_description'] ?? $existing['meta_description']),
            'updated_at'       => date('Y-m-d H:i:s'),
        ], 'id = :id', [':id' => $id]);

        $blog = $db->fetchOne("SELECT * FROM blogs WHERE id = ?", [$id]);
        $blog['tags'] = json_decode($blog['tags'] ?? '[]', true) ?: [];
        // Auto-regenerate static HTML [A2.1]
        try { regenerateBlogHtml($id); } catch(Exception $e) { error_log('HTML regen failed: '.$e->getMessage()); }
        sendJson($blog);
    }

    elseif ($method === 'DELETE' && $id) {
        requireAdmin();
        // Delete the static HTML file too [A2.1]
        $blog = $db->fetchOne("SELECT title FROM blogs WHERE id = ?", [$id]);
        $db->delete('blogs', 'id = ?', [$id]);
        if ($blog) {
            $slug = generateSlug('blogs', $id, $blog['title']);
            $file = dirname(__DIR__, 2) . '/blogs/' . $slug . '.html';
            if (file_exists($file)) @unlink($file);
        }
        sendJson(['success' => true]);
    }

    else {
        sendError('Not found', 404);
    }

} catch (Exception $e) {
    error_log('Blogs error: ' . $e->getMessage());
    sendError('Server error', 500);
}

/**
 * Regenerates a single blog's static HTML file [A2.1]
 */
function regenerateBlogHtml(int $blogId): void {
    $genScript = dirname(__DIR__) . '/api/generate_html.php';
    if (!file_exists($genScript)) return;
    // Run in background so the API response isn't blocked
    if (PHP_OS_FAMILY === 'Windows') {
        pclose(popen("start /B php \"$genScript\" blog $blogId", 'r'));
    } else {
        exec("php \"$genScript\" blog $blogId > /dev/null 2>&1 &");
    }
}
