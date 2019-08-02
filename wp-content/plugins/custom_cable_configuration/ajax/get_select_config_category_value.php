<?php
require('../../../../wp-load.php');
if(!empty($_POST))
{
	//$combine_data = $_POST['combine_data'];
	$p_id = $_POST['p_id'];	
	$p_name = $_POST['p_name'];
	$combine_data = array_combine($p_id,$p_name);
	//echo "<pre>"; print_r($combine_data); die("here");
	$parent_category_id = $_POST['parent_category_id'];
	$number = $_POST['number'];
?>
	<form method="post" id="formConduction">
		<input type="hidden" name="pageNumber" id="pageNumber" value="<?php echo $number ?>">
		<?php 
			$n= 0;
			foreach ($combine_data as  $DataKey => $DataValue) 
			{
			$term_meta = get_option( "taxonomy_term_$DataKey" );
			$finalHIdeConfig = $term_meta['hide_config'];
			$finalPresConfig = $term_meta['presenter_id'];
			if($finalHIdeConfig){
				continue;
			}
			if($finalPresConfig == 'changable'){
				continue;
			}	
			
			if($n == 1){
				$args = array(
					'hide_empty' => false,
					'parent'=>$DataKey
					);
				$terms = get_terms( 'configuration', $args);
				if(!empty($terms)){
					?>
					<div style="width: 20%; margin-right: 5%; float: left;">
						<label><?php echo $DataValue.': '; ?></label><br>
						<select multiple class="child_child_category" id="child_child_id" name="child_child[<?php echo $DataKey ?>][]" style="width: 100%;" title="Selected items will be hide">
							<?php
							foreach($terms as $term){
								$t_id = $term->term_id;
								$term_meta = get_option( "taxonomy_term_$t_id" );
								$tax_type = $term_meta['presenter_id'] ? $term_meta['presenter_id'] : '';
								$class = '';
								if($tax_type == 'changable'){
									$class = 'changable';
								}
								?>
								<option class="<?php echo $class; ?>" data_id="<?php echo $t_id; ?>" value="<?php echo $term->term_id; ?>"><?php echo $term->name.' ('.$term->slug.')'; ?></option>
								<?php
							}
							?>
						</select>
					</div>

					<?php
				}
			}
			if($parent_category_id == $DataKey)
			{			
				$n = 1;
			}			
		} ?>
	</form>
<?php 
}
?>
<script type="text/javascript">
	jQuery(document).ready(function(){
			jQuery('select.child_child_category').each(function() {
				var $this = $(this);
				jQuery($this).blur(function(){	            	            	
	            	var total_count = $this.find('option').length;
            		var selected_count = $this.find("option:selected").length;            		
            		if(total_count > 1 && total_count == selected_count)
            		{
            			alert("You cannot select all options from the list.");
            			$this.find("option:selected").removeAttr("selected");
            		}
				});
			});			  		
		});
</script>
