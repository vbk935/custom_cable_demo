<?php
class BeRocket_AAPF_Wizard {
    function __construct() {
        require_once dirname( __FILE__ ) . '/../wizard/setup-wizard.php';
        add_filter('berocket_wizard_steps_br-aapf-setup', array( __CLASS__, 'setup_wizard_steps'));
        add_action( 'before_wizard_run_br-aapf-setup', array( __CLASS__, 'set_wizard_js_css'));
        berocket_add_setup_wizard('br-aapf-setup', array('title' => __( 'AJAX Product Filters Setup Wizard', 'BeRocket_products_label_domain' )));
    }

    public static function set_wizard_js_css() {
        wp_enqueue_script('common');
        do_action('BeRocket_wizard_javascript');
    }

    public static function setup_wizard_steps($steps) {
        $steps = array(
            'wizard_selectors' => array(
                'name'    => __( 'Selectors', 'BeRocket_domain' ),
                'view'    => array( __CLASS__, 'wizard_selectors' ),
                'handler' => array( __CLASS__, 'wizard_selectors_save' ),
                'fa_icon' => 'fa-circle-o',
            ),
            'wizard_permalinks' => array(
                'name'    => __( 'URL', 'BeRocket_domain' ),
                'view'    => array( __CLASS__, 'wizard_permalinks' ),
                'handler' => array( __CLASS__, 'wizard_permalinks_save' ),
                'fa_icon' => 'fa-link',
            ),
            'wizard_count_reload' => array(
                'name'    => __( 'Attribute count', 'BeRocket_domain' ),
                'view'    => array( __CLASS__, 'wizard_count_reload' ),
                'handler' => array( __CLASS__, 'wizard_count_reload_save' ),
                'fa_icon' => 'fa-eye',
            ),
            'wizard_extra' => array(
                'name'    => __( 'Extra', 'BeRocket_domain' ),
                'view'    => array( __CLASS__, 'wizard_extra' ),
                'handler' => array( __CLASS__, 'wizard_extra_save' ),
                'fa_icon' => 'fa-cogs',
            ),
            'wizard_end' => array(
                'name'    => __( 'Ready!', 'BeRocket_domain' ),
                'view'    => array( __CLASS__, 'wizard_ready' ),
                'handler' => array( __CLASS__, 'wizard_ready_save' ),
                'fa_icon' => 'fa-check',
            ),
        );
        return $steps;
    }

