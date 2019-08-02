<?php
if( get_field('add_team', 'option') ){ 

	add_action( 'init', 'ss_register_team' );
	function ss_register_team() {

	$labels = array(
		"name" => "Team",
		"singular_name" => "Team",
		);

	$args = array(
		"labels" => $labels,
		"description" => "Members of the team to display on the Contact Us page",
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
		"menu_position" => 57,		
		"menu_icon" => "dashicons-universal-access",		
		"supports" => array( "title", "editor", "thumbnail" ),			
	);
	register_post_type( "team", $args );


}
}
?>