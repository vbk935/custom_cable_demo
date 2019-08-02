<?php
	add_action( 'init', 'ss_register_slider' );
	function ss_register_slider() {

	$labels = array(
		"name" => "Article/Slider",
		"singular_name" => "Articles/Sliders",
		);

	$args = array(
		"labels" => $labels,
		"description" => "Home Page Slideshow links to an article or internal & external links",
		"public" => true,
		"show_ui" => true,
		"has_archive" => true,
		"show_in_menu" => true,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => array( "slug" => "article", 
		"with_front" => true ),
		"query_var" => true,
		"menu_position" => 21,		
		"menu_icon" => "dashicons-media-document",		
		"supports" => array( "title", "editor", "excerpt", "revisions", "thumbnail" ),		
		"taxonomies" => array( "category" )	);
	register_post_type( "article", $args );

}
?>