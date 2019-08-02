<?php
/*
Plugin Name: Product Taxonomy Import Export
Plugin URI: https://wordpress.org/plugins/esb-csv-import-export/
Description: Product category, custom category import export
Version: 10.06.14
Author: ABC
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if( !defined( 'ESB_CIE_DIR' ) ) {
    define('ESB_CIE_DIR', dirname( __FILE__ ) ); // plugin dir
}
if( !defined( 'ESB_CIE_URL' ) ) {
    define('ESB_CIE_URL', plugin_dir_url( __FILE__ ) ); // plugin url
}
if( !defined( 'ESB_CIE_META_PREFIX' ) ) {
    define( 'ESB_CIE_META_PREFIX', '_esb_cie_' ); // meta box prefix
}
if( !defined('ESB_CIE_BASENAME') ){
    define('ESB_CIE_BASENAME', 'esb-csv-import-export');  // plugin base name
}

/**
 * Load Text Domain
 *
 * This gets the plugin ready for translation.
 */

function esb_cie_load_textdomain() {

  load_plugin_textdomain( 'esbcie', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

}
add_action( 'init', 'esb_cie_load_textdomain' ); 

/**
 * Activation Hook
 *
 * Register plugin activation hook.
 */
register_activation_hook( __FILE__, 'esb_cie_install' );

/**
 * Deactivation Hook
 *
 * Register plugin deactivation hook.
 */
register_deactivation_hook( __FILE__, 'esb_cie_uninstall');

/**
 * Plugin Setup (On Activation)
 *
 * Does the initial setup,
 * stest default values for the plugin options.
 */
function esb_cie_install() {
    
    //get option for when plugin is activating first time
    $esb_cie_set_option = get_option( 'esb_cie_set_option' );

    if( empty( $esb_cie_set_option ) ) { //check plugin version option

        //update plugin version to option 
        update_option( 'esb_cie_set_option', '1.0' );
    }
}

/**
 * Plugin Setup (On Deactivation)
 *
 * Delete plugin options.
 */
function esb_cie_uninstall() {
    
}

//include model file
include ESB_CIE_DIR . '/includes/esb-cie-model.php';

//include scripts file
include ESB_CIE_DIR . '/includes/esb-cie-scripts.php';

//include admin file
include ESB_CIE_DIR . '/includes/admin/esb-cie-admin.php';

?>