<?php
require('../../../../wp-load.php');
if(!empty($_POST)){

	$p_id = $_POST['childParentid'];	
	$p_name = $_POST['childParentName'];
	$group_id = $_POST['group_id'];
	$number  = $_POST['number'];
	$args = array(
		'hide_empty' => false,
		'parent'=>$_POST['parent_category_id']
		);

	$terms = get_terms( 'configuration', $args);
	if(!empty($terms)){
		?>
		
		<label>Configuration Value: </label>
		<select class="child_child_category_primary" name="child_config_name[]" id="child_config_id">
		<!-- <select class="child_child_category_primary" name="child_config_name[]" onchange="store_val(this,'<?php //echo $_POST['parent_category_id']; ?>'); return false;" id="child_config_id"> --> 
			<option value="">- Select Configuration -</option>
			<?php
			foreach($terms as $term){
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
				<?php echo $term->name; 
			} ?>
		</select>
		
		<?php $combineData =  array_combine($_POST['childParentid'],$_POST['childParentName']); ?>
		<div class="add-configBtn-outer" id="config_outer_<?php echo $_POST['parent_category_id']; ?>">
			<a href="#" onclick="add_config('<?php echo $group_id;?>','<?php echo $_POST['parent_category_id']; ?>'); return false;">Add Configuration</a>
			<a href="#" onclick="set_condition('<?php echo $group_id;?>','<?php echo $_POST['parent_category_id']; ?>','<?php echo $number; ?>'); return false;">Set Condition</a>
		</div>
		<input type="hidden" name="group_id" class="group_id" value="<?php echo $group_id; ?>">
		<input type="hidden" name="primary_parent_id[]" id="primary_parent_id">
		<?php
		}
		?>
		<div class="new_config"></div>
		<div class="set_condition"></div>
	
	<?php }
	?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script type="text/javascript">
	function add_config(group_id,parent_category_id)
	{		
		jQuery('#config_outer_'+parent_category_id).hide();
		jQuery("#formConduction").hide();		
		jQuery.ajax({
			method: "POST",
			url: "<?php  echo plugin_dir_url(__FILE__)?>get_next_config.php",
			dataType: "html",
			data: {'parent_category_id':parent_category_id,'group_id':group_id},
			success: function(response) {
				if (jQuery('.new_config').children().length == 0 ) 
				{
					jQuery('.new_config').html(response);
				}
				else
				{
					jQuery('.new_config').append(response);
				}
			}
		});
	}

	function show_value($this) {		
		var group_id = jQuery('.group_id').val();
		var parent_category_id = jQuery($this).val();
		var str = parent_category_id;
		var res = str.split("-");
		var parent_category_id = res[0];
		var number = res[1];
		jQuery($this).attr('id','parent_config_'+number);		
		//$(".divLoader1").css("visibility", "visible");
		jQuery($this).next('img').attr('id','config_loader_'+number);		
		jQuery('#config_loader_'+number).css({"visibility":"visible", "right":"725px"});
		jQuery.ajax({
			method: "POST",
			url: "<?php  echo plugin_dir_url(__FILE__)?>get_config_next_category.php",
			dataType: "html",
			data: {'parent_category_id':parent_category_id,'number':number,'group_id':group_id},
			success: function(response) {
				//$(".divLoader1").css("visibility", "hidden");
				$('#config_loader_'+number).css("visibility", "hidden");
				if (response == '') 
				{
					jQuery("<p>No Records found</p>").insertAfter(jQuery('#parent_config_'+number));
				} 
				else 
				{

					if(jQuery("#parent_config_"+number).next('img').next().is('div'))
					{									
						jQuery("#parent_config_"+number).next('img').nextAll().remove();
						jQuery("#parent_config_"+number).parent('div').nextAll().remove();
						jQuery("#formConduction").remove();
					}					
					jQuery(response).insertAfter(jQuery('#parent_config_'+number).next('img'));
				}
			}
		});
		return false;
	}

	
	function set_condition(group_id,parent_category_id,number)
	{		
		//var combine_data = <?php //echo  $combineData; ?>		
		var p_id = <?php echo json_encode($p_id); ?>;
		var p_name = <?php echo json_encode($p_name); ?>;
		jQuery.ajax({
			method: "POST",
			url: "<?php  echo plugin_dir_url(__FILE__)?>get_select_config_category_value.php",
			dataType: "html",
			data: {'parent_category_id':parent_category_id,'group_id':group_id,'number':number, 'p_id' : p_id, 'p_name' : p_name},
			success: function(response) 
			{				
				jQuery(".set_condition").html(response);
			}
		});
	}

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
