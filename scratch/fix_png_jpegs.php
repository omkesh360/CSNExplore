<?php
// Fix misnamed JPEG files saved as .png (FF D8 FF header = JPEG)
$files = ['ajanta.png','bibi.png','daulatabad.png'];
$variantDir = __DIR__ . '/images/uploads/variants/';
$widths = [400, 700];

foreach ($files as $f) {
    $srcPath = __DIR__ . '/images/uploads/' . $f;
    $baseName = pathinfo($f, PATHINFO_FILENAME);
    
    // Read first bytes to determine actual type
    $handle = fopen($srcPath, 'rb');
    $header = fread($handle, 3);
    fclose($handle);
    
    $hex = bin2hex($header);
    echo "File $f header: $hex\n";
    
    // FF D8 FF = JPEG
    if (substr($hex, 0, 6) === 'ffd8ff') {
        $img = imagecreatefromstring(file_get_contents($srcPath));
    } else {
        $img = imagecreatefrompng($srcPath);
    }
    
    if (!$img) { echo "  FAILED to load\n"; continue; }
    
    $srcW = imagesx($img);
    $srcH = imagesy($img);
    echo "  Loaded: {$srcW}x{$srcH}\n";
    
    foreach ($widths as $w) {
        $newW = min($w, $srcW);
        $newH = (int)round($srcH * ($newW / $srcW));
        $resized = imagecreatetruecolor($newW, $newH);
        imagecopyresampled($resized, $img, 0, 0, 0, 0, $newW, $newH, $srcW, $srcH);
        $destPath = $variantDir . $baseName . '-' . $w . 'w.webp';
        if (imagewebp($resized, $destPath, 82)) {
            echo "  OK: " . basename($destPath) . " " . round(filesize($destPath)/1024,1) . "KB\n";
        } else {
            echo "  FAIL: " . basename($destPath) . "\n";
        }
        imagedestroy($resized);
    }
    imagedestroy($img);
}
echo "Done.\n";
