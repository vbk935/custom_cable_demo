<?php

/**
 * Scripts File
 * Handles to admin functionality & other functions
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Load Admin styles & scripts
 */
function esb_cie_admin_scripts(){
    
     // Load our admin stylesheet.
     wp_enqueue_style( 'esb-cie-admin-style', ESB_CIE_URL . 'css/admin-style.css' );
     
     // Load our admin script.
     wp_enqueue_script( 'esb-cie-admin-script', ESB_CIE_URL . 'js/admin-script.js' );
}

//add action to load scripts and styles for the back end
add_action( 'admin_enqueue_scripts', 'esb_cie_admin_scripts' );

?>