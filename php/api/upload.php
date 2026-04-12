<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Method not allowed', 405);

$uploadDir = __DIR__ . '/../../images/uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

if (empty($_FILES['file'])) sendError('No file uploaded', 400);

$file = $_FILES['file'];
if ($file['error'] !== UPLOAD_ERR_OK) sendError('Upload error: ' . $file['error'], 400);

// Validate type
$allowed = ['image/jpeg','image/png','image/webp','image/gif'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime  = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);
if (!in_array($mime, $allowed)) sendError('Only JPG, PNG, WebP, GIF allowed', 400);

// Validate size (5MB)
if ($file['size'] > 5 * 1024 * 1024) sendError('File too large (max 5MB)', 400);

$ext      = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'][$mime];
$filename = uniqid('img_', true) . '.' . $ext;
$dest     = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $dest)) sendError('Failed to save file', 500);

$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
     . '://' . $_SERVER['HTTP_HOST']
     . '/images/uploads/' . $filename;

sendJson(['url' => $url, 'filename' => $filename]);
