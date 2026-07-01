<section id="htmlCache" class="tab-pane fade<?php echo $tab == 'htmlCache' ? ' active in' : ''; ?>">
	<div class="header w3d-flex gap20">
		<div class="heading_container">
			<h4 class="w3heading"><?php $admin->core->translate('HTML Caches'); ?>
			</h4>
			<span class="w3info"><a
					href="https://w3speedster.com/w3speedster-documentation/"><?php $admin->core->translate('More info'); ?>?
				</a></span>
		</div>
		<div class="icon_container"> <img
				src="<?php $admin->core->esc_url_e( W3SPEEDSTER_URL . 'assets/images/html_caches-icon1.webp' ); ?>"></div>
	</div>
	<hr>
	<?php
	$admin->core->checkAdvCacheFileExists();
	?>

	<div class="html-cache-main">
		<div class="w3d-flex gap20 <?php $admin->core->esc_attr_e($hidden_class); ?>">
			<label><?php $admin->core->translate('Enable HTML Caching'); ?><span class="w3info"></span><span
					class="w3info-display"><?php $admin->core->translate('Enable to on html caching'); ?></span></label>
			<div class="input_box">
				<label class="switch" for="enable-html-caching">
					<input type="checkbox" name="html_caching" <?php if (!empty($result['html_caching']) && $result['html_caching'] == "on")
						echo "checked"; ?> id="enable-html-caching">
					<div class="checked"></div>
				</label>
			</div>
		</div>
		<div class="w3d-flex gap20 <?php $admin->core->esc_attr_e($hidden_class); ?>">
			<label><?php $admin->core->translate('Enable caching for logged in user'); ?><span
					class="w3info"></span><span
					class="w3info-display"><?php $admin->core->translate('Enable caching for logged in user'); ?></span></label>
			<div class="input_box">
				<label class="switch" for="enable-caching-loggedin-user">
					<input type="checkbox" name="enable_loggedin_user_caching" <?php if (!empty($result['enable_loggedin_user_caching']) && $result['enable_loggedin_user_caching'] == "on")
						echo "checked"; ?>
						id="enable-caching-loggedin-user">
					<div class="checked"></div>
				</label>
			</div>
		</div>
		<div class="w3d-flex gap20 <?php $admin->core->esc_attr_e($hidden_class); ?>">
			<label><?php $admin->core->translate('Serve html cache file by'); ?><span
					class="w3info"></span><span
					class="w3info-display"><?php $admin->core->translate('Check method for serve cache html file'); ?></span></label>
			<div class="input_box w3d-flex gap10">
				<label class="switch" for="htaccess">
					<input value="htaccess" type="radio" name="by_serve_cache_file" <?php if (empty($result['by_serve_cache_file']) || $result['by_serve_cache_file'] == "htaccess")
						echo "checked"; ?> id="htaccess">
					<div class="checked"></div>
				</label>
				<span><?php $admin->core->translate('Htaccess'); ?></span>
			</div>
			<div class="input_box w3d-flex gap10">
				<label class="switch" for="advanceCache">
					<input value="advanceCache" type="radio" name="by_serve_cache_file" <?php if (!empty($result['by_serve_cache_file']) && $result['by_serve_cache_file'] == "advanceCache")
						echo "checked"; ?> id="advanceCache">
					<div class="checked"></div>
				</label>
				<span><?php $admin->core->translate('PHP Cache'); ?></span>
			</div>

		</div>
		<div class="w3d-flex gap20 <?php $admin->core->esc_attr_e($hidden_class); ?>">
			<label><?php $admin->core->translate('Enable caching page with GET parameters'); ?><span
					class="w3info"></span><span
					class="w3info-display"><?php $admin->core->translate('Enable caching page with GET parameters'); ?></span></label>
			<div class="input_box">
				<label class="switch" for="enable-caching-page-get-para">
					<input type="checkbox" name="enable_caching_get_para" <?php if (!empty($result['enable_caching_get_para']) && $result['enable_caching_get_para'] == "on")
						echo "checked"; ?>
						id="enable-caching-page-get-para">
					<div class="checked"></div>
				</label>
			</div>
		</div>
		<div class="w3d-flex gap20 <?php $admin->core->esc_attr_e($hidden_class); ?>">
			<label><?php $admin->core->translate('Cache Expiry Time'); ?><span class="w3info"></span><span
					class="w3info-display"><?php $admin->core->translate('Input an time for cache expiry default time is 3600(1 hour)'); ?></span></label>
			<div class="input_box">
				<label class="html-cache-expiry w3d-flex" for="html-cache-expiry-time">
					<input type="text" name="html_caching_expiry_time"
						value="<?php if (!empty($result['html_caching_expiry_time'])) $admin->core->esc_attr_e($result['html_caching_expiry_time']); else $admin->core->esc_attr_e('3600'); ?>"
						id="html-cache-expiry-time" style="max-width:80px;"><small>&nbsp;
						<?php $admin->core->translate('*Time delay in seconds'); ?></small>
					<div class="checked"></div>
				</label>
			</div>
		</div>
		<div class="w3d-flex gap20 <?php $admin->core->esc_attr_e($hidden_class); ?>">
			<label><?php $admin->core->translate('Enable leverage browsing cache'); ?><span
					class="w3info"></span><span
					class="w3info-display"><?php $admin->core->translate('Enable to turn on leverage browsing cache.'); ?></span></label>
			<div class="input_box">
				<label class="switch" for="enable-leverage-browsing-cache">
					<input type="checkbox" name="lbc" id="enable-leverage-browsing-cache" <?php if (!empty($result['lbc']) && $result['lbc'] == "on")
						echo "checked"; ?>>
					<div class="checked"></div>
				</label>
			</div>
		</div>
		<div class="w3d-flex gap20 <?php $admin->core->esc_attr_e($hidden_class); ?>">
			<label><?php $admin->core->translate('Enable Gzip compression'); ?><span
					class="w3info"></span><span
					class="w3info-display"><?php $admin->core->translate('Enable to turn on Gzip compresssion.'); ?></span></label>
			<div class="input_box">
				<label class="switch" for="enable-gzip-compression">
					<input type="checkbox" name="gzip" <?php if (!empty($result['gzip']) && $result['gzip'] == "on")
						echo "checked"; ?> id="enable-gzip-compression">
					<div class="checked"></div>
				</label>
			</div>
		</div>
			<div class="w3d-flex gap20 <?php $admin->core->esc_attr_e($hidden_class); ?>">
			<label><?php $admin->core->translate('Remove query parameters'); ?><span
					class="w3info"></span><span
					class="w3info-display"><?php $admin->core->translate('Enable to remove query parameters from resources.'); ?></span></label>
			<div class="input_box">
				<label class="switch" for="remove-query-parameters">
					<input type="checkbox" name="remquery" <?php if (!empty($result['remquery']) && $result['remquery'] == "on")
						echo "checked"; ?> id="remove-query-parameters">
					<div class="checked"></div>
				</label>
			</div>
		</div>
		<hr>
		<div class="cdn_resources <?php $admin->core->esc_attr_e($hidden_class); ?>">
			<div class="w3d-flex gap20 w3align-item-baseline">
				<label for="cache_path"><?php $admin->core->translate('Cache Path'); ?><span
						class="w3info"></span><span
						class="w3info-display"><?php $admin->core->translate('Enter path where cache can be stored. Leave empty for default path'); ?></span></label>
				<div class="input_box">
					<div class="cdn_input_box">
						<input type="text" name="cache_path"
							placeholder="<?php $admin->core->translate('Please Enter full cache path'); ?>"
							value="<?php if (!empty($result['cache_path'])) $admin->core->esc_attr_e($result['cache_path']); else $admin->core->esc_attr_e(''); ?>"
							id="cache_path"
							placeholder="<?php $admin->core->translate('Please Enter full cache path'); ?>">
						<small
							class="w3d-block"><?php $admin->core->translate('Default cache path:'); ?>
							<?php $admin->core->esc_html_e($admin->core->addSettings['content_path'] . '/cache'); ?>
						</small>
					</div>
				</div>

			</div>
		</div>
		<hr>
		<div class="save-changes w3d-flex gap10">
			<input type="button" value="<?php $admin->core->translate('Save Changes'); ?>" class="btn hook_submit">
			<div class="in-progress w3d-flex save-changes-loader" style="display:none">
				<img src="<?php $admin->core->esc_url_e( W3SPEEDSTER_URL . 'assets/images/loader-gif.gif' ); ?>" alt="loader"
					class="loader-img">
			</div>
		</div>
	</div>

</section>