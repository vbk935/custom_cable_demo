<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'AWS_Search' ) ) :

    /**
     * Class for plugin search
     */
    class AWS_Search {

        /**
         * @var AWS_Search Array of all plugin data $data
         */
        private $data = array();

        /**
         * @var AWS_Search Current language $lang
         */
        private $lang = 0;

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
         * Constructor
         */
        public function __construct() {}

        /**
         * Setup actions and filters for all things settings
         */
        public function setup() {

            $this->data['settings'] = get_option( 'aws_settings' );

            add_action( 'wp_ajax_aws_action', array( $this, 'action_callback' ) );
            add_action( 'wp_ajax_nopriv_aws_action', array( $this, 'action_callback' ) );

        }
        
        /*
         * AJAX call action callback
         */
        public function action_callback() {

            if ( ! defined( 'DOING_AJAX' ) ) {
                define( 'DOING_AJAX', true );
            }

            echo json_encode( $this->search() );

            die;

        }
        
        /*
         * Search
         */
        public function search( $keyword = ''  ) {

            global $wpdb;

            $this->lang = isset( $_REQUEST['lang'] ) ? sanitize_text_field( $_REQUEST['lang'] ) : '';

            if ( $this->lang ) {
                do_action( 'wpml_switch_language', $this->lang );
            }

            $cache = AWS()->get_settings( 'cache' );

            $s = $keyword ? esc_attr( $keyword ) : esc_attr( $_POST['keyword'] );
            $s = htmlspecialchars_decode( $s );

            $this->data['s_nonormalize'] = $s;

            $s = AWS_Helpers::normalize_string( $s );


            /**
             * Fires each time when performing the search
             * @since 1.59
             * @param string $s Search query
             */
            do_action( 'aws_search_start', $s );


            $cache_option_name = '';
            
            if ( $cache === 'true' && ! $keyword  ) {
                $cache_option_name = AWS()->cache->get_cache_name( $s );
                $res = AWS()->cache->get_from_cache_table( $cache_option_name );
                if ( $res ) {
                    $cached_value = json_decode( $res );
                    if ( $cached_value && ! empty( $cached_value ) ) {
                        return $cached_value;
                    }
                }
            }

            $show_cats     = AWS()->get_settings( 'show_cats' );
            $show_tags     = AWS()->get_settings( 'show_tags' );
            $results_num   = $keyword ? apply_filters( 'aws_page_results', 100 ) : AWS()->get_settings( 'results_num' );
            $search_in     = AWS()->get_settings( 'search_in' );
            $outofstock    = AWS()->get_settings( 'outofstock' );

            $search_in_arr = explode( ',',  AWS()->get_settings( 'search_in' ) );

            // Search in title if all options is disabled
            if ( ! $search_in ) {
                $search_in_arr = array( 'title' );
            }

            $products_array = array();
            $tax_to_display = array();
            $custom_tax_array = array();

            $this->data['s'] = $s;
            $this->data['results_num']  = $results_num ? $results_num : 10;
            $this->data['search_terms'] = array();
            $this->data['search_in']    = $search_in_arr;
            $this->data['outofstock']   = $outofstock;

            $search_array = array_unique( explode( ' ', $s ) );

            $search_array = AWS_Helpers::filter_stopwords( $search_array );

            if ( is_array( $search_array ) && ! empty( $search_array ) ) {
                foreach ( $search_array as $search_term ) {
                    $search_term = trim( $search_term );
                    if ( $search_term ) {
                        $this->data['search_terms'][] = $search_term;
                    }
                }
            }

//            if ( empty( $this->data['search_terms'] ) ) {
//                $this->data['search_terms'][] = '';
//            }

            if ( ! empty( $this->data['search_terms'] ) ) {

                $posts_ids = $this->query_index_table();

                /**
                 * Filters array of products ids
                 *
                 * @since 1.53
                 *
                 * @param array $posts_ids Array of products ids
                 * @param string $s Search query
                 */
                $posts_ids = apply_filters( 'aws_search_results_products_ids', $posts_ids, $s );


                $products_array = $this->get_products( $posts_ids );

                /**
                 * Filters array of products before they displayed in search results
                 *
                 * @since 1.42
                 *
                 * @param array $products_array Array of products results
                 * @param string $s Search query
                 */
                $products_array = apply_filters( 'aws_search_results_products', $products_array, $s );

                if ( $show_cats === 'true' ) {
                    $tax_to_display[] = 'product_cat';
                }

                if ( $show_tags === 'true' ) {
                    $tax_to_display[] = 'product_tag';
                }

                /**
                 * Filters array of custom taxonomies that must be displayed in search results
                 *
                 * @since 1.68
                 *
                 * @param array $taxonomies_archives Array of custom taxonomies
                 * @param string $s Search query
                 */
                $taxonomies_archives = apply_filters( 'aws_search_results_tax_archives', $tax_to_display, $s );

                if ( $taxonomies_archives && is_array( $taxonomies_archives ) && ! empty( $taxonomies_archives ) ) {

                    foreach( $taxonomies_archives as $taxonomies_archive_name ) {
                        $res = $this->get_taxonomies( $taxonomies_archive_name );

                        if ( $taxonomies_archive_name === 'product_cat' ) {
                            $res = apply_filters( 'aws_search_results_categories', $res, $s );
                        }

                        if ( $taxonomies_archive_name === 'product_tag' ) {
                            $res = apply_filters( 'aws_search_results_tags', $res, $s );
                        }

                        $custom_tax_array[$taxonomies_archive_name] = $res;
                    }

                }

            }


            $result_array = array(
                'tax'      => $custom_tax_array,
                'products' => $products_array
            );


            /**
             * Filters array of all results data before they displayed in search results
             *
             * @since 1.43
             *
             * @param array $brands_array Array of products data
             * @param string $s Search query
             */
            $result_array = apply_filters( 'aws_search_results_all', $result_array, $s );

            if ( $cache === 'true' && ! $keyword  ) {
                AWS()->cache->insert_into_cache_table( $cache_option_name, $result_array );
            }

            return $result_array;

        }

        /*
         * Query in index table
         */
        private function query_index_table() {

            global $wpdb;

            $table_name = $wpdb->prefix . AWS_INDEX_TABLE_NAME;

            $search_in_arr    = $this->data['search_in'];
            $results_num      = $this->data['results_num'];
            $outofstock       = $this->data['outofstock'];


            $reindex_version = get_option( 'aws_reindex_version' );

            $query = array();

            $query['search'] = '';
            $query['source'] = '';
            $query['relevance'] = '';
            $query['stock'] = '';
            $query['visibility'] = '';
            $query['exclude_products'] = '';
            $query['lang'] = '';

            $search_array = array();
            $source_array = array();
            $relevance_array = array();
            $new_relevance_array = array();


            /**
             * Filters array of search terms before generating SQL query
             *
             * @since 1.49
             *
             * @param array $this->data['search_terms'] Array of search terms
             */
            $this->data['search_terms'] = apply_filters( 'aws_search_terms', $this->data['search_terms'] );


            foreach ( $this->data['search_terms'] as $search_term ) {

                $search_term_len = strlen( $search_term );

                $relevance_title        = 200 + 20 * $search_term_len;
                $relevance_content      = 35 + 4 * $search_term_len;
                $relevance_title_like   = 40 + 2 * $search_term_len;
                $relevance_content_like = 35 + 1 * $search_term_len;


                $search_term_norm = preg_replace( '/(s|es|ies)$/i', '', $search_term );

                if ( $search_term_norm && $search_term_len > 3 && strlen( $search_term_norm ) > 2 ) {
                    $search_term = $search_term_norm;
                }

                $like = '%' . $wpdb->esc_like( $search_term ) . '%';


                if ( $search_term_len > 1 ) {
                    $search_array[] = $wpdb->prepare( '( term LIKE %s )', $like );
                } else {
                    $search_array[] = $wpdb->prepare( '( term = "%s" )', $search_term );
                }

                foreach ( $search_in_arr as $search_in_term ) {

                    switch ( $search_in_term ) {

                        case 'title':
                            $relevance_array['title'][] = $wpdb->prepare( "( case when ( term_source = 'title' AND term = '%s' ) then {$relevance_title} * count else 0 end )", $search_term );
                            $relevance_array['title'][] = $wpdb->prepare( "( case when ( term_source = 'title' AND term LIKE %s ) then {$relevance_title_like} * count else 0 end )", $like );
                            break;

                        case 'content':
                            $relevance_array['content'][] = $wpdb->prepare( "( case when ( term_source = 'content' AND term = '%s' ) then {$relevance_content} * count else 0 end )", $search_term );
                            $relevance_array['content'][] = $wpdb->prepare( "( case when ( term_source = 'content' AND term LIKE %s ) then {$relevance_content_like} * count else 0 end )", $like );
                            break;

                        case 'excerpt':
                            $relevance_array['content'][] = $wpdb->prepare( "( case when ( term_source = 'excerpt' AND term = '%s' ) then {$relevance_content} * count else 0 end )", $search_term );
                            $relevance_array['content'][] = $wpdb->prepare( "( case when ( term_source = 'excerpt' AND term LIKE %s ) then {$relevance_content_like} * count else 0 end )", $like );
                            break;

                        case 'category':
                            $relevance_array['category'][] = $wpdb->prepare( "( case when ( term_source = 'category' AND term = '%s' ) then 35 else 0 end )", $search_term );
                            $relevance_array['category'][] = $wpdb->prepare( "( case when ( term_source = 'category' AND term LIKE %s ) then 5 else 0 end )", $like );
                            break;

                        case 'tag':
                            $relevance_array['tag'][] = $wpdb->prepare( "( case when ( term_source = 'tag' AND term = '%s' ) then 35 else 0 end )", $search_term );
                            $relevance_array['tag'][] = $wpdb->prepare( "( case when ( term_source = 'tag' AND term LIKE %s ) then 5 else 0 end )", $like );
                            break;

                        case 'sku':
                            $relevance_array['sku'][] = $wpdb->prepare( "( case when ( term_source = 'sku' AND term = '%s' ) then 300 else 0 end )", $search_term );
                            $relevance_array['sku'][] = $wpdb->prepare( "( case when ( term_source = 'sku' AND term LIKE %s ) then 50 else 0 end )", $like );
                            break;

                    }

                }

            }

            // Sort 'relevance' queries in the array by search priority
            foreach ( $search_in_arr as $search_in_item ) {
                if ( isset( $relevance_array[$search_in_item] ) ) {
                    $new_relevance_array[$search_in_item] = implode( ' + ', $relevance_array[$search_in_item] );
                }
            }

            foreach ( $search_in_arr as $search_in_term ) {
                $source_array[] = "term_source = '{$search_in_term}'";
            }

            $query['relevance'] = sprintf( ' (SUM( %s )) ', implode( ' + ', $new_relevance_array ) );
            $query['search'] = sprintf( ' AND ( %s )', implode( ' OR ', $search_array ) );
            $query['source'] = sprintf( ' AND ( %s )', implode( ' OR ', $source_array ) );


            if ( $reindex_version && version_compare( $reindex_version, '1.16', '>=' ) ) {

                if ( $outofstock !== 'true' ) {
                    $query['stock'] = " AND in_stock = 1";
                }

                $query['visibility'] = " AND NOT visibility LIKE '%hidden%'";

            }


            /**
             * Exclude certain products from search
             *
             * @since 1.58
             *
             * @param array
             */
            $exclude_products_filter = apply_filters( 'aws_exclude_products', array() );

            if ( $exclude_products_filter && is_array( $exclude_products_filter ) && ! empty( $exclude_products_filter ) ) {
                $query['exclude_products'] = sprintf( ' AND ( id NOT IN ( %s ) )', implode( ',', $exclude_products_filter ) );
            }


            if ( $this->lang ) {
                $current_lang = $this->lang;
            } else {
                $current_lang = AWS_Helpers::get_lang();
            }

            /**
             * Filter current language code
             * @since 1.59
             * @param string $current_lang Lang code
             */
            $current_lang = apply_filters( 'aws_search_current_lang', $current_lang );

            if ( $current_lang && $reindex_version && version_compare( $reindex_version, '1.20', '>=' ) ) {
                $query['lang'] = $wpdb->prepare( " AND ( lang LIKE %s OR lang = '' )", $current_lang );
            }

            /**
             * Filter search query parameters
             * @since 1.67
             * @param array $query Query parameters
             */
            $query = apply_filters( 'aws_search_query_array', $query );

            $sql = "SELECT
                    distinct ID,
                    {$query['relevance']} as relevance
                FROM
                    {$table_name}
                WHERE
                    1=1
                {$query['source']}
                {$query['search']}
                {$query['stock']}
                {$query['visibility']}
                {$query['exclude_products']}
                {$query['lang']}
                GROUP BY ID
                ORDER BY
                    relevance DESC, id DESC
				LIMIT 0, {$results_num}
		    ";


            $posts_ids = $this->get_posts_ids( $sql );

            return $posts_ids;

        }

        /*
         * Get array of included to search result posts ids
         */
        private function get_posts_ids( $sql ) {

            global $wpdb;

            $posts_ids = array();

            $search_results = $wpdb->get_results( $sql );


            if ( !empty( $search_results ) && !is_wp_error( $search_results ) && is_array( $search_results ) ) {
                foreach ( $search_results as $search_result ) {
                    $post_id = intval( $search_result->ID );
                    if ( ! in_array( $post_id, $posts_ids ) ) {
                        $posts_ids[] = $post_id;
                    }
                }
            }

            unset( $search_results );

            return $posts_ids;

        }

        /*
         * Get products info
         */
        private function get_products( $posts_ids ) {

            $products_array = array();

            if ( count( $posts_ids ) > 0 ) {

                $show_excerpt         = AWS()->get_settings( 'show_excerpt' );
                $excerpt_source       = AWS()->get_settings( 'desc_source' );
                $excerpt_length       = AWS()->get_settings( 'excerpt_length' );
                $mark_search_words    = AWS()->get_settings( 'mark_words' );
                $show_price           = AWS()->get_settings( 'show_price' );
                $show_outofstockprice = AWS()->get_settings( 'show_outofstock_price' );
                $show_sale            = AWS()->get_settings( 'show_sale' );
                $show_image           = AWS()->get_settings( 'show_image' );
                $show_sku             = AWS()->get_settings( 'show_sku' );
                $show_stock_status    = AWS()->get_settings( 'show_stock' );
                $show_featured        = AWS()->get_settings( 'show_featured' );


                foreach ( $posts_ids as $post_id ) {

                    $product = wc_get_product( $post_id );

                    if( ! is_a( $product, 'WC_Product' ) ) {
                        continue;
                    }

                    /**
                     * Filter additional product data
                     * @since 1.60
                     * @param array $this->data Additional data
                     * @param int $post_id Product id
                     * @param object $product Product
                     */
                    $this->data = apply_filters( 'aws_search_data_params', $this->data, $post_id, $product );

                    $post_data = get_post( $post_id );

                    $title = $product->get_title();
                    $title = AWS_Helpers::html2txt( $title );

                    $excerpt      = '';
                    $price        = '';
                    $on_sale      = '';
                    $image        = '';
                    $sku          = '';
                    $stock_status = '';
                    $featured     = '';


                    if ( $show_excerpt === 'true' ) {
                        $excerpt = ( $excerpt_source === 'excerpt' && $post_data->post_excerpt ) ? $post_data->post_excerpt : $post_data->post_content;
                        $excerpt = AWS_Helpers::html2txt( $excerpt );
                        $excerpt = str_replace('"', "'", $excerpt);
                        $excerpt = strip_shortcodes( $excerpt );
                        $excerpt = AWS_Helpers::strip_shortcodes( $excerpt );
                    }

                    if ( $mark_search_words === 'true'  ) {

                        $marked_content = $this->mark_search_words( $title, $excerpt );

                        $title   = $marked_content['title'];

                        if ( $marked_content['content'] ) {
                            $excerpt = $marked_content['content'];
                        } else {
                            $excerpt = wp_trim_words( $excerpt, $excerpt_length, '...' );
                        }

                    } else {
                        $excerpt = wp_trim_words( $excerpt, $excerpt_length, '...' );
                    }

                    if ( $show_price === 'true' && ( $product->is_in_stock() || ( ! $product->is_in_stock() && $show_outofstockprice === 'true' ) ) ) {
                        $price = $product->get_price_html();
                    }

                    if ( $show_sale === 'true' && ( $product->is_in_stock() || ( ! $product->is_in_stock() && $show_outofstockprice === 'true' ) ) ) {
                        $on_sale = $product->is_on_sale();
                    }

                    if ( $show_image === 'true' ) {
                        $image_id = $product->get_image_id();
                        $image_attributes = wp_get_attachment_image_src( $image_id );
                        $image = $image_attributes[0];
                    }

                    if ( $show_sku === 'true' ) {
                        $sku = $product->get_sku();
                    }

                    if ( $show_featured === 'true' ) {
                        $featured = $product->is_featured();
                    }

                    if ( $show_stock_status === 'true' ) {
                        if ( $product->is_in_stock() ) {
                            $stock_status = array(
                                'status' => true,
                                'text'   => esc_html__( 'In stock', 'aws' )
                            );
                        } else {
                            $stock_status = array(
                                'status' => false,
                                'text'   => esc_html__( 'Out of stock', 'aws' )
                            );
                        }
                    }

                    if ( method_exists( $product, 'get_price' ) ) {
                        $f_price = $product->get_price();
                    }

                    if ( method_exists( $product, 'get_average_rating' ) ) {
                        $f_rating  = $product->get_average_rating();
                    }

                    if ( method_exists( $product,'get_review_count' ) ) {
                        $f_reviews = $product->get_review_count();
                    }

//                    $categories = $product->get_categories( ',' );
//                    $tags = $product->get_tags( ',' );

                    $title   = apply_filters( 'aws_title_search_result', $title, $post_id, $product );
                    $excerpt = apply_filters( 'aws_excerpt_search_result', $excerpt, $post_id, $product );

                    $new_result = array(
                        'id'           => $post_id,
                        'title'        => $title,
                        'excerpt'      => $excerpt,
                        'link'         => get_permalink( $post_id ),
                        'image'        => $image,
                        'price'        => $price,
                        'on_sale'      => $on_sale,
                        'sku'          => $sku,
                        'stock_status' => $stock_status,
                        'featured'     => $featured,
                        'f_price'      => $f_price,
                        'f_rating'     => $f_rating,
                        'f_reviews'    => $f_reviews,
                        'post_data'    => $post_data
                    );

                    $products_array[] = $new_result;
                }

            }

            /**
             * Filter products array before output
             * @since 1.60
             * @param array $products_array Products array
             * @param array $this->data Additional data
             */
            $products_array = apply_filters( 'aws_search_pre_filter_products', $products_array, $this->data );

            return $products_array;

        }

        /*
         * Mark search words
         */
        private function mark_search_words( $title, $content ) {

            $pattern = array();
            $exact_pattern = array();
            $exact_words = array();
            $words = array();

            foreach( $this->data['search_terms'] as $search_in ) {

                $exact_words[] = '\b' . $search_in . '\b';
                $exact_pattern[] = '(\b' . $search_in . '\b)+';

                if ( strlen( $search_in ) > 1 ) {
                    $pattern[] = '(' . $search_in . ')+';
                    $words[] = $search_in;
                } else {
                    $pattern[] = '\b[' . $search_in . ']{1}\b';
                    $words[] = '\b' . $search_in . '\b';
                }

            }

            usort( $exact_words, array( $this, 'sort_by_length' ) );
            $exact_words = implode( '|', $exact_words );

            usort( $words, array( $this, 'sort_by_length' ) );
            $words = implode( '|', $words );

            usort( $exact_pattern, array( $this, 'sort_by_length' ) );
            $exact_pattern = implode( '|', $exact_pattern );
            $exact_pattern = sprintf( '/%s/i', $exact_pattern );

            usort( $pattern, array( $this, 'sort_by_length' ) );
            $pattern = implode( '|', $pattern );
            $pattern = sprintf( '/%s/i', $pattern );


            preg_match( '/([^.?!]*?)(' . $exact_words . '){1}(.*?[.!?])/i', $content, $matches );

            if ( ! isset( $matches[0] ) ) {
                preg_match( '/([^.?!]*?)(' . $words . '){1}(.*?[.!?])/i', $content, $matches );
            }

            if ( ! isset( $matches[0] ) ) {
                preg_match( '/([^.?!]*?)(.*?)(.*?[.!?])/i', $content, $matches );
            }

            if ( isset( $matches[0] ) ) {

                $content = $matches[0];

                // Trim to long content
                if (str_word_count(strip_tags($content)) > 34) {

                    if (str_word_count(strip_tags($matches[3])) > 34) {
                        $matches[3] = wp_trim_words($matches[3], 30, '...');
                    }

                    $content = '...' . $matches[2] . $matches[3];

                }

            } else {

                $content = '';

            }

            $title = preg_replace($pattern, '<strong>${0}</strong>', $title );
            $content = preg_replace( $pattern, '<strong>${0}</strong>', $content );

            return array(
                'title'   => $title,
                'content' => $content
            );

        }

        /*
         * Sort array by its values length
         */
        private function sort_by_length( $a, $b ) {
            return strlen( $b ) - strlen( $a );
        }

        /*
         * Query product taxonomies
         */
        private function get_taxonomies( $taxonomy ) {

            global $wpdb;

            $result_array = array();
            $search_array = array();
            $relevance_array = array();
            $excludes = '';
            $search_query = '';
            $relevance_query = '';

            $filtered_terms = $this->data['search_terms'];
            $filtered_terms[] = $this->data['s_nonormalize'];

            /**
             * Max number of terms to show
             * @since 1.73
             * @param int
             */
            $terms_number = apply_filters( 'aws_search_terms_number', 10 );

            if ( $filtered_terms && ! empty( $filtered_terms ) ) {

                foreach ( $filtered_terms as $search_term ) {

                    $search_term_len = strlen( $search_term );
                    $relevance = 40 + 2 * $search_term_len;
                    $search_term_norm = preg_replace( '/(s|es|ies)$/i', '', $search_term );

                    if ( $search_term_norm && $search_term_len > 3 && strlen( $search_term_norm ) > 2 ) {
                        $search_term = $search_term_norm;
                    }

                    $like = '%' . $wpdb->esc_like($search_term) . '%';
                    $search_array[] = $wpdb->prepare('( name LIKE %s )', $like);

                    $relevance_array[] = $wpdb->prepare( "( case when ( name LIKE %s ) then {$relevance} else 0 end )", $like );

                }

            } else {

                return $result_array;

            }

            if ( $relevance_array && ! empty( $relevance_array ) ) {
                $relevance_query = sprintf( ' (SUM( %s )) ', implode( ' + ', $relevance_array ) );
            } else {
                $relevance_query = '0';
            }

            $search_query .= sprintf( ' AND ( %s )', implode( ' OR ', $search_array ) );

            /**
             * Exclude certain terms from search
             * @since 1.58
             * @param array
             */
            $exclude_terms = apply_filters( 'aws_terms_exclude_' . $taxonomy, array() );

            if ( $exclude_terms && is_array( $exclude_terms ) && ! empty( $exclude_terms ) ) {
                $excludes = $wpdb->prepare( " AND ( " . $wpdb->terms . ".term_id NOT IN ( %s ) )", implode( ',', $exclude_terms ) );
            }

            $sql = "
			SELECT
				distinct($wpdb->terms.name),
				$wpdb->terms.term_id,
				$wpdb->term_taxonomy.taxonomy,
				$wpdb->term_taxonomy.count,
				{$relevance_query} as relevance
			FROM
				$wpdb->terms
				, $wpdb->term_taxonomy
			WHERE 1 = 1
				{$search_query}
				AND $wpdb->term_taxonomy.taxonomy = '{$taxonomy}'
				AND $wpdb->term_taxonomy.term_id = $wpdb->terms.term_id
				AND count > 0
			    {$excludes}
			    GROUP BY term_id
			    ORDER BY relevance DESC, term_id DESC
			LIMIT 0, {$terms_number}";

            $sql = trim( preg_replace( '/\s+/', ' ', $sql ) );

            $search_results = $wpdb->get_results( $sql );

            if ( ! empty( $search_results ) && !is_wp_error( $search_results ) ) {

                foreach ( $search_results as $result ) {

                    if ( ! $result->count > 0 ) {
                        continue;
                    }

                    if ( function_exists( 'wpml_object_id_filter' )  ) {
                        $term = wpml_object_id_filter( $result->term_id, $result->taxonomy );
                        if ( $term != $result->term_id ) {
                            continue;
                        }
                    } else {
                        $term = get_term( $result->term_id, $result->taxonomy );
                    }

                    if ( $term != null && !is_wp_error( $term ) ) {
                        $term_link = get_term_link( $term );
                    } else {
                        continue;
                    }

                    $new_result = array(
                        'name'     => $result->name,
                        'count'    => $result->count,
                        'link'     => $term_link
                    );

                    $result_array[] = $new_result;

                }

                /**
                 * Filters array of custom taxonomies that must be displayed in search results
                 *
                 * @since 1.63
                 *
                 * @param array $result_array Array of custom taxonomies
                 * @param string $taxonomy Name of taxonomy
                 * @param string $s Search query
                 */
                $result_array = apply_filters( 'aws_search_tax_results', $result_array, $taxonomy, $this->data['s'] );

            }

            return $result_array;

        }

    }


endif;

AWS_Search::factory();

function aws_search( $keyword = '' ) {
    return AWS_Search::factory()->search( $keyword );
}