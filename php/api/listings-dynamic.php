<?php
// Dynamic API endpoints using SQLite database

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';
require_once __DIR__ . '/../rate-limiter.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['PATH_INFO'] ?? '/';

// Parse path
$pathParts = array_filter(explode('/', $path));
$pathParts = array_values($pathParts);

$category = $pathParts[0] ?? null;
$id = $pathParts[1] ?? null;

$validCategories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];
$adminCategories = ['bookings', 'users', 'vendors'];
$allCategories = array_merge($validCategories, $adminCategories);

if (!$category || !in_array($category, $allCategories)) {
    sendError('Invalid category', 400);
}

try {
    $db = getDB();
    
    // Route handling
    if ($method === 'GET' && !$id) {
        getListings($db, $category, $adminCategories);
    } elseif ($method === 'GET' && $id) {
        getListingById($db, $category, $id, $adminCategories);
    } elseif ($method === 'POST') {
        createListing($db, $category, $validCategories);
    } elseif ($method === 'PUT' && $id) {
        updateListing($db, $category, $id);
    } elseif ($method === 'DELETE' && $id) {
        deleteListing($db, $category, $id);
    } else {
        sendError('Not found', 404);
    }
    
} catch (Exception $e) {
    error_log('Listings API error: ' . $e->getMessage());
    sendError('Internal server error: ' . $e->getMessage(), 500);
}

function getListings($db, $category, $adminCategories) {
    // Admin-only categories require authentication
    if (in_array($category, $adminCategories)) {
        $user = verifyToken();
        if (!isAdmin($user)) {
            sendError('Access denied. Admin privileges required.', 403);
        }
    }
    
    $where = ['is_active = 1'];
    $params = [];
    
    // Filters
    if (isset($_GET['location']) && $_GET['location']) {
        $where[] = 'location LIKE ?';
        $params[] = '%' . $_GET['location'] . '%';
    }
    
    if (isset($_GET['type']) && $_GET['type']) {
        $where[] = 'type = ?';
        $params[] = $_GET['type'];
    }
    
    // Price range
    $priceField = getPriceField($category);
    if (isset($_GET['minPrice']) && is_numeric($_GET['minPrice'])) {
        $where[] = "$priceField >= ?";
        $params[] = $_GET['minPrice'];
    }
    if (isset($_GET['maxPrice']) && is_numeric($_GET['maxPrice'])) {
        $where[] = "$priceField <= ?";
        $params[] = $_GET['maxPrice'];
    }
    
    // Rating filter
    if (isset($_GET['minRating']) && is_numeric($_GET['minRating'])) {
        $where[] = 'rating >= ?';
        $params[] = $_GET['minRating'];
    }
    
    // Search
    if (isset($_GET['search']) && $_GET['search']) {
        $where[] = '(name LIKE ? OR description LIKE ?)';
        $searchTerm = '%' . $_GET['search'] . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $whereClause = implode(' AND ', $where);
    $orderBy = 'rating DESC, reviews DESC';
    
    if (isset($_GET['sortBy'])) {
        switch ($_GET['sortBy']) {
            case 'price_asc':
                $orderBy = "$priceField ASC";
                break;
            case 'price_desc':
                $orderBy = "$priceField DESC";
                break;
            case 'rating':
                $orderBy = 'rating DESC';
                break;
            case 'name':
                $orderBy = 'name ASC';
                break;
        }
    }
    
    $sql = "SELECT * FROM $category WHERE $whereClause ORDER BY $orderBy";
    $items = $db->fetchAll($sql, $params);
    
    // Parse JSON fields
    $items = array_map(fn($item) => parseJsonFields($item, $category), $items);
    
    sendJson($items);
}

function getListingById($db, $category, $id, $adminCategories) {
    // Admin-only categories require authentication
    if (in_array($category, $adminCategories)) {
        $user = verifyToken();
        if (!isAdmin($user)) {
            sendError('Access denied. Admin privileges required.', 403);
        }
    }
    
    $item = $db->fetchOne("SELECT * FROM $category WHERE id = ? AND is_active = 1", [$id]);
    
    if (!$item) {
        sendError('Item not found', 404);
    }
    
    $item = parseJsonFields($item, $category);
    sendJson($item);
}

function createListing($db, $category, $validCategories) {
    $user = verifyToken();
    
    $listingCategories = $validCategories;
    $userCategories = ['bookings'];
    
    if (!in_array($category, $listingCategories) && !in_array($category, $userCategories)) {
        sendError('Invalid category or operation not allowed');
    }
    
    // Listing categories require admin
    if (in_array($category, $listingCategories) && !isAdmin($user)) {
        sendError('Admin privileges required', 403);
    }
    
    // Handle file upload
    $imageUrl = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageUrl = handleImageUpload($_FILES['image']);
    }
    
    // Get form data
    $newItem = $_POST;
    
    if ($imageUrl) {
        $newItem['image'] = $imageUrl;
    }
    
    // Prepare data for database
    $data = prepareDataForInsert($newItem, $category);
    
    // Insert into database
    $itemId = $db->insert($category, $data);
    
    // Fetch the created item
    $item = $db->fetchOne("SELECT * FROM $category WHERE id = ?", [$itemId]);
    $item = parseJsonFields($item, $category);
    
    sendJson(['message' => 'Item added successfully', 'item' => $item], 201);
}

