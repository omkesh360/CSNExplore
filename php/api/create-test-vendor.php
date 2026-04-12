<?php
// Helper script to create a test vendor for testing vendor login
// Access this once: http://yoursite.com/php/api/create-test-vendor.php

header('Content-Type: application/json');
require_once '../config.php';

try {
    $db = getDB()->getConnection();
    
    // Check if test vendor already exists
    $stmt = $db->prepare("SELECT id FROM vendors WHERE username = ?");
    $stmt->execute(['testvendor']);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Test vendor already exists',
            'username' => 'testvendor',
            'password' => 'test123'
        ]);
        exit;
    }
    
    // Create test vendor
    $password_hash = password_hash('test123', PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("
        INSERT INTO vendors (name, username, password_hash, email, phone, business_name, status)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        'Test Vendor',
        'testvendor',
        $password_hash,
        'testvendor@example.com',
        '+1234567890',
        'Test Vendor Business',
        'active'
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Test vendor created successfully',
        'username' => 'testvendor',
        'password' => 'test123',
        'note' => 'Use these credentials to test vendor login'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to create test vendor: ' . $e->getMessage()
    ]);
}
