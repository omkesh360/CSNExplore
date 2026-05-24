<?php
/**
 * generate_webp_variants.php
 * One-time script: generates optimised WebP variants at 400w and 700w
 * for all PNG/JPEG images in /images/uploads/
 * 
 * Run once via browser: https://csnexplore.com/generate_webp_variants.php
 * Then DELETE or protect this file.
 */

// Simple security: only run from CLI or with a secret token
if (php_sapi_name() !== 'cli') {
    $token = $_GET['token'] ?? '';
    if ($token !== 'csn_gen_2026') {
        http_response_code(403);
        die('403 Forbidden. Add ?token=csn_gen_2026 to run.');
    }
}

set_time_limit(300);
ini_set('memory_limit', '256M');

$uploadDir  = __DIR__ . '/images/uploads/';
$variantDir = __DIR__ . '/images/uploads/variants/';

// Create variants directory if missing
if (!is_dir($variantDir)) {
    mkdir($variantDir, 0755, true);
}

// Create .htaccess in variants to allow serving webp
$htaccess = $variantDir . '.htaccess';
if (!file_exists($htaccess)) {
    file_put_contents($htaccess, "Options -Indexes\nAddType image/webp .webp\n");
}

// Target widths for responsive images
$widths = [400, 700];

// Supported source extensions
$extensions = ['png', 'jpg', 'jpeg', 'webp'];

$files = scandir($uploadDir);
$results = [];

foreach ($files as $file) {
    if ($file === '.' || $file === '..') continue;
    
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if (!in_array($ext, $extensions)) continue;
    
    $srcPath = $uploadDir . $file;
    $baseName = pathinfo($file, PATHINFO_FILENAME);
    
    foreach ($widths as $w) {
        $destFile = $baseName . '-' . $w . 'w.webp';
        $destPath = $variantDir . $destFile;
        
        // Skip if already exists and newer than source
        if (file_exists($destPath) && filemtime($destPath) >= filemtime($srcPath)) {
            $results[] = "⏭ Skipped (exists): $destFile";
            continue;
        }
        
        // Load source image
        $img = null;
        try {
            if ($ext === 'png') {
                $img = @imagecreatefrompng($srcPath);
            } elseif (in_array($ext, ['jpg', 'jpeg'])) {
                $img = @imagecreatefromjpeg($srcPath);
            } elseif ($ext === 'webp') {
                $img = @imagecreatefromwebp($srcPath);
            }
        } catch (Throwable $e) {
            $img = null;
        }
        
        if (!$img) {
            $results[] = "❌ Failed to load: $file";
            continue;
        }
        
        // Get source dimensions
        $srcW = imagesx($img);
        $srcH = imagesy($img);
        
        // Calculate new height preserving aspect ratio
        $newW = min($w, $srcW); // Don't upscale
        $newH = (int)round($srcH * ($newW / $srcW));
        
        // Create resized image
        $resized = imagecreatetruecolor($newW, $newH);
        
        // Preserve transparency for PNG
        if ($ext === 'png') {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
            imagefilledrectangle($resized, 0, 0, $newW, $newH, $transparent);
        }
        
        imagecopyresampled($resized, $img, 0, 0, 0, 0, $newW, $newH, $srcW, $srcH);
        
        // Save as WebP with quality 82
        if (imagewebp($resized, $destPath, 82)) {
            $origSize  = round(filesize($srcPath) / 1024, 1);
            $newSize   = round(filesize($destPath) / 1024, 1);
            $results[] = "✅ $destFile ({$newW}×{$newH}) — {$origSize}KB → {$newSize}KB";
        } else {
            $results[] = "❌ Failed to write WebP: $destFile";
        }
        
        imagedestroy($resized);
        imagedestroy($img);
    }
}

// Output results
if (php_sapi_name() === 'cli') {
    foreach ($results as $r) echo $r . "\n";
} else {
    echo '<html><head><title>WebP Variant Generator</title>';
    echo '<style>body{font-family:monospace;background:#111;color:#eee;padding:20px;}';
    echo '.ok{color:#4ade80}.skip{color:#94a3b8}.err{color:#f87171}</style></head><body>';
    echo '<h2>WebP Variant Generation Results</h2>';
    echo '<pre>';
    foreach ($results as $r) {
        if (strpos($r, '✅') !== false) echo '<span class="ok">' . htmlspecialchars($r) . "</span>\n";
        elseif (strpos($r, '⏭') !== false) echo '<span class="skip">' . htmlspecialchars($r) . "</span>\n";
        else echo '<span class="err">' . htmlspecialchars($r) . "</span>\n";
    }
    echo '</pre>';
    echo '<p style="color:#94a3b8">Total: ' . count($results) . ' files processed.</p>';
    echo '<p style="color:#f87171"><strong>⚠️ Delete or protect this file after running!</strong></p>';
    echo '</body></html>';
}
