<?php
require_once __DIR__ . '/php/database.php';

try {
    $db = Database::getInstance();
    
    $email = 'admin@travelhub.com';
    $password = 'admin123';
    
    // Hash password with PHP's default bcrypted hash
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // Check if user exists
    $user = $db->fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
    if ($user) {
        $db->update('users', [
            'password_hash' => $passwordHash,
            'role' => 'admin',
            'is_verified' => 1
        ], "email = ?", [$email]);
        echo "Successfully updated admin user in travelhub.db\n";
    } else {
        $db->insert('users', [
            'email' => $email,
            'password_hash' => $passwordHash,
            'name' => 'Admin User',
            'role' => 'admin',
            'is_verified' => 1
        ]);
        echo "Successfully inserted admin user into travelhub.db\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
