<?php
define('BEROCKETAAPF', 'BeRocket_AAPF_Widget');
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/**
 * BeRocket_AAPF_Widget - main filter widget. One filter for any needs
 */
class BeRocket_AAPF_Widget extends WP_Widget {

    public static $defaults = array(
        'br_wp_footer'                  => false,
        'widget_type'                   => 'filter',
        'title'                         => '',
        'filter_type'                   => 'attribute',
        'attribute'                     => 'price',
        'custom_taxonomy'               => 'product_cat',
        'type'                          => 'slider',
        'select_first_element_text'     => '',
        'operator'                      => 'OR',
        'order_values_by'               => '',
        'order_values_type'             => '',
        'text_before_price'             => '',
        'text_after_price'              => '',
        'enable_slider_inputs'          => '',
        'parent_product_cat'            => '',
        'depth_count'                   => '0',
        'widget_collapse_enable'        => '0',
        'widget_is_hide'                => '0',
        'show_product_count_per_attr'   => '0',
        'hide_child_attributes'         => '0',
        'hide_collapse_arrow'           => '0',
        'use_value_with_color'          => '0',
        'values_per_row'                => '1',
        'icon_before_title'             => '',
        'icon_after_title'              => '',
        'icon_before_value'             => '',
        'icon_after_value'              => '',
        'price_values'                  => '',
        'description'                   => '',
        'css_class'                     => '',
        'tag_cloud_height'              => '0',
        'tag_cloud_min_font'            => '12',
        'tag_cloud_max_font'            => '14',
        'tag_cloud_tags_count'          => '100',
        'tag_cloud_type'                => 'doe',
        'use_min_price'                 => '0',
        'min_price'                     => '0',
        'use_max_price'                 => '0',
        'max_price'                     => '1',
        'height'                        => 'auto',
        'scroll_theme'                  => 'dark',
        'selected_area_show'            => '0',
        'hide_selected_arrow'           => '0',
        'selected_is_hide'              => '0',
        'slider_default'                => '0',
        'number_style'                  => '0',
        'number_style_thousand_separate'=> '',
        'number_style_decimal_separate' => '.',
        'number_style_decimal_number'   => '2',
        'is_hide_mobile'                => '0',
        'user_can_see'                  => '',
        'cat_propagation'               => '0',
        'product_cat'                   => '',
        'parent_product_cat_current'    => '0',
        'attribute_count'               => '',
        'show_page'                     => array( 'shop', 'product_cat', 'product_tag', 'product_taxonomy' ),
        'cat_value_limit'               => '0',
        'child_parent'                  => '',
        'child_parent_depth'            => '1',
        'child_parent_no_values'        => '',
        'child_parent_previous'         => '',
        'child_parent_no_products'      => '',
        'child_onew_count'              => '1',
        'child_onew_childs'             => array(
            1                               => array('title' => '', 'no_product' => '', 'no_values' => '', 'previous' => ''),
            2                               => array('title' => '', 'no_product' => '', 'no_values' => '', 'previous' => ''),
            3                               => array('title' => '', 'no_product' => '', 'no_values' => '', 'previous' => ''),
            4                               => array('title' => '', 'no_product' => '', 'no_values' => '', 'previous' => ''),
            5                               => array('title' => '', 'no_product' => '', 'no_values' => '', 'previous' => ''),
            6                               => array('title' => '', 'no_product' => '', 'no_values' => '', 'previous' => ''),
            7                               => array('title' => '', 'no_product' => '', 'no_values' => '', 'previous' => ''),
            8                               => array('title' => '', 'no_product' => '', 'no_values' => '', 'previous' => ''),
            9                               => array('title' => '', 'no_product' => '', 'no_values' => '', 'previous' => ''),
            10                              => array('title' => '', 'no_product' => '', 'no_values' => '', 'previous' => ''),
        ),
        'search_box_link_type'          => 'shop_page',
        'search_box_url'                => '',
        'search_box_category'           => '',
        'search_box_count'              => '1',
        'search_box_attributes'             => array(
            1                               => array('type' => 'attribute', 'attribute' => '', 'custom_taxonomy' => '', 'title' => '', 'visual_type' => 'select'),
            2                               => array('type' => 'attribute', 'attribute' => '', 'custom_taxonomy' => '', 'title' => '', 'visual_type' => 'select'),
            3                               => array('type' => 'attribute', 'attribute' => '', 'custom_taxonomy' => '', 'title' => '', 'visual_type' => 'select'),
            4                               => array('type' => 'attribute', 'attribute' => '', 'custom_taxonomy' => '', 'title' => '', 'visual_type' => 'select'),
            5                               => array('type' => 'attribute', 'attribute' => '', 'custom_taxonomy' => '', 'title' => '', 'visual_type' => 'select'),
            6                               => array('type' => 'attribute', 'attribute' => '', 'custom_taxonomy' => '', 'title' => '', 'visual_type' => 'select'),
            7                               => array('type' => 'attribute', 'attribute' => '', 'custom_taxonomy' => '', 'title' => '', 'visual_type' => 'select'),
            8                               => array('type' => 'attribute', 'attribute' => '', 'custom_taxonomy' => '', 'title' => '', 'visual_type' => 'select'),
            9                               => array('type' => 'attribute', 'attribute' => '', 'custom_taxonomy' => '', 'title' => '', 'visual_type' => 'select'),
            10                              => array('type' => 'attribute', 'attribute' => '', 'custom_taxonomy' => '', 'title' => '', 'visual_type' => 'select'),
        ),
        'search_box_style'              => array(
            'position'                      => 'vertical',
            'search_position'               => 'after',
            'search_text'                   => 'Search',
            'background'                    => 'bbbbff',
            'back_opacity'                  => '0',
            'button_background'             => '888800',
            'button_background_over'        => 'aaaa00',
            'text_color'                    => '000000',
            'text_color_over'               => '000000',
        ),
        'ranges'                        => array( 1, 10 ),
        'hide_first_last_ranges'        => '',
        'include_exclude_select'        => '',
        'include_exclude_list'          => array(),
    );

