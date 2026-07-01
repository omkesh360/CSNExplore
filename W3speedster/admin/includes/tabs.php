<ul class="nav nav-tabs w3speedsternav">
	<?php if (empty($result['manage_site_separately'])) { ?>
		<li class="w3_optimize_ai<?php echo $tab == 'optimizeAi' ? ' active' : ''; ?>">
			<a data-toggle="tab" data-section="optimizeAi" href="javascript:void(0)">
				<?php $admin->core->esc_html_e('Optimize with AI', 'w3speedster-wp'); ?>
				<span class="new-feature">New</span>
			</a>
		</li>
		<li class="w3_html_cache<?php echo $tab == 'htmlCache' ? ' active' : ''; ?>">
			<a data-toggle="tab" data-section="htmlCache" href="javascript:void(0)">
				<?php $admin->core->esc_html_e('HTML Cache', 'w3speedster-wp'); ?>
			</a>
		</li>
	<?php } ?>
	<li class="w3_general<?php echo empty($tab) || $tab == 'general' ? ' active' : ''; ?>">
		<a data-toggle="tab" data-section="general" href="javascript:void(0)">
			<?php $admin->core->esc_html_e('General', 'w3speedster-wp'); ?>
		</a>
	</li>
	<?php if (empty($result['manage_site_separately'])) { ?>
		<li class="w3_cdn<?php echo $tab == 'w3-cdn' ? ' active' : ''; ?>">
			<a data-toggle="tab" data-section="w3-cdn" href="javascript:void(0)">
				<?php $admin->core->esc_html_e('CDN', 'w3speedster-wp'); ?>
			</a>
		</li>
		<li class="w3_opt_img<?php echo $tab == 'opt_img' ? ' active' : ''; ?>">
			<a data-toggle="tab" data-section="opt_img" href="javascript:void(0)">
				<?php $admin->core->esc_html_e('Image Optimization', 'w3speedster-wp'); ?>
			</a>
		</li>
		<li class="w3_css<?php echo $tab == 'css' ? ' active' : ''; ?>">
			<a data-toggle="tab" data-section="css" href="javascript:void(0)">
				<?php $admin->core->esc_html_e('CSS', 'w3speedster-wp'); ?>
			</a>
		</li>
		<li class="w3_js<?php echo $tab == 'js' ? ' active' : ''; ?>">
			<a data-toggle="tab" data-section="js" href="javascript:void(0)">
				<?php $admin->core->esc_html_e('JavaScript', 'w3speedster-wp'); ?>
			</a>
		</li>
		<li class="w3_exclusions<?php echo $tab == 'exclusions' ? ' active' : ''; ?>">
			<a data-toggle="tab" data-section="exclusions" href="javascript:void(0)">
				<?php $admin->core->esc_html_e('Exclusions', 'w3speedster-wp'); ?>
			</a>
		</li>
		<li class="w3_custom_code<?php echo $tab == 'w3_custom_code' ? ' active' : ''; ?>">
			<a data-toggle="tab" data-section="w3_custom_code" href="javascript:void(0)">
				<?php $admin->core->esc_html_e('Custom Code', 'w3speedster-wp'); ?>
			</a>
		</li>
		<li class="w3_cache<?php echo $tab == 'cache' ? ' active' : ''; ?>">
			<a data-toggle="tab" data-section="cache" href="javascript:void(0)">
				<?php $admin->core->esc_html_e('Clear Cache', 'w3speedster-wp'); ?>
			</a>
		</li>
		<li class="w3_hooks<?php echo $tab == 'hooks' ? ' active' : ''; ?>">
			<a data-toggle="tab" data-section="hooks" href="javascript:void(0)">
				<?php $admin->core->esc_html_e('Hooks', 'w3speedster-wp'); ?>
			</a>
		</li>
		<li class="w3_webvitals_log<?php echo $tab == 'webvitalslogs' ? ' active' : ''; ?>">
			<a data-toggle="tab" data-section="webvitalslogs" href="javascript:void(0)">
				<?php $admin->core->esc_html_e('Web Vitals Logs', 'w3speedster-wp'); ?>
			</a>
		</li>
		<li class="w3_import<?php echo $tab == 'import' ? ' active' : ''; ?>">
			<a data-toggle="tab" data-section="import" href="javascript:void(0)">
				<?php $admin->core->esc_html_e('Import/Export', 'w3speedster-wp'); ?>
			</a>
		</li>
		<li class="w3_change_log<?php echo $tab == 'w3ChangeLog' ? ' active' : ''; ?>">
			<a data-toggle="tab" data-section="w3ChangeLog" href="javascript:void(0)">
				<?php $admin->core->esc_html_e('Change Logs', 'w3speedster-wp'); ?>
			</a>
		</li>
		<li class="w3_logout">
			<a data-toggle="tab" data-section="w3_logout" href="<?php echo $admin->addSettings['siteUrl']; ?>?w3_logout=true">
				<?php $admin->core->esc_html_e('LogOut', 'w3speedster-wp'); ?>
			</a>
		</li>
	<?php } ?>
</ul>
			
