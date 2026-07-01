<section id="opt_img" class="tab-pane fade<?php echo $tab == 'opt_img' ? ' active in' : ''; ?>">
	<div class="header w3d-flex gap20">
		<div class="heading_container">
			<h4 class="w3heading">
				<?php $admin->core->translate('Image Optimization'); ?>
			</h4>
			<span class="w3info"><a
					href="https://w3speedster.com/w3speedster-documentation/#img_optimization"><?php $admin->core->translate('More info'); ?>?
				</a></span>
		</div>
		<div class="icon_container"> <img src="<?php $admin->core->esc_url_e( W3SPEEDSTER_URL . 'assets/images/image-icon.webp' ); ?>" alt="Image Optimization"></div>
	</div>
	<hr>
	<div class="w3d-flex gap20 <?php $admin->core->esc_attr_e($hidden_class); ?>">
		<label><?php $admin->core->translate('Convert to Webp'); ?><span class="w3info"></span><span
				class="w3info-display"><?php $admin->core->translate('This will convert and render images in webp. Need to start image optimization in image optimization tab'); ?></span></label>
		<div class="w3d-flex">
			<label for="jpg"><?php $admin->core->translate('JPG'); ?>&nbsp;</label>
			<input type="checkbox" name="webp_jpg" <?php if (!empty($result['webp_jpg']) && $result['webp_jpg'] == "on")
														echo "checked"; ?> id="jpg" class="main-opt-img">
		</div>
		<div class="w3d-flex">
			<label for="png"><?php $admin->core->translate('PNG'); ?>&nbsp;</label>
			<input type="checkbox" name="webp_png" <?php if (!empty($result['webp_png']) && $result['webp_png'] == "on")
														echo "checked"; ?> id="png" class="main-opt-img">
		</div>
	</div>

	<div class="w3d-flex gap20 
		<?php $admin->core->esc_attr_e($hidden_class); ?>">
		<label><?php $admin->core->translate('Enable Lazy Load'); ?><span class="w3info"></span><span
				class="w3info-display"><?php $admin->core->translate('This will enable lazy loading of resources.'); ?></span></label>
		<div class="w3d-flex">
			<label for="image"><?php $admin->core->translate('Image'); ?>&nbsp;</label>
			<input type="checkbox" name="lazy_load" <?php if (!empty($result['lazy_load']) && $result['lazy_load'] == "on")
														echo "checked"; ?> id="image">
		</div>
		<div class="w3d-flex">
			<label for="iframe"><?php $admin->core->translate('Iframe'); ?>&nbsp;</label>
			<input type="checkbox" name="lazy_load_iframe" <?php if (!empty($result['lazy_load_iframe']) && $result['lazy_load_iframe'] == "on")
																echo "checked"; ?> id="iframe">
		</div>
		<div class="w3d-flex">
			<label for="video"><?php $admin->core->translate('Video'); ?>&nbsp;</label>
			<input type="checkbox" name="lazy_load_video" <?php if (!empty($result['lazy_load_video']) && $result['lazy_load_video'] == "on")
																echo "checked"; ?> id="video">
		</div>
		<div class="w3d-flex">
			<label for="audio"><?php $admin->core->translate('Audio'); ?>&nbsp;</label>
			<input type="checkbox" name="lazy_load_audio" <?php if (!empty($result['lazy_load_audio']) && $result['lazy_load_audio'] == "on")
																echo "checked"; ?> id="audio">
		</div>
	</div>

	<div class="w3d-flex gap20 
		<?php $admin->core->esc_attr_e($hidden_class); ?>">
		<label><?php $admin->core->translate('Load SVG Inline Tag as URL'); ?><span
				class="w3info"></span><span
				class="w3info-display"><?php $admin->core->translate('Load SVG inline tag as url to avoid large DOM elements'); ?></span></label>
		<div class="input_box">
			<label class="switch" for="load-inline-svg-tag-url">
				<input type="checkbox" name="inlineToUrlSVG" <?php if (!empty($result['inlineToUrlSVG']) && $result['inlineToUrlSVG'] == "on") echo "checked"; ?> id="load-inline-svg-tag-url">
				<div class="checked"></div>
			</label>
		</div>
	</div>
	<div class="w3d-flex gap20 
			<?php $admin->core->esc_attr_e($hidden_class); ?>">
		<label><?php $admin->core->translate('Responsive Images'); ?><span class="w3info"></span><span
				class="w3info-display"><?php $admin->core->translate('Load smaller images on mobile to reduce load time'); ?></span></label>
		<div class="input_box">
			<label class="switch" for="resp-imgs">
				<input type="checkbox" name="resp_bg_img" <?php if (!empty($result['resp_bg_img']) && $result['resp_bg_img'] == "on")
																echo "checked"; ?> id="resp-imgs">
				<div class="checked"></div>
			</label>
		</div>
	</div>
	<?php
	if (empty($result['license_key']) || empty($result['is_activated'])) {
		echo '<span class="non_licensed"><strong class="w3text-danger">* Starting 500 images will be optimized </strong><br><br><a href="https://w3speedster.com/" class="w3text-success"><strong>*<u>GO PRO</u> </strong></a> </span><br></br>';
	}
	?>
	<hr>
	<div class="save-changes w3d-flex gap10">
		<input type="button" value="<?php $admin->core->translate('Save Changes'); ?>"
			class="btn hook_submit gen">
		<div class="in-progress w3d-flex save-changes-loader" style="display:none">
			<img src="<?php $admin->core->esc_url_e( W3SPEEDSTER_URL . 'assets/images/loader-gif.gif' ); ?>" alt="loader"
				class="loader-img">
		</div>
	</div>
</section>