    /**
     * Constructor
     */
    function __construct() {
        global $wp_version;
        /* Widget settings. */
        $widget_ops  = array( 'classname' => 'widget_berocket_aapf', 'description' => __('Add Filters to Products page', 'BeRocket_AJAX_domain') );

        /* Widget control settings. */
        $control_ops = array( 'id_base' => 'berocket_aapf_widget' );

        /* Create the widget. */
        parent::__construct( 'berocket_aapf_widget', __('AJAX Product Filters (Deprecated)', 'BeRocket_AJAX_domain'), $widget_ops, $control_ops );

        add_filter( 'berocket_aapf_listener_wp_query_args', 'br_aapf_args_parser' );
    }
    public static function br_widget_ajax_set() {
        if ( ( is_plugin_active( 'woocommerce/woocommerce.php' ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) && br_get_woocommerce_version() >= 2.1 ) {
            
            add_action( 'wp_ajax_nopriv_berocket_aapf_listener', array( __CLASS__, 'listener' ) );
            add_action( 'wp_ajax_berocket_aapf_listener', array( __CLASS__, 'listener' ) );
            add_action( 'wp_ajax_berocket_aapf_color_listener', array( __CLASS__, 'color_listener' ) );
            add_action( 'wp_ajax_br_include_exclude_list', array( __CLASS__, 'ajax_include_exclude_list' ) );
        }
    }

    /**
     * Show widget to user
     *
     * @param array $args
     * @param array $instance
     */
    function widget( $args, $instance ) {
        if( ! empty($args['widget_id']) ) {
            $this->id = $args['widget_id'];
            $this->number = $args['widget_id'];
        }
        if( empty($this->number) || $this->number == -1 ) {
            global $berocket_aapf_shortcode_id;
            if( empty($berocket_aapf_shortcode_id) ) {
                $berocket_aapf_shortcode_id = 1;
            } else {
                $berocket_aapf_shortcode_id++;
            }
            $this->id = 'berocket_aapf_widget-s'.$berocket_aapf_shortcode_id;
            $args['widget_id'] = $this->id;
            $this->number = 's'.$berocket_aapf_shortcode_id;
        }
        $set_query_var_title = array();
        $set_query_var_main = array();
        $set_query_var_footer = array();
        $filter_type_array = array(
            'attribute' => array(
                'name' => __('Attribute', 'BeRocket_AJAX_domain'),
                'sameas' => 'attribute',
            ),
            'tag' => array(
                'name' => __('Tag', 'BeRocket_AJAX_domain'),
                'sameas' => 'tag',
            ),
            'all_product_cat' => array(
                'name' => __('Product Category', 'BeRocket_AJAX_domain'),
                'sameas' => 'custom_taxonomy',
                'attribute' => 'product_cat',
            ),
        );
        if ( function_exists('wc_get_product_visibility_term_ids') ) {
            $filter_type_array['_rating'] = array(
                'name' => __('Rating', 'BeRocket_AJAX_domain'),
                'sameas' => '_rating',
            );
        }
        $filter_type_array = apply_filters('berocket_filter_filter_type_array', $filter_type_array, $instance);
        if( empty($instance['filter_type']) || ! array_key_exists($instance['filter_type'], $filter_type_array) ) {
            foreach($filter_type_array as $filter_type_key => $filter_type_val) {
                $instance['filter_type'] = $filter_type_key;
                break;
            }
        }
        if( ! empty($instance['filter_type']) && ! empty($filter_type_array[$instance['filter_type']]) && ! empty($filter_type_array[$instance['filter_type']]['sameas']) ) {
            $sameas = $filter_type_array[$instance['filter_type']];
            $instance['filter_type'] = $sameas['sameas'];
            if( ! empty($sameas['attribute']) ) {
                if( $sameas['sameas'] == 'custom_taxonomy' ) {
                    $instance['custom_taxonomy'] = $sameas['attribute'];
                } elseif( $sameas['sameas'] == 'attribute' ) {
                    $instance['attribute'] = $sameas['attribute'];
                }
            }
        }
        //CHECK WIDGET TYPES
        $berocket_admin_filter_types = array(
            'tag' => array('checkbox','radio','select','color','image','tag_cloud'),
            'product_cat' => array('checkbox','radio','select','color','image'),
            'sale' => array('checkbox','radio','select'),
            'custom_taxonomy' => array('checkbox','radio','select','color','image'),
            'attribute' => array('checkbox','radio','select','color','image'),
            'price' => array('slider'),
            'filter_by' => array('checkbox','radio','select','color','image'),
        );
        $berocket_admin_filter_types_by_attr = array(
            'checkbox' => array('value' => 'checkbox', 'text' => 'Checkbox'),
            'radio' => array('value' => 'radio', 'text' => 'Radio'),
            'select' => array('value' => 'select', 'text' => 'Select'),
            'color' => array('value' => 'color', 'text' => 'Color'),
            'image' => array('value' => 'image', 'text' => 'Image'),
            'slider' => array('value' => 'slider', 'text' => 'Slider'),
            'tag_cloud' => array('value' => 'tag_cloud', 'text' => 'Tag cloud'),
        );
        list($berocket_admin_filter_types, $berocket_admin_filter_types_by_attr) = apply_filters( 'berocket_admin_filter_types_by_attr', array($berocket_admin_filter_types, $berocket_admin_filter_types_by_attr) );
        $select_options_variants = array();
        if ( $instance['filter_type'] == 'tag' ) {
            $select_options_variants = $berocket_admin_filter_types['tag'];
        } else if ( $instance['filter_type'] == 'product_cat' || ( $instance['filter_type'] == 'custom_taxonomy' && ( $instance['custom_taxonomy'] == 'product_tag' || $instance['custom_taxonomy'] == 'product_cat' ) ) ) {
            $select_options_variants = $berocket_admin_filter_types['product_cat'];
        } else if ( $instance['filter_type'] == '_sale' || $instance['filter_type'] == '_stock_status' || $instance['filter_type'] == '_rating' ) {
            $select_options_variants = $berocket_admin_filter_types['sale'];
        } else if ( $instance['filter_type'] == 'custom_taxonomy' ) {
            $select_options_variants = $berocket_admin_filter_types['custom_taxonomy'];
        } else if ( $instance['filter_type'] == 'attribute' ) {
            if ( $instance['attribute'] == 'price' ) {
                $select_options_variants = $berocket_admin_filter_types['price'];
            } else {
                $select_options_variants = $berocket_admin_filter_types['attribute'];
            }
        } else if ( $instance['filter_type'] == 'filter_by' ) {
            $select_options_variants = $berocket_admin_filter_types['filter_by'];
        }
        $selected = false;
        $first = false;
        foreach($select_options_variants as $select_options_variant) {
            if( ! empty($berocket_admin_filter_types_by_attr[$select_options_variant]) ) {
                if( $instance['type'] == $berocket_admin_filter_types_by_attr[$select_options_variant]['value'] ) {
                    $selected = true;
                    break;
                }
                if( $first === false ) {
                    $first = $berocket_admin_filter_types_by_attr[$select_options_variant]['value'];
                }
            }
        }
        if( ! $selected ) {
            $instance['type'] = $first;
        }
        $widget_type_array = apply_filters( 'berocket_widget_widget_type_array', array(
            'filter' => __('Filter', 'BeRocket_AJAX_domain'),
            'update_button' => __('Update Products button', 'BeRocket_AJAX_domain'),
            'reset_button' => __('Reset Products button', 'BeRocket_AJAX_domain'),
            'selected_area' => __('Selected Filters area', 'BeRocket_AJAX_domain'),
        ) );
        if( ! array_key_exists($instance['widget_type'], $widget_type_array) ) {
            foreach($widget_type_array as $widget_type_id => $widget_type_name) {
                $instance['widget_type'] = $widget_type_id;
                break;
            }
        }
        $instance['title'] = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance );
        $br_options = apply_filters( 'berocket_aapf_listener_br_options', BeRocket_AAPF::get_aapf_option() );
        $default_language = apply_filters( 'wpml_default_language', NULL );

        global $wp_query, $wp_the_query, $wp, $sitepress, $br_wc_query;
        if( ! isset( BeRocket_AAPF::$error_log['6_widgets'] ) )
        {
            BeRocket_AAPF::$error_log['6_widgets'] = array();
        } 
        $widget_error_log             = array();

        $instance = array_merge( self::$defaults, $instance );
        $instance = apply_filters('aapf_widget_instance', $instance);
        $args = apply_filters('aapf_widget_args', $args);
        if( ( $instance['user_can_see'] == 'logged' && ! is_user_logged_in() ) || ( $instance['user_can_see'] == 'not_logged' && is_user_logged_in() ) ) {
            return false;
        }

        if( BeRocket_AAPF::$debug_mode ) {
            $widget_error_log['wp_query'] = $wp_query;
            $widget_error_log['args']     = $args;
            $widget_error_log['instance'] = $instance;
        }

        $BeRocket_AAPF = BeRocket_AAPF::getInstance();
        if( ! empty($br_options['user_func']) && is_array( $br_options['user_func'] ) ) {
            $user_func = array_merge( $BeRocket_AAPF->defaults['user_func'], $br_options['user_func'] );
        } else {
            $user_func = $BeRocket_AAPF->defaults['user_func'];
        }

        if( ! empty($br_options['filters_turn_off']) ) return false;

        if( ! empty($instance['child_parent']) && in_array($instance['child_parent'], array('child', 'parent')) ) {
            $br_options['show_all_values'] = true;
        }

        if ( ! empty($instance['show_page']) ) {
            $pageid = get_the_ID();
            $pageid = apply_filters( 'wpml_object_id', $pageid, 'page', true, $default_language );
            $pagelimit = FALSE;

            foreach ( $instance['show_page'] as $page => $is_show ) {
                if( $is_show ) {
                    $pagelimit = TRUE;
                    break;
                }
            }
            if ( $pagelimit &&
                ( ( ! is_product_category() && ! is_shop() && ! is_product_tag() && ! is_product_taxonomy() && ! is_product() && ! in_array( $pageid, $instance['show_page'] ) ) || 
                ( is_shop() && ! in_array( 'shop', $instance['show_page'] ) ) || 
                ( is_product_category() && ! in_array( 'product_cat', $instance['show_page'] ) ) || 
                ( is_product() && ! in_array( 'single_product', $instance['show_page'] ) ) || 
                ( is_product_taxonomy() && ! in_array( 'product_taxonomy', $instance['show_page'] ) && ! is_product_category() ) || 
                ( is_product_tag() && ! in_array( 'product_tag', $instance['show_page'] ) ) )
                ) {
                    
                if( BeRocket_AAPF::$debug_mode ) {
                    $widget_error_log['return'] = 'hidden';
                    BeRocket_AAPF::$error_log['6_widgets'][] = $widget_error_log;
                }
                return false;
            }
        }

        if ( isset ( $br_wc_query ) ) {
            if( ! is_a($br_wc_query, 'WP_Query') ) {
                $br_wc_query = new WP_Query( $br_wc_query );
            }
            if( class_exists('WC_Query') &&  method_exists('WC_Query', 'product_query') && method_exists('WC_Query', 'get_main_query') ) {
                $wc_query = wc()->query->get_main_query();
            }
            $old_query     = $wp_query;
            $old_the_query = $wp_the_query;
            $wp_query      = $br_wc_query;
            $wp_the_query  = $br_wc_query;
            if( class_exists('WC_Query') &&  method_exists('WC_Query', 'product_query') && method_exists('WC_Query', 'get_main_query') ) {
                wc()->query->product_query($wp_query);
            }
        }

        $wp_query_product_cat     = '-1';
        $wp_check_product_cat     = '1q1main_shop1q1';
        if ( ! empty($wp_query->query['product_cat']) ) {
            $wp_query_product_cat = explode( "/", $wp_query->query['product_cat'] );
            $wp_query_product_cat = $wp_query_product_cat[ count( $wp_query_product_cat ) - 1 ];
            $wp_query_product_cat = urldecode($wp_query_product_cat);
            $wp_check_product_cat = $wp_query_product_cat;
        }

        if ( empty($br_options['products_holder_id']) ) $br_options['products_holder_id'] = 'ul.products';

        if ( empty($instance['br_wp_footer']) ) {
            global $br_widget_ids;
            if ( ! isset( $br_widget_ids ) ) {
                $br_widget_ids = array();
            }
            $br_widget_ids[] = array('instance' => $instance, 'args' => $args);
        }

        extract( $args );
        extract( $instance );

        $text_before_price = apply_filters('aapf_widget_text_before_price', ( isset($text_before_price) ? $text_before_price : '' ) );
        $text_after_price = apply_filters('aapf_widget_text_after_price', ( isset($text_after_price) ? $text_after_price : '' ) );
        if( ! empty($text_before_price) || ! empty($text_after_price) ) {
            $cur_symbol = get_woocommerce_currency_symbol();
            $cur_slug = get_woocommerce_currency();
            if( !empty($text_before_price) ) {
                $text_before_price = str_replace(array('%cur_symbol%', '%cur_slug%'), array($cur_symbol, $cur_slug), $text_before_price);
            }
            if( !empty($text_after_price) ) {
                $text_after_price = str_replace(array('%cur_symbol%', '%cur_slug%'), array($cur_symbol, $cur_slug), $text_after_price);
            }
        }

        if ( empty($order_values_by) ) {
            $order_values_by = 'Default';
        }

        if ( ! empty($filter_type) && ( $filter_type == 'product_cat' || $filter_type == '_stock_status' || $filter_type == '_sale' || $filter_type == '_rating' ) ) {
            $attribute   = $filter_type;
            $filter_type = 'attribute';
        }

        $product_cat = @ json_decode( $product_cat );

        if ( $product_cat && is_product() && ! empty($instance['show_page']) && is_array($instance['show_page']) && in_array( 'single_product', $instance['show_page'] ) ) {
            $hide_widget = true;
            global $post;
            $product_cat_id = array();
            $terms = get_the_terms( $post->ID, 'product_cat' );
            if( ! empty($terms) && is_array($terms) ) {
                foreach ($terms as $term) {
                    $cur_cat_id = apply_filters( 'wpml_object_id', $term->term_id, 'product_cat', true, $default_language );
                    $product_cat_id[] = $cur_cat_id;
                    if ( ! empty($cat_propagation) ) {
                        $cur_cat_ancestors = get_ancestors( $term->term_id, 'product_cat' );
                        foreach ( $cur_cat_ancestors as $cat_ancestor ) {
                            $cur_cat_ancestor = apply_filters( 'wpml_object_id', $cat_ancestor, 'product_cat', true, $default_language );
                            $product_cat_id[] = $cur_cat_ancestor;
                        }
                    }
                    foreach ( $product_cat as $cat ) {
                        $cur_cat = get_term_by( 'slug', $cat, 'product_cat' );
                        $cur_cat_id = apply_filters( 'wpml_object_id', $cur_cat->term_id, 'product_cat', true, $default_language );
                        if ( ! empty($cur_cat_id) && is_array($product_cat_id) && in_array( $cur_cat_id, $product_cat_id ) ) {
                            $hide_widget = false;
                            break;
                        }
                    }
                }
            }

            if ( $hide_widget ) {
                $widget_error_log['return'] = 'hide_widget';
                BeRocket_AAPF::$error_log['6_widgets'][] = $widget_error_log;
                $this->restore_wp_the_query($br_wc_query, $wp_the_query, $wp_query, $wc_query, $old_the_query, $old_query);
                return true;
            }
        }

        if ( $product_cat && is_product_category() ) {
            $hide_widget = true;

            $cur_cat = get_term_by( 'slug', $wp_query_product_cat, 'product_cat' );
            $cur_cat_id = apply_filters( 'wpml_object_id', $cur_cat->term_id, 'product_cat', true, $default_language );

            if ( ! empty($cat_propagation) ) {
                $cur_cat_ancestors = get_ancestors( $cur_cat_id, 'product_cat' );
                $cur_cat_ancestors[] = $cur_cat_id;
                foreach ( $cur_cat_ancestors as &$cat_ancestor ) {
                    $cat_ancestor = apply_filters( 'wpml_object_id', $cat_ancestor, 'product_cat', true, $default_language );
                }
                foreach ( $product_cat as $cat ) {
                    $cat = get_term_by( 'slug', $cat, 'product_cat' );
                    $cat_id = apply_filters( 'wpml_object_id', $cat->term_id, 'product_cat', true, $default_language );

                    if ( ! empty($cat_id) && is_array($cur_cat_ancestors) && in_array( $cat_id, $cur_cat_ancestors ) ) {
                        $hide_widget = false;
                        break;
                    }
                }
            } else {
                foreach ( $product_cat as $cat ) {
                    $cat = get_term_by( 'slug', $cat, 'product_cat' );
                    $cat_id = apply_filters( 'wpml_object_id', $cat->term_id, 'product_cat', true, $default_language );
                    if ( $cat_id == $cur_cat_id ) {
                        $hide_widget = false;
                        break;
                    }
                }
            }

            if ( $hide_widget ) {
                $widget_error_log['return'] = 'hide_widget';
                BeRocket_AAPF::$error_log['6_widgets'][] = $widget_error_log;
                $this->restore_wp_the_query($br_wc_query, $wp_the_query, $wp_query, $wc_query, $old_the_query, $old_query);
                return true;
            }
        }

        if( empty($br_options['ajax_site']) ) {
            do_action('br_footer_script');
        } else {
            echo '<script>jQuery(document).ready(function() {if(typeof(berocket_filters_first_load) == "function") {berocket_filters_first_load();}});</script>';
        }
        if( apply_filters( 'berocket_aapf_widget_display_custom_filter', false, berocket_isset($widget_type), $instance, $args, $this ) ) return '';

        if ( ! empty($widget_type) && $widget_type == 'update_button' ) {
            $set_query_var_title = array(
                'title'          => apply_filters( 'berocket_aapf_widget_title', $title ),
                'uo'             => br_aapf_converter_styles( (empty($br_options['styles']) ? NULL : $br_options['styles']) ),
                'is_hide_mobile' => ( empty($is_hide_mobile) ? '' : $is_hide_mobile )
            );
            set_query_var( 'berocket_query_var_title', $set_query_var_title );
            echo $before_widget;
            br_get_template_part( 'widget_update_button' );
            echo $after_widget;
            $widget_error_log['return'] = 'update_button';
            BeRocket_AAPF::$error_log['6_widgets'][] = $widget_error_log;
            $this->restore_wp_the_query($br_wc_query, $wp_the_query, $wp_query, $wc_query, $old_the_query, $old_query);
            return '';
        }

        if ( ! empty($widget_type) && $widget_type == 'reset_button' ) {
            $set_query_var_title = array(
                'title'          => apply_filters( 'berocket_aapf_widget_title', $title ),
                'uo'             => br_aapf_converter_styles( (empty($br_options['styles']) ? NULL : $br_options['styles']) ),
                'is_hide_mobile' => ( empty($is_hide_mobile) ? '' : $is_hide_mobile )
            );
            set_query_var( 'berocket_query_var_title', $set_query_var_title );
            echo $before_widget;
            br_get_template_part( 'widget_reset_button' );
            echo $after_widget;
            $widget_error_log['return'] = 'reset_button';
            BeRocket_AAPF::$error_log['6_widgets'][] = $widget_error_log;
            $this->restore_wp_the_query($br_wc_query, $wp_the_query, $wp_query, $wc_query, $old_the_query, $old_query);
            return '';
        }

        if ( ! empty($widget_type) && $widget_type == 'selected_area' ) {
            if ( empty($scroll_theme) ) {
                $scroll_theme = 'dark';
            }
            $set_query_var_title = array(
                'title'          => apply_filters( 'berocket_aapf_widget_title', $title ),
                'uo'             => br_aapf_converter_styles( (empty($br_options['styles']) ? NULL : $br_options['styles']) ),
                'is_hide_mobile' => ( empty($is_hide_mobile) ? '' : $is_hide_mobile ),
                'selected_area_show' => $selected_area_show,
                'hide_selected_arrow' => $hide_selected_arrow,
                'selected_is_hide' => $selected_is_hide,
            );
            set_query_var( 'berocket_query_var_title', $set_query_var_title );
            echo $before_widget;
            br_get_template_part( 'widget_selected_area' );
            echo $after_widget;

            $widget_error_log['return'] = 'selected_area';
            BeRocket_AAPF::$error_log['6_widgets'][] = $widget_error_log;
            $this->restore_wp_the_query($br_wc_query, $wp_the_query, $wp_query, $wc_query, $old_the_query, $old_query);
            return '';
        }

        $woocommerce_hide_out_of_stock_items = BeRocket_AAPF_Widget::woocommerce_hide_out_of_stock_items();
        if( $woocommerce_hide_out_of_stock_items == 'yes' && $filter_type == 'attribute' && $attribute == '_stock_status' ) {
            $widget_error_log['return'] = 'stock_status';
            BeRocket_AAPF::$error_log['6_widgets'][] = $widget_error_log;
            $this->restore_wp_the_query($br_wc_query, $wp_the_query, $wp_query, $wc_query, $old_the_query, $old_query);
            return true;
        }

        if( $type == "slider" ) {
            $operator = 'OR';
            $order_values_by = '';
            $order_values_type = 'asc';
        }

        $terms = $sort_terms = $price_range = array();
        $instance['wp_check_product_cat'] = $wp_check_product_cat;
        $instance['wp_query_product_cat'] = $wp_query_product_cat;
        list($terms_error_return, $terms_ready, $terms, $type) = apply_filters( 'berocket_widget_attribute_type_terms', array(false, false, $terms, $type), $attribute, $filter_type, $instance );
        if( $terms_ready ) {
            if( $terms_error_return === FALSE ) {
                $set_query_var_title['terms'] = apply_filters( 'berocket_aapf_widget_terms', $terms );
                if( BeRocket_AAPF::$debug_mode ) {
                    $widget_error_log['terms'] = $terms;
                }
            } else {
                $widget_error_log['terms'] = $terms;
                $widget_error_log['return'] = $terms_error_return;
                BeRocket_AAPF::$error_log['6_widgets'][] = $widget_error_log;
                $this->restore_wp_the_query($br_wc_query, $wp_the_query, $wp_query, $wc_query, $old_the_query, $old_query);
                return false;
            }
        } else {
            if ( $filter_type == 'attribute' ) {
                if ( $attribute == 'price' ) {
                    if ( ! empty($price_values) ) {
                        $price_range = explode( ",", $price_values );
                    } elseif( $use_min_price && $use_max_price ) {
                        $price_range = array($min_price, $max_price);
                    } else {
                        $price_range = br_get_cache( 'price_range', $wp_check_product_cat );
                        if ( $price_range === false ) {
                            $price_range = BeRocket_AAPF_Widget::get_price_range( $wp_query_product_cat, ( isset($cat_value_limit) ? $cat_value_limit : null ) );
                            br_set_cache( 'price_range', $price_range, $wp_check_product_cat, BeRocket_AJAX_cache_expire );
                        }
                        if ( ! $price_range or count( $price_range ) < 2 ) {
                            $widget_error_log['price_range'] = $price_range;
                            $widget_error_log['return'] = 'price_range < 2';
                            BeRocket_AAPF::$error_log['6_widgets'][] = $widget_error_log;
                            $this->restore_wp_the_query($br_wc_query, $wp_the_query, $wp_query, $wc_query, $old_the_query, $old_query);
                            return false;
                        }
                    }
                    if( BeRocket_AAPF::$debug_mode ) {
                        $widget_error_log['price_range'] = $price_range;
                    }
                } elseif ( $attribute == '_rating' ) {
                    $terms = array();
                    $term = get_term_by('slug', 'rated-1', 'product_visibility');
                    $term->name = ( $type == 'select' ? __('1 star', 'BeRocket_AJAX_domain') : __('<i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>', 'BeRocket_AJAX_domain') );
                    array_push($terms, $term);
                    $term = get_term_by('slug', 'rated-2', 'product_visibility');
                    $term->name = ( $type == 'select' ? __('2 stars', 'BeRocket_AJAX_domain') : __('<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>', 'BeRocket_AJAX_domain') );
                    array_push($terms, $term);
                    $term = get_term_by('slug', 'rated-3', 'product_visibility');
                    $term->name = ( $type == 'select' ? __('3 stars', 'BeRocket_AJAX_domain') : __('<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>', 'BeRocket_AJAX_domain') );
                    array_push($terms, $term);
                    $term = get_term_by('slug', 'rated-4', 'product_visibility');
                    $term->name = ( $type == 'select' ? __('4 stars', 'BeRocket_AJAX_domain') : __('<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i>', 'BeRocket_AJAX_domain') );
                    array_push($terms, $term);
                    $term = get_term_by('slug', 'rated-5', 'product_visibility');
                    $term->name = ( $type == 'select' ? __('5 stars', 'BeRocket_AJAX_domain') : __('<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>', 'BeRocket_AJAX_domain') );
                    array_push($terms, $term);
                    $terms = BeRocket_AAPF_Widget::get_attribute_values( $attribute, 'id', ( empty($br_options['show_all_values']) ), ( ! empty($br_options['recount_products']) ), $terms, ( isset($cat_value_limit) ? $cat_value_limit : null ), $operator );
                    foreach($terms as &$term) {
                        $term->taxonomy = '_rating';
                    }
                    if( ! empty( $order_values_type ) && $order_values_type == 'desc' ) {
                        $terms = array_reverse($terms);
                    }
                    $set_query_var_title['terms'] = apply_filters( 'berocket_aapf_widget_terms', $terms );
                    if( BeRocket_AAPF::$debug_mode ) {
                        $widget_error_log['terms'] = $terms;
                    }
                } elseif ( $attribute == 'product_cat' ) {
                    if( $parent_product_cat_current ) {
                        $cate = get_queried_object();
                        if( isset($cate->term_id) ) {
                            $cateID = $cate->term_id;
                            $title = str_replace( '%product_cat%', $cate->name, $title );
                        } else {
                            $cateID = 0;
                            $title = str_replace( '%product_cat%', '', $title );
                        }
                        $parent_product_cat_cache = $cateID;
                        $parent_product_cat = $cateID;
                    } else {
                        $parent_product_cat_cache = $parent_product_cat;
                    }
                    $terms = br_get_cache ( $attribute . $order_values_by, $wp_check_product_cat . $parent_product_cat_cache . $depth_count );
                    if ( br_is_filtered() || $terms === false ) {
                        $terms_unsort = br_get_cat_hierarchy(array(), $parent_product_cat, $depth_count );
                        self::sort_terms( $terms_unsort, array(
                            "order_values_by" => $order_values_by,
                            "attribute"       => $attribute,
                            "order_values_type"=> (empty($order_values_type) || $order_values_type == 'asc' ? SORT_ASC : SORT_DESC)
                        ) );

                        $terms_unsort = self::set_terms_on_same_level( $terms_unsort, array(), ($type != 'checkbox' && $type != 'radio') );
                        $terms = BeRocket_AAPF_Widget::get_attribute_values( $attribute, 'id', ( empty($br_options['show_all_values']) ), ( ! empty($br_options['recount_products']) ), $terms_unsort, ( isset($cat_value_limit) ? $cat_value_limit : null ), $operator );
                        if ( isset( $depth_count ) ) {
                            $old_terms = $terms;
                            $terms = array();

                            foreach( $terms_unsort as $term_unsort ) {
                                if ( ! empty( $old_terms[ $term_unsort->term_id ] ) ) {
                                    $terms[ $term_unsort->term_id ] = $old_terms[ $term_unsort->term_id ];
                                }
                            }
                        }

                        if ( ! br_is_filtered() ) {
                            br_set_cache( $attribute.$order_values_by, $terms, $wp_check_product_cat.$parent_product_cat_cache.$depth_count, BeRocket_AJAX_cache_expire );
                        }
                    }

                    if( BeRocket_AAPF::$debug_mode ) {
                        $widget_error_log['terms'] = $terms;
                    }
                    $terms = apply_filters('berocket_aapf_widget_include_exclude_items', $terms, $instance);

                    if ( empty($terms) || ! is_array($terms) || count( $terms ) < 1 ) {
                        $widget_error_log['terms'] = $terms;
                        $widget_error_log['return'] = 'terms < 1';
                        BeRocket_AAPF::$error_log['6_widgets'][] = $widget_error_log;
                        $this->restore_wp_the_query($br_wc_query, $wp_the_query, $wp_query, $wc_query, $old_the_query, $old_query);
                        return false;
                    }
                    $set_query_var_title['terms'] = apply_filters( 'berocket_aapf_widget_terms', $terms );
                    unset( $terms, $terms_unsort );
                } else {
                    $sort_array  = array();
                    $wc_order_by = wc_attribute_orderby( $attribute );

                    $terms = br_get_cache ( $attribute, $wp_check_product_cat );
                    if( br_is_filtered() || $terms === false || ( ! empty($child_parent) && ( $child_parent == 'parent' || $child_parent == 'child' ) ) ) {
                        $current_terms = self::get_terms_child_parent ( ( empty($child_parent) ? '' : $child_parent ), $attribute, FALSE, ( isset($child_parent_depth) ? $child_parent_depth : null ) );
                        $terms = BeRocket_AAPF_Widget::get_attribute_values( $attribute, 'id', ( empty($br_options['show_all_values']) ), ( ! empty($br_options['recount_products']) ), $current_terms, ( isset($cat_value_limit) ? $cat_value_limit : null ), $operator );
                        if( ! br_is_filtered() && ( empty($child_parent) || ( $child_parent != 'parent' && $child_parent != 'child' ) ) ) {
                            br_set_cache ( $attribute, $terms, $wp_check_product_cat, BeRocket_AJAX_cache_expire );
                        }
                    }
                    $terms = apply_filters('berocket_aapf_widget_include_exclude_items', $terms, $instance);

                    if ( empty($terms) || ! is_array($terms) || count( $terms ) < 1 ) {
                        $widget_error_log['terms'] = $terms;
                        $widget_error_log['return'] = 'terms < 1';
                        BeRocket_AAPF::$error_log['6_widgets'][] = $widget_error_log;
                        $this->restore_wp_the_query($br_wc_query, $wp_the_query, $wp_query, $wc_query, $old_the_query, $old_query);
                        return false;
                    }

                    if ( $wc_order_by == 'menu_order' and $order_values_by == 'Default' ) {
                        foreach ( $terms as $term ) {
                            if( isset($term->term_id) ) {
                                $sort_array[] = get_term_meta( $term->term_id, ( br_woocommerce_version_check('3.6') ? 'order' : 'order_' . $attribute ), true );
                            }
                        }
                        array_multisort( $sort_array, (empty($order_values_type) || $order_values_type == 'asc' ? SORT_ASC : SORT_DESC), $terms );
                    } else {
                        self::sort_terms( $terms, array(
                            "wc_order_by"     => $wc_order_by,
                            "order_values_by" => $order_values_by,
                            "filter_type"     => $filter_type,
                            "order_values_type"=> (empty($order_values_type) || $order_values_type == 'asc' ? SORT_ASC : SORT_DESC)
                        ) );
                    }

                    if( BeRocket_AAPF::$debug_mode ) {
                        $widget_error_log['terms'] = $terms;
                    }
                    $set_query_var_title['terms'] = apply_filters( 'berocket_aapf_widget_terms', $terms );
                }

            } elseif ( $filter_type == 'tag' ) {
                $attribute = 'product_tag';
                $terms = br_get_cache ( $attribute.$order_values_by, $wp_check_product_cat );
                if( br_is_filtered() || $terms === false ) {
                    $terms = BeRocket_AAPF_Widget::get_attribute_values( $attribute, 'id', ( empty($br_options['show_all_values']) ), ( ! empty($br_options['recount_products']) ), FALSE, ( isset($cat_value_limit) ? $cat_value_limit : null ), $operator );

                    if ( $order_values_by != 'Default' ) {
                        self::sort_terms( $terms, array(
                            "order_values_by" => $order_values_by,
                            "attribute"       => $attribute,
                            "order_values_type"=> (empty($order_values_type) || $order_values_type == 'asc' ? SORT_ASC : SORT_DESC)
                        ) );
                    }
                    if( ! br_is_filtered() ) {
                        br_set_cache ( $attribute.$order_values_by, $terms, $wp_check_product_cat, BeRocket_AJAX_cache_expire );
                    }
                }

                if( BeRocket_AAPF::$debug_mode ) {
                    $widget_error_log['terms'] = $terms;
                }

                $terms = apply_filters('berocket_aapf_widget_include_exclude_items', $terms, $instance);

                $set_query_var_title['terms'] = apply_filters( 'berocket_aapf_widget_terms', $terms );

                if ( empty($terms) || ! is_array($terms) || count( $terms ) < 1 ) {
                    $widget_error_log['terms'] = $terms;
                    $widget_error_log['return'] = 'terms < 1';
                    BeRocket_AAPF::$error_log['6_widgets'][] = $widget_error_log;
                    $this->restore_wp_the_query($br_wc_query, $wp_the_query, $wp_query, $wc_query, $old_the_query, $old_query);
                    return false;
                }
            } elseif ( $filter_type == 'custom_taxonomy' ) {
                $terms = br_get_cache ( $custom_taxonomy.$order_values_by, $filter_type.$wp_check_product_cat );
                if( br_is_filtered() || $terms === false || ( ! empty($child_parent) && ( $child_parent == 'parent' || $child_parent == 'child' ) ) ) {
                    if ( $custom_taxonomy == 'product_cat' ) {
                        if( empty($child_parent) || ( $child_parent != 'parent' && $child_parent != 'child' ) ) {
                            $terms = br_get_cat_hierarchy(array());
                            $terms = self::set_terms_on_same_level( $terms, array(), ($type != 'checkbox' && $type != 'radio') );
                        } else {
                            $terms = self::get_terms_child_parent ( ( empty($child_parent) ? '' : $child_parent ), $custom_taxonomy, FALSE, ( isset($child_parent_depth) ? $child_parent_depth : null ) );
                        }
                    } else {
                        $terms = self::get_terms_child_parent ( $child_parent, $custom_taxonomy, FALSE, ( isset($child_parent_depth) ? $child_parent_depth : 0 ) );
                        $terms = BeRocket_AAPF_Widget::get_attribute_values( $custom_taxonomy, 'id', ( empty($br_options['show_all_values']) ), ( ! empty($br_options['recount_products']) ), $terms, ( isset($cat_value_limit) ? $cat_value_limit : null ), $operator );
                    }
                    if( $custom_taxonomy == 'product_cat' ) {
                        $terms = BeRocket_AAPF_Widget::get_attribute_values( $custom_taxonomy, 'id', ( empty($br_options['show_all_values']) ), ( ! empty($br_options['recount_products']) ), $terms, ( isset($cat_value_limit) ? $cat_value_limit : null ), $operator );
                    }
                    if ( $order_values_by != 'Default' || in_array($custom_taxonomy, array('berocket_brand', 'product_cat')) ) {
                        self::sort_terms( $terms, array(
                            "order_values_by" => $order_values_by,
                            "attribute"       => $custom_taxonomy,
                            "order_values_type"=> (empty($order_values_type) || $order_values_type == 'asc' ? SORT_ASC : SORT_DESC)
                        ) );
                    }
                    if ( ! br_is_filtered() && $child_parent != 'parent' && $child_parent != 'child' ) {
                        br_set_cache( $custom_taxonomy . $order_values_by, $terms, $filter_type . $wp_check_product_cat, BeRocket_AJAX_cache_expire );
                    }
                }

                if( BeRocket_AAPF::$debug_mode ) {
                    $widget_error_log['terms'] = $terms;
                }
                $terms = apply_filters('berocket_aapf_widget_include_exclude_items', $terms, $instance);

                $set_query_var_title['terms'] = apply_filters( 'berocket_aapf_widget_terms', $terms );
                $sort_array = self::sort_child_parent_hierarchy($terms);
                @ array_multisort( $sort_array, (empty($order_values_type) || $order_values_type == 'asc' ? SORT_ASC : SORT_DESC), SORT_NUMERIC, $terms );

                if ( ! isset($terms) || ! is_array($terms) || count( $terms ) < 1 ) {
                    $widget_error_log['terms'] = ( isset($terms) ? $terms : '' );
                    $widget_error_log['return'] = 'terms < 1';
                    BeRocket_AAPF::$error_log['6_widgets'][] = $widget_error_log;
                    $this->restore_wp_the_query($br_wc_query, $wp_the_query, $wp_query, $wc_query, $old_the_query, $old_query);
                    return false;
                }
            }
        }

        $style = $class = '';
        $style = br_get_value_from_array($args, 'widget_inline_style');
        if( ! empty($height) and $height != 'auto' ){
            $style .= "max-height: {$height}px; overflow: hidden;";
            $class = "berocket_aapf_widget_height_control";
        }

        if( !$scroll_theme ) $scroll_theme = 'dark';
        if( $filter_type == 'custom_taxonomy' )
            $attribute = $custom_taxonomy;
        if( ! isset($attribute_count) || $attribute_count == '' ) {
            $attribute_count = br_get_value_from_array($br_options,'attribute_count');
        }

        if( $type == 'select' || $type == 'slider' ) {
            $values_per_row = 1;
        }

        $set_query_var_title['operator']                    = $operator;
        $set_query_var_title['attribute']                   = $attribute;
        $set_query_var_title['type']                        = $type;
        $set_query_var_title['title']                       = apply_filters( 'berocket_aapf_widget_title', $title );
        $set_query_var_title['class']                       = apply_filters( 'berocket_aapf_widget_class', $class );
        $set_query_var_title['css_class']                   = apply_filters( 'berocket_aapf_widget_css_class', (isset($css_class) ? $css_class : '') );
        $set_query_var_title['style']                       = apply_filters( 'berocket_aapf_widget_style', $style );
        $set_query_var_title['scroll_theme']                = $scroll_theme;
        $set_query_var_title['x']                           = time();
        $set_query_var_title['filter_type']                 = $filter_type;
        $set_query_var_title['uo']                          = br_aapf_converter_styles( (empty($br_options['styles']) ? '' : $br_options['styles']) );
        $set_query_var_title['notuo']                       = (empty($br_options['styles']) ? '' : $br_options['styles']);
        $set_query_var_title['widget_is_hide']              = (! empty($widget_collapse_enable) && ! empty($widget_is_hide));
        $set_query_var_title['widget_collapse_disable']     = empty($widget_collapse_enable);
        $set_query_var_title['is_hide_mobile']              = ! empty($is_hide_mobile);
        $set_query_var_title['show_product_count_per_attr'] = ! empty($show_product_count_per_attr);
        $set_query_var_title['hide_child_attributes']       = ! empty($hide_child_attributes);
        $set_query_var_title['cat_value_limit']             = ( isset($cat_value_limit) ? $cat_value_limit : null );
        $set_query_var_title['select_first_element_text']   = ( empty($select_first_element_text) ? __('Any', 'BeRocket_AJAX_domain') : $select_first_element_text );
        $set_query_var_title['icon_before_title']           = (isset($icon_before_title) ? $icon_before_title : null);
        $set_query_var_title['icon_after_title']            = (isset($icon_after_title) ? $icon_after_title : null);
        $set_query_var_title['hide_o_value']                = ! empty($br_options['hide_value']['o']);
        $set_query_var_title['hide_sel_value']              = ! empty($br_options['hide_value']['sel']);
        $set_query_var_title['hide_empty_value']            = ! empty($br_options['hide_value']['empty']);
        $set_query_var_title['hide_button_value']           = ! empty($br_options['hide_value']['button']);
        $set_query_var_title['attribute_count_show_hide']   = berocket_isset($attribute_count_show_hide);
        $set_query_var_title['attribute_count']             = $attribute_count;
        $set_query_var_title['description']                 = (isset($description) ? $description : null);
        $set_query_var_title['hide_collapse_arrow']         = (empty($widget_collapse_enable) || ! empty($hide_collapse_arrow));
        $set_query_var_title['values_per_row']              = (isset($values_per_row) ? $values_per_row : null);
        $set_query_var_title['child_parent']                = (isset($child_parent) ? $child_parent : null);
        $set_query_var_title['child_parent_depth']          = (isset($child_parent_depth) ? $child_parent_depth : null);
        $set_query_var_title['product_count_style']         = (isset($br_options['styles_input']['product_count']) ? $br_options['styles_input']['product_count'] : '').'pcs '.(isset($br_options['styles_input']['product_count_position']) ? $br_options['styles_input']['product_count_position'] : null).'pcs';
        $set_query_var_title['styles_input']                = (isset($br_options['styles_input']) ? $br_options['styles_input'] : array());
        $set_query_var_title['child_parent_previous']       = (isset($child_parent_previous) ? $child_parent_previous : null);
        $set_query_var_title['child_parent_no_values']      = (isset($child_parent_no_values) ? $child_parent_no_values : null);
        $set_query_var_title['child_parent_no_products']    = (isset($child_parent_no_products) ? $child_parent_no_products : null);
        $set_query_var_title['before_title']                = (isset($before_title) ? $before_title : null);
        $set_query_var_title['after_title']                 = (isset($after_title) ? $after_title : null);
        $set_query_var_title['widget_id']                   = ( $this->id ? $this->id : $widget_id );
        $set_query_var_title['widget_id_number']            = ( $this->number ? $this->number : $widget_id_number );
        $set_query_var_title['slug_urls']                   = ! empty($br_options['slug_urls']);
        $set_query_var_title = apply_filters('berocket_aapf_query_var_title_filter', $set_query_var_title, $instance, $br_options);
        set_query_var( 'berocket_query_var_title', $set_query_var_title );

        // widget title and start tag ( <ul> ) can be found in templates/widget_start.php
        echo $before_widget;
        do_action('berocket_aapf_widget_before_start');
        br_get_template_part('widget_start');
        do_action('berocket_aapf_widget_after_start');

        $slider_with_string = false;
        $stringed_is_numeric = true;
        $slider_step = 1;

        if ( $type == 'slider' ) {
            $min = $max   = false;
            $main_class   = 'slider';
            $slider_class = 'berocket_filter_slider';

            if ( $attribute == 'price' ){
                wp_localize_script(
                    'berocket_aapf_widget-script',
                    'br_price_text',
                    array(
                        'before'  => (isset($text_before_price) ? $text_before_price : ''),
                        'after'   => (isset($text_after_price) ? $text_after_price : ''),
                    )
                );
                if ( ! empty($price_values) ) {
                    $all_terms_name = $price_range;
                    $all_terms_slug = $price_range;
                    $stringed_is_numeric = true;
                    $min = 0;
                    $max = count( $all_terms_name ) - 1;
                    $slider_with_string = true;
                } else {
                    if( $price_range ) {
                        foreach ( $price_range as $price ) {
                            if ( $min === false or $min > (int) $price ) {
                                $min = $price;
                            }
                            if ( $max === false or $max < (int) $price ) {
                                $max = $price;
                            }
                        }
                    }
                    if( $use_min_price ) {
                        $min = $min_price;
                    }
                    if ( $use_max_price ) {
                        $max = $max_price;
                    }
                }
                $id = 'br_price';
                $slider_class .= ' berocket_filter_price_slider';
                $main_class .= ' price';

                $min = floor( $min );
                $max = ceil( $max );
            } else {
                if ( $slider_default ) {
                    $slider_with_string = true;
                }

                if ( isset($terms) && is_array($terms) && count( $terms ) < 1 ) {
                    $widget_error_log['terms'] = $terms;
                    $widget_error_log['return'] = 'terms < 1';
                    BeRocket_AAPF::$error_log['6_widgets'][] = $widget_error_log;
                    $this->restore_wp_the_query($br_wc_query, $wp_the_query, $wp_query, $wc_query, $old_the_query, $old_query);
                    return false;
                }

                if( ! empty($terms) && is_array($terms) ) {
                    $all_terms_name = array();
                    $all_terms_slug = array();
                    foreach ( $terms as $term ) {
                        if ( ! is_numeric( $term->name ) ) {
                            $slider_with_string = true;
                            if ( ! is_numeric( substr( $term->name, 0, 1 ) ) ) {
                                $stringed_is_numeric = false;
                            }
                        }
                        if ( $min === false or strcmp( $min, $term->name ) > 0 ) {
                            $min = $term->name;
                        }
                        if ( $max === false or strcmp( $max, $term->name ) < 0 ) {
                            $max = $term->name;
                        }
                        array_push( $all_terms_name, urldecode($term->slug) );
                        array_push( $all_terms_slug, $term->name );
                    }

                    if ( ! $slider_with_string ) {
                        $min = false;
                        $max = false;
                        foreach ( $terms as $term ) {
                            if ( (float) $term->name != (int) (float) $term->name ) {
                                if ( round( (float) $term->name, 1 ) == (float) $term->name && $slider_step != 100 ) {
                                    $slider_step = 10;
                                } else {
                                    $slider_step = 100;
                                }
                            }
                            if ( $min === false or $min > (float) $term->name ) {
                                $min = round( (float) $term->name, 2 );
                                if ( $min > (float) $term->name ) {
                                    $min -= 0.01;
                                }
                            }
                            if ( $max === false or $max < (float) $term->name ) {
                                $max = round( (float) $term->name, 2 );
                                if ( $max < (float) $term->name ) {
                                    $max += 0.01;
                                }
                            }
                        }
                    }
                }
                $widget_error_log['slider_with_string'] = (isset($slider_with_string) ? $slider_with_string : null);
                $widget_error_log['stringed_is_numeric'] = (isset($stringed_is_numeric) ? $stringed_is_numeric : null);

                $id = $term->taxonomy;
                if ( empty($slider_with_string) ) {
                    $min *= $slider_step;
                    $max *= $slider_step;
                    $all_terms_name = null;
                } else {
                    if ( count( $all_terms_name ) == 1 ) {
                        array_push( $all_terms_name, $all_terms_name[0] );
                        array_push( $all_terms_slug, $all_terms_slug[0] );
                    }
                    $min = 0;
                    $max = count( $all_terms_name ) - 1;
                    if( ! empty($stringed_is_numeric) ) {
                        array_multisort( $all_terms_slug, SORT_NUMERIC, $all_terms_slug, $all_terms_name );
                    } elseif($filter_type == 'custom_taxonomy') {
                        array_multisort( $all_terms_name, $all_terms_name, $all_terms_slug );
                    }
                }
                $widget_error_log['all_terms_slug'] = (isset($all_terms_slug) ? $all_terms_slug : null);
                $widget_error_log['all_terms_name'] = (isset($all_terms_name) ? $all_terms_name : null);
            }

            $slider_value1 = $min;
            $slider_value2 = $max;

            if ( $attribute == 'price' and ! empty($_POST['price']) ) {
                if ( ! empty($price_values) ) {
                    $slider_value1 = array_search( $_POST['price'][0], $all_terms_name );
                    $slider_value2 = array_search( $_POST['price'][1], $all_terms_name );
                } else {
                    $slider_value1 = apply_filters('berocket_price_filter_widget_min_amount', apply_filters('berocket_price_slider_widget_min_amount', apply_filters('woocommerce_price_filter_widget_min_amount', $_POST['price'][0])), $_POST['price'][0]);
                    $slider_value2 = apply_filters('berocket_price_filter_widget_max_amount', apply_filters('berocket_price_slider_widget_max_amount', apply_filters('woocommerce_price_filter_widget_max_amount', $_POST['price'][1])), $_POST['price'][1]);
                }
            }
            if ( $attribute != 'price' and ! empty($_POST['limits']) and is_array($_POST['limits']) ) {
                foreach ( $_POST['limits'] as $p_limit ) {
                    if ( $p_limit[0] == $attribute ) {
                        $slider_value1 = $p_limit[1];
                        $slider_value2 = $p_limit[2];
                        if ( ! $slider_with_string ) {
                            $slider_value1 *= $slider_step;
                            $slider_value2 *= $slider_step;
                        } else {
                            $p_limit[1] = urldecode( $p_limit[1] );
                            $p_limit[2] = urldecode( $p_limit[2] );
                            $slider_value1 = array_search( $p_limit[1], $all_terms_name );
                            $slider_value2 = array_search( $p_limit[2], $all_terms_name );
                            if( $slider_value1 === FALSE ) {
                                $slider_value1 = 0;
                            }
                            if( $slider_value2 === FALSE ) {
                                $slider_value2 = count($all_terms_name) - 1;
                            }
                        }
                    }
                }
                if( BeRocket_AAPF::$debug_mode ) {
                    $widget_error_log['value_1'] = $slider_value1;
                    $widget_error_log['value_2'] = $slider_value2;
                    $widget_error_log['step'] = $slider_step;
                }
            }

            $wpml_id = preg_replace( '#^pa_#', '', $id );
            $wpml_id = 'pa_'.berocket_wpml_attribute_translate($wpml_id);
            $set_query_var_title['slider_value1'] = $slider_value1;
            $set_query_var_title['slider_value2'] = $slider_value2;
            $set_query_var_title['filter_slider_id'] = $wpml_id;
            $set_query_var_title['main_class'] = $main_class;
            $set_query_var_title['slider_class'] = $slider_class;
            $set_query_var_title['min'] = $min;
            $set_query_var_title['max'] = $max;
            $set_query_var_title['step'] = $slider_step;
            $set_query_var_title['slider_with_string'] = $slider_with_string;
            $set_query_var_title['all_terms_name'] = ( empty($all_terms_name) ? null : $all_terms_name );
            $set_query_var_title['all_terms_slug'] = ( empty($all_terms_slug) ? null : $all_terms_slug );
            $set_query_var_title['text_before_price'] = (isset($text_before_price) ? $text_before_price : null);
            $set_query_var_title['text_after_price'] = (isset($text_after_price) ? $text_after_price : null);
            $set_query_var_title['enable_slider_inputs'] = (isset($enable_slider_inputs) ? $enable_slider_inputs : null);
            if( ! empty($number_style) ) {
                $set_query_var_title['number_style'] = array(
                    ( empty($number_style_thousand_separate) ? '' : $number_style_thousand_separate ), 
                    ( empty($number_style_decimal_separate) ? '' : $number_style_decimal_separate ), 
                    ( empty($number_style_decimal_number) ? '' : $number_style_decimal_number )
                );
            } else {
                $set_query_var_title['number_style'] = '';
            }
        }
        $set_query_var_title['first_page_jump'] = ( empty($first_page_jump) ? '' : $first_page_jump );
        $set_query_var_title['icon_before_value'] = (isset($icon_before_value) ? $icon_before_value : null);
        $set_query_var_title['icon_after_value'] = (isset($icon_after_value) ? $icon_after_value : null);

        if ( $type == 'tag_cloud' ) {
            $tag_script_var = array(
                'height'        => $tag_cloud_height,
                'min_font_size' => $tag_cloud_min_font,
                'max_font_size' => $tag_cloud_max_font,
                'tags_count'    => $tag_cloud_tags_count,
                'tags_type'    => $tag_cloud_type
            );
            $set_query_var_title['tag_script_var'] = $tag_script_var;
        } elseif ( $type == 'color' || $type == 'image' ) {
            $set_query_var_title['use_value_with_color'] = (isset($use_value_with_color) ? $use_value_with_color : null);
            $set_query_var_title['disable_multiple'] = (isset($disable_multiple) ? $disable_multiple : null);
            $set_query_var_title['color_image_block_size'] = berocket_isset($color_image_block_size, false, 'h2em w2em');
            $set_query_var_title['color_image_checked'] = berocket_isset($color_image_checked, false, 'brchecked_default');
            $set_query_var_title['color_image_checked_custom_css'] = berocket_isset($color_image_checked_custom_css);
            $set_query_var_title['color_image_block_size_height'] = berocket_isset($color_image_block_size_height);
            $set_query_var_title['color_image_block_size_width'] = berocket_isset($color_image_block_size_width);
        }
        if( $type == 'select' ) {
            if( ! empty($br_options['use_select2']) ) {
                if( ! empty($br_options['fixed_select2']) ) {
                    wp_enqueue_style( 'br_select2' );
                } else {
                    wp_enqueue_style( 'select2' );
                }
                wp_enqueue_script( 'select2' );
            }
            $set_query_var_title['select_multiple'] = ! empty($select_multiple);
        }
        set_query_var( 'berocket_query_var_title', apply_filters('berocket_query_var_title_before_widget', $set_query_var_title, $type, $instance));
        br_get_template_part( apply_filters('berocket_widget_load_template_name', $type, $instance, (empty($terms) ? '' : $terms)) );

        do_action('berocket_aapf_widget_before_end');
        br_get_template_part('widget_end');
        do_action('berocket_aapf_widget_after_end');
        echo $after_widget;
        if( BeRocket_AAPF::$debug_mode ) {
            $widget_error_log['terms'] = (isset($terms) ? $terms : null);
        }
        $widget_error_log['return'] = 'OK';
        BeRocket_AAPF::$error_log['6_widgets'][] = $widget_error_log;
        $this->restore_wp_the_query($br_wc_query, $wp_the_query, $wp_query, $wc_query, $old_the_query, $old_query);
    }

