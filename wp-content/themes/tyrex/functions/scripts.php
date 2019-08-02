<?php
function bigmarlin_scripts_load_cdn() {

    // Register the script like this for a theme:
    wp_register_script( 'bootstrap-js', get_stylesheet_directory_uri() . '/library/bootstrap-3.1.1/js/bootstrap.min.js', array( 'jquery' ) );
    wp_register_script( 'equal-height', get_stylesheet_directory_uri() . '/library/jquery.matchHeight.js', array( 'jquery' ) );
    wp_register_script( 'bs-slideshow-home', get_stylesheet_directory_uri() . '/library/bootstrap-settings/home-slideshow.js', array( 'jquery' ) );
    wp_register_script( 'bs-popover', get_stylesheet_directory_uri() . '/library/bootstrap-settings/popover.js', array( 'jquery' ) );
    wp_register_script( 'bs-modal', get_stylesheet_directory_uri() . '/library/bootstrap-settings/bs-modal.js', array( 'jquery' ) );
 
    wp_enqueue_script( 'bootstrap-js' );
    wp_enqueue_script( 'equal-height' );
    wp_enqueue_script( 'bs-slideshow-home' );
    wp_enqueue_script( 'bs-popover' );




} add_action( 'wp_enqueue_scripts', 'bigmarlin_scripts_load_cdn' );


function custom_clean_head() {
remove_action('wp_head', 'wp_print_scripts');
remove_action('wp_head', 'wp_print_head_scripts', 9);
remove_action('wp_head', 'wp_enqueue_scripts', 1);
}
add_action( 'wp_enqueue_scripts', 'custom_clean_head' );


?>