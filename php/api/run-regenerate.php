<?php
ob_start(); // Prevent unexpected output breaking JSON
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Auth check
$token = getAuthToken();
if (!$token) { sendError('Unauthorized', 401); }
$payload = verifyJWT($token, JWT_SECRET);
if (!$payload || $payload['role'] !== 'admin') { sendError('Forbidden', 403); }

set_time_limit(300);

try {
    $db = getDB();
    $categories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];
    $total = 0;
    $breakdown = [];
    $log = [];

    define('SKIP_REGENERATE', true);
    require_once __DIR__ . '/../regenerate-complete.php';

    foreach ($categories as $category) {
        $listings = $db->fetchAll("SELECT * FROM {$category} WHERE is_active = 1");
        $count = 0;
        foreach ($listings as $listing) {
            $html = generateCompleteHTML($category, $listing, $db);
            $slug = generateSlug($category, $listing['id'], $listing['name'] ?? $listing['operator'] ?? 'item');
            $dir  = __DIR__ . '/../../listing-detail';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            file_put_contents($dir . '/' . $slug . '.html', $html);
            $count++;
            $total++;
        }
        $breakdown[$category] = $count;
        $log[] = "✓ {$category}: {$count} pages";
    }

    $output = ob_get_clean();
    if (trim($output)) {
        $log[] = "System notes: " . trim($output);
    }
    
    sendJson([
        'success'   => true,
        'total'     => $total,
        'breakdown' => $breakdown,
        'log'       => $log,
    ]);

} catch (Exception $e) {
    ob_end_clean();
    error_log('Regenerate error: ' . $e->getMessage());
    sendJson(['success' => false, 'error' => $e->getMessage()]);
}
