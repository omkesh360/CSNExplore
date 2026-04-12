<?php
// Add map_embed column to all listing tables if not exists
require_once __DIR__ . '/../config.php';

$db = getDB();

$tables = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];

foreach ($tables as $table) {
    try {
        // Check if column exists
        $result = $db->fetchOne(
            "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
             WHERE TABLE_NAME = ? AND COLUMN_NAME = 'map_embed' AND TABLE_SCHEMA = DATABASE()",
            [$table]
        );
        
        if (!$result) {
            // Add column if it doesn't exist
            $db->execute("ALTER TABLE `$table` ADD COLUMN `map_embed` LONGTEXT NULL");
            echo "✓ Added map_embed column to $table table\n";
        } else {
            echo "✓ map_embed column already exists in $table table\n";
        }
    } catch (Exception $e) {
        echo "✗ Error with $table: " . $e->getMessage() . "\n";
    }
}

echo "\nAll tables updated successfully!";
?>
