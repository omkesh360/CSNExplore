<?php
$files = glob(__DIR__ . '/images/*.png');
foreach ($files as $f) {
    $webp = preg_replace('/\.png$/', '.webp', $f);
    if (file_exists($webp)) continue;
    $img = @imagecreatefrompng($f);
    if (!$img) { echo "LOAD_FAIL " . basename($f) . "\n"; continue; }
    $tc = imageistruecolor($img);
    echo ($tc ? "TRUECOLOR" : "PALETTE  ") . " " . basename($f) . "\n";
    imagedestroy($img);
}
