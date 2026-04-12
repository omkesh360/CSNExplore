<?php
require_once __DIR__ . '/php/config.php';
$db = getDB();

// Update Contact section
$row = $db->fetchOne("SELECT content FROM about_contact WHERE section = 'contact'");
$contact = $row ? json_decode($row['content'], true) : [];
$contact['address'] = "Jay Tower, Konkanwadi, Samadhan Colony, Padampura, Chhatrapati Sambhajinagar, Maharashtra 431005";
$db->update('about_contact', ['content' => json_encode($contact)], 'section = :s', [':s' => 'contact']);

echo "Database updated successfully.\n";
