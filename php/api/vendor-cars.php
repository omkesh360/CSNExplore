<?php
// Vendor Cars Management API
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
    $total = $db->fetchOne("SELECT COUNT(*) as count FROM cars WHERE vendor_id = ?", [$vendor_id]);
    $available = $db->fetchOne("SELECT COUNT(*) as count FROM cars WHERE vendor_id = ? AND is_available = 1", [$vendor_id]);
    
    sendJson([
        'total' => $total['count'] ?? 0,
        'available' => $available['count'] ?? 0
    ]);
}

// ── LIST CARS ────────────────────────────────────────────────────────────────
if ($action === 'list') {
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
    
    $cars = $db->fetchAll("
        SELECT * FROM cars
        WHERE vendor_id = ?
        ORDER BY created_at DESC
        LIMIT $limit
    ", [$vendor_id]);
    
    sendJson(['cars' => $cars]);
}

// ── GET CAR ──────────────────────────────────────────────────────────────────
if ($action === 'get') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) sendError('Car ID required', 400);
    
    $car = $db->fetchOne("SELECT * FROM cars WHERE id = ? AND vendor_id = ?", [$id, $vendor_id]);
    if (!$car) sendError('Car not found', 404);
    
    sendJson(['car' => $car]);
}

// ── CREATE CAR ───────────────────────────────────────────────────────────────
if ($action === 'create') {
    $input = getJsonInput();
    
    $name = sanitize($input['name'] ?? '');
    $type = sanitize($input['type'] ?? '');
    $location = sanitize($input['location'] ?? '');
    $description = sanitize($input['description'] ?? '');
    $price_per_day = floatval($input['price_per_day'] ?? 0);
    $fuel_type = sanitize($input['fuel_type'] ?? '');
    $transmission = sanitize($input['transmission'] ?? '');
    $seats = intval($input['seats'] ?? 5);
    $image = sanitize($input['image'] ?? '');
    $gallery = $input['gallery'] ?? '';
    $features = $input['features'] ?? '';
    $map_embed = $input['map_embed'] ?? '';
    $is_available = isset($input['is_available']) ? (int)$input['is_available'] : 1;
    $is_active = isset($input['is_active']) ? (int)$input['is_active'] : 1;

    if (!$name || !$location) {
        sendError('Car name and location required', 400);
    }

    $id = $db->insert('cars', [
        'vendor_id' => $vendor_id,
        'name' => $name,
        'type' => $type,
        'location' => $location,
        'description' => $description,
        'price_per_day' => $price_per_day,
        'fuel_type' => $fuel_type,
        'transmission' => $transmission,
        'seats' => $seats,
        'image' => $image,
        'gallery' => $gallery,
        'features' => $features,
        'map_embed' => $map_embed,
        'is_available' => $is_available,
        'is_active' => $is_active,
        'rating' => 0,
        'reviews' => 0
    ]);

    sendJson(['success' => true, 'id' => $id, 'message' => 'Car created successfully']);
}

// ── UPDATE CAR ───────────────────────────────────────────────────────────────
if ($action === 'update') {
    $input = getJsonInput();
    $id = (int)($input['id'] ?? 0);
    
    if (!$id) sendError('Car ID required', 400);
    
    // Verify ownership
    $car = $db->fetchOne("SELECT id FROM cars WHERE id = ? AND vendor_id = ?", [$id, $vendor_id]);
    if (!$car) sendError('Car not found or access denied', 404);

    $data = [];
    if (isset($input['name'])) $data['name'] = sanitize($input['name']);
    if (isset($input['type'])) $data['type'] = sanitize($input['type']);
    if (isset($input['location'])) $data['location'] = sanitize($input['location']);
    if (isset($input['description'])) $data['description'] = sanitize($input['description']);
    if (isset($input['price_per_day'])) $data['price_per_day'] = floatval($input['price_per_day']);
    if (isset($input['fuel_type'])) $data['fuel_type'] = sanitize($input['fuel_type']);
    if (isset($input['transmission'])) $data['transmission'] = sanitize($input['transmission']);
    if (isset($input['seats'])) $data['seats'] = intval($input['seats']);
    if (isset($input['image'])) $data['image'] = sanitize($input['image']);
    if (isset($input['gallery'])) $data['gallery'] = $input['gallery'];
    if (isset($input['features'])) $data['features'] = $input['features'];
    if (isset($input['map_embed'])) $data['map_embed'] = $input['map_embed'];
    if (isset($input['is_available'])) $data['is_available'] = (int)$input['is_available'];
    if (isset($input['is_active'])) $data['is_active'] = (int)$input['is_active'];

    if (empty($data)) sendError('No data to update', 400);

    $db->update('cars', $data, 'id = :id', [':id' => $id]);
    sendJson(['success' => true, 'message' => 'Car updated successfully']);
}

// ── DELETE CAR ───────────────────────────────────────────────────────────────
if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) sendError('Car ID required', 400);
    
    // Verify ownership
    $car = $db->fetchOne("SELECT id FROM cars WHERE id = ? AND vendor_id = ?", [$id, $vendor_id]);
    if (!$car) sendError('Car not found or access denied', 404);

    $db->delete('cars', 'id = ?', [$id]);
    sendJson(['success' => true, 'message' => 'Car deleted successfully']);
}

// ── TOGGLE AVAILABILITY ──────────────────────────────────────────────────────
if ($action === 'toggle_availability') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) sendError('Car ID required', 400);
    
    // Verify ownership
    $car = $db->fetchOne("SELECT is_available FROM cars WHERE id = ? AND vendor_id = ?", [$id, $vendor_id]);
    if (!$car) sendError('Car not found or access denied', 404);

    $newStatus = $car['is_available'] ? 0 : 1;
    $db->update('cars', ['is_available' => $newStatus], 'id = :id', [':id' => $id]);
    
    sendJson(['success' => true, 'is_available' => $newStatus, 'message' => 'Availability updated']);
}

sendError('Invalid action', 400);
