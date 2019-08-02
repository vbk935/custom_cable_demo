<?php	
add_action( 'init', 'ss_register_panels' );
function ss_register_panels() {		

	$labels = array(
		"name" => "Panels",
		"singular_name" => "Panel",
		);

	$args = array(
		"labels" => $labels,
		"description" => "Use the Panels to make objects that can be used in the Sections of the Pages.",
		"public" => true,
		"show_ui" => true,
		"has_archive" => false,
		"show_in_menu" => true,
		"exclude_from_search" => true,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => false,
		"query_var" => true,
		"menu_position" => 22,		
		"menu_icon" => "dashicons-exerpt-view",		
		"supports" => array( "title", "editor", "revisions", "thumbnail" ),			
	);
	register_post_type( "panel", $args );
}
?>