    public static function restore_wp_the_query(&$br_wc_query, &$wp_the_query, &$wp_query, &$wc_query, &$old_the_query, &$old_query) {
        if ( isset ( $br_wc_query ) ) {
            if ( isset ( $old_query ) ) {
                $wp_the_query = $old_the_query;
                $wp_query = $old_query;
            }
            if( ! empty($wc_query) && is_a($wc_query, 'WP_Query') && class_exists('WC_Query') &&  method_exists('WC_Query', 'product_query') && method_exists('WC_Query', 'get_main_query') ) {
                wc()->query->product_query($wc_query);
            }
            wc()->query->remove_ordering_args();
        }
    }

    public static function woocommerce_hide_out_of_stock_items(){
        $hide = get_option( 'woocommerce_hide_out_of_stock_items', null );

        if ( is_array( $hide ) ) {
            $hide = array_map( 'stripslashes', $hide );
        } elseif ( ! is_null( $hide ) ) {
            $hide = stripslashes( $hide );
        }

        return apply_filters( 'berocket_aapf_hide_out_of_stock_items', $hide );
    }

    public static function price_range_count($term, $from, $to) {
        if( class_exists('WP_Meta_Query') && class_exists('WP_Tax_Query') ) {
            global $wpdb, $wp_query;
            $old_join_posts = '';
            $has_new_function = method_exists('WC_Query', 'get_main_query') && method_exists('WC_Query', 'get_main_meta_query') && method_exists('WC_Query', 'get_main_tax_query');
            if( $has_new_function ) {
                $WC_Query_get_main_query = WC_Query::get_main_query();
                $has_new_function = ! empty($WC_Query_get_main_query);
            }
            if( ! $has_new_function ) {
                $old_query_vars = self::old_wc_compatible($wp_query);
                $old_meta_query = (empty( $old_query_vars[ 'meta_query' ] ) || ! is_array($old_query_vars[ 'meta_query' ]) ? array() : $old_query_vars['meta_query']);
                $old_tax_query = (empty($old_query_vars['tax_query']) || ! is_array($old_query_vars[ 'tax_query' ]) ? array() : $old_query_vars['tax_query']);
            } else {
                $old_query_vars = self::old_wc_compatible($wp_query, true);
            }
            if( ! empty( $old_query_vars['posts__in'] ) ) {
                $old_join_posts = " AND {$wpdb->posts}.ID IN (".implode(',', $old_query_vars['posts__in']).") ";
            }
            if( $has_new_function ) {
                $tax_query  = WC_Query::get_main_tax_query();
            } else {
                $tax_query = $old_tax_query;
            }
            if( $has_new_function ) {
                $meta_query  = WC_Query::get_main_meta_query();
            } else {
                $meta_query = $old_meta_query;
            }
            foreach( $meta_query as $key => $val ) {
                if( is_array($val) ) {
                    if ( ! empty( $val['price_filter'] ) || ! empty( $val['rating_filter'] ) ) {
                        unset( $meta_query[ $key ] );
                    }
                    if( isset( $val['relation']) ) {
                        unset($val['relation']);
                        foreach( $val as $key2 => $val2 ) {
                            if ( isset( $val2['key'] ) && $val2['key'] == apply_filters('berocket_price_filter_meta_key', '_price', 'widget_1162') ) {
                                if ( isset( $meta_query[ $key ][ $key2 ] ) ) unset( $meta_query[ $key ][ $key2 ] );
                            }
                        }
                        if( count($meta_query[ $key ]) <= 1 ) {
                            unset( $meta_query[ $key ] );
                        }
                    } else {
                        if ( isset( $val['key'] ) && $val['key'] == apply_filters('berocket_price_filter_meta_key', '_price', 'widget_1170') ) {
                            if ( isset( $meta_query[ $key ] ) ) unset( $meta_query[ $key ] );
                        }
                    }
                }
            }
            $queried_object = $wp_query->get_queried_object_id();
            if( ! empty($queried_object) ) {
                $query_object = $wp_query->get_queried_object();
                if( ! empty($query_object->taxonomy) && ! empty($query_object->slug) ) {
                    $tax_query[ $query_object->taxonomy ] = array(
                        'taxonomy' => $query_object->taxonomy,
                        'terms'    => array( $query_object->slug ),
                        'field'    => 'slug',
                    );
                }
            }
            $meta_query      = new WP_Meta_Query( $meta_query );
            $tax_query       = new WP_Tax_Query( $tax_query );
            $meta_query_sql  = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
            $tax_query_sql   = $tax_query->get_sql( $wpdb->posts, 'ID' );

            // Generate query
            $query           = array();
            $query['select'] = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) as range_count";
            $query['from']   = "FROM {$wpdb->posts}";
            $query['join']   = "
                INNER JOIN {$wpdb->postmeta} AS price_term ON {$wpdb->posts}.ID = price_term.post_id
                " . $tax_query_sql['join'] . $meta_query_sql['join'];
            $query['where']   = "
                WHERE {$wpdb->posts}.post_type IN ( 'product' )
                AND " . br_select_post_status() . "
                " . $tax_query_sql['where'] . $meta_query_sql['where'] . "
                AND price_term.meta_key = '".apply_filters('berocket_price_filter_meta_key', '_price', 'widget_1203')."' 
                AND price_term.meta_value >= {$from} AND price_term.meta_value <= {$to}
            ";
            if( defined( 'WCML_VERSION' ) && defined('ICL_LANGUAGE_CODE') ) {
                $query['join'] = $query['join']." INNER JOIN {$wpdb->prefix}icl_translations as wpml_lang ON ( {$wpdb->posts}.ID = wpml_lang.element_id )";
                $query['where'] = $query['where']." AND wpml_lang.language_code = '".ICL_LANGUAGE_CODE."' AND wpml_lang.element_type = 'post_product'";
            }
            br_where_search( $query );
            $query['where'] .= $old_join_posts;
            $query             = apply_filters( 'woocommerce_get_filtered_ranges_product_counts_query', $query );
            $query             = implode( ' ', $query );

            $results           = $wpdb->get_results( $query );
            if( isset( $results[0]->range_count ) ) {
                $term->count = $results[0]->range_count;
            }
        }
        return $term;
    }

    public static function get_price_range( $wp_query_product_cat, $product_cat = null ) {
        global $wpdb;

        $BeRocket_AAPF = BeRocket_AAPF::getInstance();
        $br_options = $BeRocket_AAPF->get_option();

        if( br_woocommerce_version_check('3.6') ) {
            $query[ 'select' ] = "SELECT MIN(cast(FLOOR(wc_product_meta_lookup.min_price) as decimal)) as min_price,
                              MAX(cast(CEIL(wc_product_meta_lookup.max_price) as decimal)) as max_price ";
            $query[ 'from' ]   = "FROM {$wpdb->posts}";
            $query[ 'join' ]   = " INNER JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id ";
        } else {
            $query[ 'select' ] = "SELECT MIN(cast(FLOOR(wp_price_check.meta_value) as decimal)) as min_price,
                              MAX(cast(CEIL(wp_price_check.meta_value) as decimal)) as max_price ";
            $query[ 'from' ]   = "FROM {$wpdb->postmeta} as wp_price_check";
            $query[ 'join' ]   = "INNER JOIN {$wpdb->posts} ON ({$wpdb->posts}.ID = wp_price_check.post_id)";
        }
        if ( ! empty( $br_options[ 'show_all_values' ] ) ) {
            $query[ 'where' ] = " WHERE {$wpdb->posts}.post_type = 'product' AND " . br_select_post_status();
        } else {
            $query = br_filters_query( $query, 'price', $product_cat );
        }

        if( !br_woocommerce_version_check('3.6') ) {
            if ( $query[ 'where' ] ) {
                $query[ 'where' ] .= " AND ";
            } else {
                $query[ 'where' ] = " WHERE ";
            }
            $query[ 'where' ] .= "wp_price_check.meta_key = '".apply_filters('berocket_price_filter_meta_key', '_price', 'widget_1243')."' AND wp_price_check.meta_value > ''";
        }

        $query_string = implode( ' ', $query );

        $query_string = $wpdb->get_row( $query_string );

        $price_range = false;
        if ( isset( $query_string->min_price ) && isset( $query_string->max_price ) && $query_string->min_price != $query_string->max_price ) {
            $price_range = array(
                apply_filters('berocket_price_filter_widget_min_amount', apply_filters('berocket_price_slider_widget_min_amount', apply_filters( 'woocommerce_price_filter_widget_min_amount', $query_string->min_price )), $query_string->min_price),
                apply_filters('berocket_price_filter_widget_max_amount', apply_filters('berocket_price_slider_widget_max_amount', apply_filters( 'woocommerce_price_filter_widget_max_amount', $query_string->max_price )), $query_string->max_price)
            );
        }

        return apply_filters( 'berocket_aapf_get_price_range', $price_range );
    }

    public static function get_attribute_values( $taxonomy = '', $order_by = 'id', $hide_empty = false, $count_filtering = true, $input_terms = FALSE, $product_cat = FALSE, $operator = 'OR' ) {
        $br_options = apply_filters( 'berocket_aapf_listener_br_options', BeRocket_AAPF::get_aapf_option() );
if( BeRocket_AAPF::$debug_mode ) {
    $time = microtime(true);
    if( ! isset( BeRocket_AAPF::$error_log['6_term_recount'] ) )
    {
        BeRocket_AAPF::$error_log['6_term_recount'] = array();
    } 
    $term_recount_log = array();
    $term_recount_log['start_time'] = $time;
}

        if ( ! $taxonomy || $taxonomy == 'price' ) return array();
        if( $taxonomy == '_rating' ) $taxonomy = 'product_visibility';

        global $wp_query, $wpdb, $br_wc_query, $br_aapf_wc_footer_widget;

        $post__in = ( isset($wp_query->query_vars['post__in']) ? $wp_query->query_vars['post__in'] : array() );
        if (
            ! empty( $br_wc_query ) and
            ! empty( $br_wc_query->query ) and
            isset( $br_wc_query->query['post__in'] ) and
            is_array( $br_wc_query->query['post__in'] )
        ) {
            $post__in = array_merge( $post__in, $br_wc_query->query[ 'post__in' ] );
        }

        if( empty($post__in) || ! is_array($post__in) || count($post__in) == 0 ) {
            $post__in = false;
        }
        $post__not_in = ( isset($wp_query->query_vars['post__not_in']) ? $wp_query->query_vars['post__not_in'] : array() );
        if( empty($post__not_in) || ! is_array($post__not_in) || count($post__not_in) == 0 ) {
            $post__not_in = false;
        }

        $old_join_posts = '';
        $has_new_function = method_exists('WC_Query', 'get_main_query') && method_exists('WC_Query', 'get_main_meta_query') && method_exists('WC_Query', 'get_main_tax_query');
        if( $has_new_function ) {
            $WC_Query_get_main_query = WC_Query::get_main_query();
            $has_new_function = ! empty($WC_Query_get_main_query);
        }
        if( ! $has_new_function ) {
            $old_query_vars = self::old_wc_compatible($wp_query);
            $old_meta_query = (empty( $old_query_vars[ 'meta_query' ] ) || ! is_array($old_query_vars[ 'meta_query' ]) ? array() : $old_query_vars['meta_query']);
            $old_tax_query = (empty($old_query_vars['tax_query']) || ! is_array($old_query_vars[ 'tax_query' ]) ? array() : $old_query_vars['tax_query']);
        } else {
            $old_query_vars = self::old_wc_compatible($wp_query, true);
        }

        if( ! empty( $old_query_vars['posts__in'] ) ) {
            $old_join_posts = " AND {$wpdb->posts}.ID IN (".implode(',', $old_query_vars['posts__in']).") ";
        }

        if( $taxonomy == '_stock_status' && ( $count_filtering || $hide_empty ) ) {
            if( class_exists('WP_Meta_Query') && class_exists('WP_Tax_Query') ) {
                if( $has_new_function ) {
                    $meta_query  = WC_Query::get_main_meta_query();
                } else {
                    $meta_query = $old_meta_query;
                }
                if ( 'OR' == $operator ) {
                    foreach( $meta_query as $key => $val ) {
                        if ( isset( $val['key'] ) && $val['key'] == $taxonomy ) {
                            if ( isset( $meta_query[ $key ] ) ) unset( $meta_query[ $key ] );
                        }
                    }
                }
                if( $count_filtering ) {
                    if( $has_new_function ) {
                        $tax_query  = WC_Query::get_main_tax_query();
                    } else {
                        $tax_query = $old_tax_query;
                    }
                    $args      = $wp_query->query_vars;
                } else {
                    $args      = $wp_query->query_vars;
                    $tax_query = array();
                    if ( ! empty( $args['product_cat'] ) ) {
                        $tax_query[ 'product_cat' ] = array(
                            'taxonomy' => 'product_cat',
                            'terms'    => array( $args['product_cat'] ),
                            'field'    => 'slug',
                        );
                    }
                    foreach( $meta_query as $key => $val ) {
                        if( is_array($val) ) {
                            if ( ! empty( $val['price_filter'] ) || ! empty( $val['rating_filter'] ) ) {
                                unset( $meta_query[ $key ] );
                            }
                            if( isset( $val['relation']) ) {
                                unset($val['relation']);
                                foreach( $val as $key2 => $val2 ) {
                                    if ( isset( $val2['key'] ) && $val2['key'] == apply_filters('berocket_price_filter_meta_key', '_price', 'widget_1352') ) {
                                        if ( isset( $meta_query[ $key ][ $key2 ] ) ) unset( $meta_query[ $key ][ $key2 ] );
                                    }
                                }
                                if( count($meta_query[ $key ]) <= 1 ) {
                                    unset( $meta_query[ $key ] );
                                }
                            } else {
                                if ( isset( $val['key'] ) && $val['key'] == apply_filters('berocket_price_filter_meta_key', '_price', 'widget_1360') ) {
                                    if ( isset( $meta_query[ $key ] ) ) unset( $meta_query[ $key ] );
                                }
                            }
                        }
                    }
                }
                $queried_object = $wp_query->get_queried_object_id();
                if( ! empty($queried_object) && empty($br_aapf_wc_footer_widget) ) {
                    $query_object = $wp_query->get_queried_object();
                    if( ! empty($query_object->taxonomy) && ! empty($query_object->slug) ) {
                        $tax_query[ $query_object->taxonomy ] = array(
                            'taxonomy' => $query_object->taxonomy,
                            'terms'    => array( $query_object->slug ),
                            'field'    => 'slug',
                        );
                    }
                }
                $meta_query      = new WP_Meta_Query( $meta_query );
                $tax_query       = new WP_Tax_Query( $tax_query );
                $meta_query_sql  = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
                $tax_query_sql   = $tax_query->get_sql( $wpdb->posts, 'ID' );
                $term_ids = wp_list_pluck( $input_terms, 'term_id' );

                // Generate query
                $query           = array();
                $query['select'] = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) as term_count, postmeta_stock.meta_value as term_count_id";
                $query['from']   = "FROM {$wpdb->posts}";
                $query['join']   = "
                    INNER JOIN {$wpdb->term_relationships} AS term_relationships ON {$wpdb->posts}.ID = term_relationships.object_id
                    INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy USING( term_taxonomy_id )
                    INNER JOIN {$wpdb->postmeta} AS postmeta_stock ON ( {$wpdb->posts}.ID = postmeta_stock.post_id )
                    INNER JOIN {$wpdb->terms} AS terms USING( term_id )
                    " . $tax_query_sql['join'] . $meta_query_sql['join'];
                $query['where']   = "
                    WHERE {$wpdb->posts}.post_type IN ( 'product' )
                    AND " . br_select_post_status() . "
                    " . $tax_query_sql['where'] . $meta_query_sql['where'] . "
                    AND postmeta_stock.meta_key = '_stock_status'
                ";
                if( defined( 'WCML_VERSION' ) && defined('ICL_LANGUAGE_CODE') ) {
                    $query['join'] = $query['join']." INNER JOIN {$wpdb->prefix}icl_translations as wpml_lang ON ( {$wpdb->posts}.ID = wpml_lang.element_id )";
                    $query['where'] = $query['where']." AND wpml_lang.language_code = '".ICL_LANGUAGE_CODE."' AND wpml_lang.element_type = 'post_product'";
                }
                br_where_search( $query );
                if( ! empty($post__in) ) {
                    $query['where'] .= " AND {$wpdb->posts}.ID IN (\"" . implode('","', $post__in) . "\")";
                }
                if( ! empty($post__not_in) ) {
                    $query['where'] .= " AND {$wpdb->posts}.ID NOT IN (\"" . implode('","', $post__not_in) . "\")";
                }
                if( $count_filtering ) {
                    $query['where'] .= $old_join_posts;
                }
                $query['group_by'] = "GROUP BY postmeta_stock.meta_value";
                $query             = apply_filters( 'woocommerce_get_filtered_term_product_counts_query', $query );
                $query             = implode( ' ', $query );
if( BeRocket_AAPF::$debug_mode ) {
    $term_recount_log['query_stock'] = $query;
}
                $results           = $wpdb->get_results( $query );
                $results           = wp_list_pluck( $results, 'term_count', 'term_count_id' );
                foreach($input_terms as &$res_count) {
                    if( ! empty($results[$res_count->slug] ) ) {
                        $res_count->count = $results[$res_count->slug];
                    } else {
                        $res_count->count = 0;
                    }
                }
            }
if( BeRocket_AAPF::$debug_mode ) {
    $term_recount_log['time_after_recount'] = microtime(true) - $time;
    $term_recount_log['after_recount'] = $input_terms;
    BeRocket_AAPF::$error_log['6_term_recount'][] = $term_recount_log;
}
            return $input_terms;
        }

        if( $taxonomy == '_sale' && ( $count_filtering || $hide_empty ) ) {
            if( class_exists('WP_Meta_Query') && class_exists('WP_Tax_Query') ) {
                if( $has_new_function ) {
                    $meta_query  = WC_Query::get_main_meta_query();
                } else {
                    $meta_query = $old_meta_query;
                }
                if ( 'OR' == $operator ) {
                    foreach( $meta_query as $key => $val ) {
                        if ( isset( $val['key'] ) && $val['key'] == $taxonomy ) {
                            if ( isset( $meta_query[ $key ] ) ) unset( $meta_query[ $key ] );
                        }
                    }
                }
                if( $count_filtering ) {
                    if( $has_new_function ) {
                        $tax_query  = WC_Query::get_main_tax_query();
                    } else {
                        $tax_query = $old_tax_query;
                    }
                    $args      = $wp_query->query_vars;
                } else {
                    $args      = $wp_query->query_vars;
                    $tax_query = array();
                    if ( ! empty( $args['product_cat'] ) ) {
                        $tax_query[ 'product_cat' ] = array(
                            'taxonomy' => 'product_cat',
                            'terms'    => array( $args['product_cat'] ),
                            'field'    => 'slug',
                        );
                    }
                    foreach( $meta_query as $key => $val ) {
                        if( is_array($val) ) {
                            if ( ! empty( $val['price_filter'] ) || ! empty( $val['rating_filter'] ) ) {
                                unset( $meta_query[ $key ] );
                            }
                            if( isset( $val['relation']) ) {
                                unset($val['relation']);
                                foreach( $val as $key2 => $val2 ) {
                                    if ( isset( $val2['key'] ) && $val2['key'] == apply_filters('berocket_price_filter_meta_key', '_price', 'widget_1477') ) {
                                        if ( isset( $meta_query[ $key ][ $key2 ] ) ) unset( $meta_query[ $key ][ $key2 ] );
                                    }
                                }
                                if( count($meta_query[ $key ]) <= 1 ) {
                                    unset( $meta_query[ $key ] );
                                }
                            } else {
                                if ( isset( $val['key'] ) && $val['key'] == apply_filters('berocket_price_filter_meta_key', '_price', 'widget_1485') ) {
                                    if ( isset( $meta_query[ $key ] ) ) unset( $meta_query[ $key ] );
                                }
                            }
                        }
                    }
                }
                $queried_object = $wp_query->get_queried_object_id();
                if( ! empty($queried_object) && empty($br_aapf_wc_footer_widget) ) {
                    $query_object = $wp_query->get_queried_object();
                    if( ! empty($query_object->taxonomy) && ! empty($query_object->slug) ) {
                        $tax_query[ $query_object->taxonomy ] = array(
                            'taxonomy' => $query_object->taxonomy,
                            'terms'    => array( $query_object->slug ),
                            'field'    => 'slug',
                        );
                    }
                }
                $meta_query      = new WP_Meta_Query( $meta_query );
                $tax_query       = new WP_Tax_Query( $tax_query );
                $meta_query_sql  = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
                $tax_query_sql   = $tax_query->get_sql( $wpdb->posts, 'ID' );
                $term_ids = wp_list_pluck( $input_terms, 'term_id' );

                // Generate query
                $query           = array();
                $query['select'] = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) as term_count";
                $query['from']   = "FROM {$wpdb->posts}";
                $query['join']   = "
                    INNER JOIN {$wpdb->term_relationships} AS term_relationships ON {$wpdb->posts}.ID = term_relationships.object_id
                    INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy USING( term_taxonomy_id )
                    INNER JOIN {$wpdb->terms} AS terms USING( term_id )
                    " . $tax_query_sql['join'] . $meta_query_sql['join'];
                $query['where']   = "
                    WHERE {$wpdb->posts}.post_type IN ( 'product' )
                    AND " . br_select_post_status() . "
                    " . $tax_query_sql['where'] . $meta_query_sql['where'] . "
                ";
                if( defined( 'WCML_VERSION' ) && defined('ICL_LANGUAGE_CODE') ) {
                    $query['join'] = $query['join']." INNER JOIN {$wpdb->prefix}icl_translations as wpml_lang ON ( {$wpdb->posts}.ID = wpml_lang.element_id )";
                    $query['where'] = $query['where']." AND wpml_lang.language_code = '".ICL_LANGUAGE_CODE."' AND wpml_lang.element_type = 'post_product'";
                }
                br_where_search( $query );
                global $berocket_post_before_add_terms;
                if( isset($berocket_post_before_add_terms) ) {
                    if( ! empty($post__in) ) {
                        $post__in = $berocket_post_before_add_terms;
                    }
                    if( $count_filtering ) {
                        if( empty($berocket_post_before_add_terms) ) {
                            $old_join_posts = '';
                        } else {
                            $old_join_posts = " AND {$wpdb->posts}.ID IN (".implode(',', $berocket_post_before_add_terms).") ";
                        }
                    }
                }
                if( $count_filtering ) {
                    $query['where'] .= $old_join_posts;
                }
                $query             = apply_filters( 'woocommerce_get_filtered_term_product_counts_query', $query );
                $query             = apply_filters( 'berocket_posts_clauses_recount', $query );
                $on_sale = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
                $not_on_sale = $on_sale;
                $query_sale = $query;
                $query_not_sale = $query;
                if( ! empty($post__in) ) {
                    $on_sale = array_intersect($on_sale, $post__in);
                    $query_not_sale['where'] .= " AND {$wpdb->posts}.ID IN (\"" . implode('","', $post__in) . "\")";
                }
                if( ! empty($post__not_in) ) {
                    $query_sale['where'] .= " AND {$wpdb->posts}.ID NOT IN (\"" . implode('","', $post__not_in) . "\")";
                    $not_on_sale = array_merge($not_on_sale, $post__not_in);
                }
                $query_sale['where'] .= " AND {$wpdb->posts}.ID IN (".implode(',', $on_sale).") ";
                $query_not_sale['where'] .= " AND {$wpdb->posts}.ID NOT IN (".implode(',', $not_on_sale).") ";
                $query_sale        = implode( ' ', $query_sale );
                $results           = $wpdb->get_results( $query_sale );
                foreach($input_terms as &$res_count) {
                    if( $res_count->term_id == 1 ) {
                        if( ! empty($results[0]->term_count ) ) {
                            $res_count->count = $results[0]->term_count;
                        } else {
                            $res_count->count = 0;
                        }
                    }
                }
                $query_not_sale        = implode( ' ', $query_not_sale );
if( BeRocket_AAPF::$debug_mode ) {
    $term_recount_log['query_not_sale'] = $query_not_sale;
    $term_recount_log['query_sale'] = $query_sale;
}
                $results           = $wpdb->get_results( $query_not_sale );
                foreach($input_terms as &$res_count) {
                    if( $res_count->term_id == 2 ) {
                        if( ! empty($results[0]->term_count ) ) {
                            $res_count->count = $results[0]->term_count;
                        } else {
                            $res_count->count = 0;
                        }
                    }
                }
            }
