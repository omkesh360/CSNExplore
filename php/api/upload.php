<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';
require_once __DIR__ . '/../rate-limiter.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    uploadImage();
} elseif ($method === 'GET') {
    listImages();
} elseif ($method === 'DELETE') {
    deleteImage();
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
    
    // Check if we are replacing an existing image
    if (isset($_POST['replace_target']) && !empty($_POST['replace_target'])) {
        $target = $_POST['replace_target'];
        
        // Basic security check: ensure it's actually an image path inside /images/
        if (strpos($target, '/images/') !== 0 || strpos($target, '..') !== false) {
             sendError('Invalid replace target path', 400);
        }
        
        $destination = __DIR__ . '/../../public' . $target;
        $urlPath = $target;
    } else {
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = time() . '-' . rand(100000000, 999999999) . '.' . $extension;
        $destination = UPLOADS_DIR . '/' . $filename;
        $urlPath = '/images/uploads/' . $filename;
    }
    
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        sendError('Failed to upload image', 500);
    }
    
    sendJson(['success' => true, 'url' => $urlPath]);
}

function deleteImage() {
    requireAdmin();
    
    if (!isset($_GET['path'])) {
        sendError('No path provided', 400);
    }
    
    $path = $_GET['path'];
    
    // Validate path (prevent traversal outside of /images/)
    if (strpos($path, '/images/') !== 0 || strpos($path, '..') !== false) {
        sendError('Invalid image path', 400);
    }
    
    $fullPath = __DIR__ . '/../../public' . $path;
    
    if (file_exists($fullPath) && is_file($fullPath)) {
        if (unlink($fullPath)) {
            sendJson(['success' => true, 'message' => 'Image deleted']);
        } else {
            sendError('Failed to delete image', 500);
        }
    } else {
        sendError('Image not found', 404);
    }
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
