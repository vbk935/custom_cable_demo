<?php
//*****************************************************************************
//
// http://css-tricks.com/snippets/wordpress/make-archives-php-include-custom-post-types/
//
//*****************************************************************************

function namespace_add_custom_types( $query ) {
  if( is_category() || is_tag() && empty( $query->query_vars['suppress_filters'] ) ) {
    $query->set( 'post_type', array(
     'post', 'article'
		));
	  return $query;
	}
}
add_filter( 'pre_get_posts', 'namespace_add_custom_types' );


?>