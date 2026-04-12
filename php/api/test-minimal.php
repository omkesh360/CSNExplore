<?php
// Minimal test API - no dependencies
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    
    echo json_encode([
        'success' => true,
        'message' => 'Minimal API working',
        'method' => $_SERVER['REQUEST_METHOD'],
        'input' => $input,
        'get' => $_GET,
        'timestamp' => time()
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
