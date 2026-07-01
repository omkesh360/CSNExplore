$(function () {
    jQuery("#del_html_cache").on( "click", function() {
        jQuery('.in-progress.w3d-flex.delete_html_cache').show();
        jQuery('#del_html_cache').attr('disabled',true);
        var data = {
                    'action': 'w3_speedster_html_cache_purge',
                    '_w3nonce' : w3_speedster_html_cache_purge
                    };
        jQuery.get(adminUrl, data, function(response) {
            jQuery('#del_html_cache').attr('disabled',false);
            jQuery('.in-progress.w3d-flex.delete_html_cache').hide();
        }).fail(function() {
            jQuery('#del_html_cache').attr('disabled',false);
            jQuery('.in-progress.w3d-flex.delete_html_cache').hide();
        });
    }); 
    jQuery(".w3-speedster-cache-purge-text, #del_js_css_cache").on( "click", function() {
        jQuery('.in-progress.w3d-flex.delete_css_js_cache').show();
        jQuery('#del_js_css_cache').attr('disabled',true);
        jQuery('#w3_speedster_cache_purge').show();
        jQuery('.cache_size').addClass('deleting');
        jQuery('.w3-speedster-cache').text('Deleting...');
        var data = {
                    'action': 'w3_speedster_cache_purge',
                    '_w3nonce' : w3_speedster_cache_purge
                    };
        jQuery.get(adminUrl, data, function(response) {
            jQuery('#w3_speedster_cache_purge').hide();
            jQuery('.cache_size').removeClass('deleting');
            jQuery('.w3-speedster-cache').text('Cache Deleted!');
            jQuery('.cache_folder_size').text(response+" MB");
            jQuery('#del_js_css_cache').attr('disabled',false);
            jQuery('.in-progress.w3d-flex.delete_css_js_cache').hide();
            setTimeout(() => {
                jQuery('.w3-speedster-cache').text('W3Speedster cache');
            }, 2000);
        }).fail(function() {
            jQuery('#w3_speedster_cache_purge').hide();
            jQuery('.cache_size').removeClass('deleting');
            jQuery('.w3-speedster-cache').text('try again');
            jQuery('.cache_folder_size').text(response+" MB");
            jQuery('#del_js_css_cache').attr('disabled',false);
            jQuery('.in-progress.w3d-flex.delete_css_js_cache').hide();
            setTimeout(() => {
                jQuery('.w3-speedster-cache').text('W3Speedster cache');
            }, 2000);
        });
    });
    function confirmAction() {
        var result = confirm("Are you sure you want to proceed? Critical css may take long time to regenerate.");
        return result;
    }
    jQuery("#del_critical_css_cache,.w3-speedster-critical-cache-purge-text,.w3-speedster-critical-cache-purge-single-text").on( "click", function() {
        jQuery('.in-progress.w3d-flex.delete_critical_css_cache').show();
        if(!confirmAction()){
            jQuery('.in-progress.w3d-flex.delete_critical_css_cache').hide();
            return false;
        }
        jQuery('#w3_speedster_cache_purge').show();
        jQuery('.cache_size').addClass('deleting');
        jQuery('#del_critical_css_cache').attr('disabled',true);
        jQuery('.w3-speedster-cache').text('Deleting...');
        var data_id = jQuery(this).attr("data-id");
        var data_type = jQuery(this).attr("data-type");
        var data = {
                    'action': 'w3_speedster_critical_cache_purge',
                    'data_id':data_id,
                    'data_type':data_type,
                    '_w3nonce' : w3_speedster_critical_cache_purge
                    };

        jQuery.get(adminUrl, data, function(response) {
            jQuery('#del_critical_css_cache').attr('disabled',false);
            jQuery('#w3_speedster_cache_purge').hide();
            jQuery('.cache_size').removeClass('deleting');
            jQuery('.w3-speedster-cache').text('Cache Deleted!');
            jQuery('.in-progress.w3d-flex.delete_critical_css_cache').hide();
            window.location.reload();
        }).fail(function() {
            jQuery('#del_critical_css_cache').attr('disabled',false);
            jQuery('#w3_speedster_cache_purge').hide();
            jQuery('.cache_size').removeClass('deleting');
            jQuery('.w3-speedster-cache').text('try again');
            jQuery('.in-progress.w3d-flex.delete_critical_css_cache').hide();
            window.location.reload();
        });

    });
});
