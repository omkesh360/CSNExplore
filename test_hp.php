<?php
require 'php/config.php';
$db = getDB();

// Test stays
$stays = $db->fetchAll("SELECT id, name, image FROM stays WHERE is_active=1 LIMIT 3");
echo "STAYS:\n";
foreach ($stays as $s) {
    echo "  id={$s['id']} name={$s['name']} image=" . ($s['image'] ? substr($s['image'],0,60) : 'NULL') . "\n";
}

// Test blogs
$blogs = $db->fetchAll("SELECT id, title AS name, category AS type, image FROM blogs WHERE status='published' LIMIT 3");
echo "\nBLOGS:\n";
foreach ($blogs as $b) {
    echo "  id={$b['id']} name={$b['name']} image=" . ($b['image'] ? substr($b['image'],0,60) : 'NULL') . "\n";
}

// Test cars
$cars = $db->fetchAll("SELECT id, name, type, image FROM cars WHERE is_active=1 LIMIT 3");
echo "\nCARS:\n";
foreach ($cars as $c) {
    echo "  id={$c['id']} name={$c['name']} image=" . ($c['image'] ? substr($c['image'],0,60) : 'NULL') . "\n";
}
?>
