<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WCPV_Renderers {

    public function __construct() {
        add_action('woocommerce_admin_field_wcpv_backend_product_search', array($this, 'optimize_select2_product_search'));
        add_action('woocommerce_admin_field_wcpv_backend_subsection', array($this, 'subsection_renderer'));
        add_action('woocommerce_admin_field_wcpv_backend_header', array($this, 'header_renderer'));
        add_action('woocommerce_admin_field_wcpv_backend_role_section', array($this, 'role_section_renderer'));
        add_action('woocommerce_admin_field_wcpv_form_open', array($this, 'form_open_renderer'));
        add_action('woocommerce_admin_field_wcpv_reset_button', array($this, 'reset_button_renderer'));
        add_action('woocommerce_admin_field_wcpv_form_close', array($this, 'form_close_renderer'));
        add_action('woocommerce_admin_field_wcpv_div_open', array($this, 'div_open_renderer'));
        add_action('woocommerce_admin_field_wcpv_div_close', array($this, 'div_close_renderer'));
    }

    public function header() {
        return array(
            'id' => 'wcpv_header_ID',
            'type' => 'wcpv_backend_header'
        );
    }

    public function form_open() {
        return array(
            'type' => 'wcpv_form_open'
        );
    }
    
    public function title_inner($data) {
        return array(
            'id' => 'wcpv_section_' . $data,
            'type' => 'title',
        );
    }

    public function sectionend($id) {
        return array(
            'id' => $id,
            'type' => 'sectionend'
        );
    }

    public function role_section($data, $key) {
        return array(
            'id' => 'wcpv_section_' . $data,
            'type' => 'wcpv_backend_role_section',
            'name' => sprintf(__('Role: %s', 'woocommerce-products-visibility'), $key)
        );
    }

    public function priority_input($id, $name) {
        return array(
            'id' => $id,
            'type' => 'number',
            'name' => $name,
            'default' => 50,
            'maxlength' => 2,
            'desc_tip' => __('Priority used to calculate product visibility. Smaller numbers get their rules applied first.', 'woocommerce-products-visibility'),
            'custom_attributes' => array(
                'min' => 1,
                'step' => 1
            )
        );
    }

    public function subsection($name) {
        return array(
            'type' => 'wcpv_backend_subsection',
            'name' => $name
        );
    }

    public function show_hide_radio($id, $name, $cancel_hide_visible) {
        $condition_array = array(
            'wcpv_products_visibility_default',
            'wcpv_categories_visibility_default',
            'wcpv_tags_visibility_default',
            'wcpv_products_visibility_guest',
            'wcpv_categories_visibility_guest',
            'wcpv_tags_visibility_guest'
        );
        if (in_array($id, $condition_array) || !$cancel_hide_visible) { // Hide cancel hide in default and guest user OR when it is not needed
            $options = array('1' => __('Show', 'woocommerce-products-visibility'),
                '2' => __('Hide', 'woocommerce-products-visibility'));
        } else {
            $options = array('1' => __('Show', 'woocommerce-products-visibility'),
                '2' => __('Hide', 'woocommerce-products-visibility'),
                '3' => __('Cancel hide', 'woocommerce-products-visibility'));
        }
        return array(
            'id' => $id,
            'type' => 'radio',
            'name' => $name,
            'std' => '1', // WooCommerce < 2.0
            'default' => '1', // WooCommerce >= 2.0s
            'options' => $options,
        );
    }

    public function products($id, $name, $desc, $data_minimum_input_length, $data_action) {
        return array(
            'id' => $id,
            'type' => 'wcpv_backend_product_search',
            'name' => $name,
            'desc' => $desc,
            'data_minimum_input_length' => $data_minimum_input_length,
            'data_action' => $data_action,
        );
    }

    public function tags($id, $name, $desc, $options) {
        return array(
            'id' => $id,
            'type' => 'multiselect',
            'name' => $name,
            'desc' => $desc,
            'class' => 'wcpv-select-tag',
            'options' => $options,
        );
    }

    public function categories($id, $name, $desc, $options) {
        return array(
            'id' => $id,
            'type' => 'multiselect',
            'name' => $name,
            'desc' => $desc,
            'class' => 'wcpv-select-category',
            'options' => $options,
        );
    }

    public function reset_button() {
        return array(
            'type' => 'wcpv_reset_button'
        );
    }

    public function reset_button_renderer() {
        ?>       
        <button class="wcpv-reset-button"><?php _e('Reset', 'woocommerce-products-visibility'); ?></button>
        <?php
    }

    public function form_close() {
        return array(
            'type' => 'wcpv_form_close'
        );
    }

    public function header_renderer() {
        ?>       
        <div class="wcpv_title"><?php echo __('Products Visibility', 'woocommerce-products-visibility') ?> </div>
        <div class="wcpv-toggle-roles-container">   
            <a href="javascript:;" class="wcpv-toggle-roles" title="<?php esc_html_e('Toggle row visibility', 'woocommerce-products-visibility'); ?>"></a>
        </div>  
        <?php
    }

    public function form_open_renderer() {
        echo "<div class='wcpv-form'>";
    }

    public function role_section_renderer($value) {
        ?>
        <h2 class="wcpv-role-sections" > <?php echo $value['name']; ?> <span class="ruleStatus-container"> (<span class="ruleStatus-text" > <?php echo __('Rule Active', 'woocommerce-products-visibility') ?>  </span>)</span> </h2>       
        <?php
    }

    public function subsection_renderer($value) {
        ?>
        <tr class="wcpv-subsection-row">
            <th class="titledesc wcpv-subsection-col" scope="row" colspan="2">                
                <div class="wcpv-subsection" > <?php echo $value['name']; ?>   </div> 
            </th>
        </tr>
        <?php
    }

    public function optimize_select2_product_search($value) {
        $wc_version_check = "3.0.0";
        if (( defined('WC_VERSION') && version_compare(WC_VERSION, $wc_version_check, '<') ) || ( isset($woocommerce->version) && version_compare($woocommerce->version, $wc_version_check, '<') )) {
            $this->select2_product_search_wc_smaller_3($value);
        } else {
            $this->select2_product_search_wc_bigger_3($value);
        }
    }

    public function select2_product_search_wc_bigger_3($value) {
        $available_data = get_option($value['id']);
        ?>
        <tr valign="top">
            <th class="titledesc" scope="row">                
                <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
            </th>
            <td class="forminp forminp-select">
                <input type="hidden"  id="<?php echo $value['id']; ?>" name="<?php echo $value['id']; ?>" value="<?php echo $available_data; ?>" class="wcpv-hidden-product-search">
                <select data-minimum_input_length="<?php echo $value['data_minimum_input_length']; ?>" multiple="multiple" class="wc-product-search wcpv-product-search" data-wcpv-products-input-id="<?php echo $value['id']; ?>"  style="width:330px;" data-placeholder="<?php echo $value['placeholder']; ?>" data-action="<?php echo $value['data_action']; ?>" ><?php
                    $get_datas = array();

                    if ($available_data) {
                        $ids = is_array($available_data) ? array_unique(array_filter(array_map('absint', (array) $available_data))) : array_unique(array_filter(array_map('absint', (array) explode(',', $available_data))));

                        foreach ($ids as $product_id) {
                            $product = wc_get_product($product_id);
                            if (is_object($product)) {
                                echo '<option value="' . esc_attr($product_id) . '"' . selected(true, true, false) . '>' . wp_kses_post($product->get_formatted_name()) . '</option>';
                            }
                        }
                    }
                    ?>   
                </select >
                <span class="description"><?php echo $value['desc']; ?></span>
            </td>
        </tr>
        <?php
    }

    public function select2_product_search_wc_smaller_3($value) {
        ?>
        <tr valign="top">
            <th class="titledesc" scope="row">                
                <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
            </th>
            <td class="forminp forminp-select">

                <input data-minimum_input_length="<?php echo $value['data_minimum_input_length']; ?>" type="hidden" class="wc-product-search wcpv-product-search" id="<?php echo $value['id']; ?>"  style="width:330px;" name="<?php echo $value['id']; ?>" data-placeholder="<?php echo $value['placeholder']; ?>" data-action="<?php echo $value['data_action']; ?>" data-multiple="true" data-selected="<?php
                $get_datas = array();
                $available_data = get_option($value['id']);

                if ($available_data) {
                    $ids = is_array($available_data) ? array_unique(array_filter(array_map('absint', (array) $available_data))) : array_unique(array_filter(array_map('absint', (array) explode(',', $available_data))));
                    foreach ($ids as $eachid) {
                        $getproductdata = wc_get_product($eachid);

                        if (!empty($getproductdata)) {
                            $get_datas[$eachid] = wp_kses_post($getproductdata->get_formatted_name());
                        }
                    }
                    if (!empty($get_datas))
                        echo esc_attr(json_encode($get_datas));
                }
                ?>" value="<?php echo implode(',', array_keys($get_datas)); ?>"/>                         
                <span class="description"><?php echo $value['desc']; ?></span>
            </td>
        </tr>
        <?php
    }

    public function form_close_renderer() {
        echo "</div>";
    }
    
    public function div_open() {
        return array(
            'type' => 'wcpv_div_open'
        );
    }
    
    public function div_close() {
        return array(
            'type' => 'wcpv_div_close'
        );
    }
    
    public function div_open_renderer() {
        echo "<div>";
    }
    
    public function div_close_renderer() {
        echo "</div>";
    }

    public function enable_multiroles($id, $name) {

        $options = array('0' => __('Disable', 'woocommerce-products-visibility'),
            '1' => __('Enable', 'woocommerce-products-visibility'));

        return array(
            'id' => $id,
            'type' => 'radio',
            'name' => $name,
            'std' => '0', // WooCommerce < 2.0
            'default' => '0', // WooCommerce >= 2.0s
            'options' => $options,
            'desc_tip' => __('Enable only if you have multiple roles per user', 'woocommerce-products-visibility'),
        );
    }
    
    public function show_product_through_direct_url($id, $name) {

        $options = array('0' => __('Disable', 'woocommerce-products-visibility'),
            '1' => __('Enable', 'woocommerce-products-visibility'));

        return array(
            'id' => $id,
            'type' => 'radio',
            'name' => $name,
            'std' => '0', // WooCommerce < 2.0
            'default' => '0', // WooCommerce >= 2.0s
            'options' => $options,
            'desc_tip' => __('Enable if you want to show the hidden products when visited through direct url', 'woocommerce-products-visibility'),
        );
    }

}
