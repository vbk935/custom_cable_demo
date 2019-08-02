<?php
require('../../../../wp-load.php');
if(!empty($_POST))
{
	$group_id = $_POST['group_id'];
	$number = $_POST['number'];
	$args = array(
		'hide_empty' => false,
		'parent'=>$_POST['parent_category_id']
		);	

	$terms = get_terms( 'configuration', $args);
	if(!empty($terms))
	{
?>	
		<div id="child_config_id_<?php echo $number; ?>">
		<div class="selectConfig-outer">
		<label>Configuration Value: </label>
		<select class="child_child_category_primary" name="child_config_name[]">
		<!-- <select class="child_child_category_primary" name="child_config_name[]" onchange="store_val(this,'<?php //echo $_POST['parent_category_id']; ?>'); return false;" id="child_config_id">  -->
			<option value="">- Select Configuration -</option>
			<?php
				foreach($terms as $term)
				{
					$chlId = $term->term_id;
					$term_meta = get_option( "taxonomy_term_$chlId" );
					$finalHIdeConfig = $term_meta['hide_config'];
					$finalPresConfig = $term_meta['presenter_id'];
					if($finalHIdeConfig){
						continue;
					}
					if($finalPresConfig == 'changable'){
						continue;
					}	
			?>
					<option data_id="<?php echo $t_id; ?>" value="<?php echo $term->term_id; ?>"><?php echo $term->name.' ('.$term->slug.')'; ?></option>
			<?php 
				}	 
			?>
		</select>
		</div>
		<div class="add-configBtn-outer" id="config_outer_<?php echo $_POST['parent_category_id']; ?>">
		<a href="#" onclick="add_config('<?php echo $group_id;?>','<?php echo $_POST['parent_category_id']; ?>'); return false;">Add Configuration</a>
		<a href="#" onclick="set_condition('<?php echo $group_id;?>','<?php echo $_POST['parent_category_id']; ?>','<?php echo $number; ?>'); return false;">Set Condition</a>
		</div>
		<input type="hidden" name="group_id" class="group_id" value="<?php echo $group_id; ?>">
		</div>
<?php
	}
}
?>