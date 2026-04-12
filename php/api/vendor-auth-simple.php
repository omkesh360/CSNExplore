<?php
// Ultra-simple vendor auth — uses existing database + JWT
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);
ob_clean();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

$input  = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $_GET['action'] ?? '';

// ── LOGIN ────────────────────────────────────────────────────────────────────
if ($action === 'login') {
    $username = trim($input['username'] ?? '');
    $password = $input['password'] ?? '';

    if (!$username || !$password) {
        http_response_code(400);
        echo json_encode(['error' => 'Username and password required']);
        exit;
    }

    try {
        $pdo  = getDB()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM vendors WHERE username = ?");
        $stmt->execute([$username]);
        $vendor = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$vendor) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid username or password']);
            exit;
        }

        if ($vendor['status'] !== 'active') {
            http_response_code(403);
            echo json_encode(['error' => 'Account is inactive. Contact admin.']);
            exit;
        }

        if (!password_verify($password, $vendor['password_hash'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid username or password']);
            exit;
        }

        // Generate proper JWT (7 days) — compatible with vendor-rooms.php / vendor-cars.php
        $token = createJWT([
            'vendor_id' => (int)$vendor['id'],
            'username'  => $vendor['username'],
            'name'      => $vendor['name'],
            'type'      => 'vendor',
        ], JWT_SECRET, 7 * 24 * 3600);

        unset($vendor['password_hash']);
        echo json_encode(['success' => true, 'token' => $token, 'vendor' => $vendor]);
        exit;

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
        exit;
    }
}

// ── VERIFY ───────────────────────────────────────────────────────────────────
if ($action === 'verify') {
    $auth  = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    $token = str_replace('Bearer ', '', $auth);

    if (!$token) {
        http_response_code(401);
        echo json_encode(['error' => 'No token']);
        exit;
    }

    $payload = verifyJWT($token, JWT_SECRET);
    if (!$payload || ($payload['type'] ?? '') !== 'vendor') {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid or expired token']);
        exit;
    }

    echo json_encode(['success' => true, 'vendor' => $payload]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Invalid action']);
