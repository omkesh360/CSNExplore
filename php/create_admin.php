#!/usr/bin/env php
<?php
/**
 * Create Admin User Utility for CSNExplore
 * Creates a new admin user account
 * 
 * Usage: php php/create_admin.php
 */

require_once __DIR__ . '/config.php';

echo "===========================================\n";
echo "CSNExplore - Create Admin User\n";
echo "===========================================\n\n";

// Get user input
echo "Enter admin name: ";
$name = trim(fgets(STDIN));

echo "Enter admin email: ";
$email = trim(fgets(STDIN));

echo "Enter admin password: ";
$password = trim(fgets(STDIN));

// Validate input
if (empty($name) || empty($email) || empty($password)) {
    echo "\n✗ Error: All fields are required!\n";
    exit(1);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "\n✗ Error: Invalid email format!\n";
    exit(1);
}

if (strlen($password) < 6) {
    echo "\n✗ Error: Password must be at least 6 characters!\n";
    exit(1);
}

// Load existing users
$users = readJsonFile('users.json');

// Check if email already exists
foreach ($users as $user) {
    if (strtolower($user['email']) === strtolower($email)) {
        echo "\n✗ Error: An account with this email already exists!\n";
        exit(1);
    }
}

// Create new admin user
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

$newAdmin = [
    'id' => (string)(time() * 1000),
    'name' => $name,
    'email' => $email,
    'role' => 'Admin',
    'password' => $hashedPassword,
    'createdAt' => date('c')
];

$users[] = $newAdmin;

// Save to file
if (writeJsonFile('users.json', $users)) {
    echo "\n✓ Admin user created successfully!\n";
    echo "  Name: $name\n";
    echo "  Email: $email\n";
    echo "  Role: Admin\n";
    echo "\nYou can now login with these credentials.\n";
} else {
    echo "\n✗ Error: Failed to save user data!\n";
    exit(1);
}