if( BeRocket_AAPF::$debug_mode ) {
    $term_recount_log['time_after_recount'] = microtime(true) - $time;
    $term_recount_log['after_recount'] = $input_terms;
    BeRocket_AAPF::$error_log['6_term_recount'][] = $term_recount_log;
}
            return $input_terms;
        }

        if( $product_cat || $hide_empty) {
            $cache_name = 'br2_'.$taxonomy.'_'.$order_by;

            $queried_object = $wp_query->get_queried_object_id();
            if ( ! empty($queried_object) ) {
                $cache_name .= '_'.$queried_object;
            }

            $cache_result = br_get_cache( $cache_name, 'get_attribute_values_hide_empty' );

            if ( $cache_result === false || ! isset( $cache_result['terms'] ) || ! isset( $cache_result['term_count'] ) ) {
                if ( class_exists('WP_Meta_Query') && class_exists('WP_Tax_Query') ) {
                    $args = array(
                        'orderby'    => $order_by,
                        'order'      => 'ASC',
                        'hide_empty' => false,
                    );

                    $woocommerce_hide_out_of_stock_items = ( BeRocket_AAPF_Widget::woocommerce_hide_out_of_stock_items() == 'no' );

                    if ( $taxonomy == 'product_cat' ) {
                        $re = self::get_product_categories( '', 0, array(), 0, 50, true );
                        $re = self::set_terms_on_same_level( $re, array(), false );
                    } elseif ( ! isset( $input_terms ) || $input_terms === FALSE ) {
                        $re = get_terms( $taxonomy, $args );
                        if( is_a($re, 'WP_Error') ) {
                            trigger_error('Filter uses incorrect attribute(taxonomy). Please check that taxonomy with this slug('.$taxonomy.') exist and has values');
                            return array();
                        }
                    } else {
                        $re = $input_terms;
                    }

                    if( $has_new_function ) {
                        $meta_query  = WC_Query::get_main_meta_query();
                    } else {
                        $meta_query = $old_meta_query;
                    }

                    $tax_query = array();
                    if( ! $woocommerce_hide_out_of_stock_items && function_exists('wc_get_product_visibility_term_ids') ) {
                        $product_visibility_terms = wc_get_product_visibility_term_ids();
                        $tax_query[] = array(
                            'taxonomy' => 'product_visibility',
                            'field'    => 'term_taxonomy_id',
                            'terms'    => array($product_visibility_terms['outofstock'], (is_search() ? $product_visibility_terms['exclude-from-search'] : $product_visibility_terms['exclude-from-catalog'])),
                            'operator' => 'NOT IN',
                        );
                    }
                    if( ! empty($product_cat) ) {
                        $tax_query[] = array(
                            'taxonomy' => 'product_cat',
                            'field'    => 'slug',
                            'terms'    => array($product_cat),
                            'operator' => 'IN',
                        );
                    }
                    foreach( $meta_query as $key => $val ) {
                        if( is_array($val) ) {
                            if ( ! empty( $val['price_filter'] ) || ! empty( $val['rating_filter'] ) ) {
                                unset( $meta_query[ $key ] );
                            }
                            if( isset( $val['relation']) ) {
                                unset($val['relation']);
                                foreach( $val as $key2 => $val2 ) {
                                    if ( isset( $val2['key'] ) && $val2['key'] == apply_filters('berocket_price_filter_meta_key', '_price', 'widget_1658') ) {
                                        if ( isset( $meta_query[ $key ][ $key2 ] ) ) unset( $meta_query[ $key ][ $key2 ] );
                                    } else if( $woocommerce_hide_out_of_stock_items ) {
                                        if ( isset( $val2['key'] ) && $val2['key'] == '_stock_status' ) {
                                            if ( isset( $meta_query[ $key ][ $key2 ] ) ) unset( $meta_query[ $key ][ $key2 ] );
                                        }
                                    }
                                }
                                if( count($meta_query[ $key ]) <= 1 ) {
                                    unset( $meta_query[ $key ] );
                                }
                            } else {
                                if ( isset( $val['key'] ) && $val['key'] == apply_filters('berocket_price_filter_meta_key', '_price', 'widget_1670') ) {
                                    if ( isset( $meta_query[ $key ] ) ) unset( $meta_query[ $key ] );
                                } else if( $woocommerce_hide_out_of_stock_items ) {
                                    if ( isset( $val['key'] ) && $val['key'] == '_stock_status' ) {
                                        if ( isset( $meta_query[ $key ] ) ) unset( $meta_query[ $key ] );
                                    }
                                }
                            }
                        }
                    }
                    $args      = $wp_query->query_vars;
                    if ( ! empty( $args['product_cat'] ) ) {
                        $tax_query[ 'product_cat' ] = array(
                            'taxonomy' => 'product_cat',
                            'terms'    => array( $args['product_cat'] ),
                            'field'    => 'slug',
                        );
                    }
                    $queried_object = $wp_query->get_queried_object_id();
                    if( ! empty($queried_object) && empty($br_aapf_wc_footer_widget) ) {
                        $query_object = $wp_query->get_queried_object();
                        if( ! empty($query_object->taxonomy) && ! empty($query_object->slug) ) {
                            $tax_query[ $query_object->taxonomy ] = array(
                                'taxonomy' => $query_object->taxonomy,
                                'terms'    => array( $query_object->slug ),
                                'field'    => 'slug',
                            );
                        }
                    }
                    $meta_query      = new WP_Meta_Query( $meta_query );
                    $meta_query_sql  = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
                    unset($meta_query);
                    $tax_query       = new WP_Tax_Query( $tax_query );
                    $tax_query_sql   = $tax_query->get_sql( $wpdb->posts, 'ID' );
                    unset($tax_query);
                    if( ! empty($re) && ! is_wp_error($re) ) {
                        $term_ids = wp_list_pluck( $re, 'term_taxonomy_id' );
                    }
                    if( empty($term_ids) || ! is_array($term_ids) || ! count($term_ids) ) {
                        $terms = array();
                        $term_count = array();
                    } else {

                        // Generate query
                        $query           = array();
                        $query['select'] = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) as term_count";
                        if( $taxonomy == 'product_cat' ) {
                            $query['select'] .= ", GROUP_CONCAT(DISTINCT {$wpdb->posts}.ID SEPARATOR ',') as PID";
                        } else {
                            $query['select'] .= ", '' as PID";
                        }
                        $query['select'] .= ", term_relationships.term_taxonomy_id as term_count_id";
                        $query['from']   = "FROM {$wpdb->posts}";
                        $query['join']   = "
                            INNER JOIN {$wpdb->term_relationships} AS term_relationships ON {$wpdb->posts}.ID = term_relationships.object_id
                            " . $tax_query_sql['join'] . $meta_query_sql['join'];
                        $query['where']   = "
                            WHERE {$wpdb->posts}.post_type IN ( 'product' )
                            AND " . br_select_post_status() . "
                            " . $tax_query_sql['where'] . $meta_query_sql['where'] . "
                            AND term_relationships.term_taxonomy_id IN (" . implode( ',', array_map( 'absint', $term_ids ) ) . ")
                        ";

                        br_where_search( $query );
                        if( defined( 'WCML_VERSION' ) && defined('ICL_LANGUAGE_CODE') ) {
                            $query['join'] = $query['join']." INNER JOIN {$wpdb->prefix}icl_translations as wpml_lang ON ( {$wpdb->posts}.ID = wpml_lang.element_id )";
                            $query['where'] = $query['where']." AND wpml_lang.language_code = '".ICL_LANGUAGE_CODE."' AND wpml_lang.element_type = 'post_product'";
                        }
                        $query['group_by'] = "GROUP BY term_relationships.term_taxonomy_id";
                        $query             = apply_filters( 'woocommerce_get_filtered_term_product_counts_query', $query );
                        $query             = apply_filters( 'berocket_posts_clauses_recount', $query );
                        $query             = implode( ' ', $query );
if( BeRocket_AAPF::$debug_mode ) {
    $term_recount_log['query_1'] = $query;
}
                        $wpdb->query( 'SET SESSION group_concat_max_len = 1000000' );
                        $results           = $wpdb->get_results( $query );
                        $term_to_taxonomy  = wp_list_pluck( $re, 'term_id', 'term_taxonomy_id' );
                        foreach($results as &$results_convert) {
                            $results_convert->term_count_id = $term_to_taxonomy[$results_convert->term_count_id];
                        }
                        $results_pid       = wp_list_pluck( $results, 'PID', 'term_count_id' );
                        $results           = wp_list_pluck( $results, 'term_count', 'term_count_id' );
                        $term_count = array();
                        $terms = array();
                        foreach($re as &$res_count) {
                            if( ! empty($results[$res_count->term_id] ) ) {
                                $res_count->count = $results[$res_count->term_id];
                            } elseif( $res_count->term_id != 'R__term_id__R' ) {
                                $res_count->count = 0;
                            }
                            if( $res_count->count > 0 ) {
                                $terms[] = $res_count->term_id;
                                $term_count[$res_count->term_id] = $res_count->count;
                            }
                        }
                    }
                    
                    if( $taxonomy == 'product_cat' ) {
                        $sort_cat = self::get_product_categories( '', 0, array(), 0, 50, true );
                        $assoc_re = array();
                        foreach($re as $term_re) {
                            if( empty($term_re->count) ) {
                                $term_re->count = 0;
                            }

                            if ( isset( $results_pid ) and isset( $results_pid[$term_re->term_id] ) ) {
                                $term_re->PID = $results_pid[ $term_re->term_id ];
                            }

                            $assoc_re[$term_re->term_id] = $term_re;
                        }
                        if ( isset( $results_pid ) && ! empty($assoc_re) ) {
                            self::product_cat_recount_with_child_pid( $sort_cat, $assoc_re, $new_re );
                        }
                        foreach($assoc_re as $term_id => $assoc_r) {
                            if( $assoc_r->count > 0 ) {
                                $term_count[$term_id] = $assoc_r->count;
                                if( ! in_array($term_id, $terms) ) {
                                    $terms[] = $term_id;
                                }
                            }
                        }
                    }

                    $cache_result = array('terms' => $terms, 'term_count' => $term_count, 'results_pid' => (isset($results_pid) ? $results_pid : false));
                    br_set_cache ( $cache_name, $cache_result, 'get_attribute_values_hide_empty', BeRocket_AJAX_cache_expire );
                } else {
                    $terms                 = array();
                    $q_args                = $wp_query->query_vars;
                    $q_args['nopaging']    = true;
                    $q_args['post__in']    = '';
                    $q_args['tax_query']   = '';
                    $q_args['taxonomy']    = '';
                    $q_args['term']        = '';
                    $q_args['meta_query']  = '';
                    $q_args['attribute']   = '';
                    $q_args['title']       = '';
                    $q_args['fields']      = 'ids';
                    if ( $product_cat ) {
                        $q_args['product_cat'] = $product_cat;
                    }
                    $the_query             = new WP_Query( $q_args );

                    $term_count = array();
                    foreach ( $the_query->posts as $post_id ) {
                        $curent_terms = wp_get_object_terms( $post_id, $taxonomy, array( "fields" => "ids" ) );

                        foreach ( $curent_terms as $t ) {
                            if ( ! in_array( $t, $terms ) ) {
                                $terms[] = $t;
                            }
                            if ( isset( $term_count[$t] ) ) {
                                $term_count[$t]++;
                            } else {
                                $term_count[$t] = 1;
                            }
                        }
                    }
                    $cache_result = array('terms' => $terms, 'term_count' => $term_count);
                    br_set_cache ( $cache_name, $cache_result, 'get_attribute_values_hide_empty', BeRocket_AJAX_cache_expire );
                }
            } else {
                $terms = $cache_result['terms'];
                $term_count = $cache_result['term_count'];
                if( ! empty($cache_result['results_pid']) ) {
                    $results_pid = $cache_result['results_pid'];
                }
            }
        }
