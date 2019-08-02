<?php
class BeRocket_AAPF_compat_product_table {
    function __construct() {
        add_action( 'plugins_loaded', array( __CLASS__, 'plugins_loaded' ), 1 );
        if(defined('DOING_AJAX') && DOING_AJAX && !empty($_POST['action']) && $_POST['action'] == 'wcpt_load_products') {
            $table_id = filter_input( INPUT_POST, 'table_id', FILTER_SANITIZE_STRING );
            $table_transient = get_transient( $table_id );
            unset($table_transient['total_posts']);
            unset($table_transient['total_filtered_posts']);
            set_transient( $table_id, $table_transient, DAY_IN_SECONDS );
        }
    }
    public static function plugins_loaded() {
        if( class_exists('WC_Product_Table_Plugin') 
        && function_exists('WC_Product_Table') 
        && version_compare(WC_Product_Table_Plugin::VERSION, '2.1.3', '>') ) {
            add_filter('aapf_localize_widget_script', array( __CLASS__, 'aapf_localize_widget_script' ));
            add_action( 'wc_product_table_get_table', array( __CLASS__, 'wc_product_table_get_table' ), 10, 1 );
            self::not_ajax_functions();
            $wcpt_shortcode_defaults = get_option('wcpt_shortcode_defaults');
            $wcpt_shortcode_defaults['berocket_ajax'] = '1';
            update_option('wcpt_shortcode_defaults', $wcpt_shortcode_defaults);
        }
    }
    public static function wc_product_table_get_table($table) {
        $table_args = $table->args->get_args();
        $table->query->get_total_products();
        if( ! empty($table_args['berocket_ajax'])
        && method_exists($table->data_table, 'add_above')
        && method_exists($table->data_table, 'add_below') ) {
            $table->data_table->add_above('<div class="berocket_product_table_compat">');
            $table->data_table->add_below('</div>');
        }
    }
    public static function not_ajax_functions() {
        add_filter( 'wc_product_table_query_args', array( __CLASS__, 'woocommerce_shortcode_products_query' ), 100, 2 );
    }
    public static function woocommerce_shortcode_products_query( $query_vars, $table ) {
        $table_args = $table->args->get_args();
        if( empty($table_args['berocket_ajax']) ) {
            return $query_vars;
        }
        if(defined('DOING_AJAX') && DOING_AJAX && !empty($_POST['action']) && $_POST['action'] == 'wcpt_load_products' && ! empty($_POST['filters'])) {
            $_GET['filters'] = $_POST['filters'];
        }
        $BeRocket_AAPF = BeRocket_AAPF::getInstance();
        $query_vars = $BeRocket_AAPF->woocommerce_filter_query_vars($query_vars);
        return $query_vars;
    }
    public static function aapf_localize_widget_script($localize) {
        $localize['products_holder_id'] .= ( empty($localize['products_holder_id']) ? '' : ', ' ) . '.berocket_product_table_compat';
        $localize['user_func']['after_update'] = 'if( typeof(jQuery(".berocket_product_table_compat .wc-product-table").productTable) == "function" && ! jQuery(".berocket_product_table_compat > .dataTables_wrapper").length ) {jQuery(".berocket_product_table_compat .wc-product-table").productTable();}' . $localize['user_func']['after_update'];
        return $localize;
    }
}
new BeRocket_AAPF_compat_product_table();
