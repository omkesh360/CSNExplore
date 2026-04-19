<?php
require_once 'php/config.php';
$db = getDB();
$res = $db->fetchAll("SELECT id, name, is_active FROM stays");
print_r($res);
