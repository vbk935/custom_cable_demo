<?php
error_reporting(0);
include( plugin_dir_path( __FILE__ ) . 'for_new_tab_creation.php');
/**
 * Plugin Name: Custom Cable Configuration
 * Plugin URI: http://www.graycelltech.com/
 * Description: This plugin adds custom configuration for cables.
 * Version: 10.0.0
 * Author: GrayCell
 * Author URI: http://www.graycelltech.com/
 */


/* * ******************* BACKEND *************************** */

/* * ****************** PRODUCT GROUPING SHORTCODES ************************* */

/* Create Taxonomy for configuration */
include( plugin_dir_path( __FILE__ ) . 'includes/backend/configuration_tax.php');

/* Create Taxonomy for Grouping */
include( plugin_dir_path( __FILE__ ) . 'includes/backend/group_tax.php');


/* * ****************** CONFIGURATION CUSTOM FIELDS ************************* */

/* Add Custom Field For Configuration */
include( plugin_dir_path( __FILE__ ) . 'includes/backend/custom_fields/configuration.php');


/* Add Custom Field For Groups */
include( plugin_dir_path( __FILE__ ) . 'includes/backend/custom_fields/group.php');


/* Product MetaBox */
include( plugin_dir_path( __FILE__ ) . 'includes/backend/product_metabox.php');


/* Order MetaBox */
include( plugin_dir_path( __FILE__ ) . 'includes/backend/order_metabox.php');


/* MetaBox Callback */
include( plugin_dir_path( __FILE__ ) . 'includes/backend/metabox_callback.php');


/* Configuration Conditions */
//include( plugin_dir_path( __FILE__ ) . 'includes/backend/configuration_conditions.php');

/* Canvas Setting */
//include( plugin_dir_path( __FILE__ ) . 'includes/backend/canvas_setting_conduction.php');

/* Components */
include( plugin_dir_path( __FILE__ ) . 'includes/backend/components.php');

/* Cable Pricing  */
include( plugin_dir_path( __FILE__ ) . 'includes/backend/cable_pricing.php');


wp_enqueue_script('autocomplete', plugins_url('/js/jquery.auto-complete.js', __FILE__));

wp_enqueue_style('autocomplete.css', plugins_url('/css/jquery.auto-complete.css', __FILE__));


wp_enqueue_script('cableconfiguration', plugins_url('/js/admin_custom_jquery.js', __FILE__));

/* * ****************** FRONTEND CODE ************************* */

/* * ****************** FRONTEND STYLE ************************* */

/* Add Style */
if(!is_admin()) {
	wp_enqueue_style('cableconfiguration', plugins_url('/css/style.css', __FILE__));
}
else
{
	wp_enqueue_style('cableconfiguration', plugins_url('/css/old_style.css', __FILE__));
	wp_enqueue_script('cableconfiguration', plugins_url('/js/admin_custom_jquery.js', __FILE__));
	wp_localize_script('cableconfiguration', 'cableconfiguration', array('pluginsUrl' => plugins_url('', __FILE__)));
}
wp_enqueue_style('cableconfiguration', plugins_url('/css/font-awesome.css', __FILE__));
wp_enqueue_script('cableconfiguration', plugins_url('/js/bootstrap.min.js', __FILE__));


/* Shortcode for frontend [cable_configuration] Config term ID can be passed in shortcode or as Query String
 *  [cable_configuration config="config_id"]
 *  Config Url.com?config=config_ID
*/


/************* Add Custom Template ******************/
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/pagetemplater.php');
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/goottobebbad-template.php');


/* * ****************** PRODUCT GROUPING SHORTCODES ************************* */
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/cable_configration.php');

//wp_enqueue_script('orders', plugin_dir_url(__FILE__) . 'orders.js');

/* * ****************** PRODUCT GROUPING SHORTCODES ************************* */
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/product_grouping.php');
//include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/product_grouping_mock_up.php');  //for design mock up only.





/* * ****************** WooCommerce Extra Fields ************************* */

include( plugin_dir_path( __FILE__ ) . 'includes/woocommerce_extra_fields.php');

