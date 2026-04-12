<?php
require 'php/config.php';
$db = getDB();

// Test insert
$data = [
    'full_name' => 'John Wick',
    'phone' => '+1234567890',
    'interests' => 'Heritage | Food (Mughlai)',
    'stay_type' => 'Luxury',
    'travel_mode' => 'Car',
    'travel_details' => 'Service: WithDriver | Type: SUV',
    'extra_notes' => 'Need my own weapons'
];

$cols = implode(', ', array_keys($data));
$vals = implode(', ', array_fill(0, count($data), '?'));

$db->query("INSERT INTO trip_requests ($cols) VALUES ($vals)", array_values($data));
echo "Inserted test request\n";
