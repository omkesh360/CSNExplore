<?php
require 'php/config.php';
$db = getDB();

$sql = "CREATE TABLE IF NOT EXISTS trip_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    interests TEXT DEFAULT NULL,
    stay_type VARCHAR(50) DEFAULT NULL,
    travel_mode VARCHAR(50) DEFAULT NULL,
    travel_details TEXT DEFAULT NULL,
    extra_notes TEXT DEFAULT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

try {
    $db->query($sql);
    echo "Table 'trip_requests' created or already exists.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
