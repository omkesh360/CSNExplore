<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';
require_once __DIR__ . '/../services/EmailService.php';
require_once __DIR__ . '/../activity_logger.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

try {
    $db = getDB();

    // GET – admin only
    if ($method === 'GET') {
        requireAdmin();
        $where  = ['1=1'];
        $params = [];

        if (!empty($_GET['status'])) {
            $where[] = 'status = ?';
            $params[] = $_GET['status'];
        }
        if (!empty($_GET['search'])) {
            $where[] = '(full_name LIKE ? OR email LIKE ? OR phone LIKE ?)';
            $s = '%' . $_GET['search'] . '%';
            $params = array_merge($params, [$s, $s, $s]);
        }

        $sql = "SELECT * FROM bookings WHERE " . implode(' AND ', $where) . " ORDER BY created_at DESC";
        $bookings = $db->fetchAll($sql, $params);
        
        // Fetch listing images
        foreach ($bookings as &$b) {
            $table = $b['service_type'];
            $lid   = (int)$b['listing_id'];
            $b['listing_image'] = null;
            if ($table && $lid && in_array($table, ['stays','cars','bikes','restaurants','attractions','buses'])) {
                $item = $db->fetchOne("SELECT image FROM $table WHERE id = ?", [$lid]);
                if ($item) $b['listing_image'] = $item['image'];
            }
        }
        unset($b);
        sendJson($bookings);
    }

    // POST – public (create booking)
    elseif ($method === 'POST') {
        $data = getJsonInput();
        $name  = sanitize($data['full_name'] ?? '');
        $phone = sanitize($data['phone'] ?? '');
        // Extract email from payload, or fallback to JWT token
        $tokenPayload = null;
        $token = getAuthToken();
        if ($token) {
            $tokenPayload = verifyJWT($token, JWT_SECRET);
        }
        $email = strtolower(trim($data['email'] ?? ($tokenPayload['email'] ?? '')));

        if (!$name || !$phone) sendError('Name and phone are required', 400);
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) sendError('Invalid email', 400);

        $newId = $db->insert('bookings', [
            'full_name'        => $name,
            'phone'            => $phone,
            'email'            => $email,
            'booking_date'     => sanitize($data['booking_date'] ?? ''),
            'checkin_date'     => sanitize($data['checkin_date'] ?? ''),
            'checkout_date'    => sanitize($data['checkout_date'] ?? ''),
            'number_of_people' => max(1, (int)($data['number_of_people'] ?? 1)),
            'service_type'     => sanitize($data['service_type'] ?? ''),
            'listing_id'       => (int)($data['listing_id'] ?? 0) ?: null,
            'listing_name'     => sanitize($data['listing_name'] ?? ''),
            'notes'            => sanitize($data['notes'] ?? ''),
            'status'           => 'pending',
        ]);

        try {
            $emailResult = EmailService::sendBookingEmails($newId);
            error_log("Booking #{$newId} email status: " . json_encode($emailResult));
        } catch (Exception $e) {
            error_log("Booking #{$newId} email service error: " . $e->getMessage());
        }

        // Log the booking
        $actorId   = $tokenPayload ? (int)($tokenPayload['id'] ?? 0) : 0;
        $actorName = $tokenPayload ? ($tokenPayload['name'] ?? 'Guest') : ($name . ' (guest)');
        $actorRole = $tokenPayload ? ($tokenPayload['role'] ?? 'user') : 'guest';
        $svcType   = sanitize($data['service_type'] ?? '');
        $lstName   = sanitize($data['listing_name'] ?? '');
        log_activity(
            'booking_created',
            $actorName . ' made a booking' . ($lstName ? " for \"$lstName\"" : '') . ($svcType ? " ($svcType)" : ''),
            ['booking_id' => $newId, 'listing' => $lstName, 'service_type' => $svcType, 'phone' => $phone],
            $actorId, $actorRole, $actorName
        );

        sendJson(['success' => true, 'id' => $newId], 201);
    }

    // PUT – admin only (update status/notes)
    elseif ($method === 'PUT' && $id) {
        requireAdmin();
        $data   = getJsonInput();
        $update = [];
        $oldStatus = null;

        // Get current status before update
        $currentBooking = $db->fetchOne("SELECT status FROM bookings WHERE id = ?", [$id]);
        if ($currentBooking) {
            $oldStatus = $currentBooking['status'];
        }

        if (isset($data['status']) && in_array($data['status'], ['pending','completed','cancelled'])) {
            $update['status'] = $data['status'];
        }
        if (isset($data['notes'])) $update['notes'] = sanitize($data['notes']);
        $update['updated_at'] = date('Y-m-d H:i:s');

        $db->update('bookings', $update, 'id = :id', [':id' => $id]);
        
        // Log status change
        if (isset($update['status']) && $oldStatus !== $update['status']) {
            $admin = verifyToken();
            $booking = $db->fetchOne("SELECT full_name, listing_name, service_type FROM bookings WHERE id = ?", [$id]);
            $guestName = $booking['full_name'] ?? "Booking #$id";
            $lstName   = $booking['listing_name'] ?? '';
            log_activity(
                'booking_updated',
                ($admin['name'] ?? 'Admin') . " changed booking #$id ($guestName" . ($lstName ? " — $lstName" : '') . ") status: $oldStatus → {$update['status']}",
                ['booking_id' => $id, 'old_status' => $oldStatus, 'new_status' => $update['status']],
                (int)($admin['id'] ?? 0), $admin['role'] ?? 'admin', $admin['name'] ?? 'Admin'
            );
            if ($update['status'] === 'completed' || $update['status'] === 'cancelled') {
                try {
                    EmailService::sendStatusUpdateEmail($id, $update['status']);
                    error_log("Booking #{$id} status update email sent: {$update['status']}");
                } catch (Exception $e) {
                    error_log("Booking #{$id} status update email error: " . $e->getMessage());
                }
            }
        }
        
        sendJson(['success' => true]);
    }

    // DELETE – admin only
    elseif ($method === 'DELETE' && $id) {
        $admin = requireAdmin();
        $booking = $db->fetchOne("SELECT full_name, listing_name FROM bookings WHERE id = ?", [$id]);
        $db->delete('bookings', 'id = ?', [$id]);
        log_activity(
            'booking_deleted',
            ($admin['name'] ?? 'Admin') . " deleted booking #$id" . ($booking ? " ({$booking['full_name']}" . ($booking['listing_name'] ? " — {$booking['listing_name']}" : '') . ')' : ''),
            ['booking_id' => $id],
            (int)($admin['id'] ?? 0), $admin['role'] ?? 'admin', $admin['name'] ?? 'Admin'
        );
        sendJson(['success' => true]);
    }

    else {
        sendError('Not found', 404);
    }

} catch (Exception $e) {
    error_log('Bookings error: ' . $e->getMessage());
    sendError('Server error', 500);
}
