<?php
require_once __DIR__ . '/php/config.php';
$db = getDB();
$section = 'contact';
$row = $db->fetchOne("SELECT content FROM about_contact WHERE section = ?", [$section]);
echo $row['content'];
