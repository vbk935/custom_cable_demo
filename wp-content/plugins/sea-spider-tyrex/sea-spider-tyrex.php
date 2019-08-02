<?php
/**
  Plugin Name: Sea Spider Tyrex
  Plugin URI: https://bitbucket.org/danielgoze/sea-spider-tyrex
  Bitbucket Plugin URI: https://bitbucket.org/danielgoze/sea-spider-tyrex
  Description: Functionality For Tyrex Sites.....
  Version:  0.2.2
  Author: Big Marlin Group
  Author URI: http://bigmarlingroup.com
  Text Domain: bmg
  Network: false
  License:  GPL2
 */
 
if(!defined('WPINC')){ die;}

// create options pages.
if( function_exists('acf_add_options_page') ) {
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
}
if( function_exists('acf_add_options_sub_page') ) {
	acf_add_options_sub_page(array(
		'title' => 'Main Branch',
		'parent' => 'edit.php?post_type=company',
		'capability' => 'edit_posts'
	));
}


require_once( plugin_dir_path( __FILE__ ) . 'cpt/panels.php' );
require_once( plugin_dir_path( __FILE__ ) . 'cpt-acf/panels.php' );


require_once( plugin_dir_path( __FILE__ ) . 'cpt/companies.php' );
require_once( plugin_dir_path( __FILE__ ) . 'cpt-acf/company.php' );

require_once( plugin_dir_path( __FILE__ ) . 'cpt/downloads.php' );
require_once( plugin_dir_path( __FILE__ ) . 'cpt-acf/downloads.php' );

require_once( plugin_dir_path( __FILE__ ) . 'cpt/newsletters.php' );
require_once( plugin_dir_path( __FILE__ ) . 'cpt-acf/newsletters.php' );

require_once( plugin_dir_path( __FILE__ ) . 'cpt/article-slider.php' );
require_once( plugin_dir_path( __FILE__ ) . 'cpt-acf/slideshow-home.php' );

require_once( plugin_dir_path( __FILE__ ) . 'cpt/team.php' );
require_once( plugin_dir_path( __FILE__ ) . 'cpt-acf/team.php' );


require_once( plugin_dir_path( __FILE__ ) . 'cpt/press-releases.php' );
require_once( plugin_dir_path( __FILE__ ) . 'cpt-acf/downloads.php' );

require_once( plugin_dir_path( __FILE__ ) . 'cpt-acf/options-header.php' );
require_once( plugin_dir_path( __FILE__ ) . 'cpt-acf/options-sidebar.php' );
require_once( plugin_dir_path( __FILE__ ) . 'cpt-acf/options-site-settings.php' );
require_once( plugin_dir_path( __FILE__ ) . 'cpt-acf/options-footer.php' );

require_once( plugin_dir_path( __FILE__ ) . 'cpt-acf/home-page.php' );



require_once( plugin_dir_path( __FILE__ ) . 'generic-functions.php' );

require_once( plugin_dir_path( __FILE__ ) . 'media.php' );

require_once( plugin_dir_path( __FILE__ ) . 'scripts.php' );
require_once( plugin_dir_path( __FILE__ ) . 'styles.php' );

require_once( plugin_dir_path( __FILE__ ) . 'search.php' );
require_once( plugin_dir_path( __FILE__ ) . 'cpt-acf/article-sidebar.php' );



//pages
require_once( plugin_dir_path( __FILE__ ) . 'cpt-acf/sections-flexible-content.php' );
require_once( plugin_dir_path( __FILE__ ) . 'cpt-acf/article-submenu.php' );

require_once( plugin_dir_path( __FILE__ ) . 'cpt-acf/article-sidebar-images.php' );
require_once( plugin_dir_path( __FILE__ ) . 'cpt-acf/slideshow-header.php' );
//require_once( plugin_dir_path( __FILE__ ) . 'cpt-acf/extra-images.php' );


//require_once( plugin_dir_path( __FILE__ ) . 'cpt-acf/help.php' );

function the_excerpt_dynamic($length) { // Outputs an excerpt of variable length (in characters)
global $post;
$text = $post->post_exerpt;
if ( '' == $text ) {
$text = get_the_content('');
$text = apply_filters('the_content', $text);
$text = str_replace(']]>', ']]>', $text);
}
$text = strip_shortcodes( $text ); // optional, recommended
$text = strip_tags($text); // use ' $text = strip_tags($text,'<p><a>'); ' to keep some formats; optional

$text = substr($text,0,$length).' [...]';
echo apply_filters('the_excerpt',$text);
}

?>