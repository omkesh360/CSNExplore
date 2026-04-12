<?php
require 'php/config.php';
$db = getDB();

// Cars all have hotel images - fix with real car images
$carImages = [
    1  => 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=600&q=80',  // Ertiga - MPV
    2  => 'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?w=600&q=80',  // Swift - hatchback
    3  => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=600&q=80',  // Amaze - sedan
    4  => 'https://images.unsplash.com/photo-1550355291-bbee04a92027?w=600&q=80',  // Baleno - premium hatch
    5  => 'https://images.unsplash.com/photo-1609521263047-f8f205293f24?w=600&q=80',  // Tata Punch - compact SUV
    6  => 'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?w=600&q=80',  // i10 - hatchback
    7  => 'https://images.unsplash.com/photo-1519641471654-76ce0107ad1b?w=600&q=80',  // Bolero - MUV/SUV
    8  => 'https://images.unsplash.com/photo-1609521263047-f8f205293f24?w=600&q=80',  // Kia Sonet - SUV
    9  => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=600&q=80',  // Tiago - hatchback
    10 => 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=600&q=80',  // Kia Carens - MPV
];

$updated = 0;
foreach ($carImages as $id => $img) {
    $rows = $db->update('cars', ['image' => $img], 'id = :id', [':id' => $id]);
    if ($rows !== false) $updated++;
}
echo "Updated $updated car images.\n";

// Verify
$cars = $db->fetchAll('SELECT id, name, image FROM cars ORDER BY id');
foreach ($cars as $c) {
    echo $c['id'] . ' | ' . substr($c['name'], 0, 25) . ' | ' . substr($c['image'] ?? '', 0, 70) . "\n";
}
?>