    public static function wizard_selectors($wizard) {
        $option = BeRocket_AAPF::get_aapf_option();
        ?>
        <form method="post" class="br_framework_submit_form">
            <div class="nav-block berocket_framework_menu_general-block nav-block-active">
                <div>
                    <h3><?php _e('IMPORTANT', 'BeRocket_AJAX_domain') ?></h3>
                    <p><?php _e('Selectors can be different for each theme. Please setup correct selectors, otherwise plugin can doesn\'t work or some features can work incorrect', 'BeRocket_AJAX_domain') ?></p>
                    <p><?php _e('You can try to setup it via "Auto-selectors" and plugin will try get selectors for your theme, this take a while.', 'BeRocket_AJAX_domain') ?></p>
                    <p><?php _e('Manually you can check selectors on your shop page or contact <strong>theme author</strong> with question about it.', 'BeRocket_AJAX_domain') ?></p>
                    <p><?php _e('Also theme with Isotope/Masonry or any type of the image Lazy-Load required custom JavaScript. Please contact your <strong>theme author</strong> to get correct JavaScript code for it', 'BeRocket_AJAX_domain') ?></p>
                    <p><?php _e('JavaScript for some theme you can find in <a href="http://berocket.com/docs/plugin/woocommerce-ajax-products-filter#theme_setup" target="_blank">BeRocket Documentation</a>', 'BeRocket_AJAX_domain') ?></p>
                </div>
                <table class="framework-form-table berocket_framework_menu_selectors">
                    <tbody>
                        <tr style="display: table-row;">
                            <th scope="row"><?php _e('Get selectors automatically', 'BeRocket_AJAX_domain') ?></th>
                            <td>
                                <div>
                                    <h4><?php _e('How it work:', 'BeRocket_AJAX_domain'); ?></h4>
                                    <ol>
                                        <li><?php _e('Run Auto-selector', 'BeRocket_AJAX_domain') ?></li>
                                        <li><?php _e('Wait until end <strong style="color:red;">do not close this page</strong>', 'BeRocket_AJAX_domain') ?></li>
                                        <li><?php _e('Save settings with new selectors', 'BeRocket_AJAX_domain') ?></li>
                                    </ol>
                                </div>
                                <?php echo BeRocket_wizard_generate_autoselectors(array(
                                    'products' => '.berocket_aapf_products_selector',
                                    'pagination' => '.berocket_aapf_pagination_selector',
                                    'result_count' => '.berocket_aapf_product_count_selector')); ?>
                            </td>
                        </tr>
                        <tr style="display: table-row;">
                            <th scope="row">Products Container Selector</th>
                            <td><label>
                                <input type="text" name="berocket_aapf_wizard_settings[products_holder_id]" 
                                    value="<?php if( ! empty($option['products_holder_id']) ) echo $option['products_holder_id']; ?>" 
                                    class="berocket_aapf_products_selector">
                            </label></td>
                        </tr>
                        <tr style="display: table-row;">
                            <th scope="row">Pagination Selector</th>
                            <td><label>
                                <input type="text" name="berocket_aapf_wizard_settings[woocommerce_pagination_class]" 
                                    value="<?php if( ! empty($option['woocommerce_pagination_class']) ) echo $option['woocommerce_pagination_class']; ?>" 
                                    class="berocket_aapf_pagination_selector">
                            </label></td>
                        </tr>
                        <tr style="display: table-row;">
                            <th scope="row"><?php _e('Product count selector', 'BeRocket_AJAX_domain') ?></th>
                            <td><label>
                                <input type="text" name="berocket_aapf_wizard_settings[woocommerce_result_count_class]" 
                                    value="<?php if( ! empty($option['woocommerce_result_count_class']) ) echo $option['woocommerce_result_count_class']; ?>" 
                                    class="berocket_aapf_product_count_selector">
                            </label></td>
                        </tr>
                        <tr style="display: table-row;">
                            <th scope="row"><?php _e('Product order by selector', 'BeRocket_AJAX_domain') ?></th>
                            <td><label>
                                <input type="text" name="berocket_aapf_wizard_settings[woocommerce_ordering_class]" 
                                    value="<?php if( ! empty($option['woocommerce_ordering_class']) ) echo $option['woocommerce_ordering_class']; ?>" 
                                    class="">
                            </label></td>
                        </tr>
                        <tr style="display: table-row;">
                            <th scope="row"><?php _e('Removed elements', 'BeRocket_AJAX_domain') ?></th>
                            <td>
                                <p><?php _e('Select elements that your shop page doesn\'t have to prevent any errors with it', 'BeRocket_AJAX_domain') ?></p>
                                <div><label>
                                    <input type="checkbox" name="berocket_aapf_wizard_settings[woocommerce_removes][result_count]" value="1"
                                    <?php if( ! empty($option['woocommerce_removes']['result_count']) ) echo " checked"; ?>>
                                    <?php _e('Products count', 'BeRocket_AJAX_domain') ?>
                                </label></div>
                                <div><label>
                                    <input type="checkbox" name="berocket_aapf_wizard_settings[woocommerce_removes][ordering]" value="1"
                                    <?php if( ! empty($option['woocommerce_removes']['ordering']) ) echo " checked"; ?>>
                                    <?php _e('Products order by drop down', 'BeRocket_AJAX_domain') ?>
                                </label></div>
                                <div><label>
                                    <input type="checkbox" name="berocket_aapf_wizard_settings[woocommerce_removes][pagination]" value="1"
                                    <?php if( ! empty($option['woocommerce_removes']['pagination']) ) echo " checked"; ?>>
                                    <?php _e('Pagination', 'BeRocket_AJAX_domain') ?>
                                </label></div>
                            </td>
                        </tr>
                        <tr style="display: table-row;">
                            <td colspan="2">
                                <a href="#custom-js-css" class="wizard_custom_js_css_open"><?php _e('You need some custom JavaScript/CSS code?', 'BeRocket_AJAX_domain') ?></a>
                                <div class="wizard_custom_js_css" style="display: none;">
                                    <h3><?php _e('User custom CSS style', 'BeRocket_AJAX_domain') ?></h3>
                                    <textarea name="berocket_aapf_wizard_settings[user_custom_css]"><?php echo br_get_value_from_array($option, array('user_custom_css')) ?></textarea>
                                    <h3><?php _e('JavaScript Before Products Update', 'BeRocket_AJAX_domain') ?></h3>
                                    <textarea name="berocket_aapf_wizard_settings[user_func][before_update]"><?php echo br_get_value_from_array($option, array('user_func', 'before_update')) ?></textarea>
                                    <h3><?php _e('JavaScript On Products Update', 'BeRocket_AJAX_domain') ?></h3>
                                    <textarea name="berocket_aapf_wizard_settings[user_func][on_update]"><?php echo br_get_value_from_array($option, array('user_func', 'on_update')) ?></textarea>
                                    <h3><?php _e('JavaScript After Products Update', 'BeRocket_AJAX_domain') ?></h3>
                                    <textarea name="berocket_aapf_wizard_settings[user_func][after_update]"><?php echo br_get_value_from_array($option, array('user_func', 'after_update')) ?></textarea>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <script>
            jQuery(document).on('click', '.wizard_custom_js_css_open', function(event) {
                event.preventDefault();
                jQuery(this).hide();
                jQuery('.wizard_custom_js_css').show();
            });
            </script>
            <?php wp_nonce_field( $wizard->page_id ); ?>
            <p class="next-step">
                <input type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e( "Next Step", 'BeRocket_AJAX_domain' ); ?>" name="save_step" />
            </p>
        </form>
        <?php
    }

