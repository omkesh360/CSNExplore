<?php
require_once 'php/config.php';
$db = getDB();
$types = ['stays','cars','bikes','attractions','restaurants','buses'];
foreach($types as $t){
    $count = $db->fetchOne("SELECT count(*) as c FROM $t WHERE is_active=1")['c'];
    echo "$t: $count\n";
}
