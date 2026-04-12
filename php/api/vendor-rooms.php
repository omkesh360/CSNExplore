<?php
// Vendor Rooms Management API
require_once '../config.php';
require_once '../jwt.php';

header('Content-Type: application/json');
$db = getDB();

// Verify vendor authentication
$token = getAuthToken();
if (!$token) sendError('Unauthorized', 401);

$payload = verifyJWT($token, JWT_SECRET);
if (!$payload) sendError('Invalid or expired token', 401);

if (!isset($payload['type']) || $payload['type'] !== 'vendor') {
    sendError('Vendor access required', 403);
}

$vendor_id = $payload['vendor_id'];

// Verify vendor is active
$vendor = $db->fetchOne("SELECT status FROM vendors WHERE id = ?", [$vendor_id]);
if (!$vendor || $vendor['status'] !== 'active') {
    sendError('Vendor account inactive', 403);
}

$action = $_GET['action'] ?? '';

// ── STATS ────────────────────────────────────────────────────────────────────
if ($action === 'stats') {
    $total = $db->fetchOne("SELECT COUNT(*) as count FROM rooms WHERE vendor_id = ?", [$vendor_id]);
    $available = $db->fetchOne("SELECT COUNT(*) as count FROM rooms WHERE vendor_id = ? AND is_available = 1", [$vendor_id]);
    
    sendJson([
        'total' => $total['count'] ?? 0,
        'available' => $available['count'] ?? 0
    ]);
}

// ── LIST ROOM TYPES ──────────────────────────────────────────────────────────
if ($action === 'list') {
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
    
    $roomTypes = $db->fetchAll("
        SELECT rt.*, 
               (SELECT COUNT(*) FROM rooms WHERE room_type_id = rt.id) as rooms_count
        FROM room_types rt
        WHERE rt.vendor_id = ?
        ORDER BY rt.created_at DESC
        LIMIT $limit
    ", [$vendor_id]);
    
    sendJson(['rooms' => $roomTypes]);
}

// ── GET ROOM TYPE ────────────────────────────────────────────────────────────
if ($action === 'get') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) sendError('Room type ID required', 400);
    
    $roomType = $db->fetchOne("SELECT * FROM room_types WHERE id = ? AND vendor_id = ?", [$id, $vendor_id]);
    if (!$roomType) sendError('Room type not found', 404);
    
    // Get rooms for this type
    $rooms = $db->fetchAll("SELECT * FROM rooms WHERE room_type_id = ? ORDER BY room_number", [$id]);
    
    sendJson(['room_type' => $roomType, 'rooms' => $rooms]);
}

// ── CREATE ROOM TYPE ─────────────────────────────────────────────────────────
if ($action === 'create_type') {
    $input = getJsonInput();
    
    $name = sanitize($input['name'] ?? '');
    $description = sanitize($input['description'] ?? '');
    $base_price = floatval($input['base_price'] ?? 0);
    $max_guests = intval($input['max_guests'] ?? 2);
    $amenities = $input['amenities'] ?? '';
    $is_active = isset($input['is_active']) ? (int)$input['is_active'] : 1;

    if (!$name) sendError('Room type name required', 400);

    $id = $db->insert('room_types', [
        'vendor_id' => $vendor_id,
        'name' => $name,
        'description' => $description,
        'base_price' => $base_price,
        'max_guests' => $max_guests,
        'amenities' => $amenities,
        'is_active' => $is_active
    ]);

    sendJson(['success' => true, 'id' => $id, 'message' => 'Room type created successfully']);
}

// ── UPDATE ROOM TYPE ─────────────────────────────────────────────────────────
if ($action === 'update_type') {
    $input = getJsonInput();
    $id = (int)($input['id'] ?? 0);
    
    if (!$id) sendError('Room type ID required', 400);
    
    // Verify ownership
    $roomType = $db->fetchOne("SELECT id FROM room_types WHERE id = ? AND vendor_id = ?", [$id, $vendor_id]);
    if (!$roomType) sendError('Room type not found or access denied', 404);

    $data = [];
    if (isset($input['name'])) $data['name'] = sanitize($input['name']);
    if (isset($input['description'])) $data['description'] = sanitize($input['description']);
    if (isset($input['base_price'])) $data['base_price'] = floatval($input['base_price']);
    if (isset($input['max_guests'])) $data['max_guests'] = intval($input['max_guests']);
    if (isset($input['amenities'])) $data['amenities'] = $input['amenities'];
    if (isset($input['is_active'])) $data['is_active'] = (int)$input['is_active'];

    if (empty($data)) sendError('No data to update', 400);

    $db->update('room_types', $data, 'id = :id', [':id' => $id]);
    sendJson(['success' => true, 'message' => 'Room type updated successfully']);
}

