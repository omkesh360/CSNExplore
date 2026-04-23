<?php
/**
 * php/activity_logger.php
 * Lightweight helper — call log_activity() from anywhere.
 *
 * Usage:
 *   require_once __DIR__ . '/activity_logger.php';
 *   log_activity('user_register', 'John Doe created an account', ['email'=>'john@x.com'], $userId, 'user');
 */

function log_activity(
    string $action_type,
    string $description,
    array  $meta      = [],
    int    $actor_id  = 0,
    string $actor_role= 'system',
    string $actor_name= ''
): void {
    try {
        // Resolve actor name from DB if not provided
        if (!$actor_name && $actor_id && function_exists('getDB')) {
            $u = getDB()->fetchOne("SELECT name, role FROM users WHERE id = ?", [$actor_id]);
            if ($u) {
                $actor_name = $u['name'];
                if (!$actor_role || $actor_role === 'system') $actor_role = $u['role'];
            }
        }
        if (!$actor_name) $actor_name = 'System';

        $db = getDB();

        // Ensure table exists (idempotent)
        static $tableChecked = false;
        if (!$tableChecked) {
            $db->getConnection()->exec("
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
            $tableChecked = true;
        }

        $db->insert('activity_logs', [
            'actor_id'    => $actor_id ?: null,
            'actor_name'  => $actor_name,
            'actor_role'  => $actor_role,
            'action_type' => $action_type,
            'description' => $description,
            'meta'        => $meta ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null,
            'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? '',
        ]);
    } catch (Throwable $e) {
        // Never crash the main request due to logging failure
        error_log('log_activity failed: ' . $e->getMessage());
    }
}
