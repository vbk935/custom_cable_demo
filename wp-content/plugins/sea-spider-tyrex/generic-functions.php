<?php
//http://www.wprecipes.com/wordpress-function-to-get-postpage-slug
function the_slug_by_id($id) {
	$post_data = get_post($id, ARRAY_A);
	$slug = $post_data['post_name'];
	return $slug; 
}



?>