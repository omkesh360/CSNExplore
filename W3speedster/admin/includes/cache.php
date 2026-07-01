<section id="cache" class="tab-pane fade<?php echo $tab == 'cache' ? ' active in' : ''; ?>">
	<div class="header w3d-flex gap20">
		<div class="heading_container">
			<h4 class="w3heading">
				<?php $admin->core->translate('Cache'); ?>
			</h4>
			<span class="w3info"><a
					href="https://w3speedster.com/w3speedster-documentation/#Cache"><?php $admin->core->translate('More info'); ?>?
				</a></span>
		</div>
		<div class="icon_container"> <img
				src="<?php $admin->core->esc_url_e( W3SPEEDSTER_URL . 'assets/images/caches-icon.webp' ); ?>" alt="Cache" >
			</div>
	</div>
	<hr>
	<div class="caches_box">
		<div class="w3d-flex gap20 ">
			<label><?php $admin->core->translate('Delete HTML cache'); ?><span class="w3info"></span><span
					class="w3info-display"><?php $admin->core->translate('Delete HTML cache when you do any changes'); ?></span></label>
			<button class="btn" type="button" id="del_html_cache">
				<?php $admin->core->translate('Delete Now'); ?>
			</button>
			<div class="in-progress w3d-flex delete_html_cache" style="display:none">
				<img src="<?php $admin->core->esc_url_e( W3SPEEDSTER_URL . 'assets/images/loader-gif.gif' ); ?>" alt="loader"
					class="loader-img">
				<small
					class="extra-small m-0">&nbsp;<em>&nbsp;<?php $admin->core->translate('Deleting HTML Cache...'); ?></em></small>
			</div>
		</div>
		<div class="w3d-flex gap20 ">
			<label><?php $admin->core->translate('Delete HTML/JS/CSS Cache'); ?><span class="w3info"></span><span
					class="w3info-display"><?php $admin->core->translate('Delete Html, javascript and css combined and minified cache files'); ?></span></label>
			<button class="btn" type="button" id="del_js_css_cache">
				<?php $admin->core->translate('Delete Now'); ?>
			</button>
			<div class="in-progress w3d-flex delete_css_js_cache" style="display:none">
				<img src="<?php $admin->core->esc_url_e( W3SPEEDSTER_URL . 'assets/images/loader-gif.gif' ); ?>" alt="loader"
					class="loader-img">
				<small
					class="extra-small m-0">&nbsp;<em>&nbsp;<?php $admin->core->translate('Deleting HTML/JS/Css Cache...'); ?></em></small>
			</div>
		</div>
		<div class="w3d-flex gap20 ">
			<label><?php $admin->core->translate('Delete critical css'); ?><span
					class="w3info"></span><span
					class="w3info-display"><?php $admin->core->translate('Delete critical css only when you have made any changes to style. This may take considerable amount of time to regenerate depending upon the pages on the site'); ?></span></label>
			<button class="btn" type="button" id="del_critical_css_cache">
				<?php $admin->core->translate('Delete Now'); ?>
			</button>
			<div class="in-progress w3d-flex delete_critical_css_cache" style="display:none">
				<img src="<?php $admin->core->esc_url_e( W3SPEEDSTER_URL . 'assets/images/loader-gif.gif' ); ?>" alt="loader"
					class="loader-img">
				<small
					class="extra-small m-0">&nbsp;<em>&nbsp;<?php $admin->core->translate('Deleting Critical Css Cache...'); ?></em></small>
			</div>
		</div>
	</div>
</section>
