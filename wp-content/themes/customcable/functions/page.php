<?php
//Add Excerpt to pages
function my_init() {
     add_post_type_support('page', array('excerpt'));
}
add_action('init', 'my_init');



//  http://codex.wordpress.org/Function_Reference/is_page
/// is_tree
function is_tree( $pid ) {      // $pid = The ID of the page we're looking for pages underneath
    global $post;               // load details about this page

    if ( is_page($pid) )
        return true;            // we're at the page or at a sub page

    $anc = get_post_ancestors( $post->ID );
    foreach ( $anc as $ancestor ) {
        if( is_page() && $ancestor == $pid ) {
            return true;
        }
    }

    return false;  // we arn't at the page, and the page is not an ancestor
}
?>