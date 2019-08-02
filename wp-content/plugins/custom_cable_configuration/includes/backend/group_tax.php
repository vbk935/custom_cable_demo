<?php
add_action('init', 'create_group_hierarchical_taxonomy', 0);

function create_group_hierarchical_taxonomy() {
    $labels = array(
        'name' => _x('Groups', 'taxonomy general name'),
        'singular_name' => _x('Group', 'taxonomy singular name'),
        'search_items' => __('Search Groups'),
        'all_items' => __('All Groupsbbf'),
        'parent_item' => __('Parent Group'),
        'parent_item_colon' => __('Parent Group:'),
        'edit_item' => __('Edit Group'),
        'update_item' => __('Update Group'),
        'add_new_item' => __('Add New Group'),
        'menu_name' => __('Groups')
    );
    register_taxonomy('group', array(
        'product'
            ), array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'meta_box_cb' => false,
        'rewrite' => array(
            'slug' => 'group'
        )
    ));
}

// Add to admin_init function
add_filter("manage_edit-group_columns", 'theme_group_columns'); 
 
function theme_group_columns($theme_columns) {
    $new_columns = array(
        'cb' => '<input type="checkbox" />',
        'name' => __('Name'),
        'header_icon' => '',
        //'description' => __('Description'),
        'slug' => __('Slug'),
        //'posts' => __('Posts')
        );
    return $new_columns;
}
?>
