<?php
/**
 * Seed script: Add admin users + reseed all bikes
 * Run once: php php/seed-bikes-admins.php
 */
require_once __DIR__ . '/config.php';
$db = getDB();

echo "Starting seed...\n\n";

// ── 1. Ensure driver_available column exists on bikes ─────────────────────────
try {
    $db->getConnection()->exec("ALTER TABLE `bikes` ADD COLUMN `driver_available` TINYINT(1) DEFAULT 0 AFTER `cc`");
    echo "Column driver_available added to bikes.\n";
} catch (Exception $e) {
    echo "Column driver_available already exists (ok).\n";
}

// ── 2. Add admin users (if not exist) ─────────────────────────────────────────
$admins = [
    [
        'email'    => 'csnexplorerupeshadmin@csnexplore.com',
        'password' => 'rupeshAa.1@',
        'name'     => 'Rupesh Admin',
        'role'     => 'admin',
    ],
    [
        'email'    => 'csnexploreomkeshadmin@csnexplore.com',
        'password' => 'omekshAa.1@',
        'name'     => 'Omkesh Admin',
        'role'     => 'admin',
    ],
];

echo "\nProcessing admin users...\n";
foreach ($admins as $admin) {
    $existing = $db->fetchOne("SELECT id FROM users WHERE email = ?", [$admin['email']]);
    if ($existing) {
        echo "  Admin already exists: {$admin['email']}\n";
    } else {
        $db->insert('users', [
            'email'         => $admin['email'],
            'password_hash' => password_hash($admin['password'], PASSWORD_BCRYPT),
            'name'          => $admin['name'],
            'role'          => $admin['role'],
            'is_verified'   => 1,
        ]);
        echo "  Admin created: {$admin['name']} ({$admin['email']})\n";
    }
}

// ── 3. Clear all bikes ────────────────────────────────────────────────────────
$db->getConnection()->exec("DELETE FROM bikes");
$db->getConnection()->exec("ALTER TABLE bikes AUTO_INCREMENT = 1");
echo "\nAll bikes cleared.\n";

// ── 4. Seed bikes ─────────────────────────────────────────────────────────────
// Columns: name, type, price_per_day, rating, badge, image
$bikes = [
    // format: [name, type, price, rating, badge, image]
    ['Hero Splendor',             'Commuter',    800,    4.2, null,         'images/uploads/hero-splendor-bike-rental-chhatrapati-sambhajinagar.webp'],
    ['Hero HF Deluxe',            'Commuter',    950,    4.2, null,         'images/uploads/hero-splendor-bike-rental-chhatrapati-sambhajinagar.webp'],
    ['Honda Grazia',              'Scooter',     970,    4.3, null,         'images/uploads/honda-grazia-scooter-rental-chhatrapati-sambhajinagar.webp'],
    ['Honda Dio',                 'Scooter',     940,    4.4, null,         'images/uploads/honda-dio-scooter-rental-chhatrapati-sambhajinagar.webp'],
    ['TVS Jupiter',               'Scooter',     970,    4.4, null,         'images/uploads/tvs-jupiter-scooter-rental-chhatrapati-sambhajinagar.webp'],
    ['Honda Shine 100cc',         'Commuter',    1000,   4.3, null,         'images/uploads/honda-shine-125-bike-rental-chhatrapati-sambhajinagar.webp'],
    ['Yamaha Ray',                'Scooter',     1009,   4.3, null,         'images/uploads/yamaha-ray-scooter-rental-chhatrapati-sambhajinagar.webp'],
    ['Honda Activa',              'Scooter',     1067,   4.6, 'Top Rated',  'images/uploads/honda-activa-scooter-rental-chhatrapati-sambhajinagar.webp'],
    ['Suzuki Access 125',         'Scooter',     1012,   4.3, null,         'images/uploads/suzuki-access-125-scooter-rental-chhatrapati-sambhajinagar.webp'],
    ['Honda Activa 125cc',        'Scooter',     1012,   4.5, null,         'images/uploads/honda-activa-scooter-rental-chhatrapati-sambhajinagar.webp'],
    ['Honda Activa 6G',           'Scooter',     1067,   4.7, 'Top Rated',  'images/uploads/honda-activa-scooter-rental-chhatrapati-sambhajinagar.webp'],
    ['Honda X-Blade',             'Street Bike', 1128,   4.4, null,         'images/uploads/honda-shine-125-bike-rental-chhatrapati-sambhajinagar.webp'],
    ['Honda Shine 125cc',         'Commuter',    1104,   4.3, null,         'images/uploads/honda-shine-125-bike-rental-chhatrapati-sambhajinagar.webp'],
    ['Royal Enfield Classic 350', 'Cruiser',     2024,   4.8, 'Premium',    'images/uploads/royal-enfield-classic-350-bike-rental-chhatrapati-sambhajinagar.webp'],
];

