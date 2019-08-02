<?php
/**
 * Change the post menu to News & Events
 */

 
if( get_field('change_the_name_of_the_posts', 'option') ) {
	$page_title = get_field('post_menu_name', 'option');
	
	function change_post_menu_text() {
	  global $menu;
	  global $submenu;
	
	  // Change menu item
	  $menu[5][0] = get_field('post_menu_name', 'option');
	
	  // Change post submenu
	  $submenu['edit.php'][5][0] = get_field('all_posts_name', 'option');
	  $submenu['edit.php'][10][0] = get_field('add_posts_name', 'option');
	  $submenu['edit.php'][16][0] = get_field('post_tags_name', 'option');
	}
	
	add_action( 'admin_menu', 'change_post_menu_text' );
	
	
	/**
	 * Change the post type labels
	 */
	function change_post_type_labels() {
	  global $wp_post_types;
	
	  // Get the post labels
	  $postLabels = $wp_post_types['post']->labels;
	  $postLabels->name = get_field('post_menu_name', 'option');
	  $postLabels->singular_name = get_field('singular_post_name', 'option');
	  $postLabels->add_new = get_field('add_posts_name', 'option');
	  $postLabels->add_new_item = get_field('add_posts_name', 'option');
	  $postLabels->edit_item = get_field('edit_post_name', 'option');
	  $postLabels->new_item = get_field('new_post_name', 'option');
	  $postLabels->view_item = get_field('view_post_name', 'option');
	  $postLabels->search_items = get_field('search_post_name', 'option');
	  $postLabels->not_found = get_field('no_posts_found', 'option');
	  $postLabels->not_found_in_trash = get_field('no_posts_in_trash', 'option');
	}
	add_action( 'init', 'change_post_type_labels' );
}
?>