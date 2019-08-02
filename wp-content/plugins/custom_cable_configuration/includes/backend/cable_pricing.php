<?php
add_action("admin_menu", "create_cable_pricing_menu");
function create_cable_pricing_menu() 
{
add_submenu_page("edit.php?post_type=product", "CablePricing", "CablePricing", 0, "cable_pricing", "cable_pricing_callback");
}

function pricing_import_notice(){
global $pagenow;
if (isset($_GET['page']) ) {
if($_GET['page'] == 'cable_pricing'){
if(isset($_POST['csvpimport']) || isset($_POST['csvcimport'])){
echo '<div class="notice notice-success is-dismissible">
<p>Import Complete.</p>
</div>';
}
if(isset($_POST['del_ids'])){
 echo '<div class="notice notice-success is-dismissible">
 <p>Removed Successfully.</p>
 </div>';
}
}
}
}

function price_converter($elem,$from,$to){
$m_i = 0.0254;
$f_i = 0.0833;
$f_m = 3.2808;
$m_f = 0.3048;
$i_f = 12;
$i_m = 39.3701;
if($from == 'm' && $to == 'i')
{
$price = $elem * $m_i;		
}
if($from == 'f' && $to == 'i' )
{
$price = $elem * $f_i;
}
if($from == 'f' && $to == 'm')
{
$price = $elem * $f_m;
}	
if($from == 'm' && $to == 'f')
{
$price = $elem * $m_f;
}	
if($from == 'i' && $to == 'f')
{
$price = $elem * $i_f;
}	
if($from == 'i' && $to == 'm')
{
$price = $elem * $i_m;
}
$price = number_format((float)$price, 2, '.', '');
return $price;
}

add_action('admin_notices', 'pricing_import_notice');
function cable_pricing_callback()
{
global $wpdb; $wp;
session_start();
if($_SESSION['msg'] == "success")
{					
echo '<div class="updated notice sMsg"><p><strong>Pricing Saved Successfully.</strong></p></div>';
$_SESSION['msg'] = "";
} 
if($_SESSION['msg'] == "updated")
{					
echo '<div class="updated notice sMsg"><p><strong>Pricing Updated Successfully.</strong></p></div>';
$_SESSION['msg'] = "";
}
if($_SESSION['msg'] == "conditions_success")
{
echo '<div class="updated notice sMsg"><p><strong>Conditions Saved Successfully.</strong></p></div>';
$_SESSION['msg'] = "";
}
if(isset($_POST['delp'])){
	$ids = $_POST['del_ids'];
	$delete = $wpdb->query('DELETE FROM '.$wpdb->prefix.'cable_pricing WHERE id IN('.$ids.')');
}

if(isset($_POST['csvpimport'])){
if(isset($_FILES['importcsv_pricing'])){
$file = $_FILES['importcsv_pricing']['tmp_name'];
if (($handle = fopen($file, "r")) !== FALSE) {
$i=0;
while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
$i++;
if($i == 1) continue;
$inch = ''; $meter = ''; $foot = '';
if(!empty($data[6])){
	$meter = $data[6];
	$inch = price_converter($meter,'m','i');
	$foot = price_converter($meter,'m','f');
}
elseif(!empty($data[5])){
	$inch = $data[5];
	$meter = price_converter($inch,'i','m');
	$foot = price_converter($inch,'i','f');
}
elseif(!empty($data[7])){
	$foot = $data[7];
	$inch = price_converter($foot,'f','i');
	$meter = price_converter($foot,'f','m');
}
$price_arr = array(
	'i'=>$inch,
	'm'=>$meter,
	'f'=>$foot
);
$price = json_encode($price_arr);

$inch_weight = ''; $meter_weight = ''; $foot_weight = '';
if(!empty($data[9])){
	$meter_weight = $data[9];
	$inch_weight = price_converter($meter_weight,'m','i');
	$foot_weight = price_converter($meter_weight,'m','f');
}
elseif(!empty($data[8])){
	$inch_weight = $data[8];
	$meter_weight = price_converter($inch_weight,'i','m');
	$foot_weight = price_converter($inch_weight,'i','f');
}
elseif(!empty($data[10])){
	$foot_weight = $data[10];
	$inch_weight = price_converter($foot_weight,'f','i');
	$mete_weightr = price_converter($foot_weight,'f','m');
}
$weight_arr = array(
	'i'=>$inch_weight,
	'm'=>$meter_weight,
	'f'=>$foot_weight
);
$weight = json_encode($weight_arr);


$import = array(
	'cable_id' => $data[1],
	'wire_color' => $data[2],
	'fanout_color' => $data[3],
	'thickness' => $data[4],
	'price' => $price,
	'weight' => $weight,
	'manufacturer_part_no' => $data[11],
	'status' => ($data[12] == "Active")?1:0
);
if(!empty($data[0])){
	$where['id'] = $data[0];
	$wpdb->update($wpdb->prefix."cable_pricing",$import,$where);
}
else{
	$wpdb->insert($wpdb->prefix."cable_pricing",$import);
}
}
}
}
}
if(isset($_POST['csvcimport'])){
if(isset($_FILES['importcsv_conditions'])){
$file = $_FILES['importcsv_conditions']['tmp_name'];
if (($handle = fopen($file, "r")) !== FALSE) {
$i=0;
while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
$i++;
if($i == 1) continue;
$conditions = array();

if(!empty($data[2])){
	$term_meta = get_term_by('name', strtolower('connector a'), 'configuration');
	$term_id = $term_meta->term_id;
	$arr_dt = explode(';',$data[2]);
	$c_name = '';$cnt=-1;
	foreach($arr_dt as $dt){
		$dts_arr = explode(':',$dt);
		if($c_name != $dts_arr[0]){
			$cnt++;
			$option = array();
			$option[] = strtolower($dts_arr[1]);
		}
		else{
			$option[] = strtolower($dts_arr[1]);
		}
		$c_name = $dts_arr[0];
		$get_component = $wpdb->get_results("SELECT component_id FROM ".$wpdb->prefix."components WHERE name = '".$c_name."'", ARRAY_A);
		$comp_id = $get_component[0]['component_id'];
		$conditions[$cnt] = array(
			$term_id => array(
				'id' => $comp_id,
				'data' => $option
			)
		);
	}
}
if(!empty($data[3])){
	$term_meta = get_term_by('name', strtolower('connector b'), 'configuration');
	$term_id = $term_meta->term_id;
	$arr_dt = explode(';',$data[3]);
	$c_name = '';
	foreach($arr_dt as $dt){
		$dts_arr = explode(':',$dt);
		if($c_name != $dts_arr[0]){
			$cnt++;
			$option = array();
			$option[] = strtolower($dts_arr[1]);
		}
		else{
			$option[] = strtolower($dts_arr[1]);
		}
		$c_name = $dts_arr[0];
		$get_component = $wpdb->get_results("SELECT component_id FROM ".$wpdb->prefix."components WHERE name = '".$c_name."'", ARRAY_A);
		$comp_id = $get_component[0]['component_id'];
		$conditions[$cnt] = array(
			$term_id => array(
				'id' => $comp_id,
				'data' => $option
			)
		);
	}
}		  
if(!empty($data[4])){
	$term_meta = get_term_by('name', strtolower('boot type'), 'configuration');
	$term_id = $term_meta->term_id;
	$arr_dt = explode(';',$data[4]);
	$c_name = '';
	foreach($arr_dt as $dt){
		$dts_arr = explode(':',$dt);
		if($c_name != $dts_arr[0]){
			$cnt++;
			$option = array();
			$option[] = strtolower($dts_arr[1]);
		}
		else{
			$option[] = strtolower($dts_arr[1]);
		}
		$c_name = $dts_arr[0];
		$get_component = $wpdb->get_results("SELECT component_id FROM ".$wpdb->prefix."components WHERE name = '".$c_name."'", ARRAY_A);
		$comp_id = $get_component[0]['component_id'];
		$conditions[$cnt] = array(
			$term_id => array(
				'id' => $comp_id,
				'data' => $option
			)
		);
	}
}
$conditions = json_encode($conditions);
$import = array(
	'manufacturer_part_no' => $data[1],
	'cable_conditions' => $conditions
);
if(!empty($data[0])){
	$where['cable_id'] = $data[0];
	$wpdb->update($wpdb->prefix."cable_pricing",$import,$where);
}
}
}
}
}

