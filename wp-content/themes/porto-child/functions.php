<?php

add_action( 'wp_enqueue_scripts', 'porto_child_css', 1001 );

// Load CSS
function porto_child_css() {
	// porto child theme styles
	wp_deregister_style( 'styles-child' );
	wp_register_style( 'styles-child', esc_url( get_stylesheet_directory_uri() ) . '/style.css' );
	wp_enqueue_style( 'styles-child' );

	if ( is_rtl() ) {
		wp_deregister_style( 'styles-child-rtl' );
		wp_register_style( 'styles-child-rtl', esc_url( get_stylesheet_directory_uri() ) . '/style_rtl.css' );
		wp_enqueue_style( 'styles-child-rtl' );
	}
}



function bigmarlin_scripts_load_cdn() {

    // Register the script like this for a theme:
    wp_register_script( 'bootstrap-js', get_stylesheet_directory_uri() . '/library/bootstrap-3.1.1/js/bootstrap.min.js', array( 'jquery' ) );
    wp_register_script( 'equal-height', get_stylesheet_directory_uri() . '/library/jquery.matchHeight.js', array( 'jquery' ) );
    wp_register_script( 'bs-slideshow-home', get_stylesheet_directory_uri() . '/library/bootstrap-settings/home-slideshow.js', array( 'jquery' ) );
    wp_register_script( 'bs-popover', get_stylesheet_directory_uri() . '/library/bootstrap-settings/popover.js', array( 'jquery' ) , false, false);
    wp_register_script( 'bs-modal', get_stylesheet_directory_uri() . '/library/bootstrap-settings/bs-modal.js', array( 'jquery' ) );
 		wp_register_script( 'custom-canvas', get_stylesheet_directory_uri() . '/library/custom-canvas.js', array( 'jquery', 'bootstrap-js' ) );

    wp_enqueue_script( 'bootstrap-js' );
    wp_enqueue_script( 'equal-height' );
    wp_enqueue_script( 'bs-slideshow-home' );
    wp_enqueue_script( 'bs-popover' );
    wp_enqueue_script( 'bs-modal' );
    wp_enqueue_script( 'custom-canvas' );




} add_action( 'wp_enqueue_scripts', 'bigmarlin_scripts_load_cdn' );



function porto_child_clean_head() {
		remove_action('wp_head', 'wp_print_scripts');
		remove_action('wp_head', 'wp_print_head_scripts', 9);
		remove_action('wp_head', 'wp_enqueue_scripts', 1);
	}
	add_action( 'wp_enqueue_scripts', 'porto_child_clean_head' );
