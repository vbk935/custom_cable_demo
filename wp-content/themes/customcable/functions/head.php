<?php
//*****************************************************************************
//
// http://www.wprecipes.com/how-to-clean-up-wp_head-without-a-plugin
//
//*****************************************************************************
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'start_post_rel_link' );
remove_action( 'wp_head', 'index_rel_link' );
remove_action( 'wp_head', 'adjacent_posts_rel_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );


//*****************************************************************************
//
// Add Favicon https://wordpress.org/support/topic/favicon-in-child-theme
// http://realfavicongenerator.net/
//
//*****************************************************************************

// add a favicon to your
function ss_blog_favicon() {
	echo '<link rel="apple-touch-icon" sizes="57x57" href="'. get_stylesheet_directory_uri().'/assets/favicons/apple-touch-icon-57x57.png">';
	echo '<link rel="apple-touch-icon" sizes="114x114" href="'. get_stylesheet_directory_uri().'/assets/favicons/apple-touch-icon-114x114.png">';
	echo '<link rel="apple-touch-icon" sizes="72x72" href="'. get_stylesheet_directory_uri().'/assets/favicons/apple-touch-icon-72x72.png">';
	echo '<link rel="apple-touch-icon" sizes="144x144" href="'. get_stylesheet_directory_uri().'/assets/favicons/apple-touch-icon-144x144.png">';
	echo '<link rel="apple-touch-icon" sizes="60x60" href="'. get_stylesheet_directory_uri().'/assets/favicons/apple-touch-icon-60x60.png">';
	echo '<link rel="apple-touch-icon" sizes="120x120" href="'. get_stylesheet_directory_uri().'/assets/favicons/apple-touch-icon-120x120.png">';
	echo '<link rel="apple-touch-icon" sizes="76x76" href="'. get_stylesheet_directory_uri().'/assets/favicons/apple-touch-icon-76x76.png">';
	echo '<link rel="apple-touch-icon" sizes="152x152" href="'. get_stylesheet_directory_uri().'/assets/favicons/apple-touch-icon-152x152.png">';
	echo '<link rel="apple-touch-icon" sizes="180x180" href="'. get_stylesheet_directory_uri().'/assets/favicons/apple-touch-icon-180x180.png">';
	echo '<link rel="shortcut icon" href="'. get_stylesheet_directory_uri().'/assets/favicons/favicon.ico" />';
	echo '<link rel="icon" type="image/png" href="'. get_stylesheet_directory_uri().'/assets/favicons/favicon-192x192.png" sizes="192x192">';
	echo '<link rel="icon" type="image/png" href="'. get_stylesheet_directory_uri().'/assets/favicons/favicon-160x160.png" sizes="160x160">';
	echo '<link rel="icon" type="image/png" href="'. get_stylesheet_directory_uri().'/assets/favicons/favicon-96x96.png" sizes="96x96">';
	echo '<link rel="icon" type="image/png" href="'. get_stylesheet_directory_uri().'/assets/favicons/favicon-16x16.png" sizes="16x16">';
	echo '<link rel="icon" type="image/png" href="'. get_stylesheet_directory_uri().'/assets/favicons/favicon-32x32.png" sizes="32x32">';
	echo '<meta name="msapplication-TileColor" content="#fff">';
	echo '<meta name="msapplication-TileImage" content="'. get_stylesheet_directory_uri().'/assets/favicons/mstile-144x144.png">';
	echo '<meta name="msapplication-config" content="'. get_stylesheet_directory_uri().'/assets/favicons/browserconfig.xml">';

	// Hack for bs-modal issue working programmatically
	echo '<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>';
	echo "<script type='text/javascript' src='". site_url() ."/wp-content/plugins/sea-spider-generic/frameworks/bootstrap-3.3.4-dist/js/bootstrap.min.js?ver=4.7.3'></script>";
}
add_action('wp_head', 'ss_blog_favicon'); ?>