<?php

add_action('init', 'create_configuration_hierarchical_taxonomy', 0);

function create_configuration_hierarchical_taxonomy() {
    $labels = array(
        'name' => _x('Configurations', 'taxonomy general name'),
        'singular_name' => _x('Configuration', 'taxonomy singular name'),
        'search_items' => __('Search Configurations'),
        'all_items' => __('All Configurations'),
        'parent_item' => __('Parent Configuration'),
        'parent_item_colon' => __('Parent Configuration:'),
        'edit_item' => __('Edit Configuration'),
        'update_item' => __('Update Configuration'),
        'add_new_item' => __('Add New Configuration'),
        'new_item_name' => __('New Topic Configuration'),
        'menu_name' => __('Configurations')
    );
    register_taxonomy('configuration', array(
        'product'
            ), array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'meta_box_cb' => false,
        'rewrite' => array(
            'slug' => 'configuration'
        )
    ));
}

// Add to admin_init function
add_filter("manage_edit-configuration_columns", 'theme_config_columns'); 
 
function theme_config_columns($theme_columns) {
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
