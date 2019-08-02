<?php
class BeRocket_new_AAPF_Widget extends WP_Widget 
{
    public function __construct() {
        parent::__construct("berocket_aapf_group", "AAPF Filters Group",
            array("description" => "AJAX Product Filters Group"));
    }
    public function widget($args, $instance) {
        if( ! self::check_widget_by_instance($instance) ) {
            return false;
        }
        $current_language = apply_filters( 'wpml_current_language', NULL );
        $instance['group_id'] = apply_filters( 'wpml_object_id', $instance['group_id'], 'page', true, $current_language );
        $BeRocket_AAPF_group_filters = BeRocket_AAPF_group_filters::getInstance();
        $filters = $BeRocket_AAPF_group_filters->get_option($instance['group_id']);
        global $wp_registered_sidebars;
        $is_shortcode = empty($args['id']) || ! isset($wp_registered_sidebars[$args['id']]);
        $new_args = $args;
        if( ! $is_shortcode ) {
            $sidebar = $wp_registered_sidebars[$args['id']];
            $new_args = array_merge($new_args, $sidebar);
            $before_widget = $new_args['before_widget'];
        }
        $custom_class = trim(br_get_value_from_array($filters, 'custom_class'));
        $custom_class_instance = trim(br_get_value_from_array($instance, 'custom_class'));
        $custom_class = $custom_class . ' ' . $custom_class_instance;
        $new_args['custom_class'] = $custom_class;
        $i = 1;
        ob_start();
        $custom_vars = array();
        $custom_vars = apply_filters('berocket_aapf_group_before_all', $custom_vars, $filters);
        $new_args = apply_filters('berocket_aapf_group_new_args', $new_args, $filters, $custom_vars);
        foreach($filters['filters'] as $filter) {
            $new_args_filter = apply_filters('berocket_aapf_group_new_args_filter', $new_args, $filters, $filter, $custom_vars);
            if( $is_shortcode ) {
                if( isset($new_args_filter['before_widget']) ) {
                    unset($new_args_filter['before_widget']);
                }
                if( isset($new_args_filter['after_widget']) ) {
                    unset($new_args_filter['after_widget']);
                }
            } else {
                $new_args_filter['widget_id'] = $args['widget_id'].'-'.$i;
                $new_args_filter['before_widget'] = sprintf($before_widget, $new_args_filter['widget_id'], '%s');
            }
            $custom_vars = apply_filters('berocket_aapf_group_before_filter', $custom_vars, $filters);
            the_widget( 'BeRocket_new_AAPF_Widget_single', array('filter_id' => $filter), $new_args_filter);
            $custom_vars = apply_filters('berocket_aapf_group_after_filter', $custom_vars, $filters);
            $i++;
        }
        $custom_vars = apply_filters('berocket_aapf_group_after_all', $custom_vars, $filters);
        $widget_html = ob_get_clean();
        if( ! empty($widget_html) ) {
            if( ! empty($instance['title']) ) {
                echo '<h3 class="berocket_ajax_group_filter_title">' . $instance['title'] . '</h3>';
            }
            echo $widget_html;
        } else {
            return false;
        }
    }
    public static function check_widget_by_instance($instance) {
        if( empty($instance['group_id']) || get_post_status($instance['group_id']) != 'publish' ) {
            return false;
        }
        $current_language = apply_filters( 'wpml_current_language', NULL );
        $instance['group_id'] = apply_filters( 'wpml_object_id', $instance['group_id'], 'page', true, $current_language );
        $BeRocket_AAPF_group_filters = BeRocket_AAPF_group_filters::getInstance();
        $filters = $BeRocket_AAPF_group_filters->get_option($instance['group_id']);
        if( empty($filters) ) {
            return false;
        }
        if( ! empty($filters['data']) && ! BeRocket_conditions::check($filters['data'], $BeRocket_AAPF_group_filters->hook_name) ) {
            return false;
        }
        if( empty($filters['filters']) || ! is_array($filters['filters']) || ! count($filters['filters']) ) {
            return false;
        }
        return true;
    }
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['group_id'] = strip_tags( @ $new_instance['group_id'] );
        $instance['title'] = strip_tags( @ $new_instance['title'] );
        $instance['custom_class'] = strip_tags( @ $new_instance['custom_class'] );
        return $instance;
    }
    public function form($instance) {
        wp_enqueue_script('jquery-color');
        $instance = wp_parse_args( (array) $instance, array( 'group_id' => '', 'title' => '', 'custom_class' => '') );
        echo '<a href="' . admin_url('edit.php?post_type=br_filters_group') . '">' . __('Manage groups', 'BeRocket_AJAX_domain') . '</a>';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?></label>
            <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('custom_class'); ?>"><?php _e('Custom CSS class', 'BeRocket_AJAX_domain'); ?></label>
            <input type="text" id="<?php echo $this->get_field_id('custom_class'); ?>" name="<?php echo $this->get_field_name('custom_class'); ?>" value="<?php echo $instance['custom_class']; ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('group_id'); ?>"><?php _e('Group', 'BeRocket_AJAX_domain'); ?></label><br>
            <?php
            $query = new WP_Query(array('post_type' => 'br_filters_group', 'nopaging' => true));
            $edit_link_current = '';
            if ( $query->have_posts() ) {
                echo '<select class="berocket_new_widget_selectbox group" id="'.$this->get_field_id('group_id').'" name="'.$this->get_field_name('group_id').'">';
                echo '<option>'.__('--Please select group--', 'BeRocket_AJAX_domain').'</option>';
                while ( $query->have_posts() ) {
                    if( empty($instance['group_id']) ) {
                        $instance['group_id'] = get_the_id();
                    }
                    $query->the_post();
                    echo '<option data-edit="'.get_edit_post_link().'" value="' . get_the_id() . '"'.(get_the_id() == $instance['group_id'] ? ' selected' : '').'>' . substr(get_the_title(), 0, 50) . (strlen(get_the_title()) > 50 ? '...' : '') . ' (ID:' . get_the_id() . ')</option>';
                    if( get_the_id() == $instance['group_id'] ) {
                        $edit_link_current = get_edit_post_link();
                    }
                }
                echo '</select>';
                wp_reset_postdata();
            }
            ?>
            <a target="_blank" class="berocket_aapf_edit_post_link" href="<?php echo $edit_link_current; ?>"<?php if( empty($edit_link_current) ) echo ' style="display: none;"'; ?>><?php _e('Edit', 'BeRocket_AJAX_domain'); ?></a>
        </p>
        <a class="button berocket_create_new" data-type="group" data-action="berocket_aapf_load_simple_filter_creation" href="#"><?php _e('Create Group', 'BeRocket_AJAX_domain'); ?></a>
        <?php
    }
}
class BeRocket_new_AAPF_Widget_single extends WP_Widget 
{
    public function __construct() {
        parent::__construct("berocket_aapf_single", "AAPF Filter Single",
            array("description" => "AJAX Product Filters Single"));
    }
    public function widget($args, $instance) {
        if( ! self::check_widget_by_instance($instance) ) {
            return true;
        }
        $current_language = apply_filters( 'wpml_current_language', NULL );
        $instance['filter_id'] = apply_filters( 'wpml_object_id', $instance['filter_id'], 'page', true, $current_language );
        $filter_id = $instance['filter_id'];
        $filter_post = get_post($filter_id);
        $BeRocket_AAPF_single_filter = BeRocket_AAPF_single_filter::getInstance();
        $filter_data = $BeRocket_AAPF_single_filter->get_option($filter_id);
        if( empty($filter_data) || ! is_array($filter_data) ) {
            $filter_data = array();
        }
        if( ! empty($args['filter_data']) && is_array($args['filter_data']) ) {
            $filter_data = array_merge($filter_data, $args['filter_data']);
        }
        $custom_class = trim(br_get_value_from_array($filter_data, 'custom_class'));
        $custom_class_args = trim(br_get_value_from_array($args, 'custom_class'));
        $custom_class = $custom_class . ' ' . $custom_class_args;
        $custom_class_instance = trim(br_get_value_from_array($instance, 'custom_class'));
        $custom_class = $custom_class . ' ' . $custom_class_instance;
        $custom_class = trim($custom_class);
        if ( empty($instance['br_wp_footer']) ) {
            global $br_widget_ids;
            if ( ! isset( $br_widget_ids ) ) {
                $br_widget_ids = array();
            }
            $instance['is_new_widget'] = true;
            $br_widget_ids[] = array('instance' => $instance, 'args' => $args);
        }
        $filter_data['br_wp_footer'] = true;
        $filter_data['show_page'] = array();
        $filter_data['title'] = $filter_post->post_title;
        $additional_class = br_get_value_from_array($args, 'additional_class');
        if( ! is_array($additional_class) ) {
            $additional_class = array();
        }
        if( ! empty($filter_data['is_hide_mobile']) ) {
            $additional_class[] = 'berocket_hide_single_widget_on_mobile';
        }
        if( ! empty($filter_data['hide_group']['tablet']) ) {
            $additional_class[] = 'berocket_hide_single_widget_on_tablet';
        }
        if( ! empty($filter_data['hide_group']['desktop']) ) {
            $additional_class[] = 'berocket_hide_single_widget_on_desktop';
        }
        if( ! empty($filter_data['reset_hide']) && $filter_data['widget_type'] == 'reset_button' ) {
            $additional_class[] = $filter_data['reset_hide'];
        }
        $additional_class[] = 'berocket_single_filter_widget';
        $additional_class[] = 'berocket_single_filter_widget_' . esc_html($instance['filter_id']);
        $additional_class[] = $custom_class;
        $additional_class = array_unique($additional_class);
        if( ! empty($filter_data['widget_type']) && ($filter_data['widget_type'] == 'update_button' || $filter_data['widget_type'] == 'reset_button' ) ) {
            $search_berocket_hidden_clickable = array_search('berocket_hidden_clickable', $additional_class);
            if( $search_berocket_hidden_clickable !== FALSE ) {
                unset($additional_class[$search_berocket_hidden_clickable]);
            }
            $additional_class_esc = implode(' ', $additional_class);
            $additional_class_esc = esc_html($additional_class_esc);
            echo '<div class="' . $additional_class_esc . '" data-id="' . esc_html($instance['filter_id']) . '">';
        } else {
            $additional_class_esc = implode(' ', $additional_class);
            $additional_class_esc = esc_html($additional_class_esc);
            if( ! empty($args['widget_inline_style']) ) {
                $classes_arr = $additional_class_esc;
                $classes_arr = explode(' ', preg_replace('!\s+!', ' ', $classes_arr));
                $classes_arr = '.' . implode('.', $classes_arr);
                $classes_arr .= ' .berocket_aapf_widget';
                $classes_arr = esc_html($classes_arr);
                echo '<style>';
                echo $classes_arr;
                echo '{' . htmlentities($args['widget_inline_style'], ENT_HTML5) . '}';
                echo '</style>';
            }
            echo '<div class="' . $additional_class_esc . '" data-id="' . esc_html($instance['filter_id']) . '" style="'.htmlentities(br_get_value_from_array($args, 'inline_style')).'">';
        }
        if( apply_filters('BeRocket_AAPF_widget_old_display_conditions', true, $filter_data, $instance, $args) ) {
            the_widget( 'BeRocket_AAPF_widget', $filter_data, $args);
        }
        echo '</div>';
    }
    public static function check_widget_by_instance($instance) {
        if( empty($instance['filter_id']) || get_post_status($instance['filter_id']) != 'publish' ) {
            return false;
        }
        $current_language = apply_filters( 'wpml_current_language', NULL );
        $instance['filter_id'] = apply_filters( 'wpml_object_id', $instance['filter_id'], 'page', true, $current_language );
        $filter_id = $instance['filter_id'];
        $filter_post = get_post($filter_id);
        $BeRocket_AAPF_single_filter = BeRocket_AAPF_single_filter::getInstance();
        $filter_data = $BeRocket_AAPF_single_filter->get_option($filter_id);
        if( ! empty($filter_data['data']) && ! BeRocket_conditions::check($filter_data['data'], $BeRocket_AAPF_single_filter->hook_name) ) {
            return false;
        }
        if( empty($filter_data) || empty($filter_post) ) {
            return false;
        }
        return true;
    }
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['filter_id'] = strip_tags( $new_instance['filter_id'] );
        $instance['filter_id'] = intval($instance['filter_id']);
        $instance['custom_class'] = strip_tags( $new_instance['custom_class'] );
        $instance['custom_class'] = sanitize_text_field($instance['custom_class']);
        return $instance;
    }
    public function form($instance) {
        $instance = wp_parse_args( (array) $instance, array( 'filter_id' => '', 'custom_class' => '') );
        echo '<a href="' . admin_url('edit.php?post_type=br_product_filter') . '">' . __('Manage filters', 'BeRocket_AJAX_domain') . '</a>';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('custom_class'); ?>"><?php _e('Custom CSS class', 'BeRocket_AJAX_domain'); ?></label>
            <input type="text" id="<?php echo $this->get_field_id('custom_class'); ?>" name="<?php echo $this->get_field_name('custom_class'); ?>" value="<?php echo $instance['custom_class']; ?>">
        </p>
        <p class="berocketwizard_aapf_single_widget_filter_id">
            <label for="<?php echo $this->get_field_id('filter_id'); ?>"><?php _e('Filter', 'BeRocket_AJAX_domain'); ?></label><br>
            <?php
            $query = new WP_Query(array('post_type' => 'br_product_filter', 'nopaging' => true));
            $edit_link_current = '';
            if ( $query->have_posts() ) {
                echo '<select class="berocket_new_widget_selectbox single" id="'.$this->get_field_id('filter_id').'" name="'.$this->get_field_name('filter_id').'">';
                echo '<option>'.__('--Please select filter--', 'BeRocket_AJAX_domain').'</option>';
                while ( $query->have_posts() ) {
                    if( empty($instance['filter_id']) ) {
                        $instance['filter_id'] = get_the_id();
                    }
                    $query->the_post();
                    echo '<option data-edit="'.get_edit_post_link().'" value="' . get_the_id() . '"'.(get_the_id() == $instance['filter_id'] ? ' selected' : '').'>' . substr(get_the_title(), 0, 50) . (strlen(get_the_title()) > 50 ? '...' : '') . ' (ID:' . get_the_id() . ')</option>';
                    if( get_the_id() == $instance['filter_id'] ) {
                        $edit_link_current = get_edit_post_link();
                    }
                }
                echo '</select>';
                wp_reset_postdata();
            }
            ?>
            <a target="_blank" class="berocket_aapf_edit_post_link" href="<?php echo $edit_link_current; ?>"<?php if( empty($edit_link_current) ) echo ' style="display: none;"'; ?>><?php _e('Edit', 'BeRocket_AJAX_domain'); ?></a>
        </p>
        <a class="button berocket_create_new" data-type="single" data-action="berocket_aapf_load_simple_filter_creation" href="#"><?php _e('Create Filter', 'BeRocket_AJAX_domain'); ?></a>
        <?php
    }
}
?>
