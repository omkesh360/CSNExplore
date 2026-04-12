<?php
// Vendor Stays Management API — vendor can manage hotel/stay property listings
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);
ob_clean();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

// Authenticate vendor
$token = getAuthToken();
if (!$token) { http_response_code(401); echo json_encode(['error'=>'Unauthorized']); exit; }

$payload = verifyJWT($token, JWT_SECRET);
if (!$payload || ($payload['type'] ?? '') !== 'vendor') {
    http_response_code(401); echo json_encode(['error'=>'Invalid token']); exit;
}

$vendor_id = (int)$payload['vendor_id'];
$db = getDB();

// Verify vendor active
$vendor = $db->fetchOne("SELECT id,name,status FROM vendors WHERE id = ?", [$vendor_id]);
if (!$vendor || $vendor['status'] !== 'active') {
    http_response_code(403); echo json_encode(['error'=>'Vendor account inactive']); exit;
}

$action = $_GET['action'] ?? '';
$input  = json_decode(file_get_contents('php://input'), true) ?? [];

// ── STATS ─────────────────────────────────────────────────────────────────────
if ($action === 'stats') {
    $total = $db->fetchOne("SELECT COUNT(*) as n FROM stays WHERE vendor_id=?", [$vendor_id]);
    $active = $db->fetchOne("SELECT COUNT(*) as n FROM stays WHERE vendor_id=? AND is_active=1", [$vendor_id]);
    sendJson(['total'=>(int)($total['n']??0), 'active'=>(int)($active['n']??0)]);
}

// ── LIST ──────────────────────────────────────────────────────────────────────
if ($action === 'list') {
    $limit = max(1, min(200, (int)($_GET['limit'] ?? 50)));
    $offset = max(0, (int)($_GET['offset'] ?? 0));
    $stays = $db->fetchAll(
        "SELECT id,name,type,location,price_per_night,rating,image,is_active,badge,created_at
         FROM stays WHERE vendor_id=? ORDER BY created_at DESC LIMIT $limit OFFSET $offset",
        [$vendor_id]
    );
    $total = $db->fetchOne("SELECT COUNT(*) as n FROM stays WHERE vendor_id=?", [$vendor_id]);
    sendJson(['stays'=>$stays, 'total'=>(int)($total['n']??0)]);
}

// ── GET SINGLE ────────────────────────────────────────────────────────────────
if ($action === 'get') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) sendError('Stay ID required', 400);
    $stay = $db->fetchOne("SELECT * FROM stays WHERE id=? AND vendor_id=?", [$id, $vendor_id]);
    if (!$stay) sendError('Not found', 404);
    sendJson(['stay'=>$stay]);
}

// ── CREATE ────────────────────────────────────────────────────────────────────
if ($action === 'create') {
    $name        = sanitize($input['name'] ?? '');
    $type        = sanitize($input['type'] ?? 'Hotel');
    $location    = sanitize($input['location'] ?? '');
    $description = sanitize($input['description'] ?? '');
    $price       = round((float)($input['price_per_night'] ?? 0), 2);
    $max_guests  = max(1, (int)($input['max_guests'] ?? 2));
    $amenities   = $input['amenities'] ?? '';
    $image       = sanitize($input['image'] ?? '');
    $badge       = sanitize($input['badge'] ?? '');
    $map_embed   = $input['map_embed'] ?? '';
    $is_active   = ($input['is_active'] ?? 1) ? 1 : 0;

    if (!$name || !$location) sendError('Name and location are required', 400);
    if ($price <= 0) sendError('Price must be greater than zero', 400);

    $id = $db->insert('stays', [
        'vendor_id'      => $vendor_id,
        'name'           => $name,
        'type'           => $type,
        'location'       => $location,
        'description'    => $description,
        'price_per_night'=> $price,
        'max_guests'     => $max_guests,
        'amenities'      => $amenities,
        'image'          => $image,
        'badge'          => $badge,
        'map_embed'      => $map_embed,
        'is_active'      => $is_active,
        'rating'         => 0,
        'reviews'        => 0,
        'display_order'  => 0,
    ]);
    sendJson(['success'=>true, 'id'=>$id, 'message'=>'Stay listing created successfully']);
}

// ── UPDATE ────────────────────────────────────────────────────────────────────
if ($action === 'update') {
    $id = (int)($input['id'] ?? 0);
    if (!$id) sendError('Stay ID required', 400);
    $stay = $db->fetchOne("SELECT id FROM stays WHERE id=? AND vendor_id=?", [$id, $vendor_id]);
    if (!$stay) sendError('Not found or access denied', 404);

    $data = [];
    foreach (['name','type','location','description','image','badge','map_embed'] as $f)
        if (isset($input[$f])) $data[$f] = sanitize($input[$f]);
    if (isset($input['price_per_night'])) $data['price_per_night'] = round((float)$input['price_per_night'], 2);
    if (isset($input['max_guests']))      $data['max_guests']      = max(1,(int)$input['max_guests']);
    if (isset($input['amenities']))       $data['amenities']       = $input['amenities'];
    if (isset($input['is_active']))       $data['is_active']       = $input['is_active'] ? 1 : 0;

    if (empty($data)) sendError('Nothing to update', 400);
    $db->update('stays', $data, 'id = :id', [':id' => $id]);
    sendJson(['success'=>true, 'message'=>'Stay updated successfully']);
}

// ── TOGGLE ACTIVE ─────────────────────────────────────────────────────────────
if ($action === 'toggle') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) sendError('Stay ID required', 400);
    $stay = $db->fetchOne("SELECT is_active FROM stays WHERE id=? AND vendor_id=?", [$id, $vendor_id]);
    if (!$stay) sendError('Not found', 404);
    $new = $stay['is_active'] ? 0 : 1;
    $db->update('stays', ['is_active'=>$new], 'id = :id', [':id' => $id]);
    sendJson(['success'=>true, 'is_active'=>$new, 'message'=>$new?'Listing activated':'Listing hidden']);
}

// ── DELETE ────────────────────────────────────────────────────────────────────
if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) sendError('Stay ID required', 400);
    $stay = $db->fetchOne("SELECT id FROM stays WHERE id=? AND vendor_id=?", [$id, $vendor_id]);
    if (!$stay) sendError('Not found or access denied', 404);
    $db->delete('stays', 'id = ?', [$id]);
    sendJson(['success'=>true, 'message'=>'Stay listing deleted']);
}

// ── BOOKINGS related to vendor stays ─────────────────────────────────────────
if ($action === 'bookings') {
    $limit = max(1, min(100, (int)($_GET['limit'] ?? 20)));
    $offset= max(0, (int)($_GET['offset'] ?? 0));
    // Get all stay IDs for this vendor
    $ids = $db->fetchAll("SELECT id FROM stays WHERE vendor_id=?", [$vendor_id]);
    if (empty($ids)) { sendJson(['bookings'=>[], 'total'=>0]); }
    $idList = implode(',', array_column($ids, 'id'));
    $bookings = $db->fetchAll(
        "SELECT * FROM bookings WHERE service_type='stays' AND listing_id IN ($idList)
         ORDER BY created_at DESC LIMIT $limit OFFSET $offset"
    );
    $total = $db->fetchOne(
        "SELECT COUNT(*) as n FROM bookings WHERE service_type='stays' AND listing_id IN ($idList)"
    );
    sendJson(['bookings'=>$bookings, 'total'=>(int)($total['n']??0)]);
}

sendError('Invalid action', 400);
