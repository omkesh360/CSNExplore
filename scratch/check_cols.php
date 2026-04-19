<?php
require_once 'php/config.php';
$db = getDB();
$res = $db->fetchAll("DESCRIBE stays");
foreach($res as $row) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
