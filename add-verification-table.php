<?php
/**
 * One-time migration: ensures email_verification_tokens table exists
 * Run once: visit /add-verification-table.php?secret=csnexplore_seed
 */
require_once 'php/config.php';

$secret = $_GET['secret'] ?? '';
if ($secret !== 'csnexplore_seed') { http_response_code(403); die('Forbidden'); }

$db  = getDB();
$pdo = $db->getConnection();

$sql = "
CREATE TABLE IF NOT EXISTS `email_verification_tokens` (
  `id`         INT          NOT NULL AUTO_INCREMENT,
  `user_id`    INT          NOT NULL,
  `token_hash` VARCHAR(255) NOT NULL,
  `expires_at` DATETIME     NOT NULL,
  `created_at` DATETIME     DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_expires` (`expires_at`),
  CONSTRAINT `evt_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

try {
    $pdo->exec($sql);
    // Also delete the schema flag so initSchema re-runs on next request
    $flag = __DIR__ . '/cache/.schema_init';
    if (file_exists($flag)) unlink($flag);
    echo '<p style="color:green;font-family:monospace;font-size:16px">✅ email_verification_tokens table created (or already exists). Schema flag cleared.</p>';
    echo '<p style="font-family:monospace">You can delete this file now.</p>';
} catch (Exception $e) {
    echo '<p style="color:red;font-family:monospace">❌ Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
