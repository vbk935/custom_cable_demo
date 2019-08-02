<?php
//*****************************************************************************
//
// http://www.wpbeginner.com/wp-tutorials/how-to-add-custom-post-types-to-your-main-wordpress-rss-feed/
//
//*****************************************************************************
function myfeed_request($qv) {
    if (isset($qv['feed']) && !isset($qv['post_type']))
        $qv['post_type'] = ('news');
    return $qv;
}

add_filter('request', 'myfeed_request');


//*****************************************************************************
//
// http://wordpress.org/support/topic/removing-1?replies=3#post-3125330
//
//*****************************************************************************
// Remove auto generated feed links
function my_remove_feeds() {
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	remove_action( 'wp_head', 'feed_links', 2 );
}
add_action( 'after_setup_theme', 'my_remove_feeds' );



//*****************************************************************************
//
// http://www.paulund.co.uk/add-custom-fields-to-rss-feed
//
//*****************************************************************************
//function fields_in_feed($content) {  
//    if(is_feed()) {  
//        $post_id = get_the_ID();  
//        $output = '<div><h3>Find me on</h3>';  
//        $output .= '<p><strong>Facebook:</strong> ' . get_post_meta($post_id, "facebook_url", true) . '</p>';  
//        $output .= '<p><strong>Google:</strong> ' . get_post_meta($post_id, "google_url", true) . '</p>';  
//        $output .= '<p><strong>Twitter:</strong> ' . get_post_meta($post_id, "twitter_url", true) . '</p>';  
//        $output .= '</div>';  
//        $content = $content.$output;  
//    }  
//    return $content;  
//}  
//add_filter('the_content','fields_in_feed');


//http://www.catswhocode.com/blog/8-new-and-amazing-wordpress-hacks

function cwc_rss_post_thumbnail($content) {
    global $post;
    if(has_post_thumbnail($post->ID)) {
        $content = '<p>' . get_the_post_thumbnail($post->ID) .
        '</p>' . get_the_content();
    }

    return $content;
}
add_filter('the_excerpt_rss', 'cwc_rss_post_thumbnail');
add_filter('the_content_feed', 'cwc_rss_post_thumbnail');


?>