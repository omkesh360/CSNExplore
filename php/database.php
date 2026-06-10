<?php
class Database {
    private static $instance = null;
    private $db;
    private $useCache = false;
    private $cacheTTL = 3600; // seconds, overridden by settings.json
    private $cacheDir = __DIR__ . '/../cache/db_query_cache/';

    private function __construct() {
        // Auto-detect environment: check if running on Hostinger or localhost
        $appEnv = function_exists('env') ? env('APP_ENV') : (function_exists('getenv') ? getenv('APP_ENV') : false);
        $isProduction = (
            isset($_SERVER['HTTP_HOST']) && 
            (strpos($_SERVER['HTTP_HOST'], 'hostingersite.com') !== false || 
             strpos($_SERVER['HTTP_HOST'], 'csnexplore.com') !== false ||
             strpos($_SERVER['HTTP_HOST'], 'hostinger') !== false)
        ) || $appEnv === 'production';

        // Helper to safely get env vars
        $getEnvVar = function($key) {
            return function_exists('env') ? env($key) : (function_exists('getenv') ? getenv($key) : false);
        };

        // Use production or local credentials based on environment
        if ($isProduction) {
            $host   = $getEnvVar('DB_HOST_PROD');
            $dbName = $getEnvVar('DB_NAME_PROD');
            $user   = $getEnvVar('DB_USER_PROD');
            $pass   = $getEnvVar('DB_PASS_PROD');
        } else {
            $host   = $getEnvVar('DB_HOST_LOCAL');
            $dbName = $getEnvVar('DB_NAME_LOCAL');
            $user   = $getEnvVar('DB_USER_LOCAL');
            $pass   = $getEnvVar('DB_PASS_LOCAL');
        }
        
        // Validate required credentials
        if (!$host || !$dbName || !$user || $pass === false) {
            error_log('Database credentials missing in environment variables');
            throw new Exception('Database configuration error. Please check environment variables.');
        }
        
        // Force 127.0.0.1 when host is 'localhost' — avoids Windows named-pipe/socket issues with PDO
        if ($host === 'localhost') $host = '127.0.0.1';
        
        $port = $getEnvVar('DB_PORT') ?: '3306';

        $dsn = "mysql:host=$host;port=$port;dbname=$dbName;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Persistent connections: disabled for Hostinger compatibility
            PDO::ATTR_PERSISTENT         => false,
            PDO::ATTR_EMULATE_PREPARES   => false, // native prepared statements
            PDO::ATTR_TIMEOUT            => 5,     // fail fast if DB is unreachable
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        ];
        $this->db = new PDO($dsn, $user, $pass, $options);

        // Only run schema init once per deployment (check a flag file)
        $flagFile = __DIR__ . '/../cache/.schema_init';
        if (!file_exists($flagFile)) {
            $this->initSchema();
            if (!is_dir(dirname($flagFile))) @mkdir(dirname($flagFile), 0755, true);
            @file_put_contents($flagFile, date('Y-m-d H:i:s'));
        }
        
