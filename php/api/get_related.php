<?php
// get_related.php - Fetch related listings
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config.php';

try {
    $db = getDB();
    
    $type = $_GET['type'] ?? '';
    $exclude = intval($_GET['exclude'] ?? 0);
    $limit = intval($_GET['limit'] ?? 4);
    
    // Validate type
    $valid_types = ['stays', 'cars', 'bikes', 'attractions', 'restaurants', 'buses'];
    if (!in_array($type, $valid_types)) {
        throw new Exception('Invalid type');
    }
    
    // Build query - handle different column names per table
    $nameColumn = 'name';
    $priceColumn = 'price';
    $imageColumn = 'image';
    
    // Map actual column names for each table
    if ($type === 'cars') {
        $priceColumn = 'price_per_day';
    } elseif ($type === 'bikes') {
        $priceColumn = 'price_per_day';
    } elseif ($type === 'stays') {
        $priceColumn = 'price_per_night';
    } elseif ($type === 'buses') {
        $nameColumn = 'operator';
        $priceColumn = 'price';
    } elseif ($type === 'attractions') {
        $priceColumn = 'entry_fee';
    } elseif ($type === 'restaurants') {
        $priceColumn = 'price_per_person';
    }
    
    $sql = "SELECT id, {$nameColumn} as name, {$imageColumn} as image_url, rating, {$priceColumn} as price
            FROM {$type} 
            WHERE is_active = 1 AND id != ? 
            ORDER BY RAND() 
            LIMIT {$limit}";
    
    $listings = $db->fetchAll($sql, [$exclude]);
    
    // Fix image paths for frontend rendering
    foreach ($listings as &$listing) {
        if (!empty($listing['image_url']) && strpos($listing['image_url'], 'http') !== 0 && strpos($listing['image_url'], '../') !== 0 && strpos($listing['image_url'], '/') !== 0) {
            $listing['image_url'] = '../' . $listing['image_url'];
        }
    }
    unset($listing);
    
    echo json_encode([
        'success' => true,
        'listings' => $listings
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
