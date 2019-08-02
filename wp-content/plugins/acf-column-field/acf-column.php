<?php
/*
Plugin Name: Advanced Custom Fields: Column
Plugin URI: www.dreihochzwo.de
Description: Adds an Advanced Custom Field field to arrange ACF fields into columns. This plugin needs the installation/activation of Advanced Custom Fields V5.
Version: 1.1.0
Author: Thomas Meyer
Author URI: www.dreihochzwo.de
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/




// 1. set text domain
// Reference: https://codex.wordpress.org/Function_Reference/load_plugin_textdomain
load_plugin_textdomain( 'acf-column', false, dirname( plugin_basename(__FILE__) ) . '/lang/' ); 


// 2. Include field type for ACF5
// $version = 5 and can be ignored until ACF6 exists
function include_field_types_column( $version ) {
	
	include_once('acf-column-v5.php');
	
}

add_action('acf/include_field_types', 'include_field_types_column');	

	
?>
