<?php
// Convert uploads folder PNGs — handles RGBA/palette/corrupted PNGs more robustly
$dirs = [
    __DIR__ . '/images/uploads',
    __DIR__ . '/images',
];
$quality = 82;
$converted = 0;

foreach ($dirs as $dir) {
    foreach (glob($dir . '/*.png') as $src) {
        $dst = preg_replace('/\.png$/i', '.webp', $src);
        if (file_exists($dst)) { echo "SKIP " . basename($src) . "\n"; continue; }

        // Try loading as PNG
        $img = @imagecreatefrompng($src);
        if (!$img) {
            // Try as generic image
            $img = @imagecreatefromstring(file_get_contents($src));
        }
        if (!$img) { echo "FAIL " . basename($src) . "\n"; continue; }

        $w = imagesx($img);
        $h = imagesy($img);

        // Always convert to true color RGBA canvas
        $canvas = imagecreatetruecolor($w, $h);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
        imagefill($canvas, 0, 0, $transparent);
        imagecopy($canvas, $img, 0, 0, 0, 0, $w, $h);
        imagedestroy($img);

        $sizeBefore = round(filesize($src) / 1024, 1);
        $ok = imagewebp($canvas, $dst, $quality);
        imagedestroy($canvas);

        if ($ok) {
            $sizeAfter = round(filesize($dst) / 1024, 1);
            $saving = round((1 - $sizeAfter / $sizeBefore) * 100, 1);
            echo "OK   " . basename($src) . " → {$sizeBefore}KB → {$sizeAfter}KB (saved {$saving}%)\n";
            $converted++;
        } else {
            echo "FAIL " . basename($src) . "\n";
        }
    }
}
echo "\nDone: $converted converted\n";
