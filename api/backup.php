<?php
// Automated Off-Site Backups Engine
require_once dirname(__DIR__) . '/php/config.php';

$backup_dir = dirname(__DIR__) . '/backups';
if (!is_dir($backup_dir)) @mkdir($backup_dir, 0755, true);

$source = dirname(__DIR__) . '/php/db.sqlite';
$timestamp = date('Y-m-d_H-i-s');
$dest = $backup_dir . "/backup_$timestamp.sqlite";

if (file_exists($source)) {
    // In a real environment, this would push to AWS S3 / Off-site
    copy($source, $dest);
    
    // Prune old backups (keep last 7 days)
    foreach (glob("$backup_dir/*.sqlite") as $file) {
        if (filemtime($file) < time() - (7 * 86400)) {
            @unlink($file);
        }
    }

    echo json_encode(['status' => 'success', 'backup' => basename($dest)]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database file not found']);
}
