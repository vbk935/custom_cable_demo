<?php
/**
 * Uninstall routine.
 *
 * @package WC_Shipping_UPS
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete saved options when UPS plugin is deleted through the WordPress Admin.
if ( get_option( 'woocommerce_ups_settings' ) ) {
	delete_option( 'woocommerce_ups_settings' );
}
