<?php
$files = ['ajanta.png','bibi.png','daulatabad.png'];
foreach ($files as $f) {
    $path = 'images/uploads/' . $f;
    $info = getimagesize($path);
    echo $f . ': type=' . $info[2] . PHP_EOL;
    // Try imagecreatefrompng
    $img = @imagecreatefrompng($path);
    if ($img) { echo "  imagecreatefrompng OK: ".imagesx($img)."x".imagesy($img).PHP_EOL; imagedestroy($img); }
    else {
        echo "  imagecreatefrompng FAILED, trying imagick..." . PHP_EOL;
        if (class_exists('Imagick')) {
            $im = new Imagick($path);
            echo "  Imagick type: " . $im->getImageType() . PHP_EOL;
            $im->destroy();
        }
    }
}