if( BeRocket_AAPF::$debug_mode ) {
    $term_recount_log['cache_result'] = array();
    $term_recount_log['cache_result']['terms'] = (isset($terms) ? $terms : null);
    $term_recount_log['cache_result']['term_count'] = (isset($term_count) ? $term_count : null);
}
        if ( $hide_empty ) {

            unset( $curent_terms, $the_query );

            $args = array(
                'orderby'    => $order_by,
                'order'      => 'ASC',
                'hide_empty' => false,
            );

            if ( $taxonomy == 'product_cat' ) {
                $terms2 = self::get_product_categories( '', 0, array(), 0, 50, true );
                $terms2 = self::set_terms_on_same_level( $terms2, array(), false );
            } elseif ( ! isset($input_terms) || $input_terms === FALSE ) {
                $terms2 = get_terms( $taxonomy, $args );
            } else {
                $terms2 = $input_terms;
                unset( $input_terms );
            }
                if( isset($input_terms) && $input_terms !== FALSE && is_array($input_terms) && is_array($re) ) {
                    $re2 = array();
                    $is_child = false;
                    foreach($re as $re_id => $re_term) {
                        $remove = true;
                        foreach($input_terms as $input_term) {
                            if( ! $is_child && $input_term->term_id == 'R__term_id__R' ) {
                                $re2[0] = $input_term;
                                $is_child = true;
                            }
                            if( $input_term->term_id == $re_term->term_id ) {
                                $remove = false;
                                if( $is_child ) {
                                    $re2[$re_id] = $re_term;
                                }
                                break;
                            }
                        }
                        if( $remove ) {
                            unset($re[$re_id]);
                        }
                    }
                    if( $is_child ) {
                        $re = $re2;
                    }
                }

            $re = array();
            if( is_array($terms2) ) {
                foreach ( $terms2 as $t ) {
                    if( isset($t->term_id) ) {
                        if( $t->term_id == 'R__term_id__R' ) {
                            $re[$t->term_id] = $t;
                        } elseif ( in_array( $t->term_id, $terms ) ) {
                            $re[$t->term_id] = $t;
                            $re[$t->term_id]->count = $term_count[$t->term_id];
                        }
                    }
                }
            }
            unset( $terms2 );
        } else {
            $args = array(
                'orderby'    => $order_by,
                'order'      => 'ASC',
                'hide_empty' => false,
            );
            if ( $taxonomy == 'product_cat' ) {
                $re = self::get_product_categories( '', 0, array(), 0, 50, true );
                $re = self::set_terms_on_same_level( $re, array(), false );
            } elseif ( $input_terms === FALSE ) {
                $re = get_terms( $taxonomy, $args );
            } else {
                $re = $input_terms;
                unset( $input_terms );
            }
            if( $product_cat ) {
                foreach ( $re as $key => $t ) {
                    if( isset( $term_count[$re[$key]->term_id] ) ) {
                        $re[$key]->count = $term_count[$re[$key]->term_id];
                    } else {
                        $re[$key]->count = 0;
                    }
                }
            }
        }