    public static function wizard_selectors_save($wizard) {
        check_admin_referer( $wizard->page_id );
        $option = BeRocket_AAPF::get_aapf_option();
        if( ! empty($_POST['berocket_aapf_wizard_settings']) && is_array($_POST['berocket_aapf_wizard_settings']) ) {
            $new_option = array_merge(
                array('woocommerce_removes' => array('pagination' => '', 'result_count' => '', 'ordering' => '')), 
                $_POST['berocket_aapf_wizard_settings']
            );
            $option = array_merge($option, $new_option);
        }
        $BeRocket_AAPF = BeRocket_AAPF::getInstance();
        $option = $BeRocket_AAPF->sanitize_option($option);
        update_option( 'br_filters_options', $option );
        $wizard->redirect_to_next_step();
    }

    public static function wizard_permalinks($wizard) {
        $option = BeRocket_AAPF::get_aapf_option();
        ?>
        <form method="post" class="br_framework_submit_form">
            <div class="nav-block berocket_framework_menu_general-block nav-block-active">
                <table class="framework-form-table berocket_framework_menu_selectors">
                    <tbody>
                        <tr style="display: table-row;">
                            <th scope="row"><?php _e('SEO friendly URLs', 'BeRocket_AJAX_domain') ?></th>
                            <td>
                                <input class="berocket_wizard_seo_friendly" name="berocket_aapf_wizard_settings[seo_friendly_urls]" type="checkbox" value="1"<?php if( ! empty($option['seo_friendly_urls']) ) echo ' checked'?>>
                                <?php _e('Page URL will be changed after filtering.', 'BeRocket_AJAX_domain') ?>
                            </td>
                        </tr>
                        <tr style="display: table-row;">
                            <td colspan="2">
                                <p><?php _e('Without this option after page reload filter will be unselected', 'BeRocket_AJAX_domain') ?></p>
                                <p><?php _e('Also back button doesn\'t load previous selected filters', 'BeRocket_AJAX_domain') ?></p>
                            </td>
                        </tr>
                        <tr class="berocket_wizard_only_seo_friendly"<?php if( empty($option['seo_friendly_urls']) ) echo ' style="display:none;"'?>>
                            <th scope="row"><?php _e('Use slug', 'BeRocket_AJAX_domain') ?></th>
                            <td>
                                <input name="berocket_aapf_wizard_settings[slug_urls]" type="checkbox" value="1"<?php if( ! empty($option['slug_urls']) ) echo ' checked'?>>
                                <?php _e('Replace attribute values ID to Slug in SEO friendly URLs.', 'BeRocket_AJAX_domain') ?>
                            </td>
                        </tr>
                        <tr class="berocket_wizard_only_seo_friendly"<?php if( empty($option['seo_friendly_urls']) ) echo ' style="display:none;"'?>>
                            <td colspan="2">
                                <p><?php _e('<strong>IMPORTANT</strong> Please check that Slug for all attribute values without those symbols - _ +', 'BeRocket_AJAX_domain') ?></p>
                            </td>
                        </tr>
                        <tr class="berocket_wizard_only_seo_friendly"<?php if( empty($option['seo_friendly_urls']) ) echo ' style="display:none;"'?>>
                            <th scope="row"><?php _e('Nice permalink URL', 'BeRocket_AJAX_domain') ?></th>
                            <td>
                                <input class="berocket_wizard_nice_urls" name="berocket_aapf_wizard_settings[nice_urls]" type="checkbox" value="1"<?php if( ! empty($option['nice_urls']) ) echo ' checked'?>>
                                <?php _e('Use WordPress permalinks instead GET query', 'BeRocket_AJAX_domain') ?>
                            </td>
                        </tr>
                        <tr class="berocket_wizard_only_seo_friendly"<?php if( empty($option['seo_friendly_urls']) ) echo ' style="display:none;"'?>>
                            <td colspan="2">
                                <p><?php _e('<strong>IMPORTANT</strong> WordPress permalinks must be set to Post name(Custom structure: /%postname%/ )', 'BeRocket_AJAX_domain') ?></p>
                                <p><?php _e('Not working on any custom page(Generated with any page builder or with help of shortcodes)', 'BeRocket_AJAX_domain') ?></p>
                                <p><?php _e('Not compatible with any other plugin that change WooCommerce permalinks', 'BeRocket_AJAX_domain') ?></p>
                            </td>
                        </tr>
                        <tr class="berocket_wizard_only_seo_friendly"<?php if( empty($option['seo_friendly_urls']) ) echo ' style="display:none;"'?>>
                            <td colspan="2" class="berocket_wizard_only_nice_urls"<?php if( empty($option['nice_urls']) ) echo ' style="display:none;"'?>>
                                <?php 
                                $BeRocket_AAPF = BeRocket_AAPF::getInstance();
                                $BeRocket_AAPF->br_get_template_part( 'permalink_option' );
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <script>
            jQuery(document).on('change', '.berocket_wizard_seo_friendly', function() {
                if( jQuery(this).prop('checked') ) {
                    jQuery('.berocket_wizard_only_seo_friendly').show();
                } else {
                    jQuery('.berocket_wizard_only_seo_friendly').hide();
                }
            });
            jQuery(document).on('change', '.berocket_wizard_nice_urls', function() {
                if( jQuery(this).prop('checked') ) {
                    jQuery('.berocket_wizard_only_nice_urls').show();
                } else {
                    jQuery('.berocket_wizard_only_nice_urls').hide();
                }
            });
            </script>
            <?php wp_nonce_field( $wizard->page_id ); ?>
            <p class="next-step">
                <input type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e( "Next Step", 'BeRocket_AJAX_domain' ); ?>" name="save_step" />
            </p>
        </form>
        <?php
    }

