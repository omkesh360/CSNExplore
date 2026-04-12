<?php
// Add default map embed to all listings
require_once __DIR__ . '/../config.php';

$default_map = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d16426.329596104577!2d75.30037121099188!3d19.851617685624074!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bdb98f39ca15447%3A0x96c2632e2aaa42c!2sKamalnayan%20Bajaj%20Hospital!5e0!3m2!1sen!2sin!4v1775290483319!5m2!1sen!2sin" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';

$db = getDB();
$tables = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];

foreach ($tables as $table) {
    try {
        // Update all listings that don't have a map embed
        $db->execute(
            "UPDATE $table SET map_embed = ? WHERE map_embed IS NULL OR map_embed = ''",
            [$default_map]
        );
        
        $count = $db->fetchOne("SELECT COUNT(*) as cnt FROM $table WHERE map_embed = ?", [$default_map]);
        echo "✓ Updated $table - " . ($count['cnt'] ?? 0) . " listings\n";
    } catch (Exception $e) {
        echo "✗ Error with $table: " . $e->getMessage() . "\n";
    }
}

echo "\nDefault maps added to all listings!\n";
?>
