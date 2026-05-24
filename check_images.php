<?php
require_once 'php/config.php';
$db = getDB();
$tables = ['cars', 'bikes', 'stays', 'restaurants', 'attractions', 'buses'];
foreach ($tables as $table) {
    $col = ($table === 'buses') ? 'operator' : 'name';
    $items = $db->fetchAll("SELECT id, $col as name, image FROM $table WHERE is_active=1");
    foreach ($items as $item) {
        $img = $item['image'] ?? '';
        if (!$img) { echo "EMPTY  [$table #{$item['id']}] {$item['name']}\n"; continue; }
        if (strpos($img, 'http') === 0) { echo "EXTERN [$table #{$item['id']}] {$item['name']} → $img\n"; continue; }
        $path = __DIR__ . '/' . ltrim($img, '/');
        if (!file_exists($path)) {
            echo "MISS   [$table #{$item['id']}] {$item['name']} → $img\n";
        }
    }
}
echo "\nDone.\n";
