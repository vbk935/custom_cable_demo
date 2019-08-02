<?php
require('../../../../wp-load.php');
if(!empty($_POST['parent_category_id'])){
	$args = array(
		'hide_empty' => false,
		'parent'=>$_POST['parent_category_id']
		);
	$terms = get_terms( 'configuration', $args);
	if(!empty($terms)){
		?>
		<div style="width: 26%; float: left;">
			<label>Configuration Type: </label>
			<select id="parent_config_id" class="child_parent_category_all" name="parent_config_name">
				<option value="">- Select Configuration -</option>
				<?php 
				$foo = 0;
				
				foreach($terms as $term){ ?>
					<option value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
					<?php 
					$foo++;
				} ?>
			</select>
		</div>
		<div style="width: 26%; float: left;">
			<label>Position: </label>
			<select id="position" class="position" name="position">
				<option value="">- Select position -</option>
				<option value="right-connector">Right Connector</option>
				<option value="left-connector">Left Connector</option>
				<option value="wire">Wire</option>
				<option value="left-boot">Left Boot</option>
				<option value="right-boot">Right Boot</option>

			</select>
			<img class="divLoader1" src="<?php  echo plugins_url();?>/custom_cable_configuration/images/spinner.gif"/>
		</div>
		<div style="clear: both;"></div>
		
		<?php }else{ ?>
		<p>No record Found</p>
		<?php }
	}
	?>