function updateListing($db, $category, $id) {
    requireAdmin();
    
    $validCategories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];
    
    if (!in_array($category, $validCategories)) {
        sendError('Invalid category');
    }
    
    // Check if item exists
    $existing = $db->fetchOne("SELECT * FROM $category WHERE id = ?", [$id]);
    if (!$existing) {
        sendError('Item not found', 404);
    }
    
    // Handle file upload
    $imageUrl = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageUrl = handleImageUpload($_FILES['image']);
    }
    
    // Get form data
    $updateData = $_POST;
    
    if ($imageUrl) {
        $updateData['image'] = $imageUrl;
    }
    
    // Prepare data for database
    $data = prepareDataForInsert($updateData, $category);
    $data['updated_at'] = date('Y-m-d H:i:s');
    
    // Update in database
    $db->update($category, $data, 'id = ?', [':id' => $id]);
    
    // Fetch updated item
    $item = $db->fetchOne("SELECT * FROM $category WHERE id = ?", [$id]);
    $item = parseJsonFields($item, $category);
    
    sendJson(['message' => 'Item updated successfully', 'item' => $item]);
}

function deleteListing($db, $category, $id) {
    requireAdmin();
    
    // Special handling for users and vendors
    if ($category === 'users') {
        deleteUser($db, $id);
        return;
    }
    
    if ($category === 'vendors') {
        deleteVendor($db, $id);
        return;
    }
    
    $validCategories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];
    
    if (!in_array($category, $validCategories)) {
        sendError('Invalid category');
    }
    
    // Soft delete
    $deleted = $db->update($category, ['is_active' => 0], 'id = ?', [':id' => $id]);
    
    if ($deleted === 0) {
        sendError('Item not found', 404);
    }
    
    sendJson(['message' => 'Item deleted successfully']);
}

function deleteUser($db, $id) {
    $user = requireAdmin();
    
    $target = $db->fetchOne("SELECT * FROM users WHERE id = ?", [$id]);
    
    if (!$target) {
        sendError('User not found', 404);
    }
    
    // Prevent deleting self
    if ($user['id'] === $id) {
        sendError('You cannot delete your own account.');
    }
    
    // Check if last admin
    $admins = $db->fetchAll("SELECT * FROM users WHERE role = 'admin' AND id != ?", [$id]);
    
    if ($target['role'] === 'admin' && count($admins) === 0) {
        sendError('Cannot delete the last Admin account.');
    }
    
    $db->delete('users', 'id = ?', [$id]);
    
    sendJson(['success' => true, 'message' => 'User deleted successfully']);
}

function deleteVendor($db, $id) {
    requireAdmin();
    
    $deleted = $db->delete('vendors', 'id = ?', [$id]);
    
    if ($deleted === 0) {
        sendError('Vendor not found', 404);
    }
    
    sendJson(['success' => true, 'message' => 'Vendor deleted successfully']);
}

function handleImageUpload($file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    
    if (!in_array($file['type'], $allowedTypes)) {
        sendError('Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.');
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = time() . '-' . rand(100000000, 999999999) . '.' . $extension;
    $destination = UPLOADS_DIR . '/' . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        sendError('Failed to upload image', 500);
    }
    
    return '/images/uploads/' . $filename;
}

// Helper functions
function getPriceField($category) {
    $priceFields = [
        'stays' => 'price_per_night',
        'cars' => 'price_per_day',
        'bikes' => 'price_per_day',
        'restaurants' => 'price_per_person',
        'attractions' => 'entry_fee',
        'buses' => 'price'
    ];
    return $priceFields[$category] ?? 'price';
}