    public static function wizard_permalinks_save($wizard) {
        check_admin_referer( $wizard->page_id );
        $option = BeRocket_AAPF::get_aapf_option();
        if( empty($_POST['berocket_aapf_wizard_settings']) || ! is_array($_POST['berocket_aapf_wizard_settings']) ) {
            $_POST['berocket_aapf_wizard_settings'] = array();
        }
        $option_new = array_merge(array('seo_friendly_urls' => '', 'slug_urls' => '', 'nice_urls' => ''), $_POST['berocket_aapf_wizard_settings']);
        if( empty($option_new['seo_friendly_urls']) ) {
            $option_new['slug_urls'] = '';
            $option_new['nice_urls'] = '';
        }
        $BeRocket_AAPF = BeRocket_AAPF::getInstance();
        if( ! empty($option_new['nice_urls']) ) {
            if( ! empty($_POST['berocket_permalink_option']) && is_array($_POST['berocket_permalink_option']) ) {
                $default_values = $BeRocket_AAPF->default_permalink;
                $BeRocket_AAPF->save_permalink_option($default_values);
            }
        }
        $option = array_merge($option, $option_new);
        $option = $BeRocket_AAPF->sanitize_option($option);
        update_option( 'br_filters_options', $option );
        $wizard->redirect_to_next_step();
    }

