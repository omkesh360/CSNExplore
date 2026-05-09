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
    "ALTER TABLE `cars` ADD COLUMN `pricing_packages` JSON DEFAULT NULL AFTER `price_with_driver`",
];
foreach ($alterations as $sql) {
    try { $db->getConnection()->exec($sql); echo "Column added.\n"; }
    catch (Exception $e) { /* exists */ }
}

// ── 2. Clear old cars ─────────────────────────────────────────────────────────
$db->getConnection()->exec("DELETE FROM cars");
$db->getConnection()->exec("ALTER TABLE cars AUTO_INCREMENT = 1");
echo "Old cars cleared.\n";

// ── 3. Seed new cars ──────────────────────────────────────────────────────────

$hatch_packages = json_encode([
    'flat_rate' => 2300,
    'packages' => [
        ['name' => 'Package 1', 'price' => 1999, 'limit' => '200 km/day'],
        ['name' => 'Package 2', 'price' => 2299, 'limit' => '300 km/24 hrs']
    ],
    'extra_km' => 7
]);

$tiago_packages = json_encode([
    'packages' => [
        ['name' => 'Package 1', 'price' => 1999, 'limit' => '200 km/day'],
        ['name' => 'Package 2', 'price' => 2299, 'limit' => '300 km/24 hrs']
    ],
    'extra_km' => 7
]);

$glanza_packages = json_encode([
    'flat_rate' => 1100,
    'per_km' => 11,
    'per_km_note' => 'AC'
]);

$ertiga_packages = json_encode([
    'flat_rate' => 2800,
    'packages' => [
        ['name' => 'Package 1', 'price' => 2199, 'limit' => '200 km/day'],
        ['name' => 'Package 2', 'price' => 2799, 'limit' => '300 km/24 hrs']
    ],
    'per_km' => 13,
    'per_km_note' => 'AC (6+1 Seater)',
    'extra_km' => 9
]);

$carens_packages = json_encode([
    'flat_rate' => 3000,
    'packages' => [
        ['name' => 'Package 1', 'price' => 2499, 'limit' => '200 km/day'],
        ['name' => 'Package 2', 'price' => 2999, 'limit' => '300 km/24 hrs']
    ],
    'extra_km' => 10
]);

$hector_packages = json_encode([
    'packages' => [
        ['name' => 'Package 1', 'price' => 2799, 'limit' => '200 km/day'],
        ['name' => 'Package 2', 'price' => 3499, 'limit' => '300 km/24 hrs']
    ],
    'extra_km' => 14
]);

$innova_packages = json_encode([
    'flat_rate' => 1800,
    'per_km' => 18,
    'per_km_note' => 'AC (5+1 Seater)'
]);

$winger_packages = json_encode([
    'flat_rate' => 2300,
    'per_km' => 23,
    'per_km_note' => 'AC or Non-AC (10+1 Seater)'
]);

