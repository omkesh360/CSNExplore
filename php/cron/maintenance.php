<?php
/**
 * CSNExplore Maintenance Script
 * Handles log rotation and rate limit cleanup.
 * Run via cron daily: 0 0 * * * php /path/to/csnexplore/php/cron/maintenance.php
 */
declare(strict_types=1);

// Only allow CLI execution
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Forbidden. This script can only be run via CLI.');
}

$root = dirname(__DIR__, 2);
$logsDir = $root . '/logs';
$rateLimitDir = $logsDir . '/rate_limit';
$maxLogSize = 10 * 1024 * 1024; // 10MB

echo "Starting Maintenance Script...\n";

// 1. Log Rotation
$logsToRotate = [
    $logsDir . '/php_errors.log',
    $logsDir . '/related_debug.log'
];

foreach ($logsToRotate as $log) {
    if (file_exists($log)) {
        clearstatcache(true, $log);
        if (filesize($log) > $maxLogSize) {
            $backup = $log . '.' . date('Ymd_His') . '.bak';
            if (rename($log, $backup)) {
                echo "Rotated $log to $backup\n";
                file_put_contents($log, ''); // Create new empty file
                
                // Keep only the last 5 backups
                $backups = glob($log . '.*.bak');
                if ($backups && count($backups) > 5) {
                    sort($backups); // Oldest first
                    $toDelete = array_slice($backups, 0, count($backups) - 5);
                    foreach ($toDelete as $oldBackup) {
                        @unlink($oldBackup);
                        echo "Deleted old backup $oldBackup\n";
                    }
                }
            } else {
                echo "Failed to rotate $log\n";
            }
        }
    }
}

// 2. Rate Limit Cleanup
if (is_dir($rateLimitDir)) {
    $now = time();
    $files = glob($rateLimitDir . '/*.json');
    $deleted = 0;
    if ($files) {
        foreach ($files as $file) {
            if ($now - filemtime($file) > 86400) { // Older than 24 hours
                @unlink($file);
                $deleted++;
            }
        }
    }
    echo "Deleted $deleted old rate limit files.\n";
} else {
    echo "Rate limit directory does not exist or is empty.\n";
}

echo "Maintenance complete.\n";
