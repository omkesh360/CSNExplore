<?php
/**
 * Seed script: Replace all cars + hotels with new data
 * Run once: php php/seed-cars-hotels.php
 */
require_once __DIR__ . '/config.php';
$db = getDB();

echo "Starting seed...\n\n";

// ── 1. Add new columns to cars table ─────────────────────────────────────────
$alterations = [
    "ALTER TABLE `cars` ADD COLUMN `driver_available` TINYINT(1) DEFAULT 1 AFTER `seats`",
    "ALTER TABLE `cars` ADD COLUMN `price_with_driver` DECIMAL(10,2) DEFAULT 0 AFTER `driver_available`",
];
foreach ($alterations as $sql) {
    try { $db->getConnection()->exec($sql); echo "Column added.\n"; }
    catch (Exception $e) { echo "Column exists (ok).\n"; }
}

// ── 2. Clear old cars ─────────────────────────────────────────────────────────
$db->getConnection()->exec("DELETE FROM cars");
$db->getConnection()->exec("ALTER TABLE cars AUTO_INCREMENT = 1");
echo "Old cars cleared.\n";

// ── 3. Seed new cars ──────────────────────────────────────────────────────────
$cars = [
    ['Maruti Suzuki Ertiga', 'MUV',    'Chhatrapati Sambhajinagar', 1800, 2400, 4.5, 'Sedan', 'Petrol',  'Manual',    7, 'images/uploads/hotel-renaissance.jpg'],
    ['Maruti Suzuki Swift',  'Hatchback','Chhatrapati Sambhajinagar',1200, 1600, 4.3, null,    'Petrol',  'Manual',    5, 'images/uploads/hotel-room-1.jpg'],
    ['Honda Amaze',          'Sedan',   'Chhatrapati Sambhajinagar', 1400, 1900, 4.4, null,    'Petrol',  'Automatic', 5, 'images/uploads/1773410631-727467507.jpg'],
    ['Maruti Suzuki Baleno', 'Hatchback','Chhatrapati Sambhajinagar',1300, 1750, 4.3, null,    'Petrol',  'Manual',    5, 'images/uploads/hotel-renaissance.jpg'],
    ['Tata Punch',           'Compact SUV','Chhatrapati Sambhajinagar',1500,2000,4.5,'Top Rated','Petrol','Manual',   5, 'images/uploads/hotel-room-1.jpg'],
    ['Hyundai Grand i10',    'Hatchback','Chhatrapati Sambhajinagar', 1100, 1500, 4.2, 'Budget', 'Petrol', 'Manual',   5, 'images/uploads/1773410631-727467507.jpg'],
    ['Mahindra Bolero',      'SUV',     'Chhatrapati Sambhajinagar', 2000, 2800, 4.4, null,    'Diesel',  'Manual',    7, 'images/uploads/hotel-renaissance.jpg'],
    ['Kia Sonet',            'Compact SUV','Chhatrapati Sambhajinagar',1700,2300,4.6,'Top Rated','Petrol','Automatic', 5, 'images/uploads/hotel-room-1.jpg'],
    ['Tata Tiago',           'Hatchback','Chhatrapati Sambhajinagar', 1000, 1400, 4.2, 'Budget', 'Petrol', 'Manual',   5, 'images/uploads/1773410631-727467507.jpg'],
    ['Kia Carens',           'MUV',     'Chhatrapati Sambhajinagar', 2200, 3000, 4.7, 'Premium','Diesel', 'Automatic', 7, 'images/uploads/hotel-renaissance.jpg'],
];

foreach ($cars as $i => $c) {
    $db->insert('cars', [
        'name'              => $c[0],
        'type'              => $c[1],
        'location'          => $c[2],
        'price_per_day'     => $c[3],
        'price_with_driver' => $c[4],
        'rating'            => $c[5],
        'badge'             => $c[6],
        'fuel_type'         => $c[7],
        'transmission'      => $c[8],
        'seats'             => $c[9],
        'driver_available'  => 1,
        'image'             => $c[10],
        'description'       => 'Comfortable ' . $c[0] . ' available for self-drive or with driver in Chhatrapati Sambhajinagar. Well-maintained, AC, and fully insured.',
        'features'          => json_encode(['AC', 'Music System', 'GPS', 'Insurance Included']),
        'is_active'         => 1,
        'display_order'     => $i,
    ]);
    echo "  Car added: {$c[0]}\n";
}

// ── 4. Clear old hotels ───────────────────────────────────────────────────────
$db->getConnection()->exec("DELETE FROM stays");
$db->getConnection()->exec("ALTER TABLE stays AUTO_INCREMENT = 1");
echo "\nOld hotels cleared.\n";

