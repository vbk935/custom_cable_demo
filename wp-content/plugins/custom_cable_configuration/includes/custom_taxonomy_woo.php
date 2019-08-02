<?php
add_action('init', 'create_configuration_hierarchical_taxonomy', 0);

function create_configuration_hierarchical_taxonomy() {
    $labels = array(
        'name' => _x('Configurations', 'taxonomy general name'),
        'singular_name' => _x('Configuration', 'taxonomy singular name'),
        'search_items' => __('Search Configurations'),
        'all_items' => __('All Configurations'),
        'parent_item' => __('Parent Configuration'),
        'parent_item_colon' => __('Parent Configuration:'),
        'edit_item' => __('Edit Configuration'),
        'update_item' => __('Update Configuration'),
        'add_new_item' => __('Add New Configuration'),
        'new_item_name' => __('New Topic Configuration'),
        'menu_name' => __('Configurations')
    );
    register_taxonomy('configuration', array(
        'product'
            ), array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array(
            'slug' => 'configuration'
        )
    ));
}

/* Create Taxonomy for Grouping */

add_action('init', 'create_group_hierarchical_taxonomy', 0);

function create_group_hierarchical_taxonomy() {
    $labels = array(
        'name' => _x('Groups', 'taxonomy general name'),
        'singular_name' => _x('Group', 'taxonomy singular name'),
        'search_items' => __('Search Groups'),
        'all_items' => __('All Groups'),
        'parent_item' => __('Parent Group'),
        'parent_item_colon' => __('Parent Group:'),
        'edit_item' => __('Edit Group'),
        'update_item' => __('Update Group'),
        'add_new_item' => __('Add New Group'),
        'menu_name' => __('Groups')
    );
    register_taxonomy('group', array(
        'product'
            ), array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array(
            'slug' => 'group'
        )
    ));
}

/* Add Custom Field For Configuration */

if(isset($_GET['config']))
     {
    //start session only here
     add_action('init', 'createSession');
  function createSession()
  {
      session_start();
  }
    //include the js file on configuration page only
    wp_enqueue_script('cableconfiguration', plugins_url('/js/latest_custom_jquery.js', __FILE__));
    //create a custom function to set the title for configuration page only  
    add_filter('wp_title','my_custom_post_title',16,1);
    function my_custom_post_title($title){
    global $post;
    $woocommerce_site_settings = get_option( 'canvas_title' );
    $post->post_title = apply_filters('the_title',$woocommerce_site_settings);  
    return $post->post_title;
     }

     }


