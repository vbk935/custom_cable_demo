<?php if($_REQUEST['taxonomy'] == "configuration") { ?>
<style type="text/css">
    .term-parent-wrap
    {
        display: none;
    }
</style>
<?php
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
    wp_enqueue_script('cableconfiguration', plugins_url('../../../js/latest_custom_jquery.js', __FILE__));
    
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
            <label for="position"><?php
                _e('Position');
                ?></label>
        </th>
        <td>
            <?php
            $tax_type = $term_meta['position'] ? $term_meta['position'] : '';
            ?>
            
            <select style=" margin-bottom: 18px; " name="term_meta[position]" id="term_meta[position]" class="postform term_position">                         
                <option <?php if ($tax_type == "none") { echo 'selected'; } ?> value="none">None</option>
                <option <?php if ($tax_type == "wire") { echo 'selected'; } ?> value="wire">Wire</option>
                <option <?php if ($tax_type == "fanout") { echo 'selected'; } ?> value="fanout">Fanout</option>
                <option <?php if ($tax_type == "left_connector") { echo 'selected'; } ?> value="left_connector">Left Connector</option>
                <option <?php if ($tax_type == "right_connector") { echo 'selected'; } ?> value="right_connector">Right Connector</option>
                <option <?php if ($tax_type == "boot") { echo 'selected'; } ?> value="boot">Boot</option> 
            </select>
            
        </td>
    </tr>

    <script>
        var ajaxPath= "<?php echo plugins_url().'/custom_cable_configuration/ajax/';?>";
        jQuery(document).ready(function($) {
             jQuery("#submit").click(function(){
                var submit_val = jQuery("#submit").val();  
                
                if(submit_val == "Update")
                {
                    var name = jQuery("#name").val();                    
                } 
                else if(submit_val == "Add New Configuration") 
                {           
                    var name = jQuery("#tag-name").val();                    
                }
                var parent = jQuery("#parent").val();
                var term_position = jQuery(".term_position").val(); 

                if(name == "" || name == null)
                {                    
                    jQuery('.notice-success').hide();    
                    jQuery('.notice-error').show();
                    jQuery('#message').hide();                    
                    jQuery('.notice-error p').html("Please fill the Required Fields.");                                  
                    jQuery(window).scrollTop(0);
                    return false;
                }
                else
                {
                    if(submit_val == "")
                    {                        
                        <?php add_action('edited_configuration', 'save_taxonomy_custom_fields', 10, 2); ?>
                    }
                    if(submit_val == "Add New Configuration")
                    {
                    jQuery.ajax({       
                        method: "POST",
                        url:ajaxPath+"check_existing.php",                        
                        data: {'name':name, 'parent':'-1', 'taxonomy_type':'configuration'},
                        success: function(response) 
                        {
                            var response = response.trim();
                            if(response == "matched")
                            {                                                              
                                jQuery('.notice-success').hide();     
                                jQuery('.updated').hide();             
                                jQuery('.notice-error').show();
                                jQuery('.notice-error p').html("A Configuration with same name already exists.");
                                setTimeout(function(){ jQuery('.notice-error').fadeOut() }, 1000);   
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
                                jQuery('#tag-description').val('');                                
                                jQuery(".term_position").val(jQuery(".term_position option:first").val());
                                jQuery(window).scrollTop(0);
                            }
                        }
                    });  
                }              
                }            
            });
        });
    </script>

    <?php
}

function save_taxonomy_custom_fields($term_id) 
{
    if (isset($_POST['term_meta'])) 
    {
        $t_id = $term_id;
        $term_meta = get_option("taxonomy_term_$t_id");           
        if($_POST['term_meta'][position] == "" || $_POST['term_meta'][position] == null) 
        {
            echo $position_error = "Position is required.";
        }
        else 
        {
            $position_error = "";
        }  

        if($position_error == "")
        {  
            $cat_keys = array_keys($_POST['term_meta']);
            foreach ($cat_keys as $key) {
                if (isset($_POST['term_meta'][$key])) {
                    $term_meta[$key] = $_POST['term_meta'][$key];
                }
            }
            $update = update_option("taxonomy_term_$t_id", $term_meta);   
        }   
        else
        {
            //Show error msg
        }  
    }
}


add_action('configuration_edit_form_fields', 'configuration_taxonomy_custom_fields', 10, 2);
add_action('configuration_add_form_fields', 'configuration_taxonomy_custom_fields', 10, 2);
add_action('edited_configuration', 'save_taxonomy_custom_fields', 10, 2);
add_action('create_configuration', 'save_taxonomy_custom_fields', 10, 2);

?>
