<?php
require_once __DIR__ . '/../php/config.php';

echo "APP_ENV: " . (defined('APP_ENV') ? APP_ENV : 'undefined') . "\n";
echo "DB_HOST_LOCAL: " . getenv('DB_HOST_LOCAL') . "\n";
echo "DB_NAME_LOCAL: " . getenv('DB_NAME_LOCAL') . "\n";
echo "DB_USER_LOCAL: " . getenv('DB_USER_LOCAL') . "\n";
echo "DB_PASS_LOCAL: " . (getenv('DB_PASS_LOCAL') === false ? 'FALSE' : 'SET') . "\n";

try {
    $db = getDB();
    $categories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses', 'blogs'];

    foreach ($categories as $cat) {
        echo "Updating table: $cat...\n";
        
        if ($cat === 'buses') {
            $afterCol = 'badge';
        } elseif ($cat === 'blogs') {
            $afterCol = 'content';
        } else {
            $afterCol = 'description';
        }

        // Add mini_description column if not exists
        try {
            $db->query("ALTER TABLE $cat ADD COLUMN mini_description TEXT DEFAULT NULL AFTER $afterCol");
            echo "  Added mini_description column after $afterCol.\n";
        } catch (Exception $e) {
            echo "  mini_description column already exists or error: " . $e->getMessage() . "\n";
        }

        // Add keywords column if not exists
        try {
            $db->query("ALTER TABLE $cat ADD COLUMN keywords TEXT DEFAULT NULL AFTER mini_description");
            echo "  Added keywords column.\n";
        } catch (Exception $e) {
            echo "  keywords column already exists or error: " . $e->getMessage() . "\n";
        }

        // Initialize mini_description for existing records
        $db->query("UPDATE $cat SET mini_description = 'Hello it is mini description for this page' WHERE mini_description IS NULL OR mini_description = ''");
        echo "  Initialized mini_description for existing records.\n";
    }

    // Create site_keywords table
    echo "Creating site_keywords table...\n";
    $db->query("CREATE TABLE IF NOT EXISTS site_keywords (
        id INT AUTO_INCREMENT PRIMARY KEY,
        keyword VARCHAR(255) UNIQUE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "  site_keywords table created.\n";

    // Add some default keywords
    $defaultKeywords = ['travel', 'tourism', 'CSNExplore', 'adventure', 'vacation', 'luxury', 'budget', 'family', 'couples', 'guide'];
    foreach ($defaultKeywords as $kw) {
        try {
            $db->query("INSERT IGNORE INTO site_keywords (keyword) VALUES (?)", [$kw]);
        } catch (Exception $e) {}
    }
    echo "  Default keywords added.\n";

    echo "Migration complete!\n";

} catch (Exception $e) {
    die("Migration failed: " . $e->getMessage());
}