// ── DELETE ROOM TYPE ─────────────────────────────────────────────────────────
if ($action === 'delete_type') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) sendError('Room type ID required', 400);
    
    // Verify ownership
    $roomType = $db->fetchOne("SELECT id FROM room_types WHERE id = ? AND vendor_id = ?", [$id, $vendor_id]);
    if (!$roomType) sendError('Room type not found or access denied', 404);
    
    // Check if there are rooms
    $rooms = $db->fetchOne("SELECT COUNT(*) as count FROM rooms WHERE room_type_id = ?", [$id]);
    if ($rooms['count'] > 0) {
        sendError('Cannot delete room type with existing rooms. Delete rooms first.', 400);
    }

    $db->delete('room_types', 'id = ?', [$id]);
    sendJson(['success' => true, 'message' => 'Room type deleted successfully']);
}

// ── CREATE ROOM ──────────────────────────────────────────────────────────────
if ($action === 'create_room') {
    $input = getJsonInput();
    
    $room_type_id = intval($input['room_type_id'] ?? 0);
    $room_number = sanitize($input['room_number'] ?? '');
    $floor = sanitize($input['floor'] ?? '');
    $price = floatval($input['price'] ?? 0);
    $is_available = isset($input['is_available']) ? (int)$input['is_available'] : 1;
    $status = $input['status'] ?? 'available';

    if (!$room_type_id || !$room_number) {
        sendError('Room type and room number required', 400);
    }

    // Verify room type ownership
    $roomType = $db->fetchOne("SELECT id FROM room_types WHERE id = ? AND vendor_id = ?", [$room_type_id, $vendor_id]);
    if (!$roomType) sendError('Room type not found or access denied', 404);

    $id = $db->insert('rooms', [
        'room_type_id' => $room_type_id,
        'vendor_id' => $vendor_id,
        'room_number' => $room_number,
        'floor' => $floor,
        'price' => $price,
        'is_available' => $is_available,
        'status' => $status
    ]);

    sendJson(['success' => true, 'id' => $id, 'message' => 'Room created successfully']);
}

// ── UPDATE ROOM ──────────────────────────────────────────────────────────────
if ($action === 'update_room') {
    $input = getJsonInput();
    $id = (int)($input['id'] ?? 0);
    
    if (!$id) sendError('Room ID required', 400);
    
    // Verify ownership
    $room = $db->fetchOne("SELECT id FROM rooms WHERE id = ? AND vendor_id = ?", [$id, $vendor_id]);
    if (!$room) sendError('Room not found or access denied', 404);

    $data = [];
    if (isset($input['room_number'])) $data['room_number'] = sanitize($input['room_number']);
    if (isset($input['floor'])) $data['floor'] = sanitize($input['floor']);
    if (isset($input['price'])) $data['price'] = floatval($input['price']);
    if (isset($input['is_available'])) $data['is_available'] = (int)$input['is_available'];
    if (isset($input['status'])) $data['status'] = $input['status'];

    if (empty($data)) sendError('No data to update', 400);

    $db->update('rooms', $data, 'id = :id', [':id' => $id]);
    sendJson(['success' => true, 'message' => 'Room updated successfully']);
}

// ── DELETE ROOM ──────────────────────────────────────────────────────────────
if ($action === 'delete_room') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) sendError('Room ID required', 400);
    
    // Verify ownership
    $room = $db->fetchOne("SELECT id FROM rooms WHERE id = ? AND vendor_id = ?", [$id, $vendor_id]);
    if (!$room) sendError('Room not found or access denied', 404);

    $db->delete('rooms', 'id = ?', [$id]);
    sendJson(['success' => true, 'message' => 'Room deleted successfully']);
}

sendError('Invalid action', 400);
