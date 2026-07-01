<section id="w3ChangeLog" class="tab-pane fade<?php echo $tab == 'w3ChangeLog' ? ' active in' : ''; ?>">
    <div class="header w3d-flex gap20">
        <div class="heading_container">
            <h4 class="w3heading"><?php $admin->core->translate('Change Logs'); ?>
            </h4>
            <span class="w3info"><a href="https://w3speedster.com/w3speedster-documentation/"><?php $admin->core->translate('More info'); ?> ?</a></span>
        </div>
        <div class="icon_container"> <img src="<?php $admin->core->esc_url_e( W3SPEEDSTER_URL . 'assets/images/logs-icon.webp' ); ?>" ></div>
    </div>
    <hr>
    <div class="w3d-flex gap20 
        <?php $admin->core->esc_attr_e($hidden_class); ?>">
        <label><?php $admin->core->translate('Enable Settings Change Logs'); ?>
            <span class="w3info"></span><span class="w3info-display"><?php $admin->core->translate('Enable to Log Settings Change Logs.'); ?></span>
        </label>
        <div class="input_box">
            <label class="switch" for="enable-change-log">
                <input type="checkbox" name="change_logs" 
                    <?php if (!empty($result['change_logs']) && $result['change_logs'] == "on") $admin->core->esc_attr_e("checked"); ?> id="enable-change-log" >
                <div class="checked"></div>
            </label>
        </div>
    </div>
    <?php if (empty($result['change_logs'])) { ?>
        <p class="alert_message"><?php $admin->core->translate('Enable Change Log option for Settings Change Logging'); ?></p>
    <?php } else { ?>
        <div class="w3d-flex gap20 filter-row">
            <div class="show_log w3d-flex gap10">
                <label for="show_change_log_entry"><?php $admin->core->translate('Show'); ?></label>
                <select name="temp_input" id="show_change_log_entry" class="show_change_log_entry">
                    <option value="10"><?php $admin->core->translate('10'); ?></option>
                    <option value="20"><?php $admin->core->translate('20'); ?></option>
                    <option value="30"><?php $admin->core->translate('30'); ?></option>
                    <option value="40"><?php $admin->core->translate('40'); ?></option>
                    <option value="50"><?php $admin->core->translate('50'); ?></option>
                </select>
            </div>
            <div class="delete-log-data w3d-flex gap10">
                <label for="changelog_delete_time"><?php $admin->core->translate('Delete Logs'); ?></label>
                <select id="changelog_delete_time" name="temp_input">
                    <option value=""><?php $admin->core->translate('Select Log Time'); ?></option>
                    <option value="last7days"><?php $admin->core->translate('Keep last 7 Days'); ?></option>
                    <option value="lastMonth"><?php $admin->core->translate('Keep last 30 Days'); ?></option>
                    <option value="last3months"><?php $admin->core->translate('Keep last 90 Days'); ?></option>
                    <option value="last6months"><?php $admin->core->translate('Keep last 180 Days'); ?></option>
                    <option value="all"><?php $admin->core->translate('All'); ?></option>
                </select>
                <button type="button" class="btn btn-changelog-delete"><?php $admin->core->translate('Delete'); ?></button>
            </div>
        </div>
        <div class="w3d-flex gap10 filter-row">
            <div class="filter_by_date w3d-flex gap10">
                <label for="changelog_start_date"><?php $admin->core->translate('From'); ?></label>
                <input type="text" name="temp_input" class="changelog_start_date">
                <label for="changelog_end_date"><?php $admin->core->translate('To'); ?></label>
                <input type="text" name="temp_input" class="changelog_end_date">
            </div>
            <button type="button" class="btn btn-changelog-apply-filter"><?php $admin->core->translate('Apply Filters'); ?></button>
            <button type="button" class="btn btn-changelog-rem-filter"><?php $admin->core->translate('Clear'); ?></button>
        </div>
        <div class="change-log-data-table">
            <?php echo $admin->core->w3SpeedsterGetChangeLogData(); ?>
        </div>
    <?php } ?>
</section>
