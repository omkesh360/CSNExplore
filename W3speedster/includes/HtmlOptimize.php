<?php
/**
 * HTML Optimization Class
 *
 * This class handles HTML optimization, caching, and performance improvements
 * for the W3speedster plugin. It extends JsOptimize to inherit JavaScript
 * optimization capabilities.
 *
 * @package W3speedster
 * @author W3speedster Team
 */

namespace W3speedster;

use W3speedster\JsOptimize;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * HTML Optimization Class
 *
 * Handles HTML content optimization, caching, and performance improvements.
 * Extends JsOptimize to inherit JavaScript optimization capabilities.
 */
class HtmlOptimize extends JsOptimize {

	/**
	 * Current page content type
	 *
	 * @var string
	 */
	public $current_page_content_type = '';

	/**
	 * Cache file path for HTML content
	 *
	 * @var string
	 */
	public $cacheFilePath = '';

	/**
	 * Cache message for debugging
	 *
	 * @var string
	 */
	public $cacheMsg = '';

	/**
	 * Main HTML optimization method
	 *
	 * This is the primary method that processes HTML content and applies
	 * various optimizations including minification, lazy loading, and caching.
	 *
	 * @param string $html The HTML content to optimize
	 * @return string The optimized HTML content
	 */
	public function w3Speedster( $html ) {
		// Store the HTML content
		$this->html = $html;
		
		// Get HTTP response code
		$this->statusCode = http_response_code();
		
		// Extract page title for optimization
		$this->addSettings['title'] = $this->w3GetTagsData( $this->html, '<title>', '</title>' );
		
		// Start performance timing
		$this->w3DebugTime( 'start optimization' );
		
		// Set cache file path
		$this->setCacheFilePath();
		
		// Apply pre-optimization hooks
		$this->applyPreOptimizationHooks();
		// Check if optimization should be skipped
		$w3NoOptimization = $this->w3NoOptimization();
		if ( $w3NoOptimization > 0 ) {
			// Handle no optimization cases
			$this->handleNoOptimization( $w3NoOptimization );
			return $this->html;
		}
		
		// Apply customization hooks
		$this->applyCustomizationHooks();
		
		// Clean script tags
		$this->html = $this->cleanScriptTags( $this->html );
		
		// Handle JavaScript optimization
		if ( ! empty( $this->settings['js'] ) ) {
			$this->w3CustomJsEnqueue();
		}
		
		// Apply before optimization hooks
		$this->applyBeforeOptimizationHooks();
		
		// Handle lazy loading for background images
		if ( ! empty( $this->settings['lazy_load'] ) ) {
			$this->lazyLoadBackgroundImage();
		}
		
		// Process all links and resources
		$allLinks = $this->processAllLinks();
		
		// Handle CSS optimization
		$this->handleCssOptimization($allLinks['style']);
		
		// Get insert link content
		$insertLink = $this->getW3contentsInsertLink( $allLinks );
		
		// Load Google Fonts
		$google_fonts = $this->w3LoadGoogleFonts();
		
		// Performance timing for JavaScript insertion
		$this->w3DebugTime( 'after javascript insertion' );
		
		// Insert critical CSS
		list( $criticalCssInsertion, $criticalReplace ) = $this->insertCriticalCss();
		// Handle custom CSS and font optimization
		$this->handleCustomCssAndFonts();
		
		// Generate preload HTML
		$preload_html = $this->w3PreloadResources();
		
		// Insert content in head section
		$this->insertHeadContent( $preload_html, $google_fonts, $insertLink );
		
		// Perform bulk string replacements
		$this->w3StrReplaceBulk();
		
		// Handle critical CSS insertion
		if ( $criticalCssInsertion ) {
			$this->handleCriticalCssInsertion( $criticalReplace );
		}
		
		// Insert Web Vitals logging script
		if ( ! empty( $this->settings['webvitals_logs'] ) ) {
			$this->w3InsertContentHead( $this->W3speedsterCoreWebVitalsScript(), 4 );
		}
		
		// Insert lazy load images script before closing body tag
		$this->insertLazyLoadScript();
		
		// Performance timing for W3 script
		$this->w3DebugTime( 'w3 script' );
		
		// Apply after optimization hooks
		$this->applyAfterOptimizationHooks();
		
		// Handle HTML caching
		if ( isset( $this->settings['html_caching'] ) && $this->settings['html_caching'] == 'on' ) {
			$this->w3SpeedsterCreateHTMLCacheFile();
		}
		
		// Enqueue server tasks
		$this->w3EnqueTaskOnServer();
		
		// Final performance timing
		$this->w3DebugTime( 'before final output' );
		
		return $this->html;
	}

