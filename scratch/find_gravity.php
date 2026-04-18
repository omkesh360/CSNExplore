<?php
require_once __DIR__ . '/../php/config.php';
$db = getDB();
$item = $db->fetchOne("SELECT * FROM stays WHERE name LIKE '%Gravity%'");
print_r($item);
