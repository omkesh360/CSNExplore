<?php
$src = __DIR__ . '/images/fevicon/android-icon-192x192.png';
$dst = __DIR__ . '/images/fevicon/android-icon-512x512.png';

if (file_exists($dst)) { echo "Already exists\n"; exit; }

$img = imagecreatefrompng($src);
$resized = imagescale($img, 512, 512, IMG_BICUBIC);
imagepng($resized, $dst, 6);
imagedestroy($img);
imagedestroy($resized);
echo "Created 512x512 icon\n";
