<?php
require 'php/config.php';
$db = getDB();
$bikes = $db->fetchAll('SELECT id, name, image FROM bikes ORDER BY id');
foreach ($bikes as $b) {
    echo $b['id'] . ' | ' . substr($b['name'], 0, 35) . ' | ' . substr($b['image'] ?? 'NULL', 0, 100) . "\n";
}
?>
