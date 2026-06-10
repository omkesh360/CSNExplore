<?php
/**
 * Cache Stats & Clear API
 * GET  → returns stats about all cache layers
 * DELETE → clears cache (all or specific type)
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . (defined('CORS_ORIGIN') ? CORS_ORIGIN : 'https://csnexplore.com'));
header('Vary: Origin');
header('Access-Control-Allow-Methods: GET, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

try {
    requireAdmin();
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$dbCacheDir      = __DIR__ . '/../../cache/db_query_cache/';
$listingHtmlDir  = __DIR__ . '/../../listing-detail/';
$blogHtmlDir     = __DIR__ . '/../../blogs/';

// ── GET: return stats ─────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $dbFiles   = countFiles($dbCacheDir, '*.json');
    $htmlFiles = countFiles($listingHtmlDir, '*.html');
    $blogFiles = countFiles($blogHtmlDir, '*.html');
    $sizeKb    = dirSizeKb($dbCacheDir);

    echo json_encode([
        'success'        => true,
        'db_cache_files' => $dbFiles,
        'html_files'     => $htmlFiles,
        'blog_files'     => $blogFiles,
        'cache_size_kb'  => $sizeKb,
    ]);
    exit;
}

// ── DELETE: clear cache ───────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $type  = $input['type'] ?? 'all';

    $cleared = 0;

    if ($type === 'all' || $type === 'db') {
        $cleared += clearDir($dbCacheDir, '*.json');
    }

    // For 'all', also bust the schema init flag so it re-checks on next request
    if ($type === 'all') {
        $schemaFlag = __DIR__ . '/../../cache/.schema_init';
        // Don't delete schema flag — that would re-run migrations. Just clear query cache.
    }

    echo json_encode([
        'success' => true,
        'cleared' => $cleared,
        'message' => "Cleared $cleared cache file(s)",
    ]);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);

// ── Helpers ───────────────────────────────────────────────────────────────────
function countFiles(string $dir, string $pattern): int {
    if (!is_dir($dir)) return 0;
    $files = glob($dir . $pattern);
    return $files ? count($files) : 0;
}

function dirSizeKb(string $dir): int {
    if (!is_dir($dir)) return 0;
    $size = 0;
    foreach (glob($dir . '*') as $file) {
        if (is_file($file)) $size += filesize($file);
    }
    return (int) round($size / 1024);
}

function clearDir(string $dir, string $pattern): int {
    if (!is_dir($dir)) return 0;
    $files = glob($dir . $pattern);
    if (!$files) return 0;
    $count = 0;
    foreach ($files as $file) {
        if (is_file($file) && @unlink($file)) $count++;
    }
    return $count;
}
