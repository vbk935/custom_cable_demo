<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'AWS_Markup' ) ) :

    /**
     * Class for plugin search action
     */
    class AWS_Markup {

        /*
         * Generate search box markup
         */
        public function markup() {

            global $wpdb;

            $table_name = $wpdb->prefix . AWS_INDEX_TABLE_NAME;

            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) {
                echo 'Please go to <a href="' . admin_url( 'admin.php?page=aws-options' ) . '">plugins settings page</a> and click on "Reindex table" button.';
                return;
            }


            $placeholder   = AWS_Helpers::translate( 'search_field_text', AWS()->get_settings( 'search_field_text' ) );
            $min_chars     = AWS()->get_settings( 'min_chars' );
            $show_loader   = AWS()->get_settings( 'show_loader' );
            $show_more     = AWS()->get_settings( 'show_more' );
            $show_page     = AWS()->get_settings( 'show_page' );
            $show_clear    = AWS()->get_settings( 'show_clear' );
            $use_analytics = AWS()->get_settings( 'use_analytics' );
            $buttons_order = AWS()->get_settings( 'buttons_order' );

            $current_lang = AWS_Helpers::get_lang();

            $url_array = parse_url( home_url() );
            $url_query_parts = array();

            if ( isset( $url_array['query'] ) && $url_array['query'] ) {
                parse_str( $url_array['query'], $url_query_parts );
            }

            $form_action = home_url( '/' );
            if ( function_exists( 'pll_home_url' ) ) {
                $form_action = pll_home_url();
            }

            $params_string = '';

            $params = array(
                'data-url'           => admin_url('admin-ajax.php'),
                'data-siteurl'       => home_url(),
                'data-lang'          => $current_lang ? $current_lang : '',
                'data-show-loader'   => $show_loader,
                'data-show-more'     => $show_more,
                'data-show-page'     => $show_page,
                'data-show-clear'    => $show_clear,
                'data-use-analytics' => $use_analytics,
                'data-min-chars'     => $min_chars,
                'data-buttons-order' => $buttons_order,
                'data-is-mobile'     => wp_is_mobile() ? 'true' : 'false',
                'data-page-id'       => get_queried_object_id(),
                'data-tax'           => get_query_var('taxonomy')
            );


            /**
             * Filter form data parameters before output
             * @since 1.69
             * @param array $params Data parameters array
             */
            $params = apply_filters( 'aws_front_data_parameters', $params );


            foreach( $params as $key => $value ) {
                $params_string .= $key . '="' . esc_attr( $value ) . '" ';
            }

            $markup = '';
            $markup .= '<div class="aws-container" ' . $params_string . '>';
            $markup .= '<form class="aws-search-form" action="' . $form_action . '" method="get" role="search" >';

            $markup .= '<div class="aws-wrapper">';

                $markup .= '<input  type="search" name="s" value="' . get_search_query() . '" class="aws-search-field" placeholder="' . esc_attr( $placeholder ) . '" autocomplete="off" />';
                $markup .= '<input type="hidden" name="post_type" value="product">';
                $markup .= '<input type="hidden" name="type_aws" value="true">';

                if ( $current_lang ) {
                    $markup .= '<input type="hidden" name="lang" value="' . esc_attr( $current_lang ) . '">';
                }

                if ( $url_query_parts ) {
                    foreach( $url_query_parts as $url_query_key => $url_query_value  ) {
                        $markup .= '<input type="hidden" name="' . esc_attr( $url_query_key ) . '" value="' . esc_attr( $url_query_value ) . '">';
                    }
                }

                $markup .= '<div class="aws-search-clear">';
                    $markup .= '<span aria-label="Clear Search">×</span>';
                $markup .= '</div>';

                $markup .= '<div class="aws-loader"></div>';

            $markup .= '</div>';

            if ( $buttons_order && $buttons_order !== '1' ) {

                $markup .= '<div class="aws-search-btn aws-form-btn">';
                    $markup .= '<span class="aws-search-btn_icon">';
                        $markup .= '<svg focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24px">';
                            $markup .= '<path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"></path>';
                        $markup .= '</svg>';
                    $markup .= '</span>';
                $markup .= '</div>';

            }

            $markup .= '</form>';
            $markup .= '</div>';

            return apply_filters( 'aws_searchbox_markup', $markup );

        }

    }

endif;