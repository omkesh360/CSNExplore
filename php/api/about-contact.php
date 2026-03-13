<?php
// About & Contact Pages API

require_once __DIR__ . '/../config.php';

$method = $_SERVER['REQUEST_METHOD'];
$section = $_GET['section'] ?? 'about';

try {
    $db = getDB();
    
    if ($method === 'GET') {
        // Get about/contact content from database
        $content = $db->fetchOne(
            "SELECT content FROM about_contact WHERE section = :section",
            [':section' => $section]
        );
        
        if ($content) {
            $data = json_decode($content['content'], true);
            sendJson($data);
        } else {
            // Return empty structure
            sendJson([]);
        }
    }
    
    elseif ($method === 'PUT') {
        // Update about/contact content (admin only)
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? '';
        $token = str_replace('Bearer ', '', $token);
        
        if (!$token) {
            sendError('Unauthorized', 401);
        }
        
        require_once __DIR__ . '/../jwt.php';
        $user = verifyToken();
        if (!isAdmin($user)) {
            sendError('Forbidden - Admin access required', 403);
        }
        
        $input = getJsonInput();
        $section = $input['section'] ?? 'about';
        $data = $input['data'] ?? [];
        
        // Check if content exists
        $existing = $db->fetchOne(
            "SELECT id FROM about_contact WHERE section = :section",
            [':section' => $section]
        );
        
        if ($existing) {
            // Update existing
            $db->update('about_contact', [
                'content' => json_encode($data),
                'updated_at' => date('Y-m-d H:i:s')
            ], 'section = :section', [':section' => $section]);
        } else {
            // Insert new
            $db->insert('about_contact', [
                'section' => $section,
                'content' => json_encode($data),
                'is_active' => 1
            ]);
        }
        
        sendJson(['message' => 'Content updated successfully']);
    }
    
    else {
        sendError('Method not allowed', 405);
    }
    
} catch (Exception $e) {
    error_log('About/Contact API error: ' . $e->getMessage());
    sendError('Internal server error: ' . $e->getMessage(), 500);
}
