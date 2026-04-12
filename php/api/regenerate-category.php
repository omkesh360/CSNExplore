<?php
// Regenerate maps for a single category
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$category = $data['category'] ?? '';

$valid_categories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];
if (!in_array($category, $valid_categories)) {
    echo json_encode(['success' => false, 'error' => 'Invalid category']);
    exit;
}

try {
    $db = getDB();
    
    // Default map embed for Aurangabad
    $default_map = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d16426.329596104577!2d75.30037121099188!3d19.851617685624074!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bdb98f39ca15447%3A0x96c2632e2aaa42c!2sKamalnayan%20Bajaj%20Hospital!5e0!3m2!1sen!2sin!4v1775290483319!5m2!1sen!2sin" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
    
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
    }
    
    echo json_encode([
        'success' => true,
        'count' => $count,
        'category' => $category
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