        $settingsFile = __DIR__ . '/settings.json';
        if (file_exists($settingsFile)) {
            $settings = json_decode(file_get_contents($settingsFile), true);
            $this->useCache = $settings['features']['caching']['enabled'] ?? false;
            // Read TTL from settings (minutes → seconds), default 60 min
            $ttlMinutes = $settings['features']['caching']['ttl']['db'] ?? 60;
            $this->cacheTTL = (int)$ttlMinutes * 60;
        }
        if ($this->useCache && !is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0755, true);
        }
        
    }

    private $apcuAvailable = null;

    private function isApcuAvailable() {
        if ($this->apcuAvailable === null) {
            $this->apcuAvailable = extension_loaded('apcu') && ini_get('apc.enabled');
        }
        return $this->apcuAvailable;
    }

    private function getCacheKey($sql, $params) {
        preg_match('/(?:FROM|INTO|UPDATE)\s+([a-zA-Z0-9_]+)/i', $sql, $matches);
        $prefix = $matches ? strtolower($matches[1]) . '_' : 'query_';
        return 'csn_' . $prefix . md5($sql . serialize($params));
    }

    private function readCache($key) {
        if (!$this->useCache) return null;
        if ($this->isApcuAvailable()) {
            $success = false;
            $data = apcu_fetch($key, $success);
            if ($success) return $data;
            return null;
        }
        $file = $this->cacheDir . $key . '.json';
        if (file_exists($file)) {
            if (filemtime($file) > time() - $this->cacheTTL) {
                return json_decode(file_get_contents($file), true);
            }
        }
        return null;
    }

    private function writeCache($key, $data) {
        if (!$this->useCache) return;
        if ($this->isApcuAvailable()) {
            apcu_store($key, $data, $this->cacheTTL);
            return;
        }
        $file = $this->cacheDir . $key . '.json';
        @file_put_contents($file, json_encode($data));
    }

    public function clearCache($table = null) {
        if ($this->isApcuAvailable()) {
            if (class_exists('APCUIterator')) {
                $regex = $table ? '/^csn_' . strtolower($table) . '_/' : '/^csn_/';
                apcu_delete(new \APCUIterator($regex));
            } else {
                apcu_clear_cache();
            }
        }
        if (is_dir($this->cacheDir)) {
            if ($table) {
                $files = glob($this->cacheDir . '*_' . strtolower($table) . '_*.*');
                $files = array_merge($files ?: [], glob($this->cacheDir . strtolower($table) . '_*.*') ?: []);
            } else {
                $files = glob($this->cacheDir . '*.*');
            }
            if ($files) {
                foreach ($files as $file) {
                    @unlink($file);
                }
            }
        }
    }

    public static function getInstance() {
        if (!self::$instance) self::$instance = new self();
        return self::$instance;
    }

    public function getConnection() { return $this->db; }

    private function initSchema() {
        $this->db->exec("
        CREATE TABLE IF NOT EXISTS `users` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `email` VARCHAR(255) UNIQUE NOT NULL,
          `password_hash` VARCHAR(255) NOT NULL,
          `name` VARCHAR(255) NOT NULL,
          `phone` VARCHAR(50),
          `role` ENUM('user','admin','vendor') NOT NULL DEFAULT 'user',
          `is_verified` TINYINT(1) DEFAULT 0,
          `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
          `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `stays` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `name` VARCHAR(255) NOT NULL, `type` VARCHAR(100), `location` VARCHAR(255) NOT NULL,
          `description` TEXT, `price_per_night` DECIMAL(10,2) NOT NULL DEFAULT 0,
          `rating` DECIMAL(3,1) DEFAULT 0, `reviews` INT DEFAULT 0,
          `badge` VARCHAR(100), `image` VARCHAR(255), `gallery` TEXT, `amenities` TEXT,
          `room_type` VARCHAR(100), `max_guests` INT DEFAULT 2,
          `map_embed` LONGTEXT NULL,
          `meta_title` VARCHAR(255) DEFAULT NULL, `meta_description` TEXT DEFAULT NULL, `meta_keywords` TEXT DEFAULT NULL,
          `slug` VARCHAR(255) DEFAULT NULL, `focus_keyword` VARCHAR(255) DEFAULT NULL, `seo_score` INT DEFAULT 0,
          `is_active` TINYINT(1) DEFAULT 1, `display_order` INT DEFAULT 0,
          `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
          `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `cars` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `name` VARCHAR(255) NOT NULL, `type` VARCHAR(100), `location` VARCHAR(255) NOT NULL,
          `description` TEXT, `price_per_day` DECIMAL(10,2) NOT NULL DEFAULT 0,
          `rating` DECIMAL(3,1) DEFAULT 0, `reviews` INT DEFAULT 0,
          `badge` VARCHAR(100), `image` VARCHAR(255), `gallery` TEXT, `features` TEXT,
          `fuel_type` VARCHAR(50), `transmission` VARCHAR(50), `seats` INT DEFAULT 5,
          `driver_available` TINYINT(1) DEFAULT 0, `price_with_driver` DECIMAL(10,2) DEFAULT 0,
          `map_embed` LONGTEXT NULL,
          `meta_title` VARCHAR(255) DEFAULT NULL, `meta_description` TEXT DEFAULT NULL, `meta_keywords` TEXT DEFAULT NULL,
          `slug` VARCHAR(255) DEFAULT NULL, `focus_keyword` VARCHAR(255) DEFAULT NULL, `seo_score` INT DEFAULT 0,
          `is_active` TINYINT(1) DEFAULT 1, `display_order` INT DEFAULT 0,
          `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
          `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `bikes` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `name` VARCHAR(255) NOT NULL, `type` VARCHAR(100), `location` VARCHAR(255) NOT NULL,
          `description` TEXT, `price_per_day` DECIMAL(10,2) NOT NULL DEFAULT 0,
          `rating` DECIMAL(3,1) DEFAULT 0, `reviews` INT DEFAULT 0,
          `badge` VARCHAR(100), `image` VARCHAR(255), `gallery` TEXT, `features` TEXT,
          `fuel_type` VARCHAR(50), `cc` VARCHAR(50),
          `map_embed` LONGTEXT NULL,
          `meta_title` VARCHAR(255) DEFAULT NULL, `meta_description` TEXT DEFAULT NULL, `meta_keywords` TEXT DEFAULT NULL,
          `slug` VARCHAR(255) DEFAULT NULL, `focus_keyword` VARCHAR(255) DEFAULT NULL, `seo_score` INT DEFAULT 0,
          `is_active` TINYINT(1) DEFAULT 1, `display_order` INT DEFAULT 0,
          `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
          `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `restaurants` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `name` VARCHAR(255) NOT NULL, `type` VARCHAR(100), `cuisine` VARCHAR(100), `location` VARCHAR(255) NOT NULL,
          `description` TEXT, `price_per_person` DECIMAL(10,2) DEFAULT 0,
          `rating` DECIMAL(3,1) DEFAULT 0, `reviews` INT DEFAULT 0,
          `badge` VARCHAR(100), `image` VARCHAR(255), `gallery` TEXT, `menu_highlights` TEXT,
          `map_embed` LONGTEXT NULL,
          `meta_title` VARCHAR(255) DEFAULT NULL, `meta_description` TEXT DEFAULT NULL, `meta_keywords` TEXT DEFAULT NULL,
          `slug` VARCHAR(255) DEFAULT NULL, `focus_keyword` VARCHAR(255) DEFAULT NULL, `seo_score` INT DEFAULT 0,
          `is_active` TINYINT(1) DEFAULT 1, `display_order` INT DEFAULT 0,
          `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
          `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `attractions` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `name` VARCHAR(255) NOT NULL, `type` VARCHAR(100), `location` VARCHAR(255) NOT NULL,
          `description` TEXT, `entry_fee` DECIMAL(10,2) DEFAULT 0,
          `rating` DECIMAL(3,1) DEFAULT 0, `reviews` INT DEFAULT 0,
          `badge` VARCHAR(100), `image` VARCHAR(255), `gallery` TEXT,
          `opening_hours` TEXT, `best_time` TEXT,
          `map_embed` LONGTEXT NULL,
          `meta_title` VARCHAR(255) DEFAULT NULL, `meta_description` TEXT DEFAULT NULL, `meta_keywords` TEXT DEFAULT NULL,
          `slug` VARCHAR(255) DEFAULT NULL, `focus_keyword` VARCHAR(255) DEFAULT NULL, `seo_score` INT DEFAULT 0,
          `is_active` TINYINT(1) DEFAULT 1, `display_order` INT DEFAULT 0,
          `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
          `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `buses` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `operator` VARCHAR(255) NOT NULL, `bus_type` VARCHAR(100), `from_location` VARCHAR(255) NOT NULL,
          `to_location` VARCHAR(255) NOT NULL, `departure_time` VARCHAR(100), `arrival_time` VARCHAR(100),
          `duration` VARCHAR(100), `price` DECIMAL(10,2) DEFAULT 0,
          `rating` DECIMAL(3,1) DEFAULT 0, `reviews` INT DEFAULT 0,
          `badge` VARCHAR(100), `image` VARCHAR(255), `amenities` TEXT, `seats_available` INT DEFAULT 40,
          `map_embed` LONGTEXT NULL,
          `meta_title` VARCHAR(255) DEFAULT NULL, `meta_description` TEXT DEFAULT NULL, `meta_keywords` TEXT DEFAULT NULL,
          `slug` VARCHAR(255) DEFAULT NULL, `focus_keyword` VARCHAR(255) DEFAULT NULL, `seo_score` INT DEFAULT 0,
          `is_active` TINYINT(1) DEFAULT 1, `display_order` INT DEFAULT 0,
          `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
          `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          `gallery` TEXT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `bookings` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `full_name` VARCHAR(255) NOT NULL, `phone` VARCHAR(50) NOT NULL, `email` VARCHAR(255),
          `booking_date` VARCHAR(100), `number_of_people` INT DEFAULT 1,
          `service_type` VARCHAR(50), `listing_id` INT, `listing_name` VARCHAR(255),
          `with_driver` TINYINT(1) DEFAULT 0,
          `status` ENUM('pending','completed','cancelled') DEFAULT 'pending',
          `notes` TEXT,
          `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
          `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          `checkin_date` VARCHAR(100),
          `checkout_date` VARCHAR(100)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `blogs` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `title` VARCHAR(255) NOT NULL, `content` LONGTEXT NOT NULL,
          `author` VARCHAR(100) DEFAULT 'Admin', `image` VARCHAR(255),
          `status` ENUM('published','draft') DEFAULT 'published',
          `category` VARCHAR(100) DEFAULT 'General',
          `read_time` VARCHAR(50), `tags` VARCHAR(255), `meta_description` TEXT,
          `meta_title` VARCHAR(255) DEFAULT NULL, `meta_keywords` TEXT DEFAULT NULL,
          `focus_keyword` VARCHAR(255) DEFAULT NULL, `seo_score` INT DEFAULT 0,
          `slug` VARCHAR(255) DEFAULT NULL, `excerpt` TEXT DEFAULT NULL,
          `linked_listings` JSON DEFAULT NULL,
          `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
          `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `comments` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `user_id` INT NOT NULL,
          `ref_type` ENUM('blog','listing') NOT NULL,
          `ref_id` INT NOT NULL,
          `content` TEXT NOT NULL,
          `status` ENUM('approved','pending','rejected') DEFAULT 'approved',
          `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
          `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          INDEX `idx_ref` (`ref_type`, `ref_id`),
          INDEX `idx_user` (`user_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `trip_requests` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `full_name` VARCHAR(255) NOT NULL,
          `email` VARCHAR(255),
          `phone` VARCHAR(50) NOT NULL,
          `stay_type` VARCHAR(100),
          `travel_mode` VARCHAR(100),
          `budget` VARCHAR(100),
          `duration` VARCHAR(100),
          `interests` TEXT,
          `special_requests` TEXT,
          `status` ENUM('new','contacted','completed','cancelled') DEFAULT 'new',
          `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
          `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `about_contact` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `section` VARCHAR(100) UNIQUE NOT NULL,
          `content` TEXT NOT NULL,
          `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `contact_messages` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `first_name` VARCHAR(100) NOT NULL,
          `last_name` VARCHAR(100),
          `email` VARCHAR(255) NOT NULL,
          `interest` VARCHAR(100),
          `message` TEXT NOT NULL,
          `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `password_resets` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `user_id` INT NOT NULL,
          `token_hash` VARCHAR(255) NOT NULL,
          `expires_at` DATETIME NOT NULL,
          `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `email_verification_tokens` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `user_id` INT NOT NULL,
          `token_hash` VARCHAR(255) NOT NULL,
          `expires_at` DATETIME NOT NULL,
          `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `vendors` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `name` VARCHAR(255) NOT NULL,
          `username` VARCHAR(100) UNIQUE NOT NULL,
          `password_hash` VARCHAR(255) NOT NULL,
          `email` VARCHAR(255),
          `phone` VARCHAR(50),
          `business_name` VARCHAR(255),
          `status` ENUM('active','inactive') DEFAULT 'active',
          `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
          `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `room_types` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `vendor_id` INT NULL,
          `stay_id` INT NULL,
          `name` VARCHAR(255) NOT NULL,
          `description` TEXT,
          `base_price` DECIMAL(10,2) DEFAULT 0,
          `max_guests` INT DEFAULT 2,
          `amenities` TEXT,
          `is_active` TINYINT(1) DEFAULT 1,
          `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
          `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          INDEX `idx_vendor_room_types` (`vendor_id`),
          INDEX `idx_stay_room_types` (`stay_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `rooms` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `room_type_id` INT NOT NULL,
          `vendor_id` INT NULL,
          `room_number` VARCHAR(50) NOT NULL,
          `floor` VARCHAR(20),
          `price` DECIMAL(10,2) NOT NULL,
          `is_available` TINYINT(1) DEFAULT 1,
          `status` ENUM('available','occupied','maintenance') DEFAULT 'available',
          `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
          `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          FOREIGN KEY (`room_type_id`) REFERENCES `room_types`(`id`) ON DELETE CASCADE,
          INDEX `idx_vendor_rooms` (`vendor_id`),
          INDEX `idx_room_type` (`room_type_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `rate_limits` (
          `ip_key` VARCHAR(100) PRIMARY KEY,
          `attempts` INT NOT NULL,
          `first_attempt` INT NOT NULL,
          `expires_at` INT NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // Activity logs table (always safe — IF NOT EXISTS)
        $this->db->exec("
        CREATE TABLE IF NOT EXISTS `activity_logs` (
          `id`          INT          NOT NULL AUTO_INCREMENT,
          `actor_id`    INT          NULL,
          `actor_name`  VARCHAR(255) NOT NULL DEFAULT 'System',
          `actor_role`  VARCHAR(50)  NOT NULL DEFAULT 'system',
          `action_type` VARCHAR(80)  NOT NULL DEFAULT 'info',
          `description` TEXT         NOT NULL,
          `meta`        JSON         NULL,
          `ip_address`  VARCHAR(64)  NULL,
          `created_at`  DATETIME     DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `idx_actor`  (`actor_id`),
          KEY `idx_type`   (`action_type`),
          KEY `idx_created`(`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // Add vendor_id columns if they don't exist
        try {
            $this->db->exec("ALTER TABLE `stays` ADD COLUMN `vendor_id` INT NULL AFTER `id`, ADD INDEX `idx_vendor_stays` (`vendor_id`)");
        } catch (Exception $e) {
            // Column already exists
        }
        
        try {
            $this->db->exec("ALTER TABLE `cars` ADD COLUMN `vendor_id` INT NULL AFTER `id`, ADD INDEX `idx_vendor_cars` (`vendor_id`)");
        } catch (Exception $e) {
            // Column already exists
        }
        
        try {
            $this->db->exec("ALTER TABLE `cars` ADD COLUMN `is_available` TINYINT(1) DEFAULT 1 AFTER `is_active`");
        } catch (Exception $e) {
            // Column already exists
        }
        
        // Add map_embed column to all listing tables if they don't exist
        $tables = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];
        foreach ($tables as $table) {
            try {
                $this->db->exec("ALTER TABLE `$table` ADD COLUMN `map_embed` LONGTEXT NULL");
            } catch (Exception $e) {
                // Column already exists
            }
            
            // Add SEO columns
            foreach ([
                'meta_title'       => 'VARCHAR(255) DEFAULT NULL',
                'meta_description' => 'TEXT DEFAULT NULL',
                'meta_keywords'    => 'TEXT DEFAULT NULL',
                'slug'             => 'VARCHAR(255) DEFAULT NULL',
                'focus_keyword'    => 'VARCHAR(255) DEFAULT NULL',
                'seo_score'        => 'INT(11) DEFAULT 0'
            ] as $col => $def) {
                try {
                    $this->db->exec("ALTER TABLE `$table` ADD COLUMN `$col` $def");
                } catch (Exception $e) {
                    // Column already exists
                }
            }
        }
        
        // Add performance indexes
        try { $this->db->exec("ALTER TABLE `blogs` ADD INDEX `idx_status_category` (`status`, `category`)"); } catch (Exception $e) {}
        foreach ($tables as $table) {
            try { $this->db->exec("ALTER TABLE `$table` ADD INDEX `idx_active_order` (`is_active`, `display_order`)"); } catch (Exception $e) {}
            try { $this->db->exec("ALTER TABLE `$table` ADD INDEX `idx_type` (`type`)"); } catch (Exception $e) {}
        }

        // Add linked_listings column to blogs if not exists (for existing installs)
        try {
            $this->db->exec("ALTER TABLE `blogs` ADD COLUMN `linked_listings` JSON DEFAULT NULL");
        } catch (Exception $e) { /* column already exists */ }
        try {
            $this->db->exec("ALTER TABLE `blogs` ADD COLUMN `meta_title` VARCHAR(255) DEFAULT NULL");
        } catch (Exception $e) { /* column already exists */ }
        try {
            $this->db->exec("ALTER TABLE `blogs` ADD COLUMN `meta_keywords` TEXT DEFAULT NULL");
        } catch (Exception $e) { /* column already exists */ }
        try {
            $this->db->exec("ALTER TABLE `blogs` ADD COLUMN `focus_keyword` VARCHAR(255) DEFAULT NULL");
        } catch (Exception $e) { /* column already exists */ }
        try {
            $this->db->exec("ALTER TABLE `blogs` ADD COLUMN `seo_score` INT DEFAULT 0");
        } catch (Exception $e) { /* column already exists */ }
        try {
            $this->db->exec("ALTER TABLE `blogs` ADD COLUMN `slug` VARCHAR(255) DEFAULT NULL");
        } catch (Exception $e) { /* column already exists */ }
        try {
            $this->db->exec("ALTER TABLE `blogs` ADD COLUMN `excerpt` TEXT DEFAULT NULL");
        } catch (Exception $e) { /* column already exists */ }
        try {
            $this->db->exec("ALTER TABLE `jobs` ADD COLUMN `attempts` INT NOT NULL DEFAULT 0 AFTER `payload`");
        } catch (Exception $e) { /* column already exists */ }

        // Create comments table if not exists (for existing installs)
        try {
            $this->db->exec("
            CREATE TABLE IF NOT EXISTS `comments` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `user_id` INT NOT NULL,
              `ref_type` VARCHAR(50) NOT NULL,
              `ref_id` INT NOT NULL,
              `content` TEXT NOT NULL,
              `status` ENUM('approved','pending','rejected') DEFAULT 'approved',
              `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
              `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              INDEX `idx_ref` (`ref_type`, `ref_id`),
              INDEX `idx_user` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
        } catch (Exception $e) { /* already exists */ }
        
        try {
            $this->db->exec("ALTER TABLE `comments` MODIFY `ref_type` VARCHAR(50) NOT NULL");
        } catch (Exception $e) { /* fallback */ }
        try {
            $this->db->exec("ALTER TABLE `comments` MODIFY `user_id` INT NULL DEFAULT NULL");
        } catch (Exception $e) { /* already nullable */ }
        try {
            $this->db->exec("ALTER TABLE `comments` ADD COLUMN `guest_name` VARCHAR(100) NULL AFTER `user_id`");
        } catch (Exception $e) { /* already exists */ }

        // Seed admin user if not exists

        $admin = $this->fetchOne("SELECT id FROM users WHERE email = ?", ['admin@csnexplore.com']);
        if (!$admin) {
            $hash = password_hash('admin123', PASSWORD_DEFAULT);
            $this->insert('users', [
                'email' => 'admin@csnexplore.com',
                'password_hash' => $hash,
                'name' => 'CSNExplore Admin',
                'role' => 'admin',
                'is_verified' => 1
            ]);
        } else {
            // Ensure existing admin is verified
            $this->update('users', ['is_verified' => 1], 'email = :e', [':e' => 'admin@csnexplore.com']);
        }
    }


    public function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetchAll($sql, $params = []) {
        if ($this->useCache && stripos(trim($sql), 'SELECT') === 0) {
            $key = $this->getCacheKey($sql, $params);
            $cached = $this->readCache($key);
            if ($cached !== null) return $cached;
            $res = $this->query($sql, $params)->fetchAll();
            $this->writeCache($key, $res);
            return $res;
        }
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetchOne($sql, $params = []) {
        if ($this->useCache && stripos(trim($sql), 'SELECT') === 0) {
            $key = $this->getCacheKey($sql, $params);
            $cached = $this->readCache($key);
            if ($cached !== null) return $cached;
            $res = $this->query($sql, $params)->fetch();
            $this->writeCache($key, $res);
            return $res;
        }
        return $this->query($sql, $params)->fetch();
    }

    public function insert($table, $data) {
        $cols = array_keys($data);
        $ph   = array_map(fn($c) => ":$c", $cols);
        $sql  = "INSERT INTO $table (" . implode(',', $cols) . ") VALUES (" . implode(',', $ph) . ")";
        $params = [];
        foreach ($data as $k => $v) $params[":$k"] = $v;
        $this->query($sql, $params);
        $this->clearCache($table);
        return $this->db->lastInsertId();
    }

    public function update($table, $data, $where, $whereParams = []) {
        // Use :set_ prefix to avoid collision with WHERE named params (e.g. :id vs :id)
        $sets   = array_map(fn($c) => "$c = :set_$c", array_keys($data));
        $sql    = "UPDATE $table SET " . implode(', ', $sets) . " WHERE $where";
        $params = [];
        foreach ($data as $k => $v) $params[":set_$k"] = $v;
        $stmt = $this->query($sql, array_merge($params, $whereParams));
        $this->clearCache($table);
        return $stmt->rowCount();
    }

    public function delete($table, $where, $params = []) {
        $stmt = $this->query("DELETE FROM $table WHERE $where", $params);
        $this->clearCache($table);
        return $stmt->rowCount();
    }

    public function lastInsertId() { return $this->db->lastInsertId(); }
    public function beginTransaction() { return $this->db->beginTransaction(); }
    public function commit() { return $this->db->commit(); }
    public function rollback() { return $this->db->rollBack(); }
}
