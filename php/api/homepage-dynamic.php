<?php
// Dynamic homepage content API

require_once __DIR__ . '/../config.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = getDB();
    
    if ($method === 'GET') {
        // Get homepage content from database
        $content = $db->fetchOne("SELECT content FROM homepage_content WHERE section = 'full_content' AND is_active = 1");
        
        if ($content) {
            $data = json_decode($content['content'], true);
            sendJson($data);
        } else {
            // Fallback to JSON file if database is empty
            $data = readJsonFile('homepage-content.json');
            sendJson($data);
        }
    }
    
    elseif ($method === 'PUT') {
        // Update homepage content (admin only)
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? '';
        $token = str_replace('Bearer ', '', $token);
        
        if (!$token) {
            sendError('Unauthorized', 401);
        }
        
        require_once __DIR__ . '/../jwt.php';
        $decoded = verifyJWT($token);
        
        if (!$decoded || $decoded['role'] !== 'admin') {
            sendError('Forbidden - Admin access required', 403);
        }
        
        $input = getJsonInput();
        
        // Check if content exists
        $existing = $db->fetchOne("SELECT id FROM homepage_content WHERE section = 'full_content'");
        
        if ($existing) {
            // Update existing
            $db->update('homepage_content', [
                'content' => json_encode($input),
                'updated_at' => date('Y-m-d H:i:s')
            ], 'section = ?', [':section' => 'full_content']);
        } else {
            // Insert new
            $db->insert('homepage_content', [
                'section' => 'full_content',
                'content' => json_encode($input),
                'display_order' => 1
            ]);
        }
        
        sendJson(['message' => 'Homepage content updated successfully']);
    }
    
    else {
        sendError('Method not allowed', 405);
    }
    
} catch (Exception $e) {
    error_log('Homepage API error: ' . $e->getMessage());
    sendError('Internal server error: ' . $e->getMessage(), 500);
}
