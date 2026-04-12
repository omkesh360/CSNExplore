<?php
// Update map embed for a listing
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

header('Content-Type: application/json');

// Check if user is admin
$token = getAuthToken();
if (!$token) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$payload = verifyJWT($token, JWT_SECRET);
if (!$payload || $payload['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['category'], $data['id'], $data['map_embed'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

$category = sanitize($data['category']);
$id = (int)$data['id'];
$map_embed = $data['map_embed'];

$valid_categories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];
if (!in_array($category, $valid_categories)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid category']);
    exit;
}

try {
    $db = getDB();
    $db->update(
        $category,
        ['map_embed' => $map_embed],
        'id = ?',
        [$id]
    );
    
    echo json_encode([
        'success' => true,
        'message' => 'Map embed updated successfully'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
