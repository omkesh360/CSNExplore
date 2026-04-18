<?php
require_once __DIR__ . '/../php/config.php';
$db = getDB();

$cars_data = [
    1 => ['name' => 'Maruti Suzuki Ertiga', 'rating' => 4.5, 'type' => 'MUV', 'price' => 1800],
    2 => ['name' => 'Maruti Suzuki Swift', 'rating' => 4.3, 'type' => 'Hatchback', 'price' => 1200],
    3 => ['name' => 'Honda Amaze', 'rating' => 4.4, 'type' => 'Sedan', 'price' => 1400],
    4 => ['name' => 'Maruti Suzuki Baleno', 'rating' => 4.3, 'type' => 'Hatchback', 'price' => 1300],
    5 => ['name' => 'Tata Punch', 'rating' => 4.5, 'type' => 'Compact SUV', 'price' => 1500],
    6 => ['name' => 'Hyundai Grand i10', 'rating' => 4.2, 'type' => 'Hatchback', 'price' => 1100],
    7 => ['name' => 'Mahindra Bolero', 'rating' => 4.4, 'type' => 'SUV', 'price' => 2000],
    8 => ['name' => 'Kia Sonet', 'rating' => 4.6, 'type' => 'Compact SUV', 'price' => 1700],
    9 => ['name' => 'Tata Tiago', 'rating' => 4.2, 'type' => 'Hatchback', 'price' => 1000],
    10 => ['name' => 'Kia Carens', 'rating' => 4.7, 'type' => 'MUV', 'price' => 2200]
];

foreach ($cars_data as $id => $data) {
    if ($id <= 10) {
        $db->update('cars', [
            'name' => $data['name'],
            'rating' => $data['rating'],
            'type' => $data['type'],
            'price_per_day' => $data['price']
        ], 'id = :id', [':id' => $id]);
    }
}

// Also update Budget Inn to Gravity Inn
$db->update('stays', [
    'name' => 'Hotel The Gravity Inn Stays',
    'location' => 'Chhatrapati Sambhajinagar',
    'rating' => 4.8
], 'id = 3');

echo "Updated car names and Hotel The Gravity Inn Stays.\n";
