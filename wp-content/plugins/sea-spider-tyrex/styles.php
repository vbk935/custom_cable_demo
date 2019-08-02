<?php

//LOGIN

function tyrex_login_css() {

	wp_enqueue_style( 'adm-login', plugin_dir_url( __FILE__ ) .'assets/css/admin-login.css.php');	

}

add_action('login_head', 'tyrex_login_css');



// ADMIN

function tyrex_admin_css() {

	//wp_enqueue_style( 'ss-adm-disc', plugin_dir_url( __FILE__ ) .'assets/css/admin_discontinued.css');	

	wp_enqueue_style( 'ss-adm-main', plugin_dir_url( __FILE__ ) .'assets/css/admin.css');	

}

add_action('admin_head', 'tyrex_admin_css');





//MAIN CSS

function tyrex_main_css() {

	wp_register_style('google-alfa', "//fonts.googleapis.com/css?family=Alfa+Slab+One");

	wp_enqueue_style( 'google-alfa');	  

	wp_register_style('google-merri', "//fonts.googleapis.com/css?family=Merriweather:400,700italic,700,400italic");

	wp_enqueue_style( 'google-merri');

	wp_register_style('google-signika', "//fonts.googleapis.com/css?family=Signika:400,700");

	wp_enqueue_style( 'google-signika');





	wp_enqueue_style( 'tyrex-general', plugin_dir_url( __FILE__ ) .'assets/css/general.css');

	if ( is_front_page() ) {

		wp_enqueue_style( 'tyrex-home', plugin_dir_url( __FILE__ ) .'assets/css/home-page.css');

	}

	//if (is_page_template( 'template-contact.php' ))  {

		wp_enqueue_style( 'tyrex-contact', plugin_dir_url( __FILE__ ) .'assets/css/contact-us.css');

	//}

	//if (is_page_template( 'template-news.php' ))  {

	//}

	wp_enqueue_style( 'tyrex-navigation', plugin_dir_url( __FILE__ ) .'assets/css/navigation.css');

	wp_enqueue_style( 'tyrex-slideshow', plugin_dir_url( __FILE__ ) .'assets/css/slideshow.css');

	wp_enqueue_style( 'tyrex-buttons', plugin_dir_url( __FILE__ ) .'assets/css/buttons-forms.css');

	wp_enqueue_style( 'tyrex-extra', plugin_dir_url( __FILE__ ) .'assets/css/extra.css');

	wp_enqueue_style( 'tyrex-main', plugin_dir_url( __FILE__ ) .'assets/css/main.css');

	wp_enqueue_style( 'tyrex-sidebar', plugin_dir_url( __FILE__ ) .'assets/css/sidebar.css');

	wp_enqueue_style( 'tyrex-sections', plugin_dir_url( __FILE__ ) .'assets/css/sections.css');

	wp_enqueue_style( 'tyrex-footer', plugin_dir_url( __FILE__ ) .'assets/css/footer.css');

	wp_enqueue_style( 'tyrex-dynamic', plugin_dir_url( __FILE__ ) .'assets/css/dynamic.css.php');

	wp_enqueue_style( 'tyrex-responsive', plugin_dir_url( __FILE__ ) .'assets/css/responsive.css');

	wp_enqueue_style( 'tyrex-woo', plugin_dir_url( __FILE__ ) .'assets/css/woocommerce.css');

}

add_action('wp_enqueue_scripts', 'tyrex_main_css');

?>