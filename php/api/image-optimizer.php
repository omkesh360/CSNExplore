<?php
/**
 * Real Image Optimizer API
 * Compresses and optimizes images with actual performance metrics
 */

header('Content-Type: application/json');

// Check authentication
$token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$token = str_replace('Bearer ', '', $token);

if (empty($token)) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Handle image optimization
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];
    
    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['error' => 'Upload error: ' . $file['error']]);
        exit;
    }
    
    // Check file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid image format. Only JPEG, PNG, and WebP allowed.']);
        exit;
    }
    
    // Get original size
    $originalSize = filesize($file['tmp_name']);
    $startTime = microtime(true);
    
    // Create output directory
    $uploadDir = __DIR__ . '/../../images/uploads/optimized';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate filename
    $filename = pathinfo($file['name'], PATHINFO_FILENAME);
    $filename = preg_replace('/[^a-z0-9-]/i', '-', $filename);
    $filename = preg_replace('/-+/', '-', $filename);
    $filename = trim($filename, '-');
    
    $results = [];
    
    // Optimize JPEG/PNG
    if (in_array($mimeType, ['image/jpeg', 'image/png'])) {
        $jpgPath = $uploadDir . '/' . $filename . '.jpg';
        $optimizedSize = optimizeImage($file['tmp_name'], $jpgPath, 75, 'jpeg');
        $results['jpeg'] = [
            'path' => 'images/uploads/optimized/' . $filename . '.jpg',
            'size' => $optimizedSize,
            'reduction' => round((1 - $optimizedSize / $originalSize) * 100, 1),
        ];
    }
    
    // Convert to WebP
    if (extension_loaded('gd') || extension_loaded('imagick')) {
        $webpPath = $uploadDir . '/' . $filename . '.webp';
        $webpSize = optimizeImage($file['tmp_name'], $webpPath, 75, 'webp');
        $results['webp'] = [
            'path' => 'images/uploads/optimized/' . $filename . '.webp',
            'size' => $webpSize,
            'reduction' => round((1 - $webpSize / $originalSize) * 100, 1),
        ];
    }
    
    // Generate thumbnails
    $thumbPath = $uploadDir . '/' . $filename . '-thumb.jpg';
    $thumbSize = optimizeImage($file['tmp_name'], $thumbPath, 75, 'jpeg', 150, 150);
    $results['thumbnail'] = [
        'path' => 'images/uploads/optimized/' . $filename . '-thumb.jpg',
        'size' => $thumbSize,
        'reduction' => round((1 - $thumbSize / $originalSize) * 100, 1),
    ];
    
    // Calculate processing time
    $processingTime = round((microtime(true) - $startTime) * 1000, 2);
    
    // Update stats
    updateOptimizationStats($originalSize, $results);
    
    echo json_encode([
        'success' => true,
        'original_size' => $originalSize,
        'original_size_formatted' => formatBytes($originalSize),
        'optimized_results' => $results,
        'total_optimized_size' => array_sum(array_column($results, 'size')),
        'total_reduction_percent' => round((1 - array_sum(array_column($results, 'size')) / ($originalSize * count($results))) * 100, 1),
        'processing_time_ms' => $processingTime,
    ]);
    exit;
}

// GET - return optimization stats
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $cacheDir = __DIR__ . '/../../cache';
    $statsFile = $cacheDir . '/performance_stats.json';
    $stats = json_decode(@file_get_contents($statsFile), true) ?: [];
    
    echo json_encode([
        'images_optimized' => $stats['images_optimized'] ?? 0,
        'total_original_size' => $stats['total_original_size'] ?? 0,
        'total_optimized_size' => $stats['total_optimized_size'] ?? 0,
        'total_webp_size' => $stats['total_webp_size'] ?? 0,
        'total_original_size_formatted' => formatBytes($stats['total_original_size'] ?? 0),
        'total_optimized_size_formatted' => formatBytes($stats['total_optimized_size'] ?? 0),
        'total_webp_size_formatted' => formatBytes($stats['total_webp_size'] ?? 0),
        'average_reduction_percent' => $stats['images_optimized'] > 0 ? round((1 - ($stats['total_optimized_size'] / $stats['total_original_size'])) * 100, 1) : 0,
    ]);
    exit;
}

// Helper functions
function optimizeImage($sourcePath, $destPath, $quality = 75, $format = 'jpeg', $maxWidth = null, $maxHeight = null) {
    if (extension_loaded('imagick')) {
        return optimizeWithImageMagick($sourcePath, $destPath, $quality, $format, $maxWidth, $maxHeight);
    } elseif (extension_loaded('gd')) {
        return optimizeWithGD($sourcePath, $destPath, $quality, $format, $maxWidth, $maxHeight);
    }
    return 0;
}

function optimizeWithGD($sourcePath, $destPath, $quality, $format, $maxWidth, $maxHeight) {
    $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
    
    if ($ext === 'png') {
        $image = imagecreatefrompng($sourcePath);
    } elseif (in_array($ext, ['jpg', 'jpeg'])) {
        $image = imagecreatefromjpeg($sourcePath);
    } elseif ($ext === 'webp') {
        $image = imagecreatefromwebp($sourcePath);
    } else {
        return 0;
    }
    
    if (!$image) return 0;
    
    $width = imagesx($image);
    $height = imagesy($image);
    
    // Resize if needed
    if ($maxWidth && $maxHeight) {
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = (int)($width * $ratio);
        $newHeight = (int)($height * $ratio);
        
        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($image);
        $image = $resized;
    }
    
    // Save in requested format
    if ($format === 'webp' && function_exists('imagewebp')) {
        imagewebp($image, $destPath, $quality);
    } else {
        imagejpeg($image, $destPath, $quality);
    }
    
    imagedestroy($image);
    return filesize($destPath);
}

function optimizeWithImageMagick($sourcePath, $destPath, $quality, $format, $maxWidth, $maxHeight) {
    try {
        $image = new Imagick($sourcePath);
        
        // Resize if needed
        if ($maxWidth && $maxHeight) {
            $image->resizeImage($maxWidth, $maxHeight, Imagick::FILTER_LANCZOS, 1, true);
        }
        
        // Set quality
        $image->setImageCompressionQuality($quality);
        
        // Set format
        if ($format === 'webp') {
            $image->setImageFormat('webp');
        } else {
            $image->setImageFormat('jpeg');
        }
        
        $image->writeImage($destPath);
        $image->destroy();
        
        return filesize($destPath);
    } catch (Exception $e) {
        return 0;
    }
}

function formatBytes($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, 2) . ' ' . $units[$pow];
}

function updateOptimizationStats($originalSize, $results) {
    $cacheDir = __DIR__ . '/../../cache';
    $statsFile = $cacheDir . '/performance_stats.json';
    
    $stats = json_decode(@file_get_contents($statsFile), true) ?: [
        'cache_hits' => 0,
        'cache_misses' => 0,
        'images_optimized' => 0,
        'total_original_size' => 0,
        'total_optimized_size' => 0,
        'total_webp_size' => 0,
    ];
    
    $stats['images_optimized']++;
    $stats['total_original_size'] += $originalSize;
    
    if (isset($results['jpeg'])) {
        $stats['total_optimized_size'] += $results['jpeg']['size'];
    }
    if (isset($results['webp'])) {
        $stats['total_webp_size'] += $results['webp']['size'];
    }
    if (isset($results['thumbnail'])) {
        $stats['total_optimized_size'] += $results['thumbnail']['size'];
    }
    
    $stats['last_updated'] = time();
    
    @file_put_contents($statsFile, json_encode($stats));
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
