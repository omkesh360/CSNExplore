<?php
// Dynamic homepage content API

require_once __DIR__ . '/../config.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = getDB();
    
    if ($method === 'GET') {
        // JSON file is the source of truth (always written on save)
        $jsonData = readJsonFile('homepage-content.json');
        if (!empty($jsonData)) {
            sendJson($jsonData);
        }
        // Fallback to database if JSON file is empty
        $content = $db->fetchOne("SELECT content FROM homepage_content WHERE section = 'full_content' AND is_active = 1");
        if ($content) {
            $data = json_decode($content['content'], true);
            sendJson($data);
        }
        sendJson([]);
    }
    
    elseif ($method === 'PUT') {
        // Update homepage content (admin only)
        require_once __DIR__ . '/../jwt.php';
        requireAdmin();
        
        $input = getJsonInput();
        
        if (empty($input)) {
            sendError('No data provided', 400);
        }

        $jsonContent = json_encode($input);

        // Always write to JSON file so homepage always gets latest data
        writeJsonFile('homepage-content.json', $input);

        // Also save to database
        try {
            $db->initialize(); // ensure table exists
        } catch (Exception $e) {
            // ignore if already initialized
        }

        $existing = $db->fetchOne("SELECT id FROM homepage_content WHERE section = :section", [':section' => 'full_content']);
        
        if ($existing) {
            $db->update('homepage_content', [
                'content' => $jsonContent,
                'updated_at' => date('Y-m-d H:i:s')
            ], 'section = :section', [':section' => 'full_content']);
        } else {
            $db->insert('homepage_content', [
                'section' => 'full_content',
                'content' => $jsonContent,
                'display_order' => 1
            ]);
        }
        
        sendJson(['success' => true, 'message' => 'Homepage content updated successfully']);
    }
    
    else {
        sendError('Method not allowed', 405);
    }
    
} catch (Exception $e) {
    error_log('Homepage API error: ' . $e->getMessage());
    sendError('Internal server error: ' . $e->getMessage(), 500);
}
