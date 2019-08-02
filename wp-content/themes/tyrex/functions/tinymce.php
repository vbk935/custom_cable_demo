<?php
//*****************************************************************************
//
// https://www.gavick.com/blog/wordpress-tinymce-custom-buttons/#tmce-section-5
//
//*****************************************************************************
add_filter('mce_buttons_2', 'gk_activate_styleselect');
function gk_activate_styleselect($buttons) {
   array_unshift( $buttons, 'styleselect' );
   return $buttons;
}


function gk_own_styles($config) {
    $temp_array = array(
        array(
            'title' => 'Paragraph',
            'block' => 'p',
            'classes' => ''
        ),
        array(
            'title' => 'Red Text',
            'inline' => 'span',
            'classes' => 'red-text'
        ),
        array(
            'title' => 'Sub-heading',
            'block' => 'h2',
            'classes' => 'sub-head'
        ),       
        array(
            'title' => 'Sub-sub-heading',
            'block' => 'h3',
            'classes' => 'sub-aub-head'
        ) 
    );
    $config['style_formats'] = json_encode( $temp_array );
    return $config;
}
add_filter('tiny_mce_before_init', 'gk_own_styles');

//http://www.wpexplorer.com/wordpress-tinymce-tweaks/

function remove_mce_buttons_2($buttons) {
	$buttons = array_diff($buttons, array('formatselect', 'alignjustify', 'indent', 'outdent'));
	return $buttons;
}
add_filter('mce_buttons_2', 'remove_mce_buttons_2');


//*****************************************************************************
//
// http://laubsterboy.com/blog/2014/02/wordpress-richtext-editor-for-excerpts/
//
//*****************************************************************************

function lb_editor_remove_meta_box() {
	global $post_type;
 
	// Check to see if the global $post_type variable exists
	// and then check to see if the current post_type supports
	// excerpts. If so, remove the default excerpt meta box
	// provided by the WordPress core. If you would like to only
	// change the excerpt meta box for certain post types replace
	// $post_type with the post_type identifier.
	if (isset($post_type) && post_type_supports($post_type, 'excerpt')) remove_meta_box('postexcerpt', $post_type, 'normal');
}
add_action('admin_menu', 'lb_editor_remove_meta_box');
 
function lb_editor_add_custom_meta_box() {
	global $post_type;
 
	// Again, check to see if the global $post_type variable
	// exists and then if the current post_type supports excerpts.
	// If so, add the new custom excerpt meta box. If you would
	// like to only change the excerpt meta box for certain post
	// types replace $post_type with the post_type identifier.
	if (isset($post_type) && post_type_supports($post_type, 'excerpt')) add_meta_box('postexcerpt', __('Excerpt'), 'lb_editor_custom_post_excerpt_meta_box', $post_type, 'normal', 'high');
}
add_action( 'add_meta_boxes', 'lb_editor_add_custom_meta_box' );
 
function lb_editor_custom_post_excerpt_meta_box( $post ) {
	// Adjust the settings for the new wp_editor. For all
	// available settings view the wp_editor reference
	// http://codex.wordpress.org/Function_Reference/wp_editor
	$settings = array( 'textarea_rows' => '12', 'quicktags' => false, 'tinymce' => true);
 
	// Create the new meta box editor and decode the current
	// post_excerpt value so the TinyMCE editor can display
	// the content as it is styled.
	wp_editor(html_entity_decode(stripcslashes($post->post_excerpt)), 'excerpt', $settings);
 
	// The meta box description - adjust as necessary
	echo '<p><em>Excerpts are optional, hand-crafted, summaries of your content.</em></p>';
}



?>