function configuration_taxonomy_custom_fields($tag) {
    $t_id = $tag->term_id;
    $term_meta = get_option("taxonomy_term_$t_id");
    ?>
    <tr class="form-field">
        <th scope="row" valign="top">  
            <label for="presenter_id"><?php
                _e('Unit Name');
                ?></label>  
        </th>
        <td>  
            <input type="text" name="term_meta[unit_name]" id="term_meta[unit_name]" size="25" style="width:60%;" value="<?php
            echo $term_meta['unit_name'] ? $term_meta['unit_name'] : '';
            ?>"><br />  
            <span class="description"><?php
                _e('Unit Name');
                ?></span>  
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="presenter_id"><?php
                _e('Taxonomy Type');
                ?></label>
        </th>
        <td>
            <?php
            $tax_type = $term_meta['presenter_id'] ? $term_meta['presenter_id'] : '';
            ?>
            <select style=" margin-bottom: 18px; " name="term_meta[presenter_id]" id="term_meta[presenter_id]" class="postform">
                <option <?php
                if ($tax_type == "none") {
                    echo 'selected';
                }
                ?> value="none">None</option>
                <option <?php
                if ($tax_type == "changable") {
                    echo 'selected';
                }
                ?> value="changable">Changable</option>
            </select>
        </td>
    </tr>
    
    <tr class="form-field">
        <th scope="row" valign="top">  
            <label for="presenter_id"><?php
                _e('Canvas Image URL');
                ?></label>  
        </th>
        <td>  
            <input type="text" name="term_meta[image]" id="term_meta[image]" size="25" style="width:60%;" value="<?php
            echo $term_meta['image'] ? $term_meta['image'] : '';
            ?>"><br />  
            <span class="description"><?php
                _e('Image size should be as per canvas');
                ?></span>  
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top">  
            <label for="presenter_id"><?php
                _e('Canvas Coordinate X');
                ?></label>  
        </th>
        <td>  
            <input type="text" name="term_meta[coordinate_x]" id="term_meta[coordinate_x]" size="25" style="width:60%;" value="<?php
            echo $term_meta['coordinate_x'] ? $term_meta['coordinate_x'] : '';
            ?>"><br />  
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top">  
            <label for="presenter_id"><?php
                _e('Canvas Coordinate Y');
                ?></label>  
        </th>
        <td>  
            <input type="text" name="term_meta[coordinate_y]" id="term_meta[coordinate_y]" size="25" style="width:60%;" value="<?php
            echo $term_meta['coordinate_y'] ? $term_meta['coordinate_y'] : '';
            ?>"><br />  
        </td>
    </tr>
    <?php
    if (function_exists('wp_enqueue_media')) {
        wp_enqueue_media();
    } else {
        wp_enqueue_style('thickbox');
        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
    }
    ?>
    <tr class="form-field">
        <th scope="row" valign="top">  
            <label for="presenter_id"><?php
                _e('Part Image');
                ?></label>  
        </th>
        <td>  
            <?php
            $part_image = $term_meta['part_image'] ? $term_meta['part_image'] : '';
            ?>
            <img class="header_logo" src="<?php
            echo $part_image;
            ?>" height="100" width="100"/>
            <input class="header_logo_url" type="text" name="term_meta[part_image]" size="60" value="<?php
            echo $part_image;
            ?>">
            <a href="#" class="header_logo_upload">Upload</a>
        </td>
    </tr>

    <script>
        jQuery(document).ready(function($) {
            $('#watermark_logo_upload').click(function(e) {
                //console.log("watermark image");
                e.preventDefault();

                var custom_uploader = wp.media({
                    title: 'Watermark Logo',
                    button: {
                        text: 'Upload Watermark Logo'
                    },
                    multiple: false  // Set this to true to allow multiple files to be selected
                })
                        .on('select', function() {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    $('#watermark_logo').attr('src', attachment.url);
                    $('#watermark_logo_url').val(attachment.url);

                })
                        .open();
            });
            $('.header_logo_upload').click(function(e) {
                e.preventDefault();

                var custom_uploader = wp.media({
                    title: 'Custom Image',
                    button: {
                        text: 'Upload Image'
                    },
                    multiple: false  // Set this to true to allow multiple files to be selected
                })
                        .on('select', function() {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    $('.header_logo').attr('src', attachment.url);
                    $('.header_logo_url').val(attachment.url);

                })
                        .open();
            });
        });
    </script>

    <?php
}

