<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . (defined('CORS_ORIGIN') ? CORS_ORIGIN : 'https://csnexplore.com'));
header('Vary: Origin');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Method not allowed', 405);

$uploadDir = __DIR__ . '/../../images/uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

if (empty($_FILES['file']) && empty($_FILES['image'])) sendError('No file uploaded', 400);

$file = !empty($_FILES['file']) ? $_FILES['file'] : $_FILES['image'];
if ($file['error'] !== UPLOAD_ERR_OK) sendError('Upload error: ' . $file['error'], 400);

// Validate type
$allowed = ['image/jpeg','image/png','image/webp','image/gif'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime  = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);
if (!in_array($mime, $allowed)) sendError('Only JPG, PNG, WebP, GIF allowed', 400);

// Validate size (5MB)
if ($file['size'] > 5 * 1024 * 1024) sendError('File too large (max 5MB)', 400);

// Get original filename and extension
$originalName = pathinfo($file['name'], PATHINFO_FILENAME);
// Clean the slug
$slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $originalName));
$slug = trim(preg_replace('/-+/', '-', $slug), '-');
if (empty($slug)) $slug = uniqid('img');

$filename = $slug . '-' . substr(uniqid(), -4) . '.webp';
$dest = $uploadDir . $filename;

// Load image to resize and convert
$imgResource = null;
if ($mime === 'image/jpeg') {
    $imgResource = @imagecreatefromjpeg($file['tmp_name']);
} elseif ($mime === 'image/png') {
    $imgResource = @imagecreatefrompng($file['tmp_name']);
} elseif ($mime === 'image/webp') {
    $imgResource = @imagecreatefromwebp($file['tmp_name']);
} elseif ($mime === 'image/gif') {
    $imgResource = @imagecreatefromgif($file['tmp_name']);
}

if ($imgResource) {
    // Resize if too large
    $width = imagesx($imgResource);
    $height = imagesy($imgResource);
    $maxWidth = 1920;
    
    if ($width > $maxWidth) {
        $newWidth = $maxWidth;
        $newHeight = floor($height * ($maxWidth / $width));
        
        $resized = imagecreatetruecolor($newWidth, $newHeight);
        // Maintain transparency
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
        imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
        
        imagecopyresampled($resized, $imgResource, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($imgResource);
        $imgResource = $resized;
    }
    
    // Save as WebP
    if (!imagewebp($imgResource, $dest, 85)) {
        // Fallback if imagewebp fails
        move_uploaded_file($file['tmp_name'], $dest);
    }
    imagedestroy($imgResource);
} else {
    // Fallback if GD is missing or image is broken
    $filename = $slug . '-' . substr(uniqid(), -4) . '.' . ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'][$mime];
    $dest = $uploadDir . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) sendError('Failed to save file', 500);
}

// Build URL using configured base or detected host (never trust HTTP_HOST directly)
$baseUrl = env('APP_URL', '');
if (!$baseUrl) {
    $scheme  = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    // Validate HTTP_HOST: only allow safe hostname characters
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    if (!preg_match('/^[a-zA-Z0-9.\-:]+$/', $host)) $host = 'localhost';
    $baseUrl = $scheme . '://' . $host;
}
$url = rtrim($baseUrl, '/') . '/images/uploads/' . $filename;

sendJson(['url' => $url, 'filename' => $filename]);
