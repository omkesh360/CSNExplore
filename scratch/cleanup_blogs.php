<?php
require_once 'php/config.php';
$db = getDB();
try {
    $db->execute("DELETE FROM blogs WHERE id NOT IN (SELECT id FROM (SELECT id FROM blogs ORDER BY id ASC LIMIT 1) as t)");
    echo "Success: Cleaned up blogs table.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
