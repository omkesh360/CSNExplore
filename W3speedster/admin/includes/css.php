<section id="css" class="tab-pane fade<?php echo $tab == 'css' ? ' active in' : ''; ?>">
	<div class="header w3d-flex gap20">
		<div class="heading_container">
			<h4 class="w3heading">
				<?php $admin->core->translate('CSS Optimization'); ?>
			</h4>
			<span class="w3info"><a
					href="https://w3speedster.com/w3speedster-documentation/#css_optimization"><?php $admin->core->translate('More info'); ?>?
				</a></span>
		</div>
		<div class="icon_container">
			<img 
			src="<?php $admin->core->esc_url_e( W3SPEEDSTER_URL . 'assets/images/css-icon.webp' ); ?>" alt="CSS Optimization">
		</div>
	</div>
	<hr>
	<div class="css_box">
		<div class="w3d-flex gap20 ">
			<label><?php $admin->core->translate('Enable CSS Optimization'); ?><span
					class="w3info"></span><span
					class="w3info-display"><?php $admin->core->translate('Turn on to optimize css'); ?></span></label>
			<div class="input_box">
				<label class="switch" for="enable-css-minification">
					<input type="checkbox" name="css" <?php if (!empty($result['css']) && $result['css'] == "on")
						echo "checked"; ?> id="enable-css-minification"
						class="opt-css">
					<div class="checked"></div>
				</label>
			</div>
		</div>
		<div class="w3d-flex gap20 ">
			<label><?php $admin->core->translate('Localize Google fonts'); ?><span
					class="w3info"></span><span
					class="w3info-display"><?php $admin->core->translate('Turn on to load all google fonts from self domain'); ?></span></label>
			<div class="input_box">
				<label class="switch" for="localize-google-fonts">
					<input type="checkbox" name="localize_google_fonts" <?php if (!empty($result['localize_google_fonts']) && $result['localize_google_fonts'] == "on")
						echo "checked"; ?>
						id="localize-google-fonts" class="opt-css">
					<div class="checked"></div>
				</label>
			</div>
		</div>
	</div>
	<hr>
	<div class="css_box">
		<div class="w3d-flex gap20 ">
			<label><?php $admin->core->translate('Load Critical CSS'); ?><span
					class="w3info"></span><span
					class="w3info-display"><?php $admin->core->translate('Preload generated crictical css'); ?></span></label>
			<div class="input_box">
				<label class="switch" for="load-critical-css">
					<input type="checkbox" name="load_critical_css" <?php if (!empty($result['load_critical_css']) && $result['load_critical_css'] == "on")
						echo "checked"; ?> id="load-critical-css" class="opt-css parent-fields">
					<div class="checked"></div>
				</label>
			</div>
		</div>
		<div class="w3d-flex gap20 child-fields" <?php if (empty($result['load_critical_css'])) {
			echo 'style="display:none"';} ?>>
			<label><?php $admin->core->translate('Load Critical CSS in Style Tag'); ?><span
					class="w3info"></span><span
					class="w3info-display"><?php $admin->core->translate('Preload generated crictical css in style tag'); ?></span></label>
			<div class="input_box">
				<label class="switch" for="load-critical-css-in-style-tag">
					<input type="checkbox" name="load_critical_css_style_tag" <?php if (!empty($result['load_critical_css_style_tag']) && $result['load_critical_css_style_tag'] == "on")
						echo "checked"; ?>
						id="load-critical-css-in-style-tag" class="opt-css">
					<div class="checked"></div>
				</label>
			</div>
		</div>
	</div>
	<hr>
	<div class="css_box cdn_resources">
		<div class="w3d-flex gap20 w3align-item-baseline">
			<label><?php $admin->core->translate('Load Style Tag in Head to Avoid CLS'); ?> <span
					class="w3info"></span><span
					class="w3info-display"><?php $admin->core->translate('Enter matching text of style tag, which are to be loaded in the head. Each style tag to be entered in a new line'); ?></span></label>
			<div class="input_box">
				<div class="single-row">
					<?php
					if (!empty($result['load_style_tag_in_head']) && is_array($result['load_style_tag_in_head'])) {
						foreach ($result['load_style_tag_in_head'] as $row) {
							if (!empty(trim($row))) {
								?>
								<div class="cdn_input_box minus w3d-flex">
									<input type="text" name="load_style_tag_in_head[]"
										value="<?php $admin->core->esc_attr_e(trim($row)); ?>"
										placeholder="<?php $admin->core->translate('Please Enter style tag text'); ?>"><button
										type="button" class="w3text-white rem-row w3bg-danger"><i
											class="fa fa-times"></i></button>
								</div>
								<?php
							}
						}
					} ?>
				</div>
				<div class="cdn_input_box plus">
					<button type="button" data-name="load_style_tag_in_head"
						data-placeholder="<?php $admin->core->translate('Please Enter style tag text'); ?>"
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
