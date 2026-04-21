<?php
// DIRECT FIX - Creates table immediately
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load environment
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if (strlen($value) >= 2 && (($value[0] === '"' && $value[-1] === '"') || ($value[0] === "'" && $value[-1] === "'"))) {
            $value = substr($value, 1, -1);
        }
        putenv($key . '=' . $value);
    }
}

// Detect environment
$isLocal = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1', 'localhost:8000']);

// Get database credentials
$host = $isLocal ? getenv('DB_HOST_LOCAL') : getenv('DB_HOST_PROD');
$name = $isLocal ? getenv('DB_NAME_LOCAL') : getenv('DB_NAME_PROD');
$user = $isLocal ? getenv('DB_USER_LOCAL') : getenv('DB_USER_PROD');
$pass = $isLocal ? getenv('DB_PASS_LOCAL') : getenv('DB_PASS_PROD');

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Creating Table...</title>";
echo "<style>body{font-family:Arial;max-width:800px;margin:50px auto;padding:20px;background:#f3f4f6;}";
echo ".box{background:white;padding:30px;border-radius:12px;box-shadow:0 4px 6px rgba(0,0,0,0.1);}";
echo ".success{background:#d1fae5;border:2px solid #10b981;color:#065f46;padding:15px;border-radius:8px;margin:20px 0;}";
echo ".error{background:#fee2e2;border:2px solid #ef4444;color:#991b1b;padding:15px;border-radius:8px;margin:20px 0;}";
echo ".info{background:#dbeafe;border:2px solid #3b82f6;color:#1e40af;padding:15px;border-radius:8px;margin:20px 0;}";
echo "pre{background:#f9fafb;padding:15px;border-radius:8px;overflow-x:auto;}</style></head><body><div class='box'>";

echo "<h1>🔧 Creating trip_requests Table</h1>";

try {
    // Connect to database
    $dsn = "mysql:host=$host;dbname=$name;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "<div class='info'>✅ Connected to database: <strong>$name</strong></div>";
    
    // Create table
    $sql = "CREATE TABLE IF NOT EXISTS `trip_requests` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `full_name` varchar(255) NOT NULL,
      `email` varchar(255) NOT NULL,
      `phone` varchar(50) NOT NULL,
      `interests` text DEFAULT NULL,
      `stay_type` varchar(100) DEFAULT NULL,
      `travel_mode` varchar(100) DEFAULT NULL,
      `travel_details` text DEFAULT NULL,
      `extra_notes` text DEFAULT NULL,
      `status` enum('new','contacted','completed','cancelled') DEFAULT 'new',
      `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
      `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `idx_status` (`status`),
      KEY `idx_created` (`created_at`),
      KEY `idx_phone` (`phone`),
      KEY `idx_email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    
    echo "<div class='success'><h2>🎉 SUCCESS!</h2>";
    echo "<p>Table <strong>trip_requests</strong> has been created!</p></div>";
    
    // Verify table structure
    $stmt = $pdo->query("DESCRIBE trip_requests");
    $columns = $stmt->fetchAll();
    
    echo "<h3>📋 Table Structure:</h3><pre>";
    echo str_pad("Column", 20) . str_pad("Type", 30) . "Null\n";
    echo str_repeat("-", 60) . "\n";
    foreach ($columns as $col) {
        echo str_pad($col['Field'], 20) . str_pad($col['Type'], 30) . $col['Null'] . "\n";
    }
    echo "</pre>";
    
    echo "<div class='success'><h3>✅ NEXT STEPS:</h3>";
    echo "<ol>";
    echo "<li><strong>DELETE THIS FILE</strong> (fix-table-now.php) for security</li>";
    echo "<li>Test the form: <a href='suggestor' target='_blank'><strong>/suggestor</strong></a></li>";
    echo "<li>View admin panel: <a href='admin/trip-requests.php?admin=true' target='_blank'><strong>/admin/trip-requests.php?admin=true</strong></a></li>";
    echo "</ol></div>";
    
} catch (PDOException $e) {
    echo "<div class='error'><h3>❌ ERROR:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<h4>Database Info:</h4>";
    echo "<p>Host: $host<br>Database: $name<br>User: $user</p>";
    echo "<h4>Try This SQL Manually in phpMyAdmin:</h4>";
    echo "<pre>CREATE TABLE IF NOT EXISTS `trip_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `interests` text DEFAULT NULL,
  `stay_type` varchar(100) DEFAULT NULL,
  `travel_mode` varchar(100) DEFAULT NULL,
  `travel_details` text DEFAULT NULL,
  `extra_notes` text DEFAULT NULL,
  `status` enum('new','contacted','completed','cancelled') DEFAULT 'new',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_created` (`created_at`),
  KEY `idx_phone` (`phone`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;</pre></div>";
}

echo "</div></body></html>";
?>
