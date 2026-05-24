<?php
/**
 * Image Optimization Script — converts PNG/JPG to WebP
 * Run once via CLI: php convert_images.php
 * Safe: originals are kept, WebP files are created alongside them
 */
$imageDir = __DIR__ . '/images';
$quality  = 82; // WebP quality (82 = excellent quality, ~70% smaller than PNG)
$converted = 0;
$skipped   = 0;
$errors    = 0;

function convertToWebP(string $src, int $quality): bool {
    $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));
    $dst = preg_replace('/\.(png|jpg|jpeg)$/i', '.webp', $src);

    // Skip if WebP already exists and is newer than source
    if (file_exists($dst) && filemtime($dst) >= filemtime($src)) {
        return false; // already done
    }

    $img = null;
    if ($ext === 'png') {
        $img = @imagecreatefrompng($src);
        if ($img) {
            // Convert palette/indexed PNG to true color (required for WebP)
            if (imageistruecolor($img) === false) {
                $trueColor = imagecreatetruecolor(imagesx($img), imagesy($img));
                imagealphablending($trueColor, false);
                imagesavealpha($trueColor, true);
                $transparent = imagecolorallocatealpha($trueColor, 0, 0, 0, 127);
                imagefill($trueColor, 0, 0, $transparent);
                imagecopy($trueColor, $img, 0, 0, 0, 0, imagesx($img), imagesy($img));
                imagedestroy($img);
                $img = $trueColor;
            } else {
                imagealphablending($img, true);
                imagesavealpha($img, true);
            }
        }
    } elseif ($ext === 'jpg' || $ext === 'jpeg') {
        $img = @imagecreatefromjpeg($src);
    }

    if (!$img) return false;

    $result = imagewebp($img, $dst, $quality);
    imagedestroy($img);
    return $result;
}

// Recursively find all PNG/JPG images
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($imageDir, RecursiveDirectoryIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    $ext = strtolower($file->getExtension());
    if (!in_array($ext, ['png', 'jpg', 'jpeg'])) continue;

    $path = $file->getPathname();
    $sizeBefore = round(filesize($path) / 1024, 1);

    $result = convertToWebP($path, $quality);
    if ($result === false) {
        // Check if it was skipped (already exists) or failed
        $webpPath = preg_replace('/\.(png|jpg|jpeg)$/i', '.webp', $path);
        if (file_exists($webpPath)) {
            $skipped++;
            echo "SKIP  {$file->getFilename()} (WebP already exists)\n";
        } else {
            $errors++;
            echo "FAIL  {$file->getFilename()}\n";
        }
    } else {
        $webpPath = preg_replace('/\.(png|jpg|jpeg)$/i', '.webp', $path);
        $sizeAfter = round(filesize($webpPath) / 1024, 1);
        $saving = round((1 - $sizeAfter / $sizeBefore) * 100, 1);
        echo "OK    {$file->getFilename()} → {$sizeBefore}KB → {$sizeAfter}KB (saved {$saving}%)\n";
        $converted++;
    }
}

echo "\n=== Done: {$converted} converted, {$skipped} skipped, {$errors} errors ===\n";
