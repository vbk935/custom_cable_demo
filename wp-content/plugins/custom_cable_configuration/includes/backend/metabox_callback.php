<?php
/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 */
function wpdocs_my_display_callback_order($post) {
    $outline = '<label for="title_field" style="width:100%; display:inline-block;">' . esc_html__('Order Tracking Number', 'text-domain') . '</label>';
    $title_field = get_post_meta($post->ID, 'tracking_number', true);
    $outline .= '<input type="text" name="tracking_number" id="title_field" class="title_field" value="' . esc_attr($title_field) . '" style="width:100%;"/>';

    echo $outline;
}

/**
 * Save meta box content.
 *
 * @param int $post_id Post ID
 */
function wpdocs_save_meta_box_order($post_id) {
    if (!empty($_POST['tracking_number'])) {
        update_post_meta($post_id, 'tracking_number', $_POST['tracking_number']);
    }
}

add_action('save_post', 'wpdocs_save_meta_box_order');
?>