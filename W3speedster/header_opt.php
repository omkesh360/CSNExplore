<?php
/**
 * Header Optimization File for W3speedster Plugin
 * 
 * This file handles header-level optimizations and cache management
 * for the W3speedster optimization plugin.
 * 
 * @package W3speedster
 * @version 9.7.2
 * @author W3speedster Team
 */

// Define constants if not already defined
if (!defined('W3SPEEDSTER_PATH')) {
    define('W3SPEEDSTER_PATH', __DIR__);
}

if (!defined('W3SPEEDSTER_DIR')) {
    define('W3SPEEDSTER_DIR', __DIR__ . DIRECTORY_SEPARATOR);
}

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__);
}

/**
 * Include cache management file if it exists
 * 
 * @return bool True if file was included successfully, false otherwise
 */
function includeCacheFile(): bool
{
    $cacheFilePath = W3SPEEDSTER_PATH . '/advanced-cache.php';
    
    if (file_exists($cacheFilePath) && is_readable($cacheFilePath)) {
        include_once $cacheFilePath;
        return true;
    }
    
    return false;
}

/**
 * Initialize header optimization
 * 
 * @return void
 */
function initializeHeaderOptimization(): void
{
	if(isset($_GET['error_show'])){
        ini_set('display_errors', 1);
    }
    // Include cache management
    includeCacheFile();
    
    ob_start();
}

// Initialize the header optimization
initializeHeaderOptimization();
