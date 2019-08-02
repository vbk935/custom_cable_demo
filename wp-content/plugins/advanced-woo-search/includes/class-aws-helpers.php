<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


if ( ! class_exists( 'AWS_Helpers' ) ) :

    /**
     * Class for plugin help methods
     */
    class AWS_Helpers {

        /*
         * Removes scripts, styles, html tags
         */
        static public function html2txt( $str ) {
            $search = array(
                '@<script[^>]*?>.*?</script>@si',
                '@<[\/\!]*?[^<>]*?>@si',
                '@<style[^>]*?>.*?</style>@siU',
                '@<![\s\S]*?--[ \t\n\r]*>@'
            );
            $str = preg_replace( $search, '', $str );

            $str = esc_attr( $str );
            $str = stripslashes( $str );
            $str = str_replace( array( "\r", "\n" ), ' ', $str );

            $str = str_replace( array(
                "Â·",
                "â€¦",
                "â‚¬",
                "&shy;"
            ), "", $str );

            return $str;
        }

        /*
         * Check if index table exist
         */
        static public function is_table_not_exist() {

            global $wpdb;

            $table_name = $wpdb->prefix . AWS_INDEX_TABLE_NAME;

            return ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name );

        }

        /*
         * Get amount of indexed products
         */
        static public function get_indexed_products_count() {

            global $wpdb;

            $table_name = $wpdb->prefix . AWS_INDEX_TABLE_NAME;

            $indexed_products = 0;

            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) === $table_name ) {

                $sql = "SELECT COUNT(*) FROM {$table_name} GROUP BY ID;";

                $indexed_products = $wpdb->query( $sql );

            }

            return $indexed_products;

        }

        /*
         * Check if index table has new terms columns
         */
        static public function is_index_table_has_terms() {

            global $wpdb;

            $table_name =  $wpdb->prefix . AWS_INDEX_TABLE_NAME;

            $return = false;

            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) === $table_name ) {

                $columns = $wpdb->get_row("
                    SELECT * FROM {$table_name} LIMIT 0, 1
                ", ARRAY_A );

                if ( $columns && ! isset( $columns['term_id'] ) ) {
                    $return = 'no_terms';
                } else {
                    $return = 'has_terms';
                }

            }

            return $return;

        }

        /*
         * Check if index table has new on_sale columns
         */
        static public function is_index_table_has_on_sale() {

            global $wpdb;

            $table_name =  $wpdb->prefix . AWS_INDEX_TABLE_NAME;

            $return = false;

            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) === $table_name ) {

                $columns = $wpdb->get_row("
                    SELECT * FROM {$table_name} LIMIT 0, 1
                ", ARRAY_A );

                if ( $columns && ! isset( $columns['on_sale'] ) ) {
                    $return = 'no';
                } else {
                    $return = 'has';
                }

            }

            return $return;

        }
        
        /*
         * Get special characters that must be striped
         */
        static public function get_special_chars() {
            
            $chars = array(
                '&#33;', //exclamation point
                '&#34;', //double quotes
                '&quot;', //double quotes
                '&#35;', //number sign
                '&#36;', //dollar sign
                '&#37;', //percent sign
                '&#38;', //ampersand
                '&amp;', //ampersand
                '&#39;', //single quote
                '&#039;', //single quote
                '&#40;', //opening parenthesis
                '&#41;', //closing parenthesis
                '&#42;', //asterisk
                '&#43;', //plus sign
                '&#44;', //comma
                '&#45;', //minus sign - hyphen
                '&#46;', //period
                '&#47;', //slash
                '&#58;', //colon
                '&#59;', //semicolon
                '&#60;', //less than sign
                '&lt;', //less than sign
                '&#61;', //equal sign
                '&#62;', //greater than sign
                '&gt;', //greater than sign
                '&#63;', //question mark
                '&#64;', //at symbol
                '&#91;', //opening bracket
                '&#92;', //backslash
                '&#93;', //closing bracket
                '&#94;', //caret - circumflex
                '&#95;', //underscore
                '&#96;', //grave accent
                '&#123;', //opening brace
                '&#124;', //vertical bar
                '&#125;', //closing brace
                '&#126;', //equivalency sign - tilde
                '&#161;', //inverted exclamation mark
                '&iexcl;', //inverted exclamation mark
                '&#162;', //cent sign
                '&cent;', //cent sign
                '&#163;', //pound sign
                '&pound;', //pound sign
                '&#164;', //currency sign
                '&curren;', //currency sign
                '&#165;', //yen sign
                '&yen;', //yen sign
                '&#166;', //broken vertical bar
                '&brvbar;', //broken vertical bar
                '&#167;', //section sign
                '&sect;', //section sign
                '&#168;', //spacing diaeresis - umlaut
                '&uml;', //spacing diaeresis - umlaut
                '&#169;', //copyright sign
                '&copy;', //copyright sign
                '&#170;', //feminine ordinal indicator
                '&ordf;', //feminine ordinal indicator
                '&#171;', //left double angle quotes
                '&laquo;', //left double angle quotes
                '&#172;', //not sign
                '&not;', //not sign
                '&#174;', //registered trade mark sign
                '&reg;', //registered trade mark sign
                '&#175;', //spacing macron - overline
                '&macr;', //spacing macron - overline
                '&#176;', //degree sign
                '&deg;', //degree sign
                '&#177;', //plus-or-minus sign
                '&plusmn;', //plus-or-minus sign
                '&#178;', //superscript two - squared
                '&sup2;', //superscript two - squared
                '&#179;', //superscript three - cubed
                '&sup3;', //superscript three - cubed
                '&#180;', //acute accent - spacing acute
                '&acute;', //acute accent - spacing acute
                '&#181;', //micro sign
                '&micro;', //micro sign
                '&#182;', //pilcrow sign - paragraph sign
                '&para;', //pilcrow sign - paragraph sign
                '&#183;', //middle dot - Georgian comma
                '&middot;', //middle dot - Georgian comma
                '&#184;', //spacing cedilla
                '&cedil;', //spacing cedilla
                '&#185;', //superscript one
                '&sup1;', //superscript one
                '&#186;', //masculine ordinal indicator
                '&ordm;', //masculine ordinal indicator
                '&#187;', //right double angle quotes
                '&raquo;', //right double angle quotes
                '&#188;', //fraction one quarter
                '&frac14;', //fraction one quarter
                '&#189;', //fraction one half
                '&frac12;', //fraction one half
                '&#190;', //fraction three quarters
                '&frac34;', //fraction three quarters
                '&#191;', //inverted question mark
                '&iquest;', //inverted question mark
                '&#247;', //division sign
                '&divide;', //division sign
                '&#8211;', //en dash
                '&#8212;', //em dash
                '&#8216;', //left single quotation mark
                '&#8217;', //right single quotation mark
                '&#8218;', //single low-9 quotation mark
                '&#8220;', //left double quotation mark
                '&#8221;', //right double quotation mark
                '&#8222;', //double low-9 quotation mark
                '&#8224;', //dagger
                '&#8225;', //double dagger
                '&#8226;', //bullet
                '&#8230;', //horizontal ellipsis
                '&#8240;', //per thousand sign
                '&#8364;', //euro sign
                '&euro;', //euro sign
                '&#8482;', //trade mark sign
                '!', //exclamation point
                '"', //double quotes
                '#', //number sign
                '$', //dollar sign
                '%', //percent sign
                '&', //ampersand
                "'", //single quote
                '(', //opening parenthesis
                ')', //closing parenthesis
                '*', //asterisk
                '+', //plus sign
                ",", //comma
                '-', //minus sign - hyphen
                ".", //period
                "/", //slash
                ':', //colon
                ';', //semicolon
                "<", //less than sign
                "=", //equal sign
                ">", //greater than sign
                '?', //question mark
                '@', //at symbol
                "[", //opening bracket
                '\\', //backslash
                "]", //closing bracket
                '^', //caret - circumflex
                '_', //underscore
                '`', //grave accent
                "{", //opening brace
                '|', //vertical bar
                "}", //closing brace
                '~', //equivalency sign - tilde
                '¡', //inverted exclamation mark
                '¢', //cent sign
                '£', //pound sign
                '¤', //currency sign
                '¥', //yen sign
                '¦', //broken vertical bar
                '§', //section sign
                '¨', //spacing diaeresis - umlaut
                '©', //copyright sign
                'ª', //feminine ordinal indicator
                '«', //left double angle quotes
                '¬', //not sign
                '®', //registered trade mark sign
                '¯', //spacing macron - overline
                '°', //degree sign
                '±', //plus-or-minus sign
                '²', //superscript two - squared
                '³', //superscript three - cubed
                '´', //acute accent - spacing acute
                'µ', //micro sign
                '¶', //pilcrow sign - paragraph sign
                '·', //middle dot - Georgian comma
                '¸', //spacing cedilla
                '¹', //superscript one
                'º', //masculine ordinal indicator
                '»', //right double angle quotes
                '¼', //fraction one quarter
                '½', //fraction one half
                '¾', //fraction three quarters
                '¿', //inverted question mark
                '÷', //division sign
                '–', //en dash
                '—', //em dash
                '‘', //left single quotation mark
                "’", //right single quotation mark
                '‚', //single low-9 quotation mark
                "“", //left double quotation mark
                "”", //right double quotation mark
                '„', //double low-9 quotation mark
                '†', //dagger
                '‡', //double dagger
                '•', //bullet
                '…', //horizontal ellipsis
                '‰', //per thousand sign
                '€', //euro sign
                '™', //trade mark sign
            );
            
            return apply_filters( 'aws_special_chars', $chars );
            
        }

        /*
         * Get diacritical marks
         */
        static public function get_diacritic_chars() {

            $chars = array(
                'Š'=>'S',
                'š'=>'s',
                'Ž'=>'Z',
                'ž'=>'z',
                'À'=>'A',
                'Á'=>'A',
                'Â'=>'A',
                'Ã'=>'A',
                'Ä'=>'A',
                'Å'=>'A',
                'Æ'=>'A',
                'Ç'=>'C',
                'È'=>'E',
                'É'=>'E',
                'Ê'=>'E',
                'Ë'=>'E',
                'Ì'=>'I',
                'Í'=>'I',
                'Î'=>'I',
                'Ï'=>'I',
                'Ñ'=>'N',
                'Ò'=>'O',
                'Ó'=>'O',
                'Ô'=>'O',
                'Õ'=>'O',
                'Ö'=>'O',
                'Ø'=>'O',
                'Ù'=>'U',
                'Ú'=>'U',
                'Û'=>'U',
                'Ü'=>'U',
                'Ý'=>'Y',
                'à'=>'a',
                'á'=>'a',
                'â'=>'a',
                'ã'=>'a',
                'ä'=>'a',
                'å'=>'a',
                'ç'=>'c',
                'è'=>'e',
                'é'=>'e',
                'ê'=>'e',
                'ë'=>'e',
                'ì'=>'i',
                'í'=>'i',
                'î'=>'i',
                'ï'=>'i',
                'ð'=>'o',
                'ñ'=>'n',
                'ò'=>'o',
                'ó'=>'o',
                'ô'=>'o',
                'õ'=>'o',
                'ö'=>'o',
                'ø'=>'o',
                'ù'=>'u',
                'ú'=>'u',
                'û'=>'u',
                'ý'=>'y',
                'þ'=>'b',
                'ÿ'=>'y',
            );

            /**
             * Filters array of diacritic chars
             *
             * @since 1.52
             */
            return apply_filters( 'aws_diacritic_chars', $chars );

        }

        /*
         * Normalize string
         */
        static public function normalize_string( $string ) {

            $special_chars = AWS_Helpers::get_special_chars();

            $string = AWS_Helpers::html2txt( $string );
            $string = str_replace( $special_chars, '', $string );
            $string = str_replace( array( '&#160;', '&nbsp;' ), ' ', $string );
            $string = trim( $string );

            //$str = preg_replace( '/[[:punct:]]+/u', ' ', $str );
            $string = preg_replace( '/[[:space:]]+/', ' ', $string );

            // Most objects except unicode characters
            $string = preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F\x80-\x9F]/u', '', $string );

            // Line feeds, carriage returns, tabs
            $string = preg_replace( '/[\x00-\x1F\x80-\x9F]/u', '', $string );

            // Diacritical marks
            $string = strtr( $string, AWS_Helpers::get_diacritic_chars() );

            if ( function_exists( 'mb_strtolower' ) ) {
                $string = mb_strtolower( $string );
            } else {
                $string = strtolower( $string );
            }

            /**
             * Filters normalized string
             *
             * @since 1.52
             */
            return apply_filters( 'aws_normalize_string', $string );

        }

        /*
         * Replace stopwords
         */
        static public function filter_stopwords( $str_array ) {

            $stopwords = AWS()->get_settings( 'stopwords' );
            $stopwords_array = array();
            $new_str_array = array();

            if ( $stopwords ) {
                $stopwords_array = explode( ',', $stopwords );
            }

            if ( $str_array && is_array( $str_array ) && ! empty( $str_array ) && $stopwords_array && ! empty( $stopwords_array ) ) {

                $stopwords_array = array_map( 'trim', $stopwords_array );

                foreach ( $str_array as $str_word ) {
                    if ( in_array( $str_word, $stopwords_array ) ) {
                        continue;
                    }
                    $new_str_array[] = $str_word;
                }

            } else {
                $new_str_array = $str_array;
            }

            return $new_str_array;

        }

        /*
         * Strip shortcodes
         */
        static public function strip_shortcodes( $str ) {
            $str = preg_replace( '#\[[^\]]+\]#', '', $str );
            return $str;
        }

        /*
         * Get index table specific source name from taxonomy name
         *
         * @return string Source name
         */
        static public function get_source_name( $taxonomy ) {

            switch ( $taxonomy ) {

                case 'product_cat':
                    $source_name = 'category';
                    break;

                case 'product_tag':
                    $source_name = 'tag';
                    break;

                default:
                    $source_name = '';

            }

            return $source_name;

        }

        /*
         * Registers the WPML translations
         *
         */
        static public function register_wpml_translations( $params = false ) {

            // No WPML
            if ( ! function_exists( 'icl_register_string' ) ) {
                return;
            }

            // These options are registered
            $options_to_reg = array(
                "search_field_text" => "Search",
                "not_found_text"    => "Nothing found",
                "show_more_text"    => "View all results",
            );

            if ( ! $params ) {
                $params = $options_to_reg;
            }

            foreach ( $options_to_reg as $key => $option ) {
                icl_register_string( 'aws', $key, $params[$key] );
            }

        }

        /*
         * Wrapper for WPML print
         *
         * @return string Source name
         */
        static public function translate( $name, $value ) {

            if ( function_exists( 'icl_t' ) ) {
                return icl_t( 'aws', $name, $value );
            }

            return $value;

        }

        /*
         * Get current active site language
         *
         * @return string Language code
         */
        static public function get_lang() {

            $current_lang = false;

            if ( ( defined( 'ICL_SITEPRESS_VERSION' ) || function_exists( 'pll_current_language' ) ) ) {

                if ( has_filter('wpml_current_language') ) {
                    $current_lang = apply_filters( 'wpml_current_language', NULL );
                } elseif ( function_exists( 'pll_current_language' ) ) {
                    $current_lang = pll_current_language();
                }

            } elseif( function_exists( 'qtranxf_getLanguage' ) ) {

                $current_lang = qtranxf_getLanguage();

            }

            return $current_lang;

        }

        /*
         * Get string with current product terms names
         *
         * @return string List of terms names
         */
        static public function get_terms_array( $id, $taxonomy ) {

            $terms = wp_get_object_terms( $id, $taxonomy );

            if ( is_wp_error( $terms ) ) {
                return '';
            }

            if ( empty( $terms ) ) {
                return '';
            }

            $tax_array_temp = array();
            $source_name = AWS_Helpers::get_source_name( $taxonomy );

            foreach ( $terms as $term ) {
                $source = $source_name . '%' . $term->term_id . '%';
                $tax_array_temp[$source] = $term->name;
            }

            return $tax_array_temp;

        }

    }

endif;