<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';
require_once __DIR__ . '/../rate-limiter.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    getHomepageContent();
} elseif ($method === 'PUT') {
    updateHomepageContent();
} else {
    sendError('Method not allowed', 405);
}

function getHomepageContent() {
    $content = readJsonFile('homepage-content.json');
    sendJson($content ?: new stdClass());
}

function updateHomepageContent() {
    requireAdmin();
    
    $content = getJsonInput();
    
    if (!$content || !is_array($content)) {
        sendError('Invalid content body');
    }
    
    writeJsonFile('homepage-content.json', $content);
    
    sendJson(['success' => true, 'message' => 'Homepage content updated successfully']);
}
