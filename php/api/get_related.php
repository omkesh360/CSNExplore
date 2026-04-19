<?php
// get_related.php - Fetch related listings
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config.php';

try {
    $db = getDB();
    
    $type = $_GET['type'] ?? '';
    $exclude = intval($_GET['exclude'] ?? 0);
    $limit = intval($_GET['limit'] ?? 6);
    
    // Log the request
    file_put_contents(__DIR__ . '/../../logs/related_debug.log', date('Y-m-d H:i:s') . " - Request: type=$type, exclude=$exclude\n", FILE_APPEND);
    
    // Validate type
    $valid_types = ['stays', 'cars', 'bikes', 'attractions', 'restaurants', 'buses'];
    if (!in_array($type, $valid_types)) {
        throw new Exception('Invalid type');
    }
    
    // Build query - handle different column names per table
    $nameColumn = 'name';
    $priceColumn = 'price';
    $imageColumn = 'image';
    $locationColumn = 'location';
    
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
        $locationColumn = "CONCAT(from_location, ' to ', to_location)";
    } elseif ($type === 'attractions') {
        $priceColumn = 'entry_fee';
    } elseif ($type === 'restaurants') {
        $priceColumn = 'price_per_person';
    }
    
    // Fetch the source item to determine location and price context
    $source = $db->fetchOne("SELECT {$locationColumn} as location, {$priceColumn} as price FROM {$type} WHERE id = ?", [$exclude]);
    $srcLoc = strtolower(trim($source['location'] ?? ''));
    $srcPrice = floatval($source['price'] ?? 0);

    $sql = "SELECT id, {$nameColumn} as name, {$imageColumn} as image_url, rating, {$priceColumn} as price, {$locationColumn} as location
            FROM {$type} 
            WHERE is_active = 1 AND id != ?";
            
    $listings = $db->fetchAll($sql, [$exclude]);
    
    // Score listings for relevance
    foreach ($listings as &$listing) {
        $score = mt_rand(0, 10); // Base random element to shuffle equals
        
        $lstLoc = strtolower(trim($listing['location'] ?? ''));
        if ($srcLoc && $lstLoc && (strpos($lstLoc, $srcLoc) !== false || strpos($srcLoc, $lstLoc) !== false)) {
            $score += 50; 
        }
        
        $lstPrice = floatval($listing['price'] ?? 0);
        if ($srcPrice > 0 && $lstPrice > 0) {
            $diff = abs($srcPrice - $lstPrice);
            if ($diff <= ($srcPrice * 0.3)) {
                $score += 30; // Price within 30%
            }
        }
        
        $listing['_score'] = $score;
        
        // Fix image paths
        if (!empty($listing['image_url']) && strpos($listing['image_url'], 'http') !== 0 && strpos($listing['image_url'], '../') !== 0 && strpos($listing['image_url'], '/') !== 0) {
            $listing['image_url'] = '../' . $listing['image_url'];
        }
    }
    unset($listing);
    
    // Sort descending by score
    usort($listings, function($a, $b) {
        return $b['_score'] <=> $a['_score'];
    });
    
    // Remove internal score and slice
    $listings = array_slice($listings, 0, $limit);
    foreach ($listings as &$listing) {
        unset($listing['_score']);
    }
    unset($listing);
    
    echo json_encode([
        'success' => true,
        'listings' => $listings
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    $err = $e->getMessage();
    file_put_contents(__DIR__ . '/../../logs/related_debug.log', date('Y-m-d H:i:s') . " - ERROR: $err\n", FILE_APPEND);
    echo json_encode([
        'success' => false,
        'error' => $err
    ]);
}