?>
<div class="wrap confgrtn-listng">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
<h1 class="wp-heading-inline">Cable Pricing</h1>
<a href="<?php echo admin_url('edit.php?post_type=product&page=add_cable_price') ?>" class="confg-btn">Add New</a>
<style type="text/css">
.hidedata{display: none;}
.hiddenfile{width: 0px;height: 0px;overflow: hidden;}		
.dt-buttons{margin-left:170px;}
.dt-buttons button{margin-left:20px;}		
</style>
<div class="hiddenfile">
<form name="importcsv_pricing_frm" method="POST" action="" id="importcsv_pricing_frm" enctype="multipart/form-data">
<input type="hidden" name="csvpimport" value="1">
<input type="file" name="importcsv_pricing" id="importcsv_pricing"/>
</form>		
<form name="delete_pricing_frm" method="POST" action="" id="delete_pricing_frm">
  <input type="hidden" name="delp" value="1"/>
  <input type="hidden" name="del_ids" id="del_ids"/>
</form>
<form name="importcsv_conditions_frm" method="POST" action="" id="importcsv_conditions_frm" enctype="multipart/form-data">
<input type="hidden" name="csvcimport" value="1">
<input type="file" name="importcsv_conditions" id="importcsv_conditions"/>
</form>
</div>			
<table id="cablepricing_tbl" class="display" cellspacing="0" width="100%" style="display: none;">
<thead>
<tr>
<th><input type="checkbox" name="selectAll" id="selectAllDomainList" /></th>
<th class="hidedata">Id</th>
<th>Cable Id</th>
<th>Wire Color</th>
<th>Fanout Color</th>
<th>Thickness</th>
<th>Price</th>
<th class="hidedata">Price Per Inch</th>
<th class="hidedata">Price Per Meter</th>
<th class="hidedata">Price Per Foot</th>
<th>Weight</th>
<th class="hidedata">Weight Per Inch</th>
<th class="hidedata">Weight Per Meter</th>
<th class="hidedata">Weight Per Foot</th>
<th>Manufacturer Part Number</th>
<th class="hidedata" class="hidedata">Status(Active/Inactive)</th>
<th class="hidedata">Connector A</th>
<th class="hidedata">Connector B</th>
<th class="hidedata">Boot Type</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<?php 
global $wpdb;
$data = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."cable_pricing ORDER BY id  DESC");	
foreach ($data as $data_value) 
{
	$connectorA = '';$connectorB = '';$boottype = '';
	if(!empty($data_value->cable_conditions)){
		$cond_arr = json_decode($data_value->cable_conditions);
		$connectorA_arr = array();$connectorB_arr = array();$boot_arr = array();
		foreach ($cond_arr as $conditions){
			foreach($conditions as $term => $component){
				$term_type = get_term_by('id', $term, 'configuration');
				$comp_id = $component->id;
				$get_component = $wpdb->get_results( "SELECT name FROM ".$wpdb->prefix."components WHERE component_id = '".$comp_id."'", ARRAY_A);
				$comp_name = $get_component[0]['name'];
				foreach($component->data as $color){
					if(strtolower($term_type->name) == "connector a"){
						$connectorA_arr[] = $comp_name.':'.$color;
					}
					if(strtolower($term_type->name) == "connector b"){
						$connectorB_arr[] = $comp_name.':'.$color;
					}
					if(strtolower($term_type->name) == "boot type"){
						$boot_arr[] = $comp_name.':'.$color;
					}
				}
			}
		}
		$connectorA = implode(';',$connectorA_arr);
		$connectorB = implode(';',$connectorB_arr);
		$boottype = implode(';',$boot_arr);

	}
?>
<tr>
	<td><input class="remove-pricing" name="checkbox[]" type="checkbox" value="<?php echo $data_value->id; ?>"></td>
	<td class="hidedata"><?php echo $data_value->id; ?></td>
	<td><?php echo $data_value->cable_id; ?></td>
	<td><?php echo $data_value->wire_color; ?></td>
	<td><?php echo $data_value->fanout_color; ?></td>
	<td><?php echo $data_value->thickness; ?></td>
	<td>
		<?php 
			$cable_price = json_decode($data_value->price); 
			if($cable_price->i != "" || $cable_price->i != "")
			{
				echo "I : $".$cable_price->i;
			}
			else
			{
				echo "I : -";
			}
			if($cable_price->m != "" || $cable_price->m != "")
			{
				echo "M : $".$cable_price->m."<br>";
			}
			else
			{
				echo "M : -<br>";
			}
			if($cable_price->f != "" || $cable_price->f != "")
			{
				echo "F : $".$cable_price->f."<br>";
			}
			else
			{
				echo "F : -<br>";
			}							
		?>						
	</td>
	<td class="hidedata"><?php echo $cable_price->i; ?></td>
	<td class="hidedata"><?php echo $cable_price->m; ?></td>
	<td class="hidedata"><?php echo $cable_price->f; ?></td>
	<td>
		<?php 
			$cable_weight = json_decode($data_value->weight); 
			if($cable_weight->i != "" || $cable_weight->i != "")
			{
				echo "I : ".wc_format_weight($cable_weight->i)."&nbsp;&nbsp;";
			}
			else
			{
				echo "I : -";
			}
			if($cable_weight->m != "" || $cable_weight->m != "")
			{
				echo "M : ".wc_format_weight($cable_weight->m)."<br>";
			}
			else
			{
				echo "M : -<br>";
			}
			if($cable_weight->f != "" || $cable_weight->f != "")
			{
				echo "F : ".wc_format_weight( $cable_weight->f )."<br>";
			}
			else
			{
				echo "F : -<br>";
			}							
		?>						
	</td>	
	<td class="hidedata"><?php echo $cable_weight->i; ?></td>
	<td class="hidedata"><?php echo $cable_weight->m; ?></td>
	<td class="hidedata"><?php echo $cable_weight->f; ?></td>
	<td><?php echo $data_value->manufacturer_part_no; ?></td>
	<td class="hidedata"><?php echo ($data_value->status == 1)?'Active':'Inactive';?></td>
	<td class="hidedata"><?php echo $connectorA; ?></td>
	<td class="hidedata"><?php echo $connectorB; ?></td>
	<td class="hidedata"><?php echo $boottype; ?></td>
	<td><a href="<?php echo admin_url('edit.php?post_type=product&page=cable_pricing_update&cable_price_id='.$data_value->id); ?>" class='edit-btn' title="Edit Pricing">Edit</a> <a href='javascipt:void(0);' class='delete-btn deleteCablePricing' onclick='delete_data(this);return false' data-id="<?php echo $data_value->id; ?>" title="Delete Pricing">Delete</a><a href="<?php echo admin_url('edit.php?post_type=product&page=manage_conditions&cable_price_id='.$data_value->id); ?>" class='setting-btn' title="Manage Conditions" >Manage Conditions</a></td>
</tr>
<?php } ?>
</tbody>
</table>
</div>