if( BeRocket_AAPF::$debug_mode ) {
    $term_recount_log['time_before_recount'] = microtime(true) - $time;
    $term_recount_log['before_recount'] = $re;
}
        if( empty($re) || ! is_array($re) || count($re) == 0 ) {
if( BeRocket_AAPF::$debug_mode ) {
    BeRocket_AAPF::$error_log['6_term_recount'][] = $term_recount_log;
}
            return $re;
        }
        if( isset($input_terms) && $input_terms !== FALSE && is_array($input_terms) && is_array($re) ) {
            $re2 = array();
            $is_child = false;
            foreach($re as $re_id => $re_term) {
                $remove = true;
                foreach($input_terms as $input_term) {
                    if( ! $is_child && $input_term->term_id == 'R__term_id__R' ) {
                        $re2[0] = $input_term;
                        $is_child = true;
                    }
                    if( $input_term->term_id == $re_term->term_id ) {
                        $remove = false;
                        if( $is_child ) {
                            $re2[$re_id] = $re_term;
                        }
                        break;
                    }
                }
                if( $remove ) {
                    unset($re[$re_id]);
                }
            }
            if( $is_child ) {
                $re = $re2;
            }
        }

        if ( 
            (   ! $hide_empty 
                || apply_filters( 'berocket_aapf_is_filtered_page_check', ! empty($_GET['filters']), 'get_filter_args', $wp_query ) 
                || ( ! empty($br_options['out_of_stock_variable_reload']) && ! empty($br_options['out_of_stock_variable']) )
                || is_filtered()
            ) && $count_filtering 
        ) {
            if ( class_exists('WP_Meta_Query') && class_exists('WP_Tax_Query') ) {
                if ( $has_new_function ) {
                    $tax_query  = WC_Query::get_main_tax_query();
                } else {
                    $tax_query = $old_tax_query;
                }

                if ( $has_new_function ) {
                    $meta_query  = WC_Query::get_main_meta_query();
                } else {
                    $meta_query = $old_meta_query;
                }

                if ( is_array( $tax_query ) && 'OR' == $operator ) {
                    foreach ( $tax_query as $key => $val ) {
                        if ( is_array($val) ) {
                            if( isset( $val['relation']) ) {
                                unset($val['relation']);
                                foreach( $val as $key2 => $val2 ) {
                                    if ( isset( $val2['taxonomy'] ) && $val2['taxonomy'] == $taxonomy && ! empty($val2['is_berocket']) ) {
                                        if ( isset( $tax_query[ $key ][ $key2 ] ) ) unset( $tax_query[ $key ][ $key2 ] );
                                    }
                                }
                                if( count($tax_query[ $key ]) <= 1 ) {
                                    unset( $tax_query[ $key ] );
                                }
                            } else {
                                if ( isset( $val['taxonomy'] ) && $val['taxonomy'] == $taxonomy && ! empty($val['is_berocket']) ) {
                                    if ( isset( $tax_query[ $key ] ) ) unset( $tax_query[ $key ] );
                                }
                            }
                        }
                    }
                }

                $queried_object = $wp_query->get_queried_object_id();
                if ( ! empty($queried_object) && empty($br_aapf_wc_footer_widget) ) {
                    $query_object = $wp_query->get_queried_object();
                    if ( ! empty($query_object->taxonomy) && ! empty($query_object->slug) ) {
                        $tax_query[ $query_object->taxonomy ] = array(
                            'taxonomy' => $query_object->taxonomy,
                            'terms'    => array( $query_object->slug ),
                            'field'    => 'slug',
                        );
                    }
                }
                if( ! empty($product_cat) ) {
                    $tax_query[] = array(
                        'taxonomy' => 'product_cat',
                        'field'    => 'slug',
                        'terms'    => array($product_cat),
                        'operator' => 'IN',
                    );
                }
                $meta_query      = new WP_Meta_Query( $meta_query );
                $tax_query       = new WP_Tax_Query( $tax_query );
                $meta_query_sql  = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
                $tax_query_sql   = $tax_query->get_sql( $wpdb->posts, 'ID' );
                if( ! empty($re) && count($re)) {
                    $term_ids = wp_list_pluck( $re, 'term_taxonomy_id' );

                    // Generate query
                    $query           = array();
                    $query['select'] = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) as term_count";
                    if( $taxonomy == 'product_cat' ) {
                        $query['select'] .= ", GROUP_CONCAT(DISTINCT {$wpdb->posts}.ID SEPARATOR ',') as PID";
                    } else {
                        $query['select'] .= ", '' as PID";
                    }
                    $query['select'] .= ", term_relationships.term_taxonomy_id as term_count_id";
                    $query['from']   = "FROM {$wpdb->posts}";
                    $query['join']   = "
                        INNER JOIN {$wpdb->term_relationships} AS term_relationships ON {$wpdb->posts}.ID = term_relationships.object_id
                        " . $tax_query_sql['join'] . $meta_query_sql['join'];
                    $query['where']   = "
                        WHERE {$wpdb->posts}.post_type IN ( 'product' )
                        AND " . br_select_post_status() . "
                        " . $tax_query_sql['where'] . $meta_query_sql['where'] . "
                        AND term_relationships.term_taxonomy_id IN (" . implode( ',', array_map( 'absint', $term_ids ) ) . ")
                    ";
                    if( defined( 'WCML_VERSION' ) && defined('ICL_LANGUAGE_CODE') ) {
                        $query['join'] = $query['join']." INNER JOIN {$wpdb->prefix}icl_translations as wpml_lang ON ( {$wpdb->posts}.ID = wpml_lang.element_id )";
                        $query['where'] = $query['where']." AND wpml_lang.language_code = '".ICL_LANGUAGE_CODE."' AND wpml_lang.element_type = 'post_product'";
                    }
                    br_where_search( $query );
                    if( ! empty($post__in) ) {
                        $query['where'] .= " AND {$wpdb->posts}.ID IN (\"" . implode('","', $post__in) . "\")";
                    }

                    $query['where'] .= $old_join_posts;
                    $query['group_by'] = "GROUP BY term_relationships.term_taxonomy_id";
                    if( ! empty($br_options['out_of_stock_variable_reload']) ) {
                        $new_post_terms = berocket_isset($_POST['terms']);
                        $new_post_limits = berocket_isset($_POST['limits_arr']);
                        if( is_array($new_post_terms) && count($new_post_terms) ) {
                            foreach($new_post_terms as $new_post_terms_i => $new_post_term) {
                                if( $new_post_term[0] == $taxonomy ) {
                                    unset($new_post_terms[$new_post_terms_i]);
                                }
                            }
                        }
                        $taxonomy_terms = get_terms( array(
                            'taxonomy'      => $taxonomy,
                            'hide_empty'    => true,
                            'fields'        => 'ids'
                        ));
                        $limit_post__not_in = array();
                        foreach($taxonomy_terms as $taxonomy_term_id) {
                            $new_post_limits[$taxonomy] = array($taxonomy_term_id);
                            $limit_post__not_in[$taxonomy_term_id] = apply_filters('berocket_add_out_of_stock_variable', array(), $new_post_terms, $new_post_limits);
                        }
                        
                        if( is_array($limit_post__not_in) && count($limit_post__not_in) ) {
                            $limit_post__not_in_where_array = array();
                            $limit_post__term_id_without_product = array();
                            foreach($limit_post__not_in as $terms_id => $limit_post) {
                                if( is_array($limit_post) && count($limit_post) ) {
                                    $limit_post__not_in_where_array[$terms_id] = "({$wpdb->posts}.ID NOT IN (\"" . implode('","', $limit_post) . "\") AND term_relationships.term_taxonomy_id = {$terms_id})";
                                } else {
                                    $limit_post__term_id_without_product[] = $terms_id;
                                }
                            }
                            if( count($limit_post__term_id_without_product) ) {
                                $limit_post__not_in_where_array[] = "(term_relationships.term_taxonomy_id IN (".implode(', ', $limit_post__term_id_without_product)."))";
                            }
                            $limit_post__not_in_where = implode(' OR ', $limit_post__not_in_where_array);
                        }
                        if( empty($br_options['out_of_stock_variable_single']) && ! empty($limit_post__not_in_where) ) {
                            $query['where'] .= " AND ({$limit_post__not_in_where})";
                        }
                    } else {
                        if( ! empty($post__not_in) ) {
                            $query['where'] .= " AND {$wpdb->posts}.ID NOT IN (\"" . implode('","', $post__not_in) . "\")";
                        }
                    }
                    $query             = apply_filters( 'woocommerce_get_filtered_term_product_counts_query', $query );
                    $query             = apply_filters( 'berocket_posts_clauses_recount', $query );

                    $wpdb->query( 'SET SESSION group_concat_max_len = 1000000' );
                    if( ! empty($br_options['out_of_stock_variable_reload']) && ! empty($br_options['out_of_stock_variable_single']) ) {
                        if( isset($limit_post__not_in_where_array) &&is_array($limit_post__not_in_where_array) && count($limit_post__not_in_where_array) ) {
                            $results = array();
                            $results_pid = array();
                            foreach($limit_post__not_in_where_array as $limit_post) {
                                $query_new = $query;
                                $query_new['where'] .= " AND ({$limit_post})";
                                $query_new          = implode( ' ', $query_new );
                                $result             = $wpdb->get_results( $query_new );
                                $results_pid_temp   = wp_list_pluck( $result, 'PID', 'term_count_id' );
                                $results_temp       = wp_list_pluck( $result, 'term_count', 'term_count_id' );
                                $results_pid        = $results_pid + $results_pid_temp;
                                $results            = $results + $results_temp;
                                unset($results_pid_temp, $results_temp, $result);
                            }
                        }
                    } else {
                        $query             = implode( ' ', $query );
        if( BeRocket_AAPF::$debug_mode ) {
            $term_recount_log['query_2'] = $query;
        }
                        $results           = $wpdb->get_results( $query );
                        $term_to_taxonomy  = wp_list_pluck( $re, 'term_id', 'term_taxonomy_id' );
                        foreach($results as &$results_convert) {
                            $results_convert->term_count_id = $term_to_taxonomy[$results_convert->term_count_id];
                        }
                        $results_pid       = wp_list_pluck( $results, 'PID', 'term_count_id' );
                        $results           = wp_list_pluck( $results, 'term_count', 'term_count_id' );
                    }

                    foreach ( $re as &$res_count ) {
                        if ( ! empty( $results[ $res_count->term_id ] ) ) {
                            $res_count->count = $results[ $res_count->term_id ];
                        } else {
                            $res_count->count = 0; //this function uses to fix count of term, when count is 0 but term must be displayed
                            if( $hide_empty && $taxonomy != 'product_cat' ) {
                                unset( $re[ $res_count->term_id ] );
                            }
                        }
                    }
                }

            } else {
                $q_args = $wp_query->query_vars;
                $q_args['nopaging'] = true;
                if ( $product_cat ) {
                    $q_args['product_cat'] = $product_cat;
                }
                if ( isset($q_args['tax_query']) && is_array( $q_args['tax_query'] ) ) {
                    foreach( $q_args['tax_query'] as $key => $val ) {
                        if( is_array($val) ) {
                            if( isset( $val['relation']) ) {
                                unset($val['relation']);
                                foreach( $val as $key2 => $val2 ) {
                                    if ( isset( $val2['taxonomy'] ) && $val2['taxonomy'] == $taxonomy ) {
                                        if ( isset( $q_args['tax_query'][ $key ][ $key2 ] ) ) unset( $q_args['tax_query'][ $key ][ $key2 ] );
                                    }
                                }
                                if( count($q_args['tax_query'][ $key ]) <= 1 ) {
                                    unset( $q_args['tax_query'][ $key ] );
                                }
                            } else {
                                if ( isset( $val['taxonomy'] ) && $val['taxonomy'] == $taxonomy ) {
                                    if ( isset( $q_args['tax_query'][ $key ] ) ) unset( $q_args['tax_query'][ $key ] );
                                }
                            }
                        }
                    }
                }

                $q_args['taxonomy']    = '';
                $q_args['term']        = '';
                $q_args['attribute']   = '';
                $q_args['title']       = '';
                $args                  = apply_filters( 'berocket_aapf_listener_wp_query_args', array() );
                if ( isset( $args['product_tag'] ) )
                {
                    $q_args['product_tag'] = $args['product_tag'];
                }
                $q_args['fields'] = 'ids';

                if( isset($q_args['tax_query']) ) {
                    $tax_query_reset = $q_args['tax_query'];
                    unset($q_args['tax_query']);
                }
                $the_query = new WP_Query( $q_args );
                if( isset($tax_query_reset) ) {
                    $the_query->set('tax_query', $tax_query_reset);
                    $q_args['tax_query'] = $tax_query_reset;
                    unset($tax_query_reset);
                }
    if( BeRocket_AAPF::$debug_mode ) {
        $term_recount_log['count_query'] = $the_query;
        $term_recount_log['inside_query'] = $wp_query;
    }
                $count_terms = array();

                //debug( "1---------------------" );
                //debug( microtime() );
                foreach ( $the_query->posts as $post ) {
                    $curent_terms = br_wp_get_object_terms( $post, $taxonomy, array( "fields" => "ids" ) );
                    foreach ( $curent_terms as $t ) {
                        if ( isset( $count_terms[$t] ) ) {
                            $count_terms[$t] += 1;
                        } else {
                            $count_terms[$t] = 1;
                        }
                    }
                }
                //debug( "2---------------------" );
                //debug( microtime() );
    if( BeRocket_AAPF::$debug_mode ) {
        $term_recount_log['count'] = $count_terms;
    }
                unset( $the_query, $curent_terms, $q_args, $post );
                if ( isset($re) && is_array( $re ) ) {
                    foreach ( $re as $i => $re_val ) {
                        $re[$i]->count = 0;
                        if ( isset( $count_terms[$re[$i]->term_id] ) ) {
                            $re[$i]->count = $count_terms[$re[$i]->term_id];
                        }

                        $children = get_term_children( $re[$i]->term_id, $taxonomy );
                        $children_count = 0;
                        if( is_array( $children ) ) {
                            foreach ( $children as $child ) {
                                if( isset($count_terms[$child]) ) {
                                    $children_count += $count_terms[$child];
                                }
                            }
                        }
                        $re[$i]->count += ( $children_count );
                    }
                }
                unset( $children_count, $children, $count_terms );
            }
        }
        if( is_array($re) ) {
            $re = array_values( $re );
        }
