<?php
require 'c:\xampp\htdocs\CSNexplore\CSNExplore\php\config.php';
$_SERVER['REQUEST_METHOD'] = 'POST';
$_GET['action'] = 'login';
// Force auth
function getAuthToken() { return "fake"; }
function verifyJWT($t, $s) { return ['role'=>'admin']; }

try {
    require 'c:\xampp\htdocs\CSNexplore\CSNExplore\php\api\run-regenerate.php';
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage();
}