function parseJsonFields($item, $category) {
    if (!$item) return $item;
    
    // Convert price fields to camelCase
    $priceMapping = [
        'price_per_night' => 'pricePerNight',
        'price_per_day' => 'pricePerDay',
        'price_per_person' => 'pricePerPerson',
        'entry_fee' => 'entryFee'
    ];
    
    foreach ($priceMapping as $dbField => $jsField) {
        if (isset($item[$dbField])) {
            $item[$jsField] = (float)$item[$dbField];
        }
    }
    
    // Parse JSON fields
    if (isset($item['amenities']) && $item['amenities']) {
        $item['amenities'] = json_decode($item['amenities'], true);
    }
    if (isset($item['features']) && $item['features']) {
        $item['features'] = json_decode($item['features'], true);
    }
    
    // Convert numeric strings
    if (isset($item['rating'])) $item['rating'] = (float)$item['rating'];
    if (isset($item['reviews'])) $item['reviews'] = (int)$item['reviews'];
    if (isset($item['seats'])) $item['seats'] = (int)$item['seats'];
    if (isset($item['cc'])) $item['cc'] = (int)$item['cc'];
    if (isset($item['max_guests'])) $item['maxGuests'] = (int)$item['max_guests'];
    
    // CamelCase conversions
    if (isset($item['room_type'])) $item['roomType'] = $item['room_type'];
    if (isset($item['fuel_type'])) $item['fuelType'] = $item['fuel_type'];
    if (isset($item['bus_type'])) $item['busType'] = $item['bus_type'];
    if (isset($item['from_location'])) $item['from'] = $item['from_location'];
    if (isset($item['to_location'])) $item['to'] = $item['to_location'];
    if (isset($item['departure_time'])) $item['departureTime'] = $item['departure_time'];
    if (isset($item['arrival_time'])) $item['arrivalTime'] = $item['arrival_time'];
    if (isset($item['seats_available'])) $item['seatsAvailable'] = (int)$item['seats_available'];
    if (isset($item['opening_hours'])) $item['openingHours'] = $item['opening_hours'];
    if (isset($item['best_time'])) $item['bestTime'] = $item['best_time'];
    
    return $item;
}

function prepareDataForInsert($input, $category) {
    $data = [];
    
    // Common fields
    if (isset($input['name'])) $data['name'] = $input['name'];
    if (isset($input['type'])) $data['type'] = $input['type'];
    if (isset($input['location'])) $data['location'] = $input['location'];
    if (isset($input['description'])) $data['description'] = $input['description'];
    if (isset($input['rating'])) $data['rating'] = $input['rating'];
    if (isset($input['reviews'])) $data['reviews'] = $input['reviews'];
    if (isset($input['badge'])) $data['badge'] = $input['badge'];
    if (isset($input['image'])) $data['image'] = $input['image'];
    
    // Category-specific fields
    switch ($category) {
        case 'stays':
            if (isset($input['pricePerNight'])) $data['price_per_night'] = $input['pricePerNight'];
            if (isset($input['price'])) $data['price_per_night'] = $input['price'];
            if (isset($input['amenities'])) $data['amenities'] = is_array($input['amenities']) ? json_encode($input['amenities']) : $input['amenities'];
            if (isset($input['roomType'])) $data['room_type'] = $input['roomType'];
            if (isset($input['maxGuests'])) $data['max_guests'] = $input['maxGuests'];
            break;
            
        case 'cars':
            if (isset($input['pricePerDay'])) $data['price_per_day'] = $input['pricePerDay'];
            if (isset($input['price'])) $data['price_per_day'] = $input['price'];
            if (isset($input['features'])) $data['features'] = is_array($input['features']) ? json_encode($input['features']) : $input['features'];
            if (isset($input['fuelType'])) $data['fuel_type'] = $input['fuelType'];
            if (isset($input['transmission'])) $data['transmission'] = $input['transmission'];
            if (isset($input['seats'])) $data['seats'] = $input['seats'];
            break;
            
        case 'bikes':
            if (isset($input['pricePerDay'])) $data['price_per_day'] = $input['pricePerDay'];
            if (isset($input['price'])) $data['price_per_day'] = $input['price'];
            if (isset($input['features'])) $data['features'] = is_array($input['features']) ? json_encode($input['features']) : $input['features'];
            if (isset($input['fuelType'])) $data['fuel_type'] = $input['fuelType'];
            if (isset($input['cc'])) $data['cc'] = $input['cc'];
            break;
            
        case 'restaurants':
            if (isset($input['pricePerPerson'])) $data['price_per_person'] = $input['pricePerPerson'];
            if (isset($input['price'])) $data['price_per_person'] = $input['price'];
            if (isset($input['cuisine'])) $data['cuisine'] = $input['cuisine'];
            break;
            
        case 'attractions':
            if (isset($input['entryFee'])) $data['entry_fee'] = $input['entryFee'];
            if (isset($input['price'])) $data['entry_fee'] = $input['price'];
            if (isset($input['openingHours'])) $data['opening_hours'] = $input['openingHours'];
            if (isset($input['bestTime'])) $data['best_time'] = $input['bestTime'];
            break;
            
        case 'buses':
            if (isset($input['operator'])) $data['operator'] = $input['operator'];
            if (isset($input['busType'])) $data['bus_type'] = $input['busType'];
            if (isset($input['from'])) $data['from_location'] = $input['from'];
            if (isset($input['to'])) $data['to_location'] = $input['to'];
            if (isset($input['departureTime'])) $data['departure_time'] = $input['departureTime'];
            if (isset($input['arrivalTime'])) $data['arrival_time'] = $input['arrivalTime'];
            if (isset($input['duration'])) $data['duration'] = $input['duration'];
            if (isset($input['price'])) $data['price'] = $input['price'];
            if (isset($input['amenities'])) $data['amenities'] = is_array($input['amenities']) ? json_encode($input['amenities']) : $input['amenities'];
            if (isset($input['seatsAvailable'])) $data['seats_available'] = $input['seatsAvailable'];
            break;
    }
    
    return $data;
}
