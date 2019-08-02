<?php 
require('../../../../wp-load.php');
if(!empty($_POST['parent_category_id']))
{
	$args = array(
		'hide_empty' => false,
		'parent'=>$_POST['parent_category_id']
	);
	$terms = get_terms( 'configuration', $args);
	if(!empty($terms))
	{		
		if(!empty($_POST['post_id']))
		{
			$configuration_combination_raw = get_post_meta($_POST['post_id'], 'configuration_combination', TRUE);
			if (!empty($configuration_combination_raw))
			{
				$configuration_combination = unserialize($configuration_combination_raw);
			}
			$per_unit_raw = get_post_meta($_POST['post_id'], 'per_unit', TRUE);
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
			}
		}
		
?>

	<h2 class="select_condition">Product Configurations</h2>
	<div class="condition">
		<div class="child_parent_category_outer">			
			<?php 
				$display_type = "";
				$tax_type = "";
				$is_unit_type = "";
				$dash_count = 0;
				foreach($terms as $term)
				{ 
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
								 <div class="input-outer "><label  class="measure-ttle"><?php echo  $sub->name; ?> :</label> 
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
							<?php } 
								} else { 
									if($dash_count == 0)
									{
							?>	
							<label><?php echo $term->name; ?></label>								
							<select class="child_child_category" name="child_child[]">
								<option value="">-- Select Configuration --</option>
								<?php foreach($sub_terms as $sub) { ?>
									<option value="<?php echo $sub->term_id; ?>" <?php if(in_array($sub->term_id,$child_data)){ echo "selected"; } ?>><?php echo $sub->name."(".$sub->slug.")"; ?></option>
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
		</div>	
		<div class="product-decs-inputOuter">                          						
				<div class="input-outer ">
				 	<label  class="labor_chrgs_text">Labor Charges</label>                                               
					<input type = "text" name="labor_charges" value="">
        </div>			
			</div>	
	</div>
<?php
	}
	else 
	{
		echo "No Configuration Added for this Group";
	}	
}
?>
