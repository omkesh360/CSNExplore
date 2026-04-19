<?php
require 'c:\xampp\htdocs\CSNexplore\CSNExplore\php\config.php';
$db = Database::getInstance();

try {
    $db->query("ALTER TABLE users ADD COLUMN username VARCHAR(100) NULL UNIQUE");
    echo "Added username column.\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Column already exists.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

$admins = [
    [
        'username' => 'csnexploreomkeshadmin',
        'password' => 'omkeshAa.1@'
    ],
    [
        'username' => 'csnexplorerupeshadmin',
        'password' => 'rupeshAa.1@'
    ]
];

foreach ($admins as $admin) {
    $exists = $db->fetchOne("SELECT * FROM users WHERE email = ? OR username = ?", [$admin['username'] . '@csnexplore.com', $admin['username']]);
    $hash = password_hash($admin['password'], PASSWORD_DEFAULT);
    if ($exists) {
        $db->update('users', ['password_hash' => $hash, 'username' => $admin['username']], 'id = :id', [':id' => $exists['id']]);
        echo "Updated password for " . $admin['username'] . "\n";
    } else {
        $db->insert('users', [
            'username' => $admin['username'],
            'email' => $admin['username'] . '@csnexplore.com',
            'password_hash' => $hash,
            'name' => $admin['username'],
            'role' => 'admin',
            'is_verified' => 1
        ]);
        echo "Created user " . $admin['username'] . "\n";
    }
}
echo "Done";
