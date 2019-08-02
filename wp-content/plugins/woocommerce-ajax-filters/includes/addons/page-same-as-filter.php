<?php
if( ! class_exists('BeRocket_AAPF_addon_page_same_as_filter') ) {
    class BeRocket_AAPF_addon_page_same_as_filter {
        function __construct($variant) {
            if( $variant == 'remove' ) {
                add_filter('berocket_aapf_widget_include_exclude_items', array($this, 'remove'), 100, 2);
            } elseif( $variant == 'leave' ) {
                add_filter('berocket_aapf_widget_include_exclude_items', array($this, 'leave'), 100, 2);
                add_filter('berocket_widget_load_template_name', array($this, 'leave_replace_template'), 10, 3);
            }
        }
        function remove($terms, $instance) {
            if(get_queried_object_id() != 0 && ! empty($terms) && count($terms) ) {
                $queried_object = get_queried_object();
                $terms = array_values($terms);
                if( $terms[0]->taxonomy == $queried_object->taxonomy ) {
                    foreach($terms as $term_i => $term) {
                        if( $term->term_id == $queried_object->term_id ) {
                            unset($terms[$term_i]);
                            break;
                        }
                    }
                    $terms = array_values($terms);
                }
            }
            return $terms;
        }
        function leave($terms, $instance) {
            if(get_queried_object_id() != 0 && ! empty($terms) && count($terms) ) {
                $queried_object = get_queried_object();
                $terms = array_values($terms);
                if( $terms[0]->taxonomy == $queried_object->taxonomy ) {
                    foreach($terms as $term_i => $term) {
                        if( $term->term_id != $queried_object->term_id ) {
                            unset($terms[$term_i]);
                        }
                    }
                    $terms = array_values($terms);
                }
            }
            return $terms;
        }
        function leave_replace_template($type, $instance, $terms) {
            if(get_queried_object_id() != 0 && ! empty($terms) && count($terms) ) {
                $queried_object = get_queried_object();
                $terms = array_values($terms);
                if( $terms[0]->taxonomy == $queried_object->taxonomy ) {
                    $type = 'disabled/'.$type;
                }
            }
            return $type;
        }
    }
}
