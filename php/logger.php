<?php
/**
 * Error and Activity Logger
 * Comprehensive logging system for debugging and monitoring
 */

class Logger {
    private static $logDir = __DIR__ . '/../logs';
    private static $maxFileSize = 10485760; // 10MB
    private static $maxFiles = 5;

    /**
     * Initialize logger
     */
    private static function init() {
        if (!file_exists(self::$logDir)) {
            mkdir(self::$logDir, 0755, true);
        }
    }

    /**
     * Write log entry
     */
    private static function write($level, $message, $context = []) {
        self::init();

        $timestamp = date('Y-m-d H:i:s');
        $logFile = self::$logDir . '/' . date('Y-m-d') . '.log';

        // Rotate log if too large
        if (file_exists($logFile) && filesize($logFile) > self::$maxFileSize) {
            self::rotateLog($logFile);
        }

        $contextStr = !empty($context) ? ' | Context: ' . json_encode($context) : '';
        $logEntry = "[{$timestamp}] [{$level}] {$message}{$contextStr}" . PHP_EOL;

        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Rotate log files
     */
    private static function rotateLog($logFile) {
        for ($i = self::$maxFiles - 1; $i > 0; $i--) {
            $oldFile = $logFile . '.' . $i;
            $newFile = $logFile . '.' . ($i + 1);
            
            if (file_exists($oldFile)) {
                if ($i === self::$maxFiles - 1) {
                    unlink($oldFile);
                } else {
                    rename($oldFile, $newFile);
                }
            }
        }

        if (file_exists($logFile)) {
            rename($logFile, $logFile . '.1');
        }
    }

    /**
     * Log info message
     */
    public static function info($message, $context = []) {
        self::write('INFO', $message, $context);
    }

    /**
     * Log warning message
     */
    public static function warning($message, $context = []) {
        self::write('WARNING', $message, $context);
    }

    /**
     * Log error message
     */
    public static function error($message, $context = []) {
        self::write('ERROR', $message, $context);
    }

    /**
     * Log critical error
     */
    public static function critical($message, $context = []) {
        self::write('CRITICAL', $message, $context);
    }

    /**
     * Log debug message
     */
    public static function debug($message, $context = []) {
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            self::write('DEBUG', $message, $context);
        }
    }

    /**
     * Log API request
     */
    public static function apiRequest($method, $endpoint, $data = []) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        self::info("API Request: {$method} {$endpoint}", [
            'ip' => $ip,
            'user_agent' => $userAgent,
            'data' => $data
        ]);
    }

    /**
     * Log authentication attempt
     */
    public static function authAttempt($email, $success, $reason = '') {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $status = $success ? 'SUCCESS' : 'FAILED';
        
        self::info("Auth Attempt: {$status} for {$email}", [
            'ip' => $ip,
            'reason' => $reason
        ]);
    }

    /**
     * Log database query error
     */
    public static function dbError($query, $error) {
        self::error("Database Error: {$error}", [
            'query' => $query
        ]);
    }

    /**
     * Log file operation
     */
    public static function fileOperation($operation, $file, $success) {
        $status = $success ? 'SUCCESS' : 'FAILED';
        self::info("File Operation: {$operation} on {$file} - {$status}");
    }

    /**
     * Get recent logs
     */
    public static function getRecentLogs($lines = 100) {
        self::init();
        
        $logFile = self::$logDir . '/' . date('Y-m-d') . '.log';
        
        if (!file_exists($logFile)) {
            return [];
        }

        $file = new SplFileObject($logFile);
        $file->seek(PHP_INT_MAX);
        $totalLines = $file->key();

        $startLine = max(0, $totalLines - $lines);
        $logs = [];

        $file->seek($startLine);
        while (!$file->eof()) {
            $line = trim($file->current());
            if (!empty($line)) {
                $logs[] = $line;
            }
            $file->next();
        }

        return $logs;
    }

    /**
     * Clear old logs
     */
    public static function clearOldLogs($days = 30) {
        self::init();
        
        $files = glob(self::$logDir . '/*.log*');
        $cutoff = time() - ($days * 86400);
        
        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                unlink($file);
            }
        }
    }
}
