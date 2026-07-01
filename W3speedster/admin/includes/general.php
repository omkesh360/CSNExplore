<section id="general" class="tab-pane fade<?php echo empty($tab) || $tab == 'general' ? ' active in' : ''; ?>">
	<div class="header w3d-flex gap20">
		<div class="heading_container">
			<h4 class="w3heading">
				<?php $admin->core->translate('General Setting'); ?>
			</h4>
			<h4 class="w3_sub_heading">
				<?php $admin->core->translate('Optimization Level'); ?>
			</h4> <span class="w3info"><a
					href="https://w3speedster.com/w3speedster-documentation/#general_setting"><?php $admin->core->translate('More info'); ?>?
				</a></span>
		</div>
		<div class="icon_container">
			<img src="<?php $admin->core->esc_url_e( W3SPEEDSTER_URL . 'assets/images/general-setting-icon.webp' ); ?>">
		</div>
	</div>
	<hr>
	<div class="license_key w3d-flex gap20 <?php $admin->core->esc_attr_e($hidden_class); ?>">
		<label for="">
			<?php $admin->core->translate('License Key'); ?><span class="w3info"></span><span
				class="w3info-display">
				<?php $admin->core->translate('Activate key to get updates and access to all features of the plugin.'); ?>
			</span>
		</label>
		<div class="key w3d-flex">
			<input type="text" name="license_key"
				placeholder="<?php $admin->core->translate('Key'); ?>"
				value="<?php $admin->core->esc_attr_e($admin->core->getLicenseKey()); ?>"
				style="">
			<input type="hidden" name="w3ApiUrl"
				value="<?php if (!empty($result['w3ApiUrl'])) $admin->core->esc_attr_e($result['w3ApiUrl']); ?>">
			<input type="hidden" name="is_activated"
				value="<?php if (!empty($result['is_activated'])) $admin->core->esc_attr_e($result['is_activated']); ?>">
			<input type="hidden" name="_w3nonce"
				value="<?php $admin->core->esc_attr_e($admin->core->createSecureKey('w3_settings')); ?>">
			<input type="hidden" name="ws_action" value="cache">
			<?php if (!empty($result['license_key']) && !empty($result['is_activated'])) {
				?>
				<i class="fa fa-check-circle-o" aria-hidden="true"></i>
				<?php
			} else { ?>
				<div class="w3d-flex gap10">
					<button class="activate-key btn" type="button">
						<?php $admin->core->translate('Activate'); ?>
					</button>
					<div class="in-progress w3d-flex" id="verify-key-loader" style="display: none;">
						<img src="<?php $admin->core->esc_url_e( W3SPEEDSTER_URL . 'assets/images/loader-gif.gif' ); ?>" alt="loader"
							class="loader-img">
					</div>
				</div>
			<?php }
			?>
		</div>
	</div>
	<?php
	if($networkAdmin){ ?>
		<div class="manage-separately w3d-flex gap20">
			<label><?php $admin->core->translate('Manage Each Site Separately'); ?><span
					class="w3info"></span><span
					class="w3info-display"><?php $admin->core->translate('Enable this option to enter separate settings for each site. Plugin page will then be available in the backend of every site.'); ?></span></label>
			<div class="input_box">
				<label class="switch" for="manage-site-separately">
					<input type="checkbox" name="manage_site_separately" <?php if (!empty($result['manage_site_separately']) && $result['manage_site_separately'] == "on")
						echo "checked"; ?> id="manage-site-separately">
					<div class="checked"></div>
				</label>
			</div>
		</div>
	<?php } ?>
	<hr class="<?php $admin->core->esc_attr_e($hidden_class); ?>">
	<div class="main <?php $admin->core->esc_attr_e($hidden_class); ?>">
		<div class="turn_on_optimization <?php $admin->core->esc_attr_e($hidden_class); ?>">
			<div class="w3d-flex gap20">
				<label><?php $admin->core->translate('Turn ON optimization'); ?><span
						class="w3info"></span><span
						class="w3info-display"><?php $admin->core->translate('Site will start to optimize. All optimization settings will be applied.'); ?></span></label>
				<div class="input_box">
					<label class="switch" for="turn-on-optimization">
						<input type="checkbox" name="optimization_on" <?php if (!empty($result['optimization_on']) && $result['optimization_on'] == "on")
							echo "checked"; ?> id="turn-on-optimization">
						<div class="checked"></div>
					</label>
				</div>
			</div>
			<div class="w3d-flex gap20 <?php $admin->core->esc_attr_e($hidden_class); ?>">
				<label><?php $admin->core->translate('Optimize Pages with Query Parameters'); ?><span
						class="w3info"></span><span
						class="w3info-display"><?php $admin->core->translate('It will optimize pages with query parameters. Recommended only for servers with high performance.'); ?></span></label>
				<div class="input_box">
					<label class="switch" for="optimize-pages-with-query-parameters">
						<input type="checkbox" name="optimize_query_parameters"
							id="optimize-pages-with-query-parameters" <?php if (!empty($result['optimize_query_parameters']))
								echo "checked"; ?>>
						<div class="checked"></div>
					</label>
				</div>
			</div>
			<div class="w3d-flex gap20 <?php $admin->core->esc_attr_e($hidden_class); ?>">
				<label><?php $admin->core->translate('Optimize pages when User Logged In'); ?><span
						class="w3info"></span><span
						class="w3info-display"><?php $admin->core->translate('It will optimize pages when users are logged in. Recommended only for servers with high performance'); ?></span></label>
				<div class="input_box">
					<label class="switch" for="optimize-pages-when-user-logged-in">
						<input type="checkbox" name="optimize_user_logged_in"
							id="optimize-pages-when-user-logged-in" <?php if (!empty($result['optimize_user_logged_in']))
								echo "checked"; ?>>
						<div class="checked"></div>
					</label>
				</div>
			</div>
			<div class="w3d-flex gap20 <?php $admin->core->esc_attr_e($hidden_class); ?>">
				<label><?php $admin->core->translate('Fix INP Issues'); ?><span
						class="w3info"></span><span
						class="w3info-display"><?php $admin->core->translate('Enable to fix Interactive next paint issues appearing in googe page speed assessment test and/or google search console.'); ?></span></label>
				<div class="input_box">
					<label class="switch">
						<input type="checkbox" name="enable_inp" <?php if (!empty($result['enable_inp']) && $result['enable_inp'] == "on")
							echo "checked"; ?>
							id="enable-inp">
						<div class="checked"></div>
					</label>
				</div>
			</div>
		</div>
	</div>
	<hr class="<?php $admin->core->esc_attr_e($hidden_class); ?>">
	<div class="save-changes w3d-flex gap10">
		<input type="button" value="<?php $admin->core->translate('Save Changes'); ?>"
			class="btn hook_submit">
		<div class="in-progress w3d-flex save-changes-loader" style="display:none">
			<img src="<?php $admin->core->esc_url_e( W3SPEEDSTER_URL . 'assets/images/loader-gif.gif' ); ?>" alt="loader"
				class="loader-img">
		</div>
	</div>
</section>
