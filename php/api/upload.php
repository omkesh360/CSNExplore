<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';
require_once __DIR__ . '/../rate-limiter.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    uploadImage();
} elseif ($method === 'GET') {
    listImages();
} else {
    sendError('Method not allowed', 405);
}

function uploadImage() {
    requireAdmin();
    
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        sendError('No image file provided');
    }
    
    $file = $_FILES['image'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
    
    if (!in_array($file['type'], $allowedTypes)) {
        sendError('Invalid file type. Only JPEG, PNG, GIF, WebP, and SVG are allowed.');
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = time() . '-' . rand(100000000, 999999999) . '.' . $extension;
    $destination = UPLOADS_DIR . '/' . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        sendError('Failed to upload image', 500);
    }
    
    $urlPath = '/images/uploads/' . $filename;
    sendJson(['success' => true, 'url' => $urlPath]);
}

function listImages() {
    requireAdmin();
    
    $imagesDir = __DIR__ . '/../../public/images';
    $uploadsDir = UPLOADS_DIR;
    $validExts = ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.svg', '.avif'];
    $result = [];
    
    // Root images folder
    if (is_dir($imagesDir)) {
        $files = scandir($imagesDir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array('.' . $ext, $validExts)) {
                $result[] = ['name' => $file, 'url' => '/images/' . $file];
            }
        }
    }
    
    // Uploads subfolder
    if (is_dir($uploadsDir)) {
        $files = scandir($uploadsDir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array('.' . $ext, $validExts)) {
                $result[] = ['name' => $file, 'url' => '/images/uploads/' . $file];
            }
        }
    }
    
    sendJson($result);
}
