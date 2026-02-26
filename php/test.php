<?php
// Simple test script to verify PHP setup

echo "TravelHub PHP Backend - System Check\n";
echo "=====================================\n\n";

// Check PHP version
echo "PHP Version: " . phpversion() . "\n";
if (version_compare(phpversion(), '7.4.0', '>=')) {
    echo "✓ PHP version is compatible\n\n";
} else {
    echo "✗ PHP version must be 7.4 or higher\n\n";
    exit(1);
}

// Check required extensions
$required = ['json', 'hash'];
echo "Required Extensions:\n";
foreach ($required as $ext) {
    if (extension_loaded($ext)) {
        echo "✓ $ext\n";
    } else {
        echo "✗ $ext (missing)\n";
    }
}
echo "\n";

// Check directories
echo "Directory Permissions:\n";
$dirs = [
    __DIR__ . '/../data' => 'data/',
    __DIR__ . '/../public/images/uploads' => 'public/images/uploads/'
];

foreach ($dirs as $path => $name) {
    if (is_dir($path)) {
        if (is_writable($path)) {
            echo "✓ $name (writable)\n";
        } else {
            echo "⚠ $name (not writable)\n";
        }
    } else {
        echo "✗ $name (does not exist)\n";
    }
}
echo "\n";

// Test JWT functions
echo "Testing JWT Functions:\n";
require_once __DIR__ . '/jwt.php';
define('JWT_SECRET', 'test_secret');

$payload = ['id' => '123', 'email' => 'test@example.com', 'role' => 'user'];
$token = createJWT($payload, JWT_SECRET, 3600);
echo "✓ JWT creation successful\n";

$decoded = verifyJWT($token, JWT_SECRET);
if ($decoded && $decoded['id'] === '123') {
    echo "✓ JWT verification successful\n";
} else {
    echo "✗ JWT verification failed\n";
}
echo "\n";

// Test JSON file operations
echo "Testing File Operations:\n";
require_once __DIR__ . '/config.php';

$testData = ['test' => 'data', 'timestamp' => time()];
$result = writeJsonFile('_test.json', $testData);
if ($result !== false) {
    echo "✓ Write JSON file successful\n";
} else {
    echo "✗ Write JSON file failed\n";
}

$readData = readJsonFile('_test.json');
if ($readData && isset($readData['test']) && $readData['test'] === 'data') {
    echo "✓ Read JSON file successful\n";
} else {
    echo "✗ Read JSON file failed\n";
}

// Cleanup
@unlink(DATA_DIR . '/_test.json');
echo "\n";

echo "=====================================\n";
echo "System check complete!\n";
echo "\nTo start the server, run:\n";
echo "  php -S localhost:8000\n";
echo "\nOr with Apache, ensure mod_rewrite is enabled.\n";
