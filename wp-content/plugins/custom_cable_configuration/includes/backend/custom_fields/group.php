<?php if($_REQUEST['taxonomy'] == "group" || $_REQUEST['taxonomy'] == "configuration") { ?> 

<style type="text/css">
    .term-slug-wrap
    {
        display: none;
    }

</style>
<?php    
} 
function group_taxonomy_custom_fields($tag) 
{
$t_id = $tag->term_id;
$term_meta = get_option("taxonomy_term_$t_id");
?>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="presenter_id"><?php _e('Configuration Type'); ?></label>
        </th>
        <td>
            <?php $tax_type = $term_meta['presenter_id'] ? $term_meta['presenter_id'] : ''; ?>
            <select style=" margin-bottom: 18px; " name="term_meta[presenter_id]" id="term_meta[presenter_id]" class="config_option postform">
                <option <?php if ($tax_type == "configurable") { echo 'selected'; } ?> value="configurable">Configurable</option>
                <option <?php if ($tax_type == "standard_cart") { echo 'selected'; } ?> value="standard_cart">Product Category</option> 
                
            </select>
        </td>
    </tr>
    <div class="standard_cart hidden">
    <tr class="form-field show_simple_product <?php if ($tax_type == 'standard_cart') { echo 'show'; } ?>">
        <th scope="row" valign="top">
            <label for="presenter_id"><?php _e('Select Product Category'); ?></label>
        </th>
        <td>            
            <?php $tax_show_simple_product = $term_meta['show_simple_product'] ? $term_meta['show_simple_product'] : ''; ?>
            <select style=" margin-bottom: 18px; " name="term_meta[show_simple_product]" id="term_meta[show_simple_product]" class="postform product_cat">
                <?php
                    $args = array(
                         'taxonomy'     => 'product_cat',
                         'orderby'      => 'name',
                         'show_count'   => 0,
                         'pad_counts'   => 0,
                         'hierarchical' => 1,
                         'title_li'     => '',
                         'hide_empty'   => 0
                    );
                    $all_categories = get_categories( $args );                  
                ?>
                    <option value="">- Select Product Category-</option>
                    <?php
                        foreach ($all_categories as $cat) 
                        {
                            if($cat->category_parent == 0) 
                            {
                    ?>
                        <option value="<?php echo $cat->term_id; ?>" <?php if($tax_show_simple_product == $cat->term_id){ echo "selected"; } ?> ><?php echo $cat->name; ?></option>
                    <?php   
                                $args2 = array(
                                        'taxonomy'     => 'product_cat',
                                        'child_of'     => 0,
                                        'parent'       => $cat->term_id,
                                        'orderby'      => 'name',
                                        'show_count'   => 0,
                                        'pad_counts'   => 0,
                                        'hierarchical' => 1,
                                        'title_li'     => '',
                                        'hide_empty'   => 0
                                );
                                $sub_cats = get_categories( $args2 );
                                if($sub_cats) 
                                {
                                    foreach($sub_cats as $sub_category) 
                                    {
                    ?>
                                        <option value="<?php echo $sub_category->term_id; ?>" <?php if($tax_show_simple_product == $sub_category->term_id){ echo "selected"; } ?>>--<?php echo $sub_category->name; ?></option>
                    <?php
                                    }   
                                }
                            }
                        }                                           
                    ?>
            </select>
        </td>
    </tr>
    </div>
     
    <tr class="form-field">
        <th scope="row" valign="top">  
            <label for="presenter_id"><?php _e('Group Image URL'); ?></label>  
        </th>
        <td>  
            <a href="#" class="group_image_upload">Upload</a>
            <input class="group_image_url" type="text" name="term_meta[image]" id="term_meta[image]" size="25" style="width:60%;" value="<?php echo $term_meta['image'] ? $term_meta['image'] : ''; ?>">            
            <br />                  
        </td>
    </tr>
    
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="presenter_id"><?php _e('Restrict Role'); ?></label>
        </th>
        <td>			
            <?php $restrict_role = $term_meta['restrict_role'] ? $term_meta['restrict_role'] : ''; ?>
            <select style=" margin-bottom: 18px; " name="term_meta[restrict_role][]" id="term_meta[restrict_role]" class="postform" multiple>
				<option>-- Select --</option>
				<?php 
					global $wp_roles;
					$all_roles = $wp_roles->roles;
					$editable_roles = apply_filters('editable_roles', $all_roles);			
					foreach($editable_roles as $key=>$role){ 
				?>
					<option value="<?php echo $key; ?>" <?php if (in_array($key, $restrict_role)){ echo 'selected'; } ?>><?php echo $role['name']; ?></option>
				<?php } ?>
				<option value="guest_user" <?php if (in_array("guest_user", $restrict_role)){ echo 'selected'; } ?>>Guest User</option>
            </select>
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
    <script>
        jQuery(document).ready(function() {
            var ajaxPath= "<?php echo plugins_url().'/custom_cable_configuration/ajax/';?>";
            jQuery("#submit").click(function(){
                var submit_val = jQuery("#submit").val();               
                if(submit_val == "Update")
                {
                    var name = jQuery("#name").val();
                } 
                else if(submit_val == "Add New Group") 
                {           
                    var name = jQuery("#tag-name").val();
                }                
                var parent = jQuery("#parent").val();
                if(name == "")
                {
                    jQuery('.notice-success').hide();    
                    jQuery('.notice-error').show();
                    jQuery('.notice-error p').html("Please fill all the Required Fields.");
                    jQuery(window).scrollTop(0);
                }
                else
                {
                    if(submit_val == "Add New Group")
                    {
                    jQuery.ajax({       
                        method: "POST",
                        url:ajaxPath+"check_existing.php",                        
                        data: {'name':name, 'parent':parent, 'taxonomy_type':'group'},
                        success: function(response) 
                        {
                            var response = response.trim();
                            if(response == "matched")
                            {                                                              
                                jQuery('.notice-success').hide();     
                                jQuery('.updated').hide();       
                                jQuery('.notice-error').show();
                                jQuery('.notice-error p').html("A Group with same name and parent already exists.");
                                setTimeout(function(){ jQuery('.notice-success').fadeOut() }, 1000);   
                                jQuery(window).scrollTop(0);
                            }
                            else
                            {                                
                                jQuery('.notice-error').hide();  
                                jQuery('.updated').hide();             
                                jQuery('.notice-success').show();                                
                                jQuery('.notice-success p').html("Item Added.");
                                setTimeout(function(){ jQuery('.notice-success').fadeOut() }, 1000);   
                                jQuery('#tag-name').val('');
                                jQuery("#parent").val(jQuery("#parent option:first").val());
                                jQuery('#tag-description').val('');
                                jQuery(".config_option").val(jQuery(".config_option option:first").val());
                                jQuery(".product_cat").val(jQuery(".product_cat option:first").val());   
                                jQuery('.show_simple_product').removeClass('show');
                                jQuery('.standard_cart').removeClass('show');                    
                                jQuery('.standard_cart').addClass('hidden');
                                jQuery('.group_image_url').val('');
                                jQuery(window).scrollTop(0);
                            }
                        }
                    });
                    }
                }
                
            });
            jQuery('.config_option').on('change', function() {

                var this_config = jQuery(this).val();

                if (this_config == 'standard_cart') {                    
                    jQuery('.show_simple_product').addClass('show');
                    jQuery('.standard_cart').removeClass('hidden');
                    jQuery('.standard_cart').addClass('show');
                } else {
                    jQuery('.show_simple_product').removeClass('show');
                    jQuery('.standard_cart').removeClass('show');                    
                    jQuery('.standard_cart').addClass('hidden');                    
                }
                return false;
            });

            jQuery('.group_image_upload').click(function(e) {
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
                    jQuery('.group_image_url').val(attachment.url);
                })
                .open();
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
add_action('group_add_form_fields', 'group_taxonomy_custom_fields', 10, 2 );
add_action('edited_group', 'save_taxonomy_custom_fields_group', 10, 2);
add_action('create_group', 'save_taxonomy_custom_fields_group', 10, 2);
?>
