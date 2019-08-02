<?php

/**
 * Plugin Name: WooCommerce Products Visibility
 * Plugin URI: https://themeforest.net/user/codemine
 * Description: Hide or show products for each user role
 * Author: codemine
 * Author URI: https://themeforest.net/user/codemine
 * Version: 2.7
 * Text Domain: woocommerce-products-visibility
 * Domain Path: /languages/
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WooCommerce_Products_Visibility {

    public function __construct() {
        add_action('plugins_loaded', array($this, 'init'), 100000);
    }

    /**
     * Init Plugin Backend/Frontend
     */
    public function init() {
        //Check for dependencies Constants.
        include_once('includes/class-wcpv-dependencies.php');
        include_once('includes/class-wcpv-roles-priority.php');
        $this->dependencies = new WCPV_Dependencies;
        $this->check_dependencies = $this->dependencies->check();

        if ($this->check_dependencies) {
            if (is_admin()) {
                //Define Constants.
                self::define_constants();

                //Init Backend
                include_once('includes/class-wcpv-backend.php');
                $backend = new WCPV_BACKEND();
                $backend->init();
                load_plugin_textdomain('woocommerce-products-visibility', false, dirname(plugin_basename(__FILE__)) . '/languages/');
            }
            if ($this->request_is_frontend_ajax() || !is_admin()) {
                include_once('includes/class-wcpv-frontend.php');
                WCPV_FRONTEND::get_instance();
            }
        }
    }

    /**
     * Define Constants.
     */
    private static function define_constants() {
        define('WooCommerce_Products_Visibility_PLUGIN_FILE', __FILE__);
        define('WooCommerce_Products_Visibility_VERSION', '2.7');
    }

    function request_is_frontend_ajax() {
        $script_filename = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '';

        //Try to figure out if frontend AJAX request... If we are DOING_AJAX; let's look closer
        if ((defined('DOING_AJAX') && DOING_AJAX)) {
            //From wp-includes/functions.php, wp_get_referer() function.
            //Required to fix: https://core.trac.wordpress.org/ticket/25294
            $ref = '';
            if (!empty($_REQUEST['_wp_http_referer']))
                $ref = wp_unslash($_REQUEST['_wp_http_referer']);
            elseif (!empty($_SERVER['HTTP_REFERER']))
                $ref = wp_unslash($_SERVER['HTTP_REFERER']);

            //If referer does not contain admin URL and we are using the admin-ajax.php endpoint, this is likely a frontend AJAX request
            if (((strpos($ref, admin_url()) === false) && (basename($script_filename) === 'admin-ajax.php'))) {
                return true;
            }
        }

        //If no checks triggered, we end up here - not an AJAX request.
        return false;
    }

}

new WooCommerce_Products_Visibility();
