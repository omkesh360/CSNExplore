<?php
// Add map_embed column to all listing tables
require_once __DIR__ . '/../config.php';

$db = getDB();
$tables = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];

foreach ($tables as $table) {
    try {
        // Check if column already exists
        $result = $db->fetchOne("SHOW COLUMNS FROM $table LIKE 'map_embed'");
        
        if (!$result) {
            // Add the column
            $db->execute("ALTER TABLE $table ADD COLUMN map_embed LONGTEXT NULL AFTER location");
            echo "✓ Added map_embed column to $table\n";
        } else {
            echo "✓ map_embed column already exists in $table\n";
        }
    } catch (Exception $e) {
        echo "✗ Error with $table: " . $e->getMessage() . "\n";
    }
}

echo "\nMigration complete!\n";
?>
