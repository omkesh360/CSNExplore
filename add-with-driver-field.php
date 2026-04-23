<?php
// Migration: Add with_driver field to bookings table
require_once __DIR__ . '/php/config.php';

try {
    $db = getDB();
    
    // Check if column already exists
    $result = $db->query("SHOW COLUMNS FROM bookings LIKE 'with_driver'");
    if ($result && $result->rowCount() > 0) {
        echo "Column 'with_driver' already exists in bookings table.\n";
        exit(0);
    }
    
    // Add the column
    $db->query("ALTER TABLE bookings ADD COLUMN with_driver TINYINT(1) DEFAULT 0 AFTER listing_name");
    echo "✓ Successfully added 'with_driver' column to bookings table\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
