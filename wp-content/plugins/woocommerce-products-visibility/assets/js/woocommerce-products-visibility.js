jQuery(function () {
    jQuery('.wcpv-select-category').select2();
    jQuery('.wcpv-select-tag').select2();

    jQuery('a.wcpv-toggle-roles').on('click', function () {
        jQuery('.wcpv-form').find('table.form-table, h2.wcpv-role-sections').toggleClass('wcpv-open');
    });

    jQuery('h2.wcpv-role-sections').on('click', function () {
        jQuery(this).toggleClass('wcpv-open');
        jQuery(this).next('table.form-table').toggleClass('wcpv-open');
    });

    jQuery('.wcpv-select-category, .wcpv-select-tag, input.wc-product-search.wcpv-product-search, input.wcpv-hidden-product-search').each(function () {
        if (jQuery(this).val()) {
            var row = jQuery(this).closest("tr").prevAll("tr.wcpv-subsection-row:first");
            jQuery(row).find("div.wcpv-subsection").addClass("Active");
            var role_subsection = jQuery(row).closest("table.form-table").prevAll("h2.wcpv-role-sections:first");
            if (!jQuery(role_subsection).hasClass("Active"))
            {
                jQuery(role_subsection).addClass("Active");
            }
        }
    });

    // Reset Settings
    jQuery('.wcpv-reset-button').click(function () {
        var holder = jQuery('#mainform');
        var c = confirm(WCPV_vars.reset_confirm_message);
        if (c)
        {
            holder.addClass('disabled');
            holder.after('<div class="wcpv_spinner"></div>');
            var data = {
                'action': 'WCPV_reset_settings'
            };
            jQuery.post(ajaxurl, data, function (response) {
                if (response) {
                    location.reload();
                }
            });
        }
        return false; //prevent post
    });


    // Script for Woocomerce version 3
    jQuery('select.wc-product-search.wcpv-product-search').on('change', function () {
        var input_id = jQuery(this).data('wcpv-products-input-id');
        var val = "";
        if (jQuery(this).val() != null)
             val = jQuery(this).val().join();
        jQuery("#" + input_id).val(val);
    });
});