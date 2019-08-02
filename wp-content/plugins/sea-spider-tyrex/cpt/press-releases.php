<?php
//THIS IS FROM MEGLADON
if( get_field('add_press_releases', 'option') ){ 
	add_action( 'init', 'cptui_register_press' );
	function cptui_register_press() {
	
	
		$labels = array(
			"name" => "Press Releases",
			"singular_name" => "Press Releases",
			);
	
		$args = array(
			"labels" => $labels,
			"description" => "",
			"public" => true,
			"show_ui" => true,
			"has_archive" => false,
			"show_in_menu" => true,
			"exclude_from_search" => false,
			"capability_type" => "post",
			"map_meta_cap" => true,
			"hierarchical" => false,
			"rewrite" => array( "slug" => "press-releases", "with_front" => true ),
			"query_var" => true,
			"menu_icon" => "dashicons-media-document","supports" => array( "title", "editor", "excerpt", "thumbnail" ),			);
		register_post_type( "press-releases", $args );
	
	}
}
?>