	/**
	 * Check if optimization should be skipped
	 *
	 * Determines whether the current page should be optimized based on
	 * various conditions like user status, page type, and settings.
	 *
	 * @return int Optimization status code:
	 *             0 = Optimize normally
	 *             1 = Skip optimization
	 *             2 = Skip optimization and remove no-optimize tags
	 */
	public function w3NoOptimization() {
		// Check for original URL or missing body tag
		if ( ! empty( $this->addSettings['w3_get']['orgurl'] ) || strpos( $this->html, '<body' ) === false ) {
			return 2;
		}
		
		// Check if page is ignored
		if ( $this->ignored() ) {
			return 1;
		}
		
		// Check if URL is allowed for optimization
		if ( ! $this->w3IsUrlAllowedForOptimization() ) {
			return 1;
		}
		
		// Check if it's an AMP endpoint
		if ( $this->isAmpEndpoint() ) {
			return 1;
		}
		
		// Check header conditions
		if ( $this->w3HeaderCheck() ) {
			return 1;
		}
		
		// Check if optimization is enabled
		if ( empty( $this->settings['optimization_on'] ) && 
			 empty( $this->addSettings['w3_get']['w3_get_css_post_type'] ) && 
			 empty( $this->addSettings['w3_get']['tester'] ) && 
			 empty( $this->addSettings['w3_get']['testing'] ) ) {
			return true;
		}
		
		// Check custom exclude function
		if ( function_exists( 'w3speedup_exclude_page_optimization' ) && w3speedup_exclude_page_optimization( $this->html ) ) {
			return true;
		}
		
		// Check if user is logged in and optimization is disabled for logged-in users
		if ( empty( $this->settings['optimize_user_logged_in'] ) && $this->w3UserLoggedIn() ) {
			return true;
		}
		
		// Check query parameters
		if ( empty( $this->settings['optimize_query_parameters'] ) && 
			 $this->addSettings['full_url'] != $this->addSettings['fullUrlWithoutParam'] && 
			 empty( $this->addSettings['w3_get']['tester'] ) && empty( $this->addSettings['w3_get']['w3_crawler'] ) ) {
			return 2;
		}
		
		// Check excluded pages
		if ( ! empty( $this->settings['exclude_pages_from_optimization'] ) && 
			 $this->w3CheckIfPageExcluded( $this->settings['exclude_pages_from_optimization'] ) ) {
			return 1;
		}
		
		// Check if it's a 404 page
		if ( $this->is_404() ) {
			return 1;
		}
		
		// Check admin user status
		if ( $this->checkAdminUser() ) {
			return 1;
		}
		
		if ( method_exists($this, 'w3_exclude_page_optimization' ) && $this->w3_exclude_page_optimization( $this->html ) ) {
			return 1;
		}
		
		return 0;
	}

	/**
	 * Start optimization callback
	 *
	 * Initiates the output buffering for HTML optimization.
	 */
	public function w3StartOptimizationCallback() {
		//ob_start( array( $this, 'w3Speedster' ) );
		if (!isset($GLOBALS['w3speedster_ob_started'])) {
			$GLOBALS['w3speedster_ob_started'] = true;
			ob_start(array( $this, 'w3Speedster' ));
		}
	}

	/**
	 * End output buffer flush
	 *
	 * Safely ends the output buffer and flushes content.
	 */
	public function w3ObEndFlush() {
		if ( ob_get_level() != 0 ) {
			ob_end_flush();
		}
	}

	/**
	 * Set cache file path
	 *
	 * Determines the appropriate cache file path based on user agent
	 * and current URL for mobile/desktop optimization.
	 */
	public function setCacheFilePath() {
		$userAgent = $this->addSettings['HTTP_USER_AGENT'];
		$isMobile = $this->w3speedsterIsMobileDevice( $userAgent ) ? 1 : 0;
		$url = $this->addSettings['full_url'];
		$this->cacheFilePath = $this->w3GetHtmlCacheFilePath( $url, $isMobile );
	}

