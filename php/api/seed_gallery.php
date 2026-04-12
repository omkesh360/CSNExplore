<?php
/**
 * Seed Gallery Images for All Listings
 * Adds 6 curated Unsplash images into the gallery column for every listing.
 * Safe to run multiple times — skips listings that already have gallery data.
 * Run: php php/api/seed_gallery.php
 */
chdir(dirname(__DIR__, 2)); // project root
require_once __DIR__ . '/../config.php';

$db = getDB()->getConnection();

// ── Curated image pools per type ──────────────────────────────────────────────
$pools = [
    'stays' => [
        'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=900&q=85',
        'https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=900&q=85',
        'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=900&q=85',
        'https://images.unsplash.com/photo-1560185007-c5ca9d2c014d?w=900&q=85',
        'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=900&q=85',
        'https://images.unsplash.com/photo-1606402179428-a57976d71fa4?w=900&q=85',
        'https://images.unsplash.com/photo-1615460549969-36fa19521a4f?w=900&q=85',
        'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=900&q=85',
        'https://images.unsplash.com/photo-1582268611958-ebfd161ef9cf?w=900&q=85',
        'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=900&q=85',
    ],
    'cars' => [
        'https://images.unsplash.com/photo-1605559424843-9073c6223a36?w=900&q=85',
        'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=900&q=85',
        'https://images.unsplash.com/photo-1583121274602-3e2820c69888?w=900&q=85',
        'https://images.unsplash.com/photo-1594596402701-e04e81ffc31f?w=900&q=85',
        'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=900&q=85',
        'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=900&q=85',
        'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?w=900&q=85',
        'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=900&q=85',
        'https://images.unsplash.com/photo-1617788138017-80ad40651399?w=900&q=85',
        'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=900&q=85',
    ],
    'bikes' => [
        'https://images.unsplash.com/photo-1558981806-ec527fa84c39?w=900&q=85',
        'https://images.unsplash.com/photo-1558981285-6f0c68a4b335?w=900&q=85',
        'https://images.unsplash.com/photo-1591637333184-19aa84b3e01f?w=900&q=85',
        'https://images.unsplash.com/photo-1609630875171-b1321377ee65?w=900&q=85',
        'https://images.unsplash.com/photo-1449426468159-d96dbf08f19f?w=900&q=85',
        'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?w=900&q=85',
        'https://images.unsplash.com/photo-1547549082-4bc09bb2571b?w=900&q=85',
        'https://images.unsplash.com/photo-1580310614729-ccd69652491d?w=900&q=85',
        'https://images.unsplash.com/photo-1615172282427-9de3e2c49e32?w=900&q=85',
        'https://images.unsplash.com/photo-1598972691155-53e8b1082f9c?w=900&q=85',
    ],
    'attractions' => [
        'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=900&q=85',
        'https://images.unsplash.com/photo-1526711657229-e7e080961f54?w=900&q=85',
        'https://images.unsplash.com/photo-1477959858617-67f85cf4f1df?w=900&q=85',
        'https://images.unsplash.com/photo-1583422409516-2895a77efded?w=900&q=85',
        'https://images.unsplash.com/photo-1548013146-72479768bada?w=900&q=85',
        'https://images.unsplash.com/photo-1592477725143-2e57dc728f0a?w=900&q=85',
        'https://images.unsplash.com/photo-1469474968028-56623f02e42e?w=900&q=85',
        'https://images.unsplash.com/photo-1501594907352-04cda38ebc29?w=900&q=85',
        'https://images.unsplash.com/photo-1533929736458-ca588d08c8be?w=900&q=85',
        'https://images.unsplash.com/photo-1518391846015-55a9cc003b25?w=900&q=85',
    ],
    'restaurants' => [
        'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=900&q=85',
        'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=900&q=85',
        'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=900&q=85',
        'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=900&q=85',
        'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?w=900&q=85',
        'https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=900&q=85',
        'https://images.unsplash.com/photo-1476224203421-9ac39bcb3df1?w=900&q=85',
        'https://images.unsplash.com/photo-1484723091739-30a097e8f929?w=900&q=85',
        'https://images.unsplash.com/photo-1559339352-11d035aa65ce?w=900&q=85',
        'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=900&q=85',
    ],
    'buses' => [
        'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=900&q=85',
        'https://images.unsplash.com/photo-1464219789935-c2d9d9aba644?w=900&q=85',
        'https://images.unsplash.com/photo-1570125909232-eb263c188f7e?w=900&q=85',
        'https://images.unsplash.com/photo-1494515843206-f3117d3f51b7?w=900&q=85',
        'https://images.unsplash.com/photo-1531817683145-47af1e2c8ceb?w=900&q=85',
        'https://images.unsplash.com/photo-1501785888041-af3ef285b470?w=900&q=85',
        'https://images.unsplash.com/photo-1473445730015-841f29a9490b?w=900&q=85',
        'https://images.unsplash.com/photo-1426122402199-be02db90eb90?w=900&q=85',
        'https://images.unsplash.com/photo-1569521783705-4c7e85dc86bb?w=900&q=85',
        'https://images.unsplash.com/photo-1535132011086-b8818f016104?w=900&q=85',
    ],
];

// Tables that have a gallery column
$tablesWithGallery = ['stays', 'cars', 'bikes', 'attractions', 'restaurants'];
// buses has no gallery column — add it if missing
try {
    $db->exec("ALTER TABLE buses ADD COLUMN gallery TEXT");
    echo "Added gallery column to buses table.\n";
} catch (Exception $e) {
    echo "buses.gallery column already exists.\n";
}
$tablesWithGallery[] = 'buses';

$updated = 0;
$skipped = 0;

foreach ($tablesWithGallery as $table) {
    $pool = $pools[$table] ?? [];
    if (empty($pool)) continue;

    $idCol = 'id';
    $rows = $db->query("SELECT id, gallery FROM $table")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
        // Skip if already has gallery data
        $existing = json_decode($row['gallery'] ?? '', true);
        if (!empty($existing)) {
            $skipped++;
            continue;
        }

        // Pick 6 images offset by ID for variety
        $poolSize = count($pool);
        $offset   = (int)$row['id'] % $poolSize;
        $images   = [];
        for ($i = 0; $i < 6; $i++) {
            $images[] = $pool[($offset + $i) % $poolSize];
        }

        $json = json_encode($images);
        $stmt = $db->prepare("UPDATE $table SET gallery = ? WHERE id = ?");
        $stmt->execute([$json, $row['id']]);
        $updated++;
    }
}

echo "Done! Updated: $updated listings, Skipped (already had gallery): $skipped listings.\n";