<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.0/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.0/js/buttons.flash.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.0/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.0/js/buttons.print.min.js"></script>
<script type="text/javascript">
$( document ).ready(function() {	

$(".sMsg").delay(2500).fadeOut(1600);
$('#cablepricing_tbl').show();
var table = $('#cablepricing_tbl').DataTable({
"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
"dom": 'lfrtipB',
"columnDefs": [{
"targets": [0,19],
"orderable": false
}],
buttons: [
{
text: 'Remove Selected',
action: function ( e, dt, node, config ) {
	var arrnew = [];
	$('.remove-pricing').each(function(){
		if($(this).is(':checked')){
			arrnew.push($(this).val());
		}
	});
	if(arrnew.length > 0){
		if(confirm('Are you sure to remove '+arrnew.length+' cable(s)')){
        	ids = arrnew.join();
        	$('#del_ids').val(ids);
        	$('#delete_pricing_frm').submit();
        }
	}
	else{
		alert('No Cable Selected');
	}
}
},
{
extend: 'csv',
text: 'Export Pricing',
title: 'CablePricing',
exportOptions: 
{
columns: [1,2,3,4,5,7,8,9,11,12,13,14,15]
}
},
{
text: 'Import Pricing',
action: function ( e, dt, node, config ) {
$('#importcsv_pricing').focus().trigger('click');
}
},
{
extend: 'csv',
text: 'Export Conditions',
title: 'CableConditions',
exportOptions: 
{
columns: [2,14,16,17,18]
}
},
{
text: 'Import Conditions',
action: function ( e, dt, node, config ) {
$('#importcsv_conditions').focus().trigger('click');
}
},
]
});
table.order( [ 1, 'desc' ] ).draw();  

// Upload Pricing Import file
$('#importcsv_pricing').change(function(){    
$( "button span:contains('Import Pricing')" ).text( 'Importing.....' );
$('#importcsv_pricing_frm').submit();
});

// Upload Conditions Import file
$('#importcsv_conditions').change(function(){    
$( "button span:contains('Import Conditions')" ).text( 'Importing.....' );
$('#importcsv_conditions_frm').submit();
});
});
function delete_data($this) 
{		
var result = confirm("Want to delete?");
if (result) 
{
jQuery('.deleteCablePricing').prop('disabled', true);
var id = jQuery($this).data('id');
jQuery.ajax({
type: "POST",
url :"<?php echo plugin_dir_url(__FILE__)?>../../ajax/delete_cablepricing.php", 			
data: {"delete_id" : id, "action" : "delete"},
success: function(response) 
{				
if(jQuery.trim(response) == 1)
{
	alert('Cable ID  Deleted Successfully.');
	location.reload();
}
else
{
	alert('Please try again');
}
}
});
return false;	
}
}
$('#selectAllDomainList').click (function () {

		var checkedStatus = this.checked;
		$('#cablepricing_tbl tbody tr').find('td:first :checkbox').each(function () {
			$(this).prop('checked', checkedStatus);
		});
	});
