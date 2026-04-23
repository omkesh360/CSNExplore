<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';
require_once __DIR__ . '/../activity_logger.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

try {
    $admin = requireAdmin();
    $db    = getDB();

    if ($method === 'GET') {
        $search = $_GET['search'] ?? '';
        $sql    = "SELECT id, email, name, phone, role, is_verified, created_at FROM users";
        $params = [];
        if ($search) {
            $sql .= " WHERE name LIKE ? OR email LIKE ?";
            $params = ['%'.$search.'%', '%'.$search.'%'];
        }
        $sql .= " ORDER BY created_at DESC";
        sendJson($db->fetchAll($sql, $params));
    }

    elseif ($method === 'PUT' && $id) {
        $data = getJsonInput();
        $update = [];
        if (isset($data['role']) && in_array($data['role'], ['user','admin','vendor'])) {
            // Prevent removing last admin
            if ($data['role'] !== 'admin') {
                $adminCount = $db->fetchOne("SELECT COUNT(*) as c FROM users WHERE role = 'admin'")['c'];
                $thisUser   = $db->fetchOne("SELECT role FROM users WHERE id = ?", [$id]);
                if ($thisUser['role'] === 'admin' && $adminCount <= 1) {
                    sendError('Cannot remove the last admin', 400);
                }
            }
            $update['role'] = $data['role'];
        }
        if (isset($data['name']))  $update['name']  = sanitize($data['name']);
        if (isset($data['phone'])) $update['phone'] = sanitize($data['phone']);
        if (isset($data['is_verified'])) $update['is_verified'] = (int)$data['is_verified'] ? 1 : 0;
        $update['updated_at'] = date('Y-m-d H:i:s');
        $db->update('users', $update, 'id = :id', [':id' => $id]);
        $targetUser = $db->fetchOne("SELECT name, email FROM users WHERE id = ?", [$id]);
        $desc = 'Admin ' . ($admin['name'] ?? 'Admin') . ' updated user ' . ($targetUser['name'] ?? "#$id");
        if (isset($data['role'])) $desc .= " — role changed to {$data['role']}";
        if (isset($data['is_verified'])) $desc .= ' — verification status updated';
        log_activity('admin_user_update', $desc, ['target_id' => $id, 'changes' => array_keys($update)], (int)($admin['id'] ?? 0), 'admin', $admin['name'] ?? 'Admin');
        sendJson(['success' => true]);
    }

    elseif ($method === 'DELETE' && $id) {
        if ($id === (int)$admin['id']) sendError('Cannot delete your own account', 400);
        $adminCount = $db->fetchOne("SELECT COUNT(*) as c FROM users WHERE role = 'admin'")['c'];
        $thisUser   = $db->fetchOne("SELECT role FROM users WHERE id = ?", [$id]);
        if ($thisUser['role'] === 'admin' && $adminCount <= 1) sendError('Cannot delete last admin', 400);
        $db->delete('users', 'id = ?', [$id]);
        $desc = 'Admin ' . ($admin['name'] ?? 'Admin') . ' deleted user #' . $id;
        log_activity('admin_user_delete', $desc, ['deleted_id' => $id], (int)($admin['id'] ?? 0), 'admin', $admin['name'] ?? 'Admin');
        sendJson(['success' => true]);
    }

    else {
        sendError('Not found', 404);
    }

} catch (Exception $e) {
    error_log('Users error: ' . $e->getMessage());
    sendError('Server error', 500);
}
