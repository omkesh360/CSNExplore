<?php
// Configuration file for TravelHub PHP Application

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// CORS Headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// JWT Secret Key
define('JWT_SECRET', getenv('JWT_SECRET') ?: 'travelhub_secure_secret_key_2024');

// Data directory
define('DATA_DIR', __DIR__ . '/../data');

// Uploads directory
define('UPLOADS_DIR', __DIR__ . '/../public/images/uploads');

// Ensure data directory exists
if (!is_dir(DATA_DIR)) {
    mkdir(DATA_DIR, 0755, true);
}

// Ensure uploads directory exists
if (!is_dir(UPLOADS_DIR)) {
    mkdir(UPLOADS_DIR, 0755, true);
}

// Cache control headers for API
header('Cache-Control: no-store, no-cache, must-revalidate, proxy-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Helper function to send JSON response
function sendJson($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

// Helper function to send error response
function sendError($message, $statusCode = 400) {
    sendJson(['error' => $message], $statusCode);
}

// Helper function to get JSON input
function getJsonInput() {
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?: [];
}

// Helper function to read JSON file
function readJsonFile($filename) {
    $filepath = DATA_DIR . '/' . $filename;
    if (!file_exists($filepath)) {
        return [];
    }
    $content = file_get_contents($filepath);
    return json_decode($content, true) ?: [];
}

// Helper function to write JSON file
function writeJsonFile($filename, $data) {
    $filepath = DATA_DIR . '/' . $filename;
    return file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}
