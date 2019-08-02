<?php
	add_action( 'init', 'ss_register_newsletter' );
	function ss_register_newsletter() {
	
	
	

	$labels = array(
		"name" => "Newsletters",
		"singular_name" => "Newsletter",
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
		"rewrite" => array( "slug" => "newsletter","with_front" => true ),
		"query_var" => true,
		"menu_position" => 25,"menu_icon" => "dashicons-download",
		"supports" => array( "title", "editor", "excerpt", "thumbnail" ),			
	);
	register_post_type( "newsletter", $args );
	
	
	
		
} ?>