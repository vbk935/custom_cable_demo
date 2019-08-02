<?php

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles general admin functions, such as adding links to our settings page in the Plugins menu.
 *
 * @package   WooCommerce_Product_Table\Admin
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_Product_Table_Admin_Controller {

	private $settings_page;

	public function __construct( Barn2_Plugin_License $license ) {

		if ( WCPT_Util::is_wc_active() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );
			$this->settings_page = new WC_Product_Table_Admin_Settings_Page( $license );

			// Add settings link from Plugins page.
			add_filter( 'plugin_action_links_' . WCPT_PLUGIN_BASENAME, array( $this, 'plugin_page_action_links' ) );
		}

		// Add documentation link to Plugins page.
		add_filter( 'plugin_row_meta', array( $this, 'plugin_page_row_meta' ), 10, 2 );
	}

	public function plugin_page_action_links( $links ) {
		array_unshift( $links, sprintf( '<a href="%1$s">%2$s</a>', admin_url( 'admin.php?page=wc-settings&tab=products&section=' . WCPT_Settings::SECTION_SLUG ), __( 'Settings', 'woocommerce-product-table' ) ) );
		return $links;
	}

	public function plugin_page_row_meta( $links, $file ) {
		if ( WCPT_PLUGIN_BASENAME !== $file ) {
			return $links;
		}

		$row_meta = array(
			'docs' => sprintf( '<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>', esc_url( 'https://barn2.co.uk/kb-categories/woocommerce-product-table-kb/' ), esc_attr__( 'View WooCommerce Product Table documentation', 'woocommerce-product-table' ), esc_html__( 'Docs', 'woocommerce-product-table' ) )
		);

		return array_merge( $links, $row_meta );
	}

	public function register_admin_scripts( $hook_suffix ) {
		if ( 'woocommerce_page_wc-settings' !== $hook_suffix ) {
			return;
		}

		$suffix = WCPT_Util::get_script_suffix();

		wp_enqueue_style( 'wcpt-admin', WCPT_Util::get_asset_url( "css/admin/wc-product-table-admin{$suffix}.css" ), array(), WC_Product_Table_Plugin::VERSION );
		wp_enqueue_script( 'wcpt-admin', WCPT_Util::get_asset_url( "js/admin/wc-product-table-admin{$suffix}.js" ), array( 'jquery' ), WC_Product_Table_Plugin::VERSION, true );
	}

}
