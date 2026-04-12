<?php
require 'php/config.php';
$db = Database::getInstance();
$items = $db->fetchAll('DESCRIBE trip_requests');
print_r($items);
