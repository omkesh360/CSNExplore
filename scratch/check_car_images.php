<?php
require_once __DIR__ . '/../php/config.php';
$db = getDB();
$res = $db->fetchAll('SELECT id, name, image FROM cars');
foreach($res as $r) {
    echo "ID: {$r['id']} | Name: {$r['name']} | Image: {$r['image']}\n";
}