$car_details = [
    'Maruti Suzuki Swift' => [
        'desc' => "The Maruti Suzuki Swift is built on the advanced Heartect platform, providing a rigid yet lightweight structure that significantly enhances both fuel efficiency and handling. It is powered by the proven 1.2-liter K-Series engine, known for its peppy power delivery and smooth acceleration. The Swift's compact dimensions and highly responsive steering setup make it exceptionally agile, allowing drivers to effortlessly navigate crowded city streets or narrow lanes near local tourist sites.",
        'specs' => ['Fuel: Petrol', 'Engine: 1.2L K-Series VVT (1197cc)', 'Transmission: Manual', 'Seats: 5 Passengers', 'Boot: 268 Liters'],
        'features' => ['Manual AC with Heater', 'Bluetooth/USB Audio', 'ABS with EBD', 'Dual Front Airbags', 'Power Steering & Windows', 'Insurance Included']
    ],
    'Tata Punch' => [
        'desc' => "The Tata Punch is engineered on the ALFA-ARC platform, delivering a robust micro-SUV experience. It features an imposing stance, high seating position, and a best-in-class ground clearance of 187mm, making it highly capable of handling uneven terrain, rural roads, or steep inclines with ease. The vehicle boasts a 5-star Global NCAP safety rating, providing peace of mind through a reinforced cabin structure.",
        'specs' => ['Fuel: Petrol', 'Engine: 1.2L Revotron (1199cc)', 'Transmission: Manual', 'Seats: 5 Passengers', 'Boot: 366 Liters'],
        'features' => ['AC with Heater', 'Touchscreen Infotainment', 'Dual Airbags', '90-Degree Door Opening', 'Steering Audio Controls', 'Insurance Included']
    ],
    'Honda Amaze' => [
        'desc' => "The Honda Amaze represents the pinnacle of compact sedan engineering, focusing heavily on cabin space and ride refinement. The renowned 1.2-liter i-VTEC engine operates with minimal noise, vibration, and harshness (NVH), ensuring a remarkably quiet cabin environment even at highway speeds. With its massive 420-liter trunk capacity, it seamlessly accommodates heavy luggage, making it the ideal vehicle for airport transfers.",
        'specs' => ['Fuel: Petrol', 'Engine: 1.2L i-VTEC (1199cc)', 'Transmission: Manual', 'Seats: 5 Passengers', 'Boot: 420 Liters'],
        'features' => ['Automatic Climate Control', 'Advanced Infotainment', 'Parking Camera & Sensors', 'ABS with EBD', 'Rear Center Armrest', 'Insurance Included']
    ],
    'Hyundai Grand i10' => [
        'desc' => "The Hyundai Grand i10 is designed with a strong emphasis on premium urban drivability. Its 1.2-liter Kappa engine is mated to a highly refined gearbox and an exceptionally light clutch, drastically reducing driver fatigue in stop-and-go traffic. Inside, the vehicle sets a high benchmark for its segment with high-quality plastics, dual-tone interiors, and excellent sound insulation.",
        'specs' => ['Fuel: Petrol', 'Engine: 1.2L Kappa VTVT (1197cc)', 'Transmission: Manual', 'Seats: 5 Passengers', 'Boot: 260 Liters'],
        'features' => ['Rear AC Vents', 'Apple CarPlay / Android Auto', 'Dual Airbags & ABS', 'Electric Adjustable Mirrors', 'Bluetooth Calling', 'Insurance Included']
    ],
    'Maruti Suzuki Baleno' => [
        'desc' => "Positioned as a premium executive hatchback, the Maruti Suzuki Baleno features a wide track and long wheelbase, translating into vast interior space. The rear bench is wide enough to seat three adults comfortably without overlapping shoulder room, while offering class-leading legroom. The Dual Jet engine technology results in superior thermal efficiency and exceptional fuel economy during long highway cruises.",
        'specs' => ['Fuel: Petrol', 'Engine: 1.2L Dual Jet (1197cc)', 'Transmission: Manual', 'Seats: 5 Passengers', 'Boot: 318 Liters'],
        'features' => ['Auto Climate Control', 'SmartPlay Infotainment', 'Keyless Entry & Push Start', 'Tilt & Telescopic Steering', 'Brake Assist & EBD', 'Insurance Included']
    ],
    'Tata Tiago' => [
        'desc' => "The Tata Tiago is built under Tata's Impact Design philosophy, offering a heavy, secure build quality that is immediately noticeable. It holds a 4-star safety rating, making it one of the most durable cars in its price bracket. The suspension is meticulously calibrated to tackle Indian road conditions, effortlessly gliding over potholes while maintaining excellent straight-line stability.",
        'specs' => ['Fuel: Petrol', 'Engine: 1.2L Revotron (1199cc)', 'Transmission: Manual', 'Seats: 5 Passengers', 'Boot: 242 Liters'],
        'features' => ['Harman Premium Audio', 'Manual AC', 'Corner Stability Control', 'Digital Cluster', 'Day/Night IRVM', 'Insurance Included']
    ],
    'Toyota Glanza' => [
        'desc' => "The Toyota Glanza delivers a sophisticated, upscale driving experience backed by world-class reliability standards. The cabin is equipped with UV-protect glass, keeping the interior significantly cooler by blocking harmful rays. The MacPherson strut front suspension is tuned to provide a plush, isolated ride, making it an excellent choice for executives demanding a silent, smooth, and dependable travel experience.",
        'specs' => ['Fuel: Petrol', 'Engine: 1.2L K-Series (1197cc)', 'Transmission: Manual', 'Seats: 5 Passengers', 'Boot: 318 Liters'],
        'features' => ['Auto Climate Control', 'Touchscreen with Voice Command', 'UV Protect Glass', 'Dual Airbags & ABS', 'Steering Controls', 'Insurance Included']
    ],
    'Maruti Suzuki Ertiga' => [
        'desc' => "The Maruti Suzuki Ertiga is the quintessential multi-utility vehicle, masterfully balancing physical footprint with maximum passenger capacity. Utilizing Smart Hybrid (SHVS) technology, it provides brake energy regeneration and torque assist, ensuring optimal fuel efficiency. The 50:50 split third-row and 60:40 split second-row seats offer incredible modularity for luggage and passengers.",
        'specs' => ['Fuel: Diesel / Petrol', 'Engine: 1.5L Smart Hybrid', 'Transmission: Manual', 'Seats: 7 Passengers', 'Boot: 209-550 Liters'],
        'features' => ['Front & Rear AC', 'SmartPlay Infotainment', 'Reclining 2nd & 3rd Rows', 'Speed Sensitive Door Lock', 'Brake Assist', 'Insurance Included']
    ],
    'Kia Carens' => [
        'desc' => "Marketed as a recreational vehicle, the Kia Carens utilizes an extended wheelbase to prioritize interior volume and passenger luxury. The cabin features a wraparound dashboard, ambient lighting, and high-grade upholstery. It is engineered with a Robust 10 Hi-Safety Package, ensuring maximum occupant protection during group transit.",
        'specs' => ['Fuel: Diesel', 'Engine: 1.5L CRDi VGT', 'Transmission: Manual', 'Seats: 7 Passengers', 'Boot: 216 Liters'],
        'features' => ['Auto Climate with Roof Vents', 'Large Touchscreen Nav', '6 Airbags Standard', 'ESC & Hill Assist', 'USB Type-C Ports', 'Insurance Included']
    ],
    'MG Hector' => [
        'desc' => "The MG Hector is a formidable presence on the road, offering dimensions and interior volume that rival vehicles in segments far above its class. Powered by a potent 2.0-liter turbocharged diesel engine, it produces massive low-end torque. The interior is defined by a colossal portrait-style infotainment system and generous use of soft-touch materials.",
        'specs' => ['Fuel: Diesel', 'Engine: 2.0L Turbocharged', 'Transmission: Manual', 'Seats: 5 Passengers', 'Boot: 587 Liters'],
        'features' => ['Auto Climate Control', '10.4-inch HD Touchscreen', 'Panoramic Sunroof', 'ESP & Traction Control', '360-Degree Camera', 'Insurance Included']
    ],
    'Toyota Innova' => [
        'desc' => "The Toyota Innova is constructed on a heavy-duty ladder-frame chassis, granting it legendary durability and load-carrying capacity. This architecture allows the Innova to absorb the most punishing road conditions without transferring impact to the cabin. It is powered by the globally respected GD series diesel engine, known for running hundreds of thousands of kilometers.",
        'specs' => ['Fuel: Diesel', 'Engine: 2.4L GD Series', 'Transmission: Manual', 'Seats: 6-7 Passengers', 'Boot: 300 Liters'],
        'features' => ['High-Capacity Multi-Zone AC', 'Premium Fabric/Leatherette', 'Vehicle Stability Control', '3-Point Seatbelts', 'Integrated Audio', 'Insurance Included']
    ],
    'Tata Winger' => [
        'desc' => "Unlike traditional commercial vans, the Tata Winger utilizes a monocoque construction and front-wheel-drive layout. This design drastically lowers floor height and eliminates the harsh ride typically associated with high-capacity transporters. The high-roof design allows passengers to stand and move freely, ensuring large group comfort.",
        'specs' => ['Fuel: Diesel', 'Engine: 2.2L Dicor', 'Transmission: Manual', 'Seats: 11 Passengers', 'Body: High-Roof Van'],
        'features' => ['Dedicated Rear AC', 'Push-Back Seats with Armrests', 'Ample Legroom & Aisle Space', 'High Roof Entry/Exit', 'Heavy-Duty Suspension', 'Insurance Included']
    ],
];

