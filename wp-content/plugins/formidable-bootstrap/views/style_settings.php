<div class="menu-settings">

<h3 class="frm_no_bg"><?php _e('Bootstrap Styling') ?></h3>
<p><?php _e('Bootstrap styling', 'formidable') ?>
    <select id="frm_btsp_css" name="frm_btsp_css">
        <option value="all" <?php selected($frm_settings->btsp_css, 'all') ?>><?php _e('load on every page', 'formidable') ?></option>
        <option value="dynamic" <?php selected($frm_settings->btsp_css, 'dynamic') ?>><?php _e('only load on applicable pages', 'formidable') ?></option>
        <option value="none" <?php selected($frm_settings->btsp_css, 'none') ?>><?php _e('do not load', 'formidable') ?></option>
    </select>
</p>

<p><label for="frm_btsp_errors"><input type="checkbox" value="1" id="frm_btsp_errors" name="frm_btsp_errors" <?php checked($frm_settings->btsp_errors, 1) ?> />
    <?php _e('Show form error messages', 'formidable'); ?></label> <span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php _e('Errors messages are hidden by default. Check this box to show them.', 'formidable') ?>" ></span>
</p>
</div>
