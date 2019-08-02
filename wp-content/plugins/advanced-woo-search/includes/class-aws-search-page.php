<?php
/**
 * Integrate with WP_Query
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'AWS_Search_Page' ) ) :

    /**
     * Class for plugin search
     */
    class AWS_Search_Page {

        /**
         * Is set only when we are within a multisite loop
         *
         * @var bool|WP_Query
         */
        private $query_stack = array();

        private $posts_by_query = array();

        private $data = array();

        /**
         * Return a singleton instance of the current class
         *
         * @return object
         */
        public static function factory() {
            static $instance = false;

            if ( ! $instance ) {
                $instance = new self();
                $instance->setup();
            }

            return $instance;
        }

        /**
         * Placeholder
         */
        public function __construct() {}

        /**
         * Setup actions and filters for all things settings
         */
        public function setup() {

            // Make sure we return nothing for MySQL posts query
            add_filter( 'posts_request', array( $this, 'filter_posts_request' ), 10, 2 );

            // Query and filter to WP_Query
            add_filter( 'the_posts', array( $this, 'filter_the_posts' ), 10, 2 );

            // Add header
		    add_action( 'pre_get_posts', array( $this, 'action_pre_get_posts' ), 5 );

            // Overwrite query
            add_action( 'pre_get_posts', array( $this, 'pre_get_posts_overwrite' ), 999 );

            // Nukes the FOUND_ROWS() database query
		    add_filter( 'found_posts_query', array( $this, 'filter_found_posts_query' ), 5, 2 );

            // Update filters links
            add_filter( 'woocommerce_layered_nav_link', array( $this, 'woocommerce_layered_nav_link' ) );

            add_filter( 'posts_pre_query', array( $this, 'posts_pre_query' ), 10, 2 );

            add_filter( 'body_class', array( $this, 'body_class' ), 999 );

        }

        /**
        * Filter query string used for get_posts(). Query for posts and save for later.
        * Return a query that will return nothing.
        *
        * @param string $request
        * @param object $query
        * @return string
        */
        public function filter_posts_request( $request, $query ) {
            if ( ! $this->aws_searchpage_enabled( $query ) ) {
                return $request;
            }

            $new_posts = array();

            $search_query = $query->query_vars['s'];
            $search_res = $this->search( $search_query, $query );
            $posts_per_page = apply_filters( 'aws_posts_per_page', $query->get( 'posts_per_page' ) );

            $query->found_posts = count( $search_res['all'] );
            $query->max_num_pages = ceil( count( $search_res['all'] ) / $posts_per_page );

            foreach ( $search_res['products'] as $post_array ) {
                $post = new stdClass();

                $post_array = (array) $post_array;
                $post_data = $post_array['post_data'];

                $post->ID = $post_data->ID;
                $post->site_id = get_current_blog_id();

                if ( ! empty( $post_data->site_id ) ) {
                    $post->site_id = $post_data->site_id;
                }

                $post_return_args = array(
                    'post_type',
                    'post_author',
                    'post_name',
                    'post_status',
                    'post_title',
                    'post_parent',
                    'post_content',
                    'post_excerpt',
                    'post_date',
                    'post_date_gmt',
                    'post_modified',
                    'post_modified_gmt',
                    'post_mime_type',
                    'comment_count',
                    'comment_status',
                    'ping_status',
                    'menu_order',
                    'permalink',
                    'terms',
                    'post_meta'
                );

                foreach ( $post_return_args as $key ) {
                    if ( isset( $post_data->$key ) ) {
                        $post->$key = $post_data->$key;
                    }
                }

                $post->awssearch = true; // Super useful for debugging

                if ( $post ) {
                    $new_posts[] = $post;
                }
            }


            $this->posts_by_query[spl_object_hash( $query )] = $new_posts;


            global $wpdb;

            return "SELECT * FROM $wpdb->posts WHERE 1=0";

        }

        /**
         * Filter the posts array.
         *
         * @param array $posts
         * @param object $query
         * @return array|null
         */
        public function posts_pre_query( $posts, $query ) {

            if ( isset( $_GET['type_aws'] ) && isset( $query->query_vars['s'] ) && $query->query && isset( $query->query['fields'] ) && $query->query['fields'] == 'ids' &&
                isset( $this->data['is_elementor'] ) && $this->data['is_elementor']  )
            {

                $products_ids = array();
                $posts_per_page = apply_filters( 'aws_posts_per_page', $query->get( 'posts_per_page' ) );
                $search_query = $_GET['s'];

                $search_res = $this->search( $search_query, $query );

                $query->found_posts = count( $search_res['all'] );
                $query->max_num_pages = ceil( count( $search_res['all'] ) / $posts_per_page );

                foreach ( $search_res['products'] as $product ) {
                    $products_ids[] = $product['id'];
                }

                $posts = $products_ids;

            }
            return $posts;
        }

        /**
         * Filter the posts array to contain ES query results in EP_Post form. Pull previously queried posts.
         *
         * @param array $posts
         * @param object $query
         * @return array
         */
        public function filter_the_posts( $posts, $query ) {
            if ( ! $this->aws_searchpage_enabled( $query )  || ! isset( $this->posts_by_query[spl_object_hash( $query )] ) ) {
                return $posts;
            }

            $new_posts = $this->posts_by_query[spl_object_hash( $query )];

            return $new_posts;

        }

        /**
         * Disables cache_results, adds header.
         *
         * @param $query
         */
        public function action_pre_get_posts( $query ) {
            if ( ! $this->aws_searchpage_enabled( $query ) ) {
                return;
            }

            /**
             * `cache_results` defaults to false but can be enabled
             */
            $query->set( 'cache_results', false );
            if ( ! empty( $query->query['cache_results'] ) ) {
                $query->set( 'cache_results', true );
            }

            if ( ! headers_sent() ) {
                /**
                 * Manually setting a header as $wp_query isn't yet initialized
                 * when we call: add_filter('wp_headers', 'filter_wp_headers');
                 */
                header( 'X-AWS-Search: true' );
            }
        }

        /**
         * Make necessary changes in main query.
         *
         * @param $query
         */
        public function pre_get_posts_overwrite( $query ) {
            if ( ! $this->aws_searchpage_enabled( $query ) ) {
                return;
            }

            // Divi builder fix
            if ( defined( 'ET_CORE' ) && $GLOBALS && isset( $GLOBALS['et_builder_used_in_wc_shop'] ) && $GLOBALS['et_builder_used_in_wc_shop'] ) {

                $GLOBALS['et_builder_used_in_wc_shop'] = false;

                $query->set( 'page_id', 0 );
                $query->set( 'post_type', 'product' );
                $query->set( 'posts_per_page', apply_filters( 'aws_posts_per_page', get_option( 'posts_per_page' ) ) );
                $query->set( 'wc_query', 'product_query' );
                $query->set( 'meta_query', array() );

                $query->is_singular          = false;
                $query->is_page              = false;
                $query->is_post_type_archive = true;
                $query->is_archive           = true;

            }

        }

        /**
         * Remove the found_rows from the SQL Query
         *
         * @param string $sql
         * @param object $query
         * @return string
         */
        public function filter_found_posts_query( $sql, $query ) {
            if ( ! $this->aws_searchpage_enabled( $query ) ) {
                return $sql;
            }

            return '';
        }

        /**
         * Perform the search.
         *
         * @param string $s
         * @param object $query
         * @return array
         */
        private function search( $s, $query ) {

            $posts_array = (array) aws_search( $s );
            $posts_per_page = apply_filters( 'aws_posts_per_page', $query->get( 'posts_per_page' ) );
            $post_array_products = $posts_array['products'];

            // Filter and order output
            if ( $post_array_products && is_array( $post_array_products ) && ! empty( $post_array_products ) ) {
                $post_array_products = AWS()->order( $post_array_products, $query );
            }

            $paged  = $query->query_vars['paged'] ? $query->query_vars['paged'] : 1;
            $offset = ( $paged > 1 ) ? $paged * $posts_per_page - $posts_per_page : 0;

            $products = array_slice( $post_array_products, $offset, $posts_per_page );

            return array(
                'all'      => $post_array_products,
                'products' => $products
            );

        }

        /*
         * Update links for WooCommerce filter widgets
         */
        public function woocommerce_layered_nav_link( $link ) {
            if ( ! isset( $_GET['type_aws'] ) ) {
                return $link;
            }

            $first_char = '&';

            if ( strpos( $link, '?' ) === false ) {
                $first_char = '?';
            }

            if ( isset( $_GET['type_aws'] ) && strpos( $link, 'type_aws' ) === false ) {
                $link = $link . $first_char . 'type_aws=true';
            }

            return $link;

        }

        /*
         * Check some strings inside body classes
         */
        function body_class( $classes ) {
            foreach( $classes as $class ) {
                if ( strpos( $class, 'elementor-page-' ) !== false ) {
                    $this->data['is_elementor'] = true;
                    break;
                }
            }
            return $classes;
        }

        /**
         * Check if we should override default search query
         *
         * @param string $query
         * @return bool
         */
        private function aws_searchpage_enabled( $query ) {
            $enabled = true;

            $post_type_product = ( $query->get( 'post_type' ) && is_string( $query->get( 'post_type' ) ) && $query->get( 'post_type' ) === 'product' ) ? true :
                ( ( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'product' ) ? true : false );

            if ( ( isset( $query->query_vars['s'] ) && ! isset( $_GET['type_aws'] ) ) ||
                ! isset( $query->query_vars['s'] ) ||
                ! $query->is_search() ||
                ! $post_type_product
            ) {
                $enabled = false;
            }

            return apply_filters( 'aws_searchpage_enabled', $enabled, $query );
        }

    }


endif;

AWS_Search_Page::factory();