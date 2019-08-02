<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WCPV_BACKEND_RESET_SETTINGS {

    public function __construct() {
        add_action('wp_ajax_WCPV_reset_settings', array($this, 'WCPV_reset_settings'));
        add_action('wcpv_reset_rules', array($this, 'WCPV_reset_settings')); // add action for demo cron job
    }

    public function WCPV_reset_settings() {
        global $wp_roles;
        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }
        $getdata = $wp_roles->get_names();
        $getdata['guest'] = __('Guest', 'woocommerce-products-visibility'); // Add guest
        $getdata['default'] = __('Default', 'woocommerce-products-visibility'); // Add default
        delete_option('wcpv_multiple_roles');
        delete_option('show_product_through_direct_url');
        foreach ($getdata as $key => $data) {
            delete_option('wcpv_products_visibility_' . $key);
            delete_option('wcpv_products_' . $key);
            delete_option('wcpv_tags_visibility_' . $key);
            delete_option('wcpv_tags_' . $key);
            delete_option('wcpv_categories_visibility_' . $key);
            delete_option('wcpv_categories_' . $key);
            delete_option('wcpv_role_priority_' . $key);
        }
        echo true;
        wp_die();
    }

}
