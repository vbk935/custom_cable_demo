<?php
/**
 * Change the post menu to News & Events
 */
function change_post_menu_text() {
  global $menu;
  global $submenu;

  // Change menu item
  $menu[5][0] = 'News & Events';

  // Change post submenu
  $submenu['edit.php'][5][0] = 'News & Events';
  $submenu['edit.php'][10][0] = 'Add News & Events';
  $submenu['edit.php'][16][0] = 'News & Events Tags';
}

add_action( 'admin_menu', 'change_post_menu_text' );


/**
 * Change the post type labels
 */
function change_post_type_labels() {
  global $wp_post_types;

  // Get the post labels
  $postLabels = $wp_post_types['post']->labels;
  $postLabels->name = 'News & Events';
  $postLabels->singular_name = 'News & Events';
  $postLabels->add_new = 'Add News & Events';
  $postLabels->add_new_item = 'Add News & Events';
  $postLabels->edit_item = 'Edit News & Events';
  $postLabels->new_item = 'News & Events';
  $postLabels->view_item = 'View News & Events';
  $postLabels->search_items = 'Search News & Events';
  $postLabels->not_found = 'No News & Events found';
  $postLabels->not_found_in_trash = 'No News & Events found in Trash';
}
add_action( 'init', 'change_post_type_labels' );

?>