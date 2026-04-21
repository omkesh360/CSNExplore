<?php
/**
 * Automatic Table Creation Script
 * This will create the trip_requests table in your database
 */

require_once 'php/config.php';

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Create Trip Requests Table</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f3f4f6; }
        .container { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .success { background: #d1fae5; border: 2px solid #10b981; color: #065f46; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .error { background: #fee2e2; border: 2px solid #ef4444; color: #991b1b; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .info { background: #dbeafe; border: 2px solid #3b82f6; color: #1e40af; padding: 15px; border-radius: 8px; margin: 20px 0; }
        pre { background: #f9fafb; padding: 15px; border-radius: 8px; overflow-x: auto; }
        h1 { color: #111827; margin-top: 0; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔧 Create Trip Requests Table</h1>";

try {
    $db = getDB();
    
    echo "<div class='info'>📊 Checking database connection...</div>";
    
    // Check if table already exists
    $tableExists = $db->fetchOne("SHOW TABLES LIKE 'trip_requests'");
    
    if ($tableExists) {
        echo "<div class='info'>⚠️ Table 'trip_requests' already exists. Checking structure...</div>";
        
        // Check if email column exists
        $columns = $db->fetchAll("DESCRIBE trip_requests");
        $hasEmail = false;
        
        foreach ($columns as $col) {
            if ($col['Field'] === 'email') {
                $hasEmail = true;
                break;
            }
        }
        
        if (!$hasEmail) {
            echo "<div class='info'>➕ Adding email column to existing table...</div>";
            $db->query("ALTER TABLE trip_requests ADD COLUMN email VARCHAR(255) NOT NULL AFTER full_name");
            $db->query("ALTER TABLE trip_requests ADD INDEX idx_email (email)");
            echo "<div class='success'>✅ Email column added successfully!</div>";
        } else {
            echo "<div class='success'>✅ Table structure is correct. Email column exists.</div>";
        }
        
    } else {
        echo "<div class='info'>🔨 Creating trip_requests table...</div>";
        
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
        
        $db->query($sql);
        
        echo "<div class='success'>✅ Table 'trip_requests' created successfully!</div>";
    }
    
    // Show table structure
    echo "<h3>📋 Table Structure:</h3>";
    $structure = $db->fetchAll("DESCRIBE trip_requests");
    echo "<pre>";
    echo str_pad("Field", 20) . str_pad("Type", 30) . str_pad("Null", 10) . "Key\n";
    echo str_repeat("-", 70) . "\n";
    foreach ($structure as $col) {
        echo str_pad($col['Field'], 20) . 
             str_pad($col['Type'], 30) . 
             str_pad($col['Null'], 10) . 
             $col['Key'] . "\n";
    }
    echo "</pre>";
    
    echo "<div class='success'>
        <h3>🎉 Success! Next Steps:</h3>
        <ol>
            <li>Delete this file (create-trip-table.php) for security</li>
            <li>Test the trip planner form at: <a href='suggestor' target='_blank'>/suggestor</a></li>
            <li>Check admin panel at: <a href='admin/trip-requests.php?admin=true' target='_blank'>/admin/trip-requests.php?admin=true</a></li>
        </ol>
    </div>";
    
} catch (Exception $e) {
    echo "<div class='error'>
        <h3>❌ Error:</h3>
        <p>" . htmlspecialchars($e->getMessage()) . "</p>
        <h4>Troubleshooting:</h4>
        <ul>
            <li>Check your database credentials in .env file</li>
            <li>Make sure the database exists</li>
            <li>Verify database user has CREATE TABLE permissions</li>
        </ul>
    </div>";
    
    echo "<h3>📝 Manual SQL (if needed):</h3>";
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;</pre>";
}

echo "</div></body></html>";
?>
