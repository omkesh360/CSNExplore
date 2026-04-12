<?php
require_once __DIR__ . '/config.php';
$db = getDB();

echo "=== Testing All Admin APIs ===\n\n";

$apis = [
    'dashboard'    => __DIR__ . '/api/dashboard.php',
    'listings'     => __DIR__ . '/api/listings.php',
    'bookings'     => __DIR__ . '/api/bookings.php',
    'blogs'        => __DIR__ . '/api/blogs.php',
    'gallery'      => __DIR__ . '/api/gallery.php',
    'vendors'      => __DIR__ . '/api/vendors.php',
    'users'        => __DIR__ . '/api/users.php',
    'update-map'   => __DIR__ . '/api/update-map-embed.php',
];

foreach ($apis as $name => $path) {
    if (!file_exists($path)) {
        echo "  $name: MISSING FILE\n";
        continue;
    }
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_GET = ['category' => 'stays'];
    ob_start();
    try {
        include $path;
    } catch (Exception $e) {
        echo "  $name: EXCEPTION - " . $e->getMessage() . "\n";
        ob_end_clean();
        continue;
    }
    $out = ob_get_clean();
    $data = json_decode($out, true);
    if ($data === null && !empty($out)) {
        echo "  $name: NON-JSON output - " . substr(strip_tags($out), 0, 100) . "\n";
    } elseif (isset($data['error'])) {
        echo "  $name: API ERROR - " . $data['error'] . "\n";
    } else {
        echo "  $name: OK\n";
    }
}

echo "\n=== Listings API Full Test ===\n";
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET = ['category' => 'stays'];
ob_start();
include __DIR__ . '/api/listings.php';
$out = ob_get_clean();
$data = json_decode($out, true);
echo "Stays listings: " . (isset($data['success']) ? count($data['listings'] ?? []) . " items" : "ERROR: " . substr($out, 0, 200)) . "\n";

echo "\n=== map_embed in listings API response ===\n";
if (isset($data['listings'][0])) {
    $keys = array_keys($data['listings'][0]);
    echo "Fields returned: " . implode(', ', $keys) . "\n";
    echo "map_embed present: " . (in_array('map_embed', $keys) ? 'YES' : 'NO') . "\n";
}
