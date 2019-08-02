<?php
// Move Yoast to bottom
function ss_yoasttobottom() { return 'low';}
add_filter( 'wpseo_metabox_prio', 'ss_yoasttobottom');

//Remove admin bar logo
function ss_custom_admin_logo() {
    echo '
        <style type="text/css">
            #wp-admin-bar-wp-logo { display:none; }
        </style>
    ';
}
add_action('admin_head', 'ss_custom_admin_logo');


?>