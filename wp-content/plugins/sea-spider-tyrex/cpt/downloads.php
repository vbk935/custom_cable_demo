<?php
	add_action( 'init', 'ss_register_downloads' );
	function ss_register_downloads() {



$labels = array(
		"name" => "Downloads",
		"singular_name" => "Download",
		);

	$args = array(
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"show_ui" => true,
		"has_archive" => true,
		"show_in_menu" => true,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => true,
		"rewrite" => array( "slug" => "download", "with_front" => true ),
		"query_var" => true,
		"menu_position" => 26, "menu_icon" => "dashicons-download", "supports" => array( "title", "editor", "excerpt", "thumbnail", "page-attributes" ),			
	);
	register_post_type( "download", $args );
	
	}
?>