<?php
/**
 * Database Class
 *
 * @package W3speedster
 * @since 9.0.0
 */

namespace W3speedster;
use PDO;
use PDOException;

if (!defined('W3SPEEDSTER_CONFIG')) {
    return;
}

/**
 * Database Class
 */
class W3DB {
    private static ?W3DB $instance = null;
    private PDO $connection;
    private string $host;
    private string $dbname;
    private string $username;
    private string $password;

    // Table definitions
    private array $tables = [
        'w3_core_webvitals' => "
            CREATE TABLE IF NOT EXISTS w3_core_webvitals (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                url varchar(255) NOT NULL,
                issuetype varchar(50) NOT NULL,
                data text NOT NULL,
                deviceType varchar(255) NOT NULL,
                timestamp datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ",
        'w3_change_logs' => "
            CREATE TABLE IF NOT EXISTS w3_change_logs (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                time datetime DEFAULT CURRENT_TIMESTAMP,
                user varchar(255) NOT NULL,
                ip varchar(45) NOT NULL,
                action text NOT NULL,
                old text NOT NULL,
                new text NOT NULL,
                PRIMARY KEY  (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ",
        'w3_site_urls' => "
            CREATE TABLE IF NOT EXISTS w3_site_urls (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                url text NOT NULL,
                status TINYINT(1) NOT NULL DEFAULT 0,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY status (status),
                KEY idx_updated_at (updated_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ",
        'w3_options' => "
            CREATE TABLE IF NOT EXISTS w3_options (
                option_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                option_name varchar(191) NOT NULL DEFAULT '',
                option_value longtext NOT NULL,
                PRIMARY KEY  (option_id),
                UNIQUE KEY option_name (option_name)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        "
    ];

    public function __construct()
    {
        $dbConfig = W3SPEEDSTER_CONFIG['database'] ?? [];

        $this->host     = $dbConfig['host'] ?? "localhost";
        $this->dbname   = $dbConfig['database'] ?? "your_database";
        $this->username = $dbConfig['username'] ?? "root";
        $this->password = $dbConfig['password'] ?? "";

        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
            $this->connection = new PDO($dsn, $this->username, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // Check and create tables if not exist
            $this->checkAndCreateTables();

        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance(): W3DB
    {
        if (self::$instance === null) {
            self::$instance = new W3DB();
        }
        return self::$instance;
    }

    public function connect(): PDO
    {
        return $this->connection;
    }

    /**
     * Check if required tables exist, and create them if not.
     */
    private function checkAndCreateTables(): void
    {
        foreach ($this->tables as $tableName => $createSQL) {
            if (!$this->tableExists($tableName)) {
                try {
                    $this->connection->exec($createSQL);
                } catch (PDOException $e) {
                    error_log("Failed to create table {$tableName}: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Check if a table exists in the current database.
     */
    private function tableExists(string $tableName): bool
    {
        try {
            $stmt = $this->connection->prepare("SHOW TABLES LIKE :table");
            $stmt->execute([':table' => $tableName]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log("Error checking table existence for {$tableName}: " . $e->getMessage());
            return false;
        }
    }
}