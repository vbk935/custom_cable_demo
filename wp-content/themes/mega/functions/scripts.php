<?php
function bigmarlin_scripts_load_cdn() {
    // Deregister the included library
    //wp_deregister_script( 'jquery' );
     
    // Register the library again from Google's CDN
    //wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js', array(), null, false );

    // Register the script like this for a theme:
    wp_register_script( 'bootstrap-js', get_stylesheet_directory_uri() . '/library/bootstrap-3.1.1/js/bootstrap.min.js', array( 'jquery' ) );
   // wp_register_script( 'navbar-slide', get_stylesheet_directory_uri() . '/library/shrinking-header.js', array( 'jquery' ) );
    //wp_register_script( 'easing-js', get_stylesheet_directory_uri() . '/library/scroll/jquery.easing.1.3.js', array( 'jquery' ) );
    //wp_register_script( 'scrollto-js', get_stylesheet_directory_uri() . '/library/scroll/jquery.scrollTo.min.js', array( 'jquery' ) );
   // wp_register_script( 'localscroll-js', get_stylesheet_directory_uri() . '/library/scroll/jquery.localScroll.min.js', array( 'jquery' ) );
   // wp_register_script( 'localscroll-s-js', get_stylesheet_directory_uri() . '/library/scroll/localscroll-settings.js', array( 'jquery' ) );
    wp_register_script( 'equal-height', get_stylesheet_directory_uri() . '/library/jquery.matchHeight.js', array( 'jquery' ) );
 
    wp_enqueue_script( 'bootstrap-js' );
    wp_enqueue_script( 'equal-height' );




} add_action( 'wp_enqueue_scripts', 'bigmarlin_scripts_load_cdn' );


function custom_clean_head() {
remove_action('wp_head', 'wp_print_scripts');
remove_action('wp_head', 'wp_print_head_scripts', 9);
remove_action('wp_head', 'wp_enqueue_scripts', 1);
}
add_action( 'wp_enqueue_scripts', 'custom_clean_head' );


?>