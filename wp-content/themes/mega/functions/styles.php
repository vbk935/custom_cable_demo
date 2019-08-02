<?php
//LOGIN
function seaspider_login_css() {
	wp_enqueue_style( 'seaspider-admin', get_stylesheet_directory_uri() . '/assets/css/wp-admin.css');
	wp_enqueue_style( 'seaspider-colors', get_stylesheet_directory_uri() . '/assets/css/wp-admin-colors.css');
}
add_action('login_head', 'seaspider_login_css');

// ADMIN
function seaspider_admin_css() {
	wp_enqueue_style( 'fontawesome', get_stylesheet_directory_uri() . '/library/font-awesome-4.0.3/css/font-awesome.min.css');
	wp_enqueue_style( 'seaspider', get_stylesheet_directory_uri() . '/assets/css/wp-admin.css');
}
add_action('admin_head', 'seaspider_admin_css');

//GOOGLE FONTS
function seaspider_load_fonts() {
	wp_register_style('google-alfa', "http://fonts.googleapis.com/css?family=Alfa+Slab+One");
	wp_enqueue_style( 'google-alfa');	  
	wp_register_style('google-merri', "http://fonts.googleapis.com/css?family=Merriweather:400,700italic,700,400italic");
	wp_enqueue_style( 'google-merri');
	wp_register_style('google-signika', "http://fonts.googleapis.com/css?family=Signika:400,700");
	wp_enqueue_style( 'google-signika');
}
add_action('wp_print_styles', 'seaspider_load_fonts');

//MAIN CSS
function seaspider_main_css() {
	wp_enqueue_style( 'fontawesome', get_stylesheet_directory_uri() . '/library/font-awesome-4.0.3/css/font-awesome.min.css');
	wp_enqueue_style( 'bootstrap', get_stylesheet_directory_uri() . '/library/bootstrap-3.1.1/css/bootstrap.min.css');
	wp_enqueue_style( 'seaspider-layout', get_stylesheet_directory_uri() . '/assets/css/layout.css');
	wp_enqueue_style( 'woo-layout', get_stylesheet_directory_uri() . '/assets/css/woo.css');
}
add_action('wp_enqueue_scripts', 'seaspider_main_css');

?>