</script>
<?php 
} //cable_pricing_callback ends here

add_action("admin_menu", "create_add_cable_pricing");
function create_add_cable_pricing() 
{
add_submenu_page("edit.php?post_type=product", "Add Cable Pricing", "Add Cable Pricing", 0, "add_cable_price", "add_cable_pricing_callback");
}
function add_cable_pricing_callback()
{
?>
<div class="wrap">
<div class="content">
<h1>Add Cable Pricing</h1>
<?php 
global $wpdb;
session_start();	 
if(isset($_POST['save']))
{
$cable_id = $_POST['cable_id'];	
$wire_color = $_POST['wire_color'];
$fanout_color = $_POST['fanout_color'];
$cable_thickness = $_POST['cable_thickness'];
$price = [		
'i' => $_POST['per_inch_price'],
'm' => $_POST['per_meter_price'],
'f' => $_POST['per_foot_price']		
];	
$weight = [		
'i' => $_POST['per_inch_weight'],
'm' => $_POST['per_meter_weight'],
'f' => $_POST['per_foot_weight']		
];
$manufacturer_part_no = $_POST['manufacturer_part_no'];
$status = $_POST['status'];


$get_price = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."cable_pricing WHERE cable_id = '".$cable_id."'", ARRAY_A);

if(count($get_price) == 0)				
{
$insert = $wpdb->insert($wpdb->prefix . 'cable_pricing', array(
'cable_id' => $cable_id,	
'wire_color' => $wire_color,
'fanout_color' => $fanout_color,
'thickness' => $cable_thickness,
'price' => json_encode($price),
'weight' => json_encode($weight),
'manufacturer_part_no' => $manufacturer_part_no,
'cable_conditions' => '',
'status' => $status
));		
if($insert)
{					
$_SESSION['msg'] = "success";
header("Location:".admin_url("edit.php?post_type=product&page=cable_pricing"));
} 
else 
{
echo '<div class="error fade eMsg"><p><strong>Error! Please Try Again.</strong></p></div>';
}
}
else
{
echo '<div class="error fade eMsg"><p><strong>Cable Id already exists.</strong></p></div>';					
}	
}
?>
<form name="add_cable_price" method="POST" action="" id="add_cable_price">
<div id="col-full" class="main-div Add-Configuration addPro-comp">
<div class="main-secondary form-wrap">				 
	<div class="form-field">
		<label>Cable Id</label>
		<input type="text" name="cable_id" id="cable_id">						
	</div>
	<div class="form-field">
		<label>Wire Color</label>
		<select name="wire_color">
			<option value="">--Select Color--</option>
			<option value="yellow">Yellow</option>
			<option value="aqua">Aqua</option>
			<option value="orange">Orange</option>
			<option value="black">Black</option>							
		</select>
	</div>
	<div class="form-field">
		<label>Fanout Color</label>
		<select name="fanout_color">
			<option value="">--Select Color--</option>
			<option value="yellow">Yellow</option>
			<option value="aqua">Aqua</option>
			<option value="orange">Orange</option>
			<option value="black">Black</option>
			<option value="multi">Multi 6</option>
		</select>
	</div>
	<div class="form-field">
		<label>Thickness</label>
		<select name="cable_thickness">
			<option value="">--Select--</option>
			<option value="5">5</option>
			<option value="10">10</option>
			<option value="15">15</option>
		</select>
	</div>
	<div class="form-field">
		<label>Price Per Inch</label>
		<input type="text" name="per_inch_price" id="inch" class="price_group">
	</div>
	<div class="form-field">
		<label>Price Per Meter</label>
		<input type="text" name="per_meter_price" id="meter" class="price_group">
	</div>
	<div class="form-field">
		<label>Price Per Foot</label>
		<input type="text" name="per_foot_price" id="foot" class="price_group">
	</div>		
	<div class="form-field">
		<label>Weight Per Inch</label>
		<input type="text" name="per_inch_weight" id="inch_weight" class="weight_group">
	</div>		
	<div class="form-field">
		<label>Weight Per Meter</label>
		<input type="text" name="per_meter_weight" id="meter_weight" class="weight_group">
	</div>		
	<div class="form-field">
		<label>Weight Per Foot</label>
		<input type="text" name="per_foot_weight" id="foot_weight" class="weight_group">
	</div>
	<div class="form-field price_error" style="display: none;">
		<label></label>
		<label class="error">Prices do not match.</label>
	</div>							
	<div class="form-field">
		<label>Manufacturer Part Number</label>
		<input type="text" name="manufacturer_part_no" class="manufacturer_part_no">
	</div>	
	<div class="form-field">
		<label>Status</label>
		<input type="radio" name="status" value="1" checked="checked"> Active 
		<input type="radio" name="status" value="0"> Inactive
	</div>
	<input class="button button-primary" type="submit" name="save" value="Save">				
</div>			
</div>
</form>
</div>
</div>
<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script type="text/javascript">

function convert_price(elem,from,to)
{
var m_i = 0.0254;
var f_i = 0.0833;
var f_m = 3.2808;
var m_f = 0.3048;
var i_f = 12;
var i_m = 39.3701;

if(from == 'm' && to == 'i')
{
var price = elem * m_i;		
}
if(from == 'f' && to == 'i' )
{
var price = elem * f_i;
}
if(from == 'f' && to == 'm')
{
var price = elem * f_m;
}	
if(from == 'm' && to == 'f')
{
var price = elem * m_f;
}	
if(from == 'i' && to == 'f')
{
var price = elem * i_f;
}	
if(from == 'i' && to == 'm')
{
var price = elem * i_m;
}
price = parseFloat(Math.round(price * 100) / 100).toFixed(2);
return price;
}


