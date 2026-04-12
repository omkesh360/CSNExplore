<?php
// Populate all listings with default map embed
require_once __DIR__ . '/config.php';

$default_map = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d16426.329596104577!2d75.30037121099188!3d19.851617685624074!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bdb98f39ca15447%3A0x96c2632e2aaa42c!2sKamalnayan%20Bajaj%20Hospital!5e0!3m2!1sen!2sin!4v1775290483319!5m2!1sen!2sin" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';

$db = getDB();
$tables = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];

echo "Populating default maps...\n\n";

foreach ($tables as $table) {
    try {
        // First check if column exists
        $col_check = $db->fetchOne("SHOW COLUMNS FROM $table LIKE 'map_embed'");
        if (!$col_check) {
            echo "Adding map_embed column to $table...\n";
            $db->execute("ALTER TABLE $table ADD COLUMN map_embed LONGTEXT NULL AFTER location");
        }
        
        // Update all listings with default map
        $db->execute(
            "UPDATE $table SET map_embed = ? WHERE map_embed IS NULL OR map_embed = ''",
            [$default_map]
        );
        
        $count = $db->fetchOne("SELECT COUNT(*) as cnt FROM $table");
        echo "✓ $table - " . ($count['cnt'] ?? 0) . " listings updated\n";
    } catch (Exception $e) {
        echo "✗ Error with $table: " . $e->getMessage() . "\n";
    }
}

echo "\n✓ All listings now have default maps!\n";
?>
