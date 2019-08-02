<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WCPV_BACKEND {

    private static $wcpv_renderers;

    public static function init() {
        //initialize Renderers
        include_once('class-wcpv-renderers.php');
        self::$wcpv_renderers = new WCPV_Renderers();

        add_filter('woocommerce_get_sections_products', __CLASS__ . '::add_section');
        add_filter('woocommerce_get_settings_products', __CLASS__ . '::add_section_settings', 10, 2);
        add_action('woocommerce_update_options_wcpv_tab', __CLASS__ . '::update_settings');
        add_filter('woocommerce_json_search_found_products', __CLASS__ . '::products_current_language', 10, 1);

        // Enqueue scripts
        add_action('admin_enqueue_scripts', __CLASS__ . '::admin_enqueue_scripts');

        //Add new ajax event for products select2
        include_once('class-wcpv-backend-products-select2-ajax.php');
        $wcpv_ajax = new WCPV_BACKEND_PRODUCTS_SELECT2_AJAX();
        $wcpv_ajax->add_ajax_events();

        //Add new ajax event for reset button
        include_once('class-wcpv-backend-reset-settings.php');
        new WCPV_BACKEND_RESET_SETTINGS();
    }

    public static function admin_enqueue_scripts() {
        if (
                ( isset($_REQUEST['post']) && 'wcpv' == get_post_type($_REQUEST['post']) ) ||
                ( isset($_REQUEST['post_type']) && 'wcpv' == $_REQUEST['post_type'] ) ||
                ( isset($_REQUEST['section']) && in_array($_REQUEST['section'], array('wcpv')) )
        ) {
            wp_enqueue_style('woocommerce-products-visibility-css', plugins_url('assets/css/woocommerce-products-visibility.css', WooCommerce_Products_Visibility_PLUGIN_FILE), array(), WooCommerce_Products_Visibility_VERSION); // Style script
            wp_enqueue_script('woocommerce-products-visibility-js', plugins_url('assets/js/woocommerce-products-visibility.js', WooCommerce_Products_Visibility_PLUGIN_FILE), array('jquery', 'select2'), WooCommerce_Products_Visibility_VERSION); // Javascript

            self::localize_script();
        }
    }

    public static function localize_script() {
        $js_vars = array();
        $js_vars['reset_confirm_message'] = __('Are you sure you want to delete all rules?', 'woocommerce-products-visibility');
        wp_localize_script('woocommerce-products-visibility-js', 'WCPV_vars', $js_vars);
    }

    public static function add_section($sections) {
        $sections['wcpv'] = __('Products Visibility', 'woocommerce-products-visibility');
        return $sections;
    }

    public static function add_section_settings($settings, $current_section) {

        $wpml_condition = 0;
        if (function_exists('icl_object_id')) {
            $wpml_options = get_option('icl_sitepress_settings');
            if (isset($wpml_options['default_language']) && ICL_LANGUAGE_CODE != $wpml_options['default_language']) {
                $wpml_condition = 1;
            }
        }
        if ($current_section == 'wcpv') {
            if ($wpml_condition) {
                echo '<div class="wcpv_title">' . __('Products Visibility', 'woocommerce-products-visibility') . '</div>';
                $GLOBALS['hide_save_button'] = true; // Hide save button
                global $sitepress;
                $def_lang_code = $sitepress->get_default_language();
                $def_lang_url = get_admin_url() . 'admin.php?page=wc-settings&tab=products&section=wcpv&lang=' . $def_lang_code;
                ?>  
                <div class="wcpv error inline">
                    Please select <a href="<?php echo $def_lang_url; ?>">default language</a> to edit product visibility rules.
                </div>
                <?php
                $settings = array();
                return $settings;
            } else {

                return self::get_settings();
            }
        } else {
            return $settings;
        }
    }

    public static function update_settings() {
        woocommerce_update_options(self::get_settings());
    }

    private static function get_settings() {

        include_once('class-wcpv-cancel_hide_visible.php');
        $wcpv_cancel_hide_visible = new WCPV_CANCEL_HIDE_VISIBLE();
        $cancel_hide_visible = $wcpv_cancel_hide_visible->get_is_visible();
        $check_if_multiple_user_roles_exist = $wcpv_cancel_hide_visible->multiple_user_roles_exist;

        $rolesPriority = new WCPV_ROLES_PRIORITY();
        $categories = self::get_categories();
        $tags = self::get_tags();
        $updated_settings = array();

        global $wp_roles;
        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }

        $getroles = $wp_roles->get_names();
        if ($check_if_multiple_user_roles_exist) {
            uksort($getroles, array($rolesPriority, "sort_by_priority"));
        }
        
        $getdata['default'] = __('Default Logged User', 'woocommerce-products-visibility'); // Add default (logged-in)
        $getdata = $getdata + $getroles;
        $getdata['guest'] = __('Guest', 'woocommerce-products-visibility'); // Add guest

        $countProducts = wp_count_posts('product');
        $countProducts = $countProducts->publish;
        $data_minimum_input_length = "1-reset";
        $data_action = "woocommerce_wcpv_json_search_products";
        if ($countProducts > 100)
            $data_action = "woocommerce_json_search_products";
        if ($countProducts > 100 && $countProducts <= 300)
            $data_minimum_input_length = "1";
        if ($countProducts > 300 && $countProducts <= 600)
            $data_minimum_input_length = "2";
        if ($countProducts > 600)
            $data_minimum_input_length = "3";

        $updated_settings[] = self::$wcpv_renderers->header();
        $updated_settings[] = self::$wcpv_renderers->form_open();
        $updated_settings[] = self::$wcpv_renderers->div_open();
        $updated_settings[] = self::$wcpv_renderers->enable_multiroles('wcpv_multiple_roles', __('Multiple User Roles Compatibility', 'woocommerce-products-visibility'));
        $updated_settings[] = self::$wcpv_renderers->div_close();
        $updated_settings[] = self::$wcpv_renderers->div_open();
        $updated_settings[] = self::$wcpv_renderers->show_product_through_direct_url('show_product_through_direct_url', __('Show Products Through Direct URL', 'woocommerce-products-visibility'));
        $updated_settings[] = self::$wcpv_renderers->div_close();
        foreach ($getdata as $data => $key) {

            $default_priority = $rolesPriority->get_default_priority($data);

            $priority = get_option('wcpv_role_priority_' . $data, $default_priority);

            $key = translate_user_role($key);
            if ($data != 'default' && $data != 'guest' && $check_if_multiple_user_roles_exist) {
                $updated_settings[] = self::$wcpv_renderers->role_section($data, $key . '<span class="priority_title">' . __('Priority', 'woocommerce-products-visibility') . ': ' . $priority . '</span>');
            } else if ($data == 'default') {
                $updated_settings[] = self::$wcpv_renderers->role_section($data, $key . '<span class="priority_title">' . __('Top Priority', 'woocommerce-products-visibility') . '</span>');
            } else {
                $updated_settings[] = self::$wcpv_renderers->role_section($data, $key);
            }
            $updated_settings[] = self::$wcpv_renderers->title_inner($data);
            if ($data != 'default' && $data != 'guest' && $check_if_multiple_user_roles_exist) {
                $updated_settings[] = self::$wcpv_renderers->priority_input('wcpv_role_priority_' . $data, __('Role Priority', 'woocommerce-products-visibility'), $default_priority);
            }
            $updated_settings[] = self::$wcpv_renderers->subsection(__('Products', 'woocommerce'));
            $updated_settings[] = self::$wcpv_renderers->show_hide_radio('wcpv_products_visibility_' . $data, __('Show / Hide Product(s)', 'woocommerce-products-visibility'), $cancel_hide_visible);
            $updated_settings[] = self::$wcpv_renderers->products('wcpv_products_' . $data, __('Select Product(s)', 'woocommerce-products-visibility'), __('Rule priority', 'woocommerce-products-visibility') . ': 1', $data_minimum_input_length, $data_action);
            $updated_settings[] = self::$wcpv_renderers->subsection(__('Tags', 'woocommerce'));
            $updated_settings[] = self::$wcpv_renderers->show_hide_radio('wcpv_tags_visibility_' . $data, __('Show / Hide Tags', 'woocommerce-products-visibility'), $cancel_hide_visible);
            $updated_settings[] = self::$wcpv_renderers->tags('wcpv_tags_' . $data, __('Select Tags', 'woocommerce-products-visibility'), __('Rule priority', 'woocommerce-products-visibility') . ': 2', $tags);
            $updated_settings[] = self::$wcpv_renderers->subsection(__('Categories', 'woocommerce'));
            $updated_settings[] = self::$wcpv_renderers->show_hide_radio('wcpv_categories_visibility_' . $data, __('Show / Hide Categories', 'woocommerce-products-visibility'), $cancel_hide_visible);
            $updated_settings[] = self::$wcpv_renderers->categories('wcpv_categories_' . $data, __('Select Categories', 'woocommerce-products-visibility'), __('Rule priority', 'woocommerce-products-visibility') . ': 3', $categories);
            $updated_settings[] = self::$wcpv_renderers->sectionend('wcpv_section_' . $data);
        }
        $updated_settings[] = self::$wcpv_renderers->reset_button();
        $updated_settings[] = self::$wcpv_renderers->form_close();
        return apply_filters('wc_products_visibility_settings', $updated_settings);
    }

    public static function products_current_language($found_products) {
        if (is_admin() && function_exists('icl_object_id')) {
            $get_products_default_language = self::get_products_default_language();
            $found_products = array_intersect_key($found_products, array_flip($get_products_default_language));
        }
        return $found_products;
    }

    private static function get_products_default_language() {
        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'product',
            'suppress_filters' => false,
            'hide_empty' => 'false',
            'fields' => 'ids',
        );
        $get_terms_product = get_posts($args);
        return $get_terms_product;
    }

    private static function get_tags() {
        $args = array(
            'hide_empty' => 'false',
            'fields' => 'id=>name',
        );
        $get_terms_product = get_terms('product_tag', $args);
        return $get_terms_product;
    }

    private static function get_categories() {
        $args = array(
            'hide_empty' => 'false',
            'fields' => 'id=>name',
        );
        $get_terms_product = get_terms('product_cat', $args);
        return $get_terms_product;
    }

}
