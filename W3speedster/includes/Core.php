<?php
/**
 * Core Plugin Class
 *
 * This class serves as the central core of the W3speedster plugin, providing
 * essential functionality for settings management, caching, optimization,
 * and various utility methods. It handles plugin initialization, hooks,
 * and core operations.
 *
 * @package W3speedster
 * @author W3speedster Team
 */

namespace W3speedster;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Core Plugin Class
 *
 * Handles core plugin functionality including settings management,
 * caching operations, optimization features, and utility methods.
 * This class is the foundation for all other plugin components.
 */
class Core extends Functions {

	/**
	 * Plugin settings
	 *
	 * Stores all plugin configuration options and settings
	 *
	 * @var array
	 */
	public $settings;

	/**
	 * Additional settings
	 *
	 * Stores computed and derived settings, CDN configurations,
	 * cache paths, and other runtime data
	 *
	 * @var array
	 */
	public $addSettings;
	
	/**
	 * HTML content buffer
	 *
	 * Stores the processed HTML content during optimization
	 *
	 * @var string
	 */
	public $html = "";
    
    /**
     * HTTP status code
     *
     * @var string
     */
    public $statusCode = "";
    
    /**
     * Supported CDN content types
     *
     * @var array
     */
    public $cdnTypes = ['image', 'css', 'js', 'font', 'audio', 'video'];
    
    /**
     * Keys for .htaccess modifications
     *
     * @var array
     */
    public $htaccessModifyKeys = [];
    
    /**
     * Critical CSS data
     *
     * @var array
     */
    public $criticalData = [];
    
    /**
     * WebP image URLs for processing
     *
     * @var array
     */
    public $webpEnqueImageUrls = [];
    
    /**
     * Unique page token for caching
     *
     * @var string
     */
    public $pageToken = '';
    
    /**
     * Inline style background images
     *
     * @var array
     */
    public $inlineStyleBackgroundImages = [];
    
    /**
     * W3 data background load CSS
     *
     * @var string
     */
    public $w3DataBgLoadCss = '';

    /**
     * W3 database
     *
     * @var \W3speedster\Database
     */
    public $w3Database;

    /**
     * actionHeading
     *
     * @var array
     */
    public $actionHeading;
	/**
	 * Set additional settings
	 *
	 * Initializes computed settings, CDN configurations, cache paths,
	 * and other runtime data based on the main plugin settings.
	 * This method sets up all the derived configuration values.
	 */
	protected function setAdditionalSettings() {
		// Store GET parameters for later use
		$this->addSettings['w3_get'] = $this->verifyRequestValueArr($_GET); $this->addSettings['w3_post'] = $this->verifyRequestValueArr($_POST,false); $this->addSettings['w3_server'] = $this->verifyRequestValueArr($_SERVER);
        
        
        // Determine the secure protocol (https/http) for the current request
        $this->addSettings['secure'] = (isset($this->addSettings['siteUrl']) && strpos($this->addSettings['siteUrl'], 'https') !== false) ? 'https://' : (!empty( $this->addSettings['w3_server']['REQUEST_SCHEME']) ? $this->addSettings['w3_server']['REQUEST_SCHEME'] . '://' : 'http://');
        // Parse home URL for components
        $home_url_arr = $this->w3ParseUrl($this->addSettings['homeUrl']);
        
        // Convert CDN string settings to multi-CDN format
        $this->convertCdnStringSettingsToMultiCdn();
        
        // Process CDN settings and set up type-specific CDN URLs and exclude paths
        if (!empty($this->settings['cdn']) && is_array($this->settings['cdn'])) {
            foreach ($this->settings['cdn'] as $key => &$value) {
                $value = (array)$value;
                if (!empty($value['type'])) {
                    foreach (explode(',', $value['type']) as $type) {
                        $this->addSettings["{$type}_cdn_url"] = rtrim($value['url'], '/');
                        $this->addSettings["{$type}_exclude_cdn_path"] = $value['exclude_path'];
                    }
                }
            }
        }

        // Set main license key or generate demo key
        $this->addSettings['mainLicenseKey'] = !empty($this->settings['license_key']) ? $this->settings['license_key'] : 'w3demo-' . $home_url_arr['host'];
        
        // Process each CDN type and set up default values and URL arrays
        foreach ($this->cdnTypes as $type) {
            // Set default CDN URL if not specified
            $this->addSettings[$type.'_cdn_url'] = !empty($this->addSettings[$type.'_cdn_url']) ? $this->addSettings[$type.'_cdn_url'] : $this->addSettings['siteUrl'];
            
            // Parse exclude paths for each CDN type
            $this->addSettings[$type.'_exclude_cdn_path'] = !empty($this->addSettings[$type.'_exclude_cdn_path']) ? explode(',', str_replace(' ', '', $this->addSettings[$type.'_exclude_cdn_path'])) : [];
            
            // Parse CDN URL into components
            $this->addSettings[$type.'UrlArr'] = $this->w3ParseUrl($this->addSettings[$type.'_cdn_url']);
            
            // Combine host and path for CDN URL
            if(!empty($this->addSettings[$type.'UrlArr']['path'])){
                $this->addSettings[$type.'UrlArr']['host'] .= rtrim($this->addSettings[$type.'UrlArr']['path'], '/'); 
            }
        }
        $this->addSettings['w3ApiUrl'] = 'https://rest.w3speedster.com';
        $this->addSettings['HTTP_USER_AGENT'] = !empty($this->addSettings['w3_server']['HTTP_USER_AGENT']) ? $this->addSettings['w3_server']['HTTP_USER_AGENT'] : '';
        $this->addSettings['is_mobile'] = $this->is_mobile();
        $this->addSettings['full_url'] = !empty($this->addSettings['w3_server']['HTTP_HOST'] && !empty($this->addSettings['w3_server']['SCRIPT_URL'])) ? $this->addSettings['secure'] . $this->addSettings['w3_server']['HTTP_HOST'] . $this->addSettings['w3_server']['SCRIPT_URL'] . (empty($this->addSettings['w3_server']['QUERY_STRING']) ? '' : '?' . $this->addSettings['w3_server']['QUERY_STRING']) : $this->addSettings['siteUrl'] . $this->addSettings['w3_server']['REQUEST_URI'];
        $full_url_array = explode('?', $this->addSettings['full_url']);
        $this->addSettings['fullUrlWithoutParam'] = $full_url_array[0];
        $this->pageToken = md5(rtrim($this->addSettings['fullUrlWithoutParam'], '/'));
        $this->addSettings['cache_path'] = (!empty($this->settings['cache_path']) ? rtrim($this->settings['cache_path'], '/') : $this->addSettings['content_path'] . '/cache');
        $this->addSettings['rootCachePath'] = $this->addSettings['cache_path'] . '/w3-cache';
        $this->addSettings['criticalCssPath'] = $this->addSettings['cache_path'] . '/critical-css';
        $this->addSettings['cacheUrl'] = str_replace($this->addSettings['documentRoot'], '', $this->addSettings['rootCachePath']);
        $this->addSettings['uploadPath'] = $this->addSettings['content_path'];
        list($this->addSettings['upload_base_url'], $this->addSettings['upload_base_dir']) = $this->w3GetUploadsBasepath();
        $this->addSettings['webp_path'] = $this->addSettings['uploadPath'] . '/uploads/w3-webp';
        $this->addSettings['w3_rand_key'] = $this->w3GetOption('w3_rand_key', 0);
        if (empty($this->addSettings['w3_rand_key'])) {
            $this->w3CreateRandomKey();
            $this->addSettings['w3_rand_key'] = $this->w3GetOption('w3_rand_key', 0);
        }
        $this->addSettings['excludedImg'] = !empty($this->settings['exclude_lazy_load']) && is_array($this->settings['exclude_lazy_load']) ? $this->settings['exclude_lazy_load'] : array();
        $this->addSettings['excludedImg'] = array_merge($this->addSettings['excludedImg'], array('about:blank'));
        if ($this->addSettings['is_mobile']) {
            $this->addSettings['css_ext'] = 'mob.css';
            $this->addSettings['js_ext'] = 'mob.js';
            $this->addSettings['preload_css'] = !empty($this->settings['preload_css_mobile']) && is_array($this->settings['preload_css_mobile']) ? $this->settings['preload_css_mobile'] : array();
        } else {
            $this->addSettings['css_ext'] = '.css';
            $this->addSettings['js_ext'] = '.js';
            $this->addSettings['preload_css'] = !empty($this->settings['preload_css']) && is_array($this->settings['preload_css']) ? $this->settings['preload_css'] : array();
        }
        $this->addSettings['preload_css_url'] = [];
        $this->addSettings['headers'] = function_exists('getallheaders') ? getallheaders() : array();
        $this->addSettings['main_css_url'] = [];
        $this->addSettings['lazy_load_js'] = [];
        $this->addSettings['webp_enable'] = [];
        $this->addSettings['no_ratio_images'] = [];
        $this->addSettings['webp_enable_instance'] = array($this->addSettings['siteUrl'] . (str_replace($this->addSettings['documentRoot'], "", $this->addSettings['uploadPath'])));
        $this->addSettings['webp_enable_instance_replace'] = array($this->addSettings['siteUrl'] . '$w3$' . (str_replace($this->addSettings['documentRoot'], "", $this->addSettings['webp_path'])));
        if (!empty($this->addSettings['isMultisiteSubDomain'])) {
            $this->addSettings['webp_enable_instance'][] = $this->addSettings['network_site_url'] . (str_replace($this->addSettings['documentRoot'], "", $this->addSettings['uploadPath']));
            $this->addSettings['webp_enable_instance_replace'][] = $this->addSettings['network_site_url'] . '$w3$' . (str_replace($this->addSettings['documentRoot'], "", $this->addSettings['webp_path']));
        }
        if (!empty($this->addSettings['siteUrlDiff'])) {
            $this->addSettings['webp_enable_instance'][] = $this->addSettings['siteUrlDiff'] . (str_replace($this->addSettings['documentRoot'], "", $this->addSettings['uploadPath']));
            $this->addSettings['webp_enable_instance_replace'][] = $this->addSettings['siteUrlDiff'] . '$w3$' . (str_replace($this->addSettings['documentRoot'], "", $this->addSettings['webp_path']));
        }
        if (!empty($this->settings['webp_jpg'])) {
            $this->addSettings['webp_enable'] = array_merge($this->addSettings['webp_enable'], array('.jpg', '.jpeg'));
        }
        if (!empty($this->settings['webp_png'])) {
            $this->addSettings['webp_enable'] = array_merge($this->addSettings['webp_enable'], array('.png'));
        }
        $this->addSettings['htaccess'] = 0;
        if (file_exists($this->addSettings['documentRoot'] . "/.htaccess")) {
            $htaccess = $this->w3speedsterGetContents($this->addSettings['documentRoot'] . "/.htaccess");
            if (strpos($htaccess, 'W3WEBP') !== false) {
                $this->addSettings['htaccess'] = 1;
            }
        }
        $this->addSettings['critical_css'] = '';
        $this->addSettings['starttime'] = $this->microtime_float();
        $this->addSettings['w3UserLoggedIn'] = $this->w3UserLoggedIn();
        $this->addSettings['fonts_api_links'] = [];
        $this->addSettings['fonts_api_links_css2'] = [];
        $this->addSettings['preload_resources'] = [];
        $this->addSettings['preload_resources']['all'] = [];
        $this->addSettings['js_is_excluded'] = 0;
        $preventHtaccess = 0;

        if(method_exists($this, 'w3_prevent_generation_htaccess')) {
            $preventHtaccess = $this->w3_prevent_generation_htaccess($preventHtaccess);
        }

        $this->addSettings['wptouch'] = false;
        $this->addSettings['blank_image_url'] = $this->createBlankDataImage(16, 9);
        $this->addSettings['serverType'] = isset($this->addSettings['w3_server']['SERVER_SOFTWARE']) && stripos($this->addSettings['w3_server']['SERVER_SOFTWARE'], 'Apache') !== false ? 'apache' : '';
        $this->addSettings['disable_htaccess_webp'] = 0;
        $this->addSettings['advanced_cache_exist'] = 0;
        $this->addSettings['w3ResponsiveImgUrls'] = [];
        $this->addSettings['max_upload_size'] = 10;
        $this->addSettings['ABSPATH'] = defined('ABSPATH') ? rtrim(ABSPATH,'/') : $this->addSettings['documentRoot'];
        $this->copyAssets();
        $this->mergeInlineNExternalJavascript();
		$this->create_cache_directories();
	}

	/**
	 * Create cache directories
	 *
	 * Creates necessary cache directories if they don't exist.
	 * This includes the main cache directory, critical CSS directory,
	 * and WebP images directory.
	 */
	protected function create_cache_directories() {
		// Define required cache directories
		$directories = array(
			$this->addSettings['rootCachePath'],      // Main cache directory
			$this->addSettings['criticalCssPath'],   // Critical CSS cache directory
			$this->addSettings['webp_path'],         // WebP images cache directory
		);
		
		// Create each directory if it doesn't exist
		foreach ( $directories as $directory ) {
			if ( ! is_dir( $directory ) ) {
				$this->w3CreateFolder( $directory );
			}
		}
	}

	/**

	 * Check if user is logged in
	 *
	 *
	 * @return bool True if user is logged in, false otherwise
	 */
	public function is_user_logged_in() {
		return false;
	}

	/**
	 * Get settings
	 *
	 * Retrieves the main plugin settings array.
	 *
	 * @return array Plugin settings configuration
	 */
	public function get_settings() {
		return $this->settings;
	}

	/**
	 * Get cache path
	 *
	 * Retrieves the file system path for different cache types.
	 * Supports root cache, critical CSS, and WebP image caches.
	 *
	 * @param string $type Cache type: 'root', 'critical', or 'webp'
	 * @return string File system path to the cache directory
	 */
	public function get_cache_path( $type = 'root' ) {
		switch ( $type ) {
			case 'critical':
				return $this->addSettings['criticalCssPath'];
			case 'webp':
				return $this->addSettings['webp_path'];
			default:
				return $this->addSettings['rootCachePath'];
		}
	}

	/**
	 * Check W3 folder permission errors
	 *
	 * Verifies that all required W3speedster cache directories exist
	 * and are writable. Attempts to create directories if they don't exist.
	 * Returns an array of directories with permission issues.
	 *
	 * @return array Array of directory paths with permission errors
	 */
	function checkW3FolderPermissionErrors()
    {
        // Define required cache directories
        $w3Folders = [$this->addSettings['cache_path'], $this->addSettings['criticalCssPath'], $this->addSettings['webp_path']];
        $w3FolderErrors = [];
        
        // Check each directory for existence and writability
        foreach ($w3Folders as $value) {
            if(!$this->w3CreateFolder($value)){
                $w3FolderErrors[] = $value;
            }
        }
        return $w3FolderErrors;
    }

	/**
	 * Get license key
	 *
	 * Retrieves the plugin license key, optionally modified by external functions.
	 * Supports custom license key modification through the w3ModifyLicenseKey hook.
	 *
	 * @param string $url Optional URL parameter for license key modification
	 * @return string The license key (original or modified)
	 */
	function getLicenseKey($url = '')
    {
        $key = $this->addSettings['mainLicenseKey'];
        
        // Allow external modification of license key if function exists
        if (function_exists('w3ModifyLicenseKey')) {
            $key = w3ModifyLicenseKey($key, $url);
        }
        return $key;
    }

	/**
	 * Get errors
	 *
	 * Retrieves all stored error messages from the plugin options.
	 * Returns an empty array if no errors exist.
	 *
	 * @return array Array of error objects with 'type' and 'message' keys
	 */
	function w3GetErrors(){
        $errors = $this->w3GetOption('w3_errors', 1);
        return is_array($errors) ? $errors : [];
    }

	/**
	 * Add error
	 *
	 * Adds a new error message to the stored errors array.
	 * Errors are stored with a type (e.g., 'danger', 'warning') and message.
	 *
	 * @param string $type    Error type (e.g., 'danger', 'warning', 'info')
	 * @param string $message Error message text
	 */
	function w3AddError($type, $message){
        $errors = $this->w3GetOption('w3_errors', 1);
        $errors = is_array($errors) ? $errors : [];
        $errors[] = ['type' => $type, 'message' => $message];
        $this->w3UpdateOption('w3_errors', $errors);
    }

	/**
	 * Flush errors
	 *
	 * Clears all stored error messages by setting the errors option to an empty array.
	 * This is typically called after errors have been displayed to the user.
	 */
    function w3FlushErrors(){
        $this->w3UpdateOption('w3_errors', []);
    }

	/**
	 * Replace backslashes
	 *
	 * Recursively processes arrays and strings to replace double backslashes
	 * with single backslashes. This is useful for cleaning up file paths
	 * and other string data.
	 *
	 * @param mixed $value Value to process (can be array or string)
	 * @return mixed Processed value with corrected backslashes
	 */
	public function w3ReplaceBackslashes($value) {
        if (is_array($value)) {
            // Recursively process array elements
            foreach ($value as $key => $val) {
                $excludeKeys = ['custom_css','custom_javascript','custom_js','hook_pre_start_opt','hook_before_start_opt','hook_after_opt','hook_inner_js_customize','hook_inner_js_exclude','hook_internal_js_customize','hook_internal_css_customize','hook_internal_css_minify','hook_no_critical_css','hook_customize_critical_css','hook_disable_htaccess_webp','hook_customize_addSettings','hook_customize_main_settings','hook_sep_critical_post_type','hook_sep_critical_cat','hook_video_to_videolazy','hook_iframe_to_iframelazy','hook_exclude_image_to_lazyload','hook_customize_image','hook_prevent_generation_htaccess','hook_exclude_css_filter','hook_customize_force_lazy_css','hook_external_javascript_customize','hook_external_javascript_filter','hook_customize_script_object','hook_exclude_internal_js_w3_changes','hook_exclude_page_optimization','hook_customize_critical_css_url','hook_customize_critical_css_filename', 'hook_exclude_convert_image_to_webp'];
                if(in_array($key,$excludeKeys)){
                    continue;
                }
                $value[$key] = $this->w3ReplaceBackslashes($val);
            }
            return $value;
        }
        // Replace double backslashes with single backslashes
        return str_replace('\\\\', '\\', $value);
    }

	/**
	 * Set action heading
	 *
	 * Initializes the actionHeading array with human-readable labels
	 * for all plugin settings. These labels are used in the admin interface
	 * to display user-friendly descriptions of each setting.
	 */
	function setActionHeading(){
        $this->actionHeading = [
			// HTML Caching Settings
			"html_caching" => "Enable HTML Caching",
			"enable_loggedin_user_caching" => "Enable caching for logged in user",
			"by_serve_cache_file" => "Serve html cache file by",
			"enable_caching_get_para" => "Enable caching page with GET parameters",
			"html_caching_expiry_time" => "Cache Expiry Time",
			
			// Server Optimization Settings
			"lbc" => "Enable leverage browsing cache",
			"gzip" => "Enable Gzip compression",
			"remquery" => "Remove query parameters",
			"cache_path" => "Cache Path",
			"license_key" => "License Key",
			
			// General Optimization Settings
			"optimization_on" => "Turn ON optimization",
			"optimize_query_parameters" => "Optimize Pages with Query Parameters",
			"optimize_user_logged_in" => "Optimize pages when User Logged In",
			"enable_inp" => "Fix INP Issues",
			
			// CDN Settings
			"cdn" => "CDN url",
			"exclude_cdn" => "Exclude file extensions from cdn",
			"image_exclude_cdn_path" => "Exclude Image path from cdn",
			"css_exclude_cdn_path" => "Exclude Css path from cdn",
			"js_exclude_cdn_path" => "Exclude Js path from cdn",
			"font_exclude_cdn_path" => "Exclude Font path from cdn",
			"audio_exclude_cdn_path" => "Exclude Audio path from cdn",
			"video_exclude_cdn_path" => "Exclude Video path from cdn",
			
			// Image Optimization Settings
			"keep_org_img" => "Keep Original Images",
			"webp_jpg" => "Convert to Webp",
			"webp_png" => "Convert to PNG",
			
			// Lazy Loading Settings
			"lazy_load" => "Enable Lazy Load Image",
			"lazy_load_iframe" => "Enable Lazy Load Iframe",
			"lazy_load_video" => "Enable Lazy Load Video",
			"lazy_load_audio" => "Enable Lazy Load Audio",
			"inlineToUrlSVG" => "Load SVG Inline Tag as URL",
			"resp_bg_img" => "Responsive Images",
			
			// CSS Optimization Settings
			"css" => "Enable CSS Optimization",
			"localize_google_fonts" => "Localize Google fonts",
			"load_critical_css" => "Load Critical CSS",
			"load_critical_css_style_tag" => "Load Critical CSS in Style Tag",
			"load_style_tag_in_head" => "Load Style Tag in Head to Avoid CLS",
			
			// JavaScript Optimization Settings
			"js" => "Enable Javascript Optimization",
			"load_combined_js" => "Lazyload Javascript",
			"load_script_tag_in_url" => "Load Inline Javascript as URL",
			"preload_resources" => "Preload Resources",
			
			// Exclusion Settings
			"exclude_lazy_load" => "Exclude Resources from Lazy Loading",
			"exclude_css" => "Exclude Link Tag CSS from Optimization",
			"force_lazyload_css" => "Force Lazy Load Link Tag CSS",
			"force_lazy_load_inner_javascript" => "Force Lazy Load Javascript",
			"exclude_both_javascript" => "Exclude Javascript from Lazyload",
			"exclude_url_exclusions_html_cache" => "Exclude pages from HTML caching",
			"exclude_pages_from_optimization" => "Exclude Pages From Optimization",
			"exclude_page_from_load_combined_css" => "Exclude Pages from CSS Optimization",
			"exclude_page_from_load_combined_js" => "Exclude Pages from Javascript Optimization",
			
			// Custom Code Settings
			"custom_css" => "Custom CSS to Load on Page Load",
			"custom_javascript" => "Custom Javascript to Load on Page Load",
			"custom_javascript_file" => "Custom Javascript Load as file",
			"custom_javascript_defer" => "Custom Javascript Load as Defer",
			"custom_js" => "Custom Javascript to Load After Page Load",
			
			// Hook Settings
			"hook_pre_start_opt" => "W3speedster Pre Start Optimization",
			"hook_before_start_opt" => "W3speedster Before Start Optimization",
			"hook_after_opt" => "W3speedster After Optimization",
			"hook_inner_js_customize" => "W3speedster Inner JS Customize",
			"hook_inner_js_exclude" => "W3speedster Inner JS Exclude",
			"hook_internal_js_customize" => "W3speedster Internal JS Customize",
			"hook_internal_css_customize" => "W3speedster Internal Css Customize",
			"hook_internal_css_minify" => "W3speedster Internal Css Minify",
			"hook_no_critical_css" => "W3speedster No Critical Css",
			"hook_customize_critical_css" => "W3speedster Customize Critical Css",
			"hook_disable_htaccess_webp" => "W3speedster Disable Htaccess Webp",
			"hook_customize_addSettings" => "W3speedster Customize Add Settings",
			"hook_customize_main_settings" => "W3speedster Customize Main Settings",
			"hook_sep_critical_post_type" => "W3speedster Seprate Critical Css For Post Type",
			"hook_sep_critical_cat" => "W3speedster Seprate Critical Css For Category",
			"hook_video_to_videolazy" => "W3speedster Change Video To Videolazy",
			"hook_iframe_to_iframelazy" => "W3speedster Change Iframe To Iframlazy",
			"hook_exclude_image_to_lazyload" => "W3speedster Exclude Image To Lazyload",
			"hook_exclude_convert_image_to_webp" => "W3speedster Exclude Image From Convert to Webp",
			"hook_customize_image" => "W3speedster Customize Image",
			"hook_prevent_generation_htaccess" => "W3speedster Prevent Htaccess Generation",
			"hook_exclude_css_filter" => "W3speedster Exclude CSS Filter",
			"hook_customize_force_lazy_css" => "W3speedster Customize Force Lazyload Css",
			"hook_external_javascript_customize" => "W3speedster External Javascript Customize",
			"hook_external_javascript_filter" => "W3speedster External Javascript Filter",
			"hook_customize_script_object" => "W3speedster Customize Script Object",
			"hook_exclude_internal_js_w3_changes" => "W3speedster Exclude Internal Js W3 Changes",
			"hook_exclude_page_optimization" => "W3speedster Exclude Page Optimization",
			"hook_customize_critical_css_url" => "W3speedster Customize Critical Css Url",
			"hook_customize_critical_css_filename" => "W3speedster Customize Critical Css File Name",
			
			// Additional Settings
			"webvitals_logs" => "Enable Core Web Vitals Logs",
			"import_text" => "Import Settings",
			"page_batch" => "Page Optimize Per Batch",
		];
	}
	/**
	 * Get action heading
	 *
	 * Retrieves the human-readable label for a specific setting key.
	 * Initializes the actionHeading array if it hasn't been set yet.
	 *
	 * @param string $key Setting key to get the heading for
	 * @return string Human-readable label for the setting, or empty string if not found
	 */
	function getActionHeading($key)
    {
		// Initialize action headings if not already set
		if(empty($this->actionHeading)){
			$this->setActionHeading();
		}
		return $this->actionHeading[$key] ?? "";
    }

	/**
	 * Remove cache files hourly event callback
	 *
	 * Handles the hourly cleanup of cache files. This method is called
	 * by cron to maintain cache directory sizes and performance.
	 * Creates a new random key after cleanup for security.
	 *
	 * @param string $path Specific cache path to clean (defaults to all)
	 * @return array Cache size information after cleanup
	 */
	function w3RemoveCacheFilesHourlyEventCallback($path = '')
    {
        // Determine which cache path to clean
        if ($path == "html") {
            $cachePath =  $this->addSettings['rootCachePath'] . '/html';
        } else {
            $cachePath =  $this->w3GetCachePath($path);
             // Create new random key for security
            $this->w3CreateRandomKey();
        }
        // Use system command if available for faster cleanup
        if (function_exists('exec')) {
            exec('rm -r ' . $cachePath, $output, $retval);
        }
        
        // Fallback to PHP-based cleanup
        $this->w3CacheRmdir($cachePath);
    }
	/**
	 * Remove cache directory
	 *
	 * Recursively removes a cache directory and all its contents.
	 * Protects the critical-css directory from deletion as it contains
	 * important optimization data.
	 *
	 * @param string $dir Directory path to remove
	 */
	public function w3CacheRmdir($dir)
    {
        // Return if directory doesn't exist
        if (!is_dir($dir)) {
            return;
        }
        
        // Scan directory contents
        $objects = @scandir($dir);
        if ($objects === false) {
            return;
        }
        
        // Process each item in the directory
        foreach ($objects as $object) {
            if ($object === '.' || $object === '..') {
                continue;
            }
            
            $path = $dir . DIRECTORY_SEPARATOR . $object;
            
            if (is_dir($path) && $object !== 'critical-css') {
                // Recursively remove subdirectories (except critical-css)
                $this->w3CacheRmdir($path);
            } else {
                // Remove files
                $this->w3DeleteFile($path);
            }
        }
        if(method_exists($this,'w3RmSingleDir')){
            $this->w3RmSingleDir($dir);
        }else{
            @rmdir($dir);
        }
    }


