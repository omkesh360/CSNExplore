<?php
require_once __DIR__ . '/../config.php';
$db = getDB();
try {
    // 1. Delete all users
    $db->query("DELETE FROM users");
    echo "Success: Deleted all users.\n";

    // 2. Delete all blogs except the first one
    $db->query("DELETE FROM blogs WHERE id NOT IN (SELECT id FROM (SELECT id FROM blogs ORDER BY id ASC LIMIT 1) as t)");
    echo "Success: Cleaned up blogs table.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
