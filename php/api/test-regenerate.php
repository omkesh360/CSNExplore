<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

set_time_limit(300);

try {
    $db = getDB();
    $categories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];
    $total = 0;

    define('SKIP_REGENERATE', true);
    require_once __DIR__ . '/../regenerate-complete.php';

    foreach ($categories as $category) {
        $listings = $db->fetchAll("SELECT * FROM {$category} WHERE is_active = 1");
        foreach ($listings as $listing) {
            $html = generateCompleteHTML($category, $listing, $db);
            $slug = generateSlug($category, $listing['id'], $listing['name']);
            $filename = __DIR__ . '/../../listing-detail/' . $slug . '.html';
            
            $dir = dirname($filename);
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            
            file_put_contents($filename, $html);
            $total++;
        }
    }
    echo "DONE! Generated $total files.";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
