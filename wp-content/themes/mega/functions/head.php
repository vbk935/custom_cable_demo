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
// Add Favicon
//
//*****************************************************************************
//function blog_favicon() { 
//echo '<link rel="Shortcut Icon" type="image/x-icon" href="'.get_bloginfo('wpurl').'/wp-content/themes/williamson/assets/webicons/favicon.ico" />'; 
//} 
//add_action('wp_head', 'blog_favicon'); 




?>