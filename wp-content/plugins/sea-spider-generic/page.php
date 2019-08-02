<?php
//Add Excerpt to pages
function ssg_add_page_excerpt() {
     add_post_type_support('page', array('excerpt'));
}
add_action('init', 'ssg_add_page_excerpt');
?>