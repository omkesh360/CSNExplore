<section id="w3-cdn" class="tab-pane fade<?php echo $tab == 'w3-cdn' ? ' active in' : ''; ?>">
	<div class="header w3d-flex gap20">
		<div class="heading_container">
			<h4 class="w3heading">
				<?php $admin->core->translate('CDN'); ?>
			</h4>
			<span class="w3info"><a href="https://w3speedster.com/w3speedster-documentation/"><?php $admin->core->translate('More info'); ?>?</a></span>
		</div>
		<div class="icon_container"> <img src="<?php $admin->core->esc_url_e( W3SPEEDSTER_URL . 'assets/images/general-setting-icon.webp' ); ?>" alt="CDN" >
		</div>
	</div>
	<hr>
	<?php
	$has_entries = !empty($admin->core->settings['cdn']);
	if ($has_entries) :
		foreach ($admin->core->settings['cdn'] as $key => $value) : ?>
			<div class="w3-cdn" data-index="<?php $admin->core->esc_attr_e($key); ?>">
				<div class="w3d-flex gap10">
					<label><?php $admin->core->translate('CDN url'); ?><span class="w3info"></span><span class="w3info-display"><?php $admin->core->translate('Enter CDN url with http or https'); ?></span></label>
					<div class="cdn_input_box minus">
						<button type="button" class="Remove_w3_cdnentries">
							<i class="fa fa-times"></i>
						</button>
					</div>
					<div class="input_box">
						<label for="cdn-url-<?php $admin->core->esc_attr_e($key); ?>">
							<input type="text" name="cdn[<?php $admin->core->esc_attr_e($key); ?>][url]" id="cdn-url-<?php $admin->core->esc_attr_e($key); ?>" placeholder="<?php $admin->core->translate('Please Enter CDN url here'); ?>" value="<?php $admin->core->esc_attr_e((isset($value['url']) ? $value['url'] : '')); ?>">
						</label>
					</div>
				</div>
				<div class="w3d-flex gap20">
					<label><?php $admin->core->translate('Select File Types To Include'); ?><span class="w3info"></span></label>
					<div class="input_box custom-dropdown">
						<input type="hidden" name="cdn[<?php $admin->core->esc_attr_e($key); ?>][type]" id="exclude-file-extensions-from-cdn-<?php $admin->core->esc_attr_e($key); ?>" value="<?php $admin->core->esc_attr_e((isset($value['type']) ? $value['type'] : '')); ?>">
						<div class="selected-items" id="selected-extensions-<?php $admin->core->esc_attr_e($key); ?>">
							<?php
							if (!empty($value['type'])) {
								$types = explode(',', strtolower($value['type']));
								foreach ($types as $type) {
									$displayType = ucfirst($type); ?>
									<span class="selected-item" data-value="<?php $admin->core->esc_attr_e($type) ?>"><?php $admin->core->esc_attr_e($displayType) ?><button type="button" class="remove-item" data-value="<?php $admin->core->esc_attr_e($type) ?>">x</button></span>
							<?php }
							} ?>
						</div>
						<input type="text" placeholder="<?php $admin->core->translate('Type to filter extensions'); ?>" class="dropdown-input" oninput="filterOptions(this, 'extensions-list-<?php $admin->core->esc_attr_e($key); ?>')" data-list-id="extensions-list-<?php $admin->core->esc_attr_e($key); ?>">
						<div class="dropdown-list" id="extensions-list-<?php $admin->core->esc_attr_e($key); ?>">
							<div class="dropdown-item select-all" data-value="all"><?php $admin->core->translate('Select All'); ?></div>
							<div class="dropdown-item" data-value="image">Image</div>
							<div class="dropdown-item" data-value="font">Fonts</div>
							<div class="dropdown-item" data-value="js">Js</div>
							<div class="dropdown-item" data-value="css">Css</div>
							<div class="dropdown-item" data-value="audio">Audio</div>
							<div class="dropdown-item" data-value="video">Video</div>
						</div>
					</div>
				</div>
				<div class="w3d-flex gap20">
					<label><?php $admin->core->translate('Exclude path from cdn'); ?><span class="w3info"></span><span class="w3info-display"><?php $admin->core->translate('Enter path separated by comma which are to be excluded from CDN. For eg. (/wp-includes/)'); ?></span></label>
					<div class="input_box">
						<label for="exclude-path-from-cdn-<?php $admin->core->esc_attr_e($key); ?>">
							<input type="text" name="cdn[<?php $admin->core->esc_attr_e($key); ?>][exclude_path]" id="exclude-path-from-cdn-<?php $admin->core->esc_attr_e($key); ?>" placeholder="<?php $admin->core->translate('Please Enter paths separated by comma'); ?>" value="<?php $admin->core->esc_attr_e(isset($value['exclude_path']) ? $value['exclude_path'] : ''); ?>">
						</label>
					</div>
				</div>
			</div>
	<?php endforeach;
	endif;
	?>
	<div class="addmore_button"><button type="button" class="add_more_cdnRows btn">Add More</button></div>
	<hr>
	<div class="save-changes w3d-flex gap10">
		<input type="submit" value="<?php $admin->core->translate('Save Changes'); ?>" class="btn hook_submit">
		<div class="in-progress w3d-flex save-changes-loader" style="display:none">
			<img src="<?php $admin->core->esc_url_e( W3SPEEDSTER_URL . 'assets/images/loader-gif.gif' ); ?>" alt="loader" class="loader-img" >
		</div>
	</div>
</section>

