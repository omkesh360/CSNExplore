#!/usr/bin/env php
<?php
/**
 * Password Reset Utility for CSNExplore
 * Resets the admin password to a new value
 * 
 * Usage: php php/reset_password.php
 */

require_once __DIR__ . '/config.php';

$newPassword = 'admin123';

echo "Reading users file...\n";

$users = readJsonFile('users.json');

if (empty($users)) {
    echo "Error: No users found!\n";
    exit(1);
}

$adminUser = null;
$adminIndex = -1;

foreach ($users as $index => $user) {
    if (isset($user['role']) && $user['role'] === 'Admin') {
        $adminUser = $user;
        $adminIndex = $index;
        break;
    }
}

if ($adminUser) {
    echo "Found Admin user: {$adminUser['email']}\n";
    
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    $users[$adminIndex]['password'] = $hashedPassword;
    
    if (writeJsonFile('users.json', $users)) {
        echo "✓ Password updated successfully for Admin user.\n";
        echo "  New password: $newPassword\n";
        echo "  Email: {$adminUser['email']}\n";
    } else {
        echo "✗ Failed to write users file!\n";
        exit(1);
    }
} else {
    echo "✗ Admin user not found!\n";
    exit(1);
}
