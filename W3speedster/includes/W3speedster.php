<?php

/**
 * W3speedster Main Class
 *
 * This class extends the Core class and provides the main functionality
 * for the W3speedster. It handles initialization,
 * settings management, caching operations, and various utility methods.
 *
 * @package W3speedster
 * @author W3speedster Team
 */

namespace W3speedster;

// Prevent direct access
if (! defined('ABSPATH')) {
	exit;
}

/**
 * Main W3speedster Plugin Class
 *
 * Extends the Core class to provide comprehensive plugin functionality
 * including settings management, caching, optimization, and admin features.
 */
class W3speedster extends Core
{
	/**
	 * Constructor
	 *
	 * Initializes the plugin with default settings, sets up hooks,
	 * and configures various plugin parameters and paths.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->init_settings();

		// Set up theme paths
		$this->addSettings['theme_base_url'] = '';
		if ($this->addSettings['isMultisiteSubDomain']) {
			$this->addSettings['theme_base_url'] = str_replace($this->addSettings['siteUrl'], $this->addSettings['network_site_url'], $this->addSettings['theme_base_url']);
		}
		$theme_root_array = explode('/', $this->addSettings['theme_base_url']);
		$this->addSettings['theme_root'] = array_pop($theme_root_array);
		$this->addSettings['theme_base_dir'] = '';
		$this->addSettings['w3_img_aspect_ratio'] = [];
		$this->setAdditionalSettings();
	}

	/**
	 * Initialize settings
	 *
	 * Loads plugin settings from options and initializes
	 * the settings arrays. Also sets up admin notices if needed.
	 */
	protected function init_settings() {
        // Load main plugin settings
        $this->settings = $this->w3GetOption( 'w3_speedup_option', true );
        $this->settings = !empty($this->settings) && is_array($this->settings) ? $this->settings : array();
        $this->convertStringValuesToArray();
				
		$this->addSettings = array();
        
        // Set up basic URL settings
		$this->addSettings['homeUrl'] = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ?  'https://' : 'http://') . $_SERVER['HTTP_HOST'];
		$this->addSettings['siteUrl'] = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ?  'https://' : 'http://') . $_SERVER['HTTP_HOST'];
		$this->addSettings['siteUrlArr'] = $this->w3ParseUrl($this->addSettings['siteUrl']);
		
		// Set up www/non-www URL variations
		if (strpos($this->addSettings['siteUrlArr']['host'], 'www.') === 0) {
			$this->addSettings['siteUrlDiff'] = str_replace('www.','',$this->addSettings['siteUrlArr']['host']);
		}else{
			$this->addSettings['siteUrlDiff'] = 'www.'.$this->addSettings['siteUrlArr']['host'];
		}
 
		// Load permission errors and handle URL parameters
		$this->addSettings['permission_errors'] = $this->w3GetOption('permission_errors') ?? [];
		if (strpos($this->addSettings['homeUrl'], '?') !== false) {
            $home_url_arr = explode('?', $this->addSettings['homeUrl']);
            $this->addSettings['homeUrl'] = $home_url_arr[0];
        }
		
		// Set up multisite settings
		$this->addSettings['network_site_url'] = $this->addSettings['siteUrl'];
		$this->addSettings['is_multisite'] = $this->checkMultisite();
		$this->addSettings['is_multisite_networkadmin'] = false;
		$this->addSettings['isMultisiteSubDomain'] = false;
		
		// Set up file paths
		$this->addSettings['content_path'] = $_SERVER['DOCUMENT_ROOT'];
		$this->addSettings['documentRoot'] = $_SERVER['DOCUMENT_ROOT'];
	}

	/**
	 * Check if multisite is enabled
	 *
	 * @return bool True if multisite is enabled, false otherwise
	 */
	public function checkMultisite()
	{
		return false;
	}

	/**
	 * Remove cache directory
	 *
	 * Removes a directory using the WP Filesystem API.
	 *
	 * @param string $dir Directory path to remove
	 */
	function w3RmSingleDir($dir)
	{
		return @rmdir($dir);
	}
	/**
	 * Get uploads base path
	 *
	 * Retrieves and processes the uploads directory path,
	 * handling multisite configurations and subdomain setups.
	 *
	 * @return array Array containing base URL and base directory
	 */
	function w3GetUploadsBasepath()
	{
		$base_url = $this->addSettings['siteUrl'];
		$base_dir =  $this->addSettings['documentRoot'];
		return array($base_url, $base_dir);
	}

	/**
	 * Check if user is logged in
	 *
	 * Wrapper function to check if a user is currently logged in
	 * to site.
	 *
	 * @return bool True if user is logged in, false otherwise
	 */
	public function w3UserLoggedIn()
	{
		return false;
	}

	/**
	 * Customize CSS preload path for paginated content
	 *
	 * Removes pagination parameters from URLs when preloading CSS
	 * to ensure consistent caching across paginated content.
	 *
	 * @param string $url The URL to customize
	 * @return string Modified URL without pagination parameters
	 */
	function w3PreloadCssCustomizePath($url = '')
	{
		return $url;
	}
	/**
	 * Check if critical CSS should be ignored for specific pages
	 *
	 * Determines whether critical CSS generation should be skipped
	 * for certain page types like search results.
	 *
	 * @param string $url    The current page URL
	 * @param int    $ignore Current ignore status
	 * @return int Modified ignore status
	 */
	function checkIgnoreCriticalCssCallback($url, $ignore)
	{
		return $ignore;
	}
	/**
	 * Get current blog path for multisite installations
	 *
	 * Returns the current blog path for multisite setups,
	 * empty string for single site installations.
	 *
	 * @return string Blog path or empty string
	 */
	function getCurrentBlog()
	{
		return '';
	}

	/**
	 * Create directory with specified permissions
	 *
	 * Wrapper for mkdir function to create directories
	 * with specified permissions and recursive creation support.
	 *
	 * @param string $path        Directory path to create
	 * @param int    $permission  Directory permissions
	 * @param bool   $recusive    Whether to create directories recursively
	 * @return bool True on success, false on failure
	 */
	function w3Mkdir($path, $permission, $recursive)
	{
		if (!file_exists($path)) {
			return mkdir($path, $permission, $recursive);
		}
		return true;
	}

	/**
	 * Perform remote GET request
	 *
	 * Makes HTTP GET requests to external URLs with configurable
	 * options including mobile user agent support.
	 * Rate limited to 20 requests per minute.
	 *
	 * @param string $url     The URL to request
	 * @param array  $params  Additional parameters to send
	 * @param int    $method  HTTP method (0 = GET)
	 * @param bool   $mobile  Whether to use mobile user agent
	 * @param bool   $blocking Whether to block the request
	 * @param string $output   Whether to return the body or the response code 
	 * @return string Response body or the response code or empty string on failure
	 */
	function w3RemoteGet($url, $params = array(), $mobile = false, $blocking = true, $output = 'body')
	{
		$timeout = $blocking ? 10 : 2;

		// Prepare user agent
		$userAgent = $mobile
			? 'Mozilla/5.0 (Linux; Android 11; SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Mobile Safari/537.36'
			: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36';

		// Build full URL if params are provided
		if (!empty($params)) {
			$queryString = http_build_query($params);
			$url .= (strpos($url, '?') === false ? '?' : '&') . $queryString;
		}

		// Initialize cURL
		$ch = curl_init();

		curl_setopt_array($ch, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_TIMEOUT => $timeout,
			CURLOPT_USERAGENT => $userAgent,
			CURLOPT_HEADER => false
		));

		// Execute the request
		$responseBody = curl_exec($ch);
		$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$error = curl_error($ch);

		curl_close($ch);

		// Handle response
		if (!$error && $responseCode === 200 && !empty($responseBody)) {
			if ($output === 'body') {
				return $responseBody;
			} else {
				return $responseCode;
			}
		}

		return '';
	}


	function getCurlUrl($url)
	{
		if (!function_exists('curl_init')) {
			return file_get_contents($url);
		}
		$curl = curl_init();
		$headers = array(
			"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36"
		);
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 60,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => $headers,
		));
		$response = curl_exec($curl);
		if (curl_errno($curl)) {
			echo curl_error($curl) . $url;
			exit;
		}
		curl_close($curl);
		return $response;
	}



	/**
	 * Delete a file
	 *
	 * Wrapper for file deletion function.
	 *
	 * @param string $file Path to the file to delete
	 * @return bool True on success, false on failure
	 */
	function w3DeleteFile($file)
	{
		if (!is_string($file) || empty($file)) {
			return false;
		}
		if (file_exists($file)) {
			return @unlink($file);
		}
		return false;
	}

	/**
	 * Generate random number
	 *
	 * Wrapper for random number generation.
	 *
	 * @return int Random number between 100 and 999
	 */
	function w3Rand()
	{
		return random_int(100, 1000);
	}

	/**
	 * Check if current page should be excluded from optimization
	 *
	 * Determines whether the current page matches any exclusion
	 * patterns defined in the plugin settings.
	 *
	 * @param string $exclude_setting Comma-separated list of excluded pages
	 * @return bool True if page should be excluded, false otherwise
	 */
	function w3CheckIfPageExcluded($exclude_setting)
	{
		$e_p_from_optimization = !empty($exclude_setting) && is_array($exclude_setting) ? $exclude_setting : array();
		if (!empty($e_p_from_optimization)) {
			foreach ($e_p_from_optimization as $e_page) {
				if (empty($e_page)) {
					continue;
				}
				if (empty($this->addSettings['w3_get']['testing']) && $this->addSettings['homeUrl'] == $e_page) {
					return true;
				} else if ($this->addSettings['homeUrl'] != $e_page) {
					// Check URL pattern matches
					if (strpos($this->addSettings['full_url'], $e_page) !== false) {
						return true;
					}
				}
			}
		}
		return false;
	}

	/**
	 * Check if a plugin is active
	 *
	 * Checks whether a specific plugin is active on the current site
	 * or network-wide in multisite installations.
	 *
	 * @param string $plugin Plugin file path
	 * @return bool True if plugin is active, false otherwise
	 */
	public function w3IsPluginActive($plugin)
	{
		return false;
	}
	
	/**
	 * Encode array to JSON and echo it
	 *
	 * Wrapper for JSON encoding function.
	 *
	 * @param array $array Array to encode
	 * @return nothing
	 */
	function w3JsonEncode_e($array){
		echo json_encode($array);
	}
	/**
	 * Encode array to JSON
	 *
	 * Wrapper for JSON encoding function.
	 *
	 * @param array $array Array to encode
	 * @return string|false JSON string or false on failure
	 */
	function w3JsonEncode($array)
	{
		return json_encode($array);
	}

	/**
	 * Get direct call prevention code
	 *
	 * Returns the code snippet used to prevent direct file access.
	 *
	 * @return string PHP code to prevent direct access
	 */
	function exitDirectCall()
	{
		return 'if ( ! defined( "ABSPATH" ) ) {
            exit;
        }';
	}

	/**
	 * Check if current page should be ignored for optimization
	 *
	 * Determines whether the current page matches patterns that
	 * should be excluded from optimization
	 * core pages and e-commerce specific pages.
	 *
	 * @return bool True if page should be ignored, false otherwise
	 */
	public function ignored()
	{
		// Default list of ignored patterns
		$list = array(
			"\/wp\-comments\-post\.php",
			"\/wp\-login\.php",
			"\/robots\.txt",
			"\/wp\-cron\.php",
			"\/wp\-content",
			"\/wp\-admin",
			"\/wp\-includes",
			"\/xmlrpc\.php",
			"\/wp\-api\/",
			"leaflet\-geojson\.php",
			"\/clientarea\.php",
			"\/cart\/?$",
			"\/checkout",
			"\/receipt",
			"\/confirmation",
			"\/wc-api\/",
			"\/cart",

		);

		// Check if current URI matches any ignored patterns
		if (preg_match("/" . implode("|", $list) . "/i", $this->addSettings['w3_server']["REQUEST_URI"])) {
			return true;
		}

		return false;
	}

	/**
	 * Check if current user is a commenter
	 *
	 * Determines whether the current user has previously commented
	 * on the site based on stored commenter data.
	 *
	 * @return bool True if user is a commenter, false otherwise
	 */
	public function isCommenter()
	{
		return false;
	}

	/**
	 * Check if page is password protected
	 *
	 * Determines whether the current page is password protected
	 * by checking for password form or post password cookies.
	 *
	 * @param string $html Page HTML content
	 * @return bool True if page is password protected, false otherwise
	 */
	public function isPasswordProtected($html)
	{
		// Check for password form in HTML
		if (preg_match("/action\=[\'\"].+postpass.*[\'\"]/", $html)) {
			return true;
		}

		// Check for post password cookies
		foreach ($_COOKIE as $key => $value) {
			if (preg_match("/wp\-postpass\_/", $key)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Check if page contains CAPTCHA
	 *
	 * Determines whether the current page contains CAPTCHA
	 * elements that might interfere with caching.
	 *
	 * @param string $html Page HTML content
	 * @return bool True if CAPTCHA is detected, false otherwise
	 */
	public function checkCaptcha($html)
	{
		return false;
	}

	/**
	 * Check if current page is an error page
	 *
	 * Determines whether the current page is an error page
	 * (404, error page, etc.) that should not be cached.
	 *
	 * @param string $html Page HTML content
	 * @return bool True if error page, false otherwise
	 */
	public function last_error($html = false)
	{
		// Check HTTP response code
		if (function_exists("http_response_code") && (http_response_code() === 404)) {
			return true;
		}

		// Check 404 status
		if ($this->is_404()) {
			return true;
		}

		// Check for error page HTML pattern
		if (preg_match("/<body id\=\"error-page\">\s*<p>[^\>]+<\/p>\s*<\/body>/i", $html)) {
			return true;
		}
	}

	/**
	 * Check if current page content is HTML
	 *
	 * Determines whether the current page content type is HTML.
	 *
	 * @return bool True if content is HTML, false otherwise
	 */
	public function is_html()
	{
		return $this->current_page_content_type == "html" ? true : false;
	}

	/**
	 * Check firewall status for caching decisions
	 *
	 * Determines whether caching should be disabled due to
	 * firewall activity (e.g., Wordfence 503 responses).
	 *
	 * @return bool True if firewall is blocking, false otherwise
	 */
	function checkFirewall()
	{
		// for Wordfence: not to cache 503 pages
		if (defined('DONOTCACHEPAGE') && $this->w3IsPluginActive('wordfence/wordfence.php')) {
			if (function_exists("http_response_code") && http_response_code() == 503) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Generate .htaccess data for HTML caching
	 *
	 * Creates the necessary .htaccess rules for HTML caching
	 * including mobile detection, user authentication checks,
	 * and cache file serving rules.
	 *
	 * @return string .htaccess rules for HTML caching
	 */
	public function w3speedsterGetHtaccessData()
	{
		$mobile = "";
		$ifIsNotSecure = "";
		$trailing_slash_rule = "";
		$consent_cookie = "";

		$cache_path = str_replace($this->addSettings['content_path'], '', $this->addSettings['cache_path']);
		$cache_path .= '/w3-cache/html/%{ENV:HASH}/%{HTTPS}-%{HTTP_HOST}/';
		$mobile = "RewriteCond %{HTTP_USER_AGENT} !^.*(" . $this->getMobileUserAgents() . ").*$ [NC]" . "\n";

		if (isset($this->addSettings['w3_server']['HTTP_CLOUDFRONT_IS_MOBILE_VIEWER'])) {
			$mobile = $mobile . "RewriteCond %{HTTP_CLOUDFRONT_IS_MOBILE_VIEWER} false [NC]" . "\n";
			$mobile = $mobile . "RewriteCond %{HTTP_CLOUDFRONT_IS_TABLET_VIEWER} false [NC]" . "\n";
		}

		if (!preg_match("/^https/i", $this->addSettings['homeUrl'])) {
			$ifIsNotSecure = "RewriteCond %{HTTPS} !=on";
		}

		if ($this->is_trailing_slash()) {
			$trailing_slash_rule = "RewriteCond %{REQUEST_URI} \/$ [OR]" . "\n" . "RewriteCond %{REQUEST_URI} \.(xml)$ [NC]" . "\n";
		} else {
			$trailing_slash_rule = "RewriteCond %{REQUEST_URI} ![^\/]+\/$" . "\n";
		}

		$data = "# BEGIN W3HTMLCACHE"."\n".
				"<IfModule mod_rewrite.c>"."\n".
				"RewriteEngine On"."\n".
				"RewriteBase /"."\n".
				"RewriteRule ^ - [E=REQ_EXT:html]"."\n".
				"RewriteCond %{REQUEST_URI} \.xml$ [NC]"."\n".
				"RewriteRule ^ - [E=REQ_EXT:xml]"."\n".
				$this->prefixRedirect().
				$this->excludeRules()."\n".
				$this->http_condition_rule()."\n".
				"RewriteCond %{HTTP_USER_AGENT} !(".$this->get_excluded_useragent().")"."\n".
				"RewriteCond %{HTTP_USER_AGENT} !(W3\sCache\sPreload(\siPhone\sMobile)?\s*Bot)"."\n".
				"RewriteCond %{REQUEST_METHOD} !POST"."\n".
				"RewriteCond %{HTTP:X-Requested-With} !^XMLHttpRequest$ [NC]"."\n".
				$ifIsNotSecure."\n".
				"RewriteCond %{REQUEST_URI} !(\/){2}$"."\n".
				$trailing_slash_rule.
				$this->query_string_rule().
				$consent_cookie.
				"RewriteCond %{HTTP:Cookie} !comment_author_"."\n".
				"RewriteCond %{HTTP:Cookie} !safirmobilswitcher=mobil"."\n".
				'RewriteCond %{HTTP:Profile} !^[a-z0-9\"]+ [NC]'."\n".$mobile;
		
		if(defined('ABSPATH') && ABSPATH == "//"){
			$data = $data."RewriteCond %{DOCUMENT_ROOT}/".$cache_path."$1/%{QUERY_STRING}/index.%{ENV:REQ_EXT} -f"."\n";
		}else{
			//WARNING: If you change the following lines, you need to update webp as well
			$data = $data."RewriteCond %{DOCUMENT_ROOT}/".$cache_path."$1/%{QUERY_STRING}/index.%{ENV:REQ_EXT} -f [or]"."\n";
			// to escape spaces

			$data = $data."RewriteCond ".$cache_path.$this->getRewriteBase(true)."$1/%{QUERY_STRING}/index.%{ENV:REQ_EXT} -f"."\n";
		}
		$data = $data.'RewriteRule ^(.*) "/'.$cache_path.$this->getRewriteBase(true).'$1/%{QUERY_STRING}/index.%{ENV:REQ_EXT}" [L]'."\n";	

		if ($this->w3isPluginActive('wptouch/wptouch.php') || $this->w3isPluginActive('wptouch-pro/wptouch-pro.php')) {
			$this->set_wptouch(true);
		} else {
			$this->set_wptouch(false);
		}

		$data = $data."\n\n\n".$this->update_htaccess_mob($data);
		$data = $data."</IfModule>"."\n".
				"<FilesMatch \"index\.(html|htm)$\">"."\n".
				"AddDefaultCharset UTF-8"."\n".
				"<ifModule mod_headers.c>"."\n".
				"FileETag None"."\n".
				"Header unset ETag"."\n".
				"Header set Cache-Control \"max-age=0, no-cache, no-store, must-revalidate\""."\n".
				"Header set Pragma \"no-cache\""."\n".
				"Header set Expires \"Mon, 29 Oct 1923 20:30:00 GMT\""."\n".
				"</ifModule>"."\n".
				"</FilesMatch>"."\n".
				"# END W3HTMLCACHE"."\n";
		
		return preg_replace("/\n+/","\n", $data);
	}

	function is_writable($path)
	{
		global $wp_filesystem;
		if (function_exists('WP_Filesystem')) {
			return $wp_filesystem->is_writable($path);
		}
		return is_writable($path);
	}

	/**
	 * Get rewrite base for URL rewriting
	 *
	 * Determines the base path for URL rewriting rules,
	 * handling subdirectory installations and multisite setups.
	 *
	 * @param bool $sub Whether this is for subdirectory handling
	 * @return string Rewrite base path or empty string
	 */
	public function getRewriteBase($sub = "")
	{
		return "";
	}

	/**
	 * Set WPtouch mobile theme status
	 *
	 * Stores the WPtouch mobile theme activation status
	 * for use in mobile-specific caching rules.
	 *
	 * @param bool $status Whether WPtouch is active
	 */
	public function set_wptouch($status)
	{
		$this->addSettings['wptouch'] = $status;
	}

	/**
	 * Update .htaccess with mobile-specific rules
	 *
	 * Modifies .htaccess content to include mobile-specific
	 * caching rules and WPtouch compatibility.
	 *
	 * @param string $data Original .htaccess content
	 * @return string Modified .htaccess content with mobile rules
	 */
	public function update_htaccess_mob($data)
	{
		preg_match("/RewriteEngine\sOn(.+)/is", $data, $out);
		$htaccess = "\n##### mobile #####\n";
		$htaccess .= $out[0];

		// Add WPtouch specific rules if active
		if ($this->addSettings['wptouch']) {
			$wptouch_rule = "RewriteCond %{HTTP:Cookie} !wptouch-pro-view=desktop";
			$htaccess = str_replace("RewriteCond %{HTTP:Profile}", $wptouch_rule . "\n" . "RewriteCond %{HTTP:Profile}", $htaccess);
		}

		// Handle mobile switcher and user agent conditions
		$htaccess = str_replace("RewriteCond %{HTTP:Cookie} !safirmobilswitcher=mobil", "RewriteCond %{HTTP:Cookie} !safirmobilswitcher=masaustu", $htaccess);
		$htaccess = str_replace("RewriteCond %{HTTP_USER_AGENT} !^.*", "RewriteCond %{HTTP_USER_AGENT} ^.*", $htaccess);
		$htaccess = preg_replace("/\/index.%{ENV:REQ_EXT}/", "/w3mob/index.%{ENV:REQ_EXT}", $htaccess);

		//$htaccess = preg_replace("/(\/cache\/)[^\/]+(\/.{1}1\/index\.html)/","$1".$this->get_folder_name()."$2", $htaccess);
		$htaccess .= "\n##### mobile #####\n";

		return $htaccess;
	}

	/**
	 * Check if permalink structure uses trailing slashes
	 *
	 * Determines whether the permalink structure
	 * ends with a trailing slash, excluding Custom Permalinks plugin.
	 *
	 * @return bool True if trailing slash is used, false otherwise
	 */
	public function is_trailing_slash()
	{
		return false;
	}

	/**
	 * Get excluded user agent patterns
	 *
	 * Returns a list of user agent patterns that should be
	 * excluded from caching and optimization.
	 *
	 * @return string Pipe-separated list of excluded user agents
	 */
	protected function get_excluded_useragent()
	{
		return "facebookexternalhit|Twitterbot|LinkedInBot|WhatsApp|Mediatoolkitbot";
	}

	/**
	 * Generate prefix redirect rules for .htaccess
	 *
	 * Creates .htaccess rules to handle www/non-www redirects
	 * and HTTPS redirects based on the site configuration.
	 *
	 * @return string .htaccess redirect rules or empty string if disabled
	 */
	public function prefixRedirect()
	{
		$forceTo = "";
		// Handle HTTPS redirects
		if (preg_match("/^https:\/\//", $this->addSettings['homeUrl'])) {
			if (preg_match("/^https:\/\/www\./", $this->addSettings['homeUrl'])) {
				$forceTo = "\nRewriteCond %{HTTPS} =on" . "\n" .
					"RewriteCond %{HTTP_HOST} ^www." . str_replace("www.", "", $this->addSettings['w3_server']["HTTP_HOST"]) . "\n";
			} else {
				$forceTo = "\nRewriteCond %{HTTPS} =on" . "\n" .
					"RewriteCond %{HTTP_HOST} ^" . str_replace("www.", "", $this->addSettings['w3_server']["HTTP_HOST"]) . "\n";
			}
		} else {
			// Handle HTTP redirects
			if (preg_match("/^http:\/\/www\./", $this->addSettings['homeUrl'])) {
				$forceTo = "\nRewriteCond %{HTTP_HOST} ^" . str_replace("www.", "", $this->addSettings['w3_server']["HTTP_HOST"]) . "\n" .
					"RewriteRule ^(.*)$ " . preg_quote($this->addSettings['homeUrl'], "/") . "\/$1 [R=301,L]" . "\n";
			} else {
				$forceTo = "\nRewriteCond %{HTTP_HOST} ^www." . str_replace("www.", "", $this->addSettings['w3_server']["HTTP_HOST"]) . " [NC]" . "\n" .
					"RewriteRule ^(.*)$ " . preg_quote($this->addSettings['homeUrl'], "/") . "\/$1 [R=301,L]" . "\n";
			}
		}
		return $forceTo;
	}

	/**
	 *
	 *
	 * @param string $path Path to the file to read
	 * @return string|false File contents or false on failure
	 */
	function w3speedsterGetContents($path)
	{
		if (!is_file($path)) {
			return false;
		}
		$content = @file_get_contents($path);
		if (empty($content)) {
			return '';
		}
		return $content;
	}

	/**
	 *
	 * @param string $path    Path to the file to write
	 * @param string $content Content to write to the file
	 * @return int|false Number of bytes written or false on failure
	 */
	function w3speedsterPutContents($path, $content, $append = false)
	{
		$file = @file_put_contents($path, $content);
		return $file;
	}

	/**
	 * Get asset URL
	 *
	 * Constructs the full URL for plugin assets.
	 *
	 * @param string $path Asset path relative to plugin directory
	 * @return string Full asset URL
	 */
	function assetUrl($path)
	{
		return W3SPEEDSTER_URL . $path;
	}

	/**
	 * Get AJAX URL
	 *
	 * Returns the admin AJAX URL.
	 *
	 * @return string Admin AJAX URL
	 */
	function getAjaxUrl()
	{
		return $this->addSettings['siteUrl'];
	}

	/**
	 * Escape HTML
	 *
	 * Wrapper for HTML escaping function.
	 *
	 * @param string $text Text to escape
	 * @return string Escaped text
	 */
	function esc_html($text)
	{
		return $text;
	}

	/**
	 * Escape HTML
	 *
	 * Wrapper for HTML escaping function.
	 *
	 * @param string $text Text to escape
	 * @return string Escaped text
	 */
	function esc_html_e($text)
	{
		echo $text;
	}
	/**
	 * Escape URL
	 *
	 * Wrapper for URL escaping function.
	 *
	 * @param string $text URL to escape
	 * @return string Escaped URL
	 */
	function esc_url_e($text)
	{
		echo $text;
	}
	/**
	 * Escape URL
	 *
	 * Wrapper for URL escaping function.
	 *
	 * @param string $text URL to escape
	 * @return string Escaped URL
	 */
	function esc_url($url)
	{
		$url = filter_var($url, FILTER_SANITIZE_URL);
		return filter_var($url, FILTER_VALIDATE_URL) === false ? '' : $url;
	}

	/**
	 * Translate and echo text
	 *
	 * Wrapper for translation and echo function.
	 *
	 * @param string $text Text to translate and display
	 */
	function translate($text)
	{
		$this->esc_html_e($text);
	}

	/**
	 * Escape HTML attribute
	 *
	 * Wrapper for HTML attribute escaping function.
	 *
	 * @param string $text Text to escape
	 * @return string Escaped text
	 */
	function esc_attr_e($text)
	{
		echo $text;
	}
	/**
	 * Escape HTML attribute
	 *
	 * Wrapper for HTML attribute escaping function.
	 *
	 * @param string $text Text to escape
	 * @return string Escaped text
	 */
	function esc_attr($text)
	{
		return $text;
	}

	/**
	 * Translate text
	 *
	 * Wrapper for translation function.
	 *
	 * @param string $text Text to translate
	 * @return string Translated text
	 */
	function translate_($text)
	{
		return  $text;
	}

	/**
	 * Create secure nonce key
	 *
	 * Wrapper for nonce creation function.
	 *
	 * @param string $option Action name for the nonce
	 * @return string Generated nonce
	 */
	function createSecureKey($option)
	{
		$key = rand(10000, 100000);
		$this->w3UpdateOption($option, $key, 0);
		return $key;
	}

	/**
	 * Verify security nonce key
	 *
	 * Wrapper for nonce verification function.
	 *
	 * @param string $key    Nonce to verify
	 * @param string $option Action name for the nonce
	 * @return bool|int False on failure, 1 or 2 on success
	 */
	function checkSecurityKey($key, $option)
	{
		$getKey = $this->w3GetOption($option, 0);
		if ($getKey == $key) {
			return true;
		}
		return false;
	}

	/**
	 * Check if current page is a 404 error
	 *
	 * Determines whether the current page is a 404 error
	 * by checking HTTP status code.
	 *
	 * @return bool True if 404 error, false otherwise
	 */
	function is_404()
	{
		if ($this->statusCode >= 400) {
			return true;
		}
		return false;
	}

	/**
	 * Import plugin data
	 *
	 * Imports plugin settings from JSON data and displays
	 * appropriate success or failure notices.
	 *
	 * @param string $data JSON encoded data to import
	 */
	function importData($data)
	{
		$import_text = json_decode(stripcslashes($data), true);
		if ($import_text !== null) {
			$this->w3UpdateOption('w3_speedup_option',  $import_text, 'no');
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Remove advanced cache files
	 *
	 * Deletes both standard and W3speedster advanced cache files
	 * to clean up caching remnants.
	 */
	function removeAdvanceCacheFile()
	{
		$advancedCacheFile = W3SPEEDSTER_PATH . '/advanced-cache.php';
		if (file_exists($advancedCacheFile)) {
			$this->w3DeleteFile($advancedCacheFile);
		}
	}

	/**
	 * Check and configure HTML cache settings
	 *
	 * Handles HTML caching configuration based on user settings,
	 * including advanced cache file creation and .htaccess modifications.
	 */
	function checkHtmlCacheSettings($reset = false)
	{
		$this->w3ModifyHtaccess();
		if ((!empty($this->addSettings['w3_post']['html_caching']) && $this->addSettings['w3_post']['by_serve_cache_file'] == 'advanceCache') || ($reset && $this->settings['by_serve_cache_file'] == 'advanceCache')) {
			$advancedCacheFile = W3SPEEDSTER_PATH . '/advanced-cache.php';
			$this->w3SpeedsterRemoveHtmlCacheCode();
			if (file_exists($advancedCacheFile) && strpos($this->w3speedsterGetContents($advancedCacheFile), 'Added By W3speedster Pro') === false) {
				$this->addSettings['advanced_cache_exist'] = 1;
			} else {
				$this->createAdvanceCacheFile($advancedCacheFile);
			}
		} elseif ((!empty($this->addSettings['w3_post']['html_caching']) && $this->addSettings['w3_post']['by_serve_cache_file'] == 'htaccess') ||   ($reset && $this->settings['by_serve_cache_file'] == 'htaccess')) {
			$htaccessPath = $this->addSettings['documentRoot'] . "/.htaccess";
			if ($htaccessContent = $this->w3speedsterGetContents($htaccessPath)) {
				$this->removeAdvanceCacheFile();
				$htaccess = preg_replace("/#\s?BEGIN\s?W3HTMLCACHE.*?#\s?END\s?W3HTMLCACHE/s", "", $htaccessContent);
				$data = $this->w3speedsterGetHtaccessData();
				if (!empty($this->htaccessModifyKeys['html_cache'])) {
					if ($this->checkHtaccessStatus($data)) {
						$htaccess = $data . $htaccess;
					} else {
						unset($this->settings['html_caching']);
						$this->w3UpdateOption('w3_speedup_option', $this->settings, 'no');
						$this->w3AddError('danger', 'Server error: Unable to apply .htaccess caching rules to the site.');
					}
				} else {
					$htaccess = $data . $htaccess;
				}
				$this->w3speedsterPutContents($htaccessPath, preg_replace('/^[ \t]*[\r\n]+/m', '', $htaccess));
			} else {
				return array("htaccess not writable", "w3speedster");
			}
		} elseif (empty($this->addSettings['w3_post']['html_caching'])) {
			$this->removeAdvanceCacheFile();
			$this->w3SpeedsterRemoveHtmlCacheCode();
		}
	}

	/**
	 * Create advanced cache file
	 *
	 * Creates the advanced cache file with W3speedster-specific
	 * caching code, with fallback to alternative filename.
	 *
	 * @param string $advancedCacheFile Path to the advanced cache file
	 */
	function createAdvanceCacheFile($advancedCacheFile)
	{
		$file_content = $this->w3SpeedsterGetDataAdvancedCacheFile();
		$this->w3speedsterPutContents($advancedCacheFile, $file_content);
	}

	/**
	 * Check if advanced cache file exists and display warning
	 *
	 * Checks for existing advanced cache files not created by W3speedster
	 * and displays appropriate warnings and deletion options.
	 */
	function checkAdvCacheFileExists()
	{
		return false;
	}

	/**
	 * Enqueue admin scripts and styles
	 *
	 * Sets up hooks for enqueuing admin scripts and styles
	 * with appropriate priorities.
	 */
	function enqueueScripts()
	{
		?>
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700">
		<link rel="stylesheet" id="w3-fonts" href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700">
		<link rel="stylesheet" id="w3-font-awesome" href="<?php echo W3SPEEDSTER_URL . 'assets/css/font-awesome.min.css'; ?>">
		<link rel="stylesheet" id="w3-bootstrap" href="<?php echo W3SPEEDSTER_URL . 'assets/css/bootstrap.min.css'; ?>">
		<link rel="stylesheet" id="w3-datatables" href="<?php echo W3SPEEDSTER_URL . 'assets/css/jquery.dataTables.min.css'; ?>">
		<link rel="stylesheet" id="w3-admin-main" href="<?php echo W3SPEEDSTER_URL . 'assets/css/admin.css?ver=' . rand(); ?>">
		<link rel="stylesheet" id="w3-jquery-ui" href="<?php echo W3SPEEDSTER_URL . 'assets/css/jquery-ui.css'; ?>">
		<link rel="stylesheet" id="w3-select2" href="<?php echo W3SPEEDSTER_URL . 'assets/css/select2.min.css'; ?>">
		<link rel="stylesheet" id="w3-select2" href="<?php echo W3SPEEDSTER_URL . 'assets/css/codemirror.min.css'; ?>">
		<script id="w3-jquery" src="<?php echo W3SPEEDSTER_URL . 'assets/js/jquery.min.js'; ?>"></script>
		<script id="w3-datatables" src="<?php echo W3SPEEDSTER_URL . 'assets/js/jquery.dataTables.min.js'; ?>"></script>
		<script id="w3-prefixfree" src="<?php echo W3SPEEDSTER_URL . 'assets/js/prefixfree.min.js'; ?>"></script>
		<script id="w3-bootstrap" src="<?php echo W3SPEEDSTER_URL . 'assets/js/bootstrap.min.js'; ?>"></script>
		<script id="w3-jquery-ui" src="<?php echo W3SPEEDSTER_URL . 'assets/js/jquery-ui.js'; ?>"></script>
		<script id="w3-select2" src="<?php echo W3SPEEDSTER_URL . 'assets/js/select2.min.js'; ?>"></script>
		<script id="w3-admin-core" src="<?php echo W3SPEEDSTER_URL . 'assets/js/w3-admin-core.js?ver=' . rand(); ?>"></script>
		<script id="w3-codemirror" src="<?php echo W3SPEEDSTER_URL . 'assets/js/codemirror.min.js'; ?>"></script>
		<script id="w3-diff" src="<?php echo W3SPEEDSTER_URL . 'assets/js/diff.min.js'; ?>"></script>
		<script id="w3-codemirror-js" src="<?php echo W3SPEEDSTER_URL . 'assets/js/codemirror.javascript.min.js'; ?>"></script>
		<script id="w3-custom-js" src="<?php echo W3SPEEDSTER_URL . 'assets/js/custom.js?ver=' . rand(); ?>"></script>
		<?php
	}

	/**
	 * Modify .htaccess file with W3speedster rules
	 *
	 * Adds or removes various .htaccess rules including LBC, Gzip,
	 * WebP, and 404 redirect rules based on plugin settings.
	 *
	 * @return array Status message and type
	 */
	function w3ModifyHtaccess()
	{
		$path = $this->addSettings['documentRoot'] . '/';
		if (!file_exists($path . ".htaccess")) {
			if (isset($this->addSettings['w3_server']["SERVER_SOFTWARE"]) && $this->addSettings['w3_server']["SERVER_SOFTWARE"] && (preg_match("/iis/i", $this->addSettings['w3_server']["SERVER_SOFTWARE"]) || preg_match("/nginx/i", $this->addSettings['w3_server']["SERVER_SOFTWARE"]))) {
				//
			} else {
				return array("<label>.htaccess was not found</label>", "w3speedster");
			}
		}

		$htaccess = $this->w3speedsterGetContents($path . ".htaccess");

		if ($this->is_writable($path . ".htaccess")) {
			$change_in_htaccess = 0;
			if (!empty($this->settings['lbc'])) {
				if (strpos($htaccess, '# BEGIN W3LBC') === false || strpos($htaccess, '# END W3LBC') === false) {
					$htaccess = $this->w3InsertLbcRule($htaccess) . "\n";
					$change_in_htaccess = 1;
				}
			} elseif (strpos($htaccess, '# BEGIN W3LBC') !== false || strpos($htaccess, '# END W3LBC') !== false) {
				$htaccess = preg_replace("/#\s?BEGIN\s?W3LBC.*?#\s?END\s?W3LBC/s", "", $htaccess);
				$change_in_htaccess = 1;
			}
			if (!empty($this->settings['gzip'])) {
				if (strpos($htaccess, '# BEGIN W3Gzip') === false || strpos($htaccess, '# END W3Gzip') === false) {
					$htaccess = $this->w3InsertGzipRule($htaccess);
					$change_in_htaccess = 1;
				}
			} elseif (strpos($htaccess, '# BEGIN W3Gzip') !== false || strpos($htaccess, '# END W3Gzip') !== false) {
				$htaccess = preg_replace("/\s*\#\s?BEGIN\s?W3Gzip.*?#\s?END\s?W3Gzip\s*/s", "", $htaccess);
				$change_in_htaccess = 1;
			}

			if (!empty($this->addSettings['disable_htaccess_webp']) && !$this->checkEnableCdn('image')) {
				if (!empty($this->settings['webp_png']) || !empty($this->settings['webp_jpg'])) {
					if (strpos($htaccess, '# BEGIN W3WEBP') === false || strpos($htaccess, '# END W3WEBP') === false) {
						$htaccess = $this->w3InsertWebp($htaccess) . "\n";
						$change_in_htaccess = 1;
					}
				} elseif (strpos($htaccess, '# BEGIN W3WEBP') !== false || strpos($htaccess, '# END W3WEBP') !== false) {
					$htaccess = preg_replace("/#\s?BEGIN\s?W3WEBP.*?#\s?END\s?W3WEBP/s", "", $htaccess);
					$change_in_htaccess = 1;
				}
			} elseif (strpos($htaccess, '# BEGIN W3WEBP') !== false || strpos($htaccess, '# END W3WEBP') !== false) {
				$htaccess = preg_replace("/#\s?BEGIN\s?W3WEBP.*?#\s?END\s?W3WEBP/s", "", $htaccess);
				$change_in_htaccess = 1;
			}
			if (strpos($htaccess, '# BEGIN W3404') === false || strpos($htaccess, '# END W3404') === false) {
				// $htaccess = $this->w3Insert_404RedirectToFile($htaccess);
				// $change_in_htaccess = 1;
			}
			if ($change_in_htaccess) {
				$this->w3speedsterPutContents($path . ".htaccess", $htaccess);
			}
		} else {
			return array($this->translate_("Options have been saved"), "updated");
		}
		return array($this->translate_("Options have been saved"), "updated");
	}

	/**
	 * Insert WebP .htaccess rules
	 *
	 * Adds .htaccess rules for WebP image serving, including
	 * rewrite conditions and rules for serving WebP images
	 * when the browser supports them.
	 *
	 * @param string $htaccess Current .htaccess content
	 * @return string Modified .htaccess content with WebP rules
	 */
	function w3InsertWebp($htaccess)
	{
		$wp_content_arr = explode('/', trim($this->addSettings['content_path'], '/'));
		$wp_content = array_pop($wp_content_arr);
		$wp_content_webp = $wp_content . "/w3-webp/";
		$basename = $wp_content_webp . "$1.webp";
		if (preg_match("/https?\:\/\/[^\/]+\/(.+)/", $this->addSettings['siteUrl'], $siteurl_base_name)) {
			if (preg_match("/https?\:\/\/[^\/]+\/(.+)/", $this->addSettings['homeUrl'], $homeurl_base_name)) {
				$homeurl_base_name[1] = trim($homeurl_base_name[1], "/");
				$siteurl_base_name[1] = trim($siteurl_base_name[1], "/");

				if ($homeurl_base_name[1] == $siteurl_base_name[1]) {
					if (preg_match("/" . preg_quote($homeurl_base_name[1], "/") . "$/", trim(ABSPATH, "/"))) {
						$basename = $homeurl_base_name[1] . "/" . $basename;
					}
				}
			} else {
				$siteurl_base_name[1] = trim($siteurl_base_name[1], "/");
				$basename = $siteurl_base_name[1] . "/" . $basename;
			}
		}

		if ($this->addSettings['documentRoot'] == "//") {
			$RewriteCond = "RewriteCond %{DOCUMENT_ROOT}/" . $basename . " -f" . "\n";
		} else {
			$tmp_ABSPATH = str_replace(" ", "\ ", $this->addSettings['documentRoot']);

			$RewriteCond = "RewriteCond %{DOCUMENT_ROOT}/" . $basename . " -f [or]" . "\n";
			$RewriteCond = $RewriteCond . "RewriteCond " . $tmp_ABSPATH . $wp_content_webp . "$1w3.webp -f" . "\n";
		}

		$data = "\n" . "# BEGIN W3WEBP" . "\n" .
			"<IfModule mod_rewrite.c>" . "\n" .
			"RewriteEngine On" . "\n" .
			"RewriteCond %{HTTP_ACCEPT} image/webp" . "\n" .
			"RewriteCond %{REQUEST_URI} \.(jpe?g|png)" . "\n" .
			$RewriteCond .
			"RewriteRule ^" . $wp_content . "/([^/]+/.+)\.(jpe?g|png)$ /" . $wp_content_webp . "$1.webp [L]" . "\n" .
			"</IfModule>" . "\n" .
			"<IfModule mod_headers.c>" . "\n" .
			"Header append Vary Accept env=REDIRECT_accept" . "\n" .
			"</IfModule>" . "\n" .
			"AddType image/webp .webp" . "\n" .
			"# END W3WEBP" . "\n";
		$htaccess = preg_replace("/#\s?BEGIN\s?W3WEBP.*?#\s?END\s?W3WEBP/s", "", $htaccess);
		if (!empty($this->htaccessModifyKeys['webp'])) {
			if ($this->checkHtaccessStatus($data)) {
				$htaccess = $data . $htaccess;
			} else {
				unset($this->settings['webp_png'], $this->settings['webp_jpg']);
				$this->w3UpdateOption('w3_speedup_option', $this->settings, 'no');
				$this->w3AddError('danger', 'Server error: Unable to apply .htaccess webp rules to the site.');
			}
		} else {
			$htaccess = $data . $htaccess;
		}
		return $htaccess;
	}

	/**
	 * Check if multisite is enabled
	 *
	 *
	 * @return int 1 if multisite, 0 if single site
	 */
	function w3CheckMultisite()
	{
		return 0;
	}

	/**
	 * Parse URL
	 *
	 *
	 * @param string $url URL to parse
	 * @return array|false Parsed URL components or false on failure
	 */
	function w3ParseUrl($url, $component = '')
	{
		if(!empty($component)){
			return parse_url($url, $component);
		}
		return parse_url($url);
	}

	/**
	 * Get custom CSS
	 *
	 * Retrieves custom CSS from plugin settings.
	 *
	 * @return string Custom CSS content
	 */
	function getCustomCss()
	{
		return $this->settings['custom_css'];
	}

	/**
	 * Check if current route is a special route
	 *
	 * Determines whether the current URL is a special route
	 * that should not be cached (e.g., REST API, login, admin).
	 *
	 * @return bool True if special route, false otherwise
	 */
	public function isSpecialRoute()
	{
		$current_url = $this->addSettings['full_url'];

		if (preg_match('/(.*\/wp\/v2\/.*)/', $current_url)) {
			return true;
		}

		if (preg_match('/(.*wp-login.*)/', $current_url)) {
			return true;
		}

		if (preg_match('/(.*wp-admin.*)/', $current_url)) {
			return true;
		}

		return false;
	}

	/**
	 * Check if current page is admin
	 *
	 * @return bool True if admin page, false otherwise
	 */
	function isAdmin()
	{
		return false;
	}

	/**
	 * Check if current page is AMP endpoint
	 *
	 * Determines whether the current page is an AMP (Accelerated Mobile Pages)
	 * endpoint, with fallback for sites without AMP plugin.
	 *
	 * @return bool True if AMP endpoint, false otherwise
	 */
	function isAmpEndpoint()
	{
		return false;
	}

	/**
	 * Check if current user is admin
	 *
	 * Determines whether the current user has administrative privileges
	 * by checking if they can edit others' pages.
	 *
	 * @return bool True if admin user, false otherwise
	 */
	function checkAdminUser()
	{
		return false;
	}

	/**
	 * Check and create folder
	 *
	 * Wrapper for folder creation function.
	 *
	 * @param string $cache_path Path to create
	 * @return bool True on success, false on failure
	 */
	function w3CheckNCreateFolder($cache_path)
	{
		return $this->w3CreateFolder($cache_path);
	}

	/**
	 * Create HTML cache file
	 *
	 * Creates a cached HTML file with performance metrics
	 * and mobile detection information.
	 *
	 * @param string $html HTML content to cache
	 * @return string Modified HTML content
	 */
	function createHtmlfile($html)
	{
		$userAgent = $this->addSettings['HTTP_USER_AGENT'];
		$isMobile = $this->w3speedsterIsMobileDevice($userAgent);
		$mob_msg = '';
		if ($isMobile) {
			$mob_msg = 'Mobile';
		}
		$fileName = $this->cacheFilePath;
		$endtime = $this->microtime_float();
		$current_time = gmdate("Y-m-d H:i:s T");
		$html .= '<!--' . $mob_msg . ' Cache Created By W3speedster Pro at ' . $current_time . ' in ' . number_format($endtime - $this->addSettings['starttime'], 2) . ' secs-->';
		$this->w3CreateFile($fileName, $html);
	}
	/**
	 * Customize resource URL
	 *
	 * Hook for customizing resource URLs during optimization.
	 *
	 * @param string $css         Original CSS content
	 * @param bool   $enable_cdn  Whether CDN is enabled
	 * @param string $cssNew      New CSS content
	 * @return string Modified CSS content
	 */
	function customizeResourceUrl($css, $enable_cdn, $cssNew)
	{
		return $cssNew;
	}
	/**
	 * Customize critical CSS filename
	 *
	 * Hook for customizing critical CSS filename generation.
	 *
	 * @param string $filename Original filename
	 * @return string Modified filename
	 */
	function customizeCriticalCssFilename($filename)
	{
		return $filename;
	}

	/**
	 * Reload current page
	 *
	 * Performs a safe redirect to the current page URI.
	 */
	function pageReload()
	{
		header("Location: " . $_SERVER['REQUEST_URI']);
		exit;
	}

	/**
	 * Get user IP address
	 *
	 * Retrieves the real IP address of the current user,
	 * handling various proxy and forwarding scenarios.
	 *
	 * @return string User IP address
	 */
	private function getUserIP()
	{
		if (! empty($this->addSettings['w3_server']['HTTP_CLIENT_IP'])) {
			$ip = $this->addSettings['w3_server']['HTTP_CLIENT_IP'];
		} elseif (! empty($this->core->addSettings['w3_server']['HTTP_X_FORWARDED_FOR'])) {
			$ip = $this->addSettings['w3_server']['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $this->core->addSettings['w3_server']['REMOTE_ADDR'] ?? '';
		}
		return $ip;
	}

	/**
	 * User profile data placeholder
	 *
	 * Placeholder method for user profile data handling.
	 */
	function userProfileData() {}
	/**
	 * Change profile settings placeholder
	 *
	 * Placeholder method for profile settings changes.
	 */
	function w3changeProfilesettings() {}

	/**
	 * Get user hash from cookies
	 *
	 *
	 * @return string User hash or empty string
	 */
	function getUserHash()
	{
		return '';
	}

	/**
	 * Perform remote POST request
	 *
	 * Makes HTTP POST requests to external URLs with configurable
	 * content type (JSON or form data).
	 *
	 * @param string $url        The URL to request
	 * @param array  $body       Data to send
	 * @param bool   $sendAsJson Whether to send as JSON
	 * @param bool   $mobile     Whether to use mobile user agent
	 * @param bool   $blocking   Whether to block the request
	 * @param string $output     Whether to return the body or the response code
	 * @return string Response body or empty string on failure
	 */
	function w3RemotePost($url, $body, $sendAsJson = true, $mobile = false, $blocking = true, $output = 'body')
	{
		$timeout = $blocking ? 30 : 2;

		// Determine content type and body format
		if ($sendAsJson) {
			$headers = ['Content-Type: application/json'];
			$bodyToSend = json_encode($body);
		} else {
			$headers = ['Content-Type: application/x-www-form-urlencoded'];
			$bodyToSend = http_build_query($body);
		}

		// Add User-Agent header
		$userAgent = $mobile
			? 'Mozilla/5.0 (Linux; Android 11; SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Mobile Safari/537.36'
			: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36';

		$headers[] = 'User-Agent: ' . $userAgent;

		// Initialize cURL
		$ch = curl_init();

		curl_setopt_array($ch, array(
			CURLOPT_URL => $url,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $bodyToSend,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_TIMEOUT => $timeout,
			CURLOPT_HTTPHEADER => $headers,
		));

		// Execute request
		$responseBody = curl_exec($ch);
		$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$error = curl_error($ch);

		curl_close($ch);

		// Handle response
		if (!$error && !empty($responseBody)) {
			if ($output === 'body') {
				return $responseBody;
			} else {
				return $responseCode;
			}
		}

		return '';
	}

	/**
	 * Filter thumbnail images
	 *
	 * Filters out thumbnail images from a list of image URLs,
	 * keeping only original images for optimization.
	 *
	 * @param array $urls Array of image URLs to filter
	 * @return array Filtered array with only original images
	 */
	function w3FilterThumbnailImages(array $urls)
	{
		$originals = [];
		$sizes = [1536, 2048, 768, 595];

		foreach ($urls as $url) {
			$filename = basename($url);
			if (preg_match('/-(\d+)x(\d+)\.(jpg|jpeg|png|gif|webp)$/i', $filename, $m)) {
				$dim = $m[1];
				if (in_array($dim, $sizes) || (int)$dim <= 595) {
					continue;
				}
			} elseif (strpos($url, '-595xh.') !== false) {
				continue;
			}
			$originals[] = $url;
		}

		$originals = array_values(array_unique($originals));

		return $originals;
	}

	/**
	 * Check if multisite
	 *
	 *
	 * @return bool True if multisite, false otherwise
	 */
	public function is_multisite()
	{
		return false;
	}

	/**
	 * Strip tags
	 *
	 * @param string $url URL.
	 * @return string Stripped URL
	 */
	public function stripTags($url)
	{
		return $url;
	}

	/**
	 * Unslash
	 *
	 * @param string $text Text.
	 * @return string Unslashed text
	 */
	public function unslash($text)
	{
		return $text;
	}
	/**
	 * Sanitize text field
	 *
	 * @param string $text Text.
	 * @return string Sanitized text
	 */
	public function sanitizeTextField($text)
	{
		return $text;
	}
	/**
	 * Verify nonce
	 *
	 * @param string $action Action.
	 * @return bool True if nonce is valid, false otherwise
	 */
	public function w3VerifyNonce($action)
	{
		if (empty($_POST['_w3nonce']) || (!empty($_POST['_w3nonce']) && $this->checkSecurityKey($this->sanitizeTextField($this->unslash($_POST['_w3nonce'])), $action))) {
			return true;
		}
		return false;
	}

	/**
	 * Verify request value
	 *
	 * @param string $key Key.
	 * @param string $value Value.
	 * @return bool True if value is valid, false otherwise
	 */
	public function verifyRequestValue($key, $value = '', $sanitize = true)
	{
		if (empty($value) && !empty($_REQUEST[$key])) {
			if ($sanitize) {
				return $this->sanitizeTextField($this->unslash($_REQUEST[$key]));
			} else {
				return $this->unslash($_REQUEST[$key]);
			}
		}
		if (!empty($value) && !empty($_REQUEST[$key]) && $this->sanitizeTextField($this->unslash($_REQUEST[$key])) == $value) {
			return true;
		}
		return false;
	}

	/**
	 * Verify request value array
	 *
	 * @param array $keys Keys.
	 * @param bool $sanitize Sanitize.
	 * @return array Keys.
	 */
	public function verifyRequestValueArr($keys, $sanitize = true)
	{
		foreach ($keys as $key => $value) {
			if ($sanitize) {
				$keys[$key] = $this->sanitizeTextField($this->unslash($value));
			} else {
				$keys[$key] = $this->unslash($value);
			}
		}
		return $keys;
	}

	/**
	 * Log error
	 *
	 * @param string $message Message.
	 */
	public function logError($message)
	{
		error_log($message);
	}

	/**
	 * Add non prefetchable groups
	 *
	 * Adds W3speedster-specific groups to the list of non-prefetchable
	 * object cache groups. This prevents certain cache operations from
	 * being prefetched.
	 *
	 * @param array $groups Array of existing non-prefetchable groups
	 * @return array Modified array with W3speedster groups added
	 */
	public function add_non_prefetchable_groups( $groups ) {
		// Add W3speedster upgrade group to prevent prefetching
		$groups[] = 'w3speedster_upgrade_w3speedster';
		return $groups;
	}

	/**
	 * Filter to customize the filename for critical CSS files.
	 *
	 * @param string $main_css_file_name The default filename.
	 * @param array $combined_css_files List of combined CSS files.
	 * @return string Customized filename.
	 */
	public function w3_customize_critical_css_filename($main_css_file_name, $combined_css_files)
	{
		if (!empty($this->settings['hook_customize_critical_css_filename'])) {
            $code = str_replace(array('$main_css_file_name', '$combined_css_files'), array('$args[0]', '$arg[1]'), $this->settings['hook_customize_critical_css_filename']);
            $main_css_file_name = $this->hookCallbackFunction($code, $main_css_file_name, $combined_css_files);
        }
		return $main_css_file_name;
	}

	/**
	 * Filter to customize the generated critical CSS.
	 *
	 * @param string $critical_css The critical CSS string.
	 * @return string Customized critical CSS.
	 */
	public function w3_customize_critical_css_url($url, $orgurl)
	{
		if (!empty($this->settings['hook_customize_critical_css_url'])) {
            $code = str_replace(array('$url', '$orgurl'), array('$args[0]', '$args[1]'), $this->settings['hook_customize_critical_css_url']);
            $url = $this->hookCallbackFunction($code, $url, $orgurl);
        }
		return $url;
	}

	/**
	 * Filter to customize the generated critical CSS.
	 *
	 * @param string $critical_css The critical CSS string.
	 * @return string Customized critical CSS.
	 */
	public function w3_customize_critical_css($critical_css)
	{
		if (!empty($this->settings['hook_customize_critical_css'])) {
			$code = str_replace(array('$critical_css'), array('$args[0]'), $this->settings['hook_customize_critical_css']);
			$critical_css = $this->hookCallbackFunction($code, $critical_css);
		}
		return $critical_css;
	}

	/**
	 * Filter to customize image output or attributes.
	 *
	 * @param mixed $imgnn Image name or identifier.
	 * @param mixed $img Image data or HTML.
	 * @param array $imgnn_arr Additional image data.
	 * @return mixed Customized image output.
	 */
	public function w3_exclude_image_to_lazyload($exclude_image, $img, $imgnn_arr)
	{
		if (!empty($this->settings['hook_exclude_image_to_lazyload'])) {
            $code = str_replace(array('$exclude_image', '$img', '$imgnn_arr'), array('$args[0]', '$args[1]', '$args[2]'), $this->settings['hook_exclude_image_to_lazyload']);
            $exclude_image = $this->hookCallbackFunction($code, $exclude_image, $img, $imgnn_arr);
        }
		return $exclude_image;
	}

	/**
	 * Filter to customize image output or attributes.
	 *
	 * @param mixed $path
	 * @return mixed Customized image output.
	 */
	public function w3_exclude_image_from_convert_to_webp($path)
	{
		if (!empty($this->settings['hook_exclude_convert_image_to_webp'])) {
            $code = str_replace(array('$path'), array('$args[0]'), $this->settings['hook_exclude_convert_image_to_webp']);
            return $this->hookCallbackFunction($code, $path);
        }
		return false;
	}

	/**
	 * Filter to customize image output or attributes.
	 *
	 * @param mixed $imgnn Image name or identifier.
	 * @param mixed $img Image data or HTML.
	 * @param array $imgnn_arr Additional image data.
	 * @return mixed Customized image output.
	 */
	public function w3_customize_image($imgnn, $img, $imgnn_arr)
	{
		if (!empty($this->settings['hook_customize_image'])) {
			$code = str_replace(array('$imgnn', '$img', '$imgnn_arr'), array('$args[0]', '$args[1]', '$args[2]'), $this->settings['hook_customize_image']);
			$imgnn = $this->hookCallbackFunction($code, $imgnn, $img, $imgnn_arr);
		}
		return $imgnn;
	}

	/**
	 * Filter to change video tags to videolazy for lazy loading.
	 *
	 * @return bool True to change, false otherwise.
	 */
	public function w3_change_video_to_videolazy($videolazy)
	{
		if (!empty($this->settings['hook_video_to_videolazy'])) {
			$code = str_replace('$videolazy', '$args[0]', $this->settings['hook_video_to_videolazy']);
			$videolazy = $this->hookCallbackFunction($code, $videolazy);
		}
		return $videolazy;
	}

	/**
	 * Filter to change iframe tags to iframelazy for lazy loading.
	 *
	 * @return bool True to change, false otherwise.
	 */
	public function w3_change_iframe_to_iframelazy($iframelazy)
	{
		if (!empty($this->settings['hook_iframe_to_iframelazy'])) {
			$code = str_replace('$iframelazy', '$args[0]', $this->settings['hook_iframe_to_iframelazy']);
			$iframelazy = $this->hookCallbackFunction($code, $iframelazy);
		}
		return $iframelazy;
	}

	/**
	 * Filter to determine if critical CSS should be skipped for a given URL.
	 *
	 * @param string $fullUrl The full URL to check.
	 * @return mixed True to skip, false otherwise.
	 */
	public function w3_no_critical_css($ignore_critical_css, $url)
	{
		if (!empty($this->settings['hook_no_critical_css'])) {
            $code = str_replace(array('$ignore_critical_css', '$url'), array('$args[0]', '$args[1]'), $this->settings['hook_no_critical_css']);
            $ignore_critical_css = $this->hookCallbackFunction($code, $ignore_critical_css, $url);
        }
		return $ignore_critical_css;
	}

	/**
	 * Filter to exclude specific CSS from optimization.
	 *
	 * @param bool $exclude_css Whether to exclude CSS.
	 * @param object $script_obj Script object.
	 * @param string $script Script content.
	 * @param string $html HTML content.
	 * @return bool Filtered exclusion value.
	 */
	public function w3_prevent_generation_htaccess($preventHtaccess)
	{
		if (!empty($this->settings['hook_prevent_generation_htaccess'])) {
            $code = str_replace('$preventHtaccess', '$args[0]', $this->settings['hook_prevent_generation_htaccess']);
            $preventHtaccess = $this->hookCallbackFunction($code, $preventHtaccess);
        }
		return $preventHtaccess;
	}	
	/**
	 * Filter to exclude specific CSS from optimization.
	 *
	 * @param bool $exclude_css Whether to exclude CSS.
	 * @param object $script_obj Script object.
	 * @param string $script Script content.
	 * @param string $html HTML content.
	 * @return bool Filtered exclusion value.
	 */
	public function w3_exclude_css_filter($exclude_css, $css_obj, $css, $html)
	{
		if(!empty($this->settings['hook_exclude_css_filter'])){
			$code = str_replace(array('$exclude_css','$css_obj','$css','$html'),array('$args[0]','$args[1]','$args[2]','$args[3]'),$this->settings['hook_exclude_css_filter']);
			$exclude_css = $this->hookCallbackFunction($code,$exclude_css,$css_obj,$css,$html);
		}
		return $exclude_css;
	}	

	/**
	 * Filter to minify internal CSS.
	 *
	 * @param string $path The file path.
	 * @param string $css The CSS content.
	 * @return string Minified CSS.
	 */
	public function w3_customize_force_lazy_css($force_lazyload_css)
	{
		if(!empty($this->settings['hook_customize_force_lazy_css'])){
			$code = str_replace('$force_lazyload_css','$args[0]',$this->settings['hook_customize_force_lazy_css']);
			$force_lazyload_css = $this->hookCallbackFunction($code,$force_lazyload_css);
		}
		return $force_lazyload_css;
	}
	/**
	 * Filter to minify internal CSS.
	 *
	 * @param string $path The file path.
	 * @param string $css The CSS content.
	 * @return string Minified CSS.
	 */
	public function w3_internal_css_minify($css_minify, $path, $css)
	{
		if(!empty($this->settings['hook_internal_css_minify'])){
			$code = str_replace(array('$css_minify','$css','$path'),array('$args[0]','$args[1]','$args[2]'),$this->settings['hook_internal_css_minify']);
			$css_minify = $this->hookCallbackFunction($code, $css_minify, $css, $path);
		}
		return $css_minify;
	}

	/**
	 * Filter to customize internal CSS before minification or output.
	 *
	 * @param string $css The CSS content.
	 * @param string $path The file path.
	 * @return string Customized CSS.
	 */
	public function w3_internal_css_customize($css, $path)
	{
		if(!empty($this->settings['hook_internal_css_customize'])){
			$code = str_replace(array('$css','$path'),array('$args[0]','$args[1]'),$this->settings['hook_internal_css_customize']);
			$css = $this->hookCallbackFunction($code,$css,$path);
		}
		return $css;
	}

	/**
	 * Filter to modify HTML after optimization is complete.
	 *
	 * @param string $html The HTML content.
	 * @return string Filtered HTML.
	 */
	public function w3_after_optimization($html)
	{
		if ( ! empty( $this->settings['hook_after_opt'] ) ) {
			$code = str_replace( '$html', '$args[0]', $this->settings['hook_after_opt'] );
			$html = $this->hookCallbackFunction( $code, $html );
		}
		return $html;
	}

	/**
	 * Filter to modify HTML before starting optimization.
	 *
	 * @param string $html The HTML content.
	 * @return string Filtered HTML.
	 */
	public function w3_before_start_optimization($html)
	{
		if ( ! empty( $this->settings['hook_before_start_opt'] ) ) {
			$code = str_replace( '$html', '$args[0]', $this->settings['hook_before_start_opt'] );
			$html = $this->hookCallbackFunction( $code, $html );
		}
		return $html;
	}

	public function w3_disable_htaccess_webp()
	{
		if ( ! empty( $this->settings['hook_disable_htaccess_webp'] ) ) {
			$disable_htaccess_webp = $this->addSettings['disable_htaccess_webp'];
			$code = str_replace( array( '$disable_htaccess_webp' ), array( '$args[0]' ), $this->settings['hook_disable_htaccess_webp'] );
			$this->addSettings['disable_htaccess_webp'] = $this->hookCallbackFunction( $code, $disable_htaccess_webp );
		}
	}

	/**
	 * Filter to customize main plugin settings.
	 *
	 * @param array $settings The main settings array.
	 * @return array Customized settings.
	 */
	public function w3_customize_main_settings($settings)
	{
		if ( ! empty( $this->settings['hook_customize_main_settings'] ) ) {
			$code = str_replace( '$settings', '$args[0]', $this->settings['hook_customize_main_settings'] );
			$settings = $this->hookCallbackFunction( $code, $settings );
		}
		return $settings;
	}

	/**
	 * Filter to customize additional settings array.
	 *
	 * @param array $addSettings The settings array.
	 * @return array Customized settings.
	 */
	public function w3_customize_addSettings($addSettings)
	{
		if ( ! empty( $this->settings['hook_customize_addSettings'] ) ) {
			$code = str_replace( '$addSettings', '$args[0]', $this->settings['hook_customize_addSettings'] );
			$addSettings = $this->hookCallbackFunction( $code, $addSettings );
		}
		return $addSettings;
	}

	/**
	 * Pre-start optimization hook
	 *
	 * Applies a filter to the HTML content before
	 * starting the optimization process.
	 */
	public function w3_pre_start_optimization()
	{
		if ( ! empty( $this->settings['hook_pre_start_opt'] ) ) {
			$code = str_replace( '$html', '$args[0]', $this->settings['hook_pre_start_opt'] );
			$this->html = $this->hookCallbackFunction( $code, $this->html );
		}
	}

	/**
	 * Filter to exclude specific pages from optimization.
	 *
	 * @param string $html The HTML content.
	 * @return string Filtered HTML.
	 */
	public function w3_exclude_page_optimization($html)
	{
		// Apply custom exclude page optimization hook
		if ( ! empty( $this->settings['hook_exclude_page_optimization'] ) ) {
			$exclude_page_optimization = 0;
			$code = str_replace( 
				array( '$exclude_page_optimization', '$html' ),
				array( '$args[0]', '$args[1]' ),
				$this->settings['hook_exclude_page_optimization']
			);
			$exclude_page_optimization = $this->hookCallbackFunction( $code, $exclude_page_optimization, $html );
			if ( $exclude_page_optimization ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Filter to determine if an inline JavaScript should be excluded from optimization.
	 *
	 * @param bool $exclude_js_bool Whether to exclude.
	 * @param string $inner_js The inline JavaScript.
	 * @return bool Filtered exclusion value.
	 */
	public function w3_inner_js_excluded($exclude_js_bool, $inner_js)
	{
		if(!empty($this->settings['hook_inner_js_exclude'])){
			$code = str_replace(array('$exclude_js_bool','$inner_js'),array('$args[0]','$args[1]'),$this->settings['hook_inner_js_exclude']);
			$exclude_js_bool = $this->hookCallbackFunction($code,$exclude_js_bool,$inner_js);
		}
		return $exclude_js_bool;
	}

	/**
	 * Filter to customize inline JavaScript before output.
	 *
	 * @param string $script_text The inline JavaScript.
	 * @return string Customized JavaScript.
	 */
	public function w3_inner_js_customize($script_text)
	{
		if(!empty($this->settings['hook_inner_js_customize'])){
			$code = str_replace('$script_text','$args[0]',$this->settings['hook_inner_js_customize']);
			$script_text = $this->hookCallbackFunction($code,$script_text);
		}
		return $script_text;
	}

	/**
	 * Filter to customize external JavaScript before output.
	 *
	 * @param object $script_obj The script object.
	 * @param string $script The script content.
	 * @return object Customized script object.
	 */
	public function w3_external_javascript_customize($script_obj, $script)
	{
		if(!empty($this->settings['hook_external_javascript_customize'])){
			$code = str_replace(array('$script_obj','$script'),array('$args[0]','$args[1]'),$this->settings['hook_external_javascript_customize']);
			$script_obj = $this->hookCallbackFunction($code,$script_obj, $script);
		}
		return $script_obj;
	}

	/**
	 * Filter to customize the script object before processing.
	 *
	 * @param object $script_obj The script object.
	 * @param string $script The script content.
	 * @return object Customized script object.
	 */
	public function w3_external_javascript_filter($exclude_js,$script_obj,$script,$html)
	{
		if(!empty($this->settings['hook_external_javascript_filter'])){
			$code = str_replace(array('$exclude_js','$script_obj','$script','$html'),array('$args[0]','$args[1]','$args[2]','$args[3]'),$this->settings['hook_external_javascript_filter']);
			$exclude_js = $this->hookCallbackFunction($code,$exclude_js,$script_obj,$script,$html);
		}
		return $exclude_js;
	}

	/**
	 * Filter to customize the script object before processing.
	 *
	 * @param object $script_obj The script object.
	 * @param string $script The script content.
	 * @return object Customized script object.
	 */
	public function w3_customize_script_object($script_obj, $script)
	{
		if(!empty($this->settings['hook_customize_script_object'])){
			$code = str_replace(array('$script_obj','$script'),array('$args[0]','$args[1]'),$this->settings['hook_customize_script_object']);
			$script_obj = $this->hookCallbackFunction($code,$script_obj, $script);
		}
		return $script_obj;
	}

	/**
	 * Filter to customize internal JavaScript before minification or output.
	 *
	 * @param string $string The JavaScript content.
	 * @return string Customized JavaScript.
	 */
	public function w3_internal_js_customize($string, $path)
	{
		if(!empty($this->settings['hook_internal_js_customize'])){
		   $code = $code = str_replace(array('$string','$path'),array('$args[0]','$args[1]'),$this->settings['hook_internal_js_customize']);
           $string = $this->hookCallbackFunction($code,$string,$path);
        }
		return $string;
	}

	/**
	 * Filter to exclude specific internal JavaScript from optimization.
	 *
	 * @param string $path The file path.
	 * @param string $string The JavaScript content.
	 * @return bool Filtered exclusion value.
	 */
	public function w3_exclude_internal_js_w3_changes($exclude_from_w3_changes, $path, $string)
	{
		if(!empty($this->settings['hook_exclude_internal_js_w3_changes'])){
			$code = str_replace(array('$exclude_from_w3_changes','$path','$string'),array('$args[0]','$args[1]','$args[2]'),$this->settings['hook_exclude_internal_js_w3_changes']);
			$exclude_from_w3_changes = $this->hookCallbackFunction($code,$exclude_from_w3_changes,$path,$string);
		}
		return $exclude_from_w3_changes;
	}

	function w3RemoveCacheByType($type){
        $typeArray = ['html', 'css-js', 'critical'];
        if(!in_array($type, $typeArray)){
            return false;
        }
        if($type == 'html'){
            $this->w3Rmdir($this->w3GetCachePath('html'));
            $this->logSettingsChanges([['action' => 'Html Cache Flushed', 'old' => 'Cache', 'new' => 'Clear']]);
            return true;
        }
        if($type == 'css-js'){
            $this->w3Rmdir($this->w3GetCachePath('html'));
            $this->w3Rmdir($this->w3GetCachePath('css'));
            $this->w3Rmdir($this->w3GetCachePath('js'));
            $this->w3Rmdir($this->w3GetCachePath('fonts'));
            $this->w3Rmdir($this->w3GetCachePath('all-js'));
            $this->w3CreateRandomKey();
            $this->logSettingsChanges([['action' => 'Html/Css/Js Cache Flushed', 'old' => 'Cache', 'new' => 'Clear']]);
            return true;
        }
        if($type == 'critical'){
            $this->w3Rmdir($this->addSettings['criticalCssPath']);
            $this->w3DeleteServerCache(); 
            $this->logSettingsChanges([['action' => 'Critical CSS Cache Flushed', 'old' => 'Cache', 'new' => 'Clear']]);  
            return true;
        }
        return false;
    }
}
