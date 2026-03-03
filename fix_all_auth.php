<?php
require_once __DIR__ . '/php/database.php';
$db = Database::getInstance();
$hash = '$2b$10$IA2E1S7XN11HIL/en.difOwS71rMkb6MIbTRPV.NQ3w5HXuIuyi9u';
$db->update('users', ['password_hash' => $hash, 'role' => 'admin'], "email = 'admin@travelhub.com'");
echo "Fixed sqlite travelhub.db\n";
