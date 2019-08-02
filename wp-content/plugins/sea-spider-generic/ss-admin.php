<?php
// Move Yoast to bottom
function ssg_yoasttobottom() { return 'low';}
add_filter( 'wpseo_metabox_prio', 'ssg_yoasttobottom');

//Remove admin bar logo
function ssg_custom_admin_logo() {
    echo '
        <style type="text/css">
            #wp-admin-bar-wp-logo { display:none; }
        </style>
    ';
}
add_action('admin_head', 'ssg_custom_admin_logo');


?>