    public static function wizard_count_reload($wizard) {
        $option = BeRocket_AAPF::get_aapf_option();
        ?>
        <form method="post" class="br_framework_submit_form">
            <div class="nav-block berocket_framework_menu_general-block nav-block-active">
                <table class="framework-form-table berocket_framework_menu_selectors">
                    <tbody>
                        <tr style="display: table-row;">
                            <th scope="row"><?php _e('Presets', 'BeRocket_AJAX_domain') ?></th>
                            <td>
                                <p><?php _e('You can select some preset or setup each settings manually', 'BeRocket_AJAX_domain') ?></p>
                                <select class="attribute_count_preset">
                                    <option value="custom"><?php _e('Custom', 'BeRocket_AJAX_domain') ?></option>
                                    <option value="show_all"><?php _e('Show all attributes (very fast)', 'BeRocket_AJAX_domain') ?></option>
                                    <option value="hide_page"><?php _e('Hide empty attributes by page (fast)', 'BeRocket_AJAX_domain') ?></option>
                                    <option value="hide_empty"><?php _e('Hide empty attribute by filters (slow)', 'BeRocket_AJAX_domain') ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr class="attribute_count_preset_info attribute_count_preset_info_show_all" style="display: none;" data-count="1">
                            <td colspan="2">
                                <h4><?php _e('Show all attributes', 'BeRocket_AJAX_domain') ?></h4>
                                <p><?php _e('Display all attribute values, including attribute values without products, but work realy fast. If you have a lot of products(2000 and more) then it will be better solution. But check that all attribute values in your shop has attribute', 'BeRocket_AJAX_domain') ?></p>
                            </td>
                        </tr>
                        <tr class="attribute_count_preset_info attribute_count_preset_info_hide_page" style="display: none;" data-count="26">
                            <td colspan="2">
                                <h4><?php _e('Hide empty attributes by page', 'BeRocket_AJAX_domain') ?></h4>
                                <p><?php _e('Display only attribute values with products, but do not check selected filters. Any first selected filter will return products, but next filters can return "no products" message', 'BeRocket_AJAX_domain') ?></p>
                            </td>
                        </tr>
                        <tr class="attribute_count_preset_info attribute_count_preset_info_hide_empty" style="display: none;" data-count="58">
                            <td colspan="2">
                                <h4><?php _e('Hide empty attribute by filters', 'BeRocket_AJAX_domain') ?></h4>
                                <p><?php _e('Display only attribute values with products. Only attribute values with products will be used, also after any filtering. But it work slow, because recount products for each attribute. Can work slow on bad server and with a lot of products', 'BeRocket_AJAX_domain') ?></p>
                            </td>
                        </tr>
                        <tr style="display: table-row;">
                            <th scope="row"><?php _e('Show all values', 'BeRocket_AJAX_domain') ?></th>
                            <td>
                                <label><input name="berocket_aapf_wizard_settings[show_all_values]" class="attribute_count_preset_1" type="checkbox" value="1"
                                <?php if( ! empty($option['show_all_values']) ) echo " checked"; ?>>
                                <?php _e('Check if you want to show not used attribute values too', 'BeRocket_AJAX_domain') ?>
                                </label>
                            </td>
                        </tr>
                        <tr style="display: table-row;">
                            <td colspan="2">
                                <?php _e('Uses all attribute values in filters, uses also values without products for your shop. Can fix some problems with displaying filters on pages', 'BeRocket_AJAX_domain') ?>
                            </td>
                        </tr>
                        <tr style="display: table-row;">
                            <th scope="row"><?php _e('Hide values', 'BeRocket_AJAX_domain') ?></th>
                            <td>
                                <div><label><input name="berocket_aapf_wizard_settings[hide_value][o]" class="attribute_count_preset_2" type="checkbox" value="1"
                                <?php if( ! empty($option['hide_value']['o']) ) echo " checked"; ?>>
                                <?php _e('Hide values without products', 'BeRocket_AJAX_domain') ?>
                                </label></div>
                                <div><label><input name="berocket_aapf_wizard_settings[hide_value][sel]" class="attribute_count_preset_4" type="checkbox" value="1"
                                <?php if( ! empty($option['hide_value']['sel']) ) echo " checked"; ?>>
                                <?php _e('Hide selected values', 'BeRocket_AJAX_domain') ?>
                                </label></div>
                                <div><label><input name="berocket_aapf_wizard_settings[hide_value][empty]" class="attribute_count_preset_8" type="checkbox" value="1"
                                <?php if( ! empty($option['hide_value']['empty']) ) echo " checked"; ?>>
                                <?php _e('Hide empty widget', 'BeRocket_AJAX_domain') ?>
                                </label></div>
                                <?php do_action('berocket_aapf_wizard_attribute_count_hide_values', $option); ?>
                            </td>
                        </tr>
                        <tr style="display: table-row;">
                            <th scope="row"><?php _e('Reload amount of products', 'BeRocket_AJAX_domain') ?></th>
                            <td>
                                <label><input name="berocket_aapf_wizard_settings[recount_products]" class="attribute_count_preset_32" type="checkbox" value="1"
                                <?php if( ! empty($option['recount_products']) ) echo " checked"; ?>>
                                <?php _e('Use filters on products count display', 'BeRocket_AJAX_domain') ?>
                                </label>
                            </td>
                        </tr>
                        <tr style="display: table-row;">
                            <td colspan="2">
                                <?php _e('Slow down site load speed, because uses additional query for each filter. Replaces attribute values using selected filters to use correct count of products for each value. "Hide values without products" option do not work without this option. Also uses to display correct products count with attribute values after filtering', 'BeRocket_AJAX_domain') ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <script>
                jQuery(document).on('change', '.attribute_count_preset', function() {
                    jQuery('.attribute_count_preset_info').hide();
                    jQuery('.attribute_count_preset_info_'+jQuery(this).val()).show();
                    var data_count = jQuery('.attribute_count_preset_info_'+jQuery(this).val()).data('count');
                    if( data_count ) {
                        var data_counts = [32,16,8,4,2,1];
                        data_counts.forEach(function( item, i, arr) {
                            if( data_count >= item ) {
                                jQuery('.attribute_count_preset_'+item).prop('checked', true);
                                data_count = data_count - item;
                            } else {
                                jQuery('.attribute_count_preset_'+item).prop('checked', false);
                            }
                        });
                    }
                });
                jQuery(document).on('change', '.attribute_count_preset_1, .attribute_count_preset_2, .attribute_count_preset_4, .attribute_count_preset_8, .attribute_count_preset_16, .attribute_count_preset_32', function() {
                    jQuery('.attribute_count_preset').val('custom').trigger('change');
                });
            </script>
            <?php wp_nonce_field( $wizard->page_id ); ?>
            <p class="next-step">
                <input type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e( "Next Step", 'BeRocket_AJAX_domain' ); ?>" name="save_step" />
            </p>
        </form>
        <?php
    }

