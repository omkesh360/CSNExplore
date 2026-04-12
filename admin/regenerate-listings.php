<?php
// Regenerate all listing pages with maps and similar listings
require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/jwt.php';

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

try {
    $db = getDB();
    $categories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];
    
    // Default map embed for Aurangabad
    $default_map = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d16426.329596104577!2d75.30037121099188!3d19.851617685624074!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bdb98f39ca15447%3A0x96c2632e2aaa42c!2sKamalnayan%20Bajaj%20Hospital!5e0!3m2!1sen!2sin!4v1775290483319!5m2!1sen!2sin" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
    
    $total_updated = 0;
    $results = [];
    
    foreach ($categories as $category) {
        // Get all active listings without maps
        $listings = $db->fetchAll(
            "SELECT id FROM $category WHERE is_active = 1 AND (map_embed IS NULL OR map_embed = '')",
            []
        );
        
        $count = 0;
        foreach ($listings as $listing) {
            $id = $listing['id'];
            $db->update(
                $category,
                ['map_embed' => $default_map],
                'id = :id',
                [':id' => $id]
            );
            $count++;
            $total_updated++;
        }
        
        $results[$category] = $count;
    }
    
    echo json_encode([
        'success' => true,
        'message' => "Successfully updated $total_updated listings with default maps",
        'results' => $results,
        'total' => $total_updated
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
