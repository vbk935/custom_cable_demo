<?php
//LOGIN
function ssg_login_css() {
	wp_enqueue_style( 'seaspider-admin', plugin_dir_url( __FILE__ ) .'assets/css/wp-admin.css');
	wp_enqueue_style( 'seaspider-colors', plugin_dir_url( __FILE__ ) .'assets/css/wp-admin-colors.css');
}
add_action('login_head', 'ssg_login_css');

// ADMIN
function ssg_admin_css() {
	wp_enqueue_style( 'fontawesome', plugin_dir_url( __FILE__ ) .'frameworks/font-awesome-4.0.3/css/font-awesome.min.css');
	wp_enqueue_style( 'seaspider-admin2', plugin_dir_url( __FILE__ ) .'assets/css/wp-admin.css');
	wp_enqueue_style( 'seaspider-colors', plugin_dir_url( __FILE__ ) .'assets/css/wp-admin-colors.css');
}
add_action('admin_head', 'ssg_admin_css');


//MAIN CSS
function ssg_main_css() {
	wp_enqueue_style( 'fontawesome', plugin_dir_url( __FILE__ ) .'frameworks/font-awesome-4.3.0/css/font-awesome.min.css');
	wp_enqueue_style( 'bootstrap', plugin_dir_url( __FILE__ ) .'frameworks/bootstrap-3.3.4-dist/css/bootstrap.min.css');
	wp_enqueue_style( 'pretty', plugin_dir_url( __FILE__ ) .'frameworks/prettyPhoto_compressed_3.1.5/css/prettyPhoto.css');
	wp_enqueue_style( 'pretty', plugin_dir_url( __FILE__ ) .'assets/css/defaults.css');
}
add_action('wp_enqueue_scripts', 'ssg_main_css');

?>