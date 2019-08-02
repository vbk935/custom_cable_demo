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
		<div style="width: 26%; float: left;" class="config-type-outer">
			<label>Configuration Type: </label><select onchange="add_child(this); return false;" id="parent_config_id" class="child_parent_category_all" name="parent_config_name[]">
				<option value="">- Select Configuration -</option>
				<?php 
				$foo = 0;
				
				foreach($terms as $term){ 
					$chlId = $term->term_id;
					$term_meta = get_option( "taxonomy_term_$chlId" );
					$finalHIdeConfig = $term_meta['hide_config'];
					$finalPresConfig = $term_meta['presenter_id'];					
					if($finalHIdeConfig)
					{
						continue;
					}
					if($finalPresConfig == 'changable')
					{
						continue;
					}	

					?>
					<option value="<?php echo $term->term_id."-".$foo; ?>"><?php echo $term->name; ?></option>
					<?php 
					$foo++;
				} ?>
			</select>
			<img class="divLoader1" id="divLoader1" src="<?php  echo plugins_url();?>/custom_cable_configuration/images/spinner.gif"/>
		</div>
		<div class="childClass" id="childId"></div>
		<div style="clear: both;"></div>
		<form id="formParent" method="post" style="height: 0px;">
			<input type="hidden" name="group_id" class="group_id" value="<?php echo $_POST['parent_category_id']; ?>">
			<?php 
			$foo = 1;
			foreach($terms as $term)
			{ 
				$term_meta = get_option( "taxonomy_term_$term->term_id" );
				$finalHIdeConfig = $term_meta['hide_config'];
				$finalPresConfig = $term_meta['presenter_id'];
				//$measureConfig = $term_meta['is_unit_type'];
				if($finalHIdeConfig == 1 || $finalPresConfig == "changable")
				{
					continue;
				}
				else
				{
			?>
				<input type="hidden" name="childParentid[]" value="<?php echo $term->term_id; ?>">
				<input type="hidden" name="childParentName[]" value="<?php echo $term->name; ?>">
			<br>
			<?php 
				}
			$foo++;
		} ?>
	</form>
	<?php }else{ ?>
	<p>No record Found</p>
	<?php }
}
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript">
// add child html
function add_child($this) {
	var group_id = jQuery('.group_id').val();
	var parent_category_id = jQuery($this).val();
	var str = parent_category_id;
	var res = str.split("-");
	var parent_category_id = res[0];
	var number = res[1];
	$(".divLoader1").css("visibility", "visible");
	jQuery.ajax({
		method: "POST",
		url: "<?php  echo plugin_dir_url(__FILE__)?>get_config_category_select.php",
		dataType: "html",
		data: $('#formParent').serialize()+ "&parent_category_id="+parent_category_id+"&number="+number+"&group_id="+group_id,
		success: function(response) {
			$(".divLoader1").css("visibility", "hidden");
			if (response == '') {
				jQuery('.childClass').html(response);
			} else {
				jQuery('.childClass').html(response);
			}
		}
	});
	return false;
}
</script>
