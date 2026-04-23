<?php
/**
 * php/api/activity_log.php
 * Activity log API — GET (admin) and POST (internal logging)
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = getDB();

    // Ensure table exists
    _ensure_log_table($db);

    if ($method === 'DELETE') {
        requireAdmin();
        $db->query("DELETE FROM activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)");
        sendJson(['success' => true]);
    }

    elseif ($method === 'GET') {
        // Admin only
        requireAdmin();
        $page     = max(1, (int)($_GET['page'] ?? 1));
        $limit    = min(100, max(10, (int)($_GET['limit'] ?? 50)));
        $offset   = ($page - 1) * $limit;
        $type     = $_GET['type'] ?? '';
        $search   = $_GET['search'] ?? '';

        $where  = [];
        $params = [];
        if ($type)   { $where[] = 'action_type = ?'; $params[] = $type; }
        if ($search) { $where[] = '(actor_name LIKE ? OR description LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; }

        $whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $total    = $db->fetchOne("SELECT COUNT(*) as c FROM activity_logs $whereSQL", $params)['c'];
        $logs     = $db->fetchAll("SELECT * FROM activity_logs $whereSQL ORDER BY created_at DESC LIMIT $limit OFFSET $offset", $params);

        sendJson(['logs' => $logs, 'total' => (int)$total, 'page' => $page, 'limit' => $limit]);
    }

    elseif ($method === 'POST') {
        // Internal — accepts token OR no auth (for system events)
        $input = getJsonInput();
        $actor_id   = (int)($input['actor_id']   ?? 0);
        $actor_name = sanitize($input['actor_name'] ?? 'System');
        $actor_role = sanitize($input['actor_role'] ?? 'system');
        $action_type= sanitize($input['action_type'] ?? 'info');
        $description= sanitize($input['description'] ?? '');
        $meta       = isset($input['meta']) ? json_encode($input['meta']) : null;
        $ip         = $_SERVER['REMOTE_ADDR'] ?? '';

        if (!$description) sendError('description required', 400);

        $db->insert('activity_logs', [
            'actor_id'    => $actor_id ?: null,
            'actor_name'  => $actor_name,
            'actor_role'  => $actor_role,
            'action_type' => $action_type,
            'description' => $description,
            'meta'        => $meta,
            'ip_address'  => $ip,
        ]);
        sendJson(['success' => true]);
    }

    else {
        sendError('Not found', 404);
    }

} catch (Exception $e) {
    error_log('ActivityLog error: ' . $e->getMessage());
    sendError('Server error', 500);
}

function _ensure_log_table($db) {
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
}
