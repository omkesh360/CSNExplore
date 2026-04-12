<?php
require 'php/config.php';
$db = getDB();
echo "DB: OK\n";

$admin = $db->fetchOne('SELECT id, role, is_verified FROM users WHERE email = ?', ['admin@csnexplore.com']);
echo "Admin: " . json_encode($admin) . "\n";

foreach (['stays','cars','bikes','restaurants','attractions','buses','bookings','users','blogs'] as $t) {
    try {
        $r = $db->fetchOne("SELECT COUNT(*) as c FROM $t");
        echo "$t: " . $r['c'] . " rows\n";
    } catch (Exception $e) {
        echo "$t: ERROR - " . $e->getMessage() . "\n";
    }
}

// Check key columns
echo "\n-- Checking key columns --\n";
$checks = [
    ['stays', 'map_embed'], ['stays', 'display_order'], ['stays', 'gallery'],
    ['cars', 'map_embed'], ['cars', 'display_order'], ['cars', 'driver_available'],
    ['bikes', 'map_embed'], ['bikes', 'display_order'],
    ['bookings', 'updated_at'], ['bookings', 'listing_name'],
    ['users', 'username'],
];
foreach ($checks as [$t, $col]) {
    $c = $db->fetchAll("SHOW COLUMNS FROM $t LIKE '$col'");
    echo "$t.$col: " . (count($c) > 0 ? "EXISTS" : "MISSING") . "\n";
}

// Check uploads dir
echo "\n-- Upload dir --\n";
$up = __DIR__ . '/images/uploads/';
echo "exists: " . (is_dir($up) ? 'yes' : 'NO') . "\n";
echo "writable: " . (is_writable($up) ? 'yes' : 'NO') . "\n";

// Check listing-detail dir
$ld = __DIR__ . '/listing-detail/';
echo "listing-detail/: " . (is_dir($ld) ? 'yes' : 'NO') . "\n";
$htmlFiles = glob($ld . '*.html');
echo "Static HTML files: " . count($htmlFiles) . "\n";

echo "\nDone.\n";
?>
