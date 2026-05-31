<?php
require 'c:/xampp/htdocs/CSNExplore/php/config.php';
$db = getDB();
$blogs = $db->fetchAll('SELECT id, title, status FROM blogs');
echo count($blogs) . " total blogs.\n";
foreach($blogs as $b) {
    echo $b['id'] . " - " . $b['title'] . " (" . $b['status'] . ")\n";
}
