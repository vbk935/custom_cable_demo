<?php
/*
 	Plugin Name: Sea Spider - Generic
 	Plugin URI: http://http:bigmarlingroup.com  
 	Description: Functionality Plugin
 	Version:  1.0.0
 	Author: Big Marlin Group
 	Author URI: http://bigmarlingroup.com
 	Text Domain: bmg
 	Network: false
 	License:  GPL2
 */
 
if(!defined('WPINC')){ die;}

// 1. customize ACF path
/*add_filter('acf/settings/path', 'my_acf_settings_path');
function my_acf_settings_path( $path ) {
    $path = plugin_dir_path( __FILE__ ) . '/advanced-custom-fields-pro/';
    return $path;    
}
add_filter('acf/settings/dir', 'my_acf_settings_dir');
function my_acf_settings_dir( $dir ) {
    $dir = plugin_dir_path( __FILE__ ) . '/advanced-custom-fields-pro/';
    return $dir;
}*/
//include_once( plugin_dir_path( __FILE__ )  . '/advanced-custom-fields-pro/acf.php' );
 

/*if( function_exists('acf_add_options_page') ) {
	$page = acf_add_options_page(array(
		'page_title' 	=> 'Site Settings',
		'menu_title' 	=> 'Site Settings',
		'menu_slug' 	=> 'site-settings',
		'capability' 	=> 'edit_posts',
		'redirect' 	=> false
	)); 
}

if( function_exists('acf_add_options_sub_page') ) {
	acf_add_options_sub_page(array(
		'title' => 'Header & Footer',
		'parent' => 'site-settings',
		'capability' => 'edit_posts'
	));
}
if( function_exists('acf_add_options_sub_page') ) {
	acf_add_options_sub_page(array(
		'title' => 'Sidebar',
		'parent' => 'site-settings',
		'capability' => 'edit_posts'
	));
}*/

if( function_exists('acf_add_options_page') ) {
	$page = acf_add_options_page(array(
		'page_title' 	=> 'BMG Defaults',
		'menu_title' 	=> 'BMG Defaults',
		'menu_slug' 	=> 'bmg_defaults',
		'capability' 	=> 'install_plugins',
		'redirect' 	=> false
	)); 
}

//require_once( plugin_dir_path( __FILE__ ) . 'cpt-acf/options-header.php' );
//require_once( plugin_dir_path( __FILE__ ) . 'cpt-acf/options-sidebar.php' );
//require_once( plugin_dir_path( __FILE__ ) . 'cpt-acf/options-site-settings.php' );
//require_once( plugin_dir_path( __FILE__ ) . 'cpt-acf/options-footer.php' );


require_once( plugin_dir_path( __FILE__ ) . 'body.php' );
require_once( plugin_dir_path( __FILE__ ) . 'page.php' );
require_once( plugin_dir_path( __FILE__ ) . 'media.php' );
require_once( plugin_dir_path( __FILE__ ) . 'head.php' );
require_once( plugin_dir_path( __FILE__ ) . 'search.php' );
require_once( plugin_dir_path( __FILE__ ) . 'ss-admin.php' );
require_once( plugin_dir_path( __FILE__ ) . 'users.php' );
require_once( plugin_dir_path( __FILE__ ) . 'taxonomy.php' );

require_once( plugin_dir_path( __FILE__ ) . 'scripts.php' );
require_once( plugin_dir_path( __FILE__ ) . 'styles.php' );




?>