$cars = [
    // format: [Name, Type, price_per_day, fuel, seats, pkgs, image]
    ['Maruti Suzuki Swift', 'Hatchback', 1999, 'Petrol', 5, $hatch_packages, 'images/uploads/maruti-suzuki-swift-car-rental-chhatrapati-sambhajinagar.webp'],
    ['Tata Punch', 'Compact SUV', 1999, 'Petrol', 5, $hatch_packages, 'images/uploads/tata-punch-suv-rental-chhatrapati-sambhajinagar.webp'],
    ['Honda Amaze', 'Sedan', 1999, 'Petrol', 5, $hatch_packages, 'images/uploads/honda-amaze-sedan-rental-chhatrapati-sambhajinagar.webp'],
    ['Hyundai Grand i10', 'Hatchback', 1999, 'Petrol', 5, $hatch_packages, 'images/uploads/hyundai-grand-i10-nios-car-rental-chhatrapati-sambhajinagar.webp'],
    ['Maruti Suzuki Baleno', 'Hatchback', 1999, 'Petrol', 5, $hatch_packages, 'images/uploads/maruti-suzuki-baleno-car-rental-chhatrapati-sambhajinagar.webp'],
    ['Tata Tiago', 'Hatchback', 1999, 'Petrol', 5, $tiago_packages, 'images/uploads/tata-tiago-hatchback-rental-chhatrapati-sambhajinagar.webp'],
    ['Toyota Glanza', 'Hatchback', 1100, 'Petrol', 5, $glanza_packages, 'images/uploads/maruti-suzuki-baleno-car-rental-chhatrapati-sambhajinagar.webp'], // Baleno clone
    ['Maruti Suzuki Ertiga', 'MUV', 2199, 'Diesel', 7, $ertiga_packages, 'images/uploads/maruti-suzuki-ertiga-car-rental-chhatrapati-sambhajinagar.webp'],
    ['Kia Carens', 'MUV', 2499, 'Diesel', 7, $carens_packages, 'images/uploads/kia-carens-mpv-rental-chhatrapati-sambhajinagar.webp'],
    ['MG Hector', 'SUV', 2799, 'Diesel', 5, $hector_packages, 'images/uploads/kia-sonet-suv-rental-chhatrapati-sambhajinagar.webp'],
    ['Toyota Innova', 'MUV', 1800, 'Diesel', 6, $innova_packages, 'images/uploads/toyota-fortuner.jpg'],
    ['Tata Winger', 'Van', 2300, 'Diesel', 11, $winger_packages, 'images/uploads/travel-gate.jpg'],
];

