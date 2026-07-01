<section id="exclusions" class="tab-pane fade<?php echo $tab == 'exclusions' ? ' active in' : ''; ?>">
	<div class="header w3d-flex gap20">
		<div class="heading_container">
			<h4 class="w3heading">
				<?php $admin->core->translate('Exclusions'); ?>
			</h4>
			<span class="w3info"><a
					href="https://w3speedster.com/w3speedster-documentation/"><?php $admin->core->translate('More info'); ?>?
				</a></span>
		</div>
		<div class="icon_container"> 
			<img src="<?php $admin->core->esc_url_e( W3SPEEDSTER_URL . 'assets/images/exclusions-icon1.webp' ); ?>">
		</div>
	</div>
	<hr>
	<div class="way_to_psi">
	<details>
		<summary>
			<h4 class="w3heading w3text-skyblue"><?php $admin->core->translate('Media Exclusions (Image, Video, Audio)'); ?></h4>
		</summary>
		<div class="cdn_resources <?php $admin->core->esc_attr_e($hidden_class); ?>">
			<div class="w3d-flex gap20 w3align-item-baseline">
				<label
					for="Preload Media"><?php $admin->core->translate('Preload Media'); ?><span
						class="w3info"></span><span
						class="w3info-display"><?php $admin->core->translate('Enter url of the Resources, which are to be preloaded..'); ?></span></label>
				<div class="input_box">
					<div class="single-row">
						<?php
						if (!empty($result['preload_resources']) && is_array($result['preload_resources'])) {
							foreach ($result['preload_resources'] as $row) {
								if (!empty(trim($row))) {
									?>
									<div class="cdn_input_box minus w3d-flex">
										<input type="text" name="preload_resources[]"
											value="<?php $admin->core->esc_attr_e(rtrim($row)); ?>"
											placeholder="<?php $admin->core->translate('Please Enter Resource Url'); ?>"><button
											type="button" class="w3text-white rem-row w3bg-danger"><i
												class="fa fa-times"></i></button>
									</div>
									<?php
								}
							}
						} ?>
					</div>
					<div class="cdn_input_box plus">
						<button type="button" data-name="preload_resources"
							data-placeholder="<?php $admin->core->translate('Please Enter Resource Url'); ?>"
							class="btn small w3text-white w3bg-success add_more_row"><?php $admin->core->translate('Add Rule'); ?></button>
					</div>

				</div>

			</div>
		</div>

		<!-- <hr> -->
		<div class="cdn_resources 
			<?php $admin->core->esc_attr_e($hidden_class); ?>">
			<div class="w3d-flex gap20 w3align-item-baseline">
				<label
					for="Exclude Media from Lazy Loading"><?php $admin->core->translate('Exclude Media from Lazy Loading'); ?><span
						class="w3info"></span><span
						class="w3info-display"><?php $admin->core->translate('Enter any matching text of image/iframe/video/audio tag to exclude from lazy loading. For more than one exclusion, click on add rule. For eg. (class / Id / url / alt).'); ?></span></label>
				<div class="input_box">
					<div class="single-row">
						<?php
						if (!empty($result['exclude_lazy_load']) && is_array($result['exclude_lazy_load'])) {
							foreach ($result['exclude_lazy_load'] as $row) {
								if (!empty(trim($row))) {
									?>
									<div class="cdn_input_box minus w3d-flex">
										<input type="text" name="exclude_lazy_load[]"
											value="<?php $admin->core->esc_attr_e(trim($row)); ?>"
											placeholder="<?php $admin->core->translate('Please Enter matching text of the image here'); ?>"><button
											type="button" class="w3text-white rem-row w3bg-danger"><i
												class="fa fa-times"></i></button>
									</div>
									<?php
								}
							}
						} ?>

					</div>
					<div class="cdn_input_box plus">
						<button type="button" data-name="exclude_lazy_load"
							data-placeholder="<?php $admin->core->translate('Please Enter matching text of the image here'); ?>"
							class="btn small w3text-white w3bg-success add_more_row"><?php $admin->core->translate('Add Rule'); ?></button>
					</div>

				</div>

			</div>
		</div>
	</details>
	</div>
	<hr>
	<div class="way_to_psi">
	<details>
		<summary>
			<h4 class="w3heading w3text-skyblue"><?php $admin->core->translate('CSS Exclusions'); ?></h4>
		</summary>

	<div class="css_box cdn_resources ">
		<div class="w3d-flex gap20 w3align-item-baseline">
			<label><?php $admin->core->translate('Exclude Stylesheet URLs from Optimization'); ?><span
					class="w3info"></span><span
					class="w3info-display"><?php $admin->core->translate('Enter matching text of css link url, which are to be excluded from css optimization. For each Exclusion, click on add rule'); ?></span></label>
			<div class="input_box">
				<div class="single-row">
					<?php
					if (!empty($result['exclude_css']) && is_array($result['exclude_css'])) {
						foreach ($result['exclude_css'] as $row) {
							if (!empty(trim($row))) {
								?>
								<div class="cdn_input_box minus w3d-flex">
									<input type="text" name="exclude_css[]" 
										value="<?php $admin->core->esc_attr_e(trim($row)); ?>"
										placeholder="<?php $admin->core->translate('Please Enter part of Stylesheet URL here'); ?>"><button
										type="button" class="w3text-white rem-row w3bg-danger"><i
											class="fa fa-times"></i></button>
								</div>
								<?php
							}
						}
					} ?>
				</div>
				<div class="cdn_input_box plus">
					<button type="button" data-name="exclude_css"
						data-placeholder="<?php $admin->core->translate('Please Enter part of Stylesheet URL here'); ?>"
						class="btn small w3text-white w3bg-success add_more_row"><?php $admin->core->translate('Add Rule'); ?></button>
				</div>
			</div>
		</div>

	</div>
	<!-- <hr> -->

	<div class="css_box cdn_resources">
		<div class="w3d-flex gap20 w3align-item-baseline">
			<label><?php $admin->core->translate('Force Lazy Load Stylesheet URLs'); ?> <span
					class="w3info"></span><span
					class="w3info-display"><?php $admin->core->translate('Enter matching text of css link url, which are forced to be lazyloaded and will load on user interaction. For each Exclusion, click on add rule.'); ?></span></label>
			<div class="input_box">
				<div class="single-row">
					<?php
					if (!empty($result['force_lazyload_css']) && is_array($result['force_lazyload_css'])) {
						foreach ($result['force_lazyload_css'] as $row) {
							if (!empty(trim($row))) {
								?>
								<div class="cdn_input_box minus w3d-flex">
									<input type="text" name="force_lazyload_css[]"
										value="<?php $admin->core->esc_attr_e(trim($row)); ?>"
										placeholder="<?php $admin->core->translate('Please Enter part of Stylesheet URL here'); ?>"><button
										type="button" class="w3text-white rem-row w3bg-danger"><i
											class="fa fa-times"></i></button>
								</div>
								<?php
							}
						}
					} ?>
				</div>
				<div class="cdn_input_box plus">
					<button type="button" data-name="force_lazyload_css"
						data-placeholder="<?php $admin->core->translate('Please Enter part of Stylesheet URL here'); ?>"
						class="btn small w3text-white w3bg-success add_more_row"><?php $admin->core->translate('Add Rule'); ?></button>
				</div>
			</div>
		</div>
	</div>
	<!-- <hr> -->
	
	</details>
	</div>
	<hr>
	<div class="way_to_psi">
	<details>
		<summary>
			<h4 class="w3heading w3text-skyblue"><?php $admin->core->translate('JS Exclusions'); ?></h4>
		</summary>
		<div class="js_box cdn_resources">
			<div class="w3d-flex gap20 w3align-item-baseline">
				<label><?php $admin->core->translate('Force Lazy Load Javascript'); ?> <span
						class="w3info"></span><span
						class="w3info-display"><?php $admin->core->translate('Enter matching text of inline javascript which needs to be forced to lazyload. For each Exclusion, click on add rule.'); ?></span></label>
				<div class="input_box">
					<div class="single-row">
						<?php
						if (!empty($result['force_lazy_load_inner_javascript']) && is_array($result['force_lazy_load_inner_javascript'])) {
							foreach ($result['force_lazy_load_inner_javascript'] as $row) {
								if (!empty(trim($row))) {
									?>
									<div class="cdn_input_box minus w3d-flex">
										<input type="text" name="force_lazy_load_inner_javascript[]"
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
						<button type="button" data-name="force_lazy_load_inner_javascript"
							data-placeholder="<?php $admin->core->translate('Please Enter matching text of the inline javascript here'); ?>"
							class="btn small w3text-white w3bg-success add_more_row"><?php $admin->core->translate('Add Rule'); ?></button>
					</div>

				</div>
			</div>
		</div>
		<!-- <hr> -->
		<div class="js_box cdn_resources">
			<div class="w3d-flex gap20 w3align-item-baseline">
				<label><?php $admin->core->translate('Exclude Javascript from Lazyload'); ?> <span
						class="w3info"></span><span
						class="w3info-display"><?php $admin->core->translate('Enter matching text of javascript url, which are to be excluded from javascript optimization. For each Exclusion, click on add rule.'); ?></span></label>
				<div class="input_box">
					<div class="single-row">
						<?php
						if (!empty($result['exclude_both_javascript']) && is_array($result['exclude_both_javascript'])) {
							foreach ($result['exclude_both_javascript'] as $row) {
								if (!empty(trim($row))) {
									?>
									<div class="cdn_input_box minus w3d-flex">
										<input type="text" name="exclude_both_javascript[]"
											value="<?php $admin->core->esc_attr_e(trim($row)); ?>"
											placeholder="<?php $admin->core->translate('Please Enter matching text of the javascript here'); ?>"><button
											type="button" class="w3text-white rem-row w3bg-danger"><i
												class="fa fa-times"></i></button>
									</div>
									<?php
								}
							}
						} ?>
					</div>
					<div class="cdn_input_box plus">
						<button type="button" data-name="exclude_both_javascript"
							data-placeholder="<?php $admin->core->translate('Please Enter matching text of the javascript here'); ?>"
							class="btn small w3text-white w3bg-success add_more_row"><?php $admin->core->translate('Add Rule'); ?></button>
					</div>

				</div>
			</div>
		</div>
		<!-- <hr> -->
	</details>
	</div>
	<hr>
	<div class="way_to_psi">
	<details>
		<summary>
			<h4 class="w3heading w3text-skyblue"><?php $admin->core->translate('Pages Exclusions'); ?></h4>
		</summary>
		<div class="cdn_resources 
			<?php $admin->core->esc_attr_e($hidden_class); ?>">
			<div class="w3d-flex gap20 html-cache-row">
				<label for="Preload Resources"><?php $admin->core->translate('Exclude pages from HTML caching'); ?><span
						class="w3info"></span><span
						class="w3info-display"><?php $admin->core->translate('Dont cache the url which match rule'); ?></span></label>
				<div class="input_box">
					<div class="single-row">
						<?php
						if (!empty($result['exclude_url_exclusions_html_cache']) && is_array($result['exclude_url_exclusions_html_cache'])) {
							foreach ($result['exclude_url_exclusions_html_cache'] as $row) {
								if (!empty(trim($row))) {
									?>
									<div class="cdn_input_box minus w3d-flex">
										<input type="text" name="exclude_url_exclusions_html_cache[]"
											value="<?php $admin->core->esc_attr_e(trim($row)); ?>"
											placeholder="<?php $admin->core->translate('Please Enter Url/String'); ?>"><button
											type="button" class="w3text-white rem-row w3bg-danger"><i
												class="fa fa-times"></i></button>
									</div>
									<?php
								}
							}
						} ?>
					</div>
					<div class="cdn_input_box plus">
						<button type="button" data-name="exclude_url_exclusions_html_cache"
							data-placeholder="<?php $admin->core->translate('Please Enter Url/String'); ?>"
							class="btn small w3text-white w3bg-success add_more_row"><?php $admin->core->translate('Add Rule'); ?></button>
					</div>

				</div>

			</div>
			<div class="w3d-flex gap20 w3align-item-baseline">
				<label
					for="Exclude Pages From Optimization"><?php $admin->core->translate('Exclude Pages From Optimization'); ?><span
						class="w3info"></span><span
						class="w3info-display"><?php $admin->core->translate('Enter slug of the url to exclude from optimization. For  eg. (/blog/). For home page, enter home url. For each Exclusion, click on add rule'); ?></span></label>
				<div class="input_box">
					<div class="single-row">
						<?php
						if (!empty($result['exclude_pages_from_optimization']) && is_array($result['exclude_pages_from_optimization'])) {
							foreach ($result['exclude_pages_from_optimization'] as $row) {
								if (!empty(trim($row))) {
									?>
									<div class="cdn_input_box minus w3d-flex">
										<input type="text" name="exclude_pages_from_optimization[]"
											value="<?php $admin->core->esc_attr_e(trim($row)); ?>"
											placeholder="<?php $admin->core->translate('Please Enter Page Url'); ?>"><button
											type="button" class="w3text-white rem-row w3bg-danger"><i
												class="fa fa-times"></i></button>
									</div>
									<?php
								}
							}
						}
						?>

					</div>
					<div class="cdn_input_box plus">
						<button type="button" data-name="exclude_pages_from_optimization"
							data-placeholder="<?php $admin->core->translate('Please Enter Page Url'); ?>"
							class="btn small w3text-white w3bg-success add_more_row"><?php $admin->core->translate('Add Rule'); ?></button>
					</div>

				</div>

			</div>
		</div>
		<div class="css_box cdn_resources">
		<div class="w3d-flex gap20 w3align-item-baseline">
			<label><?php $admin->core->translate('Exclude Pages from CSS Optimization'); ?> <span
					class="w3info"></span><span
					class="w3info-display"><?php $admin->core->translate('Enter slug of the page to exclude from css optimization'); ?></span></label>
			<div class="input_box">
				<div class="single-row">
					<?php
					if (!empty($result['exclude_page_from_load_combined_css']) && is_array($result['exclude_page_from_load_combined_css'])) {
						foreach ($result['exclude_page_from_load_combined_css'] as $row) {
							if (!empty(trim($row))) {
								?>
								<div class="cdn_input_box minus w3d-flex">
									<input type="text" name="exclude_page_from_load_combined_css[]"
										value="<?php $admin->core->esc_attr_e(trim($row)); ?>"
										placeholder="<?php $admin->core->translate('Please Enter Page Url'); ?>"><button
										type="button" class="w3text-white rem-row w3bg-danger"><i
											class="fa fa-times"></i></button>
								</div>
								<?php
							}
						}
					} ?>
				</div>
				<div class="cdn_input_box plus">
					<button type="button" data-name="exclude_page_from_load_combined_css"
						data-placeholder="<?php $admin->core->translate('Please Enter Page Url'); ?>"
						class="btn small w3text-white w3bg-success add_more_row"><?php $admin->core->translate('Add Rule'); ?></button>
				</div>
			</div>
		</div>
		</div>
		<div class="js_box cdn_resources">
			<div class="w3d-flex gap20 w3align-item-baseline">
				<label><?php $admin->core->translate('Exclude Pages from Javascript Optimization'); ?> <span
						class="w3info"></span><span
						class="w3info-display"><?php $admin->core->translate('Enter slug of the page to exclude from javascript optimization'); ?></span></label>
				<div class="input_box">
					<div class="single-row">
						<?php
						if (!empty($result['exclude_page_from_load_combined_js']) && is_array($result['exclude_page_from_load_combined_js'])) {
							foreach ($result['exclude_page_from_load_combined_js'] as $row) {
								if (!empty(trim($row))) {
									?>
									<div class="cdn_input_box minus w3d-flex">
										<input type="text" name="exclude_page_from_load_combined_js[]"
											value="<?php $admin->core->esc_attr_e(trim($row)); ?>"
											placeholder="<?php $admin->core->translate('Please Enter Js Page Url'); ?>"><button
											type="button" class="w3text-white rem-row w3bg-danger"><i
												class="fa fa-times"></i></button>
									</div>
									<?php
								}
							}
						} ?>
					</div>
					<div class="cdn_input_box plus">
						<button type="button" data-name="exclude_page_from_load_combined_js"
							data-placeholder="<?php $admin->core->translate('Please Enter Js Page Url'); ?>"
							class="btn small w3text-white w3bg-success add_more_row"><?php $admin->core->translate('Add Rule'); ?></button>
					</div>

				</div>
			</div>
		</div>
	</details>
	</div>
	<hr>
	<div class="single-hook_btn">
		<div class="save-changes w3d-flex gap10">
			<input type="button" value="Save Changes" class="btn hook_submit">
			<div class="in-progress w3d-flex save-changes-loader" style="display:none">
				<img src="<?php $admin->core->esc_url_e( W3SPEEDSTER_URL . 'assets/images/loader-gif.gif' ); ?>" alt="loader"
					class="loader-img">
			</div>
		</div>
	</div>
</section>
