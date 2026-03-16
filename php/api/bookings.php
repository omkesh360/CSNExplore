<?php
// Bookings API - Handle booking requests

require_once __DIR__ . '/../config.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = getDB();
    
    if ($method === 'GET') {
        // Get all bookings (admin only)
        require_once __DIR__ . '/../jwt.php';
        requireAdmin();
        
        // Get filter parameters
        $status = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';
        
        $sql = "SELECT * FROM bookings WHERE 1=1";
        $params = [];
        
        if ($status) {
            $sql .= " AND status = :status";
            $params[':status'] = $status;
        }
        
        if ($search) {
            $sql .= " AND (full_name LIKE :search OR email LIKE :search OR phone LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $bookings = $db->fetchAll($sql, $params);

        // Resolve listing_name from DB for bookings where it's missing but listing_id exists
        foreach ($bookings as &$booking) {
            if (empty($booking['listing_name']) && !empty($booking['listing_id'])) {
                $page = $booking['service_page'] ?? '';
                // Determine table from service_page
                if (strpos($page, 'stay') !== false) {
                    $table = 'stays'; $col = 'name';
                } elseif (strpos($page, 'car') !== false) {
                    $table = 'cars'; $col = 'name';
                } elseif (strpos($page, 'bike') !== false) {
                    $table = 'bikes'; $col = 'name';
                } elseif (strpos($page, 'restaurant') !== false) {
                    $table = 'restaurants'; $col = 'name';
                } elseif (strpos($page, 'attraction') !== false) {
                    $table = 'attractions'; $col = 'name';
                } elseif (strpos($page, 'bus') !== false) {
                    $table = 'buses'; $col = 'operator';
                } else {
                    continue;
                }
                $row = $db->fetchOne("SELECT $col FROM $table WHERE id = :id", [':id' => $booking['listing_id']]);
                if ($row) {
                    $booking['listing_name'] = $row[$col];
                }
            }
        }
        unset($booking);

        sendJson($bookings);
    }
    
    elseif ($method === 'POST') {
        // Create new booking (public endpoint)
        $input = getJsonInput();
        
        // Validate required fields
        $required = ['full_name', 'phone', 'email', 'booking_date', 'number_of_people'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                sendError("Missing required field: $field", 400);
            }
        }
        
        // Validate email format
        if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            sendError('Invalid email format', 400);
        }
        
        // Validate phone number (accepts country code + digits)
        if (!preg_match('/^\+?[0-9\s\-()]{7,25}$/', $input['phone'])) {
            sendError('Invalid phone number format', 400);
        }
        
        // Insert booking
        $id = $db->insert('bookings', [
            'full_name' => $input['full_name'],
            'phone' => $input['phone'],
            'email' => $input['email'],
            'booking_date' => $input['booking_date'],
            'number_of_people' => (int)$input['number_of_people'],
            'service_page' => $input['service_page'] ?? '',
            'listing_id' => $input['listing_id'] ?? null,
            'listing_name' => $input['listing_name'] ?? '',
            'status' => 'pending',
            'notes' => $input['notes'] ?? ''
        ]);
        
        sendJson([
            'success' => true,
            'message' => 'Booking request submitted successfully',
            'id' => $id
        ], 201);
    }
    
    elseif ($method === 'PUT') {
        // Update booking status (admin only)
        require_once __DIR__ . '/../jwt.php';
        requireAdmin();
        
        $input = getJsonInput();
        
        if (empty($input['id'])) {
            sendError('Missing booking ID', 400);
        }
        
        $updateData = [];
        
        if (isset($input['status'])) {
            if (!in_array($input['status'], ['pending', 'completed', 'cancelled'])) {
                sendError('Invalid status value', 400);
            }
            $updateData['status'] = $input['status'];
        }
        
        if (isset($input['notes'])) {
            $updateData['notes'] = $input['notes'];
        }
        
        if (empty($updateData)) {
            sendError('No fields to update', 400);
        }
        
        $updateData['updated_at'] = date('Y-m-d H:i:s');
        
        $db->update('bookings', $updateData, 'id = :id', [':id' => $input['id']]);
        
        sendJson(['success' => true, 'message' => 'Booking updated successfully']);
    }
    
    elseif ($method === 'DELETE') {
        // Delete booking (admin only)
        require_once __DIR__ . '/../jwt.php';
        requireAdmin();
        
        $id = $_GET['id'] ?? '';
        
        if (!$id) {
            sendError('Missing booking ID', 400);
        }
        
        $db->delete('bookings', 'id = :id', [':id' => $id]);
        
        sendJson(['success' => true, 'message' => 'Booking deleted successfully']);
    }
    
    else {
        sendError('Method not allowed', 405);
    }
    
} catch (Exception $e) {
    error_log('Bookings API error: ' . $e->getMessage());
    sendError('Internal server error: ' . $e->getMessage(), 500);
}