$('.price_group').keyup(function(e){
tabKeyPressed = e.keyCode == 9;
shiftKeyPressed = e.keyCode == 16;
if (tabKeyPressed || shiftKeyPressed) {
e.preventDefault();
return;
}
id = $(this).attr('id');
val = $(this).val();
if(id == "inch"){
m = convert_price(val,'i','m');
f = convert_price(val,'i','f');
$('#meter').val(m);
$('#foot').val(f);
}
if(id == "foot"){
m = convert_price(val,'f','m');
i = convert_price(val,'f','i');
$('#meter').val(m);
$('#inch').val(i);
}
if(id == "meter"){
f = convert_price(val,'m','f');
i = convert_price(val,'m','i');
$('#foot').val(f);
$('#inch').val(i);
}
});

$('.weight_group').keyup(function(e){
tabKeyPressed = e.keyCode == 9;
shiftKeyPressed = e.keyCode == 16;
if (tabKeyPressed || shiftKeyPressed) {
e.preventDefault();
return;
}
id = $(this).attr('id');
val = $(this).val();
if(id == "inch_weight"){
m = convert_price(val,'i','m');
f = convert_price(val,'i','f');
$('#meter_weight').val(m);
$('#foot_weight').val(f);
}
if(id == "foot_weight"){
m = convert_price(val,'f','m');
i = convert_price(val,'f','i');
$('#meter_weight').val(m);
$('#inch_weight').val(i);
}
if(id == "meter_weight"){
f = convert_price(val,'m','f');
i = convert_price(val,'m','i');
$('#foot_weight').val(f);
$('#inch_weight').val(i);
}
});

// Check Price on saving if it is matched
function check_conversion()
{
var inch = $("#inch").val();
var meter = $("#meter").val();
var foot = $("#foot").val();		

if(inch != "" && meter != "" && foot != "") 
{		
var price_m_i = convert_price(meter,'m','i');
var price_f_i = convert_price(foot,'f','i');
if(inch == price_m_i && inch == price_f_i)
{
$(".price_error").hide();
return true;
}
else
{		
$(".price_error").show();
return false;
}	
}
else if(inch != "" && meter != "")
{		
var price_m_i = convert_price(meter,'m','i');
if(inch == price_m_i)
{
$(".price_error").hide();
return true;
}
else
{		
$(".price_error").show();
return false;
}	
}
else if(inch != "" && foot != "")
{		
var price_f_i = convert_price(foot,'f','i');
if(inch == price_f_i)
{
$(".price_error").hide();
return true;
}
else
{		
$(".price_error").show();
return false;
}	
}	
else if(meter != "" && foot != "")
{		
var price_f_m = convert_price(foot,'f','m');
if(inch == price_f_m)
{
$(".price_error").hide();
return true;
}
else
{		
$(".price_error").show();
return false;
}	
}
else if((inch != "" && meter == "" && feet == "") || ( meter != "" && inch == "" && feet == "") || (feet != "" && inch == "" && meter == ""))
{		
$(".price_error").hide();
return true;
}
else
{		
$(".price_error").show();
return false;
}	
}

$(document).ready(function(){	
//Add Cable Price Form Validations 
jQuery.validator.addMethod("alphanumeric", function(value, element) {
return this.optional(element) ||/^[a-zA-Z0-9]+$/i.test(value);
}, "Letters and numbers only please");	

$("#add_cable_price").validate({		
rules:{
cable_id: {
required: true,	
alphanumeric: true					
},				
cable_thickness: "required",
per_inch_price:{
require_from_group: [1, '.price_group'],    		
number: true
},
per_meter_price: {
require_from_group: [1, '.price_group'],   
number: true
},
per_foot_price:{
require_from_group: [1, '.price_group'],    		
number: true
},			
},
messages : {
cable_id : {
required: "This is a required field",
alphanumeric : "Letters and numbers only please"
},			
cable_thickness : "This is a required field",
per_inch_price : {
require_from_group: "Please fill atleast one of the price fields",
number: "Only numbers and a decimal is allowed",
},
per_meter_price : {
require_from_group: "Please fill atleast one of the price fields",
number: "Only numbers and a decimal is allowed",
},
per_foot_price : {
require_from_group: "Please fill atleast one of the price fields",
number: "Only numbers and a decimal is allowed",
}			
}
});
});
</script>
<?php
} //add_cable_pricing_callback ends