    public static function wizard_count_reload_save($wizard) {
        check_admin_referer( $wizard->page_id );
        $option = BeRocket_AAPF::get_aapf_option();
        if( empty($_POST['berocket_aapf_wizard_settings']) || ! is_array($_POST['berocket_aapf_wizard_settings']) ) {
            $_POST['berocket_aapf_wizard_settings'] = array();
        }
        $option_new = array_merge(array('show_all_values' => '', 'hide_value' => array('o' => '', 'sel' => '', 'empty' => '', 'button' => ''), 'recount_products' => ''), $_POST['berocket_aapf_wizard_settings']);
        $option = array_merge($option, $option_new);
        $BeRocket_AAPF = BeRocket_AAPF::getInstance();
        $option = $BeRocket_AAPF->sanitize_option($option);
        update_option( 'br_filters_options', $option );
        $wizard->redirect_to_next_step();
    }

    public static function wizard_extra($wizard) {
        $option = BeRocket_AAPF::get_aapf_option();
        ?>
        <form method="post" class="br_framework_submit_form">
            <div class="nav-block berocket_framework_menu_general-block nav-block-active">
                <table class="framework-form-table berocket_framework_menu_selectors">
                    <tbody>
                        <tr style="display: table-row;">
                            <th scope="row"><?php _e('Jump to first page', 'BeRocket_AJAX_domain') ?></th>
                            <td>
                                <label><input name="berocket_aapf_wizard_settings[first_page_jump]" type="checkbox" value="1"
                                <?php if( ! empty($option['first_page_jump']) ) echo " checked"; ?>>
                                <?php _e('Load first page after any filter changes. Can fix some problem with "no products" message after filtering', 'BeRocket_AJAX_domain') ?>
                                </label>
                            </td>
                        </tr>
                        <tr style="display: table-row;">
                            <th scope="row"><?php _e('Scroll page to the top', 'BeRocket_AJAX_domain') ?></th>
                            <td>
                                <label><input name="berocket_aapf_wizard_settings[scroll_shop_top]" type="checkbox" value="1"
                                <?php if( ! empty($option['scroll_shop_top']) ) echo " checked"; ?>>
                                <?php _e('Check if you want scroll page to the top of shop after filters change', 'BeRocket_AJAX_domain') ?>
                                </label>
                            </td>
                        </tr>
                        <tr style="display: table-row;">
                            <th scope="row"><?php _e('Show selected filters', 'BeRocket_AJAX_domain') ?></th>
                            <td>
                                <label><input name="berocket_aapf_wizard_settings[selected_area_show]" type="checkbox" value="1"
                                <?php if( ! empty($option['selected_area_show']) ) echo " checked"; ?>>
                                <?php _e('Show selected filters above products. Also you can use widget to show selected filters', 'BeRocket_AJAX_domain') ?>
                                </label>
                            </td>
                        </tr>
                        <tr style="display: table-row;">
                            <th scope="row"><?php _e('Display products', 'BeRocket_AJAX_domain') ?></th>
                            <td>
                                <label><input name="berocket_aapf_wizard_settings[products_only]" type="checkbox" value="1"
                                <?php if( ! empty($option['products_only']) ) echo " checked"; ?>>
                                <?php _e('Display always products when filters selected. Use this when you have categories and subcategories on shop pages, but you want to display products on filtering', 'BeRocket_AJAX_domain') ?>
                                </label>
                            </td>
                        </tr>
                        <tr style="display: table-row;">
                            <th scope="row"><?php _e('Search page fix', 'BeRocket_AJAX_domain') ?></th>
                            <td>
                                <label><input name="berocket_aapf_wizard_settings[search_fix]" type="checkbox" value="1"
                                <?php if( ! empty($option['search_fix']) ) echo " checked"; ?>>
                                <?php _e('Disable redirection, when search page return only one product<br>Enable it only if you will use filters on search page', 'BeRocket_AJAX_domain') ?>
                                </label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <script>
            </script>
            <?php wp_nonce_field( $wizard->page_id ); ?>
            <p class="next-step">
                <input type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e( "Next Step", 'BeRocket_AJAX_domain' ); ?>" name="save_step" />
            </p>
        </form>
        <?php
    }

