<?php
/* Shortcode for Product Grouping [product_grouping] */
function product_grouping_func($atts) 
{
    /*     
     * Add Config Page ID here. Change this with live ID
     * $config_page_ID = 1412;
    */
    $uploads_dir = wp_upload_dir();         
    $page_obj = get_page_by_path('configure-product');
    $config_page_ID = $page_obj->ID;    
    $terms_groups = get_terms('group', array(
            'hide_empty' => false,
            'parent' => 0
        ));
	
	if ( is_user_logged_in() ) {
		$user = wp_get_current_user();
		$user_id = $user->ID;
		$userdata = new WP_User( $user_id );		
		$user_role = $userdata->roles;	
	} else {
		$user_role = array('0' => "guest_user");
	}	
    if (!empty($terms_groups))
    {
        foreach ($terms_groups as $term) 
        {   
            $sub_term_groups = get_terms('group', array(
                'hide_empty' => false,
                'parent' => $term->term_id
            )); 
            
            // Continue incase of no subcategory elements
            if (empty($sub_term_groups))
                continue;

            // Check if any of the sub-groups has some data
            $all_empty_Flag = true;
            foreach($sub_term_groups as $sub_term_group) {
                $sub_sub_term_groups = get_terms('group', array(
                    'hide_empty' => false,
                    'parent' => $sub_term_group->term_id
                ));
                if (!empty($sub_sub_term_groups))
                    $all_empty_Flag = false;
            }
            if ($all_empty_Flag)
                continue;

            $green_design = false;
            if (strpos(strtolower($term->name), 'assembly') !== false)
                $green_design = true;
			
		   $term_meta = get_option("taxonomy_term_$term->term_id");  
		   $restrict_role1 = $term_meta['restrict_role'];
		   if(empty(array_intersect($user_role,$restrict_role1)))
           {
?>          
            <div class="title">
                <h2 class="home <?php echo ($green_design !== false)?"greentext": ""; ?>"><?php echo $term->name; ?></h2>
                <?php if (!$green_design) { ?>
                <?php echo wp_get_attachment_image(700, 'full', 0, array('class' => 'img-responsive h2-art')); ?>
                <?php }else{
                    echo "<img src='". $uploads_dir['baseurl'] ."/customcable/graphic-divider-green-1024x39.png' class='img-responsive h2-art' alt='Graphic. Green Divider.'>";
                } ?>
            </div>
            <?php 
                
                if (!empty($sub_term_groups))
                {    
                ?>
                    <div class="ListingView flx-tble <?php echo ($green_design !== false)?"green-sec": ""; ?>">
                        <div class="productrow row">
                <?php            
                    $subterm_chunk = $sub_term_groups;//array_chunk($sub_term_groups, 4, true);

                    foreach($subterm_chunk as $sub_term_group) {
                        $sub_sub_term_groups = get_terms('group', array(
                            'hide_empty' => false,
                            'parent' => $sub_term_group->term_id
                        ));
                        if (!empty($sub_sub_term_groups))
                        {    
							$sub_sub_term_groups_options = get_option("taxonomy_term_$sub_term_group->term_id");
							$restrict_role2 = $sub_sub_term_groups_options['restrict_role'];							  
							if(empty(array_intersect($user_role,$restrict_role2)))
							{							
                        ?>
                            <div class="col-xs-12 col-sm-4 col-md-4">
                                <h3 class="cat-title"><?php echo $sub_term_group->name;  ?></h3>
                        
                                    <?php
                                        // $sub_sub_subterm_chunk = array_chunk($sub_subsub_term_groups, 4, true);
                                        foreach ($sub_sub_term_groups as $sub_item_chunk)
                                        {
                                            $sub_term_meta = get_option("taxonomy_term_$sub_item_chunk->term_id");
                                            $tax_type = $sub_term_meta['presenter_id'] ? $sub_term_meta['presenter_id'] : '';   
                                            $restrict_role3 = $sub_term_meta['restrict_role'];
                                            if(empty(array_intersect($user_role,$restrict_role3)))
                                            {
                                            //$image = $sub_term_meta['image'] ? $sub_term_meta['image'] : '';
                                            // if($sub_term_meta['image'])
                                            // {
                                            //     $image = $sub_term_meta['image'];
                                            // }
                                            // else 
                                            // {
                                            //     $image =  plugins_url().'/custom_cable_configuration/images/product-icon.png'; 
                                            // }
                                            if($tax_type == 'configurable')
                                            { 
                                                $configuration_id = $sub_term_meta['show_configuration'] ? $sub_term_meta['show_configuration'] : '';
                                                $term_link = get_permalink($config_page_ID).'?config='.$configuration_id.'&group_id='.$sub_item_chunk->term_id;
                                                $term_value = 'Configure';
                                            }
                                            // elseif($tax_type == 'standard_cart') 
                                            else
                                            {

                                                $cat_id = $sub_term_meta['show_simple_product'];
                                                // $cat_id = $sub_item_chunk->term_id;
                                                if (!empty($cat_id))
                                                {
                                                    $cat_id = intval($cat_id);
                                                    $term_link = get_term_link($cat_id, 'product_cat');
                                                    $term_value = 'View Details';
                                                }
                                                $term_link = home_url('/')."product-category/".$sub_item_chunk->slug."/";
                                                
                                            }
                                            
                                    ?>
                                            
                                            <div class="columnView <?php echo ($green_design !== false)?"green-box": "blue-box"; ?>">                        
                                                <?php
                                                    $img_url = "/customcable/logo-products-grey.png";
                                                    if ($green_design)
                                                        $img_url = "/customcable/logo-products-green.png";
                                                    $img_url = $uploads_dir['baseurl'].$img_url;
                                                ?>
                                                <span class="logo-imgleft"><img src="<?php echo $img_url; ?>" class="img-responsive center-block" alt="Megladon Shark Logo"></span>
                                                <div class="txtHeight">
                                                    <a href="<?php echo $term_link; ?>"><h4 class="fw-600"><?php echo $sub_item_chunk->name; ?></h4></a>
                                                    <!-- <p>Exclusive HLC Termination Process</p> -->
                                                </div>    
                                            </div>
                                    <?php  
											} // End if Restrict Role3	
                                        } // End of Foreach sub_sub_term_groups ?>
                            </div> <!-- END of col-xs-4 col-sm-4 --> 
                    <?php 
							} // End of Retrict Role2 
                        } // End of if sub_sub_term_groups  ?> 

                    <?php 
                        //} //Sub Term If Empty sub_sub_term_groups 
                    } // End of foreach Sub Term chunk
                    ?>
                    </div><!-- END of productrow -->
                </div><!-- END of ListingView -->
                <?php
                }// ENd of If Subterm Groups
                else 
                {
                    echo "No Groups added";
                }
                ?>

<?php
			} // End of Restrict Role1
        } //Main Terms Foreach Closed 
    } // Main Terms If Closed
}
add_shortcode('product_grouping', 'product_grouping_func');
?>
