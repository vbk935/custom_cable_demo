<?php
/* $post_settings example
array(
   'labels' => array(
       'menu_name'          => _x( 'Product Filters', 'Admin menu name', 'BeRocket_AJAX_domain' ),
       'add_new_item'       => __( 'Add New Filter', 'BeRocket_AJAX_domain' ),
       'edit'               => __( 'Edit', 'BeRocket_AJAX_domain' ),
       'edit_item'          => __( 'Edit Filter', 'BeRocket_AJAX_domain' ),
       'new_item'           => __( 'New Filter', 'BeRocket_AJAX_domain' ),
       'view'               => __( 'View Filters', 'BeRocket_AJAX_domain' ),
       'view_item'          => __( 'View Filter', 'BeRocket_AJAX_domain' ),
       'search_items'       => __( 'Search Product Filters', 'BeRocket_AJAX_domain' ),
       'not_found'          => __( 'No Product Filters found', 'BeRocket_AJAX_domain' ),
       'not_found_in_trash' => __( 'No Product Filters found in trash', 'BeRocket_AJAX_domain' ),
   ),
   'description'     => __( 'This is where you can add Product Filters.', 'BeRocket_AJAX_domain' ),
   'public'          => true,
   'show_ui'         => true,
   'capability_type' => 'post',
   'publicly_queryable'  => false,
   'exclude_from_search' => true,
   'show_in_menu'        => 'edit.php?post_type=product',
   'hierarchical'        => false,
   'rewrite'             => false,
   'query_var'           => false,
   'supports'            => array( 'title' ),
   'show_in_nav_menus'   => false,
)
*/

