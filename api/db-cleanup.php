<?php
require_once dirname(__DIR__) . '/php/config.php';
$db = getDB();

header('Content-Type: application/json');

// This script should be protected via cron token in production
// For now we allow it for demonstration of the hygiene engine

try {
    $db->beginTransaction();

    // 1. Clear expired rate limits
    $db->exec("DELETE FROM rate_limits WHERE window_start < " . (time() - 3600));

    // 2. Clear old JSON cache files
    $cache_dir = dirname(__DIR__) . '/cache/db_query_cache';
    $cleared_files = 0;
    if (is_dir($cache_dir)) {
        foreach (glob("$cache_dir/*.json") as $file) {
            // Delete files older than 24 hours
            if (filemtime($file) < time() - 86400) {
                @unlink($file);
                $cleared_files++;
            }
        }
    }

    $db->commit();
    echo json_encode([
        'status' => 'success',
        'message' => 'Database hygiene complete.',
        'expired_rate_limits_cleared' => true,
        'cache_files_cleared' => $cleared_files
    ]);

} catch (Exception $e) {
    $db->rollBack();
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
