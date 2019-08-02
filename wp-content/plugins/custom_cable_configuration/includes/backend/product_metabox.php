<?php
function wpdocs_register_meta_boxes() {
add_meta_box('meta-box-id', __('Product Configuration', 'textdomain'), 'wpdocs_my_display_callback', 'product');
}
//add_action('add_meta_boxes', 'wpdocs_register_meta_boxes');

function wpdocs_my_display_callback($post) 
{
?>
	<div class="is_configurable">
		<?php
			$is_configurable = get_post_meta($post->ID, 'is_configurable', FALSE);
			if(is_array($is_configurable))
			{
				$is_configurable=$is_configurable[0];
			}
			else
			{
				$is_configurable=$is_configurable;
			}
			if (!empty($is_configurable)) {
		?>
		<select name="is_configurable">
			<option <?php if ($is_configurable == 'no') { echo 'selected'; } ?> value="no">No</option>
			<option <?php if ($is_configurable == 'yes') { echo 'selected'; } ?> value="yes">Yes</option>
		</select>
		<?php
			}
		?>
	</div>
	<?php
		if ($is_configurable == 'yes') 
		{
			$hide = '';
		} 
		else 
		{
			$hide = 'hide';	
		}
	?>
	<div class="show_config <?php echo $hide; ?>">
		<?php
			$configuration_combination_raw = get_post_meta($post->ID, 'configuration_combination', TRUE);
			//$configuration_combination_raw = get_post_meta($post->ID, 'is_configurable', TRUE);
			//echo "<pre>";print_r($configuration_combination_raw);
			if (!empty($configuration_combination_raw))
			{
				$configuration_combination = unserialize($configuration_combination_raw);
			}
			$per_unit_raw = get_post_meta($post->ID, 'per_unit', TRUE);
			if (!empty($per_unit_raw))
			{
				$per_unit = unserialize($per_unit_raw);
			}
			
			if(isset($configuration_combination) and (!empty($configuration_combination)))
			{
				$main_category_id = '';
				$child_data = '';
				foreach ($configuration_combination as $id => $data)
				{
					$main_category_id = $id;
					$child_data = $data;
				}
				//print_r($child_data); die('here');
			}
		?>
		<input type="hidden" name="post_id" id="post_id" value="<?php echo $post->ID; ?>" >
		<h2 class="create_config">Select Group</h2>
		<div class="row main_category">
			<?php
				$terms = get_terms('configuration', array(
					'hide_empty' => false,
					'parent' => 0
				));
				if (!empty($terms)) 
				{
			?>
			<select class="main_category" name="main_category">
				<option value="">--Select Group--</option>
				<?php foreach($terms as $term){ ?>
					<option <?php if ($term->term_id == $main_category_id) { echo 'selected'; } ?> value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
				<?php } ?>
			</select>
			<?php } ?>
		</div>
		<div class="conditions_outer Product-Confg">			
			<div class="conditions Product-ConfigurationsGroups">
				<?php 
					if(!empty($main_category_id) || $main_category_id != "")
					{
						$args = array(
							'hide_empty' => false,
							'parent'=>$main_category_id
						);
						$terms = get_terms( 'configuration', $args);
						//echo "<pre>"; print_r($terms); die("here");
						if(!empty($terms))
						{
				?>
					<h2 class="select_condition">Product Configurations</h2>
					<div class="condition">
						<div class="child_parent_category_outer">			
							<?php 
								$display_type = "";
								$tax_type = "";
								$is_unit_type = "";
								$dash_count = 0;
								foreach($terms as $term){ 
									$t_id = $term->term_id;					
									$term_meta = get_option( "taxonomy_term_$t_id" );
									$display_type = $term_meta['hide_config'];	
									$tax_type =  $term_meta['presenter_id'];
									$is_unit_type = $term_meta['is_unit_type'];
									if($display_type == 1)	
									{
										$dash_count++;
									}												
									if($display_type != 1 && $tax_type != 'changable')
									{						
										$sub_args = array(
											'hide_empty' => false,
											'parent'=>$term->term_id
										);
										$sub_terms = get_terms( 'configuration', $sub_args);				
							?>
                                        <div class="product-decs-inputOuter">            										
										<input type="hidden" name="child_parent[]" value="<?php echo $term->term_id; ?>">	
										<?php if($is_unit_type == 1){ ?>
											<label id="changable_field"><?php echo $term->name; ?></label>
											<?php foreach($sub_terms as $sub) { ?>
                                                <div class="input-outer "><label><?php echo  $sub->name; ?> :</label> 
												<input type = "hidden" value="<?php echo $sub->term_id; ?>" name="measure_type[]">
												<?php													
													if(array_key_exists($sub->term_id,$per_unit))													
													{														
														$value = $per_unit[$sub->term_id];
													} 
													else 
													{
														$value = "";
													}
												?>
												<input type = "text" name="measure_cost[]" value="<?php echo $value; ?>">
												</div>
											<?php 
													}  // subterms foreach closed										
												} else {
													if($dash_count == 0)
													{
											?>
											<label><?php echo $term->name; ?></label>												
											<select class="child_child_category" name="child_child[]">
												<option value="">-- Select Configuration --</option>
												<?php foreach($sub_terms as $sub) { ?>
													<option value="<?php echo $sub->term_id; ?>" <?php if(in_array($sub->term_id,$child_data)){ echo "selected"; } ?> ><?php echo $sub->name."(".$sub->slug.")"; ?></option>
												<?php } ?>
											</select>
										<?php } } ?>
										</div>
								<?php } ?>							  							
								<?php
									$display_type = "";
									$tax_type = "";
									$is_unit_type = "";					
								} 
							?>
							<div class="product-decs-inputOuter">                          						
								<div class="input-outer ">
								<?php $labor_charges = get_post_meta($post->ID, 'labor_charges', true ); ?>
								 	<label  class="labor_chrgs_text">Labor Charges</label>                                               
									<input type = "text" name="labor_charges" value="<?php echo $labor_charges; ?>">
								</div>			
							</div>
						</div>	
					</div>
				<?php
						}
					}
				?>
			
			
			</div>
			<div class="submit_button">
				<input type="submit" class="button button-primary button-large update_config" name="submit" value="Update"/>
			</div>
		</div>
	</div>
<script>
	var ajaxPath= "<?php echo plugins_url().'/custom_cable_configuration/ajax/';?>";

	
	jQuery(document).ready(function()
	{
		jQuery('.update_config').click(function()
		{
			jQuery('#publishing-action').find('input').trigger('click');
			return false;
		});
		jQuery('select[name="is_configurable"]').on('change', function()
		{
			if (jQuery(this).val() == 'yes')
			{
				jQuery('.show_config').show();
			}
			else
			{
				jQuery('.show_config').hide();
			}
		});
		
		jQuery('.main_category').on('change', function()
		{
			var parent_category_id = jQuery(this).val();
			var post_id = jQuery("#post_id").val();
			jQuery.ajax({
				method: "POST",
				url:ajaxPath+"get_parent_category_select.php",                
				dataType: "html",
				data: {parent_category_id: parent_category_id,post_id: post_id},
				success: function(response) {
					jQuery('.conditions_outer').find('.conditions').empty();
					if (response == '')
					{
					} 
					else 
					{
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

function wpdocs_save_meta_box($post_id) 
{
	if ($_POST['post_type'] == 'product')
	{
		$is_configurable = $_POST['is_configurable'];
		update_post_meta($post_id, 'is_configurable', $is_configurable);
		if ((!empty($_POST['main_category']) && (!empty($_POST['child_parent']))) && (!empty($_POST['child_child'])))		
		{
			$this_configuration_combination = array();
			if (!empty($_POST['main_category']))
			{
				$main_key = $_POST['main_category'];
				$this_configuration_combination[$main_key] = array();
				foreach ($_POST['child_parent'] as $key => $child_parent)
				{					
					if (!empty($_POST['child_child'][$key]))
					{											
						$this_configuration_combination[$main_key][$child_parent] = $_POST['child_child'][$key];
					}
				}
			}

			if (!empty($this_configuration_combination))
			{
				update_post_meta($post_id, 'configuration_combination', serialize($this_configuration_combination));
				if (!empty($_POST['measure_type']))
				{
					foreach($_POST['measure_type'] as $key=>$measure_value)
					{
						$measure_combi[$measure_value] = $_POST['measure_cost'][$key];
					}
					update_post_meta($post_id, 'per_unit', serialize($measure_combi));
				}
				if(!empty($_POST['labor_charges']))
				{
					update_post_meta($post_id , 'labor_charges' ,$_POST['labor_charges']);
				}
			}						
		}
	}
}
add_action('save_post', 'wpdocs_save_meta_box');

?>