	/**
	 * Get cache file size
	 *
	 * Recursively calculates the total size of all cache files and directories.
	 * Returns the size in megabytes for easy reading and comparison.
	 *
	 * @return int Total cache size in MB
	 */
	function w3GetCacheFileSize()
    {
        $dir = $this->w3GetCachePath();
        $size = 0;
        
        // Iterate through all cache files and directories
        foreach (glob(rtrim($dir, '/') . '/*', GLOB_NOSORT) as $each) {
            if (file_exists($each)) {
                // Add file size directly
                $size += filesize($each);
            } else {
                // Recursively calculate directory size
                $size += $this->w3Foldersize($each);
            }
        }
        
        // Convert bytes to megabytes
        return ($size / 1024) / 1024;
    }

	/**
	 * Remove critical css cache files
	 *
	 * Removes all critical CSS cache files and clears server-level caching.
	 * This is typically called when CSS optimization settings are changed
	 * to ensure fresh critical CSS generation.
	 *
	 * @return bool Always returns true on completion
	 */
	function w3RemoveCriticalCssCacheFiles()
    {
        // Remove critical CSS cache directory
        $this->w3Rmdir($this->addSettings['criticalCssPath']);
        
        // Clear server-level caching (e.g., object cache, page cache)
        $this->w3DeleteServerCache();
        
        return true;
    }

	/**
	 * Set all links
	 *
	 * Extracts and categorizes various HTML elements from the page content
	 * for optimization processing. This method parses the HTML to find scripts,
	 * styles, images, and other resources that can be optimized.
	 *
	 * @param string $data      HTML content to parse
	 * @param array $resources  Array of resource types to extract
	 * @return array Array containing extracted resources by type
	 */
    function w3SetAllLinks($data, $resources = array())
    {
        $resource_arr = array();
        
        // Extract comment tags that contain important content
        $comment_tag = $this->w3GetTagsData($data, '<!--', '-->');
        $new_comment_tag = array();
        
        // Filter comment tags to keep only those with scripts, links, or other important content
        foreach ($comment_tag as $key => $comment) {
            if (strpos($comment, '<script>') !== false || strpos($comment, '</script>') !== false || strpos($comment, '<link') !== false) {
                $new_comment_tag[] = $comment;
            }
        }
        
        // Extract noscript tags
        $noscript_tag = $this->w3GetTagsData($data, '<noscript', '</noscript>');
        
        // Remove extracted content from the main data
        $data = str_replace(array_merge($new_comment_tag, $noscript_tag), '', $data);
        
        // Extract script tags
        $scripts = $this->w3GetTagsData($data, '<script', '</script>');
        $data = str_replace($scripts, '', $data);

        // Remove all comment tags from data
        $data = str_replace($comment_tag, '', $data);
        
        // Store scripts if JavaScript optimization is enabled
        if (!empty($this->settings['js']) && in_array('script', $resources)) {
            $resource_arr['script'] = $scripts;
        } else {
            $resource_arr['script'] = array();
        }
        
        // Extract image elements for lazy loading and optimization
        $resource_arr['picture'] = array();
        if (in_array('img', $resources)) {
            $resource_arr['img'] = $this->w3GetTagsData($data, '<img', '>');
            if(strpos($data, '</picture>') !== false){
                $resource_arr['picture'] = $this->w3GetTagsData($data, '<picture', '</picture>');
            }
        } else {
            $resource_arr['img'] = array();
        }
        
        // Extract SVG elements for inline processing
        if (in_array('svg', $resources)) {
            $resource_arr['svg'] = $this->w3GetTagsData($data, '<svg', '</svg>');
        } else {
            $resource_arr['svg'] = array();
        }
        
        // Extract CSS-related elements if CSS optimization is enabled
        if (!empty($this->settings['css']) && in_array('link', $resources)) {
            $resource_arr['link'] = $this->w3GetTagsData($data, '<link', '>');
            $resource_arr['style'] = $this->w3GetTagsData($data, '<style', '</style>');
        } else {
            $resource_arr['link'] = array();
            $resource_arr['style'] = array();
        }

        // Extract iframe elements for lazy loading
        if (in_array('iframe', $resources)) {
            $resource_arr['iframe'] = $this->w3GetTagsData($data, '<iframe', '</iframe>');
        } else {
            $resource_arr['iframe'] = array();
        }
        
        // Extract video elements for lazy loading
        if (in_array('video', $resources)) {
            $resource_arr['video'] = $this->w3GetTagsData($data, '<video', '</video>');
        } else {
            $resource_arr['video'] = array();
        }
        
        // Extract audio elements for lazy loading
        if (in_array('audio', $resources)) {
            $resource_arr['audio'] = $this->w3GetTagsData($data, '<audio', '</audio>');
        } else {
            $resource_arr['audio'] = array();
        }
        
        // Extract URL references from CSS content
        if (in_array('url', $resources)) {
            $resource_arr['url'] = $this->w3GetTagsData($data, 'url(', ')');
        } else {
            $resource_arr['url'] = array();
        }
        
        return $resource_arr;
    }
	
	/**
	 * Get folder size
	 *
	 * Recursively calculates the total size of a directory and all its contents.
	 * This method is used to determine cache directory sizes for monitoring
	 * and cleanup purposes.
	 *
	 * @param string $path Directory path to calculate size for
	 * @return int Total size in bytes
	 */
    function w3Foldersize($path)
    {
        $total_size = 0;
        
        if (is_dir($path)) {
            $files = scandir($path);
            $cleanPath = rtrim($path, '/') . '/';
            
            // Process each file and subdirectory
            foreach ($files as $t) {
                if ($t <> "." && $t <> "..") {
                    $currentFile = $cleanPath . $t;
                    
                    if (is_dir($currentFile)) {
                        // Recursively calculate subdirectory size
                        $size = $this->w3Foldersize($currentFile);
                        $total_size += $size;
                    } else {
                        // Add file size directly
                        $size = filesize($currentFile);
                        $total_size += $size;
                    }
                }
            }
        }
        
        return $total_size;
    }

	/**
	 * Create random key
	 *
	 * Generates a new random key and stores it in options.
	 * This key is used for security purposes and cache invalidation.
	 * The key is updated periodically to maintain security.
	 */
    function w3CreateRandomKey()
    {
        $this->w3UpdateOption('w3_rand_key', $this->w3Rand(), 'no', 0);
    }

	/**
	 * Preload resources
	 *
	 * Generates HTML preload tags for critical resources to improve page load performance.
	 * This method creates preload directives for images, fonts, CSS, JavaScript, and other
	 * resources that are essential for above-the-fold content.
	 *
	 * @return string HTML string containing preload link tags
	 */
    function w3PreloadResources()
    {
        $preload_html = '';
        
        // Get preload resources from settings
        $preload_resources = !empty($this->settings['preload_resources']) && is_array($this->settings['preload_resources']) ? $this->settings['preload_resources'] : array();
        
        // Merge with additional preload resources
        if (!empty($this->addSettings['preload_resources']['all']) && is_array($this->addSettings['preload_resources']['all']) && count($this->addSettings['preload_resources']['all']) > 0) {
            $preload_resources = array_merge($preload_resources, $this->addSettings['preload_resources']['all']);
        }
        
        // Handle critical CSS preloading
        if (!empty($this->addSettings['preload_resources']['critical_css'])) {
            $preload_resources = $this->addSettings['preload_resources']['critical_css'] != 1 ? array_merge($preload_resources, array($this->addSettings['preload_resources']['critical_css'])) : $preload_resources;
            $this->addSettings['preload_resources']['css'] = [];
        } elseif (!empty($this->addSettings['preload_resources']['css'])) {
            $preload_resources = array_merge($preload_resources, $this->addSettings['preload_resources']['css']);
        }
        // Add preconnect links for external domains
        if (!empty($this->addSettings['preload_resources']['preconnect'])) {
            $preloadArr = $this->addSettings['preload_resources']['preconnect'];
            foreach ($preloadArr as $link) {
                $preload_html .= '<link rel="preconnect" href="' . trim($link) . '">';
            }
        }
        
        // Add font resources to preload list
        if(!empty($this->addSettings['preload_resources']['font'])){
            $preload_resources = array_merge($preload_resources, $this->addSettings['preload_resources']['font']);
        }
        
        // Remove duplicate resources
        $preload_resources = array_unique($preload_resources);
        
        // Generate preload tags for each resource based on file type
        if (!empty($preload_resources)) {
            $preloaded_fonts_count = 0;
            foreach ($preload_resources as $link) {
                $link_arr = explode('?', $link);
                $extension = explode(".", $link_arr[0]);
                $extension = end($extension);
                
                if (empty($extension)) {
                    continue;
                }
                
                // Preload font files
                if ($preloaded_fonts_count <= 3 && in_array(strtolower($extension), array('otf', 'ttf', 'woff', 'woff2', 'gtf', 'mmm', 'pea', 'tpf', 'ttc', 'wtf'))) {
                    $preloaded_fonts_count++;
                    $preload_html .= '<link rel="preload" href="' . trim($link) . '" as="font" type="font/' . $extension . '" crossorigin fetchpriority="high">';
                }

                // Preload video files
                if (in_array($extension, array('mp4', 'webm'))) {
                    $preload_html .= '<link rel="preload" href="' . trim($link) . '" as="video" type="video/' . $extension . '" fetchpriority="high">';
                }
                
                // Preload CSS files
                if ($extension == 'css') {
                    $crossorigin = $this->w3IsExternal($link, [], 'css') ? 'crossorigin' : '';
                    $preload_html .= '<link rel="preload" href="' . trim($link) . '" as="style" ' . $crossorigin . ' fetchpriority="high">';
                }
                
                // Preload JavaScript files
                if ($extension == 'js') {
                    $crossorigin = $this->w3IsExternal($link, [], 'js') ? 'crossorigin' : '';
                    $preload_html .= '<link rel="preload" href="' . trim($link) . '" as="script" ' . $crossorigin . ' fetchpriority="high">';
                }
            }
        }
        
        return $preload_html;
    }

	/**
	 * Remove dot path segments
	 *
	 * Normalizes file paths by removing dot segments (./ and ../) according to
	 * RFC 3986. This method handles path normalization for URLs and file paths,
	 * ensuring consistent path representation.
	 *
	 * @param string $path Path to normalize
	 * @return string Normalized path without dot segments
	 */
    function removeDotPathSegments($path)
    {
        // Return early if no dot segments exist
        if (strpos($path, '.') === false) {
            return $path;
        }

        $inputBuffer = $path;
        $outputStack = [];

        // Process the path buffer until empty
        while ($inputBuffer != '') {
            // Remove current directory references (./)
            if (strpos($inputBuffer, "./") === 0) {
                $inputBuffer = substr($inputBuffer, 2);
                continue;
            }
            
            // Remove parent directory references (../)
            if (strpos($inputBuffer, "../") === 0) {
                $inputBuffer = substr($inputBuffer, 3);
                continue;
            }

            // Handle root current directory (/.)
            if ($inputBuffer === "/.") {
                $outputStack[] = '/';
                break;
            }
            
            // Handle root current directory with trailing slash (/.//)
            if (substr($inputBuffer, 0, 3) === "/./") {
                $inputBuffer = substr($inputBuffer, 2);
                continue;
            }

            // Handle root parent directory (/..)
            if ($inputBuffer === "/..") {
                array_pop($outputStack);
                $outputStack[] = '/';
                break;
            }
            
            // Handle root parent directory with trailing slash (/../)
            if (substr($inputBuffer, 0, 4) === "/../") {
                array_pop($outputStack);
                $inputBuffer = substr($inputBuffer, 3);
                continue;
            }

            // Stop processing for standalone dot segments
            if ($inputBuffer === '.' || $inputBuffer === '..') {
                break;
            }

            // Extract path segments and continue processing
            if (($slashPos = stripos($inputBuffer, '/', 1)) === false) {
                $outputStack[] = $inputBuffer;
                break;
            } else {
                $outputStack[] = substr($inputBuffer, 0, $slashPos);
                $inputBuffer = substr($inputBuffer, $slashPos);
            }
        }

        return implode($outputStack);
    }

	/**
	 * Check ignore critical css
	 *
	 * Determines whether critical CSS should be ignored for the current page.
	 * This method checks various conditions including user login status, 404 pages,
	 * and custom hooks to decide if critical CSS optimization should be skipped.
	 *
	 * @return int 1 if critical CSS should be ignored, 0 otherwise
	 */
    function checkIgnoreCriticalCss()
    {
        // Return cached result if already determined
        if (isset($this->addSettings['ignoreCriticalCss'])) {
            return $this->addSettings['ignoreCriticalCss'];
        }
        
        $ignore_critical_css = 0;
        
        // Ignore critical CSS for logged-in users and 404 pages
        if (!empty($this->addSettings['w3UserLoggedIn']) || $this->is_404()) {
            $ignore_critical_css = 1;
        }
        
        // Check custom callback method if it exists
        if(method_exists($this, 'checkIgnoreCriticalCssCallback')) {
            $ignore_critical_css = $this->checkIgnoreCriticalCssCallback($this->addSettings['full_url'],$ignore_critical_css);
        }
        
        // Check external function hook if it exists
        if (function_exists('w3_no_critical_css')) {
            $ignore_critical_css = w3_no_critical_css($this->addSettings['full_url']);
        }

        // Process custom hook for critical CSS exclusion
        if(method_exists($this, 'w3_no_critical_css')){
            $ignore_critical_css = $this->w3_no_critical_css($ignore_critical_css, $this->addSettings['full_url']);
        }
        
        // Cache the result for future calls
        $this->addSettings['ignoreCriticalCss'] = $ignore_critical_css;
        return $ignore_critical_css;
    }

	/**
	 * Add page critical css
	 *
	 * Adds the current page to the critical CSS generation queue.
	 * This method is called when critical CSS needs to be generated
	 * for a specific page. It checks various conditions before
	 * adding the page to the queue.
	 */
    function w3AddPageCriticalCss()
    {
        // Skip 404 pages
        if ($this->is_404()) {
            return;
        }
        
        // Skip if plugin is not activated, no license key, or demo license on non-homepage
        if ((empty($this->settings['is_activated']) || empty($this->settings['license_key']) || strpos($this->settings['license_key'], 'w3demo') !== false) && rtrim($this->addSettings['fullUrlWithoutParam'], '/') !== rtrim($this->addSettings['siteUrl'], '/')) {
            return;
        }
        
        // Add page to critical CSS queue if optimization is enabled
        if (!empty($this->settings['optimization_on'])) {
            $this->criticalData['url'] = $this->addSettings['fullUrlWithoutParam'];
            $this->criticalData['data'] = [$this->addSettings['critical_css'], 2, $this->w3PreloadCssPath()];
        }
    }
	
	/**
	 * Custom js enqueue
	 *
	 * Enqueues custom JavaScript code that runs after the page loads.
	 * This method creates a JavaScript file from the custom code setting
	 * and injects it into the page before the closing body tag.
	 */
    function w3CustomJsEnqueue()
    {
        // Get custom JavaScript code or use default
        if (!empty($this->settings['custom_js'])) {
            $custom_js = stripslashes($this->settings['custom_js']);
        } else {
            $custom_js = 'console.log("js loaded");';
        }
        
        $js_file_name1 = 'custom_js_after_load.js';
        
        // Create JavaScript file if it doesn't exist
        if (!file_exists($this->w3GetCachePath('js') . '/' . $js_file_name1)) {
            $this->w3CreateFile($this->w3GetCachePath('js') . '/' . $js_file_name1, $custom_js);
        }
        
        // Inject the script tag before closing body tag
        $this->html = $this->w3StrReplaceLast('</body>', '<script '.'src'.'="' . $this->w3GetCacheUrl('js') . '/' . $js_file_name1 . '"></script></body>', $this->html);
    }

	/**
	 * Load style tag in head
	 *
	 * Processes style tags to load them in the head section of the page.
	 * This method can either inline the styles or load them as external files
	 * based on the configuration settings.
	 *
	 * @param array $style_tags Array of style tags to process
	 */
    function loadStyleTagInHead($style_tags)
    {
        $counter = 0;
        $load_style_tag_in_head_arr = array();
        
        // Parse the load_style_tag_in_head setting
        $load_style_tag_in_head = !empty($this->settings['load_style_tag_in_head']) && is_array($this->settings['load_style_tag_in_head']) ? $this->settings['load_style_tag_in_head'] : array();
        
        // Build array of CSS selectors and their load options
        foreach ($load_style_tag_in_head as $ex_css) {
            $ex_css_arr = explode(' ', $ex_css);
            $load_style_tag_in_head_arr[$counter][0] = $ex_css_arr[0];
            if (!empty($ex_css_arr[1])) {
                $load_style_tag_in_head_arr[$counter][1] = $ex_css_arr[1];
            }
            $counter++;
        }
        
        $styleArr = array();
        $styleRep = array();
        $stylesContent = '';

        foreach ($style_tags as $style_tag) {
            $file_name = $this->w3GetOption('w3_rand_key', 0);
            foreach ($load_style_tag_in_head_arr as $ex_css) {
                if (!empty($ex_css[0]) && !empty($style_tag) && strpos($style_tag, $ex_css[0]) !== false) {
                    $styleArr[] = $style_tag;

                    if (!empty($ex_css[1]) && $ex_css[1] === '1') {
                        $file_name = $ex_css[0];
                        $stylesContentFile = $this->w3ParseScript('style', $style_tag);
                        $link = $this->w3LoadStyleInFile($file_name, $stylesContentFile);
                        $styleRep[] = $link;
                    } else {
                        $stylesContent .= $this->w3ParseScript('style', $style_tag);
                        $styleRep[] = '';
                    }
                    break;
                }
            }
        }
        if (count($styleArr) > 0 && count($styleRep) > 0) {
            $this->html = str_replace($styleArr, $styleRep, $this->html);
        }
        if (empty($stylesContent)) {
            return;
        }
        $this->html = str_replace('</head>', '<style>' . $this->w3CssCompressInit($stylesContent) . '</style></head>', $this->html);
    }

	/**
	 * Load style in file
	 *
	 * @param string $file_name File name.
	 * @param string $stylesContentFile Styles content file.
	 * @return string
	 */
    function w3LoadStyleInFile($file_name, $stylesContentFile)
    {
        $file_name_cache = md5($file_name) . '.css';
        if (!file_exists($this->w3GetCachePath('css') . '/' . $file_name_cache)) {
            $this->w3CreateFile($this->w3GetCachePath('css') . '/' . $file_name_cache, $this->w3CssCompressInit($stylesContentFile));
        }
        return $this->w3CreateCssLink($this->w3GetCacheUrl('css') . '/' . $file_name_cache );
    }

	/**
	 * Create css link
	 *
	 * @param string $file_name_cache File name cache.
	 * @return string
	 */
    function w3CreateCssLink($file_name_cache){
        $defer = 'href=';
        if (!$this->checkIgnoreCriticalCss() && !empty($this->settings['load_critical_css']) && !empty($this->addSettings['critical_css']) && file_exists($this->w3PreloadCssPath() . '/' . $this->addSettings['critical_css'])) {
            $defer = 'data-css="1" href=';
        }
        return '<link'.' rel="'.'stylesheet'.'" ' . $defer . '"' . $file_name_cache . '">';
    }

	/**
	 * Create blank data image
	 *
	 * Creates a transparent SVG image as a data URI for use as a placeholder.
	 * This method generates a minimal SVG that can be used for lazy loading
	 * or as a blank image source.
	 *
	 * @param int $width  Width of the image in pixels
	 * @param int $height Height of the image in pixels
	 * @return string Data URI containing the blank SVG image
	 */
    function createBlankDataImage($width, $height)
    {
        // Create a minimal SVG with specified dimensions and transparent fill
        $image = '%3Csvg%20xmlns=\'http://www.w3.org/2000/svg\'%20width=\'' . $width . '\'%20height=\'' . $height . '\'%3E%3Crect%20width=\'100%25\'%20height=\'100%25\'%20opacity=\'0\'/%3E%3C/svg%3E';
        $dataURI = 'data:image/svg+xml,' . $image;
        return $dataURI;
    }
	
	/**
	 * Create svg image file
	 *
	 * Creates an SVG image file with the specified filename and content.
	 * This method is used for generating SVG files during optimization
	 * and lazy loading processes.
	 *
	 * @param string $filename Path and filename for the SVG file
	 * @param string $content  SVG content to write to the file
	 */
    function createSVGImageFile($filename, $content)
    {
        $this->w3CreateFile($filename, $content);
    }

	/**
	 * Load google fonts
	 *
	 * Generates Google Fonts CSS links for the fonts specified in the settings.
	 * This method supports both the legacy CSS API and the newer CSS2 API.
	 * Fonts are optionally localized for better performance.
	 *
	 * @return string HTML string containing Google Fonts link tags
	 */
    function w3LoadGoogleFonts()
    {
        $google_font = array();
        
        // Process legacy CSS API font links
        if (!empty($this->addSettings['fonts_api_links'])) {
            $all_links = '';
            foreach ($this->addSettings['fonts_api_links'] as $key => $links) {
                $all_links .= !empty($links) && is_array($links) ? $key . ':' . implode(',', $links) . '|' : $key . '|';
            }
            $google_font[] = $this->addSettings['secure'] . "fonts.googleapis.com/css?display=swap&family=" . urlencode(trim($all_links, '|'));
        }
        
        // Process CSS2 API font links
        if (!empty($this->addSettings['fonts_api_links_css2'])) {
            $all_links = 'https://fonts.googleapis.com/css2?';
            foreach ($this->addSettings['fonts_api_links_css2'] as $font) {
                $all_links .= $font . '&';
            }
            $all_links .= 'display=swap';
            $google_font[] = $all_links;
        }
        
        // Localize fonts if enabled
        $google_font = $this->localizeGoogleFonts($google_font);
        
        // Generate CSS link tags for each font
        $fontsCss = '';
        if(is_array($google_font) && count($google_font) > 0){
            foreach($google_font as $font){
                $fontsCss .= $this->w3CreateCssLink($font);
            }
        }
        
        return $fontsCss;
    }

	/**
	 * Get path from url
	 *
	 * Extracts the directory path from a URL by removing the filename.
	 * This method is useful for determining the base path of resources
	 * when processing relative URLs.
	 *
	 * @param string $url URL to extract path from
	 * @return string Directory path without the filename
	 */
    function getPathFromUrl($url){
        // Split URL by slashes and remove the last segment (filename)
        $replace_array = explode('/',str_replace('\'','/',$url));
		array_pop($replace_array);
		return implode('/',$replace_array);
    }

	/**
	 * Localize fonts
	 *
	 * Downloads and localizes font files referenced in CSS content.
	 * This method fetches external font files and stores them locally
	 * for improved performance and offline availability.
	 *
	 * @param string $url URL of the CSS file containing font references
	 * @return string Local path to the localized CSS file
	 */
    function localizeFonts($url){
        // Return original URL if font localization is disabled
        if (empty($this->settings['localize_google_fonts'])) {
            return $url;
        }
        
        $cssfileName = md5($url) . '.css';
        $cssfilePath = $this->addSettings['rootCachePath'] . '/fonts/' . $cssfileName;
        
        // Check if localized CSS file already exists
        if(file_exists($cssfilePath) && filesize($cssfilePath) > 0 ){
            $content = $this->w3speedsterGetContents($cssfilePath);
             if(!empty($content)){
                return str_replace($this->addSettings['rootCachePath'], '', $cssfilePath);
             }
        }else{
            // Fetch CSS content from remote URL
            $content = $this->w3RemoteGet($url);
        }
        
        if (empty($content)) {
            return $url;
        }
        
        // Process font URLs in the CSS content
        $parentUrl = $this->getPathFromUrl($url);
        $fontUrl = $this->w3GetTagsData($content, 'url(', ')');
        $font = ['woff2','woff','ttf','eot'];
        
        foreach ($fontUrl as $url) {
            $sanitizeUrl = $this->replaceUrlBrackets($url);
            $file_info = pathinfo($this->w3ParseUrl($sanitizeUrl, PHP_URL_PATH));
            $ext = isset($file_info['extension']) ? $file_info['extension'] : '';
            
            if(in_array($ext,$font)){
                // Handle relative paths
                if(strpos($sanitizeUrl,'..') !== false){
                    $sanitizeUrl = $this->removeDotPathSegments($parentUrl.'/'.$sanitizeUrl);
                }
                
                // Localize the font file
                if ($fileUrl = $this->localizeGoogleFont($sanitizeUrl)) {
                    $content = str_replace($url, 'url('.$fileUrl.')', $content);
                }
            } 
        }
        
        // Save the localized CSS file
        $this->w3CreateFile($cssfilePath, $content);
        return str_replace($this->addSettings['rootCachePath'], '', $cssfilePath);
    }

	/**
	 * Localize google fonts
	 *
	 * Downloads and localizes Google Fonts CSS files and their referenced font files.
	 * This method processes each Google Font URL, downloads the CSS content,
	 * and localizes the font files for improved performance.
	 *
	 * @param array $google_font Array of Google Font URLs to localize
	 * @return array Array of localized font URLs
	 */
    function localizeGoogleFonts($google_font)
    {
        // Return original fonts if localization is disabled
        if (empty($this->settings['localize_google_fonts'])) {
            return $google_font;
        }
        $fontsArr = $google_font;
        $enableCdn = $this->checkEnableCdn('font');

        $google_font = array_filter($google_font);
        // Process each Google Font URL
        foreach ($google_font as $key => $font) {
            $cssfileName = md5($font) . '.css';
            $cssfilePath = $this->addSettings['rootCachePath'] . '/fonts/' . $cssfileName;
            
            // Get CSS content from cache or remote URL
            $content = file_exists($cssfilePath) && filesize($cssfilePath) > 0 ? $this->w3speedsterGetContents($cssfilePath) : $this->w3RemoteGet($this->w3EnsureUrlScheme($font));
            if (empty($content)) {
                $content = '/* no-css-in-file */';
            }
            // Process font URLs in the CSS content
            $fontUrl = $this->w3GetTagsData($content, 'url(', ')');
            foreach ($fontUrl as $url) {
                if (strpos($url, 'fonts.gstatic.com') !== false) {
                    $sanitizeUrl = $this->replaceUrlBrackets($url);
                    if ($fileUrl = $this->localizeGoogleFont($sanitizeUrl)) {
                        $content = str_replace($sanitizeUrl, $fileUrl, $content);
                    }
                }
            }
            
            // Update font URL to point to localized version
            $fontsArr[$key] = $this->addSettings['cacheUrl'] . '/fonts/' . $cssfileName;
            // Apply CDN if enabled and not excluded
            if($enableCdn && !$this->w3CheckExcludedPath($fontsArr[$key], $this->addSettings['font_exclude_cdn_path'])){
                $fontsArr[$key] = str_replace($this->addSettings['siteUrlArr']['host'], $this->addSettings['fontUrlArr']['host'], $fontsArr[$key]);
            }
            
            // Save the localized CSS file
            $this->w3CreateFile($cssfilePath, $content);
        }
        
        return $fontsArr;
    }

