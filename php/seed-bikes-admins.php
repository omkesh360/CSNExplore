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
// Columns: name, type, price_per_day, rating, badge
$bikes = [
    // Basic Bikes / Scooters
    ['Hero Splendor',        'Commuter',    800,  4.3, null],
    ['Honda Dio',            'Scooter',     940,  4.4, null],
    ['Yamaha Ray',           'Scooter',     1008, 4.3, null],
    ['Honda Activa',         'Scooter',     1067, 4.6, 'Top Rated'],
    ['Honda Activa 6G',      'Scooter',     1100, 4.7, 'Top Rated'],
    ['TVS Jupiter',          'Scooter',     1000, 4.4, null],
    ['Suzuki Access 125',    'Scooter',     1050, 4.3, null],
    ['Honda Grazia',         'Scooter',     1050, 4.2, null],
    // Mid-range Bikes
    ['Honda X-Blade',        'Street Bike', 1128, 4.4, null],
    ['Honda Shine 125cc',    'Commuter',    1104, 4.3, null],
    ['Bajaj Pulsar',         'Sports Bike', 1200, 4.5, 'Popular'],
    // Premium Bikes
    ['Royal Enfield Classic 350', 'Cruiser',      2024, 4.8, 'Premium'],
    ['Royal Enfield Himalayan',   'Adventure',    2500, 4.7, 'Premium'],
    ['Bajaj Avenger',             'Cruiser',      1500, 4.5, null],
    ['Yamaha R15',                'Sports Bike',  1800, 4.6, 'Premium'],
];

$image    = 'images/uploads/hotel-renaissance.jpg';
$location = 'Chhatrapati Sambhajinagar';

echo "\nSeeding bikes...\n";
foreach ($bikes as $i => $b) {
    $db->insert('bikes', [
        'name'             => $b[0],
        'type'             => $b[1],
        'location'         => $location,
        'price_per_day'    => $b[2],
        'rating'           => $b[3],
        'badge'            => $b[4],
        'fuel_type'        => 'Petrol',
        'image'            => $image,
        'description'      => $b[0] . ' available for self-ride in Chhatrapati Sambhajinagar. Well-maintained and ready to ride.',
        'features'         => json_encode(['Helmet Included', 'Insurance', 'Full Tank']),
        'is_active'        => 1,
        'driver_available' => 0,
        'display_order'    => $i,
    ]);
    echo "  Bike added: {$b[0]}\n";
}

echo "\n✅ Seed complete!\n";
echo "  Admins processed: " . count($admins) . "\n";
echo "  Bikes seeded: " . count($bikes) . "\n";
