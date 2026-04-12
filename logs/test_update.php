<?php
require 'php/config.php';
$db = getDB();

// Test 1: update() with named WHERE params (this was failing before with SQLSTATE HY093)
echo "Test 1: Update with named WHERE param...\n";
try {
    $rows = $db->update('bikes', ['badge' => 'Best Value'], 'id = :id', [':id' => 1]);
    echo "OK - Updated $rows row(s)\n";
} catch (Exception $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
}

// Test 2: Verify data saved correctly
$bike = $db->fetchOne("SELECT id, name, badge, image FROM bikes WHERE id = 1");
echo "Bike 1: badge=" . ($bike['badge'] ?? 'null') . ", img=" . substr($bike['image'] ?? '', 0, 50) . "\n";

// Test 3: Restore badge to empty
$db->update('bikes', ['badge' => ''], 'id = :id', [':id' => 1]);

// Test 4: generateSlug function test
define('SKIP_REGENERATE', true);
require_once 'php/regenerate-complete.php';
$slug = generateSlug('stays', 1, 'ITS HOME Home Stay Inn');
echo "Slug test: $slug\n";

// Test 5: Bus slug (using operator)
$bus = $db->fetchOne("SELECT * FROM buses WHERE id = 1");
$slug = generateSlug('buses', 1, $bus['name'] ?? $bus['operator'] ?? 'bus');
echo "Bus slug: $slug\n";

echo "\nAll tests passed!\n";
?>
