<?php
require('../../../../wp-load.php');
if(!empty($_POST['parent_category_id']))
{
	$parent_category_id = $_POST['parent_category_id'];
	$args = array(
		'hide_empty' => false,
		'parent'=>$_POST['group_id']
		);
	$terms = get_terms( 'configuration', $args);
	
	if(!empty($terms)) 
	{
		$term_arr = [];
		$foo = 0;
		foreach($terms as $term)
		{				
			$term_meta = get_option( "taxonomy_term_$term->term_id" );
			$finalHIdeConfig = $term_meta['hide_config'];
			$finalPresConfig = $term_meta['presenter_id'];
			
			if($finalHIdeConfig  || $finalPresConfig == 'changable')
			{
				continue;
			}
			else
			{					
				$term_arr[] = $term->term_id."-".$foo."+".$term->name;
				$foo++;
				if($term->term_id == $parent_category_id)
				{
					$term_arr = [];
				}
			}
		}
		//echo "<pre>"; print_r($term_arr);		
?>
		<div class="config-type-outer">
			<label>Configuration Type: </label>			
			<select onchange='show_value(this); return false;' class='child_parent_category_all' name='parent_config_name[]'>
				<option value="">- Select Configuration -</option>
				<?php 
					$foo = 0;				
					foreach($term_arr as $term)
					{ 
						$split_term = explode("+", $term);
						$term_id = $split_term[0];
						$term_name = $split_term[1];
				?>
						<option value="<?php echo $term_id; ?>"><?php echo $term_name; ?></option>
				<?php 
						$foo++;
					} 
				?>
			</select>
			<img class="divLoader1" src="<?php  echo plugins_url();?>/custom_cable_configuration/images/spinner.gif"/>
		</div>
				
<?php
	}
}
?>
