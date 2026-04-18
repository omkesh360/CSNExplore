<?php
require_once __DIR__ . '/../php/config.php';
$db = getDB();
echo "Searching for Gravity in all tables...\n";
$categories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];
foreach ($categories as $cat) {
    $name_col = ($cat === 'buses') ? 'operator' : 'name';
    $res = $db->fetchAll("SELECT id, $name_col as name FROM $cat WHERE $name_col LIKE '%Gravity%'");
    foreach ($res as $r) {
        echo "Table: $cat | ID: {$r['id']} | Name: {$r['name']}\n";
    }
}