$location = 'Chhatrapati Sambhajinagar';

$bike_details = [
    'Hero Splendor' => [
        'desc' => "The Hero Splendor remains a staple in the commuter segment due to its highly reliable 97.2cc engine and exceptional structural durability. It features a tubular double-cradle frame that offers excellent stability while remaining lightweight enough for effortless maneuvering through congested urban areas.",
        'specs' => ['Engine: 97.2cc, Air-Cooled', 'Power: 7.91 bhp', 'Torque: 8.05 Nm', 'Transmission: 4-Speed', 'Weight: 112 kg', 'Tank: 9.8L'],
        'features' => ['Telescopic Front Shocks', '5-Step Adjustable Rear', 'Integrated Braking (IBS)', 'Alloy Wheels & Tubeless Tires', 'Side Stand Engine Cut-off']
    ],
    'Hero HF Deluxe' => [
        'desc' => "Sharing its core mechanical platform with the Splendor, the HF Deluxe is engineered to be slightly lighter to improve off-the-line acceleration and agility. It utilizes Programmed Fuel Injection (FI) to ensure consistent throttle response and optimized fuel combustion.",
        'specs' => ['Engine: 97.2cc, Air-Cooled', 'Power: 7.91 bhp', 'Torque: 8.05 Nm', 'Transmission: 4-Speed', 'Weight: 110 kg', 'Tank: 9.6L'],
        'features' => ['Programmed FI Technology', 'Integrated Braking (IBS)', 'Telescopic Front Shocks', 'Multi-Reflector Headlight', 'Alloy Wheels']
    ],
    'Honda Grazia' => [
        'desc' => "The Honda Grazia caters to riders seeking premium aesthetics paired with the convenience of an automatic scooter. Powered by a responsive 125cc engine, it generates ample torque for quick overtakes. The fully digital dashboard gives the rider instant access to trip analytics.",
        'specs' => ['Engine: 124.9cc, Fan-Cooled', 'Power: 8.14 bhp', 'Torque: 10.3 Nm', 'Transmission: Automatic CVT', 'Weight: 108 kg', 'Tank: 5.3L'],
        'features' => ['LED Headlamp', 'Fully Digital Cluster', 'Eco-Speed Indicator', 'External Fuel Filler Flap', 'Front Disc Brake Option']
    ],
    'Honda Dio' => [
        'desc' => "Characterized by its aggressive, moto-scooter styling, the Honda Dio is specifically tuned for urban agility. It features an advanced Silent Start system with ACG technology, which eliminates traditional gear meshing noises during ignition.",
        'specs' => ['Engine: 109.51cc, Fan-Cooled', 'Power: 7.76 bhp', 'Torque: 9.0 Nm', 'Transmission: Automatic CVT', 'Weight: 105 kg', 'Tank: 5.3L'],
        'features' => ['LED Projector Headlamp', 'Silent Start (ACG)', 'Engine Start/Stop Switch', 'External Fuel Lid', 'Real-Time Mileage Indicators']
    ],
    'TVS Jupiter' => [
        'desc' => "The TVS Jupiter is meticulously designed around rider comfort and everyday practicality. It boasts an industry-leading seat length of 756mm, ensuring superior comfort. Large 12-inch wheels and telescopic front suspension provide exceptional stability.",
        'specs' => ['Engine: 113.3cc, Air-Cooled', 'Power: 7.91 bhp', 'Torque: 9.8 Nm', 'Transmission: Automatic CVT', 'Weight: 105 kg', 'Tank: 5.1L'],
        'features' => ['Telescopic Front Suspension', '3-Step Adjustable Rear Shock', 'Synchronized Braking (SBT)', 'External Fuel Fill', 'Large Under-Seat Storage']
    ],
    'Honda Shine 100cc' => [
        'desc' => "The Honda Shine 100 serves as the entry-level benchmark for engine refinement. Weighing under 100 kilograms, it is incredibly manageable. eSP technology optimizes engine friction reduction, significantly increasing fuel efficiency.",
        'specs' => ['Engine: 98.98cc, Air-Cooled', 'Power: 7.28 bhp', 'Torque: 8.05 Nm', 'Transmission: 4-Speed', 'Weight: 99 kg', 'Tank: 9.0L'],
        'features' => ['eSP Engine Technology', 'Combined Braking (CBS)', 'Side Stand Engine Cut-off', 'Long 677mm Seat', 'Dual Rear Shock Absorbers']
    ],
    'Yamaha Ray' => [
        'desc' => "Marketed under the RayZR line, this scooter merges aggressive street-fighter design with a high-performance 125cc Blue Core engine. The Smart Motor Generator facilitate silent ignition and provide a power assist functionality.",
        'specs' => ['Engine: 125cc, Air-Cooled', 'Power: 8.04 bhp', 'Torque: 10.3 Nm', 'Transmission: Automatic CVT', 'Weight: 99 kg', 'Tank: 5.2L'],
        'features' => ['Digital LCD Cluster', 'Smart Motor Generator (SMG)', 'Power Assist Function', 'Unified Braking (UBS)', 'Stop & Start System']
    ],
    'Honda Activa' => [
        'desc' => "The Honda Activa is universally recognized as the benchmark for reliability. It utilizes a full-metal body construction that guarantees long-term durability and imparts a secure, planted feel on the road.",
        'specs' => ['Engine: 109.51cc, Air-Cooled', 'Power: 7.88 bhp', 'Torque: 9.05 Nm', 'Transmission: Automatic CVT', 'Weight: 106 kg', 'Tank: 5.3L'],
        'features' => ['Full Metal Body', 'Combined Braking (CBS)', 'Telescopic Front Suspension', 'External Fuel Fill', 'ACG Silent Start']
    ],
    'Suzuki Access 125' => [
        'desc' => "The Suzuki Access 125 balances a classic, understated European design language with a remarkably potent engine. SEP technology ensures a strong mid-range surge, effortlessly carrying heavier loads.",
        'specs' => ['Engine: 124cc, Air-Cooled', 'Power: 8.6 bhp', 'Torque: 10.0 Nm', 'Transmission: Automatic CVT', 'Weight: 103 kg', 'Tank: 5.0L'],
        'features' => ['LED Headlamp with Chrome', 'External Fuel Filler Cap', 'Digital-Analog Cluster', 'USB Charging Port', 'Large 21.8L Storage']
    ],
    'Honda Activa 125cc' => [
        'desc' => "The Activa 125 elevates the proven Activa platform by introducing a larger displacement engine and premium hardware. Generating 10.5 Nm of torque, it effortlessly handles highway cruising and steep gradients.",
        'specs' => ['Engine: 123.92cc, Air-Cooled', 'Power: 8.3 bhp', 'Torque: 10.5 Nm', 'Transmission: Automatic CVT', 'Weight: 107 kg', 'Tank: 5.3L'],
        'features' => ['Idling Stop System', 'LED Signature Position Lamps', '3-Step Adjustable Rear', 'Multi-Function Switch', 'Side Stand Engine Immobilizer']
    ],
    'Honda Activa 6G' => [
        'desc' => "The 6th generation Activa introduces pivotal mechanical upgrades like telescopic front suspension and an enlarged 12-inch front wheel, which transforms high-speed stability and pothole absorption.",
        'specs' => ['Engine: 109.51cc, Air-Cooled', 'Power: 7.88 bhp', 'Torque: 9.05 Nm', 'Transmission: Automatic CVT', 'Weight: 106 kg', 'Tank: 5.3L'],
        'features' => ['12-inch Front Wheel', 'Telescopic Front Suspension', 'ACG Silent Start', 'Engine Start/Stop Switch', 'Full Metal Body Structure']
    ],
    'Honda X-Blade' => [
        'desc' => "The Honda X-Blade is constructed for riders seeking sharp, robotic styling combined with responsive mid-range power. It utilizes a wide 130-section rear tire and rear monoshock for superior grip and stability.",
        'specs' => ['Engine: 162.71cc, Air-Cooled', 'Power: 13.67 bhp', 'Torque: 14.7 Nm', 'Transmission: 5-Speed Manual', 'Weight: 143 kg', 'Tank: 12.0L'],
        'features' => ['Robo-Faced LED Headlamp', 'Gear Position Indicator', 'ABS (Anti-lock Braking)', 'Rear Monoshock Suspension', 'Hazard Light Switch']
    ],
    'Honda Shine 125cc' => [
        'desc' => "As the global leader in the 125cc segment, the Honda Shine achieves a flawless equilibrium between usable power and economic efficiency. The 5-speed gearbox lowers RPM at highway speeds, reducing vibration.",
        'specs' => ['Engine: 123.94cc, Air-Cooled', 'Power: 10.59 bhp', 'Torque: 11.0 Nm', 'Transmission: 5-Speed Manual', 'Weight: 113 kg', 'Tank: 10.5L'],
        'features' => ['5-Speed Transmission', 'eSP Engine Technology', 'ACG Silent Start', 'Chrome Premium Console', 'Upright Comfort Geometry']
    ],
    'Royal Enfield Classic 350' => [
        'desc' => "The Royal Enfield Classic 350 is the definitive choice for premium, long-distance cruising. Built upon the sophisticated J-series engine platform, it retains the iconic low-end thump while suppressing harsh vibrations.",
        'specs' => ['Engine: 349cc, Air-Oil Cooled', 'Power: 20.2 bhp', 'Torque: 27.0 Nm', 'Transmission: 5-Speed Manual', 'Weight: 195 kg', 'Tank: 13.0L'],
        'features' => ['J-Series Engine Platform', 'Dual Channel ABS', '41mm Telescopic Forks', '6-Step Adjustable Preload', 'USB Charging Port']
    ],
];

echo "\nSeeding bikes...\n";
foreach ($bikes as $i => $b) {
    $name = $b[0];
    $info = $bike_details[$name] ?? null;

    $desc = $info ? $info['desc'] : $name . ' available for self-ride in Chhatrapati Sambhajinagar.';
    $feats = $info ? $info['features'] : ['Helmet Included', 'Insurance', 'Full Tank'];

    // Add specs to description
    if ($info) {
        $desc .= "\n\nSpecifications:\n* " . implode("\n* ", $info['specs']);
    }

    $db->insert('bikes', [
        'name'             => $name,
        'type'             => $b[1],
        'location'         => $location,
        'price_per_day'    => $b[2],
        'rating'           => $b[3],
        'badge'            => $b[4],
        'fuel_type'        => 'Petrol',
        'image'            => $b[5],
        'description'      => $desc,
        'features'         => json_encode($feats),
        'is_active'        => 1,
        'driver_available' => 0,
        'display_order'    => $i,
    ]);
    echo "  Bike added: $name\n";
}

echo "\n✅ Seed complete!\n";
echo "  Admins processed: " . count($admins) . "\n";
echo "  Bikes seeded: " . count($bikes) . "\n";
