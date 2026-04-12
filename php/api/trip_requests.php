<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

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
        if ($id) {
            $req = $db->fetchOne("SELECT * FROM trip_requests WHERE id = ?", [$id]);
            sendJson($req ?: ['error' => 'Not found']);
        } else {
            $status = isset($_GET['status']) && $_GET['status'] !== 'all' ? $_GET['status'] : null;
            $search = $_GET['search'] ?? '';
            
            $sql = "SELECT id, full_name, phone, stay_type, travel_mode, status, created_at FROM trip_requests";
            $where = [];
            $params = [];
            
            if ($status) {
                $where[] = "status = ?";
                $params[] = $status;
            }
            if ($search) {
                $where[] = "(full_name LIKE ? OR phone LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            
            if ($where) {
                $sql .= " WHERE " . implode(' AND ', $where);
            }
            
            $sql .= " ORDER BY created_at DESC";
            
            sendJson($db->fetchAll($sql, $params));
        }
    }
    elseif ($method === 'PUT' && $id) {
        $data = getJsonInput();
        if (isset($data['status'])) {
            $db->update('trip_requests', ['status' => $data['status']], 'id = :id', [':id' => $id]);
            sendJson(['success' => true]);
        }
        sendError('Invalid input', 400);
    }
    elseif ($method === 'DELETE' && $id) {
        $db->delete('trip_requests', 'id = ?', [$id]);
        sendJson(['success' => true]);
    }
    else {
        sendError('Method not allowed or missing ID', 405);
    }

} catch (Exception $e) {
    error_log('Trip Planner API error: ' . $e->getMessage());
    sendError('Server error', 500);
}