if( BeRocket_AAPF::$debug_mode ) {
    $term_recount_log['time_after_recount'] = microtime(true) - $time;
    $term_recount_log['after_recount'] = $re;
    BeRocket_AAPF::$error_log['6_term_recount'][] = $term_recount_log;
}
        if( $taxonomy == 'product_cat' ) {
            $sort_cat = self::get_product_categories( '', 0, array(), 0, 50, true );
            $assoc_re = array();
            foreach($re as $term_re) {
                if( empty($term_re->count) ) {
                    $term_re->count = 0;
                }

                if ( isset( $results_pid ) and isset( $results_pid[$term_re->term_id] ) ) {
                    $term_re->PID = $results_pid[ $term_re->term_id ];
                }

                $assoc_re[$term_re->term_id] = $term_re;
            }
            if ( isset( $results_pid ) && ! empty($assoc_re) ) {
                self::product_cat_recount_with_child_pid( $sort_cat, $assoc_re, $new_re );
            }
            $re = $assoc_re;
            if ( is_array( $re ) && $hide_empty ) {
                foreach ( $re as $term_id => $term_re ) {
                    if ( $term_re->count == 0 ) {
                        unset($re[$term_id]);
                    }
                }
            }
            $input_terms = self::set_terms_on_same_level($input_terms);
            if( $input_terms !== FALSE && is_array($input_terms) && is_array($re) ) {
                $re2 = array();
                $is_child = false;
                foreach($re as $re_id => $re_term) {
                    $remove = true;
                    foreach($input_terms as $input_term) {
                        if( ! $is_child && $input_term->term_id == 'R__term_id__R' ) {
                            $re2[0] = $input_term;
                            $is_child = true;
                        } elseif( $input_term->term_id == $re_term->term_id ) {
                            $remove = false;
                            if( $is_child ) {
                                $re2[$re_id] = $re_term;
                            }
                            break;
                        }
                    }
                    if( $remove ) {
                        unset($re[$re_id]);
                    }
                }
                if( $is_child ) {
                    $re = $re2;
                }
            }
            /*
            if ( isset( $input_terms ) && $input_terms !== FALSE ) {
                $re = $input_terms; //FIXME: kills subcategories in taxonomy->product_cat
            }

            if ( is_array( $re ) ) {
                foreach ( $re as $term_id => $term_re ) {
                    if ( isset( $assoc_re[ $term_re->term_id ] ) ) {
                        $re[ $term_id ] = $assoc_re[ $term_re->term_id ];
                    } else {
                        if ( $term_re->term_id != 'R__term_id__R' ) {
                            unset( $re[ $term_id ] );
                        }
                    }
                }
            }*/
        }

        return $re;
    }

    public static function product_cat_recount_with_child_pid( $terms, $re, &$new_re, $parent_products = array() ) {
        foreach ( $terms as &$term ) {
            $term_products = array();

            if ( ! empty($re[ $term->term_id ]->PID ) ) {
                $term_products = array_unique(explode( ',', $re[ $term->term_id ]->PID ));

                if ( ! empty($term_products) ) {
                    
                    $parent_products = array_unique( (array) array_merge( $term_products, $parent_products ) );
                }
            }

            if ( ! empty( $term->child ) ) {
                if ( ! isset( $re[ $term->term_id ] ) ) {
                    $re[ $term->term_id ] = $terms[ $term->term_id ];
                }
                $new_re[ $term->term_id ] = $re[ $term->term_id ];

                $re[ $term->term_id ]->PID = self::product_cat_recount_with_child_pid( $term->child, $re, $new_re, $term_products );
                unset($re[ $term->term_id ]->child);
                $re[ $term->term_id ]->count = count( $re[ $term->term_id ]->PID );

                $new_re[ $term->term_id ] = $re[ $term->term_id ];

                $parent_products = array_unique( (array) array_merge( $re[ $term->term_id ]->PID, $parent_products ) );
            } else if ( isset( $re[ $term->term_id ] ) ) {
                $re[ $term->term_id ]->PID   = $term_products;
                $re[ $term->term_id ]->count = count( $term_products );
                $new_re[ $term->term_id ] = $re[ $term->term_id ];
            }
        }

        return $parent_products;
    }

    //FIXME: do we still need it?
    public static function product_cat_recount_with_child($terms, &$re) {
        $global_count = 0;

        foreach($terms as &$term) {
            $products = array();
            if ( $re[$term->term_id]->PID ) {
                $products = explode( ',', $re[ $term->term_id ]->PID );
            }

            if ( $product_ids ) {
                $products = array_merge( $product_ids, $products );
            }

            $count = $re[$term->term_id]->count;
            if( isset($re[$term->term_id]) and $term->parent ) {
                $count = (int) count( array_unique( $products ) );
            }

            if( !empty($term->child) ) {
                $count += self::product_cat_recount_with_child($term->child, $re, array_unique( $products ) );
                if( isset($re[$term->term_id]) ) {
                    $re[$term->term_id]->count = $count;
                }
            }
            $global_count += $count;
        }
        return $global_count;
    }

    public static function sort_child_parent_hierarchy($terms) {
        $terms_sort = array();
        $new_terms = $terms;
        $terms = array_reverse($terms);
        foreach($terms as $term_id => $term) {
            if(empty($term->parent)) {
                $terms_sort[] = $term->term_id;
                unset($terms[$term_id]);
            }
        }
        $length = 0;
        while(count($terms) && $length < 30) {
            foreach($terms as $term_id => $term) {
                $term_i = array_search($term->parent, $terms_sort);
                if( $term_i !== FALSE ) {
                    array_splice($terms_sort, $term_i, 0, array($term->term_id));
                    unset($terms[$term_id]);
                }
            }
            $length++;
        }
        if( count($terms) ) {
            foreach($terms as $term_id => $term) {
                $terms_sort[] = $term->term_id;
            }
        }
        $sort_array = array();
        foreach($new_terms as $terms) {
            $sort_array[] = array_search($terms->term_id, $terms_sort);
        }
        return $sort_array;
    }

    public static function sort_terms( &$terms, $sort_data ) {
        $sort_array = array();
        if ( ! empty($terms) && is_array( $terms ) && count( $terms ) ) {
            if ( ! empty($sort_data['attribute']) and in_array($sort_data['attribute'], array('product_cat', 'berocket_brand')) and ! empty($sort_data['order_values_by']) and $sort_data['order_values_by'] == 'Default' ) {
                foreach ( $terms as $term ) {
                    $element_of_sort = get_term_meta(  $term->term_id,  'order',  true );
                    if( is_array($element_of_sort) || $element_of_sort === false ) {
                        $sort_array[] = 0;
                    } else {
                        $sort_array[] = $element_of_sort;
                    }
                    if ( ! empty($term->child) ) {
                        self::sort_terms( $term->child, $sort_data );
                    }
                }
                if( BeRocket_AAPF::$debug_mode ) {
                    BeRocket_AAPF::$error_log[$sort_data['attribute'].'_sort'] = array('array' => $sort_array, 'sort' => $terms, 'data' => $sort_data );
                }
                @ array_multisort( $sort_array, $sort_data['order_values_type'], SORT_NUMERIC, $terms );
            } elseif ( ! empty($sort_data['wc_order_by']) or ! empty($sort_data['order_values_by']) ) {
                if ( ! empty($sort_data['order_values_by']) and $sort_data['order_values_by'] == 'Numeric' ) {
                    foreach ( $terms as $term ) {
                        $sort_array[] = (float)preg_replace('/\s+/', '', str_replace(',', '.', $term->name));
                        if ( ! empty($term->child) ) {
                            self::sort_terms( $term->child, $sort_data );
                        }
                    }
                    @ array_multisort( $sort_array, $sort_data['order_values_type'], SORT_NUMERIC, $terms );
                } else {
                    $get_terms_args = array( 'hide_empty' => '0', 'fields' => 'ids' );

                    if ( ! empty($sort_data['order_values_by']) and $sort_data['order_values_by'] == 'Alpha' ) {
                        $orderby = 'name';
                    } else {
                        $orderby = wc_attribute_orderby( $terms[0]->taxonomy );
                    }

                    switch ( $orderby ) {
                        case 'name':
                            $get_terms_args['orderby']    = 'name';
                            $get_terms_args['menu_order'] = false;
                            break;
                        case 'id':
                            $get_terms_args['orderby']    = 'id';
                            $get_terms_args['order']      = 'ASC';
                            $get_terms_args['menu_order'] = false;
                            break;
                        case 'menu_order':
                            $get_terms_args['menu_order'] = 'ASC';
                            break;
                        default:
                            break;
                    }

                    if( count($terms) ) {
                        $terms_first = reset($terms);
                        $terms2 = get_terms( $terms_first->taxonomy, $get_terms_args );
                        foreach ( $terms as $term ) {
                            $sort_array[] = array_search($term->term_id, $terms2);
                            if ( ! empty($term->child) ) {
                                self::sort_terms( $term->child, $sort_data );
                            }
                        }
                        @ array_multisort( $sort_array, $sort_data['order_values_type'], SORT_NUMERIC, $terms );
                    }
                }
                $sort_array = self::sort_child_parent_hierarchy($terms);
                @ array_multisort( $sort_array, SORT_DESC, SORT_NUMERIC, $terms );
            }
        }
    }

    public static function set_terms_on_same_level( $terms, $return_array = array(), $add_spaces = true ) {
        if ( ! empty($terms) && is_array( $terms ) && count( $terms ) ) {
            foreach ( $terms as $term ) {
                if ( $add_spaces ) {
                    for ( $i = 0; $i < $term->depth; $i++ ) {
                        $term->name = "&nbsp;&nbsp;" . $term->name;
                    }
                }

                if( ! empty($term->child) ) {
                    $child = $term->child;
                    unset( $term->child );
                }

                $return_array[] = $term;

                if ( ! empty($child) ) {
                    $return_array = self::set_terms_on_same_level( $child, $return_array, $add_spaces );
                    unset($child);
                }
            }
        } else {
            $return_array = $terms;
        }
        return $return_array;
    }

    public static function get_filter_products( $wp_query_product_cat, $woocommerce_hide_out_of_stock_items, $use_filters = true ) {
        $BeRocket_AAPF = BeRocket_AAPF::getInstance();
        global $wp_query, $wp_rewrite;
        $_POST['product_cat'] = $wp_query_product_cat;

        $old_post_terms = (isset($_POST['terms']) ? $_POST['terms'] : null);

        add_filter( 'woocommerce_pagination_args', array( __CLASS__, 'pagination_args' ) );

        $args = apply_filters( 'berocket_aapf_listener_wp_query_args', array() );
        $tags = (isset($args['product_tag']) ? $args['product_tag'] : null);
        $meta_query = $BeRocket_AAPF->remove_out_of_stock( array() , true, $woocommerce_hide_out_of_stock_items != 'yes' );
        $args['post__in'] = array();

        if( $woocommerce_hide_out_of_stock_items == 'yes' ) {
            $args['post__in'] = $BeRocket_AAPF->remove_out_of_stock( $args['post__in'] );
        }
        if ( $use_filters ) {
            $args['post__in'] = $BeRocket_AAPF->limits_filter( $args['post__in'] );
            $args['post__in'] = $BeRocket_AAPF->price_filter( $args['post__in'] );
            $args['post__in'] = $BeRocket_AAPF->add_terms( $args['post__in'] );
        } else {
            $args = array( 'posts_per_page' => -1 );
            if ( ! empty($_POST['product_cat']) and $_POST['product_cat'] != '-1' ) {
                $args['tax_query'][] = array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => strip_tags( $_POST['product_cat'] ),
                    'operator' => 'IN'
                );
            }
        }

        $args['post_status'] = 'publish';
        $args['post_type'] = 'product';

        if( isset($args['tax_query']) ) {
            $tax_query_reset = $args['tax_query'];
            unset($args['tax_query']);
        }
        $wp_query = new WP_Query( $args );
        if( isset($tax_query_reset) ) {
            $wp_query->set('tax_query', $tax_query_reset);
            $args['tax_query'] = $tax_query_reset;
            unset($tax_query_reset);
        }

        // here we get max products to know if current page is not too big
        if( ! isset($_POST['location']) ) {
            $_POST['location'] = '';
        }
        if ( $wp_rewrite->using_permalinks() and preg_match( "~/page/([0-9]+)~", $_POST['location'], $mathces ) or preg_match( "~paged?=([0-9]+)~", $_POST['location'], $mathces ) ) {
            $args['paged'] = min( $mathces[1], $wp_query->max_num_pages );
            if( isset($args['tax_query']) ) {
                $tax_query_reset = $args['tax_query'];
                unset($args['tax_query']);
            }
            $wp_query = new WP_Query( $args );
            if( isset($tax_query_reset) ) {
                $wp_query->set('tax_query', $tax_query_reset);
                $args['tax_query'] = $tax_query_reset;
                unset($tax_query_reset);
            }
        }
        if ( $wp_query->found_posts <= 1 ) {
            $args['paged'] = 0;
            if( isset($args['tax_query']) ) {
                $tax_query_reset = $args['tax_query'];
                unset($args['tax_query']);
            }
            $wp_query = new WP_Query( $args );
            if( isset($tax_query_reset) ) {
                $wp_query->set('tax_query', $tax_query_reset);
                $args['tax_query'] = $tax_query_reset;
                unset($tax_query_reset);
            }
        }

        $products = array();
        if ( $wp_query->have_posts() ) {
            while ( have_posts() ) {
                the_post();
                $products[] = get_the_ID();
            }
        }

        wp_reset_query();
        if( isset($meta_query) && is_array( $meta_query ) && count( $meta_query ) > 0 ) {
            $q_vars = $wp_query->query_vars;
            foreach( $q_vars['meta_query'] as $key_meta => $val_meta ) {
                if( $key_meta != 'relation' && $val_meta['key'] == '_stock_status') {
                    unset( $q_vars['meta_query'][$key_meta] );
                }
            }
            $q_vars['meta_query'] = array_merge( $q_vars['meta_query'], $meta_query );
            $wp_query->set('meta_query', $q_vars['meta_query']);
        }
        if( ! empty($tags) ) {
            $q_vars = $wp_query->query_vars;
            $q_vars['product_tag'] = $tags;
            unset($q_vars['s']);
            if( isset($q_vars['tax_query']) ) {
                $tax_query_reset = $q_vars['tax_query'];
                unset($q_vars['tax_query']);
            }
            $wp_query = new WP_Query( $q_vars );
            if( isset($tax_query_reset) ) {
                $wp_query->set('tax_query', $tax_query_reset);
                $q_vars['tax_query'] = $tax_query_reset;
                unset($tax_query_reset);
            }
        }

        $_POST['terms'] = $old_post_terms;
        return $products;
    }

    /**
     * Validating and updating widget data
     *
     * @param array $new_instance
     * @param array $old_instance
     *
     * @return array - new merged instance
     */
    function update( $new_instance, $old_instance ) {
        return $old_instance;
    }

    /**
     * Output admin form
     *
     * @param array $instance
     *
     * @return string|void
     */
    function form( $instance ) {
        $BeRocket_AAPF = BeRocket_AAPF::getInstance();
        $BeRocket_AAPF->register_admin_scripts();
        wp_enqueue_script( 'berocket_aapf_widget-admin-colorpicker', plugins_url( '../js/colpick.js', __FILE__ ), array( 'jquery' ), BeRocket_AJAX_filters_version );
        wp_enqueue_script( 'berocket_aapf_widget-admin' );

        wp_register_style( 'berocket_aapf_widget-colorpicker-style', plugins_url( '../css/colpick.css', __FILE__ ), "", BeRocket_AJAX_filters_version );
        wp_enqueue_style( 'berocket_aapf_widget-colorpicker-style' );

        wp_register_style( 'berocket_aapf_widget-style', plugins_url('../css/admin.css', __FILE__), "", BeRocket_AJAX_filters_version );
        wp_enqueue_style( 'berocket_aapf_widget-style' );

        $default = apply_filters( 'berocket_aapf_form_defaults', self::$defaults );

        $instance          = wp_parse_args( (array) $instance, $default );
        if( ! empty($instance['product_cat']) && is_array($instance['product_cat']) ) {
            foreach($instance['product_cat'] as &$product_cat_el) {
                $product_cat_el = urldecode($product_cat_el);
            }
        }
        $instance['product_cat'] = (isset($instance['product_cat']) ? $instance['product_cat'] : '');
        $attributes        = br_aapf_get_attributes();
        $categories        = self::get_product_categories( @ json_decode( $instance['product_cat'] ) );
        $categories        = self::set_terms_on_same_level( $categories );
        $tags              = get_terms( 'product_tag' );
        $custom_taxonomies = get_object_taxonomies( 'product' );
        $custom_taxonomies = array_combine($custom_taxonomies, $custom_taxonomies);

        include AAPF_TEMPLATE_PATH . "admin.php";
    }

    /**
     * Widget ajax listener
     */
    public static function listener(){
        global $wp_query, $wp_rewrite;
        $br_options = apply_filters( 'berocket_aapf_listener_br_options', BeRocket_AAPF::get_aapf_option() );

        $wp_query = self::listener_wp_query();
        if( class_exists('WC_Query') &&  method_exists('WC_Query', 'product_query') ) {
            $wp_query->get_posts();
            wc()->query->product_query($wp_query);
        }

        if( ! empty($br_options['alternative_load']) && $br_options['alternative_load_type'] == 'wpajax' ) {
            ob_start();

            $is_have_post = $wp_query->have_posts();
            if ( $is_have_post ) {
                do_action('woocommerce_before_shop_loop');

                woocommerce_product_loop_start();
                woocommerce_product_subcategories();

                while ( have_posts() ) {
                    the_post();
                    wc_get_template_part( 'content', 'product' );
                }

                woocommerce_product_loop_end();

                do_action('woocommerce_after_shop_loop');

                wp_reset_postdata();

                $_RESPONSE['products'] = ob_get_contents();
            } else {
                echo apply_filters( 'berocket_aapf_listener_no_products_message', "<div class='no-products" . ( ( $br_options['no_products_class'] ) ? ' '.$br_options['no_products_class'] : '' ) . "'>" . $br_options['no_products_message'] . "</div>" );

                $_RESPONSE['no_products'] = ob_get_contents();
            }
            ob_end_clean();
            if( empty($br_options['woocommerce_removes']['ordering']) ) {
                ob_start();
                woocommerce_catalog_ordering();
                $_RESPONSE['catalog_ordering'] = ob_get_contents();
                ob_end_clean();
            }
            if( empty($br_options['woocommerce_removes']['result_count']) ) {
                ob_start();
                woocommerce_result_count();
                $_RESPONSE['result_count'] = ob_get_contents();
                ob_end_clean();
            }
            if( empty($br_options['woocommerce_removes']['pagination']) ) {
                ob_start();
                woocommerce_pagination();
                $_RESPONSE['pagination'] = ob_get_contents();
                ob_end_clean();
            }
        }
        
        if( ! empty($br_options['recount_products']) ) {
            $_RESPONSE['attributesname'] = array();
            $_RESPONSE['attributes'] = array();
            if(isset($_POST['attributes']) && is_array($_POST['attributes'])) {
                $attributes = array_combine ( $_POST['attributes'], $_POST['cat_limit'] );
                foreach( $attributes as $attribute => $cat_limit ) {
                    if($attribute != 'price') {
                        $terms = FALSE;
                        if( $attribute == '_stock_status' ) {
                            $terms = array();
                            array_push($terms, (object)array('term_id' => '1', 'name' => __('In stock', 'BeRocket_AJAX_domain'), 'slug' => 'instock', 'taxonomy' => '_stock_status', 'count' => 1));
                            array_push($terms, (object)array('term_id' => '2', 'name' => __('Out of stock', 'BeRocket_AJAX_domain'), 'slug' => 'outofstock', 'taxonomy' => '_stock_status', 'count' => 1));
                        }
                        $_RESPONSE['attributesname'][] = $attribute;
                        $terms = BeRocket_AAPF_Widget::get_attribute_values( $attribute, 'id', false, TRUE, $terms, $cat_limit );
                        $_RESPONSE['attributes'][] = self::remove_pid( array_values($terms) );
                    }
                }
            }
        }
        $_RESPONSE = apply_filters('berocket_ajax_response_without_fix', $_RESPONSE);
        echo json_encode( $_RESPONSE );

        die();
    }

    public static function remove_pid( $terms ) {

        foreach ( $terms as &$term ) {
            if ( isset( $term ) ) {
                if ( isset( $term->PID ) ) {
                    $term->PID = '';
                }

                if ( is_array( $term ) ) {
                    foreach ( $term as &$subterm ) {
                        if ( isset( $subterm ) and isset( $subterm->PID ) ) {
                            $subterm->PID = '';
                        }
                    }
                }

            }
        }
        return $terms;
    }

    public static function listener_wp_query() {
        global $wp_query, $wp_rewrite;
        $br_options = apply_filters( 'berocket_aapf_listener_br_options', BeRocket_AAPF::get_aapf_option() );

        $add_to_args = array();
        if ( ! empty($_POST['limits']) && is_array($_POST['limits']) ) {
            foreach ( $_POST['limits'] as $post_key => $t ) {
                if( $t[0] == '_date' ) {
                    $from = $t[1];
                    $to = $t[2];
                    $from = substr($from, 0, 2).'/'.substr($from, 2, 2).'/'.substr($from, 4, 4);
                    $to = substr($to, 0, 2).'/'.substr($to, 2, 2).'/'.substr($to, 4, 4);
                    $from = date('Y-m-d 00:00:00', strtotime($from));
                    $to = date('Y-m-d 23:59:59', strtotime($to));
                    $add_to_args['date_query'] = array(
                        'after' => $from,
                        'before' => $to,
                    );
                    unset($_POST['limits'][$post_key]);
                }
            }
        }
        $BeRocket_AAPF = BeRocket_AAPF::getInstance();
        if ( ! empty($_POST['terms']) && is_array($_POST['terms']) ) {
            $stop_sale = false;
            $check_sale = $check_notsale = 0;
            foreach ( $_POST['terms'] as $post_key => $t ) {
                if( $t[0] == 'price' ) {
                    if( preg_match( "~\*~", $t[1] ) ) {
                        if( ! isset( $_POST['price_ranges'] ) ) {
                            $_POST['price_ranges'] = array();
                        }
                        $_POST['price_ranges'][] = $t[1];
                        unset( $_POST['terms'][$post_key] );
                    }
                } elseif( $t[0] == '_sale' ) {
                    // if both used do nothing
                    if ( $t[0] == '_sale' and $t[3] == 'sale' ) {
                        $check_sale++;
                    }
                    if ( $t[0] == '_sale' and $t[3] == 'notsale' ) {
                        $check_notsale++;
                    }
                    unset($_POST['terms'][$post_key]);
                } elseif( $t[0] == '_rating' ) {
                    $_POST['terms'][$post_key][0] = 'product_visibility';
                }
            }
            if ( ! empty($br_options['slug_urls']) ) {
                foreach ( $_POST['terms'] as $post_key => $t ) {
                    if( $t[0] == '_stock_status' ) {
                        $_stock_status = array( 'instock' => 1, 'outofstock' => 2);
                        $_POST['terms'][$post_key][1] = (isset($_stock_status[$t[1]]) ? $_stock_status[$t[1]] : $_stock_status['instock']);
                    } else {
                        $t[1] = get_term_by( 'slug', $t[3], $t[0] );
                        $t[1] = $t[1]->term_id;
                        $_POST['terms'][$post_key] = $t;
                    }
                }
            }

            if ( ! ($check_sale and $check_notsale) ) {
                if ( $check_sale ) {
                    $add_to_args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
                } elseif( $check_notsale ) {
                    $add_to_args['post__in'] = array_merge( array( 0 ), $BeRocket_AAPF->wc_get_product_ids_not_on_sale() );
                }
            }
        }

        add_filter( 'post_class', array( __CLASS__, 'add_product_class' ) );
        add_filter( 'woocommerce_pagination_args', array( __CLASS__, 'pagination_args' ) );

        $woocommerce_hide_out_of_stock_items = BeRocket_AAPF_Widget::woocommerce_hide_out_of_stock_items();

        $meta_query = $BeRocket_AAPF->remove_out_of_stock( array() , true, $woocommerce_hide_out_of_stock_items != 'yes' );

        $args = apply_filters( 'berocket_aapf_listener_wp_query_args', array() );
        foreach($add_to_args as $arg_name => $add_arg) {
            $args[$arg_name] = $add_arg;
        }
        if( ! empty($_POST['limits']) ) {
            $args = apply_filters('berocket_aapf_convert_limits_to_tax_query', $args, $_POST['limits']);
        }
        if( ! isset($args['post__in']) ) {
            $args['post__in'] = array();
        }
        if( $woocommerce_hide_out_of_stock_items == 'yes' ) {
            $args['post__in'] = $BeRocket_AAPF->remove_out_of_stock( $args['post__in'] );
        }
        if( ! br_woocommerce_version_check() ) {
            $args['post__in'] = $BeRocket_AAPF->remove_hidden( $args['post__in'] );
        }
        $args['meta_query'] = $meta_query;

        if( ! empty($_POST['limits']) ) {
            $args = apply_filters('berocket_aapf_convert_limits_to_tax_query', $args, $_POST['limits']);
        }
        if( isset($_POST['price']) && is_array($_POST['price']) ) {
            $_POST['price'] = apply_filters('berocket_min_max_filter', $_POST['price']);
        }
        $min = isset( $_POST['price'][0] ) ? floatval( $_POST['price'][0] ) : 0;
        $max = isset( $_POST['price'][1] ) ? floatval( $_POST['price'][1] ) : 9999999999;

        $args['meta_query'][] = array(
            'key'          => apply_filters('berocket_price_filter_meta_key', '_price', 'widget_2847'),
            'value'        => array( $min, $max ),
            'compare'      => 'BETWEEN',
            'type'         => 'DECIMAL',
            'price_filter' => true,
        );
    $args['post_status']    = 'publish';
        if ( is_user_logged_in() ) {
            $args['post_status'] .= '|private';
        }
        $args['post_type']      = 'product';
        $default_posts_per_page = get_option( 'posts_per_page' );
        $args['posts_per_page'] = apply_filters( 'loop_shop_per_page', $default_posts_per_page );
        if ( ! empty($_POST['price_ranges']) && is_array($_POST['price_ranges']) ) {
            $price_range_query = array( 'relation' => 'OR' );
            foreach ( $_POST['price_ranges'] as $range ) {
                $range = explode( '*', $range );
                $price_range_query[] = array( 'key' => apply_filters('berocket_price_filter_meta_key', '_price', 'widget_2867'), 'compare' => 'BETWEEN', 'type' => 'NUMERIC', 'value' => array( ($range[0] - 1), $range[1] ) );
            }
            $args['meta_query'][] = $price_range_query;
        }
        if ( ! empty($_POST['price']) && is_array($_POST['price']) ) {
            $args['meta_query'][] = array( 'key' => apply_filters('berocket_price_filter_meta_key', '_price', 'widget_2872'), 'compare' => 'BETWEEN', 'type' => 'NUMERIC', 'value' => array( ($_POST['price'][0]), $_POST['price'][1] ) );
        }

        if( isset($_POST['product_taxonomy']) && $_POST['product_taxonomy'] != '-1' && strpos( $_POST['product_taxonomy'], '|' ) !== FALSE ) {
            $product_taxonomy = explode( '|', $_POST['product_taxonomy'] );
            $args['taxonomy'] = $product_taxonomy[0];
            $args['term'] = $product_taxonomy[1];
        }
        if( isset($_POST['s']) && strlen($_POST['s']) > 0 ) {
            $args['s'] = $_POST['s'];
        }

        if( function_exists('wc_get_product_visibility_term_ids') ) {
            $product_visibility_term_ids = wc_get_product_visibility_term_ids();

            $args['tax_query'][] = array(
                'taxonomy' => 'product_visibility',
                'field'    => 'term_taxonomy_id',
                'terms'    => array($product_visibility_term_ids['exclude-from-catalog']),
                'operator' => 'NOT IN'
            );
        }
        $args = array_merge($args, WC()->query->get_catalog_ordering_args());
        $wp_query = new WP_Query( $args );

        // here we get max products to know if current page is not too big
        $is_using_permalinks = $wp_rewrite->using_permalinks();
        $_POST['location'] = (empty($_POST['location']) ? $_GET['location'] : $_POST['location']);
        if ( $is_using_permalinks and preg_match( "~/page/([0-9]+)~", $_POST['location'], $mathces ) or preg_match( "~paged?=([0-9]+)~", $_POST['location'], $mathces ) ) {
            $args['paged'] = min( $mathces[1], $wp_query->max_num_pages );
            
            $wp_query = new WP_Query( $args );
        }
        return apply_filters('berocket_listener_wp_query_return', $wp_query, $args);
    }

    public static function rebuild() {
        add_action('woocommerce_before_shop_loop', array( __CLASS__, 'tags_restore' ), 999999);
    }

    public static function tags_restore() {
		global $wp_query;
        $args = apply_filters( 'berocket_aapf_listener_wp_query_args', array() );
        $tags = ( empty($args['product_tag']) ? '' : $args['product_tag'] );
        if( ! empty($tags) ) {
            $q_vars = $wp_query->query_vars;
            $q_vars['product_tag'] = $tags;
            $q_vars['taxonomy'] = '';
            $q_vars['term'] = '';
            unset( $q_vars['s'] );
            if( isset($q_vars['tax_query']) ) {
                $tax_query_reset = $q_vars['tax_query'];
                unset($q_vars['tax_query']);
            }
            $wp_query = new WP_Query( $q_vars );
            if( isset($tax_query_reset) ) {
                $wp_query->set('tax_query', $tax_query_reset);
                $q_vars['tax_query'] = $tax_query_reset;
                unset($tax_query_reset);
            }
        }
    }

    public static function woocommerce_before_main_content() {
        ?>||EXPLODE||<?php
        self::tags_restore();
    }

    public static function woocommerce_after_main_content() {
        ?>||EXPLODE||<?php
    }

    public static function pre_get_posts() {
        add_action( 'woocommerce_before_shop_loop', array( __CLASS__, 'woocommerce_before_main_content' ), 999999 );
        add_action( 'woocommerce_after_shop_loop', array( __CLASS__, 'woocommerce_after_main_content' ), 1 );
    }

    public static function end_clean() {
        global $wp_query, $wp_rewrite;
        $br_options = apply_filters( 'berocket_aapf_listener_br_options', BeRocket_AAPF::get_aapf_option() );
        if ( $br_options['alternative_load_type'] != 'js' ) {
            $_RESPONSE['products'] = explode('||EXPLODE||', ob_get_contents());
            $_RESPONSE['products'] = $_RESPONSE['products'][1];
            ob_end_clean();

            if ( $_RESPONSE['products'] == null ) {
	            unset( $_RESPONSE['products'] );
	            ob_start();
                echo apply_filters( 'berocket_aapf_listener_no_products_message', "<p class='no-products woocommerce-info" . ( ( $br_options['no_products_class'] ) ? ' '.$br_options['no_products_class'] : '' ) . "'>" . $br_options['no_products_message'] . "</p>" );
                $_RESPONSE['no_products'] = ob_get_contents();
                ob_end_clean();
            } else {
                $_RESPONSE['products'] = str_replace( 'explode=explode#038;', '', $_RESPONSE['products'] );
                $_RESPONSE['products'] = str_replace( '&#038;explode=explode', '', $_RESPONSE['products'] );
                $_RESPONSE['products'] = str_replace( '?explode=explode', '', $_RESPONSE['products'] );
            }
        }

        if ( ! empty($br_options['recount_products']) ) {
            $_RESPONSE['attributesname'] = array();
            $_RESPONSE['attributes']     = array();

            if ( isset($_POST['attributes']) && is_array( $_POST['attributes'] ) ) {
                $attributes = array_combine ( $_POST['attributes'], $_POST['cat_limit'] );
                foreach ( $attributes as $attribute => $cat_limit ) {
                    if ( $attribute != 'price' ) {
                        $terms = FALSE;
                        if( $attribute == '_stock_status' ) {
                            $terms = array();
                            array_push($terms, (object)array('term_id' => '1', 'name' => __('In stock', 'BeRocket_AJAX_domain'), 'slug' => 'instock', 'taxonomy' => '_stock_status', 'count' => 1));
                            array_push($terms, (object)array('term_id' => '2', 'name' => __('Out of stock', 'BeRocket_AJAX_domain'), 'slug' => 'outofstock', 'taxonomy' => '_stock_status', 'count' => 1));
                        }
                        $_RESPONSE['attributesname'][] = $attribute;
                        $terms                         = BeRocket_AAPF_Widget::get_attribute_values( $attribute, 'id', ( empty($br_options['show_all_values']) ), TRUE, $terms, $cat_limit );
                        $_RESPONSE['attributes'][]     = self::remove_pid( array_values($terms));
                    }
                }
            }
        }
        if( empty($br_options['woocommerce_removes']['ordering']) ) {
            ob_start();
            woocommerce_catalog_ordering();
            $_RESPONSE['catalog_ordering'] = ob_get_contents();
            ob_end_clean();
        }
        if( empty($br_options['woocommerce_removes']['result_count']) ) {
            ob_start();
            woocommerce_result_count();
            $_RESPONSE['result_count'] = ob_get_contents();
            ob_end_clean();
        }
        if( empty($br_options['woocommerce_removes']['pagination']) ) {
            ob_start();
            woocommerce_pagination();
            $_RESPONSE['pagination'] = ob_get_contents();
            $_RESPONSE['pagination'] = str_replace( 'explode=explode#038;', '', $_RESPONSE['pagination'] );
            $_RESPONSE['pagination'] = str_replace( '&#038;explode=explode', '', $_RESPONSE['pagination'] );
            $_RESPONSE['pagination'] = str_replace( '?explode=explode', '', $_RESPONSE['pagination'] );
            ob_end_clean();
        }
        if ( $br_options['alternative_load_type'] == 'js' ) echo '||JSON||';
        $_RESPONSE = apply_filters('berocket_ajax_response_with_fix', $_RESPONSE);
        $_RESPONSE['attributesname'] = array_values($_RESPONSE['attributesname']);
        $_RESPONSE['attributes'] = array_values($_RESPONSE['attributes']);
        foreach($_RESPONSE['attributesname'] as &$attributesname) {
            if( ! is_array($attributesname) ) {
                $attributesname = array();
            }
        }
        foreach($_RESPONSE['attributes'] as &$attributes) {
            if( ! is_array($attributes) ) {
                $attributes = array();
            }
        }
        echo json_encode( $_RESPONSE );
        if ( $br_options['alternative_load_type'] == 'js' ) echo '||JSON||';

        die();
    }

    public static function start_clean() {
        $br_options = apply_filters( 'berocket_aapf_listener_br_options', BeRocket_AAPF::get_aapf_option() );
        if ( $br_options['alternative_load_type'] != 'js' ) {
            ob_start();
        }
    }

    public static function color_listener() {
        if ( defined('DOING_AJAX') && DOING_AJAX && !isset( $_POST ['tax_color_set'] ) && isset( $_POST ['br_widget_color'] ) ) {
            $_POST ['tax_color_set'] = $_POST ['br_widget_color'];
        }
        if( isset( $_POST ['tax_color_set'] ) ) {
            if ( current_user_can( 'manage_woocommerce' ) ) {
                foreach( $_POST['tax_color_set'] as $key => $value ) {
                    if( $_POST['type'] == 'color' ) {
                        foreach($value as $term_key => $term_val) {
                            if( !empty($term_val) ) {
                                update_metadata( 'berocket_term', $term_key, $key, $term_val );
                            } else {
                                delete_metadata( 'berocket_term', $term_key, $key );
                            }
                        }
                    } else {
                        update_metadata( 'berocket_term', $key, $_POST['type'], $value );
                    }
                }
                unset( $_POST['tax_color_set'] );
            }
        } else {
            BeRocket_AAPF_Widget::color_list_view( $_POST['type'], $_POST['tax_color_name'], true );
            wp_die();
        }
    }

    public static function color_list_view( $type, $taxonomy_name, $load_script = false ) {
        $terms = get_terms( $taxonomy_name, array( 'hide_empty' => false ) );
        $set_query_var_color = array();
        $set_query_var_color['terms'] = $terms;
        $set_query_var_color['type'] = $type;
        $set_query_var_color['load_script'] = $load_script;
        set_query_var( 'berocket_query_var_color', $set_query_var_color );
        br_get_template_part( 'color_ajax' );
    }
    
    public static function ajax_include_exclude_list() {
        if( ! empty($_POST['taxonomy_name']) ) {
            echo self::include_exclude_terms_list($_POST['taxonomy_name']);
        }
        wp_die();
    }
    
    public static function include_exclude_terms_list($taxonomy_name = false, $selected = array() ) {
        $terms = get_terms( $taxonomy_name, array( 'hide_empty' => false ) );
        $set_query_var_exclude_list = array();
        $set_query_var_exclude_list['taxonomy'] = $taxonomy_name;
        $set_query_var_exclude_list['terms'] = $terms;
        $set_query_var_exclude_list['selected'] = $selected;
        set_query_var( 'berocket_var_exclude_list', $set_query_var_exclude_list );
        ob_start();
        br_get_template_part( 'include_exclude_list' );
        return ob_get_clean();
    }

    public static function get_product_categories( $current_product_cat = '', $parent = 0, $data = array(), $depth = 0, $max_count = 9, $follow_hierarchy = false ) {
        return br_get_sub_categories( $parent, 'id', array( 'return' => 'hierarchy_objects', 'max_depth' => $max_count ) );
    }

    public static function add_product_class( $classes ) {
        $classes[] = 'product';
        return apply_filters( 'berocket_aapf_add_product_class', $classes );
    }

    public static function pagination_args( $args = array() ) {
        $args['base'] = str_replace( 999999999, '%#%', self::get_pagenum_link( 999999999 ) );
        return $args;
    }

    // 99% copy of WordPress' get_pagenum_link.
    public static function get_pagenum_link( $pagenum = 1, $escape = true ) {
        global $wp_rewrite;

        $pagenum = (int) $pagenum;

        $request = remove_query_arg( 'paged', preg_replace( "~".home_url()."~", "", (isset($_POST['location']) ? $_POST['location'] : '') ) );

        $home_root = parse_url( home_url() );
        $home_root = ( isset( $home_root['path'] ) ) ? $home_root['path'] : '';
        $home_root = preg_quote( $home_root, '|' );

        $request = preg_replace( '|^' . $home_root . '|i', '', $request );
        $request = preg_replace( '|^/+|', '', $request );

        $is_using_permalinks = $wp_rewrite->using_permalinks();
        if ( ! $is_using_permalinks ) {
            $base = trailingslashit( get_bloginfo( 'url' ) );

            if ( $pagenum > 1 ) {
                $result = add_query_arg( 'paged', $pagenum, $base . $request );
            } else {
                $result = $base . $request;
            }
        } else {
            $qs_regex = '|\?.*?$|';
            preg_match( $qs_regex, $request, $qs_match );

            if ( ! empty( $qs_match[0] ) ) {
                $query_string = $qs_match[0];
                $request      = preg_replace( $qs_regex, '', $request );
            } else {
                $query_string = '';
            }

            $request = preg_replace( "|$wp_rewrite->pagination_base/\d+/?$|", '', $request );
            $request = preg_replace( '|^' . preg_quote( $wp_rewrite->index, '|' ) . '|i', '', $request );
            $request = ltrim( $request, '/' );

            $base = trailingslashit( get_bloginfo( 'url' ) );

            $is_using_index_permalinks = $wp_rewrite->using_index_permalinks();
            if ( $is_using_index_permalinks && ( $pagenum > 1 || '' != $request ) )
                $base .= $wp_rewrite->index . '/';

            if ( $pagenum > 1 ) {
                $request = ( ( !empty( $request ) ) ? trailingslashit( $request ) : $request ) . user_trailingslashit( $wp_rewrite->pagination_base . "/" . $pagenum, 'paged' );
            }

            $result = $base . $request . $query_string;
        }

        /**
         * Filter the page number link for the current request.
         *
         * @since 2.5.0
         *
         * @param string $result The page number link.
         */
        $result = apply_filters( 'get_pagenum_link', $result );

        if ( $escape )
            return esc_url( $result );
        else
            return esc_url_raw( $result );
    }

    public static function get_terms_child_parent ( $child_parent, $attribute, $current_terms = FALSE, $child_parent_depth = 1 ) {
        if ( isset($child_parent) && $child_parent == 'parent' ) {
            $args_terms = array(
                'orderby'    => 'id',
                'order'      => 'ASC',
                'hide_empty' => false,
                'parent'     => 0,
            );
            if( $attribute == 'product_cat' ) {
                $current_terms = br_get_taxonomy_hierarchy(array(), 0, 1);
            } else {
                $current_terms = get_terms( $attribute, $args_terms );
            }
        }
        if ( isset($child_parent) && $child_parent == 'child' ) {
            $current_terms = array( (object) array( 'depth' => 0, 'child' => 0, 'term_id' => 'R__term_id__R', 'count' => 'R__count__R', 'slug' => 'R__slug__R', 'name' => 'R__name__R', 'taxonomy' => 'R__taxonomy__R' ) );
            $selected_terms = br_get_selected_term( $attribute );
            $selected_terms_id = array();
            if( empty($child_parent_depth) ) {
                $child_parent_depth = 0;
            }
            foreach( $selected_terms as $selected_term ) {
                $ancestors = get_ancestors( $selected_term, $attribute );
                if( count( $ancestors ) >= ( $child_parent_depth - 1 ) ) {
                    if( count( $ancestors ) > ( $child_parent_depth - 1 ) ) {
                        $selected_term = $ancestors[count( $ancestors ) - ( $child_parent_depth )];
                    }
                    if ( ! in_array( $selected_term, $selected_terms_id ) ) {
                        $args_terms = array(
                            'orderby'    => 'id',
                            'order'      => 'ASC',
                            'hide_empty' => false,
                            'parent'     => $selected_term,
                        );
                        $selected_terms_id[] = $selected_term;
                        $additional_terms = get_terms( $attribute, $args_terms );
                        $current_terms = array_merge( $current_terms, $additional_terms );
                    }
                }
            }
        }
        return $current_terms;
    }

    public static function is_parent_selected($attribute, $child_parent_depth = 1) {
        $selected_terms = br_get_selected_term( $attribute );
        $selected_terms_id = array();
        foreach( $selected_terms as $selected_term ) {
            if( empty($child_parent_depth) ) {
                $child_parent_depth = 0;
            }
            $ancestors = get_ancestors( $selected_term, $attribute );
            if( count( $ancestors ) > ( $child_parent_depth - 1 ) ) {
                return true;
            }
        }
        return false;
    }

    public static function old_wc_compatible( $query, $new = false ) {
        return br_filters_old_wc_compatible( $query, $new );
    }
}