	/**
	 * Apply pre-optimization hooks
	 *
	 * Executes hooks and functions that run before optimization begins.
	 *
	 * @param string $html The HTML content
	 */
	private function applyPreOptimizationHooks() {
		// Apply custom pre-optimization function
		if ( function_exists( 'w3speedup_pre_start_optimization' ) ) {
			$this->html = w3speedup_pre_start_optimization( $this->html );
		}
		
		// Apply custom pre-optimization hook
		if(method_exists($this, 'w3_pre_start_optimization')){
			$this->w3_pre_start_optimization();
		}
	}

	/**
	 * Handle cases where optimization is skipped
	 *
	 * @param int $w3NoOptimization The optimization status code
	 */
	private function handleNoOptimization( $w3NoOptimization ) {
		
		if ( $w3NoOptimization == 1 ) {
			$this->w3RemoveNoOptimizePageIfExists();
		}
		if ( ! empty( $this->settings['html_caching'] ) ) {
			$this->w3SpeedsterCreateHTMLCacheFile();
		}
	}

	/**
	 * Apply customization hooks
	 *
	 * Executes hooks for customizing addSettings and main settings.
	 */
	private function applyCustomizationHooks() {
		// Customize addSettings
		if(method_exists($this, 'w3_customize_addSettings')){
			$this->addSettings = $this->w3_customize_addSettings($this->addSettings);
		}
		
		// Apply custom addSettings function
		if ( function_exists( 'w3speedup_customize_addSettings' ) ) {
			$this->addSettings = w3speedup_customize_addSettings( $this->addSettings );
		}
		
		// Apply custom main settings function
		if ( function_exists( 'w3speedup_customize_main_settings' ) ) {
			$this->settings = w3speedup_customize_main_settings( $this->settings );
		}
		
		// Apply custom main settings hook
		if(method_exists($this, 'w3_customize_main_settings')){
			$this->settings = $this->w3_customize_main_settings($this->settings);
		}
		
		// Handle htaccess webp disable setting
		$this->addSettings['disable_htaccess_webp'] = function_exists( 'w3_disable_htaccess_wepb' ) ? w3_disable_htaccess_wepb() : 1;
		
		// Apply custom htaccess webp hook
		if(method_exists($this, 'w3_disable_htaccess_webp')){
			$this->w3_disable_htaccess_webp();
		}
	}

	/**
	 * Apply before optimization hooks
	 *
	 * @param string $html The HTML content
	 */
	private function applyBeforeOptimizationHooks() {
		// Apply custom before optimization function
		if ( function_exists( 'w3speedup_before_start_optimization' ) ) {
			$this->html = w3speedup_before_start_optimization( $this->html );
		}
		
		// Apply custom before optimization hook
		if(method_exists($this, 'w3_before_start_optimization')){
			$this->html = $this->w3_before_start_optimization($this->html);
		}
	}

	/**
	 * Process all links and resources
	 *
	 * Handles the processing of all links including scripts, stylesheets,
	 * images, and other resources for optimization.
	 */
	private function processAllLinks() {
		$this->w3DebugTime( 'before create all links' );
		
		// Define lazy load elements
		$lazyload = array( 'script', 'link', 'img', 'url' );
		
		// Add SVG to lazy load if enabled
		if ( ! empty( $this->settings['inlineToUrlSVG'] ) ) {
			$lazyload[] = 'svg';
		}
		
		// Add iframe to lazy load if enabled
		if ( ! empty( $this->settings['lazy_load_iframe'] ) ) {
			$lazyload[] = 'iframe';
		}
		
		// Add video to lazy load if enabled
		if ( ! empty( $this->settings['lazy_load_video'] ) ) {
			$lazyload[] = 'video';
		}
		
		// Add audio to lazy load if enabled
		if ( ! empty( $this->settings['lazy_load_audio'] ) ) {
			$lazyload[] = 'audio';
		}
		
		// Parse all links
		$this->w3DebugTime( 'parse all links' );
		$allLinks = $this->w3SetAllLinks( $this->html, $lazyload );
		$this->w3DebugTime( 'after create all links' );
		
		// Minify scripts if present
		if ( ! empty( $allLinks['script'] ) ) {
			$this->minify( $allLinks['script'] );
		}
		$this->w3DebugTime( 'minify script' );
		
		$this->w3DebugTime( 'lazyload images' );
		
		// Minify CSS
		$this->minifyCss( $allLinks['link'] );
		$this->w3DataBgLoadCss = $this->w3GetDataBgCssFromCritical();
		$this->preloadW3ImageFromCritical();
		$this->w3DebugTime( 'minify css' );
		// Apply lazy loading to various elements
		$this->lazyload( array(
			'iframe'  => $allLinks['iframe'],
			'video'   => $allLinks['video'],
			'audio'   => $allLinks['audio'],
			'img'     => $allLinks['img'],
			'picture' => $allLinks['picture'],
			'url'     => $allLinks['url'],
			'svg'     => $allLinks['svg']
		) );
		return $allLinks;
	}