function save_taxonomy_custom_fields($term_id) {
    if (isset($_POST['term_meta'])) {
        $t_id = $term_id;
        $term_meta = get_option("taxonomy_term_$t_id");
        $cat_keys = array_keys($_POST['term_meta']);
        foreach ($cat_keys as $key) {
            if (isset($_POST['term_meta'][$key])) {
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }
        update_option("taxonomy_term_$t_id", $term_meta);
    }
}

add_action('configuration_edit_form_fields', 'configuration_taxonomy_custom_fields', 10, 2);
add_action('configuration_add_form_fields', 'configuration_taxonomy_custom_fields', 10, 2);
add_action('edited_configuration', 'save_taxonomy_custom_fields', 10, 2);



/* Add Custom Field For Groups */

function group_taxonomy_custom_fields($tag) {
    $t_id = $tag->term_id;
    $term_meta = get_option("taxonomy_term_$t_id");
    ?>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="presenter_id"><?php
                _e('Configuration Type');
                ?></label>
        </th>
        <td>
            <?php
            $tax_type = $term_meta['presenter_id'] ? $term_meta['presenter_id'] : '';
            ?>
            <select style=" margin-bottom: 18px; " name="term_meta[presenter_id]" id="term_meta[presenter_id]" class="config_option postform">
                <option value="">- Select Configuration -</option>
                <option <?php if ($tax_type == "standard_cart") { echo 'selected'; } ?> value="standard_cart">Standard Cart</option>
                <option <?php  if ($tax_type == "configurable") { echo 'selected'; }?> value="configurable">Configurable</option>
            </select>
        </td>
    </tr>
    <tr class="form-field show_simple_product <?php
    if ($tax_type == 'standard_cart') {
        echo 'show';
    }
    ?>">
        <th scope="row" valign="top">
            <label for="presenter_id"><?php
                _e('Select Product');
                ?></label>
        </th>
        <td>
            <?php
            $tax_show_simple_product = $term_meta['show_simple_product'] ? $term_meta['show_simple_product'] : '';
            ?>
            <select style="margin-bottom: 18px; " name="term_meta[show_simple_product]" id="term_meta[show_simple_product]" class="postform">
                <?php
                $args = array(
                    'post_type' => 'product',
                    'posts_per_page' => -1
                );
                $the_query = new WP_Query($args);

                if ($the_query->have_posts()) {
                    ?>
                    <option value="">- Select Product -</option>
                    <?php
                    while ($the_query->have_posts()) {
                        $the_query->the_post();
                        ?>
                        <option <?php
                        if ($tax_show_simple_product == get_the_ID()) {
                            echo 'selected';
                        }
                        ?> value="<?php
                            echo get_the_ID();
                            ?>"><?php
                                echo get_the_title();
                                ?></option>
                        <?php
                    }
                    wp_reset_postdata();
                }
                ?>
            </select>
        </td>
    </tr>
    <tr class="form-field show_configuration <?php
    if ($tax_type == 'configurable') {
        echo 'show';
    }
    ?>">
        <th scope="row" valign="top">
            <label for="presenter_id"><?php
                _e('Select Configuration');
                ?></label>
        </th>
        <td>
            <?php
            $tax_show_configuration = $term_meta['show_configuration'] ? $term_meta['show_configuration'] : '';
            ?>
            <select style=" margin-bottom: 18px; " name="term_meta[show_configuration]" id="term_meta[show_configuration]" class="postform">
                <?php
                $terms = get_terms('configuration', array(
                    'hide_empty' => false,
                    'parent' => 0
                ));
                if (!empty($terms)) {
                    ?>
                    <option value="configurable">- Select Configuration -</option>
                    <?php
                    foreach ($terms as $term) {
                        ?>
                        <option <?php
                        if ($tax_show_configuration == $term->term_id) {
                            echo 'selected';
                        }
                        ?> value="<?php
                            echo $term->term_id;
                            ?>"><?php
                                echo $term->name;
                                ?></option>
                        <?php
                    }
                }
                ?>
            </select>
        </td>
    </tr>
    <script>
        jQuery(document).ready(function() {
            jQuery('.config_option').on('change', function() {

                var this_config = jQuery(this).val();

                if (this_config == 'standard_cart') {
                    jQuery('.show_configuration').removeClass('show');
                    jQuery('.show_simple_product').addClass('show');
                } else if (this_config == 'configurable') {
                    jQuery('.show_simple_product').removeClass('show');
                    jQuery('.show_configuration').addClass('show');
                } else {
                    jQuery('.show_simple_product').removeClass('show');
                    jQuery('.show_configuration').removeClass('show');
                }
                return false;
            });
        });
    </script>
    <?php
}

function save_taxonomy_custom_fields_group($term_id) {

    if (isset($_POST['term_meta'])) {
        $t_id = $term_id;
        $term_meta = get_option("taxonomy_term_$t_id");
        $cat_keys = array_keys($_POST['term_meta']);
        foreach ($cat_keys as $key) {
            if (isset($_POST['term_meta'][$key])) {
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }
        update_option("taxonomy_term_$t_id", $term_meta);
    }
}

add_action('group_edit_form_fields', 'group_taxonomy_custom_fields', 10, 2);
//add_action( 'group_add_form_fields', 'group_taxonomy_custom_fields', 10, 2 );
add_action('edited_group', 'save_taxonomy_custom_fields_group', 10, 2);




/* Product MetaBox */

function wpdocs_register_meta_boxes() {
    add_meta_box('meta-box-id', __('Product Configuration', 'textdomain'), 'wpdocs_my_display_callback', 'product');
}

add_action('add_meta_boxes', 'wpdocs_register_meta_boxes');

function wpdocs_my_display_callback($post) {
     ?>
    <div class="is_configurable">
        <?php
      $is_configurable = get_post_meta($post->ID, 'is_configurable', FALSE);
      if(is_array($is_configurable))
      {
         
           $is_configurable=$is_configurable[0];
       }
       else{
           
            $is_configurable=$is_configurable;
        }
        
    
       if (!empty($is_configurable)) {
            ?>
            <select name="is_configurable">
                <option <?php
                if ($is_configurable == 'no') {
                    echo 'selected';
                }
                ?> value="no">No</option>
                <option <?php
                if ($is_configurable == 'yes') {
                    echo 'selected';
                }
                ?> value="yes">Yes</option>
            </select>
            <?php
        }
        
        ?>
    </div>
    <?php
    
    if ($is_configurable == 'yes') {
        
        $hide = '';
    } else {
        
        $hide = 'hide';
    }
    ?>
    <div class="show_config <?php
    echo $hide;
    ?>">
             <?php
             $configuration_combination_raw = get_post_meta($post->ID, 'configuration_combination', TRUE);
             //$configuration_combination_raw = get_post_meta($post->ID, 'is_configurable', TRUE);
             
             if (!empty($configuration_combination_raw)) {
                 $configuration_combination = unserialize($configuration_combination_raw);
             }
              

             $per_unit_raw = get_post_meta($post->ID, 'per_unit', TRUE);
             if (!empty($per_unit_raw)) {
                 $per_unit = unserialize($per_unit_raw);
             }
if(isset($configuration_combination) and (!empty($configuration_combination)))
{
             $main_category_id = '';
             $child_data = '';
             foreach ($configuration_combination as $id => $data) {
                 $main_category_id = $id;
                 $child_data = $data;
             }
         }
             ?>
        <h2 class="create_config">Select Configuration</h2>
        <div class="row main_category">
            <?php
            $terms = get_terms('configuration', array(
                'hide_empty' => false,
                'parent' => 0
            ));

            if (!empty($terms)) {
                ?>
                <select class="main_category" name="main_category">
                    <?php
                    foreach ($terms as $term) {
                        ?>
                        <option <?php
                        if ($term->term_id == $main_category_id) {
                            echo 'selected';
                        }
                        ?> value="<?php
                            echo $term->term_id;
                            ?>"><?php
                                echo $term->name;
                                ?></option>
                        <?php
                    }
                    ?>
                </select>
                <?php
            }
            ?>
        </div>
        <div class="conditions_outer">
            <h2 class="select_condition">Add Condition</h2>
            <div class="conditions">
                <?php
                if (!empty($child_data)) {
                    foreach ($child_data as $child_parent_id => $child_id) {

                        $args_parent = array(
                            'hide_empty' => false,
                            'parent' => $main_category_id
                        );
                        $terms_parent = get_terms('configuration', $args_parent);

                        $args_child = array(
                            'hide_empty' => false,
                            'parent' => $child_parent_id
                        );
                        $terms_child = get_terms('configuration', $args_child);

                        if ((!empty($terms_parent)) && (!empty($terms_child))) {
                            ?>
                            <div class="condition">
                                <div class="child_parent_category_outer">
                                    <select onchange="add_child(this);
                        return false;" class="child_parent_category" name="child_parent[]">
                                        <option value="">- Select Configuration -</option>
                                        <?php
                                        foreach ($terms_parent as $child_parent) {
                                            ?>
                                            <option <?php
                                            if ($child_parent->term_id == $child_parent_id) {
                                                echo 'selected';
                                            }
                                            ?> value="<?php
                                                echo $child_parent->term_id;
                                                ?>"><?php
                                                    echo $child_parent->name;
                                                    ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="child_child_category_outer">
                                    <select class="child_child_category" onchange="check_unit_cost(this);
                        return false" name="child_child[]">
                                        <option value="">- Select Configuration -</option>
                                        <?php
                                        foreach ($terms_child as $child_data) {
                                            $t_id = $child_id;
                                            $term_meta = get_option("taxonomy_term_$t_id");
                                            $tax_type = $term_meta['presenter_id'] ? $term_meta['presenter_id'] : '';
                                            $class = '';
                                            $per_unit_name = '';
                                            $per_unit_style = '';
                                            $per_unit_value = '';
                                            if ($tax_type == 'changable') {

                                                $per_unit_style = 'style="display:block;"';
                                                $per_unit_name = 'name="per_unit[' . $t_id . ']"';
                                                $per_unit_value = $per_unit[$t_id];
                                            }

                                            $t_id = $child_data->term_id;
                                            $term_meta = get_option("taxonomy_term_$t_id");
                                            $tax_type = $term_meta['presenter_id'] ? $term_meta['presenter_id'] : '';
                                            if ($tax_type == 'changable') {
                                                $class = 'changable';
                                            }
                                            ?>
                                            <option class="<?php
                                            echo $class;
                                            ?>" data_id="<?php
                                                    echo $child_data->term_id;
                                                    ?>" <?php
                                                    if ($child_data->term_id == $child_id) {
                                                        echo 'selected';
                                                    }
                                                    ?> value="<?php
                                                    echo $child_data->term_id;
                                                    ?>"><?php
                                                        echo $child_data->name;
                                                        ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                    <div class="perunitcost" <?php
                                    echo $per_unit_style;
                                    ?>>
                                        <lable>Extra Per Unit Cost</lable>
                                        <input <?php
                                        echo $per_unit_name;
                                        ?> value="<?php
                                            echo $per_unit_value;
                                            ?>" class="per_unit_cost" type="text">
                                    </div>
                                </div>
                                <div class="add_case">
                                    <a onclick="remove_child_row(this);
                        return false;" class="remove_child_row" href="#">-</a>
                                    <a onclick="add_child_row(this);
                        return false;" class="add_child_row" href="#">+</a>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                    <?php
                }
                ?>
            </div>
            <div class="submit_button">
                <input type="submit" class="button button-primary button-large update_config" name="submit" value="Update"/>
            </div>
        </div>
    </div>
    <script>
        function check_unit_cost($this) {
            var $this_class = jQuery($this).find(":selected").attr('class');
            if ($this_class == 'changable') {
                var $this_data_id = jQuery($this).find(":selected").attr('data_id');
                var $this_input_name = 'per_unit[' + $this_data_id + ']';
                jQuery($this).parent().find('.perunitcost').find('input').attr('name', $this_input_name);
                jQuery($this).parent().find('.perunitcost').show();
            } else {
                jQuery($this).parent().find('.perunitcost').find('input').attr('name', '');
                jQuery($this).parent().find('.perunitcost').hide();
            }
            return false;
        }

        function add_child($this) {
            var parent_category_id = jQuery($this).val();

            jQuery.ajax({
                method: "POST",
                url: "<?php
            echo plugin_dir_url(__FILE__);
            ?>ajax/get_category_select.php",
                dataType: "html",
                data: {parent_category_id: parent_category_id},
                success: function(response) {
                    if (response == '') {
                        jQuery($this).parent().parent().find('.child_child_category_outer').html(response);
                    } else {
                        jQuery($this).parent().parent().find('.child_child_category_outer').html(response);
                    }
                }

            });
            return false;
        }

        function add_child_row() {
            var parent_category_id = jQuery('.main_category').find('select.main_category').val();
            jQuery.ajax({
                method: "POST",
                url: "<?php
            echo plugin_dir_url(__FILE__);
            ?>ajax/get_parent_category_select.php",
                dataType: "html",
                data: {parent_category_id: parent_category_id},
                success: function(response) {
                    if (response == '') {

                    } else {
                        jQuery('.conditions').append(response);
                    }
                }

            });
            return false;
        }

        function remove_child_row($this) {
            jQuery($this).parent().parent().remove();
            return false;
        }
        jQuery(document).ready(function() {

            jQuery('.update_config').click(function() {
                jQuery('#publishing-action').find('input').trigger('click');
                return false;
            });
            jQuery('select[name="is_configurable"]').on('change', function() {
                if (jQuery(this).val() == 'yes') {
                    jQuery('.show_config').show();
                } else {
                    jQuery('.show_config').hide();
                }

            });
            jQuery('.main_category').on('change', function() {
                var parent_category_id = jQuery(this).val();
                jQuery.ajax({
                    method: "POST",
                    url: "<?php
            echo plugin_dir_url(__FILE__);
            ?>ajax/get_parent_category_select.php",
                    dataType: "html",
                    data: {parent_category_id: parent_category_id},
                    success: function(response) {
                        jQuery('.conditions_outer').find('.conditions').empty();
                        if (response == '') {

                        } else {
                            jQuery('.conditions').append(response);
                        }
                    }

                });
                return false;
            });
        });
    </script>
    <?php
}

function wpdocs_save_meta_box($post_id) {
    if ($_POST['post_type'] == 'product') {
        $is_configurable = $_POST['is_configurable'];
        update_post_meta($post_id, 'is_configurable', $is_configurable);
        if ((!empty($_POST['main_category']) && (!empty($_POST['child_parent']))) && (!empty($_POST['child_child']))) {
            $this_configuration_combination = array();

            if (!empty($_POST['main_category'])) {
                $main_key = $_POST['main_category'];
                $this_configuration_combination[$main_key] = array();

                foreach ($_POST['child_parent'] as $key => $child_parent) {
                    if (!empty($_POST['child_child'][$key])) {
                        $this_configuration_combination[$main_key][$child_parent] = $_POST['child_child'][$key];
                    }
                }
            }
            if (!empty($this_configuration_combination)) {
                update_post_meta($post_id, 'configuration_combination', serialize($this_configuration_combination));
                if (!empty($_POST['per_unit'])) {
                    update_post_meta($post_id, 'per_unit', serialize($_POST['per_unit']));
                }
            }
        }
    }
}

add_action('save_post', 'wpdocs_save_meta_box');




/* Order MetaBox */

/**
 * Register meta box(es).
 */
function wpdocs_register_meta_boxes_order() {
    add_meta_box('meta-box-id', __('Order Tracking Number', 'textdomain'), 'wpdocs_my_display_callback_order', 'shop_order', 'side');
}

add_action('add_meta_boxes', 'wpdocs_register_meta_boxes_order');

/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 */
function wpdocs_my_display_callback_order($post) {
    $outline = '<label for="title_field" style="width:100%; display:inline-block;">' . esc_html__('Order Tracking Number', 'text-domain') . '</label>';
    $title_field = get_post_meta($post->ID, 'tracking_number', true);
    $outline .= '<input type="text" name="tracking_number" id="title_field" class="title_field" value="' . esc_attr($title_field) . '" style="width:100%;"/>';

    echo $outline;
}

/**
 * Save meta box content.
 *
 * @param int $post_id Post ID
 */
function wpdocs_save_meta_box_order($post_id) {
    if (!empty($_POST['tracking_number'])) {
        update_post_meta($post_id, 'tracking_number', $_POST['tracking_number']);
    }
}

add_action('save_post', 'wpdocs_save_meta_box_order');
