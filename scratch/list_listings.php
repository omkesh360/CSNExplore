<?php
require_once __DIR__ . '/../php/config.php';
$db = getDB();
$categories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];

foreach ($categories as $cat) {
    echo "\n=== $cat ===\n";
    $query = ($cat === 'buses') ? "SELECT id, operator, location FROM $cat" : "SELECT id, name, location FROM $cat";
    $items = $db->fetchAll($query);
    foreach ($items as $item) {
        $name = $item['name'] ?? $item['operator'] ?? 'N/A';
        echo "ID: {$item['id']} | Name: $name | Location: {$item['location']}\n";
    }
}
