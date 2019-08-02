<?php
/**
 * Plugin Name: One Click Order Re-Order
 * Plugin URI: http://cedcommerce.com
 * Description: This extension is used to place the previous order again while order status is completed or not.
 * Author: CedCommerce
 * Author URI: http://cedcommerce.com
 * Text Domain: one-click-order-reorder
 * Version: 1.1.3
 * Requires at least: 3.8
 * Tested up to: 4.7.2
 * 
 * Click n Go is a plugin, supports in woocommerce Version 2.4.7 and above.
 * This extension is used to place the previous order again while order status is completed or not.
 * By installing this plugin a "Re-Order" named button will be added to your
 * My Account page next to each orders and besides to view button.
 */
if (! defined ( 'ABSPATH' )) {
	exit (); // Exit if accessed directly
}

define ( 'CNG_PREFIX', 'ced_cng' );
define ( 'CNG_VERSION', '1.1.1' );
define ( 'CNG_TXTDOMAIN', 'one-click-order-reorder' );
define ( 'CEDCOMMERCE_CNG_ORDER', plugin_dir_path ( __FILE__ ) );
define ( 'CEDCOMMERCE_CNG_ORDER_URL', plugin_dir_url ( __FILE__ ) );

$activated = true;
if (function_exists ( 'is_multisite' ) && is_multisite ()) {
	include_once (ABSPATH . 'wp-admin/includes/plugin.php');
	if (! is_plugin_active ( 'woocommerce/woocommerce.php' )) {
		$activated = false;
	}
} else {
	if (! in_array ( 'woocommerce/woocommerce.php', apply_filters ( 'active_plugins', get_option ( 'active_plugins' ) ) )) {
		$activated = false;
	}
}
/**
 * Check if WooCommerce is active
 */
if ($activated) {
	include_once CEDCOMMERCE_CNG_ORDER . 'includes/ced-click-n-go-class.php';
	include_once CEDCOMMERCE_CNG_ORDER . 'includes/class-basket-order.php';
	
	if (! function_exists ( 'ced_cng_custom_plugin_row_meta' )) {
		/**
		 * Add links of demo and documentation
		 *
		 * @param array $links
		 * @param string $file
		 * @author CedCommerce <http://cedcommerce.com>
		 */
		function ced_cng_custom_plugin_row_meta($links, $file) {
			static $plugin;
			if (! isset ( $plugin ) ) {
				$plugin = plugin_basename ( __FILE__ );
			}
			if ( $file == $plugin ) {
				$new_links = array (
					'doc' => '<a href="http://demo.cedcommerce.com/woocommerce/click-n-go/doc/index.html" target="_blank">' . __ ( 'Docs', 'one-click-order-reorder' ) . '</a>',
					'demo' => '<a href="http://demo.cedcommerce.com/woocommerce/click-n-go/my-account/" target="_blank">' . __ ( 'Live Demo', 'one-click-order-reorder' ) . '</a>'
				);

				$links = array_merge ( $links, $new_links );
			}
	
			return $links;
		}
	}
	add_filter ( 'plugin_row_meta', 'ced_cng_custom_plugin_row_meta', 10, 2 );
	
	/**
	 * This function is used to load language'.
	 * 
	 * @name ced_cng_load_text_domain()
	 * @author CedCommerce<plugins@cedcommerce.com>
	 * @link http://cedcommerce.com/
	 */
	function ced_cng_load_text_domain() {
		$domain = "one-click-order-reorder";
		$locale = apply_filters ( 'plugin_locale', get_locale(), $domain );
		load_textdomain ( $domain, CEDCOMMERCE_CNG_ORDER . 'languages/' . $domain . '-' . $locale . '.mo' );
		$var = load_plugin_textdomain ( 'one-click-order-reorder', false, plugin_basename ( dirname ( __FILE__ ) ) . '../languages' );
	}
	
	add_action ( 'plugins_loaded', 'ced_cng_load_text_domain' );
} else {
	function ced_cng_plugin_error_notice() {
		?>
		<div class="error notice is-dismissible">
			<p><?php _e( 'WooCommerce is not activated. Please install WooCommerce first, to use the One Click Order Re-Order plugin !!!', 'one-click-order-reorder' ); ?></p>
		</div>
	<?php
	}
	
	add_action ( 'admin_init', CNG_PREFIX . '_plugin_deactivate' );
	function ced_cng_plugin_deactivate() {
		deactivate_plugins ( plugin_basename ( __FILE__ ) );
		add_action ( 'admin_notices', CNG_PREFIX . '_plugin_error_notice' );
	}
}
?>