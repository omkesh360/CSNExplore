<?php
// Homepage content API - Fast JSON file approach

require_once __DIR__ . '/../config.php';

$method = $_SERVER['REQUEST_METHOD'];
$dataFile = __DIR__ . '/../../data/homepage-content.json';

try {
    if ($method === 'GET') {
        // Read directly from JSON file - fastest approach
        if (file_exists($dataFile)) {
            $data = json_decode(file_get_contents($dataFile), true);
            sendJson($data);
        } else {
            sendError('Homepage content not found', 404);
        }
    }
    
    elseif ($method === 'PUT') {
        // Save directly to JSON file - no database overhead
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? '';
        $token = str_replace('Bearer ', '', $token);
        
        if (!$token) {
            sendError('Unauthorized', 401);
        }
        
        require_once __DIR__ . '/../jwt.php';
        $decoded = verifyJWT($token, JWT_SECRET);
        
        if (!$decoded || strtolower($decoded['role']) !== 'admin') {
            sendError('Forbidden - Admin access required', 403);
        }
        
        $input = getJsonInput();
        
        // Write to JSON file immediately
        $jsonData = json_encode($input, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
        if (file_put_contents($dataFile, $jsonData) === false) {
            sendError('Failed to save homepage content', 500);
        }
        
        sendJson(['message' => 'Homepage content saved successfully', 'success' => true]);
    }
    
    else {
        sendError('Method not allowed', 405);
    }
    
} catch (Exception $e) {
    error_log('Homepage API error: ' . $e->getMessage());
    sendError('Internal server error: ' . $e->getMessage(), 500);
}
