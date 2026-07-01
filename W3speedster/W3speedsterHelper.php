<?php
/**
 * W3speedsterHelper - Main helper class for W3speedster optimization plugin
 * 
 * @package W3speedster
 * @version 9.7.2
 * @author W3speedster Team
 */


if (!defined('W3SPEEDSTER_PATH')) {
    define('W3SPEEDSTER_PATH', __DIR__);
}

if (!defined('W3SPEEDSTER_DIR')) {
    define('W3SPEEDSTER_DIR', __DIR__ . DIRECTORY_SEPARATOR);
}

if (!defined('W3SPEEDSTER_DOCUMENT_ROOT')) {
    define('W3SPEEDSTER_DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT'] ?? W3SPEEDSTER_PATH);
}

if (!defined('W3SPEEDSTER_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    define('W3SPEEDSTER_URL', $protocol . '://' . $host . '/W3speedster/');
}
if (!defined('W3SPEEDSTER_VERSION')) {
    define('W3SPEEDSTER_VERSION', '9.7.2');
}


// Include the configuration file
require_once W3SPEEDSTER_PATH . '/data/config.php';

// Check if the configuration constant is defined
if (defined('W3SPEEDSTER_CONFIG')) {

    if (!empty(W3SPEEDSTER_CONFIG['debug_show'])) {
        ini_set('display_errors', '1');
    }

    // Enable error logging if debug_log is enabled
    if (!empty(W3SPEEDSTER_CONFIG['debug_log'])) {
        ini_set('error_log', W3SPEEDSTER_CONFIG['default_error_log_path']);
        ini_set('log_errors', '1');
    }

    if (!empty(W3SPEEDSTER_CONFIG['storage_type']) && W3SPEEDSTER_CONFIG['storage_type'] == 'database') {
        require_once W3SPEEDSTER_PATH . '/includes/W3db.php';
    }
}

/**
 * W3speedster Autoloader
 * 
 * Automatically loads W3speedster classes based on namespace and file structure
 * 
 * @param string $className The fully qualified class name
 * @return void
 */
function w3speedsterAutoloader($className)
{
    // Only handle W3speedster classes
    if (strpos($className, 'W3speedster\\') !== 0) {
        return;
    }
    
    // Remove the W3speedster\ namespace prefix
    $relativeClass = substr($className, 12);
    
    // Convert namespace separators to directory separators
    $relativeClass = str_replace('\\', '/', $relativeClass);
    
    // Build the file path
    $file = W3SPEEDSTER_PATH . '/includes/' . $relativeClass . '.php';
    
    // Check if file exists and is readable
    if (file_exists($file) && is_readable($file)) {
        require_once $file;
        return;
    }
    
    // Try admin directory for admin classes
    $adminFile = W3SPEEDSTER_PATH . '/admin/' . $relativeClass . '.php';
    if (file_exists($adminFile) && is_readable($adminFile)) {
        require_once $adminFile;
        return;
    }
    
    // Try root directory for main classes
    $rootFile = W3SPEEDSTER_PATH . '/' . $relativeClass . '.php';
    if (file_exists($rootFile) && is_readable($rootFile)) {
        require_once $rootFile;
        return;
    }
    error_log("W3speedster: Autoloader could not find class file for: {$className}");
}

// Register the main W3speedster autoloader
spl_autoload_register('w3speedsterAutoloader');

/**
 * Main helper class for W3speedster optimization plugin
 */
class W3speedsterHelper
{
    /**
     * @var W3speedster\W3speedster
     */
    private $w3speedster;
    
    /**
     * @var array
     */

    private $allowed_actions = [
        'w3Call',
        'w3OptimizePage',
        'w3InsertSiteUrls',
        'w3ResetSinglePage',
        'w3speedsterPutData',
        'w3RestartOptimization',
        'w3SpeedsterGetLogData',
        'w3SpeedsterDeleteLogData',
        'w3_speedster_cache_purge',
        'hookBeforeStartOptimization',
        'w3SpeedsterGetChangeLogData',
        'w3_speedster_html_cache_purge',
        'w3SpeedsterShowUrlSuggestions',
        'w3speedsterActivateLicenseKey',
        'w3SpeedsterDeleteChangeLogData',
        'w3_speedster_critical_cache_purge',
        'w3speedster_export_settings',
        'w3speedster_import_settings',
    ];

    /**
     * Constructor - Initialize session and core objects
     */
    public function __construct()
    {
        if (!session_id()) {
            session_start();
        }
        
        // Initialize W3speedster instance
        try {
            $this->w3speedster = new W3speedster\W3speedster();
        } catch (Exception $e) {
            error_log("W3speedster: Failed to initialize W3speedster instance: " . $e->getMessage());
        }
    }

    /**
     * Main optimization method - handles all incoming requests
     *
     * @param string|null $html HTML content to optimize
     * @return mixed Result of optimization or action
     * @throws Exception When critical operations fail
     */
    public function optimize_call($html = null)
    {
        try {
            // Handle cron jobs
            if ($this->isCronRequest()) {
                return $this->handleCronRequest();
            }

            // Handle logout
            if ($this->isLogoutRequest()) {
                return $this->handleLogout();
            }

            // Handle action requests
            if ($this->hasActionRequest()) {
                return $this->handleActionRequest();
            }
            
            // Handle admin requests
            if ($this->isAdminRequest()) {
                $this->handleAdminRequest();
            }

            // Handle password reset requests
            if ($this->isPasswordResetRequest()) {
                return $this->handlePasswordResetRequest();
            }

            // Default: perform HTML optimization
            return $this->performHtmlOptimization($html);

        } catch (Exception $e) {
            error_log("W3speedster: Error in optimize_call: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if request is for cron jobs
     *
     * @return bool
     */
    private function isCronRequest(): bool
    {
        return !empty($_REQUEST['w3speedster_cron']);
    }

    /**
     * Handle cron job requests
     *
     * @return void
     */
    private function handleCronRequest(): void
    {
        // No need to manually require CronManager.php - autoloader will handle it
        try {
            $cronManager = new W3speedster\CronManager();
            $cronManager->runCronJobs();
        } catch (Exception $e) {
            error_log("W3speedster: Error in cron job: " . $e->getMessage());
        }
        exit;
    }

    /**
     * Check if request is for logout
     *
     * @return bool
     */
    private function isLogoutRequest(): bool
    {
        return !empty($_REQUEST['w3_logout']);
    }

    /**
     * Handle logout requests
     *
     * @return void
     */
    private function handleLogout(): void
    {
        session_unset();
        session_destroy();
        
        $protocol = $this->getCurrentProtocol();
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $full_url = $protocol . "://" . $host . $uri;
        
        header('Location: ' . $full_url);
        exit;
    }

    /**
     * Check if request is for admin access
     *
     * @return bool
     */
    private function isAdminRequest(): bool
    {
        return !empty($_REQUEST['admin']) && !empty($_REQUEST['page']) && $_REQUEST['page'] == 'w3_speedster';
    }

    /**
     * Handle admin requests
     *
     * @return void
     */
    private function handleAdminRequest(): void
    {   
        $this->loginRedirect();
        $this->includeFile('loginPage.php');
        exit;
    }

    /**
     * Check if request is for password reset
     *
     * @return bool
     */
    private function isPasswordResetRequest(): bool
    {
        return !empty($_REQUEST['w3_forgot_password']) || !empty($_REQUEST['w3_reset_password']);
    }

    /**
     * Handle password reset requests
     *
     * @return void
     */
    private function handlePasswordResetRequest(): void
    {   
        $this->loginRedirect();
        if (!empty($_REQUEST['w3_forgot_password'])) {
            $this->includeFile('ForgotPassword.php');
        } else {
            $this->includeFile('ResetPassword.php');
        }
    }

    /**
     * Check if request has an action parameter
     *
     * @return bool
     */
    private function hasActionRequest(): bool
    {
        return !empty($_REQUEST['action']);
    }

    /**
     * Handle action requests
     *
     * @return mixed
     */
    private function handleActionRequest()
    {
        $action = $this->sanitizeInput($_REQUEST['action']);
        
        if (!$this->w3speedster) {
            error_log("W3speedster: W3speedster instance not available");
            return false;
        }

        // Handle public actions (no login required)
        if ($action === 'w3speedsterPutData') {
            return $this->w3speedster->insertWebVitals();
        }

        if ($action === 'w3Call') {
            return $this->w3speedster->w3SaveDataWithAjax();
        }

        if ($action === 'w3OptimizePage') {
            return $this->w3speedster->w3OptimizeWithAi();
        }

        // Check login for protected actions
        if (!$this->checkLoggedIn()) {
            error_log("W3speedster: Unauthorized access attempt to action: {$action}");
            return false;
        }

        // Validate action
        if (!in_array($action, $this->allowed_actions, true)) {
            error_log("W3speedster: Invalid action requested: {$action}");
            return false;
        }

        return $this->executeAction($action);
    }

    /**
     * Execute the requested action
     *
     * @param string $action
     * @return mixed
     */
    private function executeAction(string $action)
    {
        try {
            switch ($action) {
                case 'w3SpeedsterGetLogData':
                    return $this->w3speedster->w3SpeedsterGetLogData();

                case 'w3speedster_export_settings':
                    $this->w3speedster->w3SpeedsterHandleExportSettings();

                case 'w3speedster_import_settings':
                    $this->w3speedster->w3SpeedsterImportSettings();
                    
                case 'w3SpeedsterDeleteLogData':
                    return $this->w3speedster->deleteWebVitals();
                    
                case 'hookBeforeStartOptimization':
                    $this->w3speedster->hookBeforeStartOptimization();
                    exit;
                    
                case 'w3SpeedsterShowUrlSuggestions':
                    $this->w3speedster->w3SpeedsterShowUrlSuggestions();
                    exit;
                    
                case 'w3SpeedsterGetChangeLogData':
                    return $this->w3speedster->w3SpeedsterGetChangeLogData();
                    
                case 'w3SpeedsterDeleteChangeLogData':
                    $this->w3speedster->w3SpeedsterDeleteChangeLogData();
                    exit;
                case 'w3InsertSiteUrls':
                    $this->w3speedster->w3InsertSiteUrls();
                    exit;
                case 'w3RestartOptimization':
                    $this->w3speedster->w3RestartOptimization();
                    exit;
                case 'w3ResetSinglePage':
                    $this->w3speedster->w3ResetSinglePageOptimiation();
                    exit;
                    
                case 'w3_speedster_cache_purge':
                    return $this->w3speedster->w3RemoveCacheByType('css-js') ? "Cache Flushed" : "Cache Not Flushed";
                    break;
              
                    
                case 'w3_speedster_html_cache_purge':
                    return $this->w3speedster->w3RemoveCacheByType('html') ? "Cache Flushed" : "Cache Not Flushed";
                    break;
                    
                case 'w3_speedster_critical_cache_purge':
                    return $this->w3speedster->w3RemoveCacheByType('critical') ? "Cache Flushed" : "Cache Not Flushed";
                    break;
                    
                case 'w3speedsterActivateLicenseKey':
                    echo $this->w3speedster->w3speedsterValidateLicenseKey();
                    exit;
                    
                default:
                    error_log("W3speedster: Unhandled action: {$action}");
                    return false;
            }
        } catch (Exception $e) {
            error_log("W3speedster: Error executing action {$action}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Perform HTML optimization
     *
     * @param string|null $html
     * @return mixed
     */
    private function performHtmlOptimization($html)
    {
        // No need to manually require files - autoloader will handle them
        try {
            $w3HtmlOptimize = new W3speedster\HtmlOptimize();
            return $w3HtmlOptimize->w3Speedster($html);
        } catch (Exception $e) {
            error_log("W3speedster: Error in HTML optimization: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if user is logged in
     *
     * @return bool
     */
    public function checkLoggedIn(): bool
    {
        return isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
    }

    /**
     * Redirect to admin page if already logged in
     *
     * @return void
     */
    public function loginRedirect(): void
    {
        if ($this->checkLoggedIn()) {
            try {
                $w3admin = new W3speedster\W3AdminInit();
                $w3admin->launch();
            } catch (Exception $e) {
                error_log("W3speedster: Error launching admin: " . $e->getMessage());
            }
            exit();
        }
    }

    /**
     * Get current protocol (HTTP or HTTPS)
     *
     * @return string
     */
    private function getCurrentProtocol(): string
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    }

    /**
     * Include a file safely
     *
     * @param string $filename
     * @return void
     */
    private function includeFile(string $filename): void
    {
        $filepath = W3SPEEDSTER_PATH . '/' . $filename;
        
        if (file_exists($filepath)) {
            include $filepath;
        } else {
            error_log("W3speedster: File not found: {$filepath}");
        }
    }

    /**
     * Sanitize input data
     *
     * @param mixed $input
     * @return mixed
     */
    private function sanitizeInput($input)
    {
        if (is_string($input)) {
            return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        }
        return $input;
    }
}
