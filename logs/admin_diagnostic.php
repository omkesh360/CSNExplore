<?php
/**
 * Comprehensive Admin Panel Diagnostic
 * Tests all major admin features and reports issues
 */
require_once __DIR__ . '/../php/config.php';
$db = getDB();

$pass = 0; $fail = 0; $issues = [];

function check($label, $result, &$pass, &$fail, &$issues) {
    if ($result === true) { echo "✅ $label\n"; $pass++; }
    else { echo "❌ $label: $result\n"; $fail++; $issues[] = "$label: $result"; }
}

echo "=== CSNExplore Admin Diagnostic ===\n\n";

// 1. AUTH
echo "--- AUTH ---\n";
$admin = $db->fetchOne("SELECT id, email, role, is_verified FROM users WHERE email = ?", ['admin@csnexplore.com']);
check("Admin user exists", $admin ? true : "Admin not found in DB");
check("Admin role correct", ($admin['role'] ?? '') === 'admin' ? true : "Role is: " . ($admin['role'] ?? 'null'));
check("Admin verified", ($admin['is_verified'] ?? 0) == 1 ? true : "is_verified = 0");

// 2. LISTINGS tables
echo "\n--- LISTINGS ---\n";
$tables = ['stays','cars','bikes','restaurants','attractions','buses'];
foreach ($tables as $t) {
    $rows = $db->fetchAll("SELECT COUNT(*) as c, SUM(is_active) as active FROM $t");
    $c = $rows[0]['c'] ?? 0;
    $a = $rows[0]['active'] ?? 0;
    check("$t table ({$a}/{$c} active)", $c > 0 ? true : "Table empty");
    
    // Check image column
    $cols = $db->fetchAll("SHOW COLUMNS FROM $t LIKE 'image'");
    check("$t.image column exists", count($cols) > 0 ? true : "Missing image column");
    
    // Check map_embed column
    $mc = $db->fetchAll("SHOW COLUMNS FROM $t LIKE 'map_embed'");
    check("$t.map_embed column exists", count($mc) > 0 ? true : "Missing map_embed column");
    
    // Check display_order
    $do = $db->fetchAll("SHOW COLUMNS FROM $t LIKE 'display_order'");
    check("$t.display_order column exists", count($do) > 0 ? true : "Missing display_order column");
}

// 3. BOOKINGS
echo "\n--- BOOKINGS ---\n";
$bookingCols = ['id','full_name','phone','email','status','service_type','listing_id','listing_name','booking_date','checkin_date','checkout_date','notes','created_at','updated_at'];
foreach ($bookingCols as $col) {
    $c = $db->fetchAll("SHOW COLUMNS FROM bookings LIKE '$col'");
    check("bookings.$col exists", count($c) > 0 ? true : "Missing column");
}
$bookingCount = $db->fetchOne("SELECT COUNT(*) as c FROM bookings");
echo "ℹ️  Total bookings: " . ($bookingCount['c'] ?? 0) . "\n";

// 4. USERS
echo "\n--- USERS ---\n";
$userCols = ['id','email','password_hash','name','role','is_verified','created_at'];
foreach ($userCols as $col) {
    $c = $db->fetchAll("SHOW COLUMNS FROM users LIKE '$col'");
    check("users.$col exists", count($c) > 0 ? true : "Missing column");
}

// 5. BLOGS
echo "\n--- BLOGS ---\n";
$blogCount = $db->fetchOne("SELECT COUNT(*) as c FROM blogs");
check("blogs table accessible", $blogCount !== false ? true : "Cannot query blogs");
echo "ℹ️  Total blogs: " . ($blogCount['c'] ?? 0) . "\n";

// 6. GALLERY
echo "\n--- GALLERY ---\n";
try {
    $gallery = $db->fetchAll("SELECT COUNT(*) as c FROM gallery");
    check("gallery table accessible", true);
    echo "ℹ️  Gallery items: " . ($gallery[0]['c'] ?? 0) . "\n";
} catch (Exception $e) {
    check("gallery table accessible", "Error: " . $e->getMessage());
}

// 7. SUBSCRIBERS
echo "\n--- SUBSCRIBERS ---\n";
try {
    $subs = $db->fetchOne("SELECT COUNT(*) as c FROM subscribers");
    check("subscribers table accessible", true);
    echo "ℹ️  Subscribers: " . ($subs['c'] ?? 0) . "\n";
} catch (Exception $e) {
    check("subscribers table accessible", "Error: " . $e->getMessage());
}

// 8. UPDATE method test (no actual write)
echo "\n--- DATABASE update() method ---\n";
$testSql = "UPDATE stays SET updated_at = updated_at WHERE id = :id AND 1 = 0"; // Affects 0 rows
try {
    $stmt = $db->query($testSql, [':id' => 1]);
    check("Named params in WHERE work", true);
} catch (Exception $e) {
    check("Named params in WHERE work", $e->getMessage());
}

// 9. listing-detail directory
echo "\n--- LISTING-DETAIL PAGES ---\n";
$detailDir = __DIR__ . '/../listing-detail/';
check("listing-detail/ dir exists", is_dir($detailDir) ? true : "Directory missing");
if (is_dir($detailDir)) {
    $files = glob($detailDir . '*.html');
    echo "ℹ️  Static HTML files: " . count($files) . "\n";
}

// 10. Upload directory
echo "\n--- UPLOADS ---\n";
$uploadDir = __DIR__ . '/../images/uploads/';
check("images/uploads/ dir exists", is_dir($uploadDir) ? true : "Directory missing");
check("images/uploads/ is writable", is_writable($uploadDir) ? true : "Not writable – uploads will fail");

echo "\n=== SUMMARY ===\n";
echo "✅ Passed: $pass\n";
echo "❌ Failed: $fail\n";
if ($issues) {
    echo "\nIssues to fix:\n";
    foreach ($issues as $i) echo "  - $i\n";
}
?>
