<section id="js" class="tab-pane fade white-bg-w3speedster<?php echo $tab == 'js' ? ' active in' : ''; ?>">
	<div class="header w3d-flex gap20">
		<div class="heading_container">
			<h4 class="w3heading">
				<?php $admin->core->translate('Javascript Optimization'); ?>
			</h4>
			<span class="w3info"><a
					href="https://w3speedster.com/w3speedster-documentation/#javascript_optimization"><?php $admin->core->translate('More info'); ?>?
				</a></span>
		</div>
		<div class="icon_container"><img
			src="<?php $admin->core->esc_url_e( W3SPEEDSTER_URL . 'assets/images/js-icon.webp' ); ?>" alt="Javascript Optimization"></div>
	</div>
	<hr>

	<div class="js_box">
		<div class="w3d-flex gap20 ">
			<label><?php $admin->core->translate('Enable Javascript Optimization'); ?><span
					class="w3info"></span><span
					class="w3info-display"><?php $admin->core->translate('Turn on to optimize javascript'); ?></span></label>
			<div class="input_box">
				<label class="switch" for="enable-js-minification">
					<input type="checkbox" name="js" 
					<?php if (!empty($result['js']) && $result['js'] == "on") $admin->core->esc_html_e("checked"); ?> id="enable-js-minification">
					<div class="checked"></div>
				</label>
			</div>
		</div>

		<div class="w3d-flex gap20 ">
			<label><?php $admin->core->translate('Lazyload Javascript'); ?> <span
					class="w3info"></span><span
					class="w3info-display"><?php $admin->core->translate('Choose when to load javascript'); ?></span></label>
			<select name="load_combined_js">
				<option value="after_page_load" <?php if (!empty($result['load_combined_js']) && $result['load_combined_js'] == 'after_page_load') $admin->core->esc_attr_e('selected'); ?>>
					<?php $admin->core->translate('Yes'); ?>
				</option>
				<option value="on_page_load" <?php if (!empty($result['load_combined_js']) && $result['load_combined_js'] == 'on_page_load') $admin->core->esc_attr_e('selected'); ?>>
					<?php $admin->core->translate('No'); ?>
				</option>

			</select>
		</div>
	</div>
	<hr>
	<div class="js_box cdn_resources">
		<div class="w3d-flex gap20 w3align-item-baseline">
			<label><?php $admin->core->translate('Load Inline Javascript as URL'); ?><span
					class="w3info"></span><span
					class="w3info-display"><?php $admin->core->translate('Enter matching text of inline script url which needs to be load in a url to avoid large page size, javascript execution time. Each exclusion to be entered in a new line.'); ?></span></label>
			<div class="input_box">
				<div class="single-row">
					<?php
					if (!empty($result['load_script_tag_in_url']) && is_array($result['load_script_tag_in_url'])) {
						foreach ($result['load_script_tag_in_url'] as $row) {
							if (!empty(trim($row))) {
								?>
								<div class="cdn_input_box minus w3d-flex">
									<input type="text" name="load_script_tag_in_url[]"
										value="<?php $admin->core->esc_attr_e(trim($row)); ?>"
										placeholder="<?php $admin->core->translate('Please Enter matching text of the inline javascript here'); ?>"><button
										type="button" class="w3text-white rem-row w3bg-danger"><i
											class="fa fa-times"></i></button>
								</div>
								<?php
							}
						}
					} ?>
				</div>
				<div class="cdn_input_box plus">
					<button type="button" data-name="load_script_tag_in_url"
						data-placeholder="<?php $admin->core->translate('Please Enter matching text of the inline javascript here'); ?>"
						class="btn small w3text-white w3bg-success add_more_row"><?php $admin->core->translate('Add Rule'); ?></button>
				</div>

			</div>
		</div>
	</div>
	<hr>
	<div class="save-changes w3d-flex gap10">
		<input type="button" value="<?php $admin->core->translate('Save Changes'); ?>"
			class="btn hook_submit">
		<div class="in-progress w3d-flex save-changes-loader" style="display:none">
			<img src="<?php $admin->core->esc_url_e( W3SPEEDSTER_URL . 'assets/images/loader-gif.gif' ); ?>" alt="loader"
				class="loader-img">
		</div>
	</div>

</section>
