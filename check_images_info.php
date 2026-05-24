<?php
$files = [
    'images/travelhub.png',
    'images/travelhub.webp',
    'images/csnexplore-logo.png',
    'images/csnexplore-logo.webp',
    'images/Logo-light.png',
    'images/Logo-light.webp',
    'images/Logo-dark.png',
    'images/Logo-dark.webp',
];

foreach ($files as $f) {
    if (file_exists($f)) {
        $size = filesize($f);
        $info = getimagesize($f);
        echo "$f: size=$size, dimensions=" . ($info ? "{$info[0]}x{$info[1]}" : "unknown") . "\n";
    } else {
        echo "$f: DOES NOT EXIST\n";
    }
}
