<?php
// Dynamic Dashboard API

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = getDB();
    
    // Verify admin token
    $headers = getallheaders();
    $token = $headers['Authorization'] ?? '';
    $token = str_replace('Bearer ', '', $token);
    
    if (!$token) {
        sendError('Unauthorized', 401);
    }
    
    $decoded = verifyJWT($token, JWT_SECRET);
    if (!$decoded || ($decoded['role'] !== 'admin' && $decoded['role'] !== 'Admin')) {
        sendError('Forbidden - Admin access required', 403);
    }
    
    if ($method === 'GET') {
        // Get dashboard stats
        $stats = [
            'revenue' => [
                'total' => 125840,
                'change' => 12.5,
                'trend' => 'up'
            ],
            'bookings' => [
                'total' => $db->fetchOne("SELECT COUNT(*) as count FROM bookings")['count'] ?? 0,
                'change' => 8.2,
                'trend' => 'up'
            ],
            'users' => [
                'total' => $db->fetchOne("SELECT COUNT(*) as count FROM users")['count'] ?? 0,
                'change' => -2.4,
                'trend' => 'down'
            ],
            'listings' => [
                'stays' => $db->fetchOne("SELECT COUNT(*) as count FROM stays WHERE is_active = 1")['count'] ?? 0,
                'cars' => $db->fetchOne("SELECT COUNT(*) as count FROM cars WHERE is_active = 1")['count'] ?? 0,
                'bikes' => $db->fetchOne("SELECT COUNT(*) as count FROM bikes WHERE is_active = 1")['count'] ?? 0,
                'restaurants' => $db->fetchOne("SELECT COUNT(*) as count FROM restaurants WHERE is_active = 1")['count'] ?? 0,
                'attractions' => $db->fetchOne("SELECT COUNT(*) as count FROM attractions WHERE is_active = 1")['count'] ?? 0,
                'buses' => $db->fetchOne("SELECT COUNT(*) as count FROM buses WHERE is_active = 1")['count'] ?? 0
            ],
            'recentBookings' => $db->fetchAll("SELECT * FROM bookings ORDER BY created_at DESC LIMIT 10"),
            'topListings' => [
                'stays' => $db->fetchAll("SELECT * FROM stays WHERE is_active = 1 ORDER BY rating DESC LIMIT 5"),
                'restaurants' => $db->fetchAll("SELECT * FROM restaurants WHERE is_active = 1 ORDER BY rating DESC LIMIT 5")
            ]
        ];
        
        sendJson($stats);
    }
    
    elseif ($method === 'PUT') {
        // Update dashboard settings
        $input = getJsonInput();
        
        // Store dashboard settings in database
        $db->query("CREATE TABLE IF NOT EXISTS dashboard_settings (
            id INTEGER PRIMARY KEY,
            settings TEXT,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        $existing = $db->fetchOne("SELECT id FROM dashboard_settings WHERE id = 1");
        
        if ($existing) {
            $db->update('dashboard_settings', [
                'settings' => json_encode($input),
                'updated_at' => date('Y-m-d H:i:s')
            ], 'id = 1');
        } else {
            $db->insert('dashboard_settings', [
                'id' => 1,
                'settings' => json_encode($input)
            ]);
        }
        
        sendJson(['message' => 'Dashboard settings updated']);
    }
    
    else {
        sendError('Method not allowed', 405);
    }
    
} catch (Exception $e) {
    error_log('Dashboard API error: ' . $e->getMessage());
    sendError('Internal server error: ' . $e->getMessage(), 500);
}
