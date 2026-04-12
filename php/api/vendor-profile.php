<?php
// Vendor Profile API
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);
ob_clean();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

$token = getAuthToken();
if (!$token) { http_response_code(401); echo json_encode(['error'=>'Unauthorized']); exit; }
$payload = verifyJWT($token, JWT_SECRET);
if (!$payload || ($payload['type']??'') !== 'vendor') {
    http_response_code(401); echo json_encode(['error'=>'Invalid token']); exit;
}
$vendor_id = (int)$payload['vendor_id'];
$db = getDB();
$action = $_GET['action'] ?? '';
$input  = json_decode(file_get_contents('php://input'), true) ?? [];

// ── GET PROFILE ───────────────────────────────────────────────────────────────
if ($action === 'get') {
    $v = $db->fetchOne(
        "SELECT id,name,username,email,phone,business_name,status,created_at FROM vendors WHERE id=?",
        [$vendor_id]
    );
    if (!$v) sendError('Vendor not found', 404);
    sendJson(['vendor'=>$v]);
}

// ── UPDATE PROFILE ────────────────────────────────────────────────────────────
if ($action === 'update') {
    $data = [];
    foreach (['name','email','phone','business_name'] as $f)
        if (!empty($input[$f])) $data[$f] = sanitize($input[$f]);
    if (empty($data)) sendError('Nothing to update', 400);

    // Email uniqueness check
    if (isset($data['email'])) {
        $e = $db->fetchOne("SELECT id FROM vendors WHERE email=? AND id!=?", [$data['email'], $vendor_id]);
        if ($e) sendError('Email already in use', 409);
    }
    $db->update('vendors', $data, 'id=:id', [':id' => $vendor_id]);
    sendJson(['success'=>true, 'message'=>'Profile updated']);
}

// ── CHANGE PASSWORD ───────────────────────────────────────────────────────────
if ($action === 'change_password') {
    $current = $input['current_password'] ?? '';
    $new     = $input['new_password'] ?? '';
    if (!$current || !$new) sendError('Both current and new passwords required', 400);
    if (strlen($new) < 8)   sendError('New password must be at least 8 characters', 400);

    $v = $db->fetchOne("SELECT password_hash FROM vendors WHERE id=?", [$vendor_id]);
    if (!password_verify($current, $v['password_hash'])) sendError('Current password is incorrect', 401);

    $db->update('vendors', ['password_hash'=>password_hash($new, PASSWORD_DEFAULT)], 'id=:id', [':id' => $vendor_id]);
    sendJson(['success'=>true, 'message'=>'Password changed successfully']);
}

// ── SUMMARY (all counts in one call) ─────────────────────────────────────────
if ($action === 'summary') {
    $rooms   = $db->fetchOne("SELECT COUNT(*) as n FROM rooms WHERE vendor_id=?", [$vendor_id]);
    $avRooms = $db->fetchOne("SELECT COUNT(*) as n FROM rooms WHERE vendor_id=? AND is_available=1", [$vendor_id]);
    $cars    = $db->fetchOne("SELECT COUNT(*) as n FROM cars WHERE vendor_id=?", [$vendor_id]);
    $avCars  = $db->fetchOne("SELECT COUNT(*) as n FROM cars WHERE vendor_id=? AND is_available=1", [$vendor_id]);
    $stays   = $db->fetchOne("SELECT COUNT(*) as n FROM stays WHERE vendor_id=?", [$vendor_id]);
    $avStays = $db->fetchOne("SELECT COUNT(*) as n FROM stays WHERE vendor_id=? AND is_active=1", [$vendor_id]);
    // Bookings count
    $ids = $db->fetchAll("SELECT id FROM stays WHERE vendor_id=?", [$vendor_id]);
    $bookings = 0;
    if (!empty($ids)) {
        $idList = implode(',', array_column($ids,'id'));
        $b = $db->fetchOne("SELECT COUNT(*) as n FROM bookings WHERE service_type='stays' AND listing_id IN ($idList)");
        $bookings = (int)($b['n']??0);
    }
    sendJson([
        'rooms'       => (int)($rooms['n']??0),
        'rooms_avail' => (int)($avRooms['n']??0),
        'cars'        => (int)($cars['n']??0),
        'cars_avail'  => (int)($avCars['n']??0),
        'stays'       => (int)($stays['n']??0),
        'stays_avail' => (int)($avStays['n']??0),
        'bookings'    => $bookings,
    ]);
}

sendError('Invalid action', 400);
