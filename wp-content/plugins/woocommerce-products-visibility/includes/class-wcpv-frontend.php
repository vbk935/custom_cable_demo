<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WCPV_FRONTEND {

    protected static $instance = null;
    public $exclude_categories = array();
    public $categories_count_posts = array();
    public $categories_to_change_count = array();
    public $exclude_tags = array();
    public $tags_count_posts = array();
    public $tags_to_change_count = array();
    public $include_products = array();
    public $productids = array();
    public $has_productids = false;
    public $products_visibility;
    public $categoryids = array();
    public $has_categoryids = false;
    public $categories_visibility;
    public $tagids = array();
    public $has_tagids = false;
    public $tags_visibility;
    public $productids_from_categories = array();
    public $productids_from_tags = array();
    public $products_total_subquery = "";

    public static function get_instance() {
        // If the single instance hasn't been set, set it now.
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        include_once('class-wcpv-frontend-get-settings.php');

        add_action('init', array($this, 'wcpv_create_excluded_items_array'));
        add_filter('posts_where_paged', array($this, 'wcpv_products'), 1, 2);

        // remove product categories from nav menu
        add_filter('wp_get_nav_menu_items', array($this, 'wcpv_nav_menu_categories_tags'), 10, 3);

        add_filter('get_terms', array($this, 'wcpv_get_terms'), 10, 3);

        // remove hidden products from cart
        add_action('template_redirect', array($this, 'wcpv_remove_hidden_products_from_cart'));

        // Relevanssi search plugin compatibility fix
        add_filter('relevanssi_results', array($this, 'relevanssi_query_filter'), 10, 1);

        // Show product through direct url
        $show_product_through_direct_url = get_option('show_product_through_direct_url');
        if ($show_product_through_direct_url) {
            add_action('pre_get_posts', array($this, 'show_product_page_through_direct_url_modify_main_query'));
            add_filter('wcpv_where', array($this, 'show_product_page_through_direct_url'), 10, 2);
        }

        if ($this->WC_VERSION_bigger_3()) {
            add_action('wp', array($this, 'check_if_single_product_page'));
            add_filter('woocommerce_product_related_posts_query', array($this, 'filter_woocommerce_product_related_posts_query'), 10, 2);
        }
    }

    //////////////////////////////////////////////////////////////////////
    ////////////////////FILTERS & ACTIONS/////////////////////////////////
    //////////////////////////////////////////////////////////////////////
    public function wcpv_create_excluded_items_array($query) {
        
        $settings = new WCPV_FRONTEND_GET_SETTINGS();
        $this->productids = $settings->productids;
        $this->has_productids = $settings->has_productids;
        $this->products_visibility = $settings->products_visibility;
        $this->categoryids = $settings->categoryids;
        $this->has_categoryids = $settings->has_categoryids;
        $this->categories_visibility = $settings->categories_visibility;
        $this->tagids = $settings->tagids;
        $this->has_tagids = $settings->has_tagids;
        $this->tags_visibility = $settings->tags_visibility;
        
        $this->productids = apply_filters('wcpv_productids', $this->productids);
        $this->has_productids = !empty($this->productids);
        $this->products_visibility = apply_filters('wcpv_products_visibility', $this->products_visibility);
        $this->categoryids = apply_filters('wcpv_categoryids', $this->categoryids);
        $this->has_categoryids = !empty($this->categoryids);
        $this->categories_visibility = apply_filters('wcpv_categories_visibility', $this->categories_visibility);
        $this->tagids = apply_filters('wcpv_tagids', $this->tagids);
        $this->has_tagids = !empty($this->tagids);
        $this->tags_visibility = apply_filters('wcpv_tags_visibility', $this->tags_visibility);
        if ($this->rules_are_applied()) {

            if ($this->has_categoryids) {
                $this->productids_from_categories = $this->get_productids_from_category_tag($this->categoryids, 'categories');
            }
            if ($this->has_tagids) {
                $this->productids_from_tags = $this->get_productids_from_category_tag($this->tagids, 'tags');
            }
            $this->products_total_subquery = $this->get_products_total_subquery();

            //Included products
            $this->include_products = $this->wcpv_visible_products();
            $this->exclude_categories = $this->get_excluded_tags_categories('product_cat');
            $this->exclude_tags = $this->get_excluded_tags_categories('product_tag');

            //For count performance
            $all_products = $this->wcpv_all_products();

            $this->categories_to_change_count = $this->get_tags_categories_tochange_count('product_cat', $all_products);
            $this->tags_to_change_count = $this->get_tags_categories_tochange_count('product_tag', $all_products);

            if ($this->WC_VERSION_bigger_3()) {
                WC_Cache_Helper::get_transient_version('product_query', true);
            }
        }
    }

    public function wcpv_products($where, $query) {
        if ($this->rules_are_applied()) {
            $post_type = $query->query_vars['post_type'];
            $product_category_check = isset($query->query_vars['taxonomy']) ? $query->query_vars['taxonomy'] : null;
            if ($post_type == 'product' || $post_type == 'any' || (isset($product_category_check) && ( $product_category_check == 'product_cat' || $product_category_check == 'product_tag'))) {

                $wcpv_all_products = isset($query->query_vars["wcpv_all_products"]) ? ($query->query_vars["wcpv_all_products"] == "true") : false;
                if (!$wcpv_all_products) {
                    $subquery = $this->products_total_subquery;

                    $posts_table_name = apply_filters('wcpv_posts_table_name_in_db', "");
                    if (!empty($posts_table_name)) {
                        $subquery = str_replace("ID", $posts_table_name . "." . "ID", $subquery);
                        $subquery = str_replace("post_type", $posts_table_name . "." . "post_type", $subquery);
                    }
                    $where .= apply_filters('wcpv_where', $subquery, $query);
                }
            }
        }
        return $where;
    }

    public function wcpv_nav_menu_categories_tags($items, $menu, $args) {
        if ($this->rules_are_applied()) {
            foreach ($items as $key => $item) {
                if ('taxonomy' == $item->type) {
                    if (( 'product_cat' == $item->object ) && in_array($item->object_id, $this->exclude_categories)) {
                        unset($items[$key]);
                    }
                    if (( 'product_tag' == $item->object ) && in_array($item->object_id, $this->exclude_tags)) {
                        unset($items[$key]);
                    }
                } elseif (('product' == $item->object) && !in_array($item->object_id, $this->include_products)) {
                    unset($items[$key]);
                }
            }
        }
        return $items;
    }

    public function wcpv_get_terms($terms, $taxonomies, $args) {
        if ($this->rules_are_applied()) {
            $new_terms = array();

            $only_ids = $args["fields"] == "ids";
            $wcpv_tax_init = isset($args["wcpv_tax"]) ? ($args["wcpv_tax"] == "init") : false;

            foreach ($terms as $key => $term) {
                $term_id = $only_ids ? $term : $term->term_id;
                if ((!in_array($term_id, $this->exclude_categories) && !in_array($term_id, $this->exclude_tags)) || $wcpv_tax_init) {
                    $new_terms[] = $term;

                    if (!$only_ids) {
                        $term_tax = $term->taxonomy;

                        //Categories
                        if ($term_tax == 'product_cat' && in_array($term_id, $this->categories_to_change_count)) {
                            $new_count = $this->get_categories_count($term_id);
                            $term->count = isset($new_count) ? $new_count : $term->count;
                        }
                        //Tags
                        else if ($term_tax == 'product_tag' && in_array($term_id, $this->tags_to_change_count)) {
                            $new_count = $this->get_tags_count($term_id);
                            $term->count = isset($new_count) ? $new_count : $term->count;
                        }
                    }
                }
            }

            $terms = $new_terms;
        }
        return $terms;
    }

    public function wcpv_remove_hidden_products_from_cart() {
        if ($this->rules_are_applied()) {
            // Run only in the Cart or Checkout Page
            if (is_cart() || is_checkout()) {
                // Cycle through each product in the cart
                foreach (WC()->cart->cart_contents as $prod_in_cart) {
                    // Get the Variation or Product ID
                    $prod_id = ( isset($prod_in_cart['variation_id']) && $prod_in_cart['variation_id'] != 0 ) ? $prod_in_cart['variation_id'] : $prod_in_cart['product_id'];
                    // Check if product belongs in include products
                    if (!in_array($prod_id, $this->include_products)) {
                        // Get it's unique ID within the Cart
                        $prod_unique_id = WC()->cart->generate_cart_id($prod_id);
                        // Remove it from the cart by un-setting it
                        unset(WC()->cart->cart_contents[$prod_unique_id]);
                    }
                }
            }
        }
    }

    // Relevanssi search plugin compatibility fix
    function relevanssi_query_filter($results) {
        if ($this->rules_are_applied()) {
            if (!empty($this->include_products)) {
                foreach ($results as $key => $value) {
                    $product_obj = wc_get_product($key); // check if search result ID belongs to a product
                    if (!in_array($key, $this->include_products) && ($product_obj != null)) { // only then, remove it from search results
                        unset($results[$key]);
                    }
                }
            }
        }
        return $results;
    }

    // Show product through direct url
    function show_product_page_through_direct_url_modify_main_query($query) {
        if ($this->rules_are_applied()) {
            if ($query->is_single() && $query->is_main_query()) {
                $query->set('is_product_page', 'true');
            }
        }
    }

    function show_product_page_through_direct_url($subquery, $query) {
        if ($this->rules_are_applied()) {
            if (isset($query->query_vars['is_product_page'])) {
                $subquery = '';
            }
        }
        return $subquery;
    }

    //Fixes for woocomerce 3 or higher
    function check_if_single_product_page($query) {
        if ($this->rules_are_applied()) {
            if (is_product()) {
                $product_obj = wc_get_product();
                $current_product_id = $product_obj->get_id();
                add_filter("pre_transient_wc_related_" . $current_product_id, array($this, 'reset_wc_related_transient'), 10, 1);
            }
        }
    }

    function reset_wc_related_transient($false) {
        return true;
    }

    function filter_woocommerce_product_related_posts_query($query, $this_id) {
        if ($this->rules_are_applied()) {
            $query["where"] = $query["where"] . $this->products_total_subquery;
        }
        return $query;
    }

    //////////////////////////////////////////////////////////////////////
    ////////////////////HELPERS///////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////
    public function get_excluded_tags_categories($type) {
        $all_not_empty = get_terms($type, array("fields" => "ids", "hide_empty" => true, "wcpv_tax" => "init"));
        $visibility_only_selected_tax = apply_filters('wcpv_visibility_only_selected_tax', false);
        if ($visibility_only_selected_tax) {
            $arrayofids = array();
            $visibility = "";
            $type2 = "";
            if ($type == 'product_cat') {
                if ($this->has_categoryids) {
                    $arrayofids = $this->categoryids;
                    $visibility = $this->categories_visibility;
                    $type2 = 'categories';
                }
            } else if ($type == 'product_tag') {
                if ($this->has_tagids) {
                    $arrayofids = $this->tagids;
                    $visibility = $this->tags_visibility;
                    $type2 = 'tags';
                }
            }
            if (!empty($arrayofids)) {
                $show = $visibility == 'include';
                $arrayofids = $this->get_wpml_ids($arrayofids, $type2);
                if ($show) {
                    return array_diff($all_not_empty, $arrayofids);
                } else
                    return $arrayofids;
            }

            return array();
        }


        $always_include = wp_get_object_terms($this->include_products, $type, array("fields" => "ids", "wcpv_tax" => "init"));
        return array_diff($all_not_empty, $always_include);
    }

    public function get_tags_categories_tochange_count($type, $all_products) {
        $exclude_products = array_diff($all_products, $this->include_products);
        return wp_get_object_terms($exclude_products, $type, array("fields" => "ids", "wcpv_tax" => "init"));
    }

    public function get_products_total_subquery() {
        $where = "";
        if ($this->has_categoryids || $this->has_tagids || $this->has_productids) {
            $where .= " AND ( ";
            if ($this->has_categoryids) {
                $where .= " ( ";
                $where .= $this->get_subquery('categories', $this->categoryids, $this->categories_visibility);
            }
            if ($this->has_tagids) {
                if ($this->has_categoryids)
                    $where .= $this->tags_visibility == 'include' ? " OR " : " AND ";
                $where .= $this->get_subquery('tags', $this->tagids, $this->tags_visibility);
            }

            if ($this->has_categoryids)
                $where .= " ) ";

            if ($this->has_productids) {
                if ($this->has_tagids || $this->has_categoryids)
                    $where .= $this->products_visibility == 'include' ? " OR " : " AND ";
                $where .= $this->get_subquery('products', $this->productids, $this->products_visibility);
            }
            $where .= " )";
        }
        return $where;
    }

    public function wcpv_get_term($term, $taxonomy) {
        //Categories
        if ($term->taxonomy == 'product_cat') {
            $new_count = $this->get_categories_count($term->term_id);
            $term->count = isset($new_count) ? $new_count : $term->count;
        }
        //Tags
        else if ($term->taxonomy == 'product_tag') {
            $new_count = $this->get_tags_count($term->term_id);
            $term->count = isset($new_count) ? $new_count : $term->count;
        }
        return $term;
    }

    public function get_categories_count($term_id) {
        // Cache the value.
        if (!isset($this->categories_count_posts[$term_id])) {
            $this->categories_count_posts[$term_id] = $this->wcpv_count_taxonomy_posts("product_cat", $term_id);
        }
        // Return cached value.
        return $this->categories_count_posts[$term_id];
    }

    public function get_tags_count($term_id) {
        // Cache the value.
        if (!isset($this->tags_count_posts[$term_id])) {
            $this->tags_count_posts[$term_id] = $this->wcpv_count_taxonomy_posts("product_tag", $term_id);
        }
        // Return cached value.
        return $this->tags_count_posts[$term_id];
    }

    private function wcpv_count_taxonomy_posts($taxonomy, $tax_id) {
        $args = array(
            'post_type' => 'product',
            'hierarchical' => true,
            'posts_per_page' => -1,
            'fields' => 'ids',
            'no_found_rows' => true,
            'tax_query' => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => $taxonomy,
                    'field' => 'term_id',
                    'terms' => $tax_id
                ),
            ),
            'suppress_filters' => false
        );
        $posts = get_posts($args);
        $count = count($posts);
        return $count;
    }

    private function wcpv_visible_products() {
        $part_args = array(
            'post_type' => 'product',
            'hierarchical' => true,
            'posts_per_page' => -1,
            'fields' => 'ids'
        );
        $query = new WP_Query($part_args);
        return $query->posts;
    }

    private function wcpv_all_products() {
        $part_args = array(
            'post_type' => 'product',
            'hierarchical' => true,
            'posts_per_page' => -1,
            'fields' => 'ids',
            'wcpv_all_products' => 'true'
        );
        $query = new WP_Query($part_args);
        return $query->posts;
    }

    private function get_subquery($type, $arrayofids, $visibility) {
        global $wpdb;
        if (!empty($arrayofids)) {
            $operator = $visibility == 'include' ? "IN" : "NOT IN";
            switch ($type) {
                case 'products':
                    $arrayofids = $this->get_wpml_ids($arrayofids, 'products');
                    $arrayofids = join(',', $arrayofids);
                    return "( post_type='product' AND ID " . $operator . " ($arrayofids))";
                    break;
                case 'categories':
                    $arrayofids = join(',', $this->productids_from_categories);
                    return "( ID " . $operator . " ($arrayofids))";
                case 'tags':
                    $arrayofids = join(',', $this->productids_from_tags);
                    return "( ID " . $operator . " ($arrayofids))";
                    break;
                default:
                    return "";
            }
        }
    }

    private function get_wpml_ids($arrayofids, $type) {
        if (function_exists('icl_object_id')) { // if wpml activated
            if ($type == 'categories') {
                $icl_type = 'category';
            } else if ($type == 'tags') {
                $icl_type = 'post_tag';
            } else if ($type == 'products') {
//                $arrayofids = explode(',', $arrayofids[0]);
                $icl_type = 'product';
            }
            foreach ($arrayofids as $value) {
                $trans = icl_object_id($value, $icl_type, false, ICL_LANGUAGE_CODE);
                if (!empty($trans)) {
                    $wpml_ids[] = $trans;
                }
            }
            if (isset($wpml_ids)) {
                $arrayofids = $wpml_ids;
            }
        }
        return $arrayofids;
    }

    private function get_productids_from_category_tag($arrayofids, $type) {
        $arrayofids = $this->get_wpml_ids($arrayofids, $type);
        $arrayofids = join(',', $arrayofids);
        global $wpdb;
        $query = "(SELECT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN 
                                ( 
                                    SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy} WHERE term_id IN ($arrayofids) 
                                )
                            )";
        $productids = $wpdb->get_results($query
        );
        $arr = array();

        foreach ($productids as $pr) {
            $arr[] = $pr->object_id;
        }
        return $arr;
    }

    //Fixes for woocomerce 3 or higher
    public function WC_VERSION_bigger_3() {
        $wc_version_check = "3.0.0";
        if (( defined('WC_VERSION') && version_compare(WC_VERSION, $wc_version_check, '<') ) || ( isset($woocommerce->version) && version_compare($woocommerce->version, $wc_version_check, '<') ))
            return false;
        return true;
    }

    function rules_are_applied() {
        return ($this->has_categoryids || $this->has_tagids || $this->has_productids);
    }

}
