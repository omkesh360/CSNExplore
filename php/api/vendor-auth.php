<?php
// Vendor Authentication API
// Catch all errors and convert to JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Start output buffering to catch any accidental output
ob_start();

try {
    require_once '../config.php';
    require_once '../jwt.php';

    // Clear any output that might have been generated
    ob_clean();

    // Enable error logging for debugging
    error_log('[Vendor Auth] Request received: ' . $_SERVER['REQUEST_METHOD']);

    header('Content-Type: application/json');
    $db = getDB();
    $action = $_GET['action'] ?? '';

    error_log('[Vendor Auth] Action: ' . $action);

// ── LOGIN ────────────────────────────────────────────────────────────────────
if ($action === 'login') {
    $input = getJsonInput();
    $username = trim($input['username'] ?? '');
    $password = $input['password'] ?? '';

    error_log('[Vendor Auth] Login attempt for username: ' . $username);

    if (!$username || !$password) {
        error_log('[Vendor Auth] Missing username or password');
        sendError('Username and password required', 400);
    }

    $vendor = $db->fetchOne("SELECT * FROM vendors WHERE username = ?", [$username]);
    
    if (!$vendor) {
        error_log('[Vendor Auth] Vendor not found: ' . $username);
        sendError('Invalid username or password', 401);
    }

    error_log('[Vendor Auth] Vendor found: ' . $vendor['name'] . ' (Status: ' . $vendor['status'] . ')');

    if ($vendor['status'] !== 'active') {
        error_log('[Vendor Auth] Vendor account inactive');
        sendError('Your account is inactive. Please contact admin.', 403);
    }

    if (!password_verify($password, $vendor['password_hash'])) {
        error_log('[Vendor Auth] Invalid password');
        sendError('Invalid username or password', 401);
    }

    error_log('[Vendor Auth] Login successful for: ' . $username);

    // Generate JWT token using the same format as admin
    $token = createJWT([
        'vendor_id' => $vendor['id'],
        'username' => $vendor['username'],
        'type' => 'vendor'
    ], JWT_SECRET);

    unset($vendor['password_hash']);
    
    error_log('[Vendor Auth] Token generated, sending response');
    
    sendJson([
        'token' => $token,
        'vendor' => $vendor
    ]);
}

// ── VERIFY TOKEN ─────────────────────────────────────────────────────────────
if ($action === 'verify') {
    $token = getAuthToken();
    if (!$token) {
        error_log('[Vendor Auth] No token provided');
        sendError('No token provided', 401);
    }
    
    $payload = verifyJWT($token, JWT_SECRET);
    if (!$payload) {
        error_log('[Vendor Auth] Invalid token');
        sendError('Invalid or expired token', 401);
    }
    
    if (!isset($payload['type']) || $payload['type'] !== 'vendor') {
        error_log('[Vendor Auth] Invalid token type');
        sendError('Invalid token type', 401);
    }

    $vendor = $db->fetchOne("SELECT * FROM vendors WHERE id = ?", [$payload['vendor_id']]);
    if (!$vendor || $vendor['status'] !== 'active') {
        error_log('[Vendor Auth] Vendor not found or inactive');
        sendError('Vendor not found or inactive', 401);
    }

    unset($vendor['password_hash']);
    sendJson(['vendor' => $vendor]);
}

    error_log('[Vendor Auth] Invalid action: ' . $action);
    sendError('Invalid action', 400);

} catch (Exception $e) {
    // Clear any output
    ob_clean();
    
    // Log the error
    error_log('[Vendor Auth] Exception: ' . $e->getMessage());
    error_log('[Vendor Auth] Stack trace: ' . $e->getTraceAsString());
    
    // Send JSON error response
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error: ' . $e->getMessage(),
        'file' => basename($e->getFile()),
        'line' => $e->getLine()
    ]);
    exit;
}

// End output buffering
ob_end_flush();
