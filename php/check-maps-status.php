<?php
// Check if map_embed columns exist and have data
require_once __DIR__ . '/config.php';

$db = getDB();
$categories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];

echo "=== Map Embed Status Check ===\n\n";

foreach ($categories as $category) {
    echo "Checking $category table...\n";
    
    // Check if column exists
    $result = $db->fetchOne(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE TABLE_NAME = ? AND COLUMN_NAME = 'map_embed' AND TABLE_SCHEMA = DATABASE()",
        [$category]
    );
    
    if ($result) {
        echo "  ✓ map_embed column exists\n";
        
        // Count listings with maps
        $with_maps = $db->fetchOne(
            "SELECT COUNT(*) as count FROM $category WHERE map_embed IS NOT NULL AND map_embed != ''",
            []
        );
        
        $total = $db->fetchOne(
            "SELECT COUNT(*) as count FROM $category WHERE is_active = 1",
            []
        );
        
        echo "  • Listings with maps: " . ($with_maps['count'] ?? 0) . "\n";
        echo "  • Total active listings: " . ($total['count'] ?? 0) . "\n";
    } else {
        echo "  ✗ map_embed column MISSING - needs to be added\n";
    }
    
    echo "\n";
}

echo "=== Summary ===\n";
echo "If any table shows 'column MISSING', run the regeneration script.\n";
echo "If columns exist but 'Listings with maps' is 0, regenerate all pages.\n";
?>
