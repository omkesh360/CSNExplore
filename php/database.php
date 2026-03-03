<?php
// Database connection and helper functions

class Database {
    private static $instance = null;
    private $db;
    
    private function __construct() {
        $dbPath = __DIR__ . '/../database/travelhub.db';
        $dbDir = dirname($dbPath);
        
        // Create database directory if it doesn't exist
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0755, true);
        }
        
        try {
            $this->db = new PDO('sqlite:' . $dbPath);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Enable foreign keys
            $this->db->exec('PRAGMA foreign_keys = ON');
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            throw new Exception('Database connection failed');
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->db;
    }
    
    // Initialize database with schema
    public function initialize() {
        $schemaFile = __DIR__ . '/../database/schema-sqlite.sql';
        if (!file_exists($schemaFile)) {
            throw new Exception('Schema file not found');
        }
        
        $schema = file_get_contents($schemaFile);
        $this->db->exec($schema);
        
        return true;
    }
    
    // Generic query method
    public function query($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log('Query failed: ' . $e->getMessage());
            throw new Exception('Database query failed: ' . $e->getMessage());
        }
    }
    
    // Fetch all rows
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // Fetch single row
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    // Insert and return last insert ID
    public function insert($table, $data) {
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ":$col", $columns);
        
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );
        
        $params = [];
        foreach ($data as $key => $value) {
            $params[":$key"] = $value;
        }
        
        $this->query($sql, $params);
        return $this->db->lastInsertId();
    }
    
    // Update rows
    public function update($table, $data, $where, $whereParams = []) {
        $sets = array_map(fn($col) => "$col = :$col", array_keys($data));
        
        $sql = sprintf(
            "UPDATE %s SET %s WHERE %s",
            $table,
            implode(', ', $sets),
            $where
        );
        
        $params = [];
        foreach ($data as $key => $value) {
            $params[":$key"] = $value;
        }
        $params = array_merge($params, $whereParams);
        
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    // Delete rows
    public function delete($table, $where, $params = []) {
        $sql = sprintf("DELETE FROM %s WHERE %s", $table, $where);
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    // Begin transaction
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }
    
    // Commit transaction
    public function commit() {
        return $this->db->commit();
    }
    
    // Rollback transaction
    public function rollback() {
        return $this->db->rollBack();
    }
}