    public static function wizard_extra_save($wizard) {
        check_admin_referer( $wizard->page_id );
        $option = BeRocket_AAPF::get_aapf_option();
        if( empty($_POST['berocket_aapf_wizard_settings']) || ! is_array($_POST['berocket_aapf_wizard_settings']) ) {
            $_POST['berocket_aapf_wizard_settings'] = array();
        }
        $option_new = array_merge(array('first_page_jump' => '', 'scroll_shop_top' => '', 'selected_area_show' => '', 'products_only' => '', 'search_fix' => ''), $_POST['berocket_aapf_wizard_settings']);
        $option = array_merge($option, $option_new);
        $BeRocket_AAPF = BeRocket_AAPF::getInstance();
        $option = $BeRocket_AAPF->sanitize_option($option);
        update_option( 'br_filters_options', $option );
        $wizard->redirect_to_next_step();
    }

    public static function wizard_ready($wizard) {
        $option = BeRocket_AAPF::get_aapf_option();
        ?>
        <form method="post" class="br_framework_submit_form">
            <div class="nav-block berocket_framework_menu_general-block nav-block-active">
                <h2><?php _e('Plugin ready to use', 'BeRocket_AJAX_domain') ?></h2>
                <div><iframe width="560" height="315" src="https://www.youtube.com/embed/8gaMj-IxUj0?rel=0&amp;showinfo=0" frameborder="0" gesture="media" allow="encrypted-media" allowfullscreen></iframe></div>
                <h4><?php _e('Widget', 'BeRocket_AJAX_domain') ?></h4>
                <p><?php _e('Now you can add widgets AJAX Product Filters to your side bar', 'BeRocket_AJAX_domain') ?></p>
                <p><?php _e('More information about widget options you can get on <a target="_blank" href="http://berocket.com/docs/plugin/woocommerce-ajax-products-filter#widget">BeRocket Documentation</a>', 'BeRocket_AJAX_domain') ?></p>
                <?php
                $old_filter_widgets = get_option('widget_berocket_aapf_widget');
                if( ! is_array($old_filter_widgets) ) {
                    $old_filter_widgets = array();
                }
                foreach ($old_filter_widgets as $key => $value) {
                    if (!is_numeric($key)) {
                        unset($old_filter_widgets[$key]);
                    }
                }
                $html = '';
                if( count($old_filter_widgets) ) {
                    $html = '<h3>' . __('Replace old widgets', 'BeRocket_AJAX_domain') . '</h3>
                    <div>';
                        $html .= '<span 
                            class="button berocket_replace_deprecated_with_new"
                            data-ready="' . __('Widget replaced', 'BeRocket_AJAX_domain') . '"
                            data-loading="' . __('Replacing widgets... Please wait', 'BeRocket_AJAX_domain') . '"';
                            $html .= '>' . __('Replace widgets', 'BeRocket_AJAX_domain');
                        $html .= '</span>
                        <p>' . __('Replace deprecated widgets with new single filter widgets', 'BeRocket_AJAX_domain') . '</p>
                        <script>
                            jQuery(".berocket_replace_deprecated_with_new").click(function() {
                                var $this = jQuery(this);
                                if( ! $this.is(".berocket_ajax_sending") ) {
                                    $this.data("text", $this.text());
                                    $this.attr("disabled", "disabled");
                                    $this.text($this.data("loading"));
                                    $this.addClass("berocket_ajax_sending");
                                    jQuery.post("'.admin_url('admin-ajax.php').'", {action:"replace_deprecated_with_new"}, function() {
                                        $this.text($this.data("ready"));
                                    });
                                }
                            });
                        </script>
                    </div>';
                }
                echo $html;
                ?>
            </div>
            <?php 
            wp_nonce_field( $wizard->page_id ); ?>
            <p class="next-step">
                <input type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e( "Create new filter right now", 'BeRocket_AJAX_domain' ); ?>" name="save_step" />
            </p>
        </form>
        <?php
    }

    public static function wizard_ready_save($wizard) {
        check_admin_referer( $wizard->page_id );
        $BeRocket_AAPF = BeRocket_AAPF::getInstance();
        wp_redirect( admin_url( 'post-new.php?post_type=br_product_filter&aapf=singlewizard' ) );
    }
}
