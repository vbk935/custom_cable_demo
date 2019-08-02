<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


if ( ! class_exists( 'AWS_Admin' ) ) :

/**
 * Class for plugin admin panel
 */
class AWS_Admin {

    /*
     * Name of the plugin settings page
     */
    var $page_name = 'aws-options';

    /**
     * @var AWS_Admin The single instance of the class
     */
    protected static $_instance = null;


    /**
     * Main AWS_Admin Instance
     *
     * Ensures only one instance of AWS_Admin is loaded or can be loaded.
     *
     * @static
     * @return AWS_Admin - Main instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /*
    * Constructor
    */
    public function __construct() {
        add_action( 'admin_menu', array( &$this, 'add_admin_page' ) );
        add_action( 'admin_init', array( &$this, 'register_settings' ) );

        if ( ! get_option( 'aws_settings' ) ) {
            $this->initialize_settings();
        }

        add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );
    }

    /**
     * Add options page
     */
    public function add_admin_page() {
        add_menu_page( esc_html__( 'Adv. Woo Search', 'aws' ), esc_html__( 'Adv. Woo Search', 'aws' ), 'manage_options', 'aws-options', array( &$this, 'display_admin_page' ), 'dashicons-search' );
    }

    /**
     * Generate and display options page
     */
    public function display_admin_page() {

        $options = $this->options_array();
        $nonce = wp_create_nonce( 'plugin-settings' );

        $tabs = array(
            'general' => esc_html__( 'General', 'aws' ),
            'form'    => esc_html__( 'Search Form', 'aws' ),
            'results' => esc_html__( 'Search Results', 'aws' )
        );

        $current_tab = empty( $_GET['tab'] ) ? 'general' : sanitize_text_field( $_GET['tab'] );

        $tabs_html = '';

        foreach ( $tabs as $name => $label ) {
            $tabs_html .= '<a href="' . admin_url( 'admin.php?page=aws-options&tab=' . $name ) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';

        }

        $tabs_html .= '<a href="https://advanced-woo-search.com/?utm_source=plugin&utm_medium=settings-tab&utm_campaign=aws-pro-plugin" class="nav-tab premium-tab" target="_blank">' . esc_html__( 'Get Premium', 'aws' ) . '</a>';

        $tabs_html = '<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">'.$tabs_html.'</h2>';


        if ( isset( $_POST["Submit"] ) && current_user_can( 'manage_options' ) && isset( $_POST["_wpnonce"] ) && wp_verify_nonce( $_POST["_wpnonce"], 'plugin-settings' ) ) {

            $update_settings = $this->get_settings();

            foreach ( $options[$current_tab] as $values ) {

                if ( $values['type'] === 'heading' ) {
                    continue;
                }

                if ( $values['type'] === 'checkbox' ) {

                    $checkbox_array = array();

                    foreach ( $values['choices'] as $key => $value ) {
                        $new_value = isset( $_POST[ $values['id'] ][$key] ) ? '1' : '0';
                        $checkbox_array[$key] = (string) sanitize_text_field( $new_value );
                    }

                    $update_settings[ $values['id'] ] = $checkbox_array;

                    continue;
                }

                $new_value = isset( $_POST[ $values['id'] ] ) ? $_POST[ $values['id'] ] : '';
                $update_settings[ $values['id'] ] = (string) sanitize_text_field( $new_value );

                if ( isset( $values['sub_option'] ) ) {
                    $new_value = isset( $_POST[ $values['sub_option']['id'] ] ) ? $_POST[ $values['sub_option']['id'] ] : '';
                    $update_settings[ $values['sub_option']['id'] ] = (string) sanitize_text_field( $new_value );
                }
            }

            update_option( 'aws_settings', $update_settings );

            AWS_Helpers::register_wpml_translations( $update_settings );

            do_action( 'aws_settings_saved' );
            
            do_action( 'aws_cache_clear' );

        }

        echo '<div class="wrap">';

        echo '<h1></h1>';

        echo '<h1 class="aws-instance-name">Advanced Woo Search</h1>';

        echo $tabs_html;

        echo '<form action="" name="aws_form" id="aws_form" method="post">';

        switch ($current_tab) {
            case('form'):
                new AWS_Admin_Fields( $options['form'] );
                break;
            case('results'):
                new AWS_Admin_Fields( $options['results'] );
                break;
            default:
                $this->update_table();
                new AWS_Admin_Fields( $options['general'] );
        }

        echo '<input type="hidden" name="_wpnonce" value="' . esc_attr( $nonce ) . '">';

        echo '</form>';

        echo '</div>';

    }

    /*
     * Reindex table
     */
    private function update_table() {

        echo '<table class="form-table">';
        echo '<tbody>';

        echo '<tr>';

            echo '<th>' . esc_html__( 'Activation', 'aws' ) . '</th>';
            echo '<td>';
                echo '<div class="description activation">';
                    echo esc_html__( 'In case you need to add plugin search form on your website, you can do it in several ways:', 'aws' ) . '<br>';
                    echo '<div class="list">';
                        echo '1. ' . esc_html__( 'Enable a "Seamless integration" option ( may not work with some themes )', 'aws' ) . '<br>';
                        echo '2. ' . sprintf( esc_html__( 'Add search form using shortcode %s', 'aws' ), "<code>[aws_search_form]</code>" ) . '<br>';
                        echo '3. ' . esc_html__( 'Add search form as widget for one of your theme widget areas. Go to Appearance -> Widgets and drag&drop AWS Widget to one of your widget areas', 'aws' ) . '<br>';
                        echo '4. ' . sprintf( esc_html__( 'Add PHP code to the necessary files of your theme: %s', 'aws' ), "<code>&lt;?php if ( function_exists( 'aws_get_search_form' ) ) { aws_get_search_form(); } ?&gt;</code>" ) . '<br>';
                    echo '</div>';
                echo '</div>';
            echo '</td>';

        echo '</tr>';

        echo '<tr>';

            echo '<th>' . esc_html__( 'Reindex table', 'aws' ) . '</th>';
            echo '<td>';
                echo '<div id="aws-reindex"><input class="button" type="button" value="' . esc_attr__( 'Reindex table', 'aws' ) . '"><span class="loader"></span><span class="reindex-progress">0%</span></div><br><br>';
                echo '<span class="description">' .
                    sprintf( esc_html__( 'This action only need for %s one time %s - after you activate this plugin. After this all products changes will be re-indexed automatically.', 'aws' ), '<strong>', '</strong>' ) . '<br>' .
                    __( 'Update all data in plugins index table. Index table - table with products data where plugin is searching all typed terms.<br>Use this button if you think that plugin not shows last actual data in its search results.<br><strong>CAUTION:</strong> this can take large amount of time.', 'aws' ) . '<br><br>' .
                    esc_html__( 'Products in index:', 'aws' ) . '<span id="aws-reindex-count"> <strong>' . AWS_Helpers::get_indexed_products_count() . '</strong></span>';
                echo '</span>';
            echo '</td>';

        echo '</tr>';


        echo '<tr>';

            echo '<th>' . esc_html__( 'Clear cache', 'aws' ) . '</th>';
            echo '<td>';
                echo '<div id="aws-clear-cache"><input class="button" type="button" value="' . esc_attr__( 'Clear cache', 'aws' ) . '"><span class="loader"></span></div><br>';
                echo '<span class="description">' . esc_html__( 'Clear cache for all search results.', 'aws' ) . '</span>';
            echo '</td>';

        echo '</tr>';

        echo '</tbody>';
        echo '</table>';

    }

    /*
	 * Options array that generate settings page
	 */
    public function options_array() {

        require_once AWS_DIR .'/includes/options.php';

        return $options;
    }

    /*
	 * Register plugin settings
	 */
    public function register_settings() {
        register_setting( 'aws_settings', 'aws_settings' );
    }

    /*
	 * Get plugin settings
	 */
    public function get_settings() {
        $plugin_options = get_option( 'aws_settings' );
        return $plugin_options;
    }

    /**
     * Initialize settings to their default values
     */
    public function initialize_settings() {
        $options = $this->options_array();
        $default_settings = array();

        foreach ( $options as $section ) {
            foreach ($section as $values) {

                if ( $values['type'] === 'heading' ) {
                    continue;
                }

                $default_settings[$values['id']] = (string) sanitize_text_field( $values['value'] );

                if (isset( $values['sub_option'])) {
                    $default_settings[$values['sub_option']['id']] = (string) sanitize_text_field( $values['sub_option']['value'] );
                }
            }
        }

        update_option( 'aws_settings', $default_settings );
    }

    /*
     * Enqueue admin scripts and styles
     */
    public function admin_enqueue_scripts() {

        if ( isset( $_GET['page'] ) && $_GET['page'] == 'aws-options' ) {
            wp_enqueue_style( 'plugin-admin-style', AWS_URL . '/assets/css/admin.css', array(), AWS_VERSION );
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'jquery-ui-sortable' );
            wp_enqueue_script( 'plugin-admin-scripts', AWS_URL . '/assets/js/admin.js', array('jquery'), AWS_VERSION );
            wp_localize_script( 'plugin-admin-scripts', 'aws_vars', array(
                'ajaxurl' => admin_url('admin-ajax.php' ),
                'ajax_nonce' => wp_create_nonce( 'aws_admin_ajax_nonce' ),
            ) );
        }

    }

}

endif;


add_action( 'init', 'AWS_Admin::instance' );