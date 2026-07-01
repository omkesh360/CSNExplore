<section id="import" class="tab-pane fade<?php echo $tab == 'import' ? ' active in' : ''; ?>">
	<div class="header w3d-flex gap20">
		<div class="heading_container">
			<h4 class="w3heading">
				<?php $admin->core->translate('Import / Export'); ?>
			</h4>
			<span class="w3info"><a
					href="https://w3speedster.com/w3speedster-documentation/"><?php $admin->core->translate('More info'); ?>?
				</a></span>
		</div>
		<div class="icon_container"> <img src="<?php $admin->core->esc_url_e( W3SPEEDSTER_URL . 'assets/images/import-export-icon.webp' ); ?>" alt="Import / Export"></div>
	</div>
	<hr>
	<div class="import_form">
		<label><?php $admin->core->translate('Import Settings'); ?><span class="w3info"></span><span
				class="w3info-display"><?php $admin->core->translate('Upload the exported file (.dat) from W3speedster'); ?></span></label>
		<div class="w3-import-row">
			<input form="import_form" type="file" id="w3_import_file" name="w3_import_file" accept=".dat,.txt,application/octet-stream">
			<input form="import_form" type="hidden" name="action" value="w3speedster_import_settings">
			<input form="import_form" type="hidden" name="_w3nonce"
			value="<?php $admin->core->esc_attr_e($admin->core->createSecureKey('w3_settings_import')); ?>">
			<button form="import_form" id="import_button" class="btn" type="button"><?php $admin->core->translate('Import File'); ?></button>
		</div>
	</div>
	<?php
	$export_setting = $result;
	$export_setting['license_key'] = '';
	$export_setting['is_activated'] = '';
	?>

	<hr>
	<div class="import_form">
		<label><?php $admin->core->translate('Export Settings'); ?><span class="w3info"></span><span
				class="w3info-display"><?php $admin->core->translate('Download a file with your plugin settings'); ?></span></label>
		<?php 
		$__w3_export_base = str_replace('admin-ajax.php','admin-post.php', $admin->core->getAjaxUrl());
		$__w3_export_url = $__w3_export_base . '?action=w3speedster_export_settings&_w3nonce=' . $admin->core->createSecureKey('w3_settings_export');
		?>
		<a class="btn" href="<?php $admin->core->esc_url_e($__w3_export_url); ?>"><?php $admin->core->translate('Download Export File'); ?></a>
	</div>
</section>