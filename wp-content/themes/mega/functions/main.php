<?php
	add_action( 'after_setup_theme', 'bmg_setup' );
	function bmg_setup() {
		load_theme_textdomain( 'bmg', get_template_directory() . '/languages' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'post-thumbnails' );
		global $content_width;
	//	if ( ! isset( $content_width ) ) $content_width = 640;
	//		register_nav_menus(
	//		array( 'main-menu' => __( 'Main Menu', 'bmg' ) )
	//	);
	}
	
	//add_action( 'wp_enqueue_scripts', 'bmg_load_scripts' );
	//function bmg_load_scripts() {
	//	wp_enqueue_script( 'jquery' );
	//}
	

	add_filter( 'the_title', 'bmg_title' );
	function bmg_title( $title ) {
		if ( $title == '' ) {
			return '&rarr;';
		} else {
			return $title;
		}
	}
	
	add_filter( 'wp_title', 'bmg_filter_wp_title' );
	function bmg_filter_wp_title( $title ) {
		return $title . esc_attr( get_bloginfo( 'name' ) );
	}


//http://wordpress.stackexchange.com/questions/124330/metabox-layout-for-all-users/124337#124337

function force_user_option_wpse_124330($option) {
  remove_filter('get_user_option_meta-box-order_post','force_user_option_wpse_124330');
  return get_user_option('meta-box-order_post', 2); // 1 is the known user ID
}
add_filter('get_user_option_meta-box-order_post','force_user_option_wpse_124330');


/*  add_filter('piklist_admin_pages', 'piklist_theme_setting_pages');
  function piklist_theme_setting_pages($pages)
  {
     $pages[] = array(
      'page_title' => __('Custom Settings')
      ,'menu_title' => __('Tyrex')
      ,'capability' => 'manage_options'
      ,'menu_slug' => 'custom_settings'
      ,'setting' => 'piklist_demo_post_types'
      ,'menu_icon' => plugins_url('piklist/parts/img/piklist-icon.png')
      ,'page_icon' => plugins_url('piklist/parts/img/piklist-page-icon-32.png')
      ,'single_line' => true
      ,'default_tab' => 'Basic'
      ,'save_text' => 'Save Demo Settings'
    );
 
    return $pages;
  }
  */
  ?>