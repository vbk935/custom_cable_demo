<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'AWS_Order' ) ) :

    /**
     * Class for products sorting
     */
    class AWS_Order {

        /**
         * @var AWS_Order Array of products
         */
        private $products = array();

        /**
         * Constructor
         */
        public function __construct( $products, $query ) {

            $this->products = $products;

            // Filter
            $this->filter_results( $query );

            // Order
            if ( $query->query && ( isset( $query->query['orderby'] ) || isset( $query->query_vars['orderby'] ) ) ) {
                $this->order( $query );
            }

        }

        /*
         * Filter search results
         */
        private function filter_results( $query ) {

            $new_products = array();

            $price_min = false;
            $price_max = false;
            $rating = false;
            $brand = false;

            $attr_filter = array();

            if ( isset( $query->query_vars['meta_query'] ) ) {
                $meta_query = $query->query_vars['meta_query'];

                if ( isset( $meta_query['price_filter'] ) && isset( $meta_query['price_filter']['value'] ) ) {
                    $price_values = $meta_query['price_filter']['value'];
                    if ( isset( $price_values[0] ) && isset( $price_values[1] ) ) {
                        $price_min = $price_values[0];
                        $price_max = $price_values[1];
                    }
                }

            }

            if ( isset( $_GET['rating_filter'] ) && $_GET['rating_filter'] ) {
                $rating = explode( ',', sanitize_text_field( $_GET['rating_filter'] ) );
            }

            if ( isset( $_GET['filtering'] ) && $_GET['filtering'] && isset( $_GET['filter_product_brand'] ) ) {
                $brand = explode( ',', sanitize_text_field( $_GET['filter_product_brand'] ) );
            }

            if ( isset( $query->query_vars['tax_query'] ) ) {
                $tax_query = $query->query_vars['tax_query'];

                if ( $tax_query && is_array( $tax_query ) && ! empty( $tax_query ) ) {
                    foreach( $tax_query as $taxonomy_query ) {
                        if ( is_array( $taxonomy_query ) ) {
                            if ( isset( $taxonomy_query['taxonomy'] ) && strpos( $taxonomy_query['taxonomy'], 'pa_' ) === 0 ) {
                                $tax_name = $taxonomy_query['taxonomy'];
                                $attr_filter[$tax_name] = $taxonomy_query;
                            }
                        }
                    }
                }

            }

            foreach( $this->products as $post_array ) {

                if ( $price_min && $price_max ) {
                    if ( isset( $post_array['f_price'] ) && $post_array['f_price'] ) {
                        if ( $post_array['f_price'] > $price_max || $post_array['f_price'] < $price_min ) {
                            continue;
                        }
                    }
                }

                if ( $rating && is_array( $rating ) ) {
                    if ( isset( $post_array['f_rating'] ) ) {
                        if ( array_search( floor( $post_array['f_rating'] ), $rating ) === false ) {
                            continue;
                        }
                    }
                }

                if ( $brand && is_array( $brand ) ) {

                    $skip = true;
                    $p_brands = get_the_terms( $post_array['id'], 'product_brand' );

                    if ( ! is_wp_error( $p_brands ) && ! empty( $p_brands ) ) {
                        foreach ( $p_brands as $p_brand ) {
                            if ( in_array( $p_brand->term_id,  $brand ) ) {
                                $skip = false;
                                break;
                            }
                        }
                    }

                    if ( $skip ) {
                        continue;
                    }

                }

                if ( $attr_filter && ! empty( $attr_filter ) ) {

                    $product = wc_get_product( $post_array['id'] );
                    $attributes = $product->get_attributes();
                    $skip = true;

                    if ( $attributes && ! empty( $attributes ) ) {
                        foreach( $attributes as $attr_name => $attribute_object ) {
                            if ( $attribute_object ) {
                                if ( ( is_object( $attribute_object ) && method_exists( $attribute_object, 'is_taxonomy' ) && $attribute_object->is_taxonomy() ) ||
                                    ( is_array( $attribute_object ) && isset( $attribute_object['is_taxonomy'] ) && $attribute_object['is_taxonomy'] )
                                ) {
                                    if ( isset( $attr_filter[$attr_name] ) ) {
                                        $product_terms = wp_get_object_terms( $post_array['id'], $attr_name );

                                        if ( ! is_wp_error( $product_terms ) && ! empty( $product_terms ) ) {

                                            $product_terms_array = array();

                                            foreach ( $product_terms as $product_term ) {
                                                $product_terms_array[$product_term->slug] = $product_term->slug;
                                            }

                                            $operator = $attr_filter[$attr_name]['operator'];
                                            $terms = $attr_filter[$attr_name]['terms'];

                                            if ( $terms && is_array( $terms ) && ! empty( $terms ) ) {

                                                if ( $operator === 'AND' ) {

                                                    $has_all = true;

                                                    foreach( $terms as $term ) {
                                                        if ( ! isset( $product_terms_array[$term] ) ) {
                                                            $has_all = false;
                                                            break;
                                                        }
                                                    }

                                                    if ( $has_all ) {
                                                        $skip = false;
                                                    }

                                                }

                                                if ( $operator === 'IN' || $operator === 'OR' ) {

                                                    $has_all = false;

                                                    foreach( $terms as $term ) {
                                                        if ( isset( $product_terms_array[$term] ) ) {
                                                            $has_all = true;
                                                            break;
                                                        }
                                                    }

                                                    if ( $has_all ) {
                                                        $skip = false;
                                                    }

                                                }

                                            }

                                        }

                                    }
                                }
                            }
                        }
                    }

                    if ( $skip ) {
                        continue;
                    }

                }

                $new_products[] = $post_array;

            }

            $this->products = $new_products;

        }

        /*
         * Sort products
         */
        private function order( $query ) {

            if ( isset( $query->query['orderby'] ) ) {

                $order_by = $query->query['orderby'];

            } else {

                $order_by = $query->query_vars['orderby'];

                if ( $order_by === 'meta_value_num' ) {
                    $order_by = 'price';
                }

                if ( isset( $query->query_vars['order'] ) ) {
                    $order_by = $order_by . '-' . strtolower( $query->query_vars['order'] );
                }

            }

            switch( $order_by ) {

                case 'price':
                case 'price-asc':

                    if ( isset( $this->products[0]['f_price'] ) ) {
                        usort( $this->products, array( $this, 'compare_price_asc' ) );
                    }

                    break;

                case 'price-desc':

                    if ( isset( $this->products[0]['f_price'] ) ) {
                        usort( $this->products, array( $this, 'compare_price_desc' ) );
                    }

                    break;

                case 'date':
                case 'date-desc':

                    if ( isset( $this->products[0]['post_data'] ) ) {
                        usort( $this->products, array( $this, 'compare_date' ) );
                    }

                    break;

                case 'date-asc':

                    if ( isset( $this->products[0]['post_data'] ) ) {
                        usort( $this->products, array( $this, 'compare_date_asc' ) );
                    }

                    break;

                case 'rating':

                    if ( isset( $this->products[0]['f_rating'] ) ) {
                        usort( $this->products, array( $this, 'compare_rating' ) );
                    }

                    break;

                case 'popularity':
                case 'popularity-desc':

                    if ( isset( $this->products[0]['f_reviews'] ) ) {
                        usort( $this->products, array( $this, 'compare_reviews' ) );
                    }

                    break;

                case 'popularity-asc':

                    if ( isset( $this->products[0]['f_reviews'] ) ) {
                        usort( $this->products, array( $this, 'compare_reviews_asc' ) );
                    }

                    break;

                case 'title':
                case 'title-desc':

                    if ( isset( $this->products[0]['title'] ) ) {
                        usort( $this->products, array( $this, 'compare_title' ) );
                    }

                    break;

                case 'title-asc':

                    if ( isset( $this->products[0]['title'] ) ) {
                        usort( $this->products, array( $this, 'compare_title' ) );
                        $this->products = array_reverse($this->products);
                    }

                    break;

            }

        }

        /*
         * Compare price values asc
         */
        private function compare_price_asc( $a, $b ) {
            $a = intval( $a['f_price'] * 100 );
            $b = intval( $b['f_price'] * 100 );
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        }

        /*
         * Compare price values desc
         */
        private function compare_price_desc( $a, $b ) {
            $a = intval( $a['f_price'] * 100 );
            $b = intval( $b['f_price'] * 100 );
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? 1 : -1;
        }

        /*
         * Compare date
         */
        private function compare_date( $a, $b ) {
            $a = strtotime( $a['post_data']->post_date );
            $b = strtotime( $b['post_data']->post_date );
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? 1 : -1;
        }

        /*
         * Compare date desc
         */
        private function compare_date_asc( $a, $b ) {
            $a = strtotime( $a['post_data']->post_date );
            $b = strtotime( $b['post_data']->post_date );
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        }

        /*
         * Compare rating
         */
        private function compare_rating( $a, $b ) {
            $a = intval( $a['f_rating'] * 100 );
            $b = intval( $b['f_rating'] * 100 );
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? 1 : -1;
        }

        /*
         * Compare rating
         */
        private function compare_reviews( $a, $b ) {
            $a = intval( $a['f_reviews'] * 100 );
            $b = intval( $b['f_reviews'] * 100 );
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? 1 : -1;
        }

        /*
         * Compare rating asc
         */
        private function compare_reviews_asc( $a, $b ) {
            $a = intval( $a['f_reviews'] * 100 );
            $b = intval( $b['f_reviews'] * 100 );
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        }

        /*
         * Compare title desc
         */
        private function compare_title( $a, $b ) {
            $res = strcasecmp( $a["title"], $b["title"] );
            return $res;
        }

        /*
         * Return array of sorted products
         */
        public function result() {

            return $this->products;

        }

    }

endif;