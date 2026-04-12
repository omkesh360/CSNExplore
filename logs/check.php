<?php
require 'php/config.php';
$db = Database::getInstance();
$items = $db->fetchAll('SELECT * FROM trip_requests');
print_r($items);
