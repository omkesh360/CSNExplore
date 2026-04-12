<?php
// Vendor Management API (Admin only)
require_once '../config.php';
require_once '../jwt.php';

header('Content-Type: application/json');
$db = getDB();

// Verify admin authentication
$admin = requireAdmin();

$action = $_GET['action'] ?? '';

// ── LIST VENDORS ─────────────────────────────────────────────────────────────
if ($action === 'list') {
    $vendors = $db->fetchAll("
        SELECT v.*, 
               (SELECT COUNT(*) FROM stays WHERE vendor_id = v.id) as stays_count,
               (SELECT COUNT(*) FROM cars WHERE vendor_id = v.id) as cars_count
        FROM vendors v 
        ORDER BY v.created_at DESC
    ");
    sendJson(['vendors' => $vendors]);
}

// ── GET VENDOR ───────────────────────────────────────────────────────────────
if ($action === 'get') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) sendError('Vendor ID required', 400);
    
    $vendor = $db->fetchOne("SELECT * FROM vendors WHERE id = ?", [$id]);
    if (!$vendor) sendError('Vendor not found', 404);
    
    unset($vendor['password_hash']);
    sendJson(['vendor' => $vendor]);
}

// ── CREATE VENDOR ────────────────────────────────────────────────────────────
if ($action === 'create') {
    $input = getJsonInput();
    
    $name = sanitize($input['name'] ?? '');
    $username = sanitize($input['username'] ?? '');
    $password = $input['password'] ?? '';
    $email = sanitize($input['email'] ?? '');
    $phone = sanitize($input['phone'] ?? '');
    $business_name = sanitize($input['business_name'] ?? '');
    $status = $input['status'] ?? 'active';

    if (!$name || !$username || !$password) {
        sendError('Name, username and password are required', 400);
    }

    // Check if username exists
    $existing = $db->fetchOne("SELECT id FROM vendors WHERE username = ?", [$username]);
    if ($existing) {
        sendError('Username already exists', 400);
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $id = $db->insert('vendors', [
        'name' => $name,
        'username' => $username,
        'password_hash' => $password_hash,
        'email' => $email,
        'phone' => $phone,
        'business_name' => $business_name,
        'status' => $status
    ]);

    sendJson(['success' => true, 'id' => $id, 'message' => 'Vendor created successfully']);
}

// ── UPDATE VENDOR ────────────────────────────────────────────────────────────
if ($action === 'update') {
    $input = getJsonInput();
    $id = (int)($input['id'] ?? 0);
    
    if (!$id) sendError('Vendor ID required', 400);
    
    $vendor = $db->fetchOne("SELECT id FROM vendors WHERE id = ?", [$id]);
    if (!$vendor) sendError('Vendor not found', 404);

    $data = [];
    if (isset($input['name'])) $data['name'] = sanitize($input['name']);
    if (isset($input['username'])) {
        $username = sanitize($input['username']);
        // Check if username is taken by another vendor
        $existing = $db->fetchOne("SELECT id FROM vendors WHERE username = ? AND id != ?", [$username, $id]);
        if ($existing) sendError('Username already exists', 400);
        $data['username'] = $username;
    }
    if (isset($input['email'])) $data['email'] = sanitize($input['email']);
    if (isset($input['phone'])) $data['phone'] = sanitize($input['phone']);
    if (isset($input['business_name'])) $data['business_name'] = sanitize($input['business_name']);
    if (isset($input['status'])) $data['status'] = $input['status'];
    
    // Update password if provided
    if (!empty($input['password'])) {
        $data['password_hash'] = password_hash($input['password'], PASSWORD_DEFAULT);
    }

    if (empty($data)) sendError('No data to update', 400);

    $db->update('vendors', $data, 'id = :id', [':id' => $id]);
    sendJson(['success' => true, 'message' => 'Vendor updated successfully']);
}

// ── DELETE VENDOR ────────────────────────────────────────────────────────────
if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) sendError('Vendor ID required', 400);
    
    // Check if vendor has listings
    $stays = $db->fetchOne("SELECT COUNT(*) as count FROM stays WHERE vendor_id = ?", [$id]);
    $cars = $db->fetchOne("SELECT COUNT(*) as count FROM cars WHERE vendor_id = ?", [$id]);
    
    if ($stays['count'] > 0 || $cars['count'] > 0) {
        sendError('Cannot delete vendor with active listings. Please reassign or delete listings first.', 400);
    }

    $db->delete('vendors', 'id = ?', [$id]);
    sendJson(['success' => true, 'message' => 'Vendor deleted successfully']);
}

// ── LOGIN AS VENDOR (IMPERSONATION) ───────────────────────────────────────────
if ($action === 'login_as') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) sendError('Vendor ID required', 400);
    
    $vendor = $db->fetchOne("SELECT * FROM vendors WHERE id = ?", [$id]);
    if (!$vendor) sendError('Vendor not found', 404);

    $token = createJWT([
        'vendor_id' => (int)$vendor['id'],
        'username'  => $vendor['username'],
        'name'      => $vendor['name'],
        'type'      => 'vendor',
    ], JWT_SECRET, 7 * 24 * 3600);

    unset($vendor['password_hash']);
    sendJson(['success' => true, 'token' => $token, 'vendor' => $vendor, 'message' => 'Logged in successfully']);
}

sendError('Invalid action', 400);