//Update Cable Price 
add_action("admin_menu", "create_cable_price_menu_update");
function create_cable_price_menu_update() 
{
add_submenu_page("edit.php?post_type=product", "Cable Pricing Update", "Cable Pricing Update", 0, "cable_pricing_update", "cable_pricing_update_callback");
}
function cable_pricing_update_callback()
{
global $wpdb;
session_start();
$cable_price_id = $_GET['cable_price_id'];	
$get_cable_pricing = $wpdb->get_row( "SELECT * FROM ".$wpdb->prefix."cable_pricing WHERE id = ".$cable_price_id."", ARRAY_A);
$cable_price = json_decode($get_cable_pricing['price']);	
$cable_weight = json_decode($get_cable_pricing['weight']);	
$meter_price = $cable_price->m; 	
$foot_price = $cable_price->f;	
$inch_price = $cable_price->i;
$meter_weight = $cable_weight->m; 	
$foot_weight = $cable_weight->f;	
$inch_weight = $cable_weight->i;
if(isset($_POST['update']))
{	
$cable_id = $_POST['cable_id'];	
$wire_color = $_POST['wire_color'];
$fanout_color = $_POST['fanout_color'];
$cable_thickness = $_POST['cable_thickness'];
$price = [	
'i' => $_POST['per_inch_price'],	
'm' => $_POST['per_meter_price'],
'f' => $_POST['per_foot_price']			
];	
$weight = [	
'i' => $_POST['per_inch_weight'],	
'm' => $_POST['per_meter_weight'],
'f' => $_POST['per_foot_weight']			
];	
$manufacturer_part_no = $_POST['manufacturer_part_no'];
$status = $_POST['status'];


$get_price = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."cable_pricing WHERE cable_id = '".$cable_id."' and id != '".$cable_price_id."'", ARRAY_A);
if(count($get_price) == 0)
{
$update = $wpdb->update($wpdb->prefix . 'cable_pricing', array(
	'cable_id' => $cable_id,	
	'wire_color' => $wire_color,
	'fanout_color' => $fanout_color,
	'thickness' => $cable_thickness,
	'price' => json_encode($price),
	'weight' => json_encode($weight),
	'manufacturer_part_no' => $manufacturer_part_no,
	'cable_conditions' => '',
	'status' => $status
), 
array(
	'id' => $cable_price_id
)
);		
if($update === 1)
{					
$_SESSION['msg'] = "updated";
header("Location:".admin_url("edit.php?post_type=product&page=cable_pricing"));
} 
else if($update === 0)
{
$_SESSION['msg'] = "updated";
header("Location:".admin_url("edit.php?post_type=product&page=cable_pricing"));
}
else if($update === false)
{
echo '<div class="error fade eMsg"><p><strong>Error! Please Try Again.</strong></p></div>';
}
}
else
{
echo '<div class="error fade eMsg"><p><strong>Cable Id already exists.</strong></p></div>';					
}	
}
?>
<div class="wrap">
<div class="content">
<h1>Update Cable Pricing</h1>
<form name="update_cable_price" method="POST" action="" id="update_cable_price">
<div id="col-full" class="main-div Add-Configuration addPro-comp">
<div class="main-secondary form-wrap">				 
	<div class="form-field">
		<label>Cable Id</label>
		<input type="text" name="cable_id" id="cable_id" value="<?php echo $get_cable_pricing['cable_id']; ?>">						
	</div>
	<div class="form-field">
		<label>Wire Color</label>
		<select name="wire_color">
			<option value="">--Select Color--</option>
			<option value="yellow" "<?php if($get_cable_pricing['wire_color'] == 'yellow'){ ?>" selected = "selected" "<?php } ?>" >Yellow</option>
			<option value="aqua" "<?php if($get_cable_pricing['wire_color'] == 'aqua'){ ?>" selected = "selected" "<?php } ?>" >Aqua</option>
			<option value="orange" "<?php if($get_cable_pricing['wire_color'] == 'orange'){ ?>" selected = "selected" "<?php } ?>" >Orange</option>
			<option value="black" "<?php if($get_cable_pricing['wire_color'] == 'black'){ ?>" selected = "selected" "<?php } ?>">Black</option>
		</select>
	</div>
	<div class="form-field">
		<label>Fanout Color</label>
		<select name="fanout_color">
			<option value="">--Select Color--</option>
			<option value="yellow" "<?php if($get_cable_pricing['fanout_color'] == 'yellow'){ ?>" selected = "selected" "<?php } ?>">Yellow</option>
			<option value="aqua" "<?php if($get_cable_pricing['fanout_color'] == 'aqua'){ ?>" selected = "selected" "<?php } ?>">Aqua</option>
			<option value="orange" "<?php if($get_cable_pricing['fanout_color'] == 'orange'){ ?>" selected = "selected" "<?php } ?>" >Orange</option>
			<option value="black" "<?php if($get_cable_pricing['fanout_color'] == 'black'){ ?>" selected = "selected" "<?php } ?>">Black</option>
			<option value="multi" "<?php if($get_cable_pricing['fanout_color'] == 'multi'){ ?>" selected = "selected" "<?php } ?>" >Multi</option>
		</select>
	</div>
	<div class="form-field">
		<label>Thickness</label>						
		<select name="cable_thickness">
			<option value="">--Select--</option>
			<option value="5" <?php if($get_cable_pricing['thickness'] == 5){ echo "selected"; } ?> >5</option>
			<option value="10" <?php if($get_cable_pricing['thickness'] == 10){ echo "selected"; } ?> >10</option>
			<option value="15" <?php if($get_cable_pricing['thickness'] == 15){ echo "selected"; } ?> >15</option>
		</select>
	</div>
	<div class="form-field">
		<label>Price Per Inch</label>
		<input type="text" class="price_group" id="inch" name="per_inch_price" value="<?php echo $inch_price; ?>">
	</div>
	<div class="form-field">
		<label>Price Per Meter</label>
		<input type="text" class="price_group" id="meter" name="per_meter_price" value="<?php echo $meter_price; ?>">
	</div>
	<div class="form-field">
		<label>Price Per Foot</label>
		<input type="text" class="price_group" id="foot" name="per_foot_price" value="<?php echo $foot_price; ?>">
	</div>	
	<div class="form-field">
		<label>Weight Per Inch</label>
		<input type="text" name="per_inch_weight" id="inch_weight" class="weight_group" value="<?php echo $inch_weight; ?>">
	</div>		
	<div class="form-field">
		<label>Weight Per Meter</label>
		<input type="text" name="per_meter_weight" id="meter_weight" class="weight_group" value="<?php echo $meter_weight; ?>"> 
	</div>		
	<div class="form-field">
		<label>Weight Per Foot</label>
		<input type="text" name="per_foot_weight" id="foot_weight" class="weight_group" value="<?php echo $foot_weight; ?>">
	</div>	
	<div class="form-field price_error" style="display: none;">
		<label></label>
		<label class="error">Prices do not match.</label>
	</div>											
	<div class="form-field">
		<label>Manufacturer Part Number</label>
		<input type="text" name="manufacturer_part_no" class="manufacturer_part_no" value="<?php echo $get_cable_pricing['manufacturer_part_no']; ?>">
	</div>	
	<div class="form-field">
		<label>Status</label>
		<input type="radio" name="status" value="1" <?php if($get_cable_pricing['status'] == "1"){ echo "checked"; } ?>> Active 
		<input type="radio" name="status" value="0" <?php if($get_cable_pricing['status'] == "0"){ echo "checked"; } ?>> Inactive
	</div>
	<input class="button button-primary" type="submit" name="update" value="Update">			
</div>			
</div>
</form>
</div>
</div>
<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

<script type="text/javascript">
function convert_price(elem,from,to)
{
var m_i = 0.0254;
var f_i = 0.0833;
var f_m = 3.2808;
var m_f = 0.3048;
var i_f = 12;
var i_m = 39.3701;

if(from == 'm' && to == 'i')
{
var price = elem * m_i;		
}
if(from == 'f' && to == 'i' )
{
var price = elem * f_i;
}
if(from == 'f' && to == 'm')
{
var price = elem * f_m;
}	
if(from == 'm' && to == 'f')
{
var price = elem * m_f;
}	
if(from == 'i' && to == 'f')
{
var price = elem * i_f;
}	
if(from == 'i' && to == 'm')
{
var price = elem * i_m;
}
price = parseFloat(Math.round(price * 100) / 100).toFixed(2);
return price;
}