function edit_admin_menus() {
    //global $menu;
	global $submenu;


	//echo "<pre>"; print_r($submenu);echo '</pre>';
	
	$arr = array();
	$arr[] = $submenu['edit.php?post_type=product'][16];  // Groups Menu 
	$arr[] = $submenu['edit.php?post_type=product'][15];  // Configuration Menu
	$arr[] = $submenu['edit.php?post_type=product'][21]; 	// Components Menu
	$arr[] = $submenu['edit.php?post_type=product'][24]; 	// Cable Pricing Menu
	$arr[] = $submenu['edit.php?post_type=product'][5];		// Products Menu 
	$arr[] = $submenu['edit.php?post_type=product'][10];	// Add New Product Menu
	$arr[] = $submenu['edit.php?post_type=product'][17];	// Categories Menu
	$arr[] = $submenu['edit.php?post_type=product'][18];	// Tags Menu
	$arr[] = $submenu['edit.php?post_type=product'][19];	// Attributes Menu
	$arr[] = $submenu['edit.php?post_type=product'][20];	// Import-Export Menu
  $submenu['edit.php?post_type=product'] = $arr;
	//print_r($submenu['edit.php?post_type=product']);
}
add_action( 'admin_menu', 'edit_admin_menus' );
// create the table of configuration conditions
function create_configuration_conditions_database_table() {

	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'configuration_conditions';
	if($wpdb->get_var( "show tables like '$table_name'" ) != $table_name) 
	{
		$sql = "CREATE TABLE $table_name (
		`cid` BIGINT(20) NOT NULL AUTO_INCREMENT,
		`group_id` BIGINT(20) NOT NULL,
		`primary_parent_id` BIGINT(20) UNSIGNED NOT NULL,
		`primary_child_id` BIGINT(20) NOT NULL,
		`conductions` LONGTEXT NULL COLLATE 'utf8mb4_unicode_520_ci',
		`pageNumber` INT(11) NULL DEFAULT NULL,
		`created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (`cid`),
		UNIQUE INDEX `group_id` (`group_id`, `primary_parent_id`, `primary_child_id`),
		INDEX `FK_".$wpdb->prefix."configuration_conditions_".$wpdb->prefix."terms` (`primary_parent_id`),
		CONSTRAINT `FK_".$wpdb->prefix."configuration_conditions_".$wpdb->prefix."terms` FOREIGN KEY (`primary_parent_id`) REFERENCES `".$wpdb->prefix."terms` (`term_id`) ON UPDATE CASCADE ON DELETE CASCADE
		)
		COMMENT='Table is used for the conduction for the configruration'
		COLLATE='utf8mb4_unicode_520_ci'
		ENGINE=InnoDB;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		$charset_collate = $wpdb->get_charset_collate();
	}
	$table_name = $wpdb->prefix . 'canvas_setting';
	if($wpdb->get_var( "show tables like '$table_name'" ) != $table_name) 
	{
		$sql = "CREATE TABLE $table_name (
		`canvas_id` BIGINT(20) NOT NULL AUTO_INCREMENT,
		`group_id` BIGINT(20) NOT NULL,
		`primary_parent_id` BIGINT(20) UNSIGNED NOT NULL,
		`position` VARCHAR(100) NOT NULL,
		`created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (`canvas_id`),
		UNIQUE INDEX `group_id` (`group_id`, `primary_parent_id`, `position`),
		INDEX `FK_".$wpdb->prefix."canvas_setting_".$wpdb->prefix."terms` (`primary_parent_id`),
		CONSTRAINT `FK_".$wpdb->prefix."canvas_setting_".$wpdb->prefix."terms` FOREIGN KEY (`primary_parent_id`) REFERENCES `".$wpdb->prefix."terms` (`term_id`) ON UPDATE CASCADE ON DELETE CASCADE
		)
		COMMENT='Table is used for the canvas coordinate setting'
		COLLATE='utf8mb4_unicode_520_ci'
		ENGINE=InnoDB;";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

	}
}
//register_activation_hook( __FILE__, 'create_configuration_conditions_database_table' );

add_action( 'init', 'process_post' );

function process_post() {
     if ( is_user_logged_in() ) 
    {
        $user = wp_get_current_user();
        $user_meta =  get_user_meta($user->ID, 'account_status');
        //echo "<pre>";print_r($user_meta);die("here");
        if($user_meta[0] == 'rejected' || $user_meta[0] == 'awaiting_admin_review' || $user_meta[0] == 'inactive')
        {
			if ( isset( $_REQUEST['redirect_to'] ) && $_REQUEST['redirect_to'] !== '' ) { 
				wp_logout(); 
				session_unset(); 
				exit( wp_redirect( $_REQUEST['redirect_to'] ) ); 
			} else if ( um_user('after_logout') == 'redirect_home' ) { 
				wp_logout(); 
				session_unset(); 
				exit( wp_redirect( home_url( $language_code ) ) ); 
			} else { 
				wp_logout(); 
				session_unset(); 
				exit( wp_redirect( um_user('logout_redirect_url') ) ); 
				 
			} 
		}        
    }
}


?>
