<?php
	function ssg_scripts_load() {
		wp_register_script( 'bootstrap-js', plugin_dir_url( __FILE__ ) .'frameworks/bootstrap-3.3.4-dist/js/bootstrap.min.js', array( 'jquery' ) );
		wp_register_script( 'equal-height', plugin_dir_url( __FILE__ ) . 'frameworks/match-height/jquery.matchHeight.js', array( 'jquery' ) );
		wp_register_script( 'pretty-photo', plugin_dir_url( __FILE__ ) . 'frameworks/prettyPhoto_compressed_3.1.5/js/jquery.prettyPhoto.js', array( 'jquery' ) );
		wp_register_script( 'pretty-prefs', plugin_dir_url( __FILE__ ) .  'frameworks/prettyPhoto_compressed_3.1.5/js/pretty-prefs.js', array( 'jquery' ) );		

		wp_register_script( 'nano-nano', plugin_dir_url( __FILE__ ) .  'frameworks/nano/jquery.nanoscroller.min.js', array( 'jquery' ) );		
		wp_register_script( 'nano-prefs', plugin_dir_url( __FILE__ ) .  'frameworks/nano/nano.js', array( 'jquery' ) );		


		
		wp_enqueue_script( 'bootstrap-js' );
		wp_enqueue_script( 'equal-height' );
		wp_enqueue_script( 'nano-nano' );
		wp_enqueue_script( 'nano-prefs' );
		wp_enqueue_script( 'pretty-photo' );
		wp_enqueue_script( 'pretty-prefs' );
	} add_action( 'wp_enqueue_scripts', 'ssg_scripts_load' );

	function ssg_clean_head() {
		remove_action('wp_head', 'wp_print_scripts');
		remove_action('wp_head', 'wp_print_head_scripts', 9);
		remove_action('wp_head', 'wp_enqueue_scripts', 1);
	}
	add_action( 'wp_enqueue_scripts', 'ssg_clean_head' );


?>