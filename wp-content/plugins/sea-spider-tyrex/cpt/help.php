<?php
	add_action( 'init', 'ss_register_help' );
	function ss_register_help() {


	$labels = array(
		"name" => "Help",
		"singular_name" => "Help",
		);

	$args = array(
		"labels" => $labels,
		"description" => "",
		"public" => false,
		"show_ui" => false,
		"has_archive" => true,
		"show_in_menu" => false,
		"exclude_from_search" => true,
		"capability_type" => "page",
		"map_meta_cap" => true,
		"hierarchical" => true,
		"rewrite" => array( "slug" => "help", "with_front" => true ),
		"query_var" => true,
		"supports" => array( "title", "editor", "excerpt", "trackbacks", "custom-fields", "comments", "revisions", "thumbnail", "author", "page-attributes", "post-formats" ),			);
	register_post_type( "help", $args );
	
	}
?>