$('.price_group').keyup(function(e){
tabKeyPressed = e.keyCode == 9;
shiftKeyPressed = e.keyCode == 16;
if (tabKeyPressed || shiftKeyPressed) {
e.preventDefault();
return;
}
id = $(this).attr('id');
val = $(this).val();
if(id == "inch"){
m = convert_price(val,'i','m');
f = convert_price(val,'i','f');
$('#meter').val(m);
$('#foot').val(f);
}
if(id == "foot"){
m = convert_price(val,'f','m');
i = convert_price(val,'f','i');
$('#meter').val(m);
$('#inch').val(i);
}
if(id == "meter"){
f = convert_price(val,'m','f');
i = convert_price(val,'m','i');
$('#foot').val(f);
$('#inch').val(i);
}
});

$('.weight_group').keyup(function(e){
tabKeyPressed = e.keyCode == 9;
shiftKeyPressed = e.keyCode == 16;
if (tabKeyPressed || shiftKeyPressed) {
e.preventDefault();
return;
}
id = $(this).attr('id');
val = $(this).val();
if(id == "inch_weight"){
m = convert_price(val,'i','m');
f = convert_price(val,'i','f');
$('#meter_weight').val(m);
$('#foot_weight').val(f);
}
if(id == "foot_weight"){
m = convert_price(val,'f','m');
i = convert_price(val,'f','i');
$('#meter_weight').val(m);
$('#inch_weight').val(i);
}
if(id == "meter_weight"){
f = convert_price(val,'m','f');
i = convert_price(val,'m','i');
$('#foot_weight').val(f);
$('#inch_weight').val(i);
}
});
// Check Price(s) on saving if it is matched

function check_conversion()
{
var inch = $("#inch").val();
var meter = $("#meter").val();
var foot = $("#foot").val();		

if(inch != "" && meter != "" && foot != "") 
{		
var price_m_i = convert_price(meter,'m','i');
var price_f_i = convert_price(foot,'f','i');
if(inch == price_m_i && inch == price_f_i)
{
$(".price_error").hide();
return true;
}
else
{		
$(".price_error").show();
return false;
}	
}
else if(inch != "" && meter != "")
{		
var price_m_i = convert_price(meter,'m','i');
if(inch == price_m_i)
{
$(".price_error").hide();
return true;
}
else
{		
$(".price_error").show();
return false;
}	
}
else if(inch != "" && foot != "")
{		
var price_f_i = convert_price(foot,'f','i');
if(inch == price_f_i)
{
$(".price_error").hide();
return true;
}
else
{		
$(".price_error").show();
return false;
}	
}	
else if(meter != "" && foot != "")
{		
var price_f_m = convert_price(foot,'f','m');
if(inch == price_f_m)
{
$(".price_error").hide();
return true;
}
else
{		
$(".price_error").show();
return false;
}	
}
else if((inch != "" && meter == "" && feet == "") || ( meter != "" && inch == "" && feet == "") || (feet != "" && inch == "" && meter == ""))
{		
$(".price_error").hide();
return true;
}
else
{		
$(".price_error").show();
return false;
}	
}
$(document).ready(function(){	
//Add Cable Price Form Validations 
jQuery.validator.addMethod("alphanumeric", function(value, element) {
return this.optional(element) ||/^[a-zA-Z0-9]+$/i.test(value);
}, "Letters and numbers only please");

$("#update_cable_price").validate({
rules:{
cable_id: {
required: true,		
alphanumeric: true		
},				
cable_thickness: "required",
per_inch_price:{
require_from_group: [1, '.price_group'],
number: true
},
per_meter_price: {
require_from_group: [1, '.price_group'],     
number: true
},
per_foot_price:{
require_from_group: [1, '.price_group'],
number: true
}
},
messages : {
cable_id : {
required: "This is a required field",
alphanumeric : "Letters and numbers only please"
},			
cable_thickness : "This is a required field",
per_inch_price : {
require_from_group: "Please fill atleast one of the price fields",
number: "Only numbers and a decimal is allowed",
},
per_meter_price : {
require_from_group: "Please fill atleast one of the price fields",
number: "Only numbers and a decimal is allowed",
},
per_foot_price : {
require_from_group: "Please fill atleast one of the price fields",
number: "Only numbers and a decimal is allowed",
}			
}
});
});
</script>
<?php
} //cable_pricing_update_callback ends here

