<?php
require_once __DIR__ . '/config.php';

$db = getDB();
$tables = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];

foreach ($tables as $table) {
    try {
        $result = $db->fetchOne("SHOW COLUMNS FROM $table LIKE 'map_embed'");
        if ($result) {
            echo "✓ $table has map_embed column\n";
        } else {
            echo "✗ $table missing map_embed column - adding...\n";
            $db->execute("ALTER TABLE $table ADD COLUMN map_embed LONGTEXT NULL AFTER location");
            echo "✓ Added map_embed to $table\n";
        }
    } catch (Exception $e) {
        echo "✗ Error with $table: " . $e->getMessage() . "\n";
    }
}

echo "\nDone!\n";
?>
