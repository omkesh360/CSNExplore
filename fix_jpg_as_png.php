<?php
// Fix misnamed JPEG files saved as .png (FF D8 FF header = JPEG)
$root = 'C:/xampp/htdocs/CSNExplore';
$files = ['ajanta.png','bibi.png','daulatabad.png'];
$variantDir = $root . '/images/uploads/variants/';
$widths = [400, 700];

foreach ($files as $f) {
    $srcPath = $root . '/images/uploads/' . $f;
    $baseName = pathinfo($f, PATHINFO_FILENAME);
    
    $handle = fopen($srcPath, 'rb');
    $header = fread($handle, 3);
    fclose($handle);
    $hex = bin2hex($header);
    echo "File $f header hex: $hex\n";
    
    $img = imagecreatefromstring(file_get_contents($srcPath));
    
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
