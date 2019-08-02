<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WCPV_CANCEL_HIDE_VISIBLE {

    public $default_users_are_set = false;
    public $multiple_user_roles_exist = false;
    public $unhide_set = false;

    public function __construct() {

        global $wp_roles;
        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }
        $getdata = $wp_roles->get_names();
        $getdata['default'] = __('Default Logged User', 'woocommerce-products-visibility'); // Add default (logged-in)
        $getdata['guest'] = __('Guest', 'woocommerce-products-visibility'); // Add guest

        foreach ($getdata as $key => $data) {
            wp_cache_delete('wcpv_products_' . $key, 'options');
            wp_cache_delete('wcpv_products_visibility_' . $key, 'options');
            wp_cache_delete('wcpv_tags_' . $key, 'options');
            wp_cache_delete('wcpv_tags_visibility_' . $key, 'options');
            wp_cache_delete('wcpv_categories_' . $key, 'options');
            wp_cache_delete('wcpv_categories_visibility_' . $key, 'options');
        }
        $this->multiple_user_roles_exist = $this->check_if_multiple_user_roles_exist();
        if (!$this->multiple_user_roles_exist) {
            $this->default_users_are_set = $this->check_if_default_users_are_set();
        }
        if (!$this->multiple_user_roles_exist && !$this->default_users_are_set) {
            $this->unhide_set = $this->check_if_unhide_set();
        }
    }

    public function get_is_visible() {
        return $this->default_users_are_set || $this->multiple_user_roles_exist || $this->unhide_set;
    }

    public function check_if_multiple_user_roles_exist() {
        return get_option('wcpv_multiple_roles');
    }

    private function check_if_default_users_are_set() {
        $productids = array_filter((array) explode(",", get_option('wcpv_products_default')));
        if (!empty($productids)) {
            return 1;
        }
        $categoryids = array_filter((array) get_option('wcpv_categories_default'));
        if (!empty($categoryids)) {
            return 1;
        }
        $tagids = array_filter((array) get_option('wcpv_tags_default'));
        if (!empty($tagids)) {
            return 1;
        }
        return 0;
    }

    private function check_if_unhide_set() {
        global $wp_roles;
        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }
        $getdata = $wp_roles->get_names();
        $getdata['default'] = __('Default Logged User', 'woocommerce-products-visibility'); // Add default (logged-in)
        $getdata['guest'] = __('Guest', 'woocommerce-products-visibility'); // Add guest

        foreach ($getdata as $key => $data) {
            $productids = array_filter((array) explode(",", get_option('wcpv_products_' . $key)));
            $products_cancel_exclude = get_option('wcpv_products_visibility_' . $key) == "3";
            if ((!empty($productids)) && $products_cancel_exclude)
                return 1;

            $tagids = array_filter((array) get_option('wcpv_tags_' . $key));
            $tags_cancel_exclude = get_option('wcpv_tags_visibility_' . $key) == "3";
            if ((!empty($tagids)) && $tags_cancel_exclude)
                return 1;

            $categoryids = array_filter((array) get_option('wcpv_categories_' . $key));
            $categories_cancel_exclude = get_option('wcpv_categories_visibility_' . $key) == "3";
            if ((!empty($categoryids)) && $categories_cancel_exclude)
                return 1;
        }
        return 0;
    }

}
