<?php
namespace W3speedster;

class W3AdminInit extends W3speedster
{
	/**
	 * Core instance
	 *
	 * @var \W3speedster\Core
	 */
	public $core;

	function launch()
	{
		if (!empty($_POST['password_change'])) {
			$this->w3changeprofile();
		}
		if (!empty($_POST['import_text']) && isset($_POST['_w3nonce']) && $this->checkSecurityKey($_POST['_w3nonce'], 'w3_settings_import')) {
			$this->importData($_POST['import_text']);
		}
		if (!empty($this->addSettings['w3_get']['page']) && $this->addSettings['w3_get']['page'] == 'w3_speedster' && isset($_POST['_w3nonce'])) {
			$this->w3SaveOptions();
		}
		if (!empty($this->addSettings['w3_get']['page']) && $this->addSettings['w3_get']['page'] == 'w3_speedster') {
			$this->loadAdminPanel();
		}
	}

	function w3CheckLicenseKey(){
		$res= $this->w3speedsterValidateLicenseKey();
		$response = !empty($res) ? json_decode($res) : array();
		if(!empty($response[0]) && $response[0] == 'fail' && strpos($response[1],'could not verify-1') !== false){
			$this->w3UpdateOption('w3_key_log',$this->w3JsonEncode($response),'no');
			$settings = $this->w3GetOption( 'w3_speedup_option', true );
			$settings['is_activated'] = '';
			$this->w3UpdateOption('w3_speedup_option', $settings, 'no');
		}
	}

	function w3SaveOptions() {
		if (isset($_POST['ws_action']) && $_POST['ws_action'] == 'cache') {
			unset($_POST['ws_action'], $_POST['temp_input']);
			$keysToCheck = array('preload_resources', 'exclude_lazy_load', 'exclude_pages_from_optimization', 'exclude_css', 'force_lazyload_css', 'load_style_tag_in_head','exclude_page_from_load_combined_css','exclude_both_javascript','force_lazy_load_inner_javascript','exclude_page_from_load_combined_js','load_script_tag_in_url','exclude_url_html_cache','exclude_url_exclusions_html_cache');
			$oldSettings = $this->settings;
			$newSettings = [];
			foreach ($_POST as $key => $value) {
				if (in_array($key, $keysToCheck)) {
					if($key === "preload_resources"){
						$value  = str_replace('####', 'https', $value);
						$value  = str_replace('###', 'http', $value);
					}
					$newSettings[$key] = implode("\r\n", $value);
				} else {
					$newSettings[$key] = $value;
				}
			}
			$newSettings = $this->w3ReplaceBackslashes($newSettings);
			if (empty($newSettings['license_key']) || (!empty($oldSettings['license_key']) && strpos($oldSettings['license_key'] ?? '', 'w3demo') === false && $oldSettings['license_key'] !== $newSettings['license_key'])) {
				$newSettings['is_activated'] = '';
			}	
			$changes = [];
			foreach ($oldSettings as $key => $value) {
				if (!array_key_exists($key, $newSettings)) {
					$newSettings[$key] = '';
				}
			}

			$htmlCacheExcludeKeys = ['html_caching', 'enable_loggedin_user_caching', 'by_serve_cache_file', 'enable_caching_get_para', 'html_caching_expiry_time', 'lbc', 'gzip', 'optimize_user_logged_in'];

			$clearCache = false;
			foreach ($newSettings as $key => $value) {
				if ((!isset($oldSettings[$key]) || $oldSettings[$key] != $value) && $key != "_w3nonce") {
					$action = empty($this->getActionHeading($key)) ? $key :  $this->getActionHeading($key);
					$changes[] = [
						'action' => $action,
						'new' => !empty($value) ? $value : 'Not Set',
						'old' => !empty($oldSettings[$key]) ? $oldSettings[$key] : 'Not Set',
					];
					if(!in_array($key, $htmlCacheExcludeKeys)){
						$clearCache = true;
					}
				}
			}
			if (!empty($changes)) {
				if($clearCache){
					$this->w3RemoveCacheFilesHourlyEventCallback('html');
				}
				$this->logSettingsChanges($changes);
			}

			if(!empty($newSettings['html_caching']) && !empty($newSettings['by_serve_cache_file']) && $newSettings['by_serve_cache_file'] == 'htaccess' && (empty($oldSettings['by_serve_cache_file']) || $oldSettings['by_serve_cache_file'] != 'htaccess' || empty($oldSettings['html_caching']))){
				$this->htaccessModifyKeys['html_cache'] = 1;
			}
			if(!empty($newSettings['gzip']) && (empty($oldSettings['gzip']) || $oldSettings['gzip'] != $newSettings['gzip'])){
				$this->htaccessModifyKeys['gzip'] = 1;
			}
			if(!empty($newSettings['lbc']) && (empty($oldSettings['lbc']) || $oldSettings['lbc'] != $newSettings['lbc'])){
				$this->htaccessModifyKeys['lbc'] = 1;
			}
			if((!empty($newSettings['webp_png']) && empty($oldSettings['webp_png'])) || (!empty($newSettings['webp_jpg']) && empty($oldSettings['webp_jpg']))){
				$this->htaccessModifyKeys['webp'] = 1;
			}

			if(($newSettings['license_key'] ?? '') != ($oldSettings['license_key'] ?? '')){
				$this->w3TruncateSiteUrlsTable();
			}
			$this->w3UpdateOption('w3_speedup_option', $newSettings, 'no');
			$this->settings = $this->w3GetOption('w3_speedup_option', true);
			$this->checkHtmlCacheSettings();
			$this->pageReload();
		}
	}

	function loadAdminPanel(){
		$admin = $this;
		$result = $admin->settings;
		$this->core = $this;
		require_once W3SPEEDSTER_PATH . '/admin/index.php';
		exit;
	}
}
