<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';
require_once __DIR__ . '/../rate-limiter.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['PATH_INFO'] ?? '/';

// Parse path to get category and ID
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

// Route handling
if ($method === 'GET' && !$id) {
    getListings($category, $adminCategories);
} elseif ($method === 'GET' && $id) {
    getListingById($category, $id, $adminCategories);
} elseif ($method === 'POST') {
    createListing($category, $validCategories);
} elseif ($method === 'PUT' && $id) {
    updateListing($category, $id);
} elseif ($method === 'DELETE' && $id) {
    deleteListing($category, $id);
} else {
    sendError('Not found', 404);
}

function getListings($category, $adminCategories) {
    // Admin-only categories require authentication
    if (in_array($category, $adminCategories)) {
        $user = verifyToken();
        if (!isAdmin($user)) {
            sendError('Access denied. Admin privileges required.', 403);
        }
    }
    
    $items = readJsonFile($category . '.json');
    sendJson($items);
}

function getListingById($category, $id, $adminCategories) {
    // Admin-only categories require authentication
    if (in_array($category, $adminCategories)) {
        $user = verifyToken();
        if (!isAdmin($user)) {
            sendError('Access denied. Admin privileges required.', 403);
        }
    }
    
    $items = readJsonFile($category . '.json');
    
    foreach ($items as $item) {
        if ($item['id'] === $id) {
            sendJson($item);
        }
    }
    
    sendError('Item not found', 404);
}

function createListing($category, $validCategories) {
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
    
    // Type conversions
    if (isset($newItem['rating'])) $newItem['rating'] = (float)$newItem['rating'];
    if (isset($newItem['price'])) $newItem['price'] = (float)$newItem['price'];
    if (isset($newItem['reviews'])) $newItem['reviews'] = (int)$newItem['reviews'];
    if (isset($newItem['entryFee'])) $newItem['entryFee'] = (int)$newItem['entryFee'];
    
    $newItem['id'] = (string)(time() * 1000);
    $newItem['createdAt'] = date('c');
    if (!isset($newItem['reviews'])) $newItem['reviews'] = 0;
    
    $items = readJsonFile($category . '.json');
    $items[] = $newItem;
    writeJsonFile($category . '.json', $items);
    
    sendJson(['message' => 'Item added successfully', 'item' => $newItem], 201);
}

function updateListing($category, $id) {
    requireAdmin();
    
    $validCategories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];
    
    if (!in_array($category, $validCategories)) {
        sendError('Invalid category');
    }
    
    $items = readJsonFile($category . '.json');
    $itemIndex = -1;
    
    foreach ($items as $index => $item) {
        if ($item['id'] === $id) {
            $itemIndex = $index;
            break;
        }
    }
    
    if ($itemIndex === -1) {
        sendError('Item not found', 404);
    }
    
    // Handle file upload
    $imageUrl = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageUrl = handleImageUpload($_FILES['image']);
    }
    
    // Get form data
    $updateData = $_POST;
    
    $updatedItem = array_merge($items[$itemIndex], $updateData);
    $updatedItem['id'] = $id;
    
    if ($imageUrl) {
        $updatedItem['image'] = $imageUrl;
    }
    
    // Type conversions
    if (isset($updatedItem['rating'])) $updatedItem['rating'] = (float)$updatedItem['rating'];
    if (isset($updatedItem['price'])) $updatedItem['price'] = (float)$updatedItem['price'];
    
    $items[$itemIndex] = $updatedItem;
    writeJsonFile($category . '.json', $items);
    
    sendJson(['message' => 'Item updated successfully', 'item' => $updatedItem]);
}

function deleteListing($category, $id) {
    requireAdmin();
    
    // Special handling for users and vendors
    if ($category === 'users') {
        deleteUser($id);
        return;
    }
    
    if ($category === 'vendors') {
        deleteVendor($id);
        return;
    }
    
    $validCategories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];
    
    if (!in_array($category, $validCategories)) {
        sendError('Invalid category');
    }
    
    $items = readJsonFile($category . '.json');
    $initialCount = count($items);
    
    $items = array_filter($items, function($item) use ($id) {
        return $item['id'] !== $id;
    });
    
    if (count($items) === $initialCount) {
        sendError('Item not found', 404);
    }
    
    $items = array_values($items);
    writeJsonFile($category . '.json', $items);
    
    sendJson(['message' => 'Item deleted successfully']);
}

function deleteUser($id) {
    $user = requireAdmin();
    
    $users = readJsonFile('users.json');
    
    $target = null;
    foreach ($users as $u) {
        if ($u['id'] === $id) {
            $target = $u;
            break;
        }
    }
    
    if (!$target) {
        sendError('User not found', 404);
    }
    
    // Prevent deleting self
    if ($user['id'] === $id) {
        sendError('You cannot delete your own account.');
    }
    
    // Filter out the user
    $remaining = array_filter($users, function($u) use ($id) {
        return $u['id'] !== $id;
    });
    
    // Prevent deleting the last admin
    $adminsLeft = 0;
    foreach ($remaining as $u) {
        if ($u['role'] === 'Admin') {
            $adminsLeft++;
        }
    }
    
    if ($target['role'] === 'Admin' && $adminsLeft === 0) {
        sendError('Cannot delete the last Admin account.');
    }
    
    $remaining = array_values($remaining);
    writeJsonFile('users.json', $remaining);
    
    sendJson(['success' => true, 'message' => 'User deleted successfully']);
}

function deleteVendor($id) {
    requireAdmin();
    
    $vendors = readJsonFile('vendors.json');
    $initialCount = count($vendors);
    
    $vendors = array_filter($vendors, function($v) use ($id) {
        return $v['id'] !== $id;
    });
    
    if (count($vendors) === $initialCount) {
        sendError('Vendor not found', 404);
    }
    
    $vendors = array_values($vendors);
    writeJsonFile('vendors.json', $vendors);
    
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
    
    return 'images/uploads/' . $filename;
}
