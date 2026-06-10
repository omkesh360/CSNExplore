<?php
/**
 * delete-sitemap.php
 * Deletes the sitemap.xml file from the website root
 * 
 * POST /php/api/delete-sitemap.php → requires admin JWT
 * Returns JSON: { success, message }
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . (defined('CORS_ORIGIN') ? CORS_ORIGIN : 'https://csnexplore.com'));
header('Vary: Origin');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { 
    http_response_code(200); 
    exit; 
}

// Require admin authentication
try { 
    requireAdmin(); 
} catch (Exception $e) { 
    sendError('Unauthorized', 401); 
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method not allowed', 405);
}

try {
    $root = dirname(__DIR__, 2);
    $sitemapPath = $root . '/sitemap.xml';
    
    // Check if sitemap exists
    if (!file_exists($sitemapPath)) {
        sendJson([
            'success' => true,
            'message' => 'Sitemap file does not exist',
            'already_deleted' => true
        ]);
    }
    
    // Delete the sitemap file
    if (@unlink($sitemapPath)) {
        // Log the deletion
        try {
            $db = getDB();
            $db->insert('activity_logs', [
                'actor_id'    => null,
                'actor_name'  => 'Admin',
                'actor_role'  => 'admin',
                'action_type' => 'system_config',
                'description' => 'Deleted sitemap.xml file',
                'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            ]);
        } catch (Exception $e) { 
            // Ignore logging errors
        }
        
        sendJson([
            'success' => true,
            'message' => 'Sitemap deleted successfully',
            'deleted_at' => date('Y-m-d H:i:s')
        ]);
    } else {
        throw new Exception('Failed to delete sitemap.xml - check file permissions');
    }
    
} catch (Exception $e) {
    error_log('Delete sitemap error: ' . $e->getMessage());
    sendError('Failed to delete sitemap: ' . $e->getMessage(), 500);
}
?>
