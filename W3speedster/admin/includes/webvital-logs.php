<section id="webvitalslogs" class="tab-pane fade<?php echo $tab == 'webvitalslogs' ? ' active in' : ''; ?>">
	<div class="header w3d-flex gap20">
		<div class="heading_container">
			<h4 class="w3heading"><?php $admin->core->translate('Debug Logs'); ?>
			</h4>
			<span class="w3info"><a
					href="https://w3speedster.com/w3speedster-documentation/"><?php $admin->core->translate('More info'); ?>?
				</a></span>
		</div>
		<div class="icon_container"> <img src="<?php $admin->core->esc_url_e( W3SPEEDSTER_URL . 'assets/images/logs-icon.webp' ); ?>"></div>
	</div>
	<hr>

	<div class="w3d-flex gap20 <?php $admin->core->esc_attr_e($hidden_class); ?>">
		<label><?php $admin->core->translate('Enable Core Web Vitals Logs'); ?><span
				class="w3info"></span><span
				class="w3info-display"><?php $admin->core->translate('Enable to Log Core Web Vitals Logs.'); ?></span></label>
		<div class="input_box">
			<label class="switch" for="enable-webvitals-log">
				<input type="checkbox" name="webvitals_logs" <?php if (!empty($result['webvitals_logs']) && $result['webvitals_logs'] == "on")
					echo "checked"; ?> id="enable-webvitals-log">
				<div class="checked"></div>
			</label>
		</div>
	</div>
	<?php if (empty($result['webvitals_logs'])) { ?>
		<p class="alert_message"><?php $admin->core->translate('Enable Debug Log options for Logging') ?></p>
	<?php } else { ?>
		<div class="w3d-flex gap20 filter-row">
			<div class="show_log w3d-flex gap10">
				<label for="show_log_entry"><?php $admin->core->translate('Show'); ?></label>
				<select name="temp_input" id="show_log_entry" class="show_log_entry">
					<option value="10"><?php $admin->core->translate('10'); ?></option>
					<option value="20"><?php $admin->core->translate('20'); ?></option>
					<option value="30"><?php $admin->core->translate('30'); ?></option>
					<option value="40"><?php $admin->core->translate('40'); ?></option>
					<option value="50"><?php $admin->core->translate('50'); ?></option>
				</select>
			</div>
			<div class="delete-log-data w3d-flex gap10">
				<label for="log_delete_time"><?php $admin->core->translate('Delete Logs'); ?></label>
				<select class="log_select" id="log_delete_time" name="temp_input">
					<option value=""><?php $admin->core->translate('Select Log Time'); ?></option>
					<option value="last7days"><?php $admin->core->translate('Keep last 7 Days'); ?></option>
					<option value="lastMonth"><?php $admin->core->translate('Keep last 30 Days'); ?></option>
					<option value="last3months"><?php $admin->core->translate('Keep last 90 Days'); ?>
					</option>
					<option value="last6months"><?php $admin->core->translate('Keep last 180 Days'); ?>
					</option>
					<!-- <option value="lastYear">All</option> -->
					<option value="all"><?php $admin->core->translate('All'); ?></option>
				</select>
				<button type="button"
					class="btn btn-log-delete"><?php $admin->core->translate('Delete'); ?></button>
			</div>

		</div>
		<div class="w3d-flex gap10 filter-row">
			<div class="filter_by_issue w3d-flex gap10">
				<label for="filter_by_issue"><?php $admin->core->translate('Issue Type'); ?></label>
				<select name="temp_input" class="filter_by_issuetype">
					<option value=""><?php $admin->core->translate('All'); ?></option>
					<option value="CLS"><?php $admin->core->translate('CLS'); ?></option>
					<option value="FID"><?php $admin->core->translate('FID'); ?></option>
					<option value="INP"><?php $admin->core->translate('INP'); ?></option>
					<option value="LCP"><?php $admin->core->translate('LCP'); ?></option>
				</select>
			</div>
			<div class="filter_by_device w3d-flex gap10">
				<label for="filter_by_device"><?php $admin->core->translate('Device'); ?></label>
				<select name="temp_input" class="filter_by_deviceType">
					<option value=""><?php $admin->core->translate('All'); ?></option>
					<option value="Mobile"><?php $admin->core->translate('Mobile'); ?></option>
					<option value="Desktop"><?php $admin->core->translate('Desktop'); ?></option>
				</select>
			</div>
			<div class="filter_by_url ">
				<select class="url-select-multiple" id="filter_by_url" class="filter_by_url_input"
					name="temp_input[]" multiple="multiple">
					<input type="text" class="custom_select_inp"
						placeholder="<?php $admin->core->translate('https://...'); ?>">
					<button type="button" class="btn_clear_url_inp" style="display:none">+</button>
					<div id="custom_select_url"></div>
				</select>
			</div>
			<div class="filter_by_date w3d-flex gap10">
				<label for="start_date"><?php $admin->core->translate('From'); ?></label>
				<input type="text" name="temp_input" class="start_date">
				<label for="end_date"><?php $admin->core->translate('To'); ?></label>
				<input type="text" name="temp_input" class="end_date">
			</div>
			<button type="button"
				class="btn btn-apply-filter"><?php $admin->core->translate('Apply Filters'); ?></button>
			<button type="button"
				class="btn btn-rem-filter"><?php $admin->core->translate('Clear'); ?></button>
		</div>
		<div popover="auto" id="more_info">
			<button type="button" popovertarget="more_info" popovertargetaction="hide" title="<?php $admin->core->translate('Close'); ?>"
				class="close-popover">+</button>
			<ul class="log-info">

			</ul>
		</div>
		<div class="log-data-table">
			<?php echo $admin->core->w3SpeedsterGetLogData(); ?>
		</div>
		<?php
	}
	?>
</section>