// Manage Cable Conditions based on cable id 
add_action("admin_menu", "manage_conditions");
function manage_conditions() 
{
add_submenu_page("edit.php?post_type=product", "Manage Conditions", "Manage Conditions", 0, "manage_conditions", "manage_conditions_callback");
}
function manage_conditions_callback()
{
?>
<div class="wrap">
<div class="content">
<h1>Manage Conditions</h1>
<?php
global $wpdb;
session_start();
$cable_price_id = $_GET['cable_price_id'];	
$get_data = $wpdb->get_row( "SELECT cable_id, cable_conditions FROM ".$wpdb->prefix."cable_pricing WHERE id = ".$cable_price_id."", ARRAY_A);		
if(isset($_POST['save']))
{
$conditions_arr = [];
$conditions_data = $_POST['conditions_data'];
$conditions_child_data = $_POST['conditions_child_data'];	


	foreach($conditions_child_data	as $parent_id => $child_array){
		foreach ($child_array as $child_id => $child_value) {
			$conditions_arr[][$parent_id] =array(
				'id' => $child_id,
				'data' => $child_value
			);
		}
	}			  	
$update_conditions = $wpdb->update($wpdb->prefix . 'cable_pricing', array(
          'cable_conditions' => json_encode($conditions_arr)                    
        ), 
        array(
            'id' => $cable_price_id
        )
    );      
if($update_conditions === 1)
{                   
$_SESSION['msg'] = "conditions_success";
header("Location:".admin_url("edit.php?post_type=product&page=cable_pricing"));
} 
else if($update_conditions === 0)
{
$_SESSION['msg'] = "conditions_success";
header("Location:".admin_url("edit.php?post_type=product&page=cable_pricing"));
}
else if($update_conditions === false)
{
echo '<div class="error fade eMsg"><p><strong>Error! Please Try Again.</strong></p></div>';
}
}
?>
<form name="update_cable_price" method="POST" action="" id="update_cable_price">
<div id="col-full" class="main-div Add-Configuration addPro-comp">
<div class="main-secondary form-wrap">				 
	<div class="form-field">
		<label>Cable Id: </label>
		<input type="text" name="cable_id" id="cable_id" value="<?php echo $get_data['cable_id']; ?>" readonly>						
	</div>
	<?php
		$terms = get_terms('configuration', array(
				'hide_empty' => false,
				'parent' => 0
			)); 
		$conditions_data = json_decode($get_data['cable_conditions'],true);			
		foreach($conditions_data as $data_value)
		{	
			foreach($data_value as $data_key=>$data_val)
			{
				$data_keys[] = $data_key;
				$sub_key = $data_key."_".$data_val['id'];
				$data_arr[] = $sub_key;
				$sub_data_arr[$sub_key] = $data_val['data'];
			}
		}
		
		foreach($terms as $term)
		{
			$term_id =$term->term_id;
			$term_name = $term->name;
			if($term_name == "Connector A" || $term_name == "Connector B" || $term_name == "Boot Type")
			{
				$get_components = $wpdb->get_results("SELECT * FROM x2mnb_components WHERE configuration_id like '%".$term_id."%'", ARRAY_A);	
				if(count($get_components) > 0)
				{
	?>
		<div class="form-field">								
			<label><?php echo $term_name; ?></label>	
			<ul class="optionList">			
				<?php 														
					$n=1;
					foreach ($get_components as $component_key => $component_value)
					{																				
							$comp_id = $component_value['component_id'];
							$sub_key = $term_id."_".$comp_id;								
							if(in_array($sub_key, $data_arr))
							{										
								$checked = "checked";
							}
							else
							{										
								$checked = "";
							}
							echo "<li><div class='parent-checkboxOuter'><input type='checkbox' class='parent-checkbox parentdata_".$term_id."_".$comp_id." parentitem_".$term_id."' id='conditions_data_".$term_id."' data-termid=".$term_id." data-compid=".$comp_id." name='conditions_data[".$term_id."][]' value='".$comp_id."' ".$checked." onclick='update_child_checkbox(this,".$term_id.",".$comp_id.")' >".$component_value['name']."</div></li>";
							if($component_value['name'] == 'Pigtail'){
								echo "<li>
								<div class='child-checkboxOuter'>
								<input type='checkbox' class='child-checkbox childdata_".$term_id."_".$comp_id."' name='conditions_child_data[".$term_id."][".$comp_id."][]' value='".$comp_id."' ".$checked." onclick='update_parent_checkbox(this,".$term_id.",".$comp_id.")' >pigtail</div></li>";
							}
							if($component_value['component_type'] == "connector"  || $component_value['component_type'] == "boot")
							{
								$canvas_imgs = json_decode($component_value['canvas_image'], true);
								$color = "";

								foreach($canvas_imgs as $canvas_img)
								{						
									$pigtail = 	$canvas_img['pigtail'];
									$brk_name = explode("_",$canvas_img['img']);
									$get_color = explode(".",$brk_name[2]);	
									$nxt_color = $get_color[0];
									if($color == "" || $nxt_color != $color)
									{												
										if(array_key_exists($sub_key, $sub_data_arr) && in_array($nxt_color,$sub_data_arr[$sub_key]))
										{
											$color_check = "checked";
										}
										else
										{
											$color_check = "";
										}													
	echo "<li>
	<div class='child-checkboxOuter'>
	<input type='checkbox' class='child-checkbox childdata_".$term_id."_".$comp_id."' name='conditions_child_data[".$term_id."][".$comp_id."][]' value='".$nxt_color."' ".$color_check." onclick='update_parent_checkbox(this,".$term_id.",".$comp_id.")' >".$nxt_color."</div></li>";	

									}
									$color = $brk_name[2];
								}
							} // component type if closed								
					} // get components foreach closed
				?>
			</ul>					
		</div>
	<?php	
				} // Component Count if closed 
			} // Terms if closed											
		} //Terms foreach closed					
	?>
	<input class="button button-primary" type="submit" name="save" value="Save">			
</div>			
</div>
</form>
</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript">
function update_child_checkbox(check_val,termid, compid)
{			
if (check_val.checked) {
$(".childdata_"+termid+"_"+compid).prop('checked', true);		    
} else {
$(".childdata_"+termid+"_"+compid).prop('checked', false);
}
var parentItemCount = $(".parentitem_"+termid).length;
var parentCheckCount = $('input[name="conditions_data['+termid+'][]"]:checked').length;
if(parentCheckCount == parentItemCount)
{
$(".parentdata_"+termid+"_"+compid).prop('checked', false);
$(".childdata_"+termid+"_"+compid).prop('checked', false);
alert("You can not select all items in the list.");
}
}
function update_parent_checkbox(check_val,termid,compid)
{
var numItems = $(".childdata_"+termid+"_"+compid).length;			
var check_count = $('input[name="conditions_child_data['+termid+']['+compid+'][]"]:checked').length;			
if(check_count < numItems)
{
$(".parentdata_"+termid+"_"+compid).prop('checked', false);
}
if(check_count == numItems)
{
$(".parentdata_"+termid+"_"+compid).prop('checked', true);
}
var parentItemCount = $(".parentitem_"+termid).length;
var parentCheckCount = $('input[name="conditions_data['+termid+'][]"]:checked').length;
if(parentCheckCount == parentItemCount)
{
$(".parentdata_"+termid+"_"+compid).prop('checked', false);
$(".childdata_"+termid+"_"+compid).prop('checked', false);
alert("You can not select all items in the list.");
}
}
</script>
<?php
} //manage_conditions_calback ends
?>
