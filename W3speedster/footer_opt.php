<?php
/**
 * Footer Optimization File for W3speedster Plugin
 * 
 * This file handles footer-level optimizations and processes the buffered HTML
 * content through the W3speedster optimization engine.
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

/**
 * Get the buffered HTML content safely
 * 
 * @return string The buffered HTML content or empty string if none
 */
function getBufferedHtml(): string
{
    // Check if output buffering is active
    if (!ob_get_level()) {
        error_log('W3speedster: No output buffer active in footer optimization');
        return '';
    }
    
    // Get and clean the output buffer
    $html = ob_get_clean();
    
    // Validate HTML content
    if (!is_string($html)) {
        error_log('W3speedster: Invalid HTML content type in buffer');
        return '';
    }
    
    return $html;
}

/**
 * Include the W3speedsterHelper file safely
 * 
 * @return bool True if file was included successfully, false otherwise
 */
function includeW3speedsterHelper(): bool
{
    $helperPath = W3SPEEDSTER_PATH . '/W3speedsterHelper.php';
    // Validate file path
    if (empty($helperPath) || !is_string($helperPath)) {
        error_log('W3speedster: Invalid helper file path');
        return false;
    }
    
    // Check if file exists and is readable
    if (!file_exists($helperPath)) {
        error_log('W3speedster: Helper file not found: ' . $helperPath);
        return false;
    }
    
    if (!is_readable($helperPath)) {
        error_log('W3speedster: Helper file not readable: ' . $helperPath);
        return false;
    }
    
    // Include the helper file
    try {
        include $helperPath;
        return true;
    } catch (Exception $e) {
        $errorMessage = sprintf(
            'W3speedster: [%s] Exception in footer optimization (File: %s, Line: %d): %s',
            date('Y-m-d H:i:s'),
            $e->getFile(),
            $e->getLine(),
            $e->getMessage()
        );
        error_log($errorMessage);
        echo $html ?? '';
    } catch (Error $e) {
        $errorMessage = sprintf(
            'W3speedster: [%s] Fatal Error in footer optimization (File: %s, Line: %d): %s',
            date('Y-m-d H:i:s'),
            $e->getFile(),
            $e->getLine(),
            $e->getMessage()
        );
        error_log($errorMessage);
        echo $html ?? '';
    }
}

/**
 * Initialize and execute footer optimization
 * 
 * @return void
 */
function executeFooterOptimization(): void
{
    // try {
        $html = getBufferedHtml();
        if (!includeW3speedsterHelper()) {
            error_log('W3speedster: Failed to include helper file, outputting original HTML');
            echo $html;
            return;
        }
        
        if (!class_exists('W3speedsterHelper')) {
            error_log('W3speedster: W3speedsterHelper class not found');
            echo $html;
            return;
        }
        
        $optimizer = new W3speedsterHelper();
        if (!method_exists($optimizer, 'optimize_call')) {
            error_log('W3speedster: optimize_call method not found in W3speedsterHelper');
            echo $html;
            return;
        }

       
        $optimizedHtml = $optimizer->optimize_call($html);
        
        if ($optimizedHtml !== false && !empty($optimizedHtml)) {
            echo $optimizedHtml;
        } else {
            echo $html;
        }
        
    // } catch (Exception $e) {
    //     error_log('W3speedster: Exception in footer optimization: ' . $e->getMessage());
    //     echo $html ?? '';
    // } catch (Error $e) {
    //     error_log('W3speedster: Fatal error in footer optimization: ' . $e->getMessage());
    //     echo $html ?? '';
    // }
}

executeFooterOptimization();

