<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This class handles our TinyMCE toolbar button.
 *
 * @package   WooCommerce_Product_Table\Admin
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_Product_Table_Admin_TinyMCE {

	public static function setup() {
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		if ( 'true' !== get_user_option( 'rich_editing' ) ) {
			return;
		}

		add_filter( 'mce_external_plugins', array( __CLASS__, 'add_tinymce_plugin' ) );
		add_filter( 'mce_buttons_2', array( __CLASS__, 'add_tinymce_button' ) );
	}

	public static function add_tinymce_plugin( $plugins ) {
		$plugins['producttable'] = WCPT_Util::get_asset_url( 'js/admin/tinymce-product-table' . WCPT_Util::get_script_suffix() . '.js' );
		return $plugins;
	}

	public static function add_tinymce_button( $buttons ) {
		array_push( $buttons, 'producttable' );
		return $buttons;
	}

}