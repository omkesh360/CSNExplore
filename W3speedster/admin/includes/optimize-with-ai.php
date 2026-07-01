<section id="optimizeAi" class="tab-pane fade">
    <div class="header w3d-flex gap20">
        <div class="heading_container">
            <h4 class="w3heading">Optimize with AI</h4>
            <span class="w3info">
                <a href="https://w3speedster.com/w3speedster-documentation/">More info ?</a>
            </span>
        </div>
        <div class="icon_container">
            <img src="<?php $admin->core->esc_url_e( W3SPEEDSTER_URL . 'assets/images/optimize-ai-icon.png' ); ?>" alt="Optimize with AI">
        </div>
    </div>
    <hr>
    <div class="w3d-flex gap20 ">
        <label>
            <?php $admin->core->translate('Enable AI Optimization'); ?>
            <span class="w3info"></span>
            <span class="w3info-display"><?php $admin->core->translate('Automatically optimize your website with AI-powered performance enhancements'); ?>
            </span>
        </label>
        <div class="input_box">
            <label class="switch" for="enable-ai-optimization">
                <input type="checkbox" name="ai-optimization" <?php if (!empty($result['ai-optimization']) && $result['ai-optimization'] == "on")
					$admin->core->esc_attr_e("checked"); ?> id="enable-ai-optimization">
                <div class="checked"></div>
            </label>
        </div>
        <div class="w3d-flex gap10 reverse">
            <button type="button" class="btn" id="w3RestartOptimization"><?php $admin->core->translate('Reset'); ?></button>
            <div class="in-progress w3d-flex" id="restart-optimization-loader" style="display:none">
                <img src="<?php $admin->core->esc_url_e( W3SPEEDSTER_URL . 'assets/images/loader-gif.gif' ); ?>" alt="loader" class="loader-img">
            </div>
        </div>
    </div>
    <?php if (!empty($result['ai-optimization']) && $result['ai-optimization'] == "on") : ?>
    <div class="enable-optimizeAi">
        <div class="w3d-flex gap20">
            <label for="" class="w3d-flex mb-10">
                <?php $admin->core->translate('Optimization Progress'); ?>
            </label>
        </div>
        <div class="progress-bar-bg optimize-bar-bg">
            <div class="progress progress-bar progress-bar-striped w3bg-success progress-bar-animated-img optimize-bar" id="optimize-ai-bar"></div>
        </div>
        <div class="w3d-flex gap20 justify-space-between">
            <span class="extra_display" id="optimize-pages-count">
                <?php $admin->core->translate('Loading...'); ?>
            </span>
            <span class="extra_display" id="optimize-remaining-time">
                <?php $admin->core->translate('Estimated time: Loading...'); ?>
            </span>
        </div>
    </div>
    <div class="w3d-flex gap20 ">
        <label>
            <?php $admin->core->translate('Pages per Minutes'); ?>
            <span class="w3info"></span>
            <span class="w3info-display"><?php $admin->core->translate('Number of pages to optimize simultaneously'); ?></span>
        </label>
        <div class="input_box">
            <label class="html-cache-expiry w3d-flex" for="page-batch">
                <input type="number" name="page_batch" value="<?php if (!empty($result['page_batch'])) $admin->core->esc_attr_e($result['page_batch']); else $admin->core->esc_attr_e('1'); ?>" id="page-batch"  max="20"
                    style="max-width:80px;">
                <small>&nbsp; <?php $admin->core->translate('Max 20 pages'); ?></small>
                <div class="checked"></div>
            </label>
        </div>
        <div class="w3d-flex gap10 reverse">
            <input type="button" value="<?php $admin->core->translate('Save'); ?>" class="btn hook_submit">
            <div class="in-progress w3d-flex save-changes-loader" style="display:none">
                <img src="<?php $admin->core->esc_url_e( W3SPEEDSTER_URL . 'assets/images/loader-gif.gif' ); ?>" alt="loader" class="loader-img">
            </div>
        </div>
    </div>

    <div class="w3d-flex gap20 filter-row">
        <div class="filter-list optai-filter-list w3d-flex gap10">
            <label for="filter-optai-rows"><?php $admin->core->translate('Rows Per Page'); ?></label>
            <select id="filter-optai-rows" name="temp_input">
                <option value="10" selected><?php $admin->core->translate('10'); ?></option>
                <option value="20"><?php $admin->core->translate('20'); ?></option>
                <option value="50"><?php $admin->core->translate('50'); ?></option>
                <option value="100"><?php $admin->core->translate('100'); ?></option>
            </select>
        </div>
        <div class="filter-list optai-filter-list w3d-flex gap10">
            <label for="filter-optai-status"><?php $admin->core->translate('Status'); ?></label>
            <select id="filter-optai-status" name="filter-optai-status">
                <option value="all" selected><?php $admin->core->translate('All'); ?></option>
                <option value="pending"><?php $admin->core->translate('Pending'); ?></option>
                <option value="in-progress"><?php $admin->core->translate('In-progress'); ?></option>
                <option value="error"><?php $admin->core->translate('Error'); ?></option>
                <option value="done"><?php $admin->core->translate('Done'); ?></option>
            </select>
        </div>
        <div class="filter-list optai-filter-list w3d-flex gap10">
            <label for="filter-optai-url"><?php $admin->core->translate('Url'); ?></label>
            <input type="text" id="filter-optai-url" name="filter-optai-url" placeholder="<?php $admin->core->translate('Enter matching url...'); ?>">
            <button type="button" class="btn" id="opt-ai-filter-btn"><?php $admin->core->translate('Filter'); ?></button>
        </div>
    </div>
    <div class="optimize-ai-data-table" id="optimize-ai-data-table">
        <table class="optimize-url-table">
            <thead>
				<tr>
					<th><?php $admin->core->translate('URL'); ?></th>
					<th style="width: 10%;"><?php $admin->core->translate('Desktop'); ?></th>
					<th style="width: 10%;"><?php $admin->core->translate('Mobile'); ?></th>
					<th style="width: 12%;"><?php $admin->core->translate('Timestamp'); ?></th>
					<th style="width: 10%;"><?php $admin->core->translate('Actions'); ?></th>
				</tr>
			</thead>
            <tbody>
                <tr>
                    <td colspan="4" style="text-align: center;"><?php $admin->core->translate('Loading...'); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="ai-optimize-container">
        <div class="ai-optimize-banner">
            <div class="ai-optimize-icon">
            <img src="<?php $admin->core->esc_url_e( W3SPEEDSTER_URL . 'assets/images/optimize-ai-icon.png' ); ?>" alt="AI Icon">
            </div>
            <div class="ai-optimize-text">
            <h3><?php $admin->core->translate('Note !'); ?></h3>
            <p><?php $admin->core->translate('Enabling Optimize with AI will automatically apply the recommended optimal settings for your website performance.'); ?></p>
            </div>
        </div>
        <h4 class="ai-optimize-title"><?php $admin->core->translate('The following optimal settings will be enabled:'); ?></h4>
        <ul class="ai-optimize-list">
            <li><span class="opt-icon">✅ <?php $admin->core->translate('Enable HTML Caching'); ?></span></li>
            <li><span class="opt-icon">✅ <?php $admin->core->translate('Turn ON optimization'); ?></span></li>
            <li><span class="opt-icon">✅ <?php $admin->core->translate('JPG to WebP Image Conversion'); ?></span></li>
            <li><span class="opt-icon">✅ <?php $admin->core->translate('PNG to WebP Image Conversion'); ?></span></li>
            <li><span class="opt-icon">✅ <?php $admin->core->translate('Enable Lazy Load (Image, Iframe, Video, Audio'); ?></span></li>
            <li><span class="opt-icon">✅ <?php $admin->core->translate('Enable CSS Optimization'); ?></span></li>
            <li><span class="opt-icon">✅ <?php $admin->core->translate('Load Critical CSS'); ?></span></li>
        </ul>
    </div>
    <?php endif; ?>
</section>