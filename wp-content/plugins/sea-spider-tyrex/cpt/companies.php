<?php
	add_action( 'init', 'ss_register_company' );
	function ss_register_company() {


	$labels = array(
		"name" => "Companies",
		"singular_name" => "Company",
		);

	$args = array(
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"show_ui" => true,
		"has_archive" => true,
		"show_in_menu" => true,
		"exclude_from_search" => true,
		"capability_type" => "page",
		"map_meta_cap" => true,
		"hierarchical" => true,
		"rewrite" => array( "slug" => "company", 
		"with_front" => true ),
		"query_var" => true,
		"menu_position" => 58,		
		"menu_icon" => "dashicons-admin-site",		
		"supports" => array( "title", "excerpt" ),			
	);
	register_post_type( "company", $args );
	
	
} ?>