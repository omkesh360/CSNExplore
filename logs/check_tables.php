<?php
require 'php/config.php';
$db = getDB();
print_r($db->fetchAll('SHOW TABLES'));
