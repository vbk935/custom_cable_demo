<?php
/**
 * Register meta box(es).
 */
function wpdocs_register_meta_boxes_order() {
    add_meta_box('meta-box-id', __('Order Tracking Number', 'textdomain'), 'wpdocs_my_display_callback_order', 'shop_order', 'side');
}
add_action('add_meta_boxes', 'wpdocs_register_meta_boxes_order');
?>