	/**
	 * Localize google font
	 *
	 * Downloads and localizes a single Google Font file. This method fetches
	 * the font file from Google's servers and stores it locally for improved
	 * performance and offline availability.
	 *
	 * @param string $sanitizeUrl URL of the Google Font file to localize
	 * @return string|false Localized font URL or false if localization failed
	 */
    function localizeGoogleFont($sanitizeUrl)
    {
        $ext = '.' . pathinfo($sanitizeUrl, PATHINFO_EXTENSION);
        $urlArr = $this->w3ParseUrl($sanitizeUrl);
        $fileName = !empty($urlArr['path']) && strlen($urlArr['path']) < 254 ? $urlArr['path'] : md5($sanitizeUrl) . $ext;
        $filepath = $this->addSettings['rootCachePath'] . '/fonts/' . ltrim($fileName, '/');
        
        // Get font data from cache or remote URL
        $fontData = file_exists($filepath) && filesize($filepath) > 0 ? $this->w3speedsterGetContents($filepath) : $this->w3RemoteGet($sanitizeUrl);
        $enableCdn = $this->checkEnableCdn('font');
        if (!empty($fontData)) {
            // Save the font file locally
            $this->w3CreateFile($filepath, $fontData);
            $fontUrl = $this->addSettings['cacheUrl'] . '/fonts/' . ltrim($fileName, '/');
            
            // Apply CDN if enabled and not excluded
            if($enableCdn && !$this->w3CheckExcludedPath($fontUrl, $this->addSettings['font_exclude_cdn_path'])){
                $fontUrl = str_replace($this->addSettings['siteUrlArr']['host'], $this->addSettings['fontUrlArr']['host'], $fontUrl);
            }
            
            return $fontUrl;
        }
        
        return false;
    }

	/**
	 * Lazy load iframe
	 *
	 * Applies lazy loading to iframe elements. This method modifies iframe tags
	 * to use lazy loading, including special handling for YouTube iframes with
	 * thumbnail previews. It also supports custom hooks for iframe processing.
	 *
	 * @param array $iframe_links Array of iframe HTML elements to process
	 */
    function lazyLoadIframe($iframe_links)
    {
        if (!empty($this->settings['lazy_load_iframe'])) {
            foreach ($iframe_links as $img) {
                // Skip iframes with backslashes (malformed)
                if (strpos($img, '\\') !== false) {
                    continue;
                }
                
                // Skip excluded iframes
                if ($this->checkImageExcluded($img)) {
                    continue;
                }
                
                $img_obj = $this->w3ParseLink('iframe', $img);
                $iframe_html = '';
                
                if (empty($img_obj['src'])) {
                    continue;
                }
                
                // Special handling for YouTube iframes - add thumbnail preview
                if (strpos($img_obj['src'], 'youtu') !== false) {
                    preg_match("#([\/|\?|&]vi?[\/|=]|youtu\.be\/|embed\/)([a-zA-Z0-9_-]+)#", $img_obj['src'], $matches);
                    if (empty($img_obj['style'])) {
                        $img_obj['style'] = '';
                    }
                    $img_obj['style'] .= 'background-image:url(https://i.ytimg.com/vi/' . trim(end($matches)) . '/sd1.jpg);background-size:contain;';
                }
                
                // Set up lazy loading attributes
                $img_obj['data-src'] = $img_obj['src'];
                $img_obj['src'] = 'about:blank';
                $img_obj['data-class'] = 'LazyLoad';
                
                if(isset($img_obj['onload'])){
                    $img_obj['data-onload'] = $img_obj['onload'];
                    unset($img_obj['onload']);
                }

                // Check custom hook for iframe to iframelazy conversion
                $iframelazy = 0;

                $iframelazy = (method_exists($this, 'w3_change_iframe_to_iframelazy') && $this->w3_change_iframe_to_iframelazy($iframelazy));
                
                // Apply iframelazy conversion if enabled
                if ((function_exists('w3_change_iframe_to_iframelazy') && w3_change_iframe_to_iframelazy()) || $iframelazy) {
                    $this->w3StrReplaceSetImg($img, $this->w3ImplodeLinkArray('iframelazy', $img_obj) . $iframe_html);
                } else {
                    $this->w3StrReplaceSetImg($img, $this->w3ImplodeLinkArray('iframe', $img_obj) . $iframe_html);
                }
            }
        }
    }

	/**
	 * Lazy load video
	 *
	 * Applies lazy loading to video elements. This method modifies video tags
	 * to use lazy loading with blank video placeholders and supports custom
	 * hooks for video processing. It also handles CDN integration for videos.
	 *
	 * @param array $video_links Array of video HTML elements to process
	 */
    function lazyLoadVideo($video_links)
    {
        if (!empty($this->settings['lazy_load_video'])) {
            $enableCdn =  $this->checkEnableCdn('video');
            
            // Determine blank video source (with or without CDN)
            if (strpos($this->addSettings['cacheUrl'], $this->addSettings['siteUrl']) !== false && $enableCdn) {
                $v_src = $this->addSettings['video_cdn_url'] . str_replace($this->addSettings['siteUrl'], '', $this->addSettings['cacheUrl']) . '/blank.mp4';
            } else {
                $v_src = $this->addSettings['cacheUrl'] . '/blank.mp4';
            }
            
            foreach ($video_links as $video) {
                // Skip videos with backslashes (malformed)
                if (strpos($video, '\\') !== false) {
                    continue;
                }
                
                // Skip excluded videos
                if ($this->checkImageExcluded($video)) {
                    continue;
                }
                
                // Skip lazy loading if source type is video/youtube
                if (preg_match('/<source[^>]*\s+type=["\']video\/youtube["\']/i', $video)) {
                    continue;
                }
                
                $video_new = $video;
                
                // Convert poster attribute to data-poster for lazy loading
                if (strpos($video, 'poster=') !== false) {
                    $video_new = str_replace('poster=', 'data-poster=', $video_new);
                }
                
                // Set blank video source and data-src for lazy loading
                // Handle both video and source tags with proper regex
                $video_new = preg_replace(
                    '/(<(?:video|source)[^>]*?)\ssrc=(["\']?)([^"\'>\s]+)(["\']?)/i',
                    '$1 src="' . $v_src . '" data-src=$2$3$4',
                    $video_new
                );
                $video_new = str_replace('<video ', '<video data-class="LazyLoad" ', $video_new);
                
                // Check custom hook for video to videolazy conversion
                $videolazy = 0;
                $videolazy = method_exists($this, 'w3_change_video_to_videolazy') && $this->w3_change_video_to_videolazy($videolazy);
                
                // Apply videolazy conversion if enabled
                if (function_exists('w3_change_video_to_videolazy') && w3_change_video_to_videolazy() || $videolazy) {
                    $video_new = str_replace(array('<video', '</video>'), array('<videolazy', '</videolazy>'), $video_new);
                }
                
                // Apply CDN if enabled and not excluded
                $videoObj = $this->w3ParseLink('video', $video);
                if($enableCdn && !empty($videoObj['src']) && !$this->w3CheckExcludedPath($videoObj['src'], $this->addSettings['video_exclude_cdn_path'])){
                    $video_new = str_replace($this->addSettings['siteUrlArr']['host'], $this->addSettings['videoUrlArr']['host'], $video);
                }
                
                $this->w3StrReplaceSetImg($video, $video_new);
            }
        }
    }

	/**
	 * Lazy load audio
	 *
	 * Applies lazy loading to audio elements. This method modifies audio tags
	 * to use lazy loading with blank audio placeholders and supports CDN
	 * integration for audio files.
	 *
	 * @param array $audio_links Array of audio HTML elements to process
	 */
    function lazyLoadAudio($audio_links)
    {
        if (!empty($this->settings['lazy_load_audio']) && count($audio_links) > 0) {
            $enableCdn =  $this->checkEnableCdn('audio');
            
            // Determine blank audio source (with or without CDN)
            if (strpos($this->addSettings['cacheUrl'], $this->addSettings['siteUrl']) !== false && $enableCdn) {
                $v_src = $this->addSettings['audio_cdn_url'] . str_replace($this->addSettings['siteUrl'], '', $this->addSettings['cacheUrl']) . '/blank.mp3';
            } else {
                $v_src = $this->addSettings['cacheUrl'] . '/blank.mp3';
            }
            
            foreach ($audio_links as $audio) {
                // Skip audio with backslashes (malformed)
                if (strpos($audio, '\\') !== false) {
                    continue;
                }
                
                // Skip excluded audio
                if ($this->checkImageExcluded($audio)) {
                    continue;
                }
                
                $audioObj = $this->w3ParseLink('audio', $audio);
                $audio_new = $audio;
                
                // Apply CDN if enabled and not excluded
                if($enableCdn && !empty($audioObj['src']) && !$this->w3CheckExcludedPath($audioObj['src'], $this->addSettings['audio_exclude_cdn_path'])){
                    $audio_new = str_replace($this->addSettings['siteUrlArr']['host'], $this->addSettings['audioUrlArr']['host'], $audio_new);
                }
                
                // Set blank audio source and data-src for lazy loading
                $audio_new = str_replace('src=', 'data-class="LazyLoad" src="' . $v_src . '" data-src=', $audio_new);
                $this->w3StrReplaceSetImg($audio, $audio_new);
            }
        }
    }

	/**
	 * Lazy load picture
	 *
	 * Applies lazy loading and WebP conversion to picture elements.
	 * Processes source tags within picture elements for WebP conversion
	 * and handles the main img tag similar to lazyLoadImg.
	 *
	 * @param array $picture Array of picture HTML elements to process
	 */
    function lazyLoadPicture($picture, $img_links)
    {
        if (empty($this->settings['lazy_load'])) {
            return;
        }

        if (!empty($picture)) {
            $webp_enable = $this->addSettings['webp_enable'];
            list($img_root_path, $img_root_url) = $this->getImgRootPath();
            
            foreach ($picture as $pictureElement) {
                $picture_org = $pictureElement;
                $picture_new = $pictureElement;
                
                // Extract all source tags from the picture element
                $sourceTags = $this->w3GetTagsData($pictureElement, '<source', '>');
                
                // Process each source tag for WebP conversion
                if (!empty($sourceTags)) {
                    foreach ($img_links as $key => $img) {
                        if (strpos($picture_org, $img) !== false) {
                            $this->addSettings['no_ratio_images'][] = $key;
                        }
                    }
                    foreach ($sourceTags as $sourceTag) {
                        $source_org = $sourceTag;
                        
                        // Extract srcset using w3ParseLink (now fixed to preserve URL encoding)
                        $source_obj = $this->w3ParseLink('source', $sourceTag);
                        $srcset_original = !empty($source_obj['srcset']) ? $source_obj['srcset'] : '';
                        
                        if (!empty($srcset_original)) {
                            $components = $this->w3ParseUrl($srcset_original);
                            
                            // Process internal images only
                            if (!$this->w3IsExternal($srcset_original, $components)) {
                                // Parse srcset to get individual URLs
                                $urls = $this->parseSrcset($srcset_original);
                                
                                // Check if any URL has a supported image extension
                                $hasSupportedExt = false;
                                foreach ($urls as $url) {
                                    $w3_img_ext = '.' . pathinfo($url, PATHINFO_EXTENSION);
                                    if (count($webp_enable) > 0 && in_array($w3_img_ext, $webp_enable)) {
                                        $hasSupportedExt = true;
                                        break;
                                    }
                                }
                                
                                // Convert srcset to WebP if supported
                                if ($hasSupportedExt) {
                                    $srcset_webp = $this->srcsetToWebp($srcset_original, $img_root_url, $img_root_path);
                                    
                                    // Replace srcset in the source tag using regex to preserve exact format
                                    if ($srcset_webp != $srcset_original) {
                                        $sourceTag = preg_replace(
                                            '/(\s+srcset=["\'])([^"\']+)(["\'])/i',
                                            '$1' . $srcset_webp . '$3',
                                            $sourceTag
                                        );
                                        // Replace the source tag in the picture element
                                        $picture_new = str_replace($source_org, $sourceTag, $picture_new);
                                    }
                                }
                            }
                        }
                    }
                }
                
                // Apply CDN if enabled and not excluded
                if ($this->checkEnableCdn('image') && !$this->w3CheckExcludedPath($picture_new, $this->addSettings['image_exclude_cdn_path'])) {
                    $picture_new = str_replace($this->addSettings['siteUrl'], $this->addSettings['image_cdn_url'], $picture_new);
                }
                
                // Replace the picture element in HTML
                if ($picture_org != $picture_new) {
                    $this->w3StrReplaceSetImg($picture_org, $picture_new);
                }
            }
        }
    }

	/**
	 * Lazy load img
	 *
	 * Applies lazy loading to image elements.
	 * This method handles responsive images, WebP conversion, CDN integration,
	 * and various image optimization features. It also supports custom hooks
	 * for image processing.
	 *
	 * @param array $img_links     Array of img HTML elements to process
	 */
    function lazyLoadImg($img_links)
    {
        if (empty($this->settings['lazy_load'])) {
            return;
        }

        // Process img elements
        if (!empty($img_links)) {
            $webp_enable = $this->addSettings['webp_enable'];
            
            foreach ($img_links as $key => $img) {
                if(!empty($this->addSettings['no_ratio_images']) && in_array($key, $this->addSettings['no_ratio_images'])){
                    $no_ratio = true;
                }else{
                    $no_ratio = false;
                }
                $imgnn = $img;
                $imgnn_org_arr = $imgnn_arr = $this->w3ParseLink('img', $imgnn);
                
                if (empty($imgnn_arr['src'])) {
                    continue;
                }
                
                // Skip malformed or data URI images
                if (strpos($imgnn_arr['src'], '\\') !== false || strpos($imgnn_arr['src'], 'data:image') !== false) {
                    continue;
                }
                
                $components = $this->w3ParseUrl($imgnn_arr['src']);
                
                // Process internal images
                if (!$this->w3IsExternal($imgnn_arr['src'], $components)) {
                    $imgnn_arr['src'] = $this->removeQueryParams($imgnn_arr['src'], $components);
                    list($img_root_path, $img_root_url) = $this->getImgRootPath();
                    $w3_img_ext = '.' . pathinfo($imgnn_arr['src'], PATHINFO_EXTENSION);
                    
                    // Enqueue responsive images for supported formats
                    if(in_array($w3_img_ext, ['.jpg', '.webp', '.jpeg', '.png'])){
                        $this->w3EnqueResponsiveImage($imgnn_arr['src']);
                    }
                    
                    $imgsrc_filepath = $this->getResourceRootPath($imgnn_arr['src'], $img_root_url, $img_root_path);
                    $imgnn = trim(preg_replace('/\s+/', ' ', $imgnn));
                    
                    // Get and apply image dimensions
                    $img_size = $this->w3GetImageSize($imgsrc_filepath);
                    if (!empty($img_size[0]) && !empty($img_size[1])) {
                        $imgnn = $this->getImageAttributes($imgnn, $imgnn_arr, $img_size, $no_ratio);
                    }
                    
                    // Handle mobile responsive images
                    if (!empty($this->addSettings['is_mobile']) && !empty($this->settings['resp_bg_img']) && !$this->w3_exclude_image_from_convert_to_webp($imgsrc_filepath)) {
                        if (!empty($img_size[0]) && $img_size[0] > 600) {
                            [$imgnn_arr, $imgsrc_filepath] = $this->convertToSmallerImage($img_root_path, $imgsrc_filepath, $imgnn_arr);
                        }
                    }
                    // Process srcset for responsive images
                    if (strpos($imgnn, ' srcset=') !== false) {
                        $urls = $this->parseSrcset($imgnn_arr['srcset']);
                        foreach ($urls as $url) {
                            $this->w3EnqueResponsiveImage($url);
                        }
                    }
                    
                    // Handle WebP conversion
                    if (count($webp_enable) > 0 && in_array($w3_img_ext, $webp_enable) && !$this->w3_exclude_image_from_convert_to_webp($imgsrc_filepath)) {
                        $imgsrc_webpfilepath = $this->getImgWebpPath($img_root_path, $imgsrc_filepath);
                        if (file_exists($imgsrc_webpfilepath)) {
                            $imgnn_arr['src'] = $this->convertToWebp($imgnn_arr['src']);
                        } else {
                            $this->webpEnqueImageUrls[] = $imgnn_arr['src'];
                        }
                        
                        // Convert srcset to WebP if available
                        if (strpos($imgnn, ' srcset=') !== false) {
                            if(strpos($imgnn_arr['src'], '-595xh.webp') !== false){
                                $imgnn_arr['srcset'] = '';
                            } else {
                                $imgnn_arr['srcset'] = $this->srcsetToWebp($imgnn_arr['srcset'], $img_root_url, $img_root_path);
                            }
                        }

                    }
                }
                
                // Update srcset if modified
                if (!empty($imgnn_org_arr['srcset']) && $imgnn_arr['srcset'] != $imgnn_org_arr['srcset']) {
                    if(empty($imgnn_arr['srcset'])){
                        $imgnn = str_replace([' srcset="'.$imgnn_org_arr['srcset'].'"', " srcset='".$imgnn_org_arr['srcset']."'"],'', $imgnn);
                    }else{
                        $imgnn = str_replace($imgnn_org_arr['srcset'], $imgnn_arr['srcset'], $imgnn);
                    }
                }
                
                // Update src if modified
                if ($imgnn_arr['src'] != $imgnn_org_arr['src']) {
                    if(strpos($imgnn,' src=') !== false){
                        $imgnn = str_replace([' src="'.$imgnn_org_arr['src'].'"', " src='".$imgnn_org_arr['src']."'"],[' src="'.$imgnn_arr['src'].'"', " src='".$imgnn_arr['src']."'"], $imgnn);
                    }elseif(strpos($imgnn,'src=') !== false){
                        $imgnn = str_replace(['"src="'.$imgnn_org_arr['src'].'"', "'src='".$imgnn_org_arr['src']."'"],['" src="'.$imgnn_arr['src'].'"', "' src='".$imgnn_arr['src']."'"], $imgnn);
                    }
                }
                
                // Apply CDN if enabled and not excluded
                if ($this->checkEnableCdn('image') && !$this->w3CheckExcludedPath($imgnn, $this->addSettings['image_exclude_cdn_path'])) {
                    $imgnn = str_replace($this->addSettings['siteUrl'], $this->addSettings['image_cdn_url'], $imgnn);
                }
                $imgSrcArr = $this->w3ParseUrl($imgnn_arr['src']);
                // Handle excluded images (preload instead of lazy load)
                if ($this->checkImageExcluded($img, $imgnn_arr,$imgSrcArr)) {
                    if(!in_array($imgSrcArr['path'], $this->addSettings['preload_resources']['all'])){
                        $this->addSettings['preload_resources']['all'][] = $imgSrcArr['path'];
                    }
                    
                    // Apply custom image customization hooks
                    if (function_exists('w3speedup_customize_image')) {
                        $imgnn = w3speedup_customize_image($imgnn, $img, $imgnn_arr);
                    }

                    if(method_exists($this, 'w3_customize_image')){
                        $imgnn = $this->w3_customize_image($imgnn, $img, $imgnn_arr);
                    }
                    
                    // Set high priority loading for excluded images
                    $imgnn = str_replace('<img ', '<img fetchpriority="high" loading="eager" ', $imgnn);
                    if ($img != $imgnn) {
                        $this->w3StrReplaceSetImg($img, $imgnn);
                    }
                    continue;
                } elseif (!empty($imgnn_arr['fetchpriority']) && $imgnn_arr['fetchpriority'] == 'high') {
                    // Set eager loading for high priority images
                    $imgnn = str_replace(' src=', ' loading="eager" src=', $imgnn);
                } else {
                    if (function_exists('w3speedster_img_src_to_data_src')) {
                        $img_size = empty($img_size) ? array() : $img_size;
                        $blank_image_url = $this->getBlankImageUrl($img_size);
                        // Preserve original quote style around src when converting to data-src
                        $imgnn = preg_replace_callback(
                            '/\s+src=(["\'])([^"\']*)(["\'])/i',
                            function ($m) use ($blank_image_url) {
                                $quote = $m[1];
                                $origSrc = $m[2];
                                return ' data-class='. $quote .'LazyLoad'. $quote . ' src=' . $quote . $blank_image_url . $quote . ' data-src=' . $quote . $origSrc . $quote;
                            },
                            $imgnn,
                            1
                        );
                        if (strpos($imgnn, ' srcset=') !== false) {
                            // Preserve original quote style for srcset as well
                            $imgnn = preg_replace(
                                '/\s+srcset=(["\'])([^"\']*)(["\'])/i',
                                ' data-srcset=$1$2$3',
                                $imgnn,
                                1
                            );
                        }
                    } else {
                        $imgnn = str_replace([' src="', " src='"],[' loading="lazy" src="', " loading='lazy' src='"], $imgnn);
                    }
                }
                
                // Apply custom image customization hooks
                if (function_exists('w3speedup_customize_image')) {
                    $imgnn = w3speedup_customize_image($imgnn, $img, $imgnn_arr);
                }
                if(method_exists($this, 'w3_customize_image')){
                    $imgnn = $this->w3_customize_image($imgnn, $img, $imgnn_arr);
                }
                
                $this->w3StrReplaceSetImg($img, $imgnn);
            }
        }
    }

	/**
	 * Convert to smaller image
	 *
	 * @param string $imgsrc_filepath Img src filepath.
	 * @param array $imgnn_arr Img array.
	 * @return array
	 */
    function convertToSmallerImage($img_root_path, $imgsrc_filepath, $imgnn_arr = [])   
    {
        $imgsrc_filepath_595xh = $this->getImgWebpPath595xh($this->getImgWebpPath($img_root_path, $imgsrc_filepath),$imgsrc_filepath);
        if (file_exists($imgsrc_filepath_595xh)) {
            $imgsrc_filepath = $imgsrc_filepath_595xh;
            $imgsrc_filepath = str_replace(' ', '%20', $imgsrc_filepath);
            $imgnn_arr['src'] = $this->getResourceUrl($imgsrc_filepath);
        }
        return [$imgnn_arr, $imgsrc_filepath];
    }

	/**
	 * Convert srcset to webp
	 *
	 * @param string $srcset Srcset.
	 * @param string $img_root_url Img root url.
	 * @param string $img_root_path Img root path.
	 * @return string
	 */
    function srcsetToWebp($srcset, $img_root_url, $img_root_path)
    {
        $urls = $this->parseSrcset($srcset);
        $webpUrls = $this->srcToWebp($urls, $img_root_url, $img_root_path);
        if (count($webpUrls) > 0) {
            $srcset = str_replace($urls, $webpUrls, $srcset);
        }
        return $srcset;
    }

	/**
	 * Convert src to webp
	 *
	 * @param array $urls Urls.
	 * @param string $img_root_url Img root url.
	 * @param string $img_root_path Img root path.
	 * @return array
	 */
    function srcToWebp($urls, $img_root_url, $img_root_path)
    {
        $webpUrls = [];
        foreach ($urls as $url) {
            $imgsrc_filepath = $this->getResourceRootPath($url, $img_root_url, $img_root_path);
            $imgsrc_webpfilepath = $this->getImgWebpPath($img_root_path, $imgsrc_filepath);
            if (file_exists($imgsrc_webpfilepath)) {
                $webpUrls[] = $this->getResourceUrl($imgsrc_webpfilepath);
            } else {
                $this->webpEnqueImageUrls[] = $url;
                $webpUrls[] = $url;
            }
        }
        return $webpUrls;
    }

	/**
	 * Parse srcset
	 *
	 * @param string $srcset Srcset.
	 * @return array
	 */
    function parseSrcset($srcset)
    {
        $entries = explode(',', $srcset);
        $urls = [];

        foreach ($entries as $entry) {
            // Trim spaces and split URL from descriptors
            $parts = preg_split('/\s+/', trim($entry));
            if (count($parts) > 0) {
                $urls[] = $parts[0]; // First part is always the URL
            }
        }

        return $urls;
    }
    /**
	 * Get img webp 595xh src
	 *
	 * @param string $src Src.
	 * @return string
	 */
	function getImgWebp595xhSrc($src){
        $extension = strtolower(pathinfo($src, PATHINFO_EXTENSION));
        if(strpos($src, 'w3-webp') === false){
            $src = str_replace($this->addSettings['webp_enable_instance'], $this->addSettings['webp_enable_instance_replace'], $src);
            if($extension !== 'webp'){
                return rtrim(str_replace('.webp$', '-595xh.webp$', $src.'$'), '$');
            }else {
                return $src.'-595xh.webp';
            }
        }
        return $src;
    }
    /**
	 * Get img webp path 595xh
	 *
	 * @param string $imgsrc_webpfilepath Img webp filepath.
	 * @param string $imgsrc_filepath Img src filepath.
	 * @return string
	 */
    function getImgWebpPath595xh($imgsrc_webpfilepath,$imgsrc_filepath)
    {
        $extension = strtolower(pathinfo($imgsrc_filepath, PATHINFO_EXTENSION));
        if($extension !== 'webp'){
            return str_replace('.webp','-595xh.webp', $imgsrc_webpfilepath);
        }
        return $imgsrc_webpfilepath.'-595xh.webp';
         
    }

	/**
	 * Get img webp path
	 *
	 * @param string $img_root_path Img root path.
	 * @param string $imgsrc_filepath Img src filepath.
	 * @return string
	 */
    function getImgWebpPath($img_root_path, $imgsrc_filepath)
    {
        if(strpos($imgsrc_filepath, 'w3-webp') !== false && strpos($imgsrc_filepath, '.webp') !== false){
            return $imgsrc_filepath;
        }
        // Check if file path already has .webp extension
        $extension = strtolower(pathinfo($imgsrc_filepath, PATHINFO_EXTENSION));
        if($extension !== 'webp'){
            $imgsrc_filepath = $imgsrc_filepath . '.webp';
        }
        if (!empty($this->addSettings['uploadPath']) && strpos($imgsrc_filepath, $this->addSettings['webp_path']) === false) {
            $imgsrc_webpfilepath = str_replace($this->addSettings['uploadPath'], $this->addSettings['webp_path'], $imgsrc_filepath);
        } else {
            $imgsrc_webpfilepath = str_replace($img_root_path . $this->addSettings['uploadPath'], $img_root_path . $this->addSettings['webp_path'], $imgsrc_filepath);
        }
        return $imgsrc_webpfilepath;
    }

