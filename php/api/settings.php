<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

// Verify admin access
try {
    requireAdmin();
} catch (Exception $e) {
    error_log('[Settings API] Auth error: ' . $e->getMessage());
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized: ' . $e->getMessage()]);
    exit;
}

$settingsFile = __DIR__ . '/../settings.json';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        if (file_exists($settingsFile)) {
            $content = file_get_contents($settingsFile);
            $settings = json_decode($content, true);
            if ($settings === null) {
                throw new Exception('Invalid JSON in settings file');
            }
        } else {
            $settings = [];
        }
        sendJson(['success' => true, 'settings' => $settings]);
    } catch (Exception $e) {
        error_log('[Settings API] GET error: ' . $e->getMessage());
        sendJson(['success' => false, 'error' => $e->getMessage()]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $input = getJsonInput();
        
        if (file_exists($settingsFile)) {
            $content = file_get_contents($settingsFile);
            $settings = json_decode($content, true);
            if ($settings === null) {
                $settings = [];
            }
        } else {
            $settings = [];
        }

        if (isset($input['features']['caching']['enabled'])) {
            if (!isset($settings['features'])) $settings['features'] = [];
            if (!isset($settings['features']['caching'])) $settings['features']['caching'] = [];
            $settings['features']['caching']['enabled'] = (bool)$input['features']['caching']['enabled'];
            
            // Clear cache if disabled
            if (!$settings['features']['caching']['enabled']) {
                try {
                    $db = getDB();
                    $db->clearCache();
                    error_log('[Settings API] Cache cleared successfully');
                } catch (Exception $e) {
                    error_log('[Settings API] Cache clear error: ' . $e->getMessage());
                    // Continue anyway - cache clear failure shouldn't block settings update
                }
            }
        }

        $result = file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT));
        if ($result === false) {
            throw new Exception('Failed to write settings file');
        }
        
        error_log('[Settings API] Settings updated successfully');
        sendJson(['success' => true, 'message' => 'Settings updated successfully']);
    } catch (Exception $e) {
        error_log('[Settings API] POST error: ' . $e->getMessage());
        sendJson(['success' => false, 'error' => $e->getMessage()]);
    }
}

sendError('Method not allowed', 405);