// ── 5. Seed new hotels ────────────────────────────────────────────────────────
$hotels = [
    [
        'name'           => "ITS HOME – Home Stay Inn",
        'type'           => 'Homestay',
        'location'       => 'Chhatrapati Sambhajinagar',
        'price_per_night'=> 999,
        'rating'         => 4.3,
        'badge'          => null,
        'amenities'      => ['Wi-Fi', 'Parking', 'Hot Water', 'TV'],
        'description'    => 'A cozy and comfortable homestay in the heart of Chhatrapati Sambhajinagar. Perfect for families and solo travelers looking for a home-like experience.',
        'image'          => 'images/uploads/hotel-renaissance.jpg',
    ],
    [
        'name'           => "Its Home – Service Apartments",
        'type'           => 'Service Apartment',
        'location'       => 'Chhatrapati Sambhajinagar',
        'price_per_night'=> 1499,
        'rating'         => 4.4,
        'badge'          => null,
        'amenities'      => ['Wi-Fi', 'Kitchen', 'Parking', 'AC', 'Washing Machine'],
        'description'    => 'Fully furnished service apartments with kitchen facilities. Ideal for long stays and business travelers in Chhatrapati Sambhajinagar.',
        'image'          => 'images/uploads/hotel-room-1.jpg',
    ],
    [
        'name'           => "Treebo Aroma Executive",
        'type'           => 'Business Hotel',
        'location'       => 'Chhatrapati Sambhajinagar',
        'price_per_night'=> 1800,
        'rating'         => 4.2,
        'badge'          => null,
        'amenities'      => ['Wi-Fi', 'Breakfast', 'AC', 'Room Service', 'Parking'],
        'description'    => 'A premium business hotel offering executive rooms with modern amenities. Treebo certified quality with complimentary breakfast.',
        'image'          => 'images/uploads/1773410631-727467507.jpg',
    ],
    [
        'name'           => "Hotel Blossom",
        'type'           => 'Budget Hotel',
        'location'       => 'Chhatrapati Sambhajinagar',
        'price_per_night'=> 1200,
        'rating'         => 4.1,
        'badge'          => 'Budget',
        'amenities'      => ['Wi-Fi', 'AC', 'TV', 'Hot Water'],
        'description'    => 'Clean and affordable hotel in Chhatrapati Sambhajinagar. Great value for money with all essential amenities for a comfortable stay.',
        'image'          => 'images/uploads/hotel-renaissance.jpg',
    ],
    [
        'name'           => "Hotel The Gravity Inn",
        'type'           => 'Premium Hotel',
        'location'       => 'Silicon City, Indore',
        'price_per_night'=> 2172,
        'rating'         => 4.7,
        'badge'          => 'Top Rated',
        'amenities'      => ['Free Wi-Fi', 'Free Breakfast', 'AC', 'Parking', 'Room Service', 'Swimming Pool'],
        'description'    => 'A top-rated premium hotel in Silicon City, Indore. Featuring free Wi-Fi, complimentary breakfast, and world-class amenities for a luxurious stay.',
        'image'          => 'images/uploads/hotel-room-1.jpg',
    ],
    [
        'name'           => "Hotel O Indraprasth",
        'type'           => 'Budget Hotel',
        'location'       => 'Silicon City, Indore',
        'price_per_night'=> 840,
        'rating'         => 3.1,
        'badge'          => 'Budget',
        'amenities'      => ['Free Parking', 'Free Wi-Fi', 'AC', 'TV'],
        'description'    => 'Budget-friendly hotel in Silicon City, Indore with free parking and Wi-Fi. Perfect for travelers looking for affordable accommodation.',
        'image'          => 'images/uploads/1773410631-727467507.jpg',
    ],
];

foreach ($hotels as $i => $h) {
    $db->insert('stays', [
        'name'           => $h['name'],
        'type'           => $h['type'],
        'location'       => $h['location'],
        'price_per_night'=> $h['price_per_night'],
        'rating'         => $h['rating'],
        'badge'          => $h['badge'],
        'amenities'      => json_encode($h['amenities']),
        'description'    => $h['description'],
        'image'          => $h['image'],
        'is_active'      => 1,
        'display_order'  => $i,
        'max_guests'     => 2,
    ]);
    echo "  Hotel added: {$h['name']}\n";
}

echo "\n✅ Seed complete!\n";
echo "  Cars: " . count($cars) . "\n";
echo "  Hotels: " . count($hotels) . "\n";
