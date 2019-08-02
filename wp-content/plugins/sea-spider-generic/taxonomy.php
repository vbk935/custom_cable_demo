<?php
/// http://www.wprecipes.com/wordpress-function-to-get-postpage-slug
function the_slug() {
    $post_data = get_post($post->ID, ARRAY_A);
    $slug = $post_data['post_name'];
    return $slug; 
}