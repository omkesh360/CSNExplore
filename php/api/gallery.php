<?php
// Gallery Management API
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . (defined('CORS_ORIGIN') ? CORS_ORIGIN : 'https://csnexplore.com'));
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Vary: Origin');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

validateCsrf();

// Verify admin authentication
$admin = requireAdmin();

$method = $_SERVER['REQUEST_METHOD'];
$uploadDir = __DIR__ . '/../../images/uploads/';
$uploadUrl = BASE_PATH . '/images/uploads/';

// GET - List all images
if ($method === 'GET') {
    $images = [];
    
    if (is_dir($uploadDir)) {
        $files = scandir($uploadDir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || $file === '.htaccess' || $file === '.gitkeep') continue;
            
            $filePath = $uploadDir . $file;
            if (is_file($filePath)) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                    $images[] = [
                        'filename' => $file,
                        'url' => $uploadUrl . $file,
                        'size' => filesize($filePath),
                        'modified' => filemtime($filePath),
                        'type' => $ext
                    ];
                }
            }
        }
    }
    
    // Sort by modified date (newest first)
    usort($images, function($a, $b) {
        return $b['modified'] - $a['modified'];
    });
    
    sendJson($images);
}

// POST - Upload image
if ($method === 'POST') {
    if (!isset($_FILES['image'])) {
        sendError('No image uploaded', 400);
    }
    
    $file = $_FILES['image'];
    
    // Validate file using finfo (not user-controlled $_FILES['type'])
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mimeType, $allowedTypes)) {
        sendError('Invalid file type. Only JPG, PNG, GIF, and WebP allowed.', 400);
    }
    
    // Check file size (max 10MB)
    if ($file['size'] > 10 * 1024 * 1024) {
        sendError('File too large. Maximum size is 10MB.', 400);
    }
    
    // Generate unique filename
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = time() . '-' . mt_rand(100000000, 999999999) . '.' . $ext;
    $destination = $uploadDir . $filename;
    
    // Create upload directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        sendError('Failed to upload image', 500);
    }
    
    sendJson([
        'success' => true,
        'filename' => $filename,
        'url' => $uploadUrl . $filename,
        'message' => 'Image uploaded successfully'
    ]);
}

// DELETE - Delete image
if ($method === 'DELETE') {
    $input = getJsonInput();
    $filename = $input['filename'] ?? '';
    
    if (!$filename) {
        sendError('Filename required', 400);
    }
    
    // Sanitize filename
    $filename = basename($filename);
    $filePath = $uploadDir . $filename;
    
    if (!file_exists($filePath)) {
        sendError('File not found', 404);
    }
    
    if (!unlink($filePath)) {
        sendError('Failed to delete image', 500);
    }
    
    sendJson([
        'success' => true,
        'message' => 'Image deleted successfully'
    ]);
}

sendError('Invalid request method', 405);
