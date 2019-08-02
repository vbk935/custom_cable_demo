<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WCPV_BACKEND_PRODUCTS_SELECT2_AJAX {

    /**
     * Hook in ajax handlers.
     */
    public static function add_ajax_events() {
        $ajax_events = array(
            'wcpv_json_search_products' => false,
        );

        foreach ($ajax_events as $ajax_event => $nopriv) {
            add_action('wp_ajax_woocommerce_' . $ajax_event, array(__CLASS__, $ajax_event));

            if ($nopriv) {
                add_action('wp_ajax_nopriv_woocommerce_' . $ajax_event, array(__CLASS__, $ajax_event));

                // WC AJAX can be used for frontend ajax requests
                add_action('wc_ajax_' . $ajax_event, array(__CLASS__, $ajax_event));
            }
        }
    }

    /**
     * Search for products and echo json.
     *
     * @param string $term (default: '')
     * @param string $post_types (default: array('product'))
     */
    public static function wcpv_json_search_products($term = '', $post_types = array('product')) {
        global $wpdb;

        ob_start();

        check_ajax_referer('search-products', 'security');

        if (empty($term)) {
            $term = wc_clean(stripslashes($_GET['term']));
        } else {
            $term = wc_clean($term);
        }

        $like_term = '%' . $wpdb->esc_like($term) . '%';

        if (is_numeric($term)) {
            $query = $wpdb->prepare("
				SELECT ID FROM {$wpdb->posts} posts LEFT JOIN {$wpdb->postmeta} postmeta ON posts.ID = postmeta.post_id
				WHERE posts.post_status = 'publish'
				AND (
					posts.post_parent = %s
					OR posts.ID = %s
					OR posts.post_title LIKE %s
					OR (
						postmeta.meta_key = '_sku' AND postmeta.meta_value LIKE %s
					)
				)
			", $term, $term, $term, $like_term);
        } else {
            $query = $wpdb->prepare("
				SELECT ID FROM {$wpdb->posts} posts LEFT JOIN {$wpdb->postmeta} postmeta ON posts.ID = postmeta.post_id
				WHERE posts.post_status = 'publish'
				AND (
					posts.post_title LIKE %s
					or posts.post_content LIKE %s
					OR (
						postmeta.meta_key = '_sku' AND postmeta.meta_value LIKE %s
					)
				)
			", $like_term, $like_term, $like_term);
        }

        $query .= " AND posts.post_type IN ('" . implode("','", array_map('esc_sql', $post_types)) . "')";

        if (!empty($_GET['exclude'])) {
            $query .= " AND posts.ID NOT IN (" . implode(',', array_map('intval', explode(',', $_GET['exclude']))) . ")";
        }

        if (!empty($_GET['include'])) {
            $query .= " AND posts.ID IN (" . implode(',', array_map('intval', explode(',', $_GET['include']))) . ")";
        }

        if (!empty($_GET['limit'])) {
            $query .= " LIMIT " . intval($_GET['limit']);
        }

        $posts = array_unique($wpdb->get_col($query));
        $found_products = array();

        if (!empty($posts)) {
            foreach ($posts as $post) {
                $product = wc_get_product($post);

                if (!current_user_can('read_product', $post)) {
                    continue;
                }

                if (!$product || ( $product->is_type('variation') && empty($product->parent) )) {
                    continue;
                }

                $found_products[$post] = rawurldecode($product->get_formatted_name());
            }
        }

        $found_products = apply_filters('woocommerce_json_search_found_products', $found_products);

        wp_send_json($found_products);
    }

}