if ( ! class_exists('BeRocket_custom_post_class') ) {
    class BeRocket_custom_post_class {
        public $meta_boxes = array();
        public $default_settings = array();
        public $post_settings, $post_name;
        public $post_type_parameters = array();
        protected static $instance;

        public static function getInstance() {
            if (null === static::$instance)
            {
                static::$instance = new static();
            }
            return static::$instance;
        }

        function __construct () {
            if (null === static::$instance)
            {
                static::$instance = $this;
            }
            $this->post_type_parameters = array_merge(array(
                'sortable' => false
            ), $this->post_type_parameters);
            add_filter( 'init', array( $this, 'init' ) );
            add_filter( 'admin_init', array( $this, 'admin_init' ), 15 );
            add_filter( 'wp_insert_post_data', array( $this, 'wp_insert_post_data' ), 30, 2 );
            if( $this->post_type_parameters['sortable'] ) {
                if( is_admin() ) {
                    add_action('berocket_custom_post_'.$this->post_name.'_admin_init', array($this, 'sortable_admin_init'));
                    add_action('berocket_custom_post_'.$this->post_name.'_wc_save_product_before', array($this, 'sortable_wc_save_product_before'), 10, 2);
                    add_action('berocket_custom_post_'.$this->post_name.'_wc_save_product_without_check_before', array($this, 'sortable_wc_save_product_before'), 10, 2);
                    add_action('berocket_custom_post_'.$this->post_name.'_columns_replace', array($this, 'sortable_columns_replace'), 10, 1);
                    add_filter('berocket_custom_post_'.$this->post_name.'_manage_edit_columns', array($this, 'sortable_manage_edit_columns'));
                }
                add_filter('berocket_custom_post_'.$this->post_name.'_get_custom_posts_args_default', array($this, 'sortable_get_custom_post'));
                add_action('berocket_custom_post_'.$this->post_name.'_admin_init_only', array($this, 'jquery_sortable_for_posts'));
            }
            if( ! empty($this->post_settings['capability_type']) && $this->post_settings['capability_type'] != 'product' ) {
                add_filter('BeRocket_admin_init_user_capabilities', array($this, 'init_user_capabilities'));
            }
        }

        function init() {
            $this->default_settings = apply_filters('berocket_custom_post_'.$this->post_name.'_default_settings', $this->default_settings, self::$instance);
            register_post_type( $this->post_name, $this->post_settings);
        }

        public function get_custom_posts($args = array()) {
            $args = array_merge(apply_filters( 'berocket_custom_post_'.$this->post_name.'_get_custom_posts_args_default', array(
                'posts_per_page'   => -1,
                'offset'           => 0,
                'category'         => '',
                'category_name'    => '',
                'include'          => '',
                'exclude'          => '',
                'meta_key'         => '',
                'meta_value'       => '',
                'post_type'        => $this->post_name,
                'post_mime_type'   => '',
                'post_parent'      => '',
                'author'           => '',
                'post_status'      => 'publish',
                'fields'           => 'ids',
                'suppress_filters' => false 
            ) ), $args);
            $posts_array = new WP_Query($args);
            $posts_array = $posts_array->posts;
            return $posts_array;
        }

        public function add_meta_box($slug, $name, $callback = false, $position = 'normal', $priority = 'high') {
            if( $callback === false ) {
                $callback = array($this, $slug);
            }
            $this->meta_boxes[$slug] = array('slug' => $slug, 'name' => $name, 'callback' => $callback, 'position' => $position, 'priority' => $priority);
        }

        public function admin_init() {
            global $pagenow;
            if( 'edit.php' == $pagenow && isset( $_GET['post_type'] ) && $_GET['post_type'] == $this->post_name ){
                wp_enqueue_script( 'berocket_framework_admin' );
                wp_enqueue_style( 'berocket_framework_admin_style' );
                do_action('berocket_custom_post_'.$this->post_name.'_admin_init_only', $this->post_type_parameters);
            }
            add_filter( 'bulk_actions-edit-'.$this->post_name, array( $this, 'bulk_actions_edit' ) );
            add_filter( 'views_edit-'.$this->post_name, array( $this, 'views_edit' ) );
            add_filter( 'manage_edit-'.$this->post_name.'_columns', array( $this, 'manage_edit_columns' ) );
            add_action( 'manage_'.$this->post_name.'_posts_custom_column', array( $this, 'columns_replace' ), 2 );
            add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
            add_action( 'save_post', array( $this, 'wc_save_product' ), 10, 2 );
            add_filter( 'post_row_actions', array( $this, 'post_row_actions' ), 10, 2 );
            add_filter( 'list_table_primary_column', array( $this, 'list_table_primary_column' ), 10, 2 );

            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
            do_action( 'berocket_custom_post_'.$this->post_name.'_admin_init', $this->post_type_parameters);
        }

        public function admin_enqueue_scripts() {
            global $post;
            if ( ! empty( $post ) and $post->post_type == $this->post_name ) {
                wp_register_style( 'font-awesome', plugins_url( '../assets/css/font-awesome.min.css', __FILE__ ) );
            }
        }

        public function post_row_actions($actions, $post) {
            if( $post->post_type == $this->post_name ) {
                if( isset($actions['inline hide-if-no-js']) ) {
                    unset($actions['inline hide-if-no-js']);
                }
            }
            return $actions;
        }

        public function list_table_primary_column($default, $screen_id) {
            if( $screen_id == 'edit-'.$this->post_name ) {
                $default = 'name';
            }
            return $default;
        }

        public function bulk_actions_edit ( $actions ) {
            unset( $actions['edit'] );
            return $actions;
        }

        public function views_edit ( $view ) {
            unset( $view['publish'], $view['private'], $view['future'] );
            return $view;
        }

        public function manage_edit_columns ( $columns ) {
            $columns = array();
            $columns["cb"]   = '<input type="checkbox" />';
            $columns["name"] = __( "Name", 'BeRocket_domain' );
            $columns = apply_filters( 'berocket_custom_post_'.$this->post_name.'_manage_edit_columns', $columns, $this->post_type_parameters);
            return $columns;
        }

        public function columns_replace ( $column ) {
            global $post;
            switch ( $column ) {
                case "name":

                    $edit_link = get_edit_post_link( $post->ID );
                    $title = '<a class="row-title" href="' . $edit_link . '">' . _draft_or_post_title() . '</a>';

                    echo 'ID:' . $post->ID . ' <strong>' . $title . '</strong>';

                    break;
                default:
                    break;
            }
            do_action( 'berocket_custom_post_'.$this->post_name.'_columns_replace', $column, $this->post_type_parameters);
        }

        public  function add_meta_boxes () {
            add_meta_box( 'submitdiv', __( 'Save content', 'BeRocket_domain' ), array( $this, 'save_meta_box' ), $this->post_name, 'side', 'high' );
            add_meta_box( 'copysettingsfromdiv', __( 'Copy settings from', 'BeRocket_domain' ), array( $this, 'copy_settings_from' ), $this->post_name, 'side', 'high' );
            foreach($this->meta_boxes as $meta_box) {
                add_meta_box( $meta_box['slug'], $meta_box['name'], $meta_box['callback'], $this->post_name, $meta_box['position'], $meta_box['priority'] );
            }
        }

        public function copy_settings_from($post) {
            $posts_array = $this->get_custom_posts();
            ?>
            <div class="berocket_copy_from_custom_post_block">
                <?php do_action('berocket_copy_from_custom_post_block', $this->post_name, $post);?>
                <select name="berocket_copy_from_custom_post_select">
                    <option value="0"><?php _e( 'Do not copy', 'BeRocket_domain' ) ?></option>
                    <?php
                    if( ! empty($posts_array) && is_array($posts_array) ) {
                        foreach($posts_array as $post_id) {
                            if( $post_id == $post->ID ) continue;
                            echo '<option value="'.$post_id.'">(ID: '.$post_id.') '.get_the_title($post_id).'</option>';
                        }
                    }
                    ?>
                </select>
                <input name="berocket_copy_from_custom_post" type="hidden" value="">
                <button type="button" class="button" disabled><?php _e( 'Copy', 'BeRocket_domain' ) ?></button>
            </div>
            <script>
                jQuery('.berocket_copy_from_custom_post_block button').on('click', function() {
                    jQuery('.berocket_copy_from_custom_post_block input').val(jQuery('.berocket_copy_from_custom_post_block select').val());
                    jQuery('.submitbox input[type=submit]').trigger('click');
                });
                jQuery('.berocket_copy_from_custom_post_block select').on('change', function() {
                        jQuery('.berocket_copy_from_custom_post_block button').prop('disabled', ( ! jQuery(this).val() || jQuery(this).val() == '0' ));

                });
            </script>
            <?php
        }

        public  function save_meta_box($post) {
            wp_enqueue_script( 'berocket_aapf_widget-colorpicker' );
            wp_enqueue_script( 'berocket_aapf_widget-admin' );
            wp_enqueue_style( 'brjsf-ui' );
            wp_enqueue_script( 'brjsf-ui' );
            wp_enqueue_script( 'berocket_framework_admin' );
            wp_enqueue_style( 'berocket_framework_admin_style' );
            wp_enqueue_script( 'berocket_widget-colorpicker' );
            wp_enqueue_style( 'berocket_widget-colorpicker-style' );
            wp_enqueue_style( 'font-awesome' );
            ?>
            <div class="submitbox" id="submitpost">

                <div id="minor-publishing">
                    <div id="major-publishing-actions">
                        <div id="delete-action">
                            <?php
                            global $pagenow;
                            if( ! in_array( $pagenow, array( 'post-new.php' ) ) ) {
                                if ( current_user_can( "delete_post", $post->ID ) ) {
                                    if ( ! EMPTY_TRASH_DAYS )
                                        $delete_text = __( 'Delete Permanently', 'BeRocket_domain' );
                                    else
                                        $delete_text = __( 'Move to Trash', 'BeRocket_domain' );
                                    ?>
                                    <a class="submitdelete deletion" href="<?php echo esc_url( get_delete_post_link( $post->ID ) ); ?>"><?php echo esc_attr( $delete_text ); ?></a>
                                <?php 
                                }
                            } ?>
                        </div>

                        <div id="publishing-action">
                            <span class="spinner"></span>
                            <input type="submit" class="button button-primary tips" name="publish" value="<?php _e( 'Save', 'BeRocket_domain' ); ?>" data-tip="<?php _e( 'Save/update notice', 'BeRocket_domain' ); ?>" />
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
            <?php
            wp_nonce_field($this->post_name.'_check', $this->post_name.'_nonce');
        }

        public function wc_save_check($post_id, $post) {
            if ( $this->post_name != $post->post_type ) {
                return false;
            }

            $current_settings = get_post_meta( $post_id, $this->post_name, true );

            if( empty($current_settings) ) {
                update_post_meta( $post_id, $this->post_name, $this->default_settings );
            }

            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return false;
            }

            if( empty($_REQUEST[$this->post_name.'_nonce']) || ! wp_verify_nonce($_REQUEST[$this->post_name.'_nonce'], $this->post_name.'_check') ) {
                return false;
            }

            return true;
        }

        public function wc_save_product( $post_id, $post ) {
            do_action( 'berocket_custom_post_'.$this->post_name.'_wc_save_product_before', $post_id, $post, $this->post_type_parameters);
            if ( ! $this->wc_save_check( $post_id, $post ) ) {
                return false;
            }
            $this->wc_save_product_without_check($post_id, $post);
            do_action( 'berocket_custom_post_'.$this->post_name.'_wc_save_product_after', $post_id, $post, $this->post_type_parameters);
        }
        public function wc_save_product_without_check( $post_id, $post ) {
            do_action( 'berocket_custom_post_'.$this->post_name.'_wc_save_product_without_check_before', $post_id, $post, $this->post_type_parameters);
            if ( isset( $_POST[$this->post_name] ) ) {
                $post_data = berocket_sanitize_array($_POST[$this->post_name]);

                if( is_array($post_data) ) {
                    $settings = BeRocket_Framework::recursive_array_set($this->default_settings, $post_data);
                } else {
                    $settings = $post_data;
                }

                update_post_meta( $post_id, $this->post_name, $settings );
            }

            if ( ! empty($_POST['berocket_copy_from_custom_post']) ) {
                $copy_option = get_post_meta( $_POST['berocket_copy_from_custom_post'], $this->post_name, true );

                if( ! empty($copy_option) ) {
                    update_post_meta( $post_id, $this->post_name, $copy_option );
                    $_POST[$this->post_name] = $copy_option;
                    do_action('berocket_copy_from_custom_post', $this->post_name, $post_id, $post);
                }
            }
            do_action( 'berocket_custom_post_'.$this->post_name.'_wc_save_product_without_check_after', $post_id, $post, $this->post_type_parameters);
        }

        public function get_option( $post_id ) {
            $options = get_post_meta( $post_id, $this->post_name, true );

            if ( ! is_array($options) ) {
                $options = array();
            }

            $options = array_merge( $this->default_settings, $options );

            return $options;
        }

        public function create_new_post($post_data = array(), $options = array()) {
            $post_data = array_merge(array(
                'post_title'    => '',
                'post_content'  => '',
                'post_type'     => $this->post_name,
                'post_status'   => 'publish'
            ), $post_data);
            $post_id = wp_insert_post($post_data);
            $options = array_merge($this->default_settings, $options);
            $_POST[$this->post_name] = $options;
            $post = get_post($post_id);
            $this->wc_save_product_without_check($post_id, $post);
            return $post_id;
        }
        public function wp_insert_post_data($data, $post) {
            if ( ! isset($post['ID']) || ! $post['ID'] ) return $data;
            if ( $post['post_type'] !== $this->post_name ) return $data;
            if( ! in_array($data['post_status'], array('publish', 'trash')) ) {
                $data['post_status'] = 'publish';
            }
            return $data;
        }
        //SORTABLE CUSTOM POST
        public function sortable_admin_init() {
            $this->get_custom_posts();
            add_action( 'pre_get_posts', array($this, 'sortable_get_posts') );
            if( ! empty($_POST['braction']) && $_POST['braction'] == 'berocket_custom_post_sortable' ) {
                $this->sortable_change();
            }
        }
        public function sortable_change() {
            if( ! empty($_POST['BRsortable_id']) && isset($_POST['BRorder']) ) {
                $BRsortable_id = sanitize_key($_POST['BRsortable_id']);
                $BRorder = sanitize_key($_POST['BRorder']);
                $BRsortable_id = intval($BRsortable_id);
                $BRorder = intval($BRorder);
                if( current_user_can('edit_post', $BRsortable_id) ) {
                    update_post_meta($BRsortable_id, 'berocket_post_order', $BRorder);
                }
            }
            if( ! empty($_POST['BRsortable']) ) {
                $BRsortable = $_POST['BRsortable'];
                if( ! is_array($BRsortable) ) {
                    $BRsortable = array();
                }
                foreach($BRsortable as $BRsortable_post) {
                    $BRsortable_id = sanitize_key($BRsortable_post['id']);
                    $BRorder = sanitize_key($BRsortable_post['order']);
                    $BRsortable_id = intval($BRsortable_id);
                    $BRorder = intval($BRorder);
                    if( current_user_can('edit_post', $BRsortable_id) ) {
                        update_post_meta($BRsortable_id, 'berocket_post_order', $BRorder);
                    }
                }
            }
        }
        public function sortable_get_posts( $query ){
            global $pagenow;
            if( 'edit.php' == $pagenow && isset( $_GET['post_type'] ) && $_GET['post_type'] == $this->post_name ){
                $query->set( 'meta_key', 'berocket_post_order' );
                $query->set( 'orderby', 'meta_value_num' );
                $query->set( 'order', 'ASC' );
            }
        }
        public function sortable_get_custom_post($args) {
            if( is_admin() ) {
                $posts_not_ordered = new WP_Query($args);
                $posts_not_ordered = $posts_not_ordered->posts;
            }
            $args = array_merge($args, array(
                'meta_key'         => 'berocket_post_order',
                'orderby'          => 'meta_value_num',
                'order'            => 'ASC',
            ));
            if( is_admin() ) {
                $posts_ordered = new WP_Query($args);
                $posts_ordered = $posts_ordered->posts;
                $posts_fix = array_diff($posts_not_ordered, $posts_ordered);
                foreach($posts_fix as $post_fix_id) {
                    add_post_meta( $post_fix_id, 'berocket_post_order', '0', true );
                }
            }
            return $args;
        }
        public function sortable_wc_save_product_before( $post_id, $post ) {
            $order_position = get_post_meta( $post_id, 'berocket_post_order', true );
            $order_position = intval($order_position);
            update_post_meta( $post_id, 'berocket_post_order', $order_position );
        }
        public function sortable_columns_replace($column) {
            global $post;
            $post_id = $post->ID;
            $order_position = get_post_meta( $post_id, 'berocket_post_order', true );
            $order_position = intval($order_position);
            switch ( $column ) {
                case "berocket_sortable":
                    echo $this->sortable_html_position($post_id, $order_position);
                    break;
                default:
                    break;
            }
        }
        public function sortable_html_position($post_id, $order) {
            $html = '';
            if( $order > 0 ) {
                $html .= '<a href="#order-up" class="berocket_post_set_new_sortable" data-post_id="'.$post_id.'" data-order="'.($order - 1).'"><i class="fa fa-arrow-up"></i></a>';
            }
            $html .= '<span class="berocket_post_set_new_sortable_input"><input type="number" min="0" value="'.$order.'"><a class="berocket_post_set_new_sortable_set fa fa-arrow-circle-right" data-post_id="'.$post_id.'" href="#order-set"></a></span>';
            $html .= '<a href="#order-up" class="berocket_post_set_new_sortable" data-post_id="'.$post_id.'" data-order="'.($order + 1).'"><i class="fa fa-arrow-down"></i></a>';
            return $html;
        }
        public function sortable_manage_edit_columns($columns) {
            $columns["berocket_sortable"] = __( "Order", 'BeRocket_domain' );
            return $columns;
        }
        public function jquery_sortable_for_posts() {
            wp_enqueue_script('jquery-ui-sortable');
            add_action('in_admin_footer', array($this, 'sortable_in_admin_footer'));
        }
        public function sortable_in_admin_footer() {
            global $wp_query;
            if( $wp_query->is_main_query() && $wp_query->max_num_pages == 1 ) {
                ?>
                <script>
                    jQuery(document).ready(function() {
                        var BRsortable_jquery_ui = function() {
                            if( ! jQuery("#the-list").is(".ui-sortable") ) {
                                jQuery("#the-list .column-name").prepend(jQuery("<i class='fa fa-bars'></i>"));
                                jQuery("#the-list").sortable({
                                    handle:".fa-bars",
                                    axis: "y",
                                    stop: function() {
                                        jQuery("#the-list .berocket_post_set_new_sortable_input input").each(function(i, o) {
                                            jQuery(o).val(i);
                                        });
                                        var BRsortable = [];
                                        jQuery("#the-list .berocket_post_set_new_sortable_input").each(function() {
                                            BRsortable.push({id:jQuery(this).find(".berocket_post_set_new_sortable_set").data('post_id'), order:jQuery(this).find("input").val()});
                                        });
                                        jQuery.post(location.href, {braction:'berocket_custom_post_sortable', BRsortable:BRsortable}, function(html) {
                                            var $html = jQuery(html);
                                            var $tbody = $html.find('.berocket_post_set_new_sortable').first().parents('tbody').first();
                                            jQuery('.berocket_post_set_new_sortable').first().parents('tbody').first().replaceWith($tbody);
                                            jQuery(document).trigger('BRsortable_loaded_html');
                                        });
                                    }
                                });
                            }
                        }
                        BRsortable_jquery_ui();
                        jQuery(document).on("BRsortable_loaded_html", BRsortable_jquery_ui);
                    });
                </script>
                <?php
            }
        }
        public function init_user_capabilities($user_caps) {
            $cap_settings = $this->post_settings;
            $cap_settings['capabilities'] = array();
            if( ! isset($cap_settings['map_meta_cap']) ) {
                $cap_settings['map_meta_cap'] = true;
            }
            $post_caps = get_post_type_capabilities((object)$cap_settings);
            $post_caps = (array)$post_caps;
            foreach($post_caps as $post_cap) {
                $user_caps[] = $post_cap;
            }
            return $user_caps;
        }
    }
}
