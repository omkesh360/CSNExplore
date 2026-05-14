<?php
/**
 * Image Optimization Script
 * Converts images to WebP and resizes them based on PageSpeed Insights recommendations.
 */

$rootDir = __DIR__;

function optimizeImage($sourcePath, $targetPath, $maxWidth, $maxHeight, $quality = 80) {
    if (!file_exists($sourcePath)) return false;
    
    $info = getimagesize($sourcePath);
    if (!$info) return false;
    
    list($origWidth, $origHeight, $type) = $info;
    
    // Calculate new dimensions while maintaining aspect ratio
    $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
    $newWidth = $origWidth;
    $newHeight = $origHeight;
    
    if ($ratio < 1) {
        $newWidth = round($origWidth * $ratio);
        $newHeight = round($origHeight * $ratio);
    } else {
        // If the user specified exactly 336x336, we might want to force it
        // For our purpose, if ratio >= 1, we just convert without resizing, or force resize if we want cropped.
        // Let's force exact size if requested to be square.
        if ($maxWidth === 336 && $maxHeight === 336) {
            $newWidth = 336;
            $newHeight = 336;
        }
    }
    
    $image = null;
    switch ($type) {
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_WEBP:
            $image = imagecreatefromwebp($sourcePath);
            break;
    }
    
    if (!$image) return false;
    
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    
    // Handle transparency for PNG/WebP
    if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_WEBP) {
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
        imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
    }
    
    imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
    
    // Save as WebP
    $success = imagewebp($newImage, $targetPath, $quality);
    
    imagedestroy($image);
    imagedestroy($newImage);
    
    return $success;
}

$tasks = [
    // 336x336 Square Attractions
    ['src' => 'images/uploads/ajanta.png', 'dest' => 'images/uploads/ajanta.webp', 'w' => 336, 'h' => 336],
    ['src' => 'images/uploads/ellora_caves.png', 'dest' => 'images/uploads/ellora_caves.webp', 'w' => 336, 'h' => 336],
    ['src' => 'images/uploads/bibi.png', 'dest' => 'images/uploads/bibi.webp', 'w' => 336, 'h' => 336],
    ['src' => 'images/uploads/daulatabad.png', 'dest' => 'images/uploads/daulatabad.webp', 'w' => 336, 'h' => 336],
    
    // Stays (JPEG to WebP)
    ['src' => 'images/uploads/its-home-home-stay-inn-stays-main.jpeg', 'dest' => 'images/uploads/its-home-home-stay-inn-stays-main.webp', 'w' => 336, 'h' => 189],
    
    // Vehicle Rentals
    ['src' => 'images/uploads/honda-dio-scooter-rental-bikes-main.webp', 'dest' => 'images/uploads/honda-dio-scooter-rental-bikes-main.webp', 'w' => 336, 'h' => 189],
    ['src' => 'images/uploads/tata-punch-cars-main.webp', 'dest' => 'images/uploads/tata-punch-cars-main.webp', 'w' => 336, 'h' => 189],
    
    // High compression for existing WebP
    ['src' => 'images/uploads/treebo-aroma-executive-stays-main.webp', 'dest' => 'images/uploads/treebo-aroma-executive-stays-main.webp', 'w' => 336, 'h' => 189, 'q' => 60],
    ['src' => 'images/uploads/its-home-service-apartments-stays-main.webp', 'dest' => 'images/uploads/its-home-service-apartments-stays-main.webp', 'w' => 336, 'h' => 189, 'q' => 60],
    
    // Mobile Hero Image
    ['src' => 'images/hotel-hero-section (4).webp', 'dest' => 'images/hotel-hero-section-mobile.webp', 'w' => 800, 'h' => 1000, 'q' => 60],
    
    // Logo
    ['src' => 'images/travelhub.png', 'dest' => 'images/travelhub.webp', 'w' => 120, 'h' => 36],
];

echo "Starting image optimization...<br><br>";

foreach ($tasks as $task) {
    $src = $rootDir . '/' . $task['src'];
    $dest = $rootDir . '/' . $task['dest'];
    $q = isset($task['q']) ? $task['q'] : 80;
    
    if (file_exists($src)) {
        if (optimizeImage($src, $dest, $task['w'], $task['h'], $q)) {
            echo "Optimized: {$task['src']} -> {$task['dest']} ({$task['w']}x{$task['h']})<br>";
        } else {
            echo "Failed to optimize: {$task['src']}<br>";
        }
    } else {
        echo "File not found: {$task['src']}<br>";
    }
}
echo "<br>Optimization complete!";
?>
