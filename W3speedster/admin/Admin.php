<?php
$admin = $w3admin;
$result = $admin->settings;
if(empty($result['by_serve_cache_file'])){
	$result['by_serve_cache_file'] = $admin->addSettings['serverType'] == 'apache' ? 'htaccess' : 'advanceCache';
}
$tab = !empty($_GET['tab']) ? $_GET['tab'] : '';
$networkAdmin = $admin->addSettings['is_multisite_networkadmin'] ? 1 : 0;
$hidden_class = !empty($result['manage_site_separately']) && $admin->addSettings['is_multisite_networkadmin'] ? 'tr-hidden' : '';
$w3FolderErrors = $admin->checkW3FolderPermissionErrors();
$result['ai-optimization'] = !empty($result['ai-optimization']) ? ($admin->checkAiOptimizationEnabled() ? 'on' : '') : '';
?>
<script>
var adminUrl = "<?php echo $admin->getAjaxUrl(); ?>";
var secureKey = "<?php echo $admin->createSecureKey("hook_callback"); ?>";
</script>
<div class="w3-toast-container"></div>
<main class="admin-w3speedster">
	<div class="top_panel_container">
		<div class="top_panel d-none">
			<div class="logo_container">
				<img class="logo" src="<?php echo W3SPEEDSTER_URL; ?>assets/images/w3-logo.png">
			</div>

			<div class="support_section">
				<div class="right_section">
					<div class="doc w3d-flex gap10">
						<p class="m-0"><i class="fa fa-file-text" aria-hidden="true"></i></p>
						<p class="m-0 text-center w3text-white">
							<?php $admin->translate('Need help or have question'); ?><br><a
								href="https://w3speedster.com/w3speedster-documentation/"
								target="_blank"><?php $admin->translate('Check our documentation'); ?></a>
						</p>

					</div>

				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="tab-panel col-md-2">
			<div class="mobile_toggle d-none">
				<button type="button">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512">
						<path fill="#fff"
							d="M246.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-128-128c-9.2-9.2-22.9-11.9-34.9-6.9S63.9 115 63.9 128v256c0 12.9 7.8 24.6 19.8 29.6s25.7 2.2 34.9-6.9l128-128z" />
					</svg>
				</button>
			</div>
			<div class="logo_container">
				<img class="logo" src="<?php echo W3SPEEDSTER_URL; ?>assets/images/w3-logo.png">
			</div>
			<?php include 'includes/tabs.php'; ?>
			<div class="support_section">
				<a class="doc btn" href="https://w3speedster.com/w3speedster-documentation/"
					target="_blank"><?php $admin->translate('Documentation'); ?> <i
						class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
				<a class="contact btn"
					href="https://w3speedster.com/contact-us/"><?php $admin->translate('Contact Us'); ?> <i
						class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
			</div>
		</div>

		<form method="post" class="main-form">
			<?php $admin->userProfileData(); ?>
			<div class="tab-content col-md-10">
				<?php if(!empty($w3FolderErrors)) :?>
					<div style='padding-top:10px'>
						<div class="w3d-flex gap10 error-div-main">
							<h3 class="error_heading">Permission Denied | Unable to create the following path: <br>➝ <?= implode("<br>➝ ",$w3FolderErrors) ?></h3>
							<button type="button" class="error_close_btn">
								<svg width="25" height="25" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg">
									<path d="m19.41 18 8.29-8.29a1 1 0 0 0-1.41-1.41L18 16.59l-8.29-8.3a1 1 0 0 0-1.42 1.42l8.3 8.29-8.3 8.29A1 1 0 1 0 9.7 27.7l8.3-8.29 8.29 8.29a1 1 0 0 0 1.41-1.41Z"></path>
								</svg>
							</button>
						</div>
					</div>
				<?php endif; ?>
				<?php include 'includes/general.php'; ?>
				<?php include 'includes/cdn.php'; ?>
				<?php include 'includes/css.php'; ?>
				<?php include 'includes/js.php'; ?>
				<?php include 'includes/exclusions.php'; ?>
				<?php include 'includes/w3-custom-code.php'; ?>
				<?php include 'includes/cache.php'; ?>
				<?php include 'includes/hooks.php'; ?>
				<?php include 'includes/webvital-logs.php'; ?>
				<?php include 'includes/html-cache.php'; ?>
				<?php include 'includes/opt-img.php'; ?>
				<?php include 'includes/import-export.php'; ?>
				<?php include 'includes/change-log.php'; ?>
				<?php include 'includes/optimize-with-ai.php'; ?>
				<?php $changePassFile = __DIR__ . '/includes/change-profile-settings.php';if(file_exists($changePassFile)) include_once($changePassFile);?>
			</div>
		</form>
		<form id="import_form" method="post" enctype="multipart/form-data">
		</form>
		<?php $admin->w3changeProfilesettings() ?>
	</div>
	<script>
		<?php $errors = $admin->w3GetErrors(); foreach ($errors as $value) : ?>
			w3ShowToast('<?php echo $value['type'] ?? "info";?>', '<?php echo $value['message'] ?? "Something Went Wrong";?>');
		<?php endforeach; $admin->w3FlushErrors();?>
	</script>
</main>
<?php include 'includes/code-editor.php';