<?php
require 'php/config.php';
$db = getDB();
$cols = $db->getConnection()->query('DESCRIBE cars')->fetchAll(PDO::FETCH_ASSOC);
print_r($cols);