	/**
	 * Handle CSS optimization
	 *
	 * Processes CSS optimization including critical CSS and custom CSS.
	 */
	private function handleCssOptimization($styleLinks) {
		// Handle style tag loading in head
		if ( ! empty( $this->settings['load_style_tag_in_head'] ) ) {
			$this->loadStyleTagInHead( $styleLinks );
		}
	}

	/**
	 * Handle custom CSS and fonts
	 *
	 * Processes custom CSS and optimizes font loading.
	 */
	private function handleCustomCssAndFonts() {
		if ( ! empty( $this->settings['custom_css'] ) ) {
			$fontfaces = $this->w3GetTagsData( $this->settings['custom_css'], '@font-face', '}' );
			$preferredFormats = array( 'woff2', 'woff', 'ttf', 'eot', 'svg' );
			
			foreach ( $fontfaces as $fontface ) {
				$urls = $this->w3GetTagsData( $fontface, 'url(', ')' );
				$fontAdded = false;
				$fontUrl = '';
				
				foreach ( $preferredFormats as $format ) {
					foreach ( $urls as $url ) {
						$url = $this->replaceUrlBrackets( $url );
						$fontUrl = $url = trim( $url );
						
						if ( stripos( $url, $format ) !== false ) {
							$this->addSettings['preload_resources']['font'][] = $url;
							$fontAdded = true;
							break 2;
						}
					}
				}
				
				if ( ! $fontAdded && count( $urls ) ) {
					$this->addSettings['preload_resources']['font'][] = $fontUrl;
				}
			}
		}
	}

	/**
	 * Insert content in head section
	 *
	 * @param string $preload_html Preload HTML content
	 * @param string $google_fonts Google Fonts content
	 * @param string $insertLink Insert link content
	 */
	private function insertHeadContent( $preload_html, $google_fonts, $insertLink ) {
		$head_content = $preload_html . 
					   '<style id="w3-bg-load">' . $this->excludeLazyLoadBackgroundImages() . ' video[data-class="LazyLoad"]{opacity: 0;} '.$this->w3ImageRatioCss().'</style>' .
					   $google_fonts .
					   '<script>' . $this->w3LazyLoadJavascript() . '</script>';
		
		$this->w3InsertContentHead( $head_content, 2, $insertLink );
	}

	/**
	 * Handle critical CSS insertion
	 *
	 * @param array $criticalReplace Critical CSS replacement data
	 */
	private function handleCriticalCssInsertion( $criticalReplace ) {
		$this->w3InsertContentHead( "\n" . '{{main_w3_critical_css}}', 2, '<style id="w3-bg-load">' );
		$this->html = str_replace( $criticalReplace[0], $criticalReplace[1], $this->html );
	}

	/**
	 * Insert lazy load script
	 *
	 * Inserts the lazy loading JavaScript before the closing body tag.
	 */
	private function insertLazyLoadScript() {
		$position = strrpos( $this->html, '</body>' );
		if ( $position ) {
			$this->html = substr_replace( 
				$this->html, 
				'<script>' . $this->w3LazyLoadImages() . '</script>', 
				$position, 
				0 
			);
		} else {
			$this->html .= '<script>' . $this->w3LazyLoadImages() . '</script>';
		}
	}

	/**
	 * Apply after optimization hooks
	 *
	 * @param string $html The HTML content
	 */
	private function applyAfterOptimizationHooks() {
		// Apply custom after optimization function
		if ( function_exists( 'w3speedup_after_optimization' ) ) {
			$this->html = w3speedup_after_optimization( $this->html );
		}
		
		// Apply custom after optimization hook
		if(method_exists($this, 'w3_after_optimization')){
			$this->html = $this->w3_after_optimization( $this->html );
		}
	}
}
