<?php
	function seaspider_clean_head() {
		remove_action('wp_head', 'wp_print_scripts');
		remove_action('wp_head', 'wp_print_head_scripts', 9);
		remove_action('wp_head', 'wp_enqueue_scripts', 1);
	}
	add_action( 'wp_enqueue_scripts', 'seaspider_clean_head' );
?>