<?php
// Generate mobile-sized hero images (800px wide) for srcset
$heroImages = [
    'hotel-hero-section (4).webp'       => 'hotel-hero-section-mobile.webp',
    'car-rental-hero-section (3).webp'  => 'car-rental-hero-section-mobile.webp',
    'bike rentals-hero-section (6).webp'=> 'bike-rentals-hero-section-mobile.webp',
    'attractions-hero-section (7).webp' => 'attractions-hero-section-mobile.webp',
    'dine-hero-section (1).webp'        => 'dine-hero-section-mobile.webp',
    'bus-hero-section (2).webp'         => 'bus-hero-section-mobile.webp',
];

$dir = __DIR__ . '/images/';

foreach ($heroImages as $src => $dst) {
    $srcPath = $dir . $src;
    $dstPath = $dir . $dst;

    if (file_exists($dstPath)) { echo "SKIP $dst\n"; continue; }
    if (!file_exists($srcPath)) { echo "MISS $src\n"; continue; }

    $img = @imagecreatefromwebp($srcPath);
    if (!$img) { echo "FAIL $src\n"; continue; }

    $origW = imagesx($img);
    $origH = imagesy($img);
    $newW  = 800;
    $newH  = (int)round($origH * $newW / $origW);

    $resized = imagescale($img, $newW, $newH, IMG_BICUBIC);
    imagewebp($resized, $dstPath, 80);
    imagedestroy($img);
    imagedestroy($resized);

    $kb = round(filesize($dstPath) / 1024, 1);
    echo "OK   $dst ({$newW}x{$newH}, {$kb}KB)\n";
}
echo "Done.\n";
