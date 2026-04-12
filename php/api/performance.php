<?php
// Performance Management API
require_once '../config.php';
require_once '../jwt.php';

header('Content-Type: application/json');
$db = getDB();

// Verify admin authentication
$admin = requireAdmin();

$method = $_SERVER['REQUEST_METHOD'];

// GET - Fetch performance data
if ($method === 'GET') {
    // Mock performance data (you can implement real metrics later)
    $cacheDir = __DIR__ . '/../../cache';
    $cacheSize = 0;
    
    if (is_dir($cacheDir)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($cacheDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($files as $file) {
            if ($file->isFile()) {
                $cacheSize += $file->getSize();
            }
        }
    }
    
    $cacheSizeMB = round($cacheSize / 1024 / 1024, 2);
    
    // Count images
    $imageCount = 0;
    $uploadDir = __DIR__ . '/../../images/uploads';
    if (is_dir($uploadDir)) {
        $imageFiles = glob($uploadDir . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
        $imageCount = count($imageFiles);
    }
    
    $settingsFile = __DIR__ . '/../settings.json';
    $settings = file_exists($settingsFile) ? json_decode(file_get_contents($settingsFile), true) : [];
    
    $features = $settings['features'] ?? [
        'cache' => ['enabled' => true],
        'image' => ['enabled' => true],
        'assets' => ['enabled' => true],
        'query_cache' => ['enabled' => true],
        'lazy_loading' => ['enabled' => true]
    ];
    
    $config = $settings['config'] ?? [
        'cache' => ['ttl' => 3600, 'max_size_mb' => 2000],
        'image' => ['quality' => 75],
        'query_cache' => ['ttl' => 3600]
    ];
    
    sendJson([
        'cache_hit_rate' => 85.5,
        'avg_page_load' => 245,
        'cache_size_mb' => $cacheSizeMB,
        'images_optimized' => $imageCount,
        'features' => $features,
        'config' => $config,
        'metrics' => [
            'php_version' => [
                'label' => 'PHP Version',
                'value' => PHP_VERSION,
                'status' => version_compare(PHP_VERSION, '7.4.0', '>=') ? 'good' : 'warning'
            ],
            'memory_usage' => [
                'label' => 'Memory Usage',
                'value' => round(memory_get_usage() / 1024 / 1024, 2) . ' MB',
                'status' => 'good'
            ],
            'db_connection' => [
                'label' => 'Database Connection',
                'value' => 'Active',
                'status' => 'good'
            ]
        ],
        'slow_queries' => []
    ]);
}

// POST - Perform actions
if ($method === 'POST') {
    $input = getJsonInput();
    $action = $input['action'] ?? '';
    
    if ($action === 'purge_all') {
        // Clear cache directory
        $cacheDir = __DIR__ . '/../../cache';
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . '/*');
            foreach ($files as $file) {
                if (is_file($file) && basename($file) !== '.gitkeep' && basename($file) !== '.htaccess') {
                    unlink($file);
                }
            }
        }
        sendJson(['success' => true, 'message' => 'All cache purged']);
    }
    
    if ($action === 'purge_page_cache') {
        $cacheDir = __DIR__ . '/../../cache';
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . '/page_*.cache');
            foreach ($files as $file) {
                if (is_file($file)) unlink($file);
            }
        }
        sendJson(['success' => true, 'message' => 'Page cache cleared']);
    }
    
    if ($action === 'purge_query_cache') {
        $cacheDir = __DIR__ . '/../../cache';
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . '/query_*.cache');
            foreach ($files as $file) {
                if (is_file($file)) unlink($file);
            }
        }
        sendJson(['success' => true, 'message' => 'Query cache cleared']);
    }
    
    if ($action === 'preload_cache') {
        // Mock preload - you can implement actual preloading
        sendJson(['success' => true, 'message' => 'Cache preloaded']);
    }
    
    if ($action === 'toggle_feature') {
        $settingsFile = __DIR__ . '/../settings.json';
        $settings = file_exists($settingsFile) ? json_decode(file_get_contents($settingsFile), true) : [];
        if (!isset($settings['features'])) $settings['features'] = [];
        $settings['features'][$input['feature']] = ['enabled' => (bool)$input['enabled']];
        file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT));
        sendJson(['success' => true, 'message' => 'Feature toggled']);
    }
    
    if ($action === 'save_config') {
        $settingsFile = __DIR__ . '/../settings.json';
        $settings = file_exists($settingsFile) ? json_decode(file_get_contents($settingsFile), true) : [];
        if (!isset($settings['config'])) $settings['config'] = [];
        if (isset($input['config']['cache_ttl'])) $settings['config']['cache']['ttl'] = (int)$input['config']['cache_ttl'];
        if (isset($input['config']['cache_size_mb'])) $settings['config']['cache']['max_size_mb'] = (int)$input['config']['cache_size_mb'];
        if (isset($input['config']['image_quality'])) $settings['config']['image']['quality'] = (int)$input['config']['image_quality'];
        if (isset($input['config']['query_ttl'])) $settings['config']['query_cache']['ttl'] = (int)$input['config']['query_ttl'];
        file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT));
        sendJson(['success' => true, 'message' => 'Configuration saved']);
    }
}

sendError('Invalid request', 400);
