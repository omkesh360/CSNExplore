<?php
require 'c:\xampp\htdocs\CSNexplore\CSNExplore\php\config.php';
$db = Database::getInstance();
$user = $db->fetchOne("SELECT * FROM users WHERE username = 'csnexploreomkeshadmin'");
if ($user) {
    echo 'omkesh: ' . (password_verify('omkeshAa.1@', $user['password_hash']) ? 'YES' : 'NO') . "\n";
} else {
    echo "omkesh not found\n";
}
$user2 = $db->fetchOne("SELECT * FROM users WHERE username = 'csnexplorerupeshadmin'");
if ($user2) {
    echo 'rupesh: ' . (password_verify('rupeshAa.1@', $user2['password_hash']) ? 'YES' : 'NO') . "\n";
} else {
    echo "rupesh not found\n";
}