	/**
	 * Remove query params
	 *
	 * @param string $url Url.
	 * @param array $components Components.
	 * @return string
	 */
    function removeQueryParams($url, $components = [])
    {
        $url = urldecode(trim($url));
        $components = isset($components['path']) ? $components : $this->w3ParseUrl($url);
        $path = isset($components['path']) ? $components['path'] : '';
        $query = isset($components['query']) ? $components['query'] : '';
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $urlPath = false;
        // Only remove query params for image, CSS, or JS files
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'ico', 'css', 'js'];
        if (in_array($extension, $allowedExtensions)) {
            $urlPath = $path;
        }else{
            $urlPath = $path.'?'.$query;
        }
        $urlPath = '/'.ltrim(ltrim($urlPath, './'), '/');
        if (!isset($components['scheme']) || !isset($components['host'])) {
            return $this->addSettings['siteUrl'] . $urlPath;
        } else {
            return $this->addSettings['siteUrlArr']['scheme'] . '://' . $components['host'] . $urlPath;
        }
    }

	/**
	 * Get image attributes
	 *
	 * @param string $imgnn Img nn.
	 * @param array $imgnn_arr Img array.
	 * @param array $img_size Img size.
	 * @return string
	 */
    function getImageAttributes($imgnn, $imgnn_arr, $img_size, $no_ratio = false)
    {
        if(!empty($imgnn_arr['width']) && strpos($imgnn_arr['width'], 'px') !== false){
            $imgnn = str_replace([' width="'.$imgnn_arr['width'].'"', " width='".$imgnn_arr['width']."'"], [' width="'.intval($imgnn_arr['width']).'"', " width='".intval($imgnn_arr['width'])."'"], $imgnn);
        }
        if(!empty($imgnn_arr['height']) && strpos($imgnn_arr['height'], 'px') !== false){
            $imgnn = str_replace([' height="'.$imgnn_arr['height'].'"', " height='".$imgnn_arr['height']."'"], [' height="'.intval($imgnn_arr['height']).'"', " height='".intval($imgnn_arr['height'])."'"], $imgnn);
        }
        /*if(function_exists('w3AddHeightWidthAttribute')){
            if (empty($imgnn_arr['width']) || $imgnn_arr['width'] == 'auto' || $imgnn_arr['width'] == '100%') {
                $imgnn = str_replace(array(' width="auto"', ' src='), array('', ' width="' . $img_size[0] . '" src='), $imgnn);
            }
            if (empty($imgnn_arr['height']) || $imgnn_arr['height'] == 'auto' || $imgnn_arr['height'] == '100%') {
                $imgnn = str_replace(array(' height="auto"', ' src='), array('', ' height="' . $img_size[1] . '" src='), $imgnn);
            }
        }*/
        if ((empty($imgnn_arr['width']) || empty($imgnn_arr['height'])) && !empty($img_size[0]) && !empty($img_size[1]) && !$no_ratio) {
            $ratioWidth = $img_size[0];
            $ratioHeight = $img_size[1];
            $class = "w3-ratio-{$ratioWidth}x{$ratioHeight}";
            $this->addSettings['w3_img_aspect_ratio'][$class] = [$ratioWidth, $ratioHeight];
            if(strpos($imgnn, ' class=') !== false){
               $imgnn = str_replace([' class="', " class='"],[' class="'.$class.' ', " class='".$class." "], $imgnn);  
            } else {
                if(strpos($imgnn, 'class=') !== false){
                    $imgnn = str_replace(['class="', "class='"],[' class="'.$class.' ', " class='".$class." "], $imgnn);
                } else {
                    $imgnn = str_replace([' src="', " src='"],[' class="'.$class.'" src="', " class='".$class."' src='"], $imgnn);
                }
            }
        }
        return $imgnn;
    }

	/**
	 * Debug
	 *
	 * @param string $text Text.
	 */
    function w3debug($text)
    {
        if (!empty($this->addSettings['w3_get']['tester'])) {
            $this->html .= $text;
        }
    }

    function w3GetImageSize($path)
    {
        if (!file_exists($path)) {
            return array('', '');
        }
        $img_size = array();
        $w3_img_ext = '.' . pathinfo($path, PATHINFO_EXTENSION);
        if ($w3_img_ext == '.svg') {
            list($img_size[0], $img_size[1], $alt) = $this->getSvgAttributes($this->w3speedsterGetContents($path));
        } else {
            $img_size = strlen($path) < 4097 && file_exists($path) ? @getimagesize($path) : array();
        }
        return $img_size;
    }
	
	/**
	 * Get img root path
	 *
	 * @return array
	 */
    function getImgRootPath()
    {
        return array($this->addSettings['documentRoot'], $this->addSettings['is_multisite'] ? $this->addSettings['network_site_url'] : $this->addSettings['siteUrl']);
    }
	
	/**
	 * Get resource root path
	 *
	 * @param string $src Src.
	 * @param string $img_root_url Img root url.
	 * @param string $img_root_path Img root path.
	 * @return string
	 */
    function getResourceRootPath($src, $img_root_url, $img_root_path)
    {
        if ($this->addSettings['isMultisiteSubDomain']) {
            $src = str_replace($this->addSettings['siteUrl'], $this->addSettings['network_site_url'], $src);
        }
        if (strpos($src, $img_root_url) === false) {
            $src = str_replace(array('https://', 'http://'), $this->addSettings['siteUrlArr']['scheme'] . '://', $src);
            if(strpos($src,$this->addSettings['siteUrlDiff']) !== false){
                $src = str_replace($this->addSettings['siteUrlDiff'],$this->addSettings['siteUrlArr']['host'],$src);
            }
        }
        $path = $img_root_path . str_replace($img_root_url, '', $src);
        if (strpos($path, '..') !== false) {
            $path = $this->removeDotPathSegments($path);
        }
        return $path;
    }
	
	/**
	 * Get resource url
	 *
	 * @param string $path Path.
	 * @return string
	 */
    function getResourceUrl($path)
    {
        if ($this->addSettings['isMultisiteSubDomain']) {
            return str_replace($this->addSettings['documentRoot'], $this->addSettings['network_site_url'], $path);
        } else {
            return str_replace($this->addSettings['documentRoot'], $this->addSettings['siteUrl'], $path);
        }
    }
	
	/**
	 * Get blank image url
	 *
	 * @param array $img_size Img size.
	 * @return string
	 */
    function getBlankImageUrl($img_size)
    {
        if (!empty($img_size[0]) && !empty($img_size[1])) {
            $blank_image_url = $this->createBlankDataImage((int)$img_size[0], (int)$img_size[1]);
        } else {
            $blank_image_url = $this->addSettings['blank_image_url'];
        }
        return $blank_image_url;
    }
	
	/**
	 * Get svg xml
	 *
	 * @param string $data Data.
	 * @return string
	 */
    function getSvgXml($data)
    {
        if (strpos($data, '<svg') !== false) {
            return @simplexml_load_string($data);
        } elseif (file_exists($data)) {
            return simplexml_load_file($data);
        } else {
            return array();
        }
    }

	/**
	 * Get svg attributes
	 *
	 * @param string $content Content.
	 * @return array
	 */
    function getSvgAttributes($content)
    {
        $svg = $this->getSvgXml(html_entity_decode($content));
        if (!empty($svg['width'])) {
            if (strpos($svg['width'], 'em') !== false) {
                $width = (int)$svg['width'] * 16;
            } elseif (strpos($svg['width'], 'mm') !== false) {
                $width = (int)$svg['width'] * 3.7795275591;
            } else {
                $width = (int)$svg['width'];
            }
        } else {
            $width = '';
        }
        if (!empty($svg['height'])) {
            if (strpos($svg['height'], 'em') !== false) {
                $height = (int)$svg['height'] * 16;
            } else {
                $height = (int)$svg['height'];
            }
        } else {
            $height = '';
        }
        return array($width, $height, $this->getSvgTitle($svg));
    }
		
	/**
	 * Get svg title
	 *
	 * @param string $svg Svg.
	 * @return string
	 */
    function getSvgTitle($svg)
    {
        return (!empty($svg->title) ? (string)$svg->title : '');
    }
	
	/**
	 * Check image excluded
	 *
	 * @param string $img Img.
	 * @param array $imgnn_arr Img array.
	 * @return int
	 */
    function checkImageExcluded($img, $imgnn_arr = [], $imgSrcArr = [])
    {
        $exclude_image = 0;
        if(!empty($imgnn_arr['data-lazyload'])){
            $exclude_image = 1;
        }elseif ($this->settings['lazy_load']) {
            foreach ($this->addSettings['excludedImg'] as $ex_img) {
                if (!empty($ex_img) && strpos($img, $ex_img) !== false) {
                    $exclude_image = 1;
                }
            }
            if(!empty($imgSrcArr['path'])){
                foreach ($this->addSettings['preload_resources']['all'] as $ex_img) {
                    if (!empty($ex_img) && strpos($imgSrcArr['path'], $ex_img) !== false) {
                        $exclude_image = 1;
                    }
                }
            }
            if (!empty($imgnn_arr['data-class']) && strpos($imgnn_arr['data-class'], 'LazyLoad') !== false) {
                $exclude_image = 1;
            }
        } else {
            $exclude_image = 1;
        }

        if(method_exists($this, 'w3_exclude_image_to_lazyload')){
            $exclude_image = $this->w3_exclude_image_to_lazyload($exclude_image, $img, $imgnn_arr);
        }
        if (function_exists('w3speedup_image_exclude_lazyload')) {
            $exclude_image = w3speedup_image_exclude_lazyload($exclude_image, $img, $imgnn_arr);
        }
        return $exclude_image;
    }
	
	/**
	 * Convert svgs to file
	 *
	 * @param array $svgs Svgs.
	 */
    function convertSVGsToFile($svgs)
    {
        $convertedSvg = [];
        foreach ($svgs as $svg) {
            $path = $this->addSettings['rootCachePath'] . '/images/';
            $filename = md5($svg) . '.svg';
            if (!in_array($filename, $convertedSvg)) {
                $convertedSvg[] = $filename;
            } else {
                continue;
            }
            if ($this->checkImageExcluded($svg)) {
                continue;
            }
            if (!file_exists($path . $filename)) {
                $filePath = $this->createSVGImageFile($path . $filename, $svg);
            }
            if (file_exists($path . $filename)) {
                $newSvgArr = array();
                $newSvgArr['src'] = $this->addSettings['cacheUrl'] . '/images/' . $filename;
                list($newSvgArr['width'], $newSvgArr['height'], $newSvgArr['alt']) = $this->getSvgAttributes($svg);
                $newSvgArr['loading'] = 'lazy';
                $newSvgArr['class'] = 'w3-svg';
                $this->w3StrReplaceSetImg($svg, $this->w3ImplodeLinkArray('img', $newSvgArr));
            }
        }
    }
	
	/**
	 * Lazyload
	 *
	 * @param array $all_links All links.
	 */
    function lazyload($all_links)
    {
        $this->lazyLoadIframe($all_links['iframe']);
        $this->lazyLoadVideo($all_links['video']);
        $this->lazyLoadAudio($all_links['audio']);
        $this->lazyLoadPicture($all_links['picture'], $all_links['img']);
        $this->lazyLoadImg($all_links['img']);
        if (!empty($all_links['svg'])) {
            $this->convertSVGsToFile($all_links['svg']);
        }
        $this->html = $this->w3ConvertArrRelativeToAbsolute($this->html, $this->addSettings['fullUrlWithoutParam'] . '/index.php', $all_links['url']);
    }
	
	/**
	 * Lazy load background image
	 */
    function lazyLoadBackgroundImage()
    {
        $elements = array('<div ', '<section ', '<iframelazy ', '<iframe ');
        $Repelements = array('<div data-bglz=1 ', '<section data-bglz=1 ', '<iframelazy data-bglz=1 ', '<iframe data-bglz=1 ');
        $this->html = str_replace($elements, $Repelements, $this->html);
    }
	
	/**
	 * W3speedster core web vitals script
	 *
	 * @return string
	 */
    function W3speedsterCoreWebVitalsScript()
    {
        $script_content = '<script>
			(function () {
				var secureKey = \'' . $this->createSecureKey('cwvLog') . '\';
				var adminAjax = \'' . $this->getAjaxUrl() . '\';
				var device = /Mobi|Android/i.test(navigator.userAgent) ? "Mobile" : "Desktop" ;
				var script = document.createElement(\'script\');
				script.src = \'' . $this->assetUrl('assets/js/web-vitals.iife.js') . '\';
				script.onload = function () {
					webVitals.onCLS(handleVitalsCLS);
					webVitals.onFID(handleVitalsFID);
					webVitals.onLCP(handleVitalsLCP);
					webVitals.onINP(handleVitalsINP);
				};
				document.head.appendChild(script);
			
	
				function handleVitalsFID(metric) {
				   if(metric.rating != \'good\'){
						var metricString = JSON.stringify(metric);
						var metricObject = JSON.parse(metricString);
						var index = 0;
						metric.entries.forEach(() => {
							 metricObject.entries[index].targetElement = metric.entries[index].target.className;
							index++;
						});
						var lastString = JSON.stringify(metricObject);
						w3Ajax(lastString,\'LCP\');
					}
				}
		
				function handleVitalsCLS(metric) {
				   if(metric.rating != \'good\'){
					var metricString = JSON.stringify(metric);
					var metricObject = JSON.parse(metricString);
					metric.entries.forEach((e,i) => {
						e.sources.forEach((j,k) => {
							metricObject.entries[i].sources[k].targetElement = j.node["className"];
						});
					});
					var lastString = JSON.stringify(metricObject);
					w3Ajax(lastString,\'CLS\');
				  }
				}
		
				function handleVitalsLCP(metric) {
					if(metric.rating != \'good\'){
						var metricString = JSON.stringify(metric); // Serialize the metric object
						var metricObject = JSON.parse(metricString);
						var index = 0;
						metric.entries.forEach(() => {
							 metricObject.entries[index].targetElement = metric.entries[index].element.className;
							index++;
						});
						var lastString = JSON.stringify(metricObject);
						w3Ajax(lastString,\'LCP\');
					}
				} 
				function w3Ajax(lastString, issueType) {
					var xhr = new XMLHttpRequest();
					var url = adminAjax;  // Assuming `adminAjax` is defined elsewhere

					xhr.open(\'POST\', url, true);
					xhr.setRequestHeader(\'Content-Type\', \'application/x-www-form-urlencoded\');

					// Create the data string in the URL-encoded format
					var data = \'action=w3speedsterPutData\' +
								\'&_w3nonce=\'+encodeURIComponent(secureKey) +
							   \'&data=\' + encodeURIComponent(lastString) +
							   \'&url=\' + encodeURIComponent(window.location.href) +
							   \'&issueType=\' + encodeURIComponent(issueType) +
							   \'&deviceType=\' + encodeURIComponent(device); 

					xhr.onreadystatechange = function() {
						if (xhr.readyState === XMLHttpRequest.DONE) {
							if (xhr.status === 200) {
								console.log(\'data inserted\');
							} else {
								console.log(xhr.statusText);
							}
						}
					};

					xhr.onerror = function() {
						console.log(xhr.statusText);
					};

					xhr.send(data);
				}
				function handleVitalsINP(metric) {
					if(metric.rating != \'good\'){
						var metricString = JSON.stringify(metric); 
						var metricObject = JSON.parse(metricString);
						var index = 0;
						metric.entries.forEach(() => {
							 metricObject.entries[index].targetElement = metric.entries[index].target.className;
							index++;
						});
						var lastString = JSON.stringify(metricObject);
						w3Ajax(lastString,\'LCP\');
					}
				}
			})();
		</script>';
        $webVitalspath = $this->w3GetCachePath('all-js') . '/webvital.js';
        if (!is_file($webVitalspath)) {
            $this->w3CreateFile($webVitalspath, $this->w3CompressJs($script_content));
        }
        return $this->w3speedsterGetContents($webVitalspath);
    }
	
	/**
	 * Copy assets
	 */
    function copyAssets()
    {
        $path = $this->w3GetCachePath();
        $fileArray = ['blank.mp4', 'blank.mp3', 'blank.png'];
        foreach ($fileArray as $value) {
            if (!file_exists($path . '/' . $value)) {
                copy(W3SPEEDSTER_DIR. "assets/images/" . $value, $path . '/' . $value);
            }
        }
    }
	
	/**
	 * Insert critical css
	 *
	 * @return array
	 */
    function insertCriticalCss()
    {
        $criticalCssInsertion = '';
        $criticalReplace = [];
        if (!$this->checkIgnoreCriticalCss()) {
            if (!empty($this->addSettings['w3_get']['w3_get_css_post_type'])) {
                $this->html .= 'rocket22' . W3SPEEDSTER_VERSION . str_replace($this->addSettings['documentRoot'], '', $this->w3PreloadCssPath()) . '--' . $this->addSettings['critical_css'] . '--' . file_exists($this->w3PreloadCssPath() . '/' . $this->addSettings['critical_css']);
            }
            if (!empty($this->settings['load_critical_css'])) {
                if (!file_exists($this->w3PreloadCssPath() . '/' . $this->addSettings['critical_css']) || filesize($this->w3PreloadCssPath() . '/' . $this->addSettings['critical_css']) < 10) {
                    $this->w3DeleteFile($this->w3PreloadCssPath() . '/' . $this->addSettings['critical_css']);
                    $this->w3AddPageCriticalCss();
                } else {
                    $critical_css = $this->w3speedsterGetContents($this->w3PreloadCssPath() . '/' . $this->addSettings['critical_css']);
                    if (!empty($critical_css)) {
                        $criticalCssInsertion = 1;
                        if (function_exists('w3speedup_customize_critical_css')) {
                            $critical_css = w3speedup_customize_critical_css($critical_css);
                        }
                        if(method_exists($this, 'w3_customize_critical_css')){
                            $critical_css = $this->w3_customize_critical_css($critical_css);
                        }
                        $critical_css_modified = $this->convertWebpInCritical($critical_css);
                        if(strlen($critical_css_modified) !== strlen($critical_css)){
                            $this->w3speedsterPutContents($this->w3PreloadCssPath() . '/' . $this->addSettings['critical_css'], $critical_css_modified);
                        }
                        $this->preloadFontFromCritical($critical_css_modified);
                        if (!empty($this->settings['load_critical_css_style_tag'])) {
                            $critical_css_modified = preg_replace('/\/\*\s*<w3images>.*?<\/w3images>\s*\*\//s', '', $critical_css_modified);
                            $critical_css_modified = preg_replace('/\/\*\s*<w3elements>.*?<\/w3elements>\s*\*\//s', '', $critical_css_modified);
                            $critical_css_modified = preg_replace('/\/\*\s*<w3DataBgLoad>.*?<\/w3DataBgLoad>\s*\*\//s', '', $critical_css_modified);
                            $criticalReplace[0] = array('data-css="1" ', '{{main_w3_critical_css}}');
                            $criticalReplace[1] = array('data-', '<style id="w3speedster-critical-css">' . $critical_css_modified . '</style>');
                            $this->addSettings['preload_resources']['critical_css'] = 1;
                        } else {
                            $enableCdnCss = $this->checkEnableCdn('css');
                            $critical_css_url = str_replace($this->addSettings['documentRoot'], ($enableCdnCss ? $this->addSettings['css_cdn_url'] : $this->addSettings['siteUrl']), $this->w3PreloadCssPath() . '/' . $this->addSettings['critical_css']);
                            $critical_css_url .= '?v='. $this->w3Rand();
                            $criticalReplace[0] = array('data-css="1" ', '{{main_w3_critical_css}}');
                            $criticalReplace[1] = array('data-', '<link rel="'.'stylesheet'.'" href="' . $critical_css_url . '"/>');
                            $this->addSettings['preload_resources']['critical_css'] = $critical_css_url;
                        }
                    } else {
                        $this->w3AddPageCriticalCss();
                    }
                }
            }
        }
        return array($criticalCssInsertion, $criticalReplace);
    }
	
	/**
	 * Preload font from critical
	 *
	 * @param string $content Content.
	 */
    function preloadFontFromCritical($content){
        $fontfaces = $this->w3GetTagsData($content, '@font-face', '}');
        $preferredFormats = ['woff2', 'woff', 'ttf', 'eot', 'svg'];
        foreach ($fontfaces as $fontface) {
            $urls = $this->w3GetTagsData($fontface, 'url(', ')');
            $fontAdded = false;
            $fontUrl = '';
            foreach ($preferredFormats as $format) {
                foreach ($urls as $url) {
                    $url = $this->replaceUrlBrackets($url);
                    if(strpos($url, 'data:') !== false){
                        continue;
                    }
                    $fontUrl = $url = trim($url);
                    if (stripos($url, $format) !== false) {
                        $this->addSettings['preload_resources']['font'][] = $url;
                        $fontAdded = true;
                        break 2;
                    }
                }
            }
            if (!$fontAdded && count($urls) && !empty($fontUrl)) {
                $this->addSettings['preload_resources']['font'][] = $this->w3ChangeUrlScheme($fontUrl);
            }
        }
    }
	
	/**
	 * Compress js
	 *
	 * @param string $string String.
	 * @return string
	 */
    function w3CompressJs($string)
    {
        include_once W3SPEEDSTER_DIR. 'includes/W3jsMin.php';
        if (!\W3jsMin::isLikelyMinified($string)) {
            $string = \W3jsMin::minify($string);
        }
        return $string;
    }
	
	/**
	 * Hook callback function
	 *
	 * @param string $code Code.
	 * @param array $args Args.
	 * @return string
	 */
    function hookCallbackFunction($code, ...$args)
    {
        if (!empty($code)) {
            $code = stripcslashes($code);
            // @codingStandardsIgnoreLine
            eval($code);
            return $args[0];
        }
    }
	
	/**
	 * Safe eval
	 *
	 * @param string $code Code.
	 * @return string
	 */
    function safeEval($code)
    {
        $allowedFunctions = ['strpos', 'str_replace', 'preg_match', 'preg_replace', 'preg_match_all', 'strlen', 'if', 'wp_is_mobile', 'array'];
        $tokens = $this->extractPhpFunctions($code);
        foreach ($tokens as $token) {
            if (!is_numeric($token) && !in_array($token, $allowedFunctions) && stripos($token, 'w3speedster') === false) {
                $parseErrorData = array('error' => "Use of function '$token' is not allowed.");
                return $parseErrorData;
            }
        }
        // @codingStandardsIgnoreLine
        return eval($code);
    }
		
	/**
	 * Extract php functions
	 *
	 * @param string $code Code.
	 * @return array
	 */
    function extractPhpFunctions($code)
    {
        $pattern = '/\b([a-zA-Z_][a-zA-Z0-9_]*)\s*\(/';
        preg_match_all($pattern, $code, $matches);
        return array_unique($matches[1]);
    }

	/**
	 * Get web cache path
	 *
	 * @param string $type Type.
	 * @param string $cachePath Cache path.
	 * @return string
	 */
    function getWebCachePath($type, $cachePath)
    {
        if ($this->w3IsPluginActive('gtranslate/gtranslate.php')) {
            if (isset($this->addSettings['w3_server']["HTTP_X_GT_LANG"])) {
                $cacheFilePath = $this->addSettings['rootCachePath'] . $type . "/" . $this->addSettings['w3_server']["HTTP_X_GT_LANG"] . $cachePath;
            } else if (isset($this->addSettings['w3_server']["REDIRECT_URL"]) && $this->addSettings['w3_server']["REDIRECT_URL"] != "/index.php") {
                $cacheFilePath = $this->addSettings['rootCachePath'] . $type . "/" . $this->addSettings['w3_server']["REDIRECT_URL"];
            } else if (isset($this->addSettings['w3_server']["REQUEST_URI"])) {
                $cacheFilePath = $this->addSettings['rootCachePath'] . $type . "/" . $cachePath;
            }
        } else {
            $cacheFilePath = $this->addSettings['rootCachePath'] . $type . "/" . $cachePath;
        }
        return rtrim($cacheFilePath, '/');
    }

	/**
	 * W3speedster remove html cache code
	 */
    function w3SpeedsterRemoveHtmlCacheCode()
    {
        $htaccessPath = $this->addSettings['documentRoot'] . "/.htaccess";
        $htaccessContent = $this->w3speedsterGetContents($htaccessPath);

        // @codingStandardsIgnoreLine
        if (is_file($this->addSettings['documentRoot'] . "/.htaccess") && $this->is_writable($this->addSettings['documentRoot'] . "/.htaccess") && strpos($htaccessContent, '# BEGIN W3HTMLCACHE') !== false && strpos($htaccessContent, '# END W3HTMLCACHE') !== false) {
            $htaccess = preg_replace("/#\s?BEGIN\s?W3HTMLCACHE.*?#\s?END\s?W3HTMLCACHE\s*/s", "", $htaccessContent);
            $this->w3speedsterPutContents($htaccessPath, $htaccess);
        }
    }
	
	/**
	 * W3 insert lbc rule
	 *
	 * @param string $htaccess Htaccess.
	 * @return string
	 */
    function w3InsertLbcRule($htaccess)
    {
        $data = "\n" . "# BEGIN W3LBC" . "\n" .
            '<FilesMatch "\.(webm|ogg|mp4|ico|pdf|flv|jpg|jpeg|png|gif|webp|js|css|swf|x-html|css|xml|js|woff|woff2|otf|ttf|svg|eot)(\.gz)?$">' . "\n" .
            '<IfModule mod_expires.c>' . "\n" .
            'AddType application/font-woff2 .woff2' . "\n" .
            'AddType application/x-font-opentype .otf' . "\n" .
            'ExpiresActive On' . "\n" .
            'ExpiresDefault A0' . "\n" .
            'ExpiresByType video/webm A10368000' . "\n" .
            'ExpiresByType video/ogg A10368000' . "\n" .
            'ExpiresByType video/mp4 A10368000' . "\n" .
            'ExpiresByType image/webp A10368000' . "\n" .
            'ExpiresByType image/gif A10368000' . "\n" .
            'ExpiresByType image/png A10368000' . "\n" .
            'ExpiresByType image/jpg A10368000' . "\n" .
            'ExpiresByType image/jpeg A10368000' . "\n" .
            'ExpiresByType image/ico A10368000' . "\n" .
            'ExpiresByType image/svg+xml A10368000' . "\n" .
            'ExpiresByType text/css A10368000' . "\n" .
            'ExpiresByType text/javascript A10368000' . "\n" .
            'ExpiresByType application/javascript A10368000' . "\n" .
            'ExpiresByType application/x-javascript A10368000' . "\n" .
            'ExpiresByType application/font-woff2 A10368000' . "\n" .
            'ExpiresByType application/x-font-opentype A10368000' . "\n" .
            'ExpiresByType application/x-font-truetype A10368000' . "\n" .
            '</IfModule>' . "\n" .
            '<IfModule mod_headers.c>' . "\n" .
            'Header set Expires "max-age=A10368000, public"' . "\n" .
            'Header unset ETag' . "\n" .
            'Header set Connection keep-alive' . "\n" .
            'FileETag None' . "\n" .
            '</IfModule>' . "\n" .
            '</FilesMatch>' . "\n" .
            "# END W3LBC" . "\n";

        $htaccess = preg_replace("/#\s?BEGIN\s?W3LBC.*?#\s?END\s?W3LBC/s", "", $htaccess);
        if (!empty($this->htaccessModifyKeys['lbc'])) {
            if ($this->checkHtaccessStatus($data)) {
                $htaccess = $data . $htaccess;
            } else {
                unset($this->settings['lbc']);
                $this->w3UpdateOption('w3_speedup_option', $this->settings, 'no');
                $this->w3AddError('danger', 'Server error: Unable to apply .htaccess LBC rules to the site.');
            }
        } else {
            $htaccess = $data . $htaccess;
        }
        return $htaccess;
    }

	/**
	 * W3 insert gzip rule
	 *
	 * @param string $htaccess Htaccess.
	 * @return string
	 */
    function w3InsertGzipRule($htaccess)
    {
        $data = "\n" . "# BEGIN W3Gzip" . "\n" .
            "<IfModule mod_deflate.c>" . "\n" .
            "AddType x-font/woff .woff" . "\n" .
            "AddType x-font/ttf .ttf" . "\n" .
            "AddOutputFilterByType DEFLATE image/svg+xml" . "\n" .
            "AddOutputFilterByType DEFLATE text/plain" . "\n" .
            "AddOutputFilterByType DEFLATE text/html" . "\n" .
            "AddOutputFilterByType DEFLATE text/xml" . "\n" .
            "AddOutputFilterByType DEFLATE text/css" . "\n" .
            "AddOutputFilterByType DEFLATE text/javascript" . "\n" .
            "AddOutputFilterByType DEFLATE application/xml" . "\n" .
            "AddOutputFilterByType DEFLATE application/xhtml+xml" . "\n" .
            "AddOutputFilterByType DEFLATE application/rss+xml" . "\n" .
            "AddOutputFilterByType DEFLATE application/javascript" . "\n" .
            "AddOutputFilterByType DEFLATE application/x-javascript" . "\n" .
            "AddOutputFilterByType DEFLATE application/x-font-ttf" . "\n" .
            "AddOutputFilterByType DEFLATE x-font/ttf" . "\n" .
            "AddOutputFilterByType DEFLATE application/vnd.ms-fontobject" . "\n" .
            "AddOutputFilterByType DEFLATE font/opentype font/ttf font/eot font/otf" . "\n" .
            "</IfModule>" . "\n";

        $data = $data . "# END W3Gzip" . "\n";

        $htaccess = preg_replace("/\s*\#\s?BEGIN\s?W3Gzip.*?#\s?END\s?W3Gzip\s*/s", "", $htaccess);
        if (!empty($this->htaccessModifyKeys['gzip'])) {
            if ($this->checkHtaccessStatus($data)) {
                $htaccess = $data . $htaccess;
            } else {
                unset($this->settings['gzip']);
                $this->w3UpdateOption('w3_speedup_option', $this->settings, 'no');
                $this->w3AddError('danger', 'Server error: Unable to apply .htaccess Gzip rules to the site.');
            }
        } else {
            $htaccess = $data . $htaccess;
        }
        return $htaccess;
    }

	/**
	 * W3 insert 404 redirect to file
	 *
	 * @param string $htaccess Htaccess.
	 * @return string
	 */
    function w3Insert_404RedirectToFile($htaccess)
    {
        $data = "\n". "# BEGIN W3404" . "\n" .
            "<IfModule mod_rewrite.c>" . "\n" .
            "RewriteEngine On" . "\n" .
            "RewriteBase /" . "\n" .
            "RewriteCond %{REQUEST_FILENAME} !-f" . "\n" .
            "RewriteRule (.*)/w3-cache/(css|js|woff|woff2|ttf|otf|eot|svg)/(\d)*(.*)[mob]*\.(css|js|woff|woff2|ttf|otf|eot|svg) $4.$5 [L]" . "\n" .
            "</IfModule>" . "\n";
        $data = $data . "# END W3404" . "\n";
        $htaccess = preg_replace("/\s*\#\s?BEGIN\s?W3404.*?#\s?END\s?W3404\s*/s", "", $htaccess);
        if(method_exists($this, 'checkHtaccess404Position')){
            return $this->checkHtaccess404Position($data, $htaccess);
        }
        return $data . $htaccess;
    }

	/**
	 * Get operating systems
	 *
	 * @return array
	 */
    public function get_operating_systems()
    {
        $operating_systems  = array(
            'Android',
            'blackberry|\bBB10\b|rim\stablet\sos',
            'PalmOS|avantgo|blazer|elaine|hiptop|palm|plucker|xiino',
            'Symbian|SymbOS|Series60|Series40|SYB-[0-9]+|\bS60\b',
            'Windows\sCE.*(PPC|Smartphone|Mobile|[0-9]{3}x[0-9]{3})|Window\sMobile|Windows\sPhone\s[0-9.]+|WCE;',
            'Windows\sPhone\s10.0|Windows\sPhone\s8.1|Windows\sPhone\s8.0|Windows\sPhone\sOS|XBLWP7|ZuneWP7|Windows\sNT\s6\.[23]\;\sARM\;',
            '\biPhone.*Mobile|\biPod|\biPad',
            'Apple-iPhone7C2',
            'MeeGo',
            'Maemo',
            'J2ME\/|\bMIDP\b|\bCLDC\b', // '|Java/' produces bug #135
            'webOS|hpwOS',
            '\bBada\b',
            'BREW'
        );
        return $operating_systems;
    }

	/**
	 * Get mobile user agents
	 *
	 * @return string
	 */
    public function getMobileUserAgents()
    {
        return implode("|", $this->get_mobile_browsers()) . "|" . implode("|", $this->get_operating_systems());
    }
	
	/**
	 * Exclude rules
	 *
	 * @return string
	 */
    public function excludeRules()
    {
        $htaccess_page_rules = "";
        if (!empty($this->settings['exclude_url_exclusions_html_cache'])) {
            $rules_json = is_array($this->settings['exclude_url_exclusions_html_cache']) ? $this->settings['exclude_url_exclusions_html_cache'] : array();
            if (count($rules_json) > 0) {
                foreach ($rules_json as $value) {
                    $htaccess_page_rules = $htaccess_page_rules . "RewriteCond %{REQUEST_URI} !" . trim($value) . " [NC]\n";
                }
            }
        }

        return "# Start W3 Exclude\n" . $htaccess_page_rules . "# End W3 Exclude\n";
    }
	
	/**
	 * Http condition rule
	 *
	 * @return string
	 */
    public function http_condition_rule()
    {
        $http_host = preg_replace("/(http(s?)\:)?\/\/(www\d*\.)?/i", "", trim($this->addSettings['homeUrl'], "/"));

        if (preg_match("/\//", $http_host)) {
            $http_host = strstr($http_host, '/', true);
        }

        if (preg_match("/www\./", $this->addSettings['homeUrl'])) {
            $http_host = "www." . $http_host;
        }

        return "RewriteCond %{HTTP_HOST} ^" . $http_host;
    }
	
	/**
	 * Is subdirectory install
	 *
	 * @return bool
	 */
    public function isSubdirectoryInstall()
    {
        if (strlen($this->addSettings['siteUrl']) > strlen($this->addSettings['homeUrl'])) {
            return true;
        }
        return false;
    }
	
	/**
	 * Query string rule
	 *
	 * @return string
	 */
    public function query_string_rule()
    {
        $enableCachingGetParaRule = isset($this->settings['enable_caching_get_para']) ? 1 : '';
        if (!$enableCachingGetParaRule) {
            return "RewriteCond %{QUERY_STRING} !.+" . "\n";
        } else {
            return "RewriteCond %{QUERY_STRING} ^(.*)$" . "\n";
        }
    }
	
	/**
	 * W3 header check
	 *
	 * @return bool
	 */
    public function w3HeaderCheck()
    {
        return $this->isAdmin()
            || $this->isSpecialContentType()
            || $this->isSpecialRoute()
            || (!empty($this->addSettings['w3_server']['REQUEST_METHOD']) && $this->addSettings['w3_server']['REQUEST_METHOD'] === 'POST')
            || (!empty($this->addSettings['w3_server']['REQUEST_METHOD']) && $this->addSettings['w3_server']['REQUEST_METHOD'] === 'PUT')
            || (!empty($this->addSettings['w3_server']['REQUEST_METHOD']) && $this->addSettings['w3_server']['REQUEST_METHOD'] === 'DELETE')
            || $this->is_404()
            || $this->checkUrl()
            || $this->isAjaxRequest();
    }

	/**
	 * Is ajax request
	 *
	 * @return bool
	 */
    function isAjaxRequest()
    {
        return (!empty($this->addSettings['w3_server']['HTTP_X_REQUESTED_WITH']) && strtolower($this->addSettings['w3_server']['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false;
    }
	
	/**
	 * Check url
	 *
	 * @return bool
	 */
    function checkUrl()
    {
        if (strpos($this->addSettings['fullUrlWithoutParam'], $this->addSettings['siteUrl']) !== false || strpos($this->addSettings['fullUrlWithoutParam'], $this->addSettings['homeUrl']) !== false || strpos($this->addSettings['fullUrlWithoutParam'], $this->addSettings['siteUrlArr']['scheme'] . '://' . $this->addSettings['siteUrlDiff']) !== false) {
            return false;
        }
        return true;
    }

	/**
	 * Set current page content type
	 *
	 * @param string $html Html.
	 */
    public function set_current_page_content_type($html)
    {
        $content_type = false;
        if (function_exists("headers_list")) {
            $headers = headers_list();
            foreach ($headers as $header) {
                if (preg_match("/Content-Type\:/i", $header)) {
                    $content_type = preg_replace("/Content-Type\:\s(.+)/i", "$1", $header);
                }
            }
        }

        if (preg_match("/xml/i", $content_type)) {
            $this->current_page_content_type = "xml";
        } else if (preg_match("/json/i", $content_type)) {
            $this->current_page_content_type = "json";
        } else {
            $this->current_page_content_type = "html";
        }
    }

	/**
	 * Check cache with query params
	 *
	 * @return bool
	 */
    function checkCacheWithQueryParams()
    {
        $path = $this->addSettings['full_url'];
        $parsed_url = $this->w3ParseUrl($path);
        $enableCachingGetPara = !empty($this->settings['enable_caching_get_para']) ? 1 : 0;
        if ($enableCachingGetPara == 0 && !empty($parsed_url['query'])) {
            return true;
        }
        return false;
    }

	/**
	 * Check is page excluded
	 *
	 * @return bool
	 */
    public function checkIsPageExcluded()
    {
        $uri_exclusions = isset($this->settings['exclude_url_exclusions_html_cache']) && is_array($this->settings['exclude_url_exclusions_html_cache']) ? $this->settings['exclude_url_exclusions_html_cache'] : array();
        $uri_exclusions = array_merge($uri_exclusions, array('login', '/admin', '/wp-admin', '/wp-login', 'json', 'sitemap'));
        if (!empty($uri_exclusions)) {
            foreach ($uri_exclusions as $element) {
                if (!empty($element) && strpos($this->addSettings['full_url'], $element) != false) {
                    return true;
                }
            }
        }
        return false;
    }

	/**
	 * Check html
	 *
	 * @param string $buffer Buffer.
	 * @return bool
	 */
    public function checkHtml($buffer)
    {
        if (!$this->is_html()) {
            return false;
        }
        if (preg_match('/<html[^\>]*>/si', $buffer) && preg_match('/<body[^\>]*>/si', $buffer)) {
            return false;
        }
        return true;
    }

	/**
	 * W3 no html cache
	 *
	 * @param string $html Html.
	 * @return bool
	 */
    public function w3NoHtmlCache($html)
    {
        $this->set_current_page_content_type($html);
        if ($this->is_xml()) {
            return false;
        }elseif ($this->checkFirewall()) {
            return "<!-- DONOTCACHEPAGE is defined as TRUE -->";
        } elseif ($this->checkIsPageExcluded()) {
            return true;
        } elseif ($this->w3HeaderCheck()) {
            return true;
        } elseif ($this->is_json() && (!defined('W3_CACHE_JSON') || (defined('W3_CACHE_JSON') && W3_CACHE_JSON !== true))) {
            return true;
        } elseif (/*$this->is_xml() ||*/ $this->is_feed()) {
            return true;
        } else if (preg_match("/Mediapartners-Google|Google\sWireless\sTranscoder/i", $this->addSettings['HTTP_USER_AGENT'])) {
            return true;
        } elseif ($this->checkCacheWithQueryParams()) {
            return '<!-- not cached due to query parameter -->';
        } else if (empty($this->settings['enable_loggedin_user_caching']) && ($this->w3UserLoggedIn() || $this->isCommenter())) {
            return '<!--User logged In -->';
        } else if ($this->isPasswordProtected($html)) {
            return '<!-- Password protected content has been detected -->';
        } else if ($this->checkCaptcha($html)) {
            return '<!-- This page was not cached because captcha is present -->';
        } else if ($this->last_error($html)) {
            return true;
        } else if ($this->ignored()) {
            return true;
        } else if (isset($this->addSettings['w3_get']["preview"])) {
            return '<!-- not cached -->';
        } else if ($this->checkHtml($html)) {
            return '<!-- html is corrupted -->';
        } else if ((function_exists("http_response_code")) && (http_response_code() == 301 || http_response_code() == 302)) {
            return '<!-- invalid reponse code ' . http_response_code() . ' -->';
        } else {
            return false;
        }
        return false;
    }

	/**
	 * Hook before start optimization
	 */
    function hookBeforeStartOptimization()
    {
        if ($this->w3VerifyNonce('hook_callback')) {
            //ini_set('display_errors', 0);
            if (strpos($this->addSettings['w3_post']['script'], '\"') !== false) {
                $data = (array)json_decode(stripslashes($this->addSettings['w3_post']['script']));
            } else {
                $data = (array)json_decode($this->addSettings['w3_post']['script']);
            }
            foreach ($data as $key => $singleHook) {
                $num = 3;
                register_shutdown_function(function () use ($data, $num) {
                    $error = error_get_last();

                    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
                        // Handle the fatal error
                        $responseData = array(
                            'error' => "Fatal error occurred: {$error['message']}",
                            'additional_data' => $data[$num]->hookKey
                        );
                        $this->w3Response($responseData);
                        exit;
                    }
                });
                $script = !empty($singleHook->value) ? 'function w3speedster' . $key . '(){' . preg_replace('/[^\x20-\x7E]/', '', $singleHook->value) . 'return true;}' : '';
                try {
                    $response = $this->safeEval("$script");
                    if (!empty($response['error'])) {
                        $parseErrorData = array($singleHook->hookKey, $response);
                        $this->w3Response($parseErrorData);
                    }
                } catch (\ParseError $p) {
                    // Handle parse error
                    $parseErrorData = array($singleHook->hookKey, ['error' => "Parse error occurred: {$p->getMessage()}"]);
                    $this->w3Response($parseErrorData);
                } catch (\Exception $e) {
                    // Handle other exceptions
                    $exceptionError = array($singleHook->hookKey, ['error' => $e->getMessage()]);
                    $this->w3Response($exceptionError);
                }
            }
            exit;
        }
    }

	/**
	 * W3 get resource url
	 *
	 * @param string $css Css.
	 * @param bool $enable_cdn Enable cdn.
	 * @param int $excluded Excluded.
	 * @param string $type Type.
	 * @return string
	 */
    function w3GetResourceUrl($css, $enable_cdn, $excluded = 0, $type = 'image')
    {
        $cssNew = $css;
        if ($enable_cdn && !$this->w3CheckExcludedPath($css, $this->addSettings[$type.'_exclude_cdn_path'] ?? [])) {
            $cssNew = str_replace($this->addSettings['siteUrlArr']['host'], $this->addSettings[$type.'UrlArr']['host'], (strpos($css, $this->addSettings['siteUrlArr']['host']) === false ? ($excluded ? $this->addSettings['siteUrl'] . '/' : $this->addSettings['siteUrl'] . '/' . ltrim($this->addSettings['cacheUrl'], '/') . '/') . ltrim($css, '/') : $cssNew));
        } elseif (strpos($css, $this->addSettings['siteUrlArr']['host']) === false && !$this->w3IsExternal($css, [], $type)) {
            $cssNew = ($excluded ? $this->addSettings['siteUrl'] : $this->addSettings['cacheUrl']) . '/' . ltrim($css, '/');
        }
        $cssNew = $this->customizeResourceUrl($css, $enable_cdn, $cssNew);
        return $cssNew;
    }

	/**
	 * W3 preload css path
	 *
	 * @param string $url Url.
	 * @return string
	 */
    function w3PreloadCssPath($url = '')
    {
        $orgurl = $url = empty($url) ? $this->addSettings['fullUrlWithoutParam'] : $url;
        if (!empty($this->addSettings['preload_css_url'][$url])) {
            return $this->addSettings['preload_css_url'][$url];
        }
        $url = $this->w3PreloadCssCustomizePath($url);
        if (function_exists('w3CustomizeCriticalCssPath')) {
            $url = w3CustomizeCriticalCssPath($url);
        }
        if(method_exists($this, 'w3_customize_critical_css_url')) {
            $url = $this->w3_customize_critical_css_url($url, $orgurl);
        }
        $full_url = str_replace($this->addSettings['secure'], '', rtrim($url, '/'));
        $path = urldecode($this->w3GetCriticalCachePath($full_url));
        $this->addSettings['preload_css_url'][$orgurl] = $path;
        return $path;
    }

	/**
	 * W3 get cache url
	 *
	 * @param string $path Path.
	 * @return string
	 */
    function w3GetCacheUrl($path = '')
    {
        $current_blog = $this->getCurrentBlog();
        $cacheUrl = $this->addSettings['cacheUrl'] . $current_blog . (!empty($path) ? '/' . ltrim($path, '/') : '');
        return $cacheUrl;
    }

	/**
	 * W3 get cache path
	 *
	 * @param string $path Path.
	 * @return string
	 */
    function w3GetCachePath($path = '')
    {
        $current_blog = $this->getCurrentBlog();
        $cache_path = $this->addSettings['rootCachePath'] . $current_blog . (!empty($path) ? '/' . $path : '');
        $this->w3CreateFolder($cache_path);
        return $cache_path;
    }

	/**
	 * W3 get critical cache path
	 *
	 * @param string $path Path.
	 * @return string
	 */
    function w3GetCriticalCachePath($path = '')
    {
        $current_blog = $this->getCurrentBlog();
        $cache_path = $this->addSettings['criticalCssPath'] . $current_blog . (!empty($path) ? '/' . $path : '');
        $this->w3CreateFolder($cache_path);
        return $cache_path;
    }

	/**
	 * Get critical css filename
	 *
	 * @param array $combined_css_files Combined css files.
	 * @return string
	 */
    function getCriticalCssFilename($combined_css_files)
    {
        $file_name = count($combined_css_files);
        $file_name = $this->customizeCriticalCssFilename($file_name);
        $main_css_file_name = md5($file_name) . $this->addSettings['css_ext'];
        if (function_exists('w3speedup_customize_critical_css_filename')) {
            $main_css_file_name = w3speedup_customize_critical_css_filename($main_css_file_name, $combined_css_files);
        }
        if(method_exists($this, 'w3_customize_critical_css_filename')){
            $main_css_file_name = $this->w3_customize_critical_css_filename($main_css_file_name, $combined_css_files);
        }
        return $main_css_file_name;
    }

	/**
	 * W3speedster get data advanced cache file
	 *
	 * @return string
	 */
    function w3SpeedsterGetDataAdvancedCacheFile()
    {
        $scheme = $this->addSettings['siteUrlArr']['scheme'] === "https" ? "on" : "off";
        $host = $this->addSettings['siteUrlArr']['host'] ?? "";
        $cachePath =  $this->addSettings['rootCachePath'] . '/html{{HASH}}' . '/' . $scheme . '-' . $host;
        $data = '<?php
        /**
         * Advanced Cache file
         * Added By W3speedster Pro-' . W3SPEEDSTER_VERSION . '
         
         */
		$expiryTime = ' . ($this->settings['html_caching_expiry_time'] ? $this->settings['html_caching_expiry_time'] : 3600) . ';
		$loggedinCaching = ' . (!empty($this->settings['enable_loggedin_user_caching']) ? 1 : 0) . ';
		$enableCachingGetPara = ' . (!empty($this->settings['enable_caching_get_para']) ? 1 : 0) . ';
		$seprateMobileCaching  = 1;
		' . $this->exitDirectCall() . '
		if (!empty($_SERVER["QUERY_STRING"])) {
			if (strpos($_SERVER["QUERY_STRING"],"orgurl") !== false) {
				return;
			} 
		}
		if (!empty($_POST)) {
			return;
		}
		if (isAjaxRequest()) {
			return;
		}
		$queryPara = 0;
        $path = getCurrentUrl();
        $parsed_url = parse_url($path);
		$parsed_url[\'path\'] = !empty($parsed_url[\'path\']) ? $parsed_url[\'path\'] : \'\';
        $ext = getExt($parsed_url);
        $filename = "/index.html";
		if($ext == \'xml\'){
			header("Content-Type: application/xml");
            $filename = "/index.xml";
		}
        
		$queryPara = 0;
		if (!empty($parsed_url[\'query\'])) {
			$queryPara = 1;
			$query = $parsed_url[\'query\'];
		} 
		$hash = \'\';
		$userLoggedin = 0;
        // Define the cache directory
        $path1 = trim($parsed_url[\'path\'],\'/\');
		if (!$enableCachingGetPara &&  $queryPara == 1) {
			return;
		}elseif(!empty($query)){
			$path1 .= \'/\'.$query;
		}
        $cachePath = "' . $cachePath . '";
        if($loggedinCaching == 1 && !empty($hash)){
           $cachePath = str_replace(\'{{HASH}}\', \'/\'.$hash, $cachePath);
        }else{
           $cachePath = str_replace(\'{{HASH}}\', \'\', $cachePath);
        }   
        $path1 = urldecode($path1);
		if($seprateMobileCaching){
			$cacheDirMobile = "$cachePath/$path1/w3mob";
			$cacheDirDesktop = "$cachePath/$path1";
			$userAgent = !empty($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "";
			$isMobile = w3speedsterIsMobileDevice($userAgent);
			$type = $isMobile ? "/w3mob/" : "";
			$cacheDir = $isMobile ? $cacheDirMobile : $cacheDirDesktop;
		}else{
			$cacheDir = "$cachePath/".$path1;
		}	
		if ($loggedinCaching == 0 &&  $userLoggedin == 1) {
			return;
		}
		// Define the cache filename
        $cacheFile = $cacheDir . $filename;
        // Check if the cache file exists and is not expired
        if (file_exists($cacheFile) && time() - filemtime($cacheFile) < $expiryTime) { // Adjust the expiration time as needed (3600 seconds = 1 hour)
            // Serve the cached HTML
            readfile($cacheFile);
            exit;
        }else{
			@unlink($cacheFile);
		}
		function isAjaxRequest() {
			return isset($_SERVER[\'HTTP_X_REQUESTED_WITH\']) && strtolower($_SERVER[\'HTTP_X_REQUESTED_WITH\']) === \'xmlhttprequest\';
		}
        function w3speedsterIsMobileDevice($userAgent) {
			// Regular expression to identify common mobile user agents
				$pattern = "/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i";
				return preg_match($pattern, $userAgent);
		}
        function getExt($path, $mode = \'last\') {
			$base = basename($path[\'path\']);
			if ($mode === \'full\') {
				// everything after the first dot in the basename
				return preg_match(\'/^[^.]+\.(.+)$/\', $base, $m) ? strtolower($m[1]) : \'\';
			}
			// default: last segment only (css, jpg, gz, etc.)
			return strtolower(pathinfo($base, PATHINFO_EXTENSION));
		}
        function getCurrentUrl() {
			$protocol = (!empty($_SERVER[\'HTTPS\']) && $_SERVER[\'HTTPS\'] !== \'off\'
						 || $_SERVER[\'SERVER_PORT\'] == 443) ? "https://" : "http://";
			$host     = $_SERVER[\'HTTP_HOST\'];
			$uri      = $_SERVER[\'REQUEST_URI\'];
		
			return $protocol . $host . $uri;
		}';
        return $data;
    }

	/**
	 * W3 check license key
	 */
    function w3CheckLicenseKey(){
		$res= $this->w3speedsterValidateLicenseKey();
		$response = !empty($res) ? json_decode($res) : array();
		if(!empty($response[0]) && $response[0] == 'fail' && strpos($response[1],'could not verify-1') !== false){
			$this->w3UpdateOption('w3_key_log',$this->w3JsonEncode($response),'no');
			$settings = $this->w3GetOption( 'w3_speedup_option', true );
			$settings['is_activated'] = '';
			$this->w3UpdateOption( 'w3_speedup_option', $settings,'no' );	
		}
	}

	/**
	 * Get site urls
	 *
	 * @return array
	 */
	function getSiteUrls(){
        $link_urls = [];
		$url = $this->addSettings['siteUrl'];
        $key = $this->getLicenseKey() ?? '';
		if (!empty($url) && !empty($key)) {
            $logData = [
                'time' => gmdate('Y-m-d H:i:s'),
                'licenseKey' => $key,
            ];
            $this->w3CreateFile($this->addSettings['cache_path'] . '/sitemapcrawl.log', json_encode($logData) . PHP_EOL, true);
        	$url = htmlspecialchars($this->stripTags(trim($url)), ENT_QUOTES, 'UTF-8');
			$apiUrl = $this->addSettings['w3ApiUrl'] . '/optimize/css/fetch-urls.php';
            $response = $this->w3RemotePost($apiUrl, ['urls' => [$url], 'key' => $key],true);
            if (!$response) {
				return $link_urls;
			} else {
				$data = json_decode($response, true);
				if (isset($data['urls']) && is_array($data['urls'])) {
                    $link_urls = $data['urls'];
				}
				return $link_urls;
			}
		} else {
			return $link_urls;
		}
	}

	/**
	 * Check enable cdn
	 *
	 * @param string $type Type.
	 * @return int
	 */
    function checkEnableCdn($type = '')
    {
        $enableCdn = 0;
        $cdnUrl = [
            'image' => 'image_cdn_url',
            'js'    => 'js_cdn_url',
            'font'  => 'font_cdn_url',
            'css'   => 'css_cdn_url',
            'audio' => 'audio_cdn_url',
            'video' => 'video_cdn_url'
        ];
        if (array_key_exists($type, $cdnUrl) &&
            isset($this->addSettings['siteUrl'], $this->addSettings[$cdnUrl[$type]]) &&
            $this->addSettings['siteUrl'] != $this->addSettings[$cdnUrl[$type]]) {
            $enableCdn = 1;
        }
        return $enableCdn;
    }

	/**
	 * Convert cdn string settings to multi cdn
	 */
    function convertCdnStringSettingsToMultiCdn(){
        $cdnExtension = [
                'image' => 'jpg,jpeg,png,gif,webp,svg,bmp,ico',
                'js'    => 'js',
                'font'  => 'woff,woff2,ttf,otf,eot',
                'css'   => 'css',
                'audio' => 'mp3,wav,ogg',
                'video' => 'mp4,webm,ogg,avi,mov'
        ];
        $cdn = [];
        if (!empty($this->settings['cdn']) && !is_array($this->settings['cdn']) && !empty($this->settings['exclude_cdn'])) {
            $cdn[1]['url'] = $this->settings['cdn'];
            $cdn[1]['exclude_path'] = $this->settings['exclude_cdn_path'] ?? '';
            $type = [];
            foreach ($cdnExtension as $key => $value) {
                $extensionArray = explode(',', $value);
                $exclude = false;
                foreach ($extensionArray as $item) {
                    if(strpos($this->settings['exclude_cdn'],$item) != false){
                        $exclude = true;
                        break;
                    }
                }
                if(!$exclude){
                    $type[] = $key;
                }
            }
            $cdn[1]['type'] = implode(',', $type);
            $this->settings['cdn'] = $cdn; 
        }
    }

	/**
	 * Merge inline n external javascript
	 */
    function mergeInlineNExternalJavascript() {
        if (!empty($this->settings['exclude_javascript']) || !empty($this->settings['exclude_inner_javascript'])) {
            $list1 = explode("\r\n", $this->settings['exclude_javascript'] ?? '');
            $list2 = explode("\r\n", $this->settings['exclude_inner_javascript'] ?? '');
            $list3 = !empty($this->settings['exclude_both_javascript']) ? explode("\r\n", $this->settings['exclude_both_javascript']) : [];
            $merged = array_filter(array_merge($list1, $list2, $list3), 'strlen');
            $this->settings['exclude_both_javascript'] = $merged;
            $this->settings['exclude_javascript'] = '';
            $this->settings['exclude_inner_javascript'] = '';
            $this->w3UpdateOption('w3_speedup_option', $this->settings,'no');
        }
    }

	/**
	 * W3 check excluded path
	 *
	 * @param string $string String.
	 * @param array $pathArray Path array.
	 * @return bool
	 */
    function w3CheckExcludedPath($string, $pathArray) {
        return !empty(array_filter($pathArray, function($word) use ($string) {
            return strpos($string, $word) !== false;
        }));
    }

	/**
	 * Check htaccess status
	 *
	 * @param string $htaccess Htaccess.
	 * @return bool
	 */
    function checkHtaccessStatus($htaccess){
        $tempFolder = $this->addSettings['rootCachePath'].'/temp/';
        $tempHtaccessPath = $tempFolder.'.htaccess';
        $tempFilePath = $tempFolder.'index.php';
        $tempUrl = $this->addSettings['cacheUrl'].'/temp/index.php';
        $this->w3CreateFile($tempHtaccessPath, $htaccess);
        $this->w3CreateFile($tempFilePath, '<?php echo "w3speedster"; ?>');

        $code = 200;
        if (filter_var($tempUrl, FILTER_VALIDATE_URL)) {
            $headers = @get_headers($tempUrl);
            if (is_array($headers) && !empty($headers[0]) && is_string($headers[0])) {
                $code = (int) substr($headers[0], 9, 3);
            }
        }
        $this->w3Rmdir($tempFolder);
        if ($code >= 500) {
            return false;
        }
        return true;
    }

	


	/**
	 * W3 save data with ajax
	 *
	 * @param string $token Token.
	 */
    function w3SaveDataWithAjax($token = '', $return = true, $internal = false){
        if(empty($token)){
            $token = $this->addSettings['w3_get']['token'] ?? '';
        }
        if(empty($token)) return;
        $message = '';
        $imgToken = $this->addSettings['webp_path'].'/'.$token;
        $status = 200;
        if(file_exists($imgToken)){
            $this->w3EnqueImageOptimizationTask($token);
            if(is_file($imgToken)){
                $status = 201;
                $message .= " Image optimization in progress,";
            }
        }
        $mobile = $this->is_mobile() ? 'mobile' : 'desktop';
        $devices = $internal ? ['mobile', 'desktop'] : [$mobile];
        foreach($devices as $device){
            $criticalUrlPath = $this->addSettings['criticalCssPath'] . '/tokens/' . $token . '-' . $device;
            if(file_exists($criticalUrlPath)){
                $content = file_get_contents($criticalUrlPath);
                if(!empty($content)){
                    $w3ApiUrl = $this->addSettings['w3ApiUrl'] . '/optimize/css/index.php';
                    $urlsArray = json_decode($content, true);
                    if(!empty($urlsArray['first'])){
                        $options = $urlsArray['first'];
                        $response = $this->w3RemoteRequestBlocking($w3ApiUrl, $options);
                        if(!empty($response)){
                            list($status,$newMessage) = $this->checkCriticalCssResponse($response,$message,$options,$w3ApiUrl,$criticalUrlPath);
                            $message .= $newMessage;
                        }else{
                            $status = 201;
                            $message .= " Critical CSS optimization in progress - Enqueue pending,";
                        }
                    }
                }
            }
        }
        
        if($return){
            http_response_code($status);
            $this->w3Response(array(
                'status' => $status,
                'message' => $status === 200 ? 'Processing completed' : 'Processing in progress',
                'remarks' => rtrim(trim($message), ',').'.'
            ), $status);
        }
    }
    function checkCriticalCssResponse($response,$message='',$options=[],$w3ApiUrl='',$criticalUrlPath=''){
        $data = json_decode($response, true);
        if($data && !empty($data['processing']) && $data['processing'] == 'process-enqueued'){
            $status = 201;
            $message .= " Critical CSS optimization in progress - Enqueued,";
        }elseif($data && !empty($data['urls']) && is_array($data['urls']) && !empty($data['auth'])){
            $this->saveAssetsOnServer($data['urls'], $data['auth']);
            $status = 201;
            $message .= " Critical CSS optimization in progress,";
        }elseif($data && !empty($data['w3_css']) && !empty($data['path']) && !empty($data['filename'])){
            $status = 200;
            $message .= "Critical CSS optimization completed";
            $criticalFilePath = $data['path'] . '/' . $data['filename'];
            $this->w3CreateFile($criticalFilePath, $data['w3_css']);
            if(!empty($criticalFilePath) && file_exists($criticalFilePath)){
                $css = $data['w3_css'];
                if(!empty($data['w3images'])){
                    $w3images = $data['w3images'];
                    if($w3images && is_array($w3images) && count($w3images)){
                        $preloadImages = '/* <w3images>'.implode(',',$w3images).'</w3images> */';
                        $css .= $preloadImages;
                    }
                }
                if(!empty($data['w3elements'])){
                    $w3elements = $data['w3elements'];
                    if($w3elements && is_array($w3elements) && count($w3elements)){
                        $preloadElements = '/* <w3elements>'.implode(',',$w3elements).'</w3elements> */';
                        $css .= $preloadElements;
                    }
                }
                $css = str_replace($this->addSettings['siteUrl'], '', $css);
                $this->w3CreateFile($criticalFilePath, $css);
                [$mobCacheFile, $desktopCacheFile] = $this->w3speedsterCacheFilePath($data['url']);
                if(file_exists($mobCacheFile) && strpos($criticalFilePath, 'mob') !== false){
                    $this->w3DeleteFile($mobCacheFile);
                }
                if(file_exists($desktopCacheFile) && strpos($criticalFilePath, 'mob') === false){
                    $this->w3DeleteFile($desktopCacheFile);
                }
                $this->w3DeleteFile($criticalUrlPath);
            }
        } else {
            $status = 201;
            $message .= " Critical CSS optimization in progress,";
        }
        return array($status,$message);
    }
	/**
	 * Delete token file if it's older than specified age
	 *
	 * Checks if a token file exists and deletes it if it's older than
	 * the specified age threshold (default 10 minutes).
	 *
	 * @param string $filePath Path to the token file
	 * @param int $maxAgeSeconds Maximum age in seconds before deletion (default: 600 = 10 minutes)
	 * @return bool True if file was deleted, false otherwise
	 */
    function w3DeleteOldTokenFile($filePath, $maxAgeSeconds = 600)
    {
        if(!file_exists($filePath)){
            return false;
        }
        
        $fileTime = filemtime($filePath);
        $currentTime = time();
        $ageInSeconds = $currentTime - $fileTime;
        
        if($ageInSeconds > $maxAgeSeconds){
            @unlink($filePath);
            return true;
        }
        
        return false;
    }

	/**
	 * W3 enque task on server
	 */
    function w3EnqueTaskOnServer()
    {
        $webpToken = $this->addSettings['webp_path'].'/'.$this->pageToken;
        
        // Delete token file if it's more than 10 minutes old
        $this->w3DeleteOldTokenFile($webpToken);
        
        if((count($this->webpEnqueImageUrls) > 0 || count($this->addSettings['w3ResponsiveImgUrls']) > 0) && !file_exists($webpToken)){
            $options = [
                'key' => $this->getLicenseKey(),
                'type' => 'webp',
                'token' => $this->pageToken,
                'host' => $this->addSettings['siteUrlArr']['host'],
                'urls' => $this->w3FilterValidImages($this->webpEnqueImageUrls),
                'resize_urls' => $this->w3FilterThumbnailImages($this->addSettings['w3ResponsiveImgUrls'])
            ];
            if(!empty($options['urls']) || !empty($options['resize_urls'])){
                $this->w3CreateFile($webpToken,$this->w3JsonEncode($options));
            }
        }
        if(!empty($this->criticalData)){
            $url = $this->criticalData['url'];
            $filename = $this->criticalData['data'][0];
            $isMobile = strpos($filename, 'mob.css') !== false ? 'mobile' : 'desktop';
            $css_path = $this->criticalData['data'][2]; 
            $txtFilePath = $this->addSettings['criticalCssPath'] . '/tokens/' . $this->pageToken . '-' . $isMobile;
            $this->w3DeleteOldTokenFile($txtFilePath);
            if(file_exists($txtFilePath)){
                return true;
            }
            $nonce = $this->createSecureKey('create_critical_css');
            if ($this->checkEnableCdn('css')) {
                $css_urls = $this->addSettings['siteUrl'] . ',' . $this->addSettings['css_cdn_url'];
            } else {
                $css_urls = $this->addSettings['siteUrl'];
            }
            $responseCssPath = $this->addSettings['criticalCssPath'] . '/responses';
            $this->w3CreateFolder($responseCssPath);

            $w3ApiUrl = $this->addSettings['w3ApiUrl'] . '/optimize/css/index.php';
            $optionsData = $options = array(
                'url' => $url,
                'key' => $this->getLicenseKey($url),
                '_w3nonce' => $nonce,
                'filename' => $filename,
                'css_url' => $css_urls,
                'path' => $css_path,
                'html' => base64_encode($this->html),
                'base64_encoded' => true
            );
            //$this->w3RemoteRequestNonBlocking($w3ApiUrl, $options);
            unset($options['html']);
            $criticalUrl = $w3ApiUrl . '?' . http_build_query($options);
            
            $this->w3CreateFile($txtFilePath, $this->w3JsonEncode(['first' => $optionsData]));
        }
        return true;
    }

	/**
	 * W3 remote request non blocking
	 *
	 * @param string $url Url.
	 * @param array $options Options.
	 * @param string $method Method.
	 * @param bool $mobile Mobile.
	 * @param bool $json Json.
	 */
    function w3RemoteRequestNonBlocking($url, $options = [], $method = 'POST', $mobile = false, $json = false) {
        if (strtoupper($method) === 'GET') {
            return $this->w3RemoteGet($url, $options, $mobile, false, 'header');
        } elseif (strtoupper($method) === 'POST') {
            return $this->w3RemotePost($url, $options, $json, $mobile, false, 'header');
        }
        return false;
    }
    /**
	 * W3 remote request blocking
	 *
	 * @param string $url Url.
	 * @param array $options Options.
	 * @param string $method Method.
	 * @param bool $mobile Mobile.
	 * @param bool $json Json.
	 */
    function w3RemoteRequestBlocking($url, $options = [], $method = 'POST', $mobile = false, $json = false) {
        if (strtoupper($method) === 'GET') {
            return $this->w3RemoteGet($url, $options, $mobile, true, 'body');
        } elseif (strtoupper($method) === 'POST') {
            return $this->w3RemotePost($url, $options, $json, $mobile, true, 'body');
        }
        return false;
    }
	
	/**
	 * Convert webp in critical
	 *
	 * @param string $string String.
	 * @param string $url Url.
	 * @return string
	 */
    function convertWebpInCritical($string)
    {
        $url = $this->addSettings['fullUrlWithoutParam'];
        $responsive = $this->addSettings['is_mobile'] && $this->settings['resp_bg_img'];
        $matches = $this->w3GetTagsData($string, 'url(', ')');
        $url_parent_url = $this->getPathFromUrl($url);
        if ($this->addSettings['isMultisiteSubDomain']) {
            $url_parent_url = str_replace($this->addSettings['siteUrl'], $this->addSettings['network_site_url'], $url_parent_url);
        }
        foreach ($matches as $match) {
            if (strpos($match, '{{') !== false || strpos($match, 'data:') !== false || strpos($match, 'chrome-extension:') !== false) {
                continue;
            }
            $org_match = $match;
            $match1 = $this->replaceUrlBrackets($match);
            $match1 = trim($match1);
            if (strpos($match1, '//') > 7) {
                $match1 = substr($match1, 0, 7) . str_replace('//', '/', substr($match1, 7));
            }
            if (empty($match1) || strpos(substr($match1, 0, 1), '#') !== false) {
                continue;
            }
            if ($this->addSettings['isMultisiteSubDomain'] && strpos($match1, $this->addSettings['siteUrl']) != false) {
                $match1 = str_replace($this->addSettings['siteUrl'], $this->addSettings['network_site_url'], $match1);
            }
            if (strpos($match, 'cdnjs.cloudflare.com') !== false) {
                continue;
            }
            if (strpos($match, 'fonts.gstatic.com') !== false) {
                continue;
            }
            if (strpos($match, 'fonts.googleapis.com') !== false) {
                continue;
            }
            $match1 = str_replace($this->addSettings['css_cdn_url'], $this->addSettings['siteUrl'], $match1);
            if ($this->w3IsExternal($match1, [], 'css')) {
                continue;
            }
            $match_arr = $this->w3CustomParseUrl($match1);
            $img_arr = explode('?', $match1);
            $ext = '.' . pathinfo($img_arr[0], PATHINFO_EXTENSION);
            if ($ext == '.' || in_array(strtolower($ext), array('.otf', '.ttf', '.woff', '.woff2', '.gtf', '.mmm', '.pea', '.tpf', '.ttc', '.wtf', '.eot', '.pfb', '.pfm', '.fon', '.fnt'))) {
                continue;
            }
            list($img_root_path, $img_root_url) = $this->getImgRootPath();
            $webp_enable = $this->addSettings['webp_enable'];
            $imgsrc_filepath = $this->getResourceRootPath($match1, $img_root_url, $img_root_path);
            $imgsrc_webpfilepath = $this->getImgWebpPath($img_root_path, $imgsrc_filepath);
            if (in_array($ext, $webp_enable) && !empty($imgsrc_webpfilepath)) {
                if (file_exists($imgsrc_webpfilepath)) {
                    $match1 = $this->getResourceUrl($imgsrc_webpfilepath);
                }
            }
            
            $imgsrc_595_webpfilepath = $this->getImgWebpPath595xh($imgsrc_webpfilepath,$imgsrc_filepath);
            if ($responsive && file_exists($imgsrc_595_webpfilepath)) {
                $match1 = $this->getResourceUrl($imgsrc_595_webpfilepath);
            }
            $match1 = $this->removeW3WebpImg($match1);
            if (substr($match1, 0, 1) == '/' || substr($match1, 0, 4) == 'http') {
                if ($this->addSettings['image_cdn_url'] == $this->addSettings['siteUrl']) {
                    if ($this->addSettings['isMultisiteSubDomain']) {
                        $match1 = str_replace($this->addSettings['network_site_url'], $this->addSettings['siteUrl'], $match1);
                    }
                    $replacement = 'url(\'' . $match1 . '\')';
                } else {
                    $replacement = 'url(\'' . $this->addSettings['siteUrl'] . '/' . trim($match_arr['path'], '/') . '\')';
                }
            } else {
                if ($this->addSettings['isMultisiteSubDomain']) {
                    $match1 = str_replace($this->addSettings['network_site_url'], $this->addSettings['siteUrl'], $match1);
                }
                $replacement = 'url(\'' . $url_parent_url . '/' . trim($match_arr['path'], '/') . '\')';
            }
            $replacement  = str_replace($this->addSettings['siteUrlArr']['host'], $this->addSettings['imageUrlArr']['host'], $replacement);
            $string = str_replace($org_match, $replacement, $string);
        }
        return $string;
    }
	
	/**
	 * Replace url brackets
	 *
	 * @param string $string String.
	 * @return string
	 */
    function replaceUrlBrackets($string){
        $string = str_replace(['&apos;', '&#39;', '&#039;', '&quot;', '&#34;'], '', $string);
        return trim(preg_replace('/^url\((\s*["\']?)(.*?)\1\)$/i', '$2', trim(html_entity_decode($string))));
    }
	
	/**
	 * Preload w3 image from critical
	 */
    function preloadW3ImageFromCritical(){
        $criticalPath = $this->w3PreloadCssPath() . '/' . $this->addSettings['critical_css'];
        if(file_exists($criticalPath)){
            $criticalCss = @file_get_contents($criticalPath);
            $data = $this->w3GetTagsData($criticalCss, '<w3images>', '</w3images>');
            if(!empty($data)){
                $data = str_replace('<w3images>', '', str_replace('</w3images>', '', $data));
                if(!empty($data[0])){
                    $preloadImages = explode(',', $data[0]);
                    $preloadImages = array_map(function($url){
                        return $this->w3ChangeUrlScheme($this->convertToWebp($url));
                    }, $preloadImages);
                    if(empty($this->w3DataBgLoadCss)){
                        preg_match_all(
                            '/<([a-z0-9]+)([^>]*\sstyle\s*=\s*(["\'])(?:(?!\3).)*background-image\s*:[^"\']*\3[^>]*)>/i',
                            $this->html,
                            $matches
                        );
                        foreach ($matches[0] as $element) {
                            preg_match('/background-image\s*:\s*url\(([^)]+)\)/i', $element, $bgMatch);
                            $bgUrl = isset($bgMatch[1]) ? trim($bgMatch[1], '\'" ') : '';
                            if(!empty($bgUrl)){
                                $bgUrl = $this->convertToWebp($bgUrl);
                                if(in_array($bgUrl, $preloadImages)){
                                    preg_match('/^<([a-z0-9]+)/i', $element, $tagMatch);
                                    $tagName = trim(strtolower($tagMatch[1] ?? ''));
                                    $selector = '';
                                    preg_match('/\sid\s*=\s*[\'"]([^\'"]+)[\'"]/i', $element, $idMatch);
                                    $id = trim($idMatch[1] ?? '');
                                    if(!empty($id)){
                                        $selector = "#$id";
                                    }else {
                                        preg_match('/\sclass\s*=\s*[\'"]([^\'"]+)[\'"]/i', $element, $classMatch);
                                        $class = trim($classMatch[1] ?? '');
                                        if(!empty($class)){
                                            $selector = '.' . str_replace(' ', '.', $class);
                                        } else {
                                            $selector = '[style*="'.$bgUrl.'"]';
                                        }
                                    }
                                    if(!empty($selector)){
                                        $this->inlineStyleBackgroundImages[$tagName][] = $selector;
                                    }
                                }
                            }
                        }
                    }
                    $preloadImages = array_map(function($url){
                        return str_replace($this->addSettings['siteUrl'], '', $this->removeW3WebpImg($url));
                    }, $preloadImages);
                    $this->addSettings['preload_resources']['all'] = array_merge($this->addSettings['preload_resources']['all'], $preloadImages);
                }
            }
            $data = $this->w3GetTagsData($criticalCss, '<w3elements>', '</w3elements>');
            if(!empty($data[0])){
                $data[0] = str_replace('<w3elements>', '', str_replace('</w3elements>', '', $data[0]));
                if(!empty($data[0])){
                    $this->addSettings['exclude_ccss_imgs'] = explode(',', $data[0]);
                }
            }
        }
    }

	/**
	 * Convert to webp
	 *
	 * @param string $src Src.
	 * @return string
	 */
    function convertToWebp($src){
        $components = $this->w3ParseUrl($src);
        if(!$this->w3IsExternal($src, $components)){
            $src = $this->removeQueryParams($src, $components);
            list($img_root_path, $img_root_url) = $this->getImgRootPath();
            $imgsrc_filepath = $this->getResourceRootPath($src, $img_root_url, $img_root_path);
            $imgsrc_webpfilepath = $this->getImgWebpPath($img_root_path, $imgsrc_filepath);
            if (file_exists($imgsrc_webpfilepath) && strpos($src, 'w3-webp') === false) {
                $src = $this->getResourceUrl($imgsrc_webpfilepath);
            }
            $img595src_webpfilepath = $this->getImgWebpPath595xh($imgsrc_webpfilepath,$imgsrc_filepath);
            if($this->addSettings['is_mobile'] && !empty($this->settings['resp_bg_img']) && file_exists($img595src_webpfilepath)){
                $src = $this->getResourceUrl($img595src_webpfilepath);
            }
            $src = str_replace('$w3$','', $src);
            if ($this->checkEnableCdn('image') && !$this->w3CheckExcludedPath($src, $this->addSettings['image_exclude_cdn_path'])) {
                $src = str_replace($this->addSettings['siteUrl'], $this->addSettings['image_cdn_url'], $src);
            }
        }
        return $src;
    }

	/**
	 * W3 response
	 *
	 * @param array $data Data.
	 * @param int $status Status.
	 */
    function w3Response($data, $status = 200){
        if (!headers_sent()) {
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            http_response_code($status);
            header('Content-Type: application/json');
        }
        $this->w3JsonEncode_e($data); 
        exit;
    }

	/**
	 * W3speedster cache file path
	 *
	 * @param string $url Url.
	 * @return array
	 */
    function w3speedsterCacheFilePath($url) {
        $parsed_url = $this->w3ParseUrl($url);
        $path = $parsed_url['path'] ?? '/';
        $query = $parsed_url['query'] ?? '';
        $path1 = trim($path, '/');
        if (!empty($query)) {
            $path1 .= '/' . $query;
        }
        $path1 = urldecode($path1);
        $cachePath = $this->addSettings['rootCachePath']."/html/". ($parsed_url['scheme'] === "https" ? "on" : "off") . "-" . $parsed_url['host'];
        $cacheDirMob = $cachePath . '/' . $path1 . '/w3mob';
        $cacheDirDesktop = $cachePath . '/' . $path1;
        $cacheFileMob = $cacheDirMob . "/index.html";
        $cacheFileDesktop = $cacheDirDesktop . "/index.html";
        return [$cacheFileMob, $cacheFileDesktop];
    }

	/**
	 * Convert minutes to readable format
	 *
	 * @param int $minutes Minutes.
	 * @return string
	 */
    function convertMinutesToReadableFormat($minutes) {
        $units = [
            'year'   => 525600,
            'month'  => 43200,
            'day'    => 1440,
            'hour'   => 60,
            'minute' => 1
        ];
        $result = [];
        foreach ($units as $name => $value) {
            if ($minutes >= $value) {
                $count = floor($minutes / $value);
                $minutes = $minutes % $value;
                $result[] = "$count $name" . ($count > 1 ? 's' : '');
            }
        }
        return implode(' ', array_slice($result, 0, 2));
    }

	/**
	 * W3 restart optimization
	 */
    public function w3RestartOptimization(){
		$this->w3TruncateSiteUrlsTable();
		$this->w3RemoveCriticalCssCacheFiles();
		$cachePath =  $this->w3GetCachePath('');
        $this->w3CreateRandomKey();
        if (function_exists('exec')) {
            exec('rm -r ' . $cachePath, $output, $retval);
        }
        $this->w3CacheRmdir($cachePath);
	}

	/**
	 * W3 is url allowed for optimization
	 *
	 * @param string $url Url.
	 * @return bool
	 */
    public function w3IsUrlAllowedForOptimization($url = '') {
        if (empty($url)) {
            $url = rtrim($this->addSettings['fullUrlWithoutParam'], '/');
        }
        if (!filter_var($url, FILTER_VALIDATE_URL) && !preg_match('/^\/.*/', $url)) {
            return false;
        }
        
        static $patterns = [
            '/wp-comments-post\.php/',
            '/wp-login\.php/',
            '/robots\.txt/',
            '/wp-cron\.php/',
            '/wp-content/',
            '/wp-admin/',
            '/wp-includes/',
            '/xmlrpc\.php/',
            '/wp-api/',
            '/leaflet-geojson\.php/',
            '/clientarea\.php/',
            '/wp-json/',
            '/favicon\.ico$/',
            '/style\.php$/',
            '/wp-config\.php/',
            '/wp-config\.php\.(orig|backup|bak)/',
            '/\.env(\.\w+)?$/',
            '/sendgrid\.env$/',
            '/docker-compose\.ya?ml$/',
            '/appsettings\.json$/',
            '/application\.ini$/',
            '/configuration\.php\.bak$/',
            '/settings\.py$/',
            '/config\.ini$/',
            '/app\.yaml$/',
            '/app\.log$/',
            '/phpinfo\.phtml$/',
            '/Default\.aspx$/',
            '/readme\.md$/',
            '/\.(xml|json|txt|ico|rss|webmanifest|xsl|map)$/i',
            '/\.(png|jpe?g|svg|gif|webp|avif|bmp|tiff|ico)$/i',
            '/\.(css|js)$/i',
        ];
        
        $path = $this->w3ParseUrl($url, PHP_URL_PATH);
        
        if (empty($path) || $path === '/') {
            $normalizedUrl = rtrim($url, '/');
            $normalizedSiteUrl = rtrim($this->addSettings['siteUrl'], '/');
			return $normalizedUrl === $normalizedSiteUrl;
        }
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $path)) {
                return false;
            }
        }
        
        return true;
    }

	/**
	 * Check ai optimization enabled
	 *
	 * @return bool
	 */
    function checkAiOptimizationEnabled(){
        $optaisettings = ['optimization_on', 'css', 'load_critical_css', 'webp_jpg', 'webp_png', 'lazy_load', 'lazy_load_iframe', 'lazy_load_video', 'lazy_load_audio', 'js', 'localize_google_fonts', 'resp_bg_img', 'lbc', 'gzip', 'remquery', 'load_critical_css_style_tag'];
        foreach ($optaisettings as $value) {
            if(empty($this->settings[$value])){
               return false;
            }
        }
        return true;
    }

	/**
	 * Get url quote type
	 *
	 * @param string $cssValue Css value.
	 * @return string
	 */
    function getUrlQuoteType($cssValue) {
        $cssValue = trim($cssValue);
        if (stripos($cssValue, 'url(') !== 0) {
            return '';
        }
        $inside = substr($cssValue, 4, -1);
        $inside = trim($inside);

        $entities = ['&quot;', '&#39;', '&apos;'];
        foreach ($entities as $entity) {
            $len = strlen($entity);
            if (substr($inside, 0, $len) === $entity && substr($inside, -$len) === $entity) {
                return $entity;
            }
        }

        if (strlen($inside) > 1 && $inside[0] === "'" && substr($inside, -1) === "'") {
            return "'";
        } elseif (strlen($inside) > 1 && $inside[0] === '"' && substr($inside, -1) === '"') {
            return '"';
        }
        return '';
    }

	/**
	 * W3 insert data bg css in critical
	 *
	 * @param string $css Css.
	 * @return bool
	 */
    function w3InsertDataBgCssInCritical($css){
        $criticalFile = $this->w3PreloadCssPath() . '/' . $this->addSettings['critical_css'];
        if(is_file($criticalFile) && !empty($css)){
            $critical_css = $this->w3speedsterGetContents($criticalFile);
            $critical_css .= '/* <w3DataBgLoad>'.$css.'</w3DataBgLoad> */';
            $this->w3CreateFile($criticalFile, $critical_css);
            return true;
        }
        return false;
    }

	/**
	 * W3 get data bg css from critical
	 *
	 * @return string
	 */
    function w3GetDataBgCssFromCritical(){
        $criticalFile = $this->w3PreloadCssPath() . '/' . $this->addSettings['critical_css'];
        if(is_file($criticalFile)){
            $criticalCss = $this->w3speedsterGetContents($criticalFile);
            $data = $this->w3GetTagsData($criticalCss, '<w3DataBgLoad>', '</w3DataBgLoad>');
            if(!empty($data[0])){
                $data[0] = str_replace('<w3DataBgLoad>', '', str_replace('</w3DataBgLoad>', '', $data[0]));
                if(!empty($data[0])){
                    return $data[0];
                }
            }
        }
        return '';
    }

	/**
	 * Modify CSS URL array for optimization
	 *
	 * Processes URL arrays to clean up paths, remove version numbers,
	 * and handle special directory structures.
	 *
	 * @param array $url_array Parsed URL array to modify
	 * @return array Modified URL array
	 */
	public function modifyCssUrlArray( $url_array ) {
		if(strpos($url_array['path'],'./') !== false || strpos($url_array['path'],'../') !== false){
			$url_array['path'] = $this->removeDotPathSegments($url_array['path']);
		}
		if(strpos($url_array['path'],'/version') !== false){
			$url_array['path'] = preg_replace('/version(\d)*\//', '', $url_array['path']);
		}
		if(strpos($this->addSettings['documentRoot'],'/pub') !== false && strpos($url_array['path'],'/pub') !== false){
			$url_array['path'] = str_replace('/pub/', '/', $url_array['path']);
		}
		return $url_array;
	}
	
	/**
	 * Parse CSS URL for optimization
	 *
	 * Parses a CSS href URL and returns a structured array
	 * with path information for optimization.
	 *
	 * @param string $css_href The CSS href URL to parse
	 * @return array Parsed URL array with path information
	 */
	public function w3ParseCssUrl( $css_href ) {
		$url_array = $this->w3CustomParseUrl(str_replace($this->addSettings['siteUrl'],'',$css_href));
		$url_array['path'] = !empty($url_array['path']) ? '/'.ltrim($url_array['path'],'/') : '';
		return $url_array;
	}

	/**
	 * Save assets on server
	 *
	 * @param array $assetUrls Asset urls.
	 * @param string $auth Auth.
	 */
    function saveAssetsOnServer($assetUrls, $auth = ''){
        if(is_array($assetUrls) && count($assetUrls)){
            $cssData = [];
            foreach ($assetUrls as $key => $url) {
                $newUrl = str_replace($this->addSettings['css_cdn_url'],$this->addSettings['siteUrl'],$url);
                $url_array = $this->w3ParseCssUrl($newUrl);
				$url_array = $this->modifyCssUrlArray($url_array);
                $cssPath = $this->addSettings['documentRoot'].$url_array['path'];
                if(is_file($cssPath) && filesize($cssPath) > 0){
                    $content = @file_get_contents($cssPath);
                    if(!empty($content)){
                        $data = [];
                        $data['url'] = $url;
                        $ext = pathinfo($cssPath, PATHINFO_EXTENSION);
                        $data['content'] = ($ext === 'css') ? $content : base64_encode($content);
                        $cssData[] = $data;
                        unset($data);
                    }
                }else{
                    $data = [];
                    $data['url'] = $url;
                    $data['content'] = '/* no-css-in-file */';
                    $cssData[] = $data;
                    unset($data);
                }
            }
            
            if(is_array($cssData) && count($cssData)){
                $requestUrl = $this->addSettings['w3ApiUrl'] . '/optimize/css/fetch-css.php';
                $this->w3RemoteRequestNonBlocking($requestUrl, ['auth' => $auth, 'domain' => $this->addSettings['siteUrl'], 'key' => $this->getLicenseKey(),  'urls' => $cssData], 'POST', false, true);
            }
        }
    }

	/**
	 * W3 css files count
	 *
	 * @param string $url Url.
	 * @return array
	 */
    function w3CssFilesCount($url) {
		$criticalPath = $this->w3PreloadCssPath($url);
		$files = glob(rtrim($criticalPath, '/').'/*.css');
		$mobCount = 0;
		$nonMobCount = 0;
		foreach ($files as $file) {
			if (substr($file, -4) !== '.css') {
				continue;
			}
			if (substr($file, -7) === 'mob.css') {
				$mobCount++;
			} else {
				$nonMobCount++;
			}
		}

		return [
			'mobile'     => $mobCount,
			'desktop' => $nonMobCount
		];
	}

	/**
	 * Is json
	 *
	 * @return bool
	 */
    public function is_json(){
		return $this->current_page_content_type == "json" ? true : false;
	}

	/**
	 * Is xml
	 *
	 * @return bool
	 */
	public function is_xml(){
		return $this->current_page_content_type == "xml" ? true : false;
	}

	/**
	 * Is feed
	 *
	 * @return bool
	 */
	public function is_feed() {
		return strpos($this->html, '<atom:link') !== false;
	}

	/**
	 * W3 change url scheme
	 *
	 * @param string $url Url.
	 * @return string
	 */
    function w3ChangeUrlScheme($url){
		$parsedUrl = $this->w3ParseUrl($url);
		if(!(empty($parsedUrl['host']) || empty($parsedUrl['scheme']) || $parsedUrl['host'] != $this->addSettings['siteUrlArr']['host'])){
			$url = str_replace($parsedUrl['scheme'], $this->addSettings['siteUrlArr']['scheme'], $url);
		}
		return $url;
	} 

	/**
	 * W3 optimize with ai
	 */
    public function w3OptimizeWithAi() {
        $ids = $this->addSettings['w3_post']['ids'] ?? [];
		$rows = $this->addSettings['w3_post']['rows'] ?? 10;
		$page = $this->addSettings['w3_post']['page'] ?? 1;
		$count = $this->addSettings['w3_post']['count'] ?? 0;
		$url = $this->addSettings['w3_post']['url'] ?? '';
		$status = $this->addSettings['w3_post']['status'] ?? 'all';
		switch ($status) {
			case 'all':
				$statusArray = [0, 1, 2, 3, 4, 5, 6];
				break;
			case 'pending':
				$statusArray = [0];
				break;
			case 'in-progress':
				$statusArray = [1, 2, 3];
				break;
			case 'error':
				$statusArray = [5];
				break;
			case 'done':
				$statusArray = [4, 6];
				break;
			default:
				$statusArray = [0, 1, 2, 3, 4, 5, 6];
				break;
		}
		$reqCount = $this->addSettings['w3_post']['reqCount'] ?? 0;
		$response = [];
		$response['status'] = true;
		$response['message'] = 'Optimization is in progress';
		if($count){
			if(empty($ids) || !is_array($ids) || count($ids) == 0){
				$ids = $this->w3GetPendingIds($count);
                if(!empty($ids)){
                    $this->w3RunBackgroundCron('crawl', $ids, $reqCount);
                }
			} else {
				$ids = $this->w3FilterPendingOptimizationIds($ids);
                if(!empty($ids)){
                    $this->w3RunBackgroundCron('optimize', $ids, $reqCount);
                }
			}
		}
		$response['ids'] = $ids;
		$response['table_data'] = $this->w3GetOptimizeAiTableData(['rows' => $rows, 'page' => $page, 'status' => $statusArray, 'url' => $url]);
		$this->w3Response($response);
	}

	/**
	 * W3 change url absolute to relative
	 *
	 * @param string $url Url.
	 * @param array $parsedUrl Parsed url.
	 * @return string
	 */
    function w3ChangeUrlAbsoluteToRelative($url, $parsedUrl = []){
		if(empty($parsedUrl)){
			$parsedUrl = $this->w3ParseUrl($url);
		}
		if(empty($parsedUrl['host']) || empty($parsedUrl['scheme'])) return false;
		$url = trim(str_replace($parsedUrl['host'], '', str_replace($parsedUrl['scheme'].'://', '', $url)));
		$url = empty($url) ? '/' : $url;
		return $url;
	}

	/**
	 * W3 change url relative to absolute
	 *
	 * @param string $url Url.
	 * @return string
	 */
    function w3ChangeUrlRelativeToAbsolute($url){
		return $this->addSettings['siteUrlArr']['scheme'] . '://' . $this->addSettings['siteUrlArr']['host'] . $url; 
	}

	/**
	 * Get sitemap urls
	 *
	 * @param string $siteUrl Site url.
	 * @return array
	 */
    function getSitemapUrls($siteUrl) {
        $robotsTxt = @file_get_contents($siteUrl . '/robots.txt');
        if (!$robotsTxt) {
            return [];
        }
        preg_match_all('/Sitemap:\s*(.+)/i', $robotsTxt, $matches);
        $sitemaps = $matches[1] ?? [];
        if (empty($sitemaps)) {
            $sitemaps[] = $siteUrl . '/sitemap.xml';
        }
        $allUrls = [];
        foreach ($sitemaps as $sitemapUrl) {
            $allUrls = array_merge($allUrls, $this->parseSitemap($sitemapUrl));
        }
        return $allUrls;
    }

	/**
	 * Parse sitemap
	 *
	 * @param string $sitemapUrl Sitemap url.
	 * @return array
	 */
    function parseSitemap($sitemapUrl) {
        $content = @file_get_contents($sitemapUrl);
        if (!$content) {
            return [];
        }
        $urls = [];
        $xml = @simplexml_load_string($content);
        if (!$xml) return [];
        if (isset($xml->sitemap)) {
            foreach ($xml->sitemap as $s) {
                $loc = (string)$s->loc;
                $urls = array_merge($urls, $this->parseSitemap($loc));
            }
        }
        if (isset($xml->url)) {
            foreach ($xml->url as $u) {
                $urls[] = (string)$u->loc;
            }
        }
        return $urls;
    }

	/**
	 * W3 enque responsive image
	 *
	 * @param string $url Url.
	 * @return array
	 */
    function w3EnqueResponsiveImage($url){
		list($img_root_path, $img_root_url) = $this->getImgRootPath();
		$imgsrc_filepath = $this->getResourceRootPath($url, $img_root_url, $img_root_path);
		$webp_path = $this->getImgWebpPath($img_root_path, $imgsrc_filepath);
		
		// Check if file extension is already .webp
		$extension = strtolower(pathinfo($imgsrc_filepath, PATHINFO_EXTENSION));
		if($extension === 'webp'){
			// If already .webp, just add '-595xh.webp'
			$imgsrc_webpfilepath = $webp_path.'-595xh.webp';
		} else {
			// If not .webp, replace .webp with -595xh.webp (for cases like file.jpg.webp)
			$imgsrc_webpfilepath = rtrim(str_replace('.webp$','-595xh.webp$', $webp_path.'$'), '$');
		}
		
		if(!file_exists($imgsrc_webpfilepath)){
            $this->addSettings['w3ResponsiveImgUrls'][] = $url;
        }
        return [$url, $imgsrc_webpfilepath];
	}

	/**
	 * W3 create folder
	 *
	 * @param string $path Path.
	 * @return string
	 */
	function w3CreateFolder($path)
    {
        $realpath = urldecode($path);
        if (is_dir($realpath)) {
            return $path;
        }
        try {
            $this->w3Mkdir($realpath, 0755, true);
        } catch (\Exception $e) {
            return false;
        }
        return $path;
    }

	/**
	 * Is mobile
	 *
	 * @return int
	 */
	function is_mobile()
    {
        $useragent = $this->addSettings['w3_server']['HTTP_USER_AGENT'] ?? '';
        if (!empty($useragent)) {
            if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
                return 1;
            }
        }
        return 0;
    }

	/**
	 * W3speedster create html cache file
	 *
	 * @param string $html Html.
	 * @return string
	 */
    function w3SpeedsterCreateHTMLCacheFile()
    {
        if ($msg = $this->w3NoHtmlCache($this->html)) {
            $this->html = preg_replace('/<\!--W3_PAGE_TYPE_[a-z]+-->/i', '', $this->html);
            return $this->html . (strlen($msg) > 1 ? $msg : '');
        } else {
            $this->html = preg_replace('/<\!--W3_PAGE_TYPE_[a-z]+-->/i', '', $this->html);
        }
        $this->createHtmlfile($this->html);
    }

	/**
	 * Get mobile browsers
	 *
	 * @return array
	 */
    public function get_mobile_browsers()
    {
        $mobile_browsers  = array(
            '\bCrMo\b|CriOS|Android.*Chrome\/[.0-9]*\s(Mobile)?',
            '\bDolfin\b',
            'Opera.*Mini|Opera.*Mobi|Android.*Opera|Mobile.*OPR\/[0-9.]+|Coast\/[0-9.]+',
            'Skyfire',
            'Mobile\sSafari\/[.0-9]*\sEdge',
            'IEMobile|MSIEMobile', // |Trident/[.0-9]+
            'fennec|firefox.*maemo|(Mobile|Tablet).*Firefox|Firefox.*Mobile|FxiOS',
            'bolt',
            'teashark',
            'Blazer',
            'Version.*Mobile.*Safari|Safari.*Mobile|MobileSafari',
            'Tizen',
            'UC.*Browser|UCWEB',
            'baiduboxapp',
            'baidubrowser',
            'DiigoBrowser',
            'Puffin',
            '\bMercury\b',
            'Obigo',
            'NF-Browser',
            'NokiaBrowser|OviBrowser|OneBrowser|TwonkyBeamBrowser|SEMC.*Browser|FlyFlow|Minimo|NetFront|Novarra-Vision|MQQBrowser|MicroMessenger',
            'Android.*PaleMoon|Mobile.*PaleMoon'
        );
        return $mobile_browsers;
    }

	/**
	 * W3speedster is mobile device
	 *
	 * @param string $user_agent User agent.
	 * @return bool
	 */
    function w3speedsterIsMobileDevice($user_agent)
    {
        // Regular expression to identify common mobile user agents
        $pattern = "/(\bCrMo\b|CriOS|Android.*Chrome\/[.0-9]*\s(Mobile)?|\bDolfin\b|Opera.*Mini|Opera.*Mobi|Android.*Opera|Mobile.*OPR\/[0-9.]+|Coast\/[0-9.]+|Skyfire|Mobile\sSafari\/[.0-9]*\sEdge|IEMobile|MSIEMobile|fennec|firefox.*maemo|(Mobile|Tablet).*Firefox|Firefox.*Mobile|FxiOS|bolt|teashark|Blazer|Version.*Mobile.*Safari|Safari.*Mobile|MobileSafari|Tizen|UC.*Browser|UCWEB|baiduboxapp|baidubrowser|DiigoBrowser|Puffin|\bMercury\b|Obigo|NF-Browser|NokiaBrowser|OviBrowser|OneBrowser|TwonkyBeamBrowser|SEMC.*Browser|FlyFlow|Minimo|NetFront|Novarra-Vision|MQQBrowser|MicroMessenger|Android.*PaleMoon|Mobile.*PaleMoon|Android|blackberry|\bBB10\b|rim\stablet\sos|PalmOS|avantgo|blazer|elaine|hiptop|palm|plucker|xiino|Symbian|SymbOS|Series60|Series40|SYB-[0-9]+|\bS60\b|Windows\sCE.*(PPC|Smartphone|Mobile|[0-9]{3}x[0-9]{3})|Window\sMobile|Windows\sPhone\s[0-9.]+|WCE;|Windows\sPhone\s10.0|Windows\sPhone\s8.1|Windows\sPhone\s8.0|Windows\sPhone\sOS|XBLWP7|ZuneWP7|Windows\sNT\s6\.[23]\;\sARM\;|\biPhone.*Mobile|\biPod|\biPad|Apple-iPhone7C2|MeeGo|Maemo|J2ME\/|\bMIDP\b|\bCLDC\b|webOS|hpwOS|\bBada\b|BREW)/i";
        return preg_match($pattern, $user_agent);
    }

	/**
	 * W3 check enable cdn path
	 *
	 * @param string $url Url.
	 * @param string $type Type.
	 * @return int
	 */
    public function w3CheckEnableCdnPath($url, $type = '')
    {
        $enable_cdn = 1;
        if (!empty($this->addSettings["{$type}_exclude_cdn_path"])) {
            foreach ($this->addSettings["{$type}_exclude_cdn_path"] as $path) {
                if (strpos($url, $path) !== false) {
                    $enable_cdn = 0;
                    break;
                }
            }
        }
        return $enable_cdn;
    }

	/**
	 * W3 delete server cache
	 *
	 * @return bool
	 */
    function w3DeleteServerCache()
    {
        $options = array(
            'url' => $this->addSettings['homeUrl'],
            'key' => $this->getLicenseKey($this->addSettings['homeUrl'])
        );

        $response = $this->w3RemoteGet($this->addSettings['w3ApiUrl'] . '/optimize/css/delete-css.php', $options);
        if (!empty($response)) {
            return true;
        } else {
            return false;
        }
    }

	/**
	 * W3speedster validate license key
	 *
	 * @param string $key Key.
	 * @return string
	 */
    function w3speedsterValidateLicenseKey($key = '')
    {
        $key = !empty($this->addSettings['w3_get']['key']) ? $this->addSettings['w3_get']['key'] : $key;
        if (!empty($key)) {
            $options = array(
                'license_id' => $key,
                'domain' => base64_encode($this->addSettings['homeUrl'])
            );

            $response = $this->w3RemoteGet($this->addSettings['w3ApiUrl'] . '/optimize/get_license_detail.php', $options);
            if (!empty($response)) {
                $res_arr = json_decode($response);
                if ($res_arr[0] == 'success') {
                    return $this->w3JsonEncode(array('success', 'verified', $res_arr[1]));
                } else {
                    return $this->w3JsonEncode(array('fail', 'could not verify-1' . $response));
                }
            } else {
                return $this->w3JsonEncode(array('fail', 'could not verify-3'));
            }
        }
    }

	/**
	 * Is special content type
	 *
	 * @return bool
	 */
    public function isSpecialContentType()
    {
        if ($this->w3Endswith($this->addSettings['full_url'], '.xml') || $this->w3Endswith($this->addSettings['full_url'], '.xsl')) {
            return true;
        }

        return false;
    }

	/**
	 * Get w3 contents insert link
	 *
	 * @param array $all_links All links.
	 * @return string
	 */
    function getW3contentsInsertLink($all_links)
    {
        $insertLink = '';
        if (strpos($this->html, '<script>var w3GoogleFont') !== false) {
            $insertLink = '<script>var w3GoogleFont';
        } elseif (!empty($all_links['link'])) {
            foreach ($all_links['link'] as $link) {
                if (strpos($link, 'stylesheet') !== false) {
                    $insertLink = $link;
                    break;
                }
            }
        } else {
            $insertLink = !empty($all_links['script'][0]) ? $all_links['script'][0] : '';
        }
        return $insertLink;
    }

	/**
	 * W3 debug time
	 *
	 * @param string $process Process.
	 */
    function w3DebugTime($process)
    {
        if (!empty($this->addSettings['w3_get']['w3_debug'])) {
            $starttime = !empty($this->addSettings['starttime']) ? $this->addSettings['starttime'] : $this->microtime_float();
            $endtime = $this->microtime_float();
            $this->html .= $process . '-' . ($endtime - $starttime) . '-ram-' . (memory_get_usage() / 1024 / 1024) . '-cpu-' . $this->w3JsonEncode(sys_getloadavg()) . "\n";
        }
    }

	/**
	 * Microtime float
	 *
	 * @return float
	 */
    function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }

	/**
	 * W3 str replace last
	 *
	 * @param string $search Search.
	 * @param string $replace Replace.
	 * @param string $str Str.
	 * @return string
	 */
    function w3StrReplaceLast($search, $replace, $str)
    {
        if (($pos = strrpos($str, $search)) !== false) {
            $search_length = strlen($search);
            $str = substr_replace($str, $replace, $pos, $search_length);
        }
        return $str;
    }

	/**
	 * W3 custom parse url
	 *
	 * @param string $src Src.
	 * @return array
	 */
    function w3CustomParseUrl($src)
    {
        if (!empty($this->addSettings['siteUrlArr']['path'])) {
            if (strpos($src, $this->addSettings['siteUrlArr']['host']) !== false) {
                $src = str_replace($this->addSettings['siteUrlArr']['host'] . $this->addSettings['siteUrlArr']['path'], $this->addSettings['siteUrlArr']['host'], $src);
            } else {
                $src = $this->strReplaceFirst($this->addSettings['siteUrlArr']['path'], '', $src);
            }
        }
        if (substr_count($src, '//') > 0) {
            $src = substr($src, 0, 7) . str_replace('//', '/', substr($src, 7));
        }
        $src_arr = $this->w3ParseUrl($src);
        return $src_arr;
    }

	/**
	 * Str replace first
	 *
	 * @param string $search Search.
	 * @param string $replace Replace.
	 * @param string $subject Subject.
	 * @return string
	 */
    function strReplaceFirst($search, $replace, $subject)
    {
        $pos = strpos($subject, $search); // Find the position of the first occurrence
        if ($pos !== false) {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }
        return $subject;
    }

	/**
	 * W3 is external
	 *
	 * @param string $url Url.
	 * @param array $components Components.
	 * @param string $type Type.
	 * @return bool
	 */
    function w3IsExternal($url, $components = [], $type = 'image')
    {
        if (empty($components)) {
            $components = $this->w3ParseUrl($url);
        }
        $siteHost = $this->addSettings['siteUrlArr']['host'] ?? '';
        $cdnHost = $this->addSettings[$type . 'UrlArr']['host'] ?? $siteHost;
        $siteHostDiff = $this->addSettings['siteUrlDiff'] ?? '';
        $urlHost = $components['host'] ?? '';
        if (empty($urlHost) || stripos($cdnHost, $urlHost) !== false || stripos($siteHost, $urlHost) !== false || stripos($siteHostDiff, $urlHost) !== false) {
            return false;
        }
        return true;
    }

	/**
	 * W3 endswith
	 *
	 * @param string $string String.
	 * @param string $test Test.
	 * @return bool
	 */
    function w3Endswith($string, $test)
    {
        $str_arr = explode('?', $string);
        $string = $str_arr[0];
        $ext = '.' . pathinfo($str_arr[0], PATHINFO_EXTENSION);
        if ($ext == $test)
            return true;
        else
            return false;
    }

	/**
	 * W3 get html cache file path
	 *
	 * @param string $url Url.
	 * @param int $mobile Mobile.
	 * @return string
	 */
    function w3GetHtmlCacheFilePath($url, $mobile = 0)
    {
        if ($path = $this->w3GetHtmlCachePath($url, $mobile)) {
            return $path . ($this->w3Endswith($url,'.xml') ? "/index.xml" : "/index.html");
        }
        return false;
    }

	/**
	 * W3 get html cache path
	 *
	 * @param string $url Url.
	 * @param int $mobile Mobile.
	 * @return string
	 */
    function w3GetHtmlCachePath($url, $mobile = 0)
    {
        $type = "/html";
        $enableCachingGetPara = isset($this->settings['enable_caching_get_para']) ? 1 : 0;
        $parsed_url = $this->w3ParseUrl($url);
        $cachePath = !empty($parsed_url['path']) ? trim($parsed_url['path'], '/') : '';
        $device = '';
        if ($mobile) {
            $device = '/w3mob';
        }
        if (!empty($enableCachingGetPara) && !empty($parsed_url['query'])) {
            $cachePath .= '/' . $parsed_url['query'];
        }
        if (!empty($this->settings['enable_loggedin_user_caching']) && !empty($this->getUserHash())) {
            $type .= '/' . $this->getUserHash();
        }
        $type .= "/" . ($parsed_url['scheme'] === "https" ? "on" : "off") . "-" . $parsed_url['host'];
        $cachePath = $this->getWebCachePath($type, $cachePath);
        $cachePath .= $device;
        if ($cachePath) {
            $cachePath = urldecode($cachePath);
        }
        // for security
        if (preg_match("/\.{2,}/", $cachePath)) {
            $cachePath = false;
        }
        return $cachePath;
    }

	/**
	 * W3 css compress init
	 *
	 * @param string $minify Minify.
	 * @return string
	 */
    function w3CssCompressInit($minify)
    {
        $minify = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $minify);
        $minify = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), ' ', $minify);
        return $minify;
    }

	/**
	 * W3 create file
	 *
	 * @param string $path Path.
	 * @param string $text Text.
	 * @return bool
	 */
	function w3CreateFile($path, $text = '//',$append = false)
    {
        $path_arr = explode('/', $path);
        $filename = array_pop($path_arr);
        $realpath = urldecode(implode('/', $path_arr));
        if (is_link($realpath) || strpos($realpath, '/./') !== false || strpos($realpath, '/../') !== false) {
            $realpath = realpath($realpath);
        }
        $this->w3CreateFolder($realpath);
        $realFullPath = $realpath . '/' . $filename;
        return $this->w3speedsterPutContents($realFullPath, $text,$append);
    }
    /**
     * Find script tags occurrence without quotes
     *
     * @param string $text Text.
     * @return int
     */
	function findOccurrenceWithoutQuotes($text){
        // Pattern to match script tags while skipping quoted content
        $pattern = '/(?:
            "(?:\\\\.|[^"\\\\])*"         # double-quoted string (supports escapes)
          | \'(?:\\\\.|[^\'\\\\])*\'      # single-quoted string (supports escapes)
        )(*SKIP)(*FAIL)                   # skip any quoted region entirely
        | <script\b[^>]*>.*?<\/script>    # match script tags
        /isx';                            // i = case-insensitive, s = dot-newline, x = ignore whitespace
    
        preg_match_all($pattern, $text, $matches);
        return count($matches[0]);
    }
	/**
	 * W3 get tags data
	 *
	 * @param string $data Data.
	 * @param string $start_tag Start tag.
	 * @param string $end_tag End tag.
	 * @param string $duplicate Duplicate.
	 * @return array
	 */
	function w3GetTagsData($data, $start_tag, $end_tag,$duplicate='',$extract = 'o'){
        $data_exists = 0;
        $i = 0;
        $end_tag_char_len = strlen($end_tag);
        $start_tag_char_len = strlen($start_tag);
        $script_array = array();
        $duplicate = !empty($duplicate) ? $duplicate : $start_tag;
        $script_array = array();
        while($data_exists != -1 && $i<20000) {
            if($start_tag == '<script'){
                $data1 = strpos($data,$start_tag.' ',$data_exists);
                $data2 = strpos($data,$start_tag.'>',$data_exists);
                if($data1 === false && $data2 !== false){
                    $data_exists = $data2;
                    $duplicate = $start_tag.'>';
                }elseif($data2 === false && $data1 !== false){
                    $data_exists = $data1;
                    $duplicate = $start_tag.' ';
                }elseif( $data1 > $data2){
                    $data_exists = $data2;
                    $duplicate = $start_tag.'>';
                }else{
                    $data_exists = $data1;
                    $duplicate = $start_tag.' ';
                }
            }else{
                $data_exists = strpos($data,$start_tag,$data_exists);
            }
            if($data_exists !== false){
                $end_tag_pointer = strpos($data,$end_tag,$data_exists);
                $extractedText = substr($data, $data_exists, $end_tag_pointer-$data_exists+$end_tag_char_len);
                if(($count = $this->findOccurrenceWithoutQuotes(substr($extractedText,$start_tag_char_len)) > 0)){
                    $end_tag_pointer = $this->findNthOccurrence($data, $end_tag, $count,$data_exists);
                }
                if(empty($end_tag_pointer)){
                    $end_tag_pointer = strlen($data)-strlen($end_tag);
                }
                if($extract == 'o'){
                    $script_array[] = substr($data, $data_exists, $end_tag_pointer - $data_exists + $end_tag_char_len);
                }else{
                    $script_array[] = substr($data, $data_exists + $start_tag_char_len, $end_tag_pointer - $data_exists - $start_tag_char_len);
                }
                $data_exists = $end_tag_pointer;
            }else{
                $data_exists = -1;
            }
            $i++;
        }
        return $script_array;
    }

	/**
	 * Find nth occurrence
	 *
	 * @param string $haystack Haystack.
	 * @param string $needle Needle.
	 * @param int $nth Nth.
	 * @param int $data_exists Data exists.
	 * @return int
	 */
    function findNthOccurrence($haystack, $needle, $nth,$data_exists) {
        $pos = $data_exists;
        for ($i = 0; $i < $nth; $i++) {
            $pos = strpos($haystack, $needle, $pos);
            if ($pos === false) {
                return false; // Not found
            }
            $pos++; // Move past the last found position
        }
        return $pos - 1 ?? $data_exists; // Adjust position (since we increment after finding)
    }

	/**
	 * Clean script tags
	 *
	 * @param string $html Html.
	 * @return string
	 */
	function cleanScriptTags($html){
        $cleaned = preg_replace_callback('/(<script[\s\S]*?>)/i', function($matches){
            return preg_replace('/\s+/', ' ', $matches[1]);
        }, $html);
        return $cleaned;
    }

	/**
	 * W3 parse link
	 *
	 * @param string $tag Tag.
	 * @param string $link Link.
	 * @return array
	 */
	function w3ParseLink($tag, $link)
    {
        $link_arr = array();
        
        // Extract URL-related attributes directly from HTML to preserve URL encoding
        // These attributes often contain URL-encoded characters that get corrupted by DOMDocument + iconv
        $urlAttributes = ['src', 'srcset', 'href', 'data-src', 'data-srcset', 'data-href'];

        foreach ($urlAttributes as $attr) {
            $pattern = '/(' . preg_quote($attr, '/') . '\s*=\s*)([“”‘’"])(.*?)([“”‘’"])/iu';
            $link = preg_replace_callback($pattern, function ($m) {
                return $m[1] . '"' . $m[3] . '"';
            }, $link);
        }

        // For script tags, limit parsing to the opening <script ...> tag only
        $linkForUrlAttrs = $link;
        if (preg_match('/<script\b([^>]*)>/i', $link, $m)) {
            $linkForUrlAttrs = '<script ' . $m[1] . '>';
        }

        foreach ($urlAttributes as $attrName) {
            // Extract attribute value directly from (opening) tag HTML to preserve encoding
            if (preg_match('/(?:[\s>"]|^)' . preg_quote($attrName, '/') . '=["\']([^"\']+)["\']/i', $linkForUrlAttrs, $matches)) {
                $link_arr[$attrName] = $matches[1];
            }
        }
        
        // Use DOMDocument for other attributes
        $xmlDoc = new \DOMDocument();
        if (!empty($link) && @$xmlDoc->loadHTML($link) !== false) {
            $tag_html = $xmlDoc->getElementsByTagName($tag);
            if (!empty($tag_html[0])) {
                foreach ($tag_html[0]->attributes as $attr) {
                    // Skip URL attributes that we already extracted from HTML
                    if (!in_array(strtolower($attr->nodeName), $urlAttributes)) {
                        $attrValue = $attr->nodeValue;
                        // Only apply iconv if the value is not already valid UTF-8
                        if (!mb_check_encoding($attrValue, 'UTF-8')) {
                            $attrValue = iconv('ISO-8859-1', 'UTF-8', $attrValue);
                        }
                        $link_arr[$attr->nodeName] = $attrValue;
                    }
                }
            }
        }
        
        if($tag == 'script'){
            $link_arr['type'] = empty($link_arr['type']) ? '' : $link_arr['type'];
        }
        if (strpos($link, '><') === false) {
            $link_arr['html'] = $this->w3ParseScript($tag, $link);
        }
        return $link_arr;
    }

	/**
	 * W3 parse script
	 *
	 * @param string $tag Tag.
	 * @param string $link Link.
	 * @return array
	 */
	function w3ParseScript($tag, $link)
    {
        $data_exists = strpos($link, '>');
        if (!empty($data_exists)) {
            $end_tag_pointer = strpos($link, '</' . $tag . '>', $data_exists);
            $link_arr = substr($link, $data_exists + 1, $end_tag_pointer - $data_exists - 1);
        }
        return $link_arr;
    }

	/**
	 * W3 str replace set js
	 *
	 * @param string $str String.
	 * @param string $rep Rep.
	 */
	function w3StrReplaceSetJs($str, $rep)
    {
        global $str_replace_str_js, $str_replace_rep_js;
        $str_replace_str_js[] = $str;
        $str_replace_rep_js[] = $rep;
    }

	/**
	 * W3 implode link array
	 *
	 * @param string $tag Tag.
	 * @param array $array Array.
	 * @return string
     * @since 1.0.0
     */
	function w3ImplodeLinkArray($tag, $array)
    {
        if (empty($array)) {
            return '';
        }
        $link = '<' . $tag . ' ';
        $html = '';
        if (!empty($array['html'])) {
            $html = $array['html'];
            unset($array['html']);
        }
        foreach ($array as $key => $arr) {
            if ($key != 'html') {
                $link .= $key . "=\"" . str_replace('"', "'", $arr) . "\" ";
            }
        }
        $link = trim($link);
        if ($tag == 'script') {
            $link .= '>' . $html . '</script>';
        } elseif ($tag == 'iframe') {
            $link .= '>' . $html . '</iframe>';
        } elseif ($tag == 'iframelazy') {
            $link .= '>' . $html . '</iframelazy>';
        } else {
            $link .= '>';
        }
        return $link;
    }

	/**
	 * W3 insert content head
	 *
	 * @param string $content Content.
	 * @param int $pos Pos.
	 * @param string $link Link.
	 */
	function w3InsertContentHead($content, $pos, $link = '')
    {
        global $insert_content_head;
        $insert_content_head[] = array($content, $pos, $link);
        if ($pos == 1) {
            $this->html = preg_replace('/<style/', $content . '<style', $this->html, 1, $count);
        } elseif ($pos == 2) {
            if (!empty($link)) {
                $this->html = str_replace($link, $content . $link, $this->html);
            } else {
                $this->html = preg_replace('/(<link.*>)/', $content . "$1", $this->html, 1);
            }
        } elseif ($pos == 3) {
            $this->html = preg_replace('/<head([^<]*)>/', '<head$1>' . $content, $this->html, 1, $count);
            if (empty($count)) {
                $this->html = preg_replace('/<html([^<]*)>/', '<html$1>' . $content, $this->html, 1, $count);
            }
        } elseif ($pos == 4) {
            $this->html = preg_replace('/<\/head(\s*)>/', $content . '</head$1>', $this->html, 1, $count);
            if (empty($count)) {
                $this->html = preg_replace('/<body([^<]*)>/', $content . '<body$1>', $this->html, 1, $count);
            }
        } elseif ($pos == 5) {
            $this->html = preg_replace($content, '', $this->html, 1, $count);
        } elseif ($pos == 6) {
            $this->html = $this->rightReplace($this->html, '<link ', $content . '<link ');
        } else {
            $this->html = preg_replace('/<script/', $content . '<script', $this->html, 1, $count);
        }
    }

	/**
	 * Right replace
	 *
	 * @param string $string String.
	 * @param string $search Search.
	 * @param string $replace Replace.
	 */
    function rightReplace($string, $search, $replace)
    {
        $offset = strrpos($string, $search);
        if ($offset !== false) {
            $length = strlen($search);
            $string = substr_replace($string, $replace, $offset, $length);
        }
        return $string;
    }

	/**
	 * W3 str replace set img
	 *
	 * @param string $str String.
	 * @param string $rep Rep.
	 */
    function w3StrReplaceSetImg($str, $rep)
    {
        global $str_replace_str_img, $str_replace_rep_img;
        $str_replace_str_img[] = $str;
        $str_replace_rep_img[] = $rep;
    }

	/**
	 * W3 str replace set css
	 *
	 * @param string $str String.
	 * @param string $rep Rep.
	 * @param string $key Key.
	 */
    function w3StrReplaceSetCss($str, $rep, $key = '')
    {
        global $str_replace_str_css, $str_replace_rep_css;
        if ($key) {
            $str_replace_str_css[$key] = $str;
            $str_replace_rep_css[$key] = $rep;
        } else {
            $str_replace_str_css[] = $str;
            $str_replace_rep_css[] = $rep;
        }
    }

	/**
	 * W3 str replace bulk
	 */
    function w3StrReplaceBulk()
    {
        global $str_replace_str_array, $str_replace_rep_array;
        global $str_replace_str_css, $str_replace_rep_css;
        global $str_replace_str_js, $str_replace_rep_js;
        global $str_replace_str_img, $str_replace_rep_img;
        if (!is_array($str_replace_str_array) && !is_array($str_replace_rep_array)) {
            $str_replace_str_array = array();
            $str_replace_rep_array = array();
        }
        if (!is_array($str_replace_str_css) && !is_array($str_replace_rep_css)) {
            $str_replace_str_css = array();
            $str_replace_rep_css = array();
        }
        if (!is_array($str_replace_str_js) && !is_array($str_replace_rep_js)) {
            $str_replace_str_js = array();
            $str_replace_rep_js = array();
        }
        if (!is_array($str_replace_str_img) && !is_array($str_replace_rep_img)) {
            $str_replace_str_img = array();
            $str_replace_rep_img = array();
        }
        $this->w3DebugTime('start json merge');
        $str_replace_str_array = array_merge($str_replace_str_img, $str_replace_str_css, $str_replace_str_js);
        $str_replace_rep_array = array_merge($str_replace_rep_img, $str_replace_rep_css, $str_replace_rep_js);
        $this->w3DebugTime('end json merge');
        $this->html = str_replace($str_replace_str_array, $str_replace_rep_array, $this->html);
    }

	/**
	 * Remove directory
	 *
	 * @param string $dir Directory.
	 */
	public function w3Rmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") {
                        $this->w3Rmdir($dir . "/" . $object);
                    } else {
                        $this->w3DeleteFile($dir . "/" . $object);
                    }
                }
            }
            reset($objects);
            return $this->w3RmSingleDir($dir);
        }
    }

	/**
	 * Remove files
	 *
	 * @param string $dir Directory.
	 */
    function w3Rmfiles($dir)
    {
        //echo $dir; exit;
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) != "dir") {
                        $this->w3DeleteFile($dir . "/" . $object);
                    }
                }
            }
            reset($objects);
        }
    }

    /**
     * Ensure that a given URL has a proper scheme (http or https).
     *
     * @param string $url The input URL (e.g., //fonts.googleapis.com, example.com, http://example.com).
     * @param string|null $defaultScheme The default scheme to use if missing (https or http).
     *                                   If null, it will detect the current request scheme.
     * @return string The URL with a proper scheme.
     */
    function w3EnsureUrlScheme($url, $defaultScheme = null) {
        // Trim spaces just in case
        $url = trim($url);

        // If scheme is not provided, detect from current request
        if ($defaultScheme === null) {
            $defaultScheme = (!empty($this->addSettings['w3_server']['HTTPS']) && $this->addSettings['w3_server']['HTTPS'] !== 'off') ? 'https' : 'http';
        }

        // Case 1: URL already has a scheme (http:// or https://), return as is
        if (preg_match('#^https?://#i', $url)) {
            return $url;
        }

        // Case 2: URL starts with // (protocol-relative URL)
        if (strpos($url, '//') === 0) {
            return $defaultScheme . ':' . $url;
        }

        // Case 3: Bare domain or path → prepend scheme and //
        return $defaultScheme . '://' . ltrim($url, '/');
    }

    function removeW3WebpImg($url){
        $responsive = $this->addSettings['is_mobile'] && $this->settings['resp_bg_img'];
        $webpJpg = !empty($this->settings['webp_jpg']) ? true : false;
        $webpPng = !empty($this->settings['webp_png']) ? true : false;

        if(!$webpJpg){
            if(strpos($url, '.jpg.webp') !== false && strpos($url, '/w3-webp/') !== false){
                $url = str_replace('/uploads/w3-webp/', '/', rtrim(str_replace('.jpg.webp$', '.jpg$', $url. '$'), '$'));
            }
            if(strpos($url, '.jpeg.webp') !== false && strpos($url, '/w3-webp/') !== false){
                $url = str_replace('/uploads/w3-webp/', '/', rtrim(str_replace('.jpeg.webp$', '.jpeg$', $url. '$'), '$'));
            }
        }
        if(!$webpPng){
            if(strpos($url, '.png.webp') !== false && strpos($url, '/w3-webp/') !== false){
                $url = str_replace('/uploads/w3-webp/', '/', rtrim(str_replace('.png.webp$', '.png$', $url. '$'), '$'));
            }
        }
        if(!$responsive){
            foreach (['jpg', 'png', 'webp', 'jpeg'] as $ext) {
                if(strpos($url, ".$ext-595xh.webp") !== false && strpos($url, '/w3-webp/') !== false){
                    $url = str_replace('/uploads/w3-webp/', '/', rtrim(str_replace(".$ext-595xh.webp$", ".$ext$", $url. '$'), '$'));
                    break;
                }
            }
        }
        return $url;
    }

    /**
     * Convert specific string settings to arrays.
     *
     * This method checks the main settings array for certain keys that are expected to be arrays,
     * but may have been saved as newline-separated strings (for example, from a textarea input).
     * If such a string is found, it is converted to an array using explode("\r\n", ...).
     * If any conversion occurs, the updated settings are saved back to the database and reloaded.
     *
     * This ensures that settings like 'exclude_lazy_load', 'exclude_css', etc., are always arrays,
     * which is important for consistent processing elsewhere in the plugin.
     *
     * @return void
     */
    function convertStringValuesToArray(){
        if(!empty($this->settings) && is_array($this->settings)){
            $keysToCheck = array('preload_resources', 'exclude_lazy_load', 'exclude_pages_from_optimization', 'exclude_css', 'force_lazyload_css', 'load_style_tag_in_head','exclude_page_from_load_combined_css','exclude_both_javascript','force_lazy_load_inner_javascript','exclude_page_from_load_combined_js','load_script_tag_in_url','exclude_url_html_cache','exclude_url_exclusions_html_cache');
            $update = false;
            foreach ($this->settings as $key => $value) {
                if (!empty($value) && is_string($value) && in_array($key, $keysToCheck)) {
                    $update = true;
                    $this->settings[$key] = explode("\r\n", $value);
                }
            }
            if($update){
                $this->w3UpdateOption( 'w3_speedup_option', $this->settings );
		        $this->settings = $this->w3GetOption('w3_speedup_option', true);
            }
        };
    }

    /**
     * Generate CSS for image aspect ratios.
     *
     * This function creates CSS rules for each image aspect ratio class found in
     * $this->addSettings['w3_img_aspect_ratio']. Each rule sets the aspect-ratio
     * property for the corresponding class, which helps maintain the correct
     * aspect ratio for images and prevents layout shifts.
     *
     * Example output:
     *   .w3-ratio-800x600 { aspect-ratio: 800/600; }
     *
     * @return string CSS string containing aspect-ratio rules for images.
     */
    function w3ImageRatioCss(){
        $css = '';
        if(!empty($this->addSettings['w3_img_aspect_ratio'])){
            foreach ($this->addSettings['w3_img_aspect_ratio'] as $key => $value) {
                $css .= ".$key{aspect-ratio:{$value[0]}/{$value[1]}} ";
            }
        }
        return $css;
    }

    /**
     * Convert HTML images to WebP format where applicable.
     * 
     * This function scans the HTML content for image tags and processes each image URL.
     * It checks if the image is eligible for conversion to WebP based on the plugin settings
     * @return void
     */

    function w3ConvertHtmlImagesToWebp()
    {
        $images = [];
        $regex = '/(?:\s(?:data-[\w-]+)\s*=\s*["\'])(https?:\/\/[^\'"]+\.(?:jpg|jpeg|png))["\']/i';
		preg_match_all($regex, $this->html, $matches);
		$images = array_unique($matches[1]);
        if (!empty($images)) {
            $webp_enable = $this->addSettings['webp_enable'];
            
            foreach ($images as $img) {
                $imgnn = $img;
                $components = $this->w3ParseUrl($img);
                if (!$this->w3IsExternal($img, $components)) {
                    $imgnn = $this->removeQueryParams($img, $components);
                    list($img_root_path, $img_root_url) = $this->getImgRootPath();
                    $w3_img_ext = '.' . pathinfo($imgnn, PATHINFO_EXTENSION);
                    
                    if(in_array($w3_img_ext, ['.jpg', '.webp', '.jpeg', '.png'])){
                        $this->w3EnqueResponsiveImage($imgnn);
                    }
                    
                    $imgsrc_filepath = $this->getResourceRootPath($imgnn, $img_root_url, $img_root_path);
                    $imgnn = trim(preg_replace('/\s+/', ' ', $imgnn));
                    
                    $img_size = $this->w3GetImageSize($imgsrc_filepath);
                    
                    if (!empty($this->addSettings['is_mobile']) && !empty($this->settings['resp_bg_img'])) {
                        if (!empty($img_size[0]) && $img_size[0] > 600) {
                            [$imgnn_arr, $imgsrc_filepath] = $this->convertToSmallerImage($img_root_path, $imgsrc_filepath, []);
                            $imgnn = !empty($imgnn_arr['src']) ? $imgnn_arr['src'] : $imgnn;
                        }
                    }
                    
                    if (count($webp_enable) > 0 && in_array($w3_img_ext, $webp_enable)) {
                        $imgsrc_webpfilepath = $this->getImgWebpPath($img_root_path, $imgsrc_filepath);
                        if (file_exists($imgsrc_webpfilepath)) {
                            $imgnn = $this->convertToWebp($imgnn);
                        } else {
                            $this->webpEnqueImageUrls[] = $imgnn;
                        }
                    }
                }

                if ($this->checkEnableCdn('image') && !$this->w3CheckExcludedPath($imgnn, $this->addSettings['image_exclude_cdn_path'])) {
                    $imgnn = str_replace($this->addSettings['siteUrl'], $this->addSettings['image_cdn_url'], $imgnn);
                }

                if(!empty($imgnn) && $imgnn != $img){
                    $this->w3StrReplaceSetImg($img, $imgnn);
                }
            }
        }
    }

    /**
     * Check if binary data is a valid WebP image.
     *
     * This function verifies whether the provided binary data represents a valid WebP image file.
     * It checks for the "RIFF" header at the start and the "WEBP" signature at the correct offset,
     * which are required for WebP format according to the specification.
     *
     * @param string $binaryData The binary data of the image file to validate.
     * @return bool Returns true if the data is a valid WebP image, false otherwise.
     */
    function w3IsValidWebP($binaryData) {
        if (strlen($binaryData) < 12) {
            return false;
        }
        $riff = substr($binaryData, 0, 4);
        $webp = substr($binaryData, 8, 4);
        if ($riff !== 'RIFF' || $webp !== 'WEBP') {
            return false;
        }
        return true;
    }


    function removeW3HtaccessCode(){
        $path = $this->addSettings['documentRoot'] . '/';
		if (!file_exists($path . ".htaccess")) {
			return;
		}

		$htaccess = $this->w3speedsterGetContents($path . ".htaccess");

		if ($this->is_writable($path . ".htaccess")) {
			if (strpos($htaccess, '# BEGIN W3LBC') !== false || strpos($htaccess, '# END W3LBC') !== false) {
				$htaccess = preg_replace("/#\s?BEGIN\s?W3LBC.*?#\s?END\s?W3LBC/s", "", $htaccess);
				$change_in_htaccess = 1;
			}
			if (strpos($htaccess, '# BEGIN W3Gzip') !== false || strpos($htaccess, '# END W3Gzip') !== false) {
				$htaccess = preg_replace("/\s*\#\s?BEGIN\s?W3Gzip.*?#\s?END\s?W3Gzip\s*/s", "", $htaccess);
				$change_in_htaccess = 1;
			}

			if (strpos($htaccess, '# BEGIN W3WEBP') !== false || strpos($htaccess, '# END W3WEBP') !== false) {
				$htaccess = preg_replace("/#\s?BEGIN\s?W3WEBP.*?#\s?END\s?W3WEBP/s", "", $htaccess);
				$change_in_htaccess = 1;
			}

            if (strpos($htaccess, '# BEGIN W3404') !== false || strpos($htaccess, '# END W3404') !== false) {
				$htaccess = preg_replace("/#\s?BEGIN\s?W3404.*?#\s?END\s?W3404/s", "", $htaccess);
				$change_in_htaccess = 1;
			}

            if (strpos($htaccess, '# BEGIN W3HTMLCACHE') !== false || strpos($htaccess, '# END W3HTMLCACHE') !== false) {
				$htaccess = preg_replace("/#\s?BEGIN\s?W3HTMLCACHE.*?#\s?END\s?W3HTMLCACHE/s", "", $htaccess);
				$change_in_htaccess = 1;
			}
            
			if ($change_in_htaccess) {
				$this->w3speedsterPutContents($path . ".htaccess", $htaccess);
			}
		}
        return;
    }

    function w3FilterValidImages($images){
        if(!is_array($images)) return [];
        $images =  array_values(array_unique($images));

        foreach ($images as &$img) {
            list($img_root_path, $img_root_url) = $this->getImgRootPath();
            $w3_img_ext = '.' . pathinfo($img, PATHINFO_EXTENSION);
            
            if(!in_array($w3_img_ext, ['.jpg', '.jpeg', '.png'])){
                unset($img);
            }
            
            $imgsrc_filepath = $this->getResourceRootPath($img, $img_root_url, $img_root_path);
            $imgsrc_webpfilepath = $this->getImgWebpPath($img_root_path, $imgsrc_filepath);
            $img_size = $this->w3GetImageSize($imgsrc_filepath);
            
            if (!is_file($imgsrc_filepath) || is_file($imgsrc_webpfilepath) || empty($img_size[0])) {
                unset($img);
            }
        }
        return $images;
    }

    function w3EnqueImageOptimizationTask($token) {
		$webpFile = $this->addSettings['webp_path'].'/'.$token;
		if(!is_file($webpFile)) return;

		$options = json_decode(file_get_contents($webpFile), true);
		$apiUrl = $this->addSettings['w3ApiUrl'] . '/imgopt/api/v2/index.php';

		list($img_root_path, $img_root_url) = $this->getImgRootPath();

		$body = [
			'token' => $options['token'],
			'host'  => $options['host'],
			'key'   => $options['key']
		];

		$max_upload_size = (int) $this->addSettings['max_upload_size'] * 1024 * 1024;
		$total_size = 0;

		foreach ($options['urls'] as $i => $url) {
			$img_filepath = $this->getResourceRootPath($url, $img_root_url, $img_root_path);
			if (is_file($img_filepath)) {
				$img_size = filesize($img_filepath);
				if ($total_size + $img_size > $max_upload_size) {
					break;
				}
				$body["urls[$i]"] = $url;
				$body["files[$i]"] = new \CURLFile(
					$img_filepath,
					mime_content_type($img_filepath),
					basename($img_filepath)
				);
				$total_size += $img_size;
			}
            unset($options['urls'][$i]);
		}

		foreach ($options['resize_urls'] as $i => $url) {
			$img_filepath = $this->getResourceRootPath($url, $img_root_url, $img_root_path);
			if (is_file($img_filepath)) {
				$img_size = filesize($img_filepath);
				if ($total_size + $img_size > $max_upload_size) {
					break;
				}
				$body["resize_urls[$i]"] = $url;
				$body["resize_files[$i]"] = new \CURLFile(
					$img_filepath,
					mime_content_type($img_filepath),
					basename($img_filepath)
				);
				$total_size += $img_size;
			}
            unset($options['resize_urls'][$i]);
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $apiUrl);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		$response = curl_exec($ch);
		$error    = curl_error($ch);
		curl_close($ch);
        if(!empty($response) && empty($error)){
			$data = json_decode($response, true);

			if($data && !empty($data['processed_files']) && is_array($data['processed_files']) && count($data['processed_files']) > 0){
				$processedImages = $data['processed_files'];
				foreach($processedImages as $key => $item){
					if(!empty($item['optimized_url'])){
						list($img_root_path, $img_root_url) = $this->getImgRootPath();
						$imgsrc_filepath = $this->getResourceRootPath($key, $img_root_url, $img_root_path);
						$imgsrc_webpfilepath = $this->getImgWebpPath($img_root_path, $imgsrc_filepath);
						if(!file_exists($imgsrc_webpfilepath)){
							$response = $this->w3RemoteGet($item['optimized_url']);
							if(empty($response)) continue;
							$isvalid = $this->w3IsValidWebP($response) || @imagecreatefromstring($response);
							if(!empty($isvalid)){
								$this->w3CreateFile($imgsrc_webpfilepath, $response);
							}
						}
					}
				}
			}

			if($data && !empty($data['processed_resized_files']) && is_array($data['processed_resized_files']) && count($data['processed_resized_files']) > 0){
				$processedImages = $data['processed_resized_files'];
				foreach($processedImages as $key => $item){
					if(!empty($item['resized_url'])){
						list($img_root_path, $img_root_url) = $this->getImgRootPath();
						$imgsrc_filepath = $this->getResourceRootPath($key, $img_root_url, $img_root_path);
						$imgsrc_webpfilepath = $this->getImgWebpPath($img_root_path, $imgsrc_filepath);
						$imgsrc_webpfilepath = $this->getImgWebpPath595xh($imgsrc_webpfilepath,$imgsrc_filepath);
						if(!file_exists($imgsrc_webpfilepath)){
							$response = $this->w3RemoteGet($item['resized_url']);
							if(empty($response)) continue;
							$isvalid = $this->w3IsValidWebP($response) || @imagecreatefromstring($response);
							if(!empty($isvalid)){
								$this->w3CreateFile($imgsrc_webpfilepath, $response);
							}
						}
					}
				}
			}
			
			if(empty($options['urls']) && empty($options['resize_urls'])){
				$this->w3DeleteFile($webpFile);
			} else {
				$this->w3CreateFile($webpFile, json_encode($options));
			}
		}
	}

    /**
	 * Check and enforce rate limiting
	 *
	 * Tracks the number of requests within a time window and returns
	 * whether a new request is allowed based on the rate limit.
	 * Uses w3GetOption to store rate limit data with hour-minute and count.
	 *
	 * @param string $rate_limit_key  Unique key for this rate limit
	 * @param int    $max_requests    Maximum number of requests allowed
	 * @param int    $time_window     Time window in seconds (default: 60 for 1 minute)
	 * @return bool  True if request is allowed, false if rate limit exceeded
	 */
	function w3CheckRateLimit($rate_limit_key, $max_requests, $time_window = 60)
	{
		$current_h_i = date('H-i');
		$rate_limit_data = $this->w3GetOption($rate_limit_key, true);
		if (empty($rate_limit_data) || !is_array($rate_limit_data)) {
			// First request in this minute
			$rate_limit_data = array(
				'h-i' => $current_h_i,
				'count' => 1
			);
		} else {
			// Check if we're still in the same minute
			if (isset($rate_limit_data['h-i']) && $rate_limit_data['h-i'] == $current_h_i) {
				// Same minute - check if we've exceeded the limit
				if (isset($rate_limit_data['count']) && $rate_limit_data['count'] >= $max_requests) {
					// Rate limit exceeded
					return false;
				}
				// Increment counter
				$rate_limit_data['count'] = isset($rate_limit_data['count']) ? $rate_limit_data['count'] + 1 : 1;
			} else {
				// New minute - reset counter
				$rate_limit_data = array(
					'h-i' => $current_h_i,
					'count' => 1
				);
			}
		}
		
		// Store the updated rate limit data
		$this->w3UpdateOption($rate_limit_key, $rate_limit_data);
		
		return true;
	}

    /**
     * Get the root path for a given relative path
     *
     * Attempts to resolve the absolute root directory for a given file or directory path by
     * checking multiple possible base paths, including the document root and ABSPATH.
     *
     * @param string $path Relative or absolute path to check
     * @return string The resolved root path, or empty string if not found
     */
    public function w3GetRootPath($path) {
        if (file_exists($this->addSettings['documentRoot'] . $path)) {
            return $this->addSettings['documentRoot'];
        }
        elseif (file_exists($this->addSettings['ABSPATH'] . '/' . ltrim($path, '/'))) {
            return $this->addSettings['ABSPATH'];
        }
        else {
            return '';
        }
    }
}
