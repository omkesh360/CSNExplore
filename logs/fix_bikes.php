<?php
require 'php/config.php';
$db = getDB();

// Map bike names to appropriate Unsplash bike images
$bikeImages = [
    1  => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80',  // Hero Splendor - commuter
    2  => 'https://images.unsplash.com/photo-1449426468159-d96dbf08f19f?w=600&q=80',  // Honda Dio - scooter
    3  => 'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?w=600&q=80',  // Yamaha Ray - scooter
    4  => 'https://images.unsplash.com/photo-1449426468159-d96dbf08f19f?w=600&q=80',  // Honda Activa - scooter
    5  => 'https://images.unsplash.com/photo-1609357605129-2f2b4e73cf77?w=600&q=80',  // Honda Activa 6G
    6  => 'https://images.unsplash.com/photo-1571068316344-75bc76f77890?w=600&q=80',  // TVS Jupiter - scooter
    7  => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80',  // Suzuki Access 125
    8  => 'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?w=600&q=80',  // Honda Grazia
    9  => 'https://images.unsplash.com/photo-1591378603223-e15b45a81640?w=600&q=80',  // Honda X-Blade - sports
    10 => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80',  // Honda Shine - commuter
    11 => 'https://images.unsplash.com/photo-1591378603223-e15b45a81640?w=600&q=80',  // Bajaj Pulsar - sports
    12 => 'https://images.unsplash.com/photo-1558980664-3a031cf67ea8?w=600&q=80',  // Royal Enfield Classic - cruiser
    13 => 'https://images.unsplash.com/photo-1609357605129-2f2b4e73cf77?w=600&q=80',  // Royal Enfield Himalayan - adventure
    14 => 'https://images.unsplash.com/photo-1558980664-3a031cf67ea8?w=600&q=80',  // Bajaj Avenger - cruiser
    15 => 'https://images.unsplash.com/photo-1591378603223-e15b45a81640?w=600&q=80',  // Yamaha R15 - sports
];

$updated = 0;
foreach ($bikeImages as $id => $imageUrl) {
    $rows = $db->update('bikes', ['image' => $imageUrl], 'id = :id', [':id' => $id]);
    if ($rows > 0) $updated++;
}

echo "Updated $updated bike images successfully.\n";

// Verify
$bikes = $db->fetchAll('SELECT id, name, image FROM bikes ORDER BY id');
foreach ($bikes as $b) {
    echo $b['id'] . ' | ' . substr($b['name'], 0, 25) . ' | ' . substr($b['image'] ?? 'NULL', 0, 80) . "\n";
}
?>
