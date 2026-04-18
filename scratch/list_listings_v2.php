<?php
require_once __DIR__ . '/../php/config.php';
$db = getDB();
$categories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];

foreach ($categories as $cat) {
    echo "\n=== $cat ===\n";
    try {
        $items = $db->fetchAll("SELECT * FROM $cat");
        foreach ($items as $item) {
            $name = $item['name'] ?? $item['operator'] ?? 'N/A';
            $id = $item['id'];
            echo "ID: $id | Name: $name\n";
        }
    } catch (Exception $e) {
        echo "Error in $cat: " . $e->getMessage() . "\n";
    }
}
