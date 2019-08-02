<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $porto_settings;

// Lazy load
require PORTO_LIB . '/lib/lazy-load/lazy-load.php';

// Infinite Scroll
require PORTO_LIB . '/lib/infinite-scroll/infinite-scroll.php';

// Image Swatch
if ( class_exists( 'Woocommerce' ) ) {
	require PORTO_LIB . '/lib/woocommerce-swatches/woocommerce-swatches.php';
}

// Live Search
if ( isset( $porto_settings['search-live'] ) && $porto_settings['search-live'] ) {
	require PORTO_LIB . '/lib/live-search/live-search.php';
}

// Porto Studio
if ( class_exists( 'Vc_Manager' ) ) {
	require_once PORTO_LIB . '/lib/porto-studio/porto-studio.php';
}