foreach ($cars as $i => $c) {
    $name = $c[0];
    $info = $car_details[$name] ?? null;
    
    $desc = $info ? $info['desc'] : 'Comfortable ' . $name . ' available for self-drive or with driver in Chhatrapati Sambhajinagar.';
    $feats = $info ? $info['features'] : ['AC', 'Music System', 'GPS', 'Insurance Included'];
    
    // Add specs to description
    if ($info) {
        $desc .= "\n\nSpecifications:\n* " . implode("\n* ", $info['specs']);
    }

    $db->insert('cars', [
        'name'              => $name,
        'type'              => $c[1],
        'location'          => 'Chhatrapati Sambhajinagar',
        'price_per_day'     => $c[2],
        'price_with_driver' => 0,
        'rating'            => 4.5,
        'badge'             => null,
        'fuel_type'         => $c[3],
        'transmission'      => 'Manual',
        'seats'             => $c[4],
        'driver_available'  => 1,
        'pricing_packages'  => $c[5],
        'image'             => $c[6],
        'description'       => $desc,
        'features'          => json_encode($feats),
        'is_active'         => 1,
        'display_order'     => $i,
    ]);
    echo "  Car added: $name\n";
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
        'image'          => 'images/uploads/its-home-home-stay-inn-stays-main.jpeg',
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
        'image'          => 'images/uploads/its-home-service-apartments-stays-main.webp',
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
        'image'          => 'images/uploads/treebo-aroma-executive-stays-main.webp',
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
        'image'          => 'images/uploads/hotel-blossom-stays-main.jpeg',
    ],
    [
        'name'           => "Hotel The Gravity Inn",
        'type'           => 'Premium Hotel',
        'location'       => 'Chhatrapati Sambhajinagar',
        'price_per_night'=> 2172,
        'rating'         => 4.7,
        'badge'          => 'Top Rated',
        'amenities'      => ['Free Wi-Fi', 'Free Breakfast', 'AC', 'Parking', 'Room Service', 'Swimming Pool'],
        'description'    => 'A top-rated premium hotel in Chhatrapati Sambhajinagar. Featuring free Wi-Fi, complimentary breakfast, and world-class amenities for a luxurious stay.',
        'image'          => 'images/uploads/hotel-the-gravity-inn-stays-main.webp',
    ],
    [
        'name'           => "Hotel O Indraprasth",
        'type'           => 'Budget Hotel',
        'location'       => 'Chhatrapati Sambhajinagar',
        'price_per_night'=> 840,
        'rating'         => 3.1,
        'badge'          => 'Budget',
        'amenities'      => ['Free Parking', 'Free Wi-Fi', 'AC', 'TV'],
        'description'    => 'Budget-friendly hotel in Chhatrapati Sambhajinagar with free parking and Wi-Fi. Perfect for travelers looking for affordable accommodation.',
        'image'          => 'images/uploads/hotel-o-indraprasth-stays-main.jpeg',
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
