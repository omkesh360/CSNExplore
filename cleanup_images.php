<?php
require_once __DIR__ . '/php/config.php';

try {
    $db = getDB()->getConnection();
} catch (Exception $e) {
    die("DB Connection failed: " . $e->getMessage() . "\n");
}

echo "Updating database tables...\n";
$tables = ['stays', 'cars', 'bikes', 'attractions', 'restaurants', 'buses', 'blogs'];

foreach ($tables as $table) {
    echo "Processing table: $table\n";
    
    // Update 'image' column
    $stmt = $db->prepare("UPDATE `$table` SET `image` = REPLACE(`image`, '.png', '.webp') WHERE `image` LIKE '%.png'");
    $stmt->execute();
    echo "  Replaced .png in image: " . $stmt->rowCount() . " rows\n";
    
    $stmt = $db->prepare("UPDATE `$table` SET `image` = REPLACE(`image`, '.jpg', '.webp') WHERE `image` LIKE '%.jpg'");
    $stmt->execute();
    echo "  Replaced .jpg in image: " . $stmt->rowCount() . " rows\n";
    
    $stmt = $db->prepare("UPDATE `$table` SET `image` = REPLACE(`image`, '.jpeg', '.webp') WHERE `image` LIKE '%.jpeg'");
    $stmt->execute();
    echo "  Replaced .jpeg in image: " . $stmt->rowCount() . " rows\n";

    // Update 'gallery' column if it exists (blogs does not have gallery)
    if ($table !== 'blogs') {
        $stmt = $db->prepare("UPDATE `$table` SET `gallery` = REPLACE(`gallery`, '.png', '.webp') WHERE `gallery` LIKE '%.png%'");
        $stmt->execute();
        echo "  Replaced .png in gallery: " . $stmt->rowCount() . " rows\n";
        
        $stmt = $db->prepare("UPDATE `$table` SET `gallery` = REPLACE(`gallery`, '.jpg', '.webp') WHERE `gallery` LIKE '%.jpg%'");
        $stmt->execute();
        echo "  Replaced .gallery in image: " . $stmt->rowCount() . " rows\n";
        
        $stmt = $db->prepare("UPDATE `$table` SET `gallery` = REPLACE(`gallery`, '.jpeg', '.webp') WHERE `gallery` LIKE '%.jpeg%'");
        $stmt->execute();
        echo "  Replaced .jpeg in gallery: " . $stmt->rowCount() . " rows\n";
    }
}

echo "\nCleaning up duplicate image files...\n";
$dirs = [__DIR__ . '/images/', __DIR__ . '/images/uploads/', __DIR__ . '/images/fevicon/'];

$deleted = 0;
foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;
    
    echo "Scanning $dir\n";
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $path = $dir . $file;
        if (!is_file($path)) continue;
        
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($ext, ['png', 'jpg', 'jpeg'])) {
            $base = pathinfo($path, PATHINFO_FILENAME);
            $webpPath = $dir . $base . '.webp';
            
            if (file_exists($webpPath)) {
                echo "  Deleting $file (webp exists)\n";
                if (unlink($path)) {
                    $deleted++;
                } else {
                    echo "  FAILED to delete $file\n";
                }
            }
        }
    }
}

echo "Done! Deleted $deleted duplicate files.\n";
