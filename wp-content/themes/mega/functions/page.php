<?php
//Add Excerpt to pages
function my_init() {
     add_post_type_support('page', array('excerpt'));
}
add_action('init', 'my_init');
?>