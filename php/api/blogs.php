<?php
/**
 * Blogs API - CRUD for Blog Posts
 * GET    /api/blogs         -> list all blogs
 * GET    /api/blogs?id=X    -> get single blog
 * POST   /api/blogs         -> create blog (admin only)
 * PUT    /api/blogs         -> update blog (admin only)
 * DELETE /api/blogs?id=X    -> delete blog (admin only)
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$db = Database::getInstance();
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            getBlog($db);
            break;
        case 'POST':
            requireAdmin();
            createBlog($db);
            break;
        case 'PUT':
            requireAdmin();
            updateBlog($db);
            break;
        case 'DELETE':
            requireAdmin();
            deleteBlog($db);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

// ---- Handlers ----

function getBlog($db) {
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $blog = $db->fetchOne('SELECT * FROM blogs WHERE id = ?', [$id]);
        if (!$blog) {
            http_response_code(404);
            echo json_encode(['error' => 'Blog not found']);
            return;
        }
        $blog['tags'] = json_decode($blog['tags'] ?? '[]', true) ?: [];
        echo json_encode($blog);
    } else {
        // List all, newest first
        $blogs = $db->fetchAll('SELECT * FROM blogs ORDER BY created_at DESC');
        $blogs = array_map(function($b) {
            $b['tags'] = json_decode($b['tags'] ?? '[]', true) ?: [];
            return $b;
        }, $blogs);
        echo json_encode($blogs);
    }
}

function createBlog($db) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['title']) || empty($data['content'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Title and content are required']);
        return;
    }

    $id = $db->insert('blogs', [
        'title'            => trim($data['title']),
        'content'          => trim($data['content']),
        'author'           => trim($data['author'] ?? 'Admin'),
        'image'            => trim($data['image'] ?? ''),
        'status'           => in_array($data['status'] ?? 'published', ['published', 'draft']) ? $data['status'] : 'published',
        'category'         => trim($data['category'] ?? 'General'),
        'read_time'        => trim($data['read_time'] ?? ''),
        'tags'             => json_encode(is_array($data['tags'] ?? null) ? $data['tags'] : []),
        'meta_description' => trim($data['meta_description'] ?? ''),
    ]);

    $blog = $db->fetchOne('SELECT * FROM blogs WHERE id = ?', [$id]);
    http_response_code(201);
    echo json_encode($blog);
}

function updateBlog($db) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Blog ID is required']);
        return;
    }

    $id = (int)$data['id'];
    $existing = $db->fetchOne('SELECT * FROM blogs WHERE id = ?', [$id]);
    if (!$existing) {
        http_response_code(404);
        echo json_encode(['error' => 'Blog not found']);
        return;
    }

    $updated = [
        'title'            => trim($data['title'] ?? $existing['title']),
        'content'          => trim($data['content'] ?? $existing['content']),
        'author'           => trim($data['author'] ?? $existing['author']),
        'image'            => trim($data['image'] ?? $existing['image']),
        'status'           => in_array($data['status'] ?? '', ['published', 'draft']) ? $data['status'] : $existing['status'],
        'category'         => trim($data['category'] ?? $existing['category'] ?? 'General'),
        'read_time'        => trim($data['read_time'] ?? $existing['read_time'] ?? ''),
        'tags'             => json_encode(is_array($data['tags'] ?? null) ? $data['tags'] : (json_decode($existing['tags'] ?? '[]', true) ?: [])),
        'meta_description' => trim($data['meta_description'] ?? $existing['meta_description'] ?? ''),
        'updated_at'       => date('Y-m-d H:i:s'),
    ];

    $db->update('blogs', $updated, 'id = :id', [':id' => $id]);
    $blog = $db->fetchOne('SELECT * FROM blogs WHERE id = ?', [$id]);
    echo json_encode($blog);
}

function deleteBlog($db) {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$id) {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = isset($data['id']) ? (int)$data['id'] : 0;
    }

    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Blog ID is required']);
        return;
    }

    $rows = $db->delete('blogs', 'id = ?', [$id]);
    if ($rows === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Blog not found']);
        return;
    }

    echo json_encode(['success' => true, 'message' => 'Blog deleted']);
}
