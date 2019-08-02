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

	<select class="child_child_category" onchange="check_unit_cost(this); return false" name="child_child[]">
		<option value="">- Select Configuration -</option>
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
		<option class="<?php echo $class; ?>" data_id="<?php echo $t_id; ?>" value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
		<?php
		}
	?>
	</select>
	<div class="perunitcost">
	<lable>Extra Per Unit Cost</lable><input class="per_unit_cost" type="text" />
	</div>
<?php
}
	
	}
?>
