<?php
add_action('wp_ajax_importcsv_components', 'importcsv_components');
add_action('wp_ajax_nopriv_importcsv_components', 'importcsv_components');

add_action("admin_menu", "create_components_menu");
function create_components_menu()
{
	add_submenu_page("edit.php?post_type=product", "Components", "Components", 0, "components", "components_callback");
}

function component_import_notice(){
	global $pagenow;
	if (isset($_GET['page']) ) {
		if($_GET['page'] == 'components'){
			if(isset($_POST['csvimport'])){
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
add_action('admin_notices', 'component_import_notice');

function components_callback()
{
global $wpdb; $wp;
session_start();
if($_SESSION['msg'] == "success")
{
	echo '<div class="updated notice sMsg"><p><strong>Component Saved Successfully.</strong></p></div>';
	$_SESSION['msg'] = "";
}
if($_SESSION['msg'] == "updated")
{
	echo '<div class="updated notice sMsg"><p><strong>Component Updated Successfully.</strong></p></div>';
	$_SESSION['msg'] = "";
}
if(isset($_POST['delc'])){
	$ids = $_POST['del_ids'];
	$delete = $wpdb->query('DELETE FROM '.$wpdb->prefix.'components WHERE component_id IN('.$ids.')');
}
if(isset($_POST['csvimport'])){
	if(isset($_FILES['importcsv_components'])){
		$file = $_FILES['importcsv_components']['tmp_name'];
		if (($handle = fopen($file, "r")) !== FALSE) {
			$i=0;
		    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		    	$i++;
		    	if($i == 1) continue;
		    	if(!empty($data[0])){
		    		$group_array = explode(';',$data[4]);
		    		$gid = array();
		    		foreach ($group_array as $value) {
		    			$term = $wpdb->get_results('SELECT term_id FROM '.$wpdb->prefix.'terms where name="'.$value.'"');
		    			if(!empty($term[0]->term_id)) $gid[] = $term[0]->term_id;
		    		}
		    		$grp = json_encode($gid);

		    		$conf_array = explode(';',$data[5]);
		    		$cid = array();
		    		foreach ($conf_array as $value) {
		    			$term = $wpdb->get_results('SELECT term_id FROM '.$wpdb->prefix.'terms where name="'.$value.'"');
		    			if(!empty($term[0]->term_id)) $cid[] = $term[0]->term_id;
		    		}
		    		$conf = json_encode($cid);
		    		$canvas_image = '';
		    		if(!empty($data[10])){
		    			$img_arr = explode(';',$data[10]);
		    			$partno_arr = explode(';',$data[11]);
						$canvas_image_arr = array();
		    			for($i=0; $i<count($img_arr); $i++) {
							$canvas_image_arr[] = array(
								'img' => @$img_arr[$i],
								'partno' => @$partno_arr[$i]
							);
		    			}
		    			$canvas_image = json_encode($canvas_image_arr);
		    		}
		    		$menu_image = '';
		    		if(!empty($data[12])){
		    			$img_arr = explode(';',$data[12]);
		    			$menu_image_arr = array();
		    			for($i=0; $i<count($img_arr); $i++) {
							$menu_image_arr[] = @$img_arr[$i];
		    			}
		    			$menu_image = json_encode($menu_image_arr);
		    		}

		    		$update = array(
		    			'name' => $data[1],
		    			'description' => $data[2],
		    			'component_type' => $data[3],
		    			'group_id' => $grp,
		    			'configuration_id' => $conf,
		    			'part_number' => $data[6],
		    			'field_type' => ($data[7] == "fixed")?0:1,
		    			'price' => $data[8],
		    			'weight' => $data[9],
		    			'canvas_image' => $canvas_image,
		    			'menu_image' => $menu_image,
		    			'status' => ($data[13] == "Active")?1:0
		    		);
		    		$where['component_id'] = $data[0];
		    		$wpdb->update($wpdb->prefix."components",$update,$where);
		    	}
		    	else{
		    		$group_array = explode(';',$data[4]);
		    		$gid = array();
		    		foreach ($group_array as $value) {
		    			$term = $wpdb->get_results('SELECT term_id FROM '.$wpdb->prefix.'terms where name="'.$value.'"');
		    			if(!empty($term[0]->term_id)) $gid[] = $term[0]->term_id;
		    		}
		    		$grp = json_encode($gid);
		    		$conf_array = explode(';',$data[5]);
		    		$cid = array();
		    		foreach ($conf_array as $value) {
		    			$term = $wpdb->get_results('SELECT term_id FROM '.$wpdb->prefix.'terms where name="'.$value.'"');
		    			if(!empty($term[0]->term_id)) $cid[] = $term[0]->term_id;
		    		}
		    		$conf = json_encode($cid);
		    		$canvas_image = '';
		    		if(!empty($data[10])){
		    			$img_arr = explode(';',$data[10]);
		    			$partno_arr = explode(';',$data[11]);
		    			$canvas_image_arr = array();
		    			for($i=0; $i<count($img_arr); $i++) {
							$canvas_image_arr[] = array(
								'img' => @$img_arr[$i],
								'partno' => @$partno_arr[$i]
							);
		    			}
		    			$canvas_image = json_encode($canvas_image_arr);
		    		}
		    		$menu_image = '';
		    		if(!empty($data[12])){
		    			$img_arr = explode(';',$data[12]);
		    			$menu_image_arr = array();
		    			for($i=0; $i<count($img_arr); $i++) {
							$menu_image_arr[] = @$img_arr[$i];
		    			}
		    			$menu_image = json_encode($menu_image_arr);
		    		}

		    		$insert = array(
		    			'name' => $data[1],
		    			'description' => $data[2],
		    			'component_type' => $data[3],
		    			'group_id' => $grp,
		    			'configuration_id' => $conf,
		    			'part_number' => $data[6],
		    			'field_type' => ($data[7] == "fixed")?0:1,
		    			'price' => $data[8],
		    			'weight' => $data[9],
		    			'canvas_image' => $canvas_image,
		    			'menu_image' => $menu_image,
		    			'status' => ($data[13] == "Active")?1:0
		    		);
		    		//print_r($insert);
		    		$wpdb->insert($wpdb->prefix."components",$insert);
		    	}
		    }
		    fclose($handle);
		}
	}
}
?>
<div class="wrap confgrtn-listng">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
	<h1 class="wp-heading-inline">Components</h1>
	<a href="<?php echo admin_url('edit.php?post_type=product&page=component_add') ?>" class="confg-btn">Add New</a>
	<style type="text/css">
	.hidedata{display: none;}
	.hiddenfile{width: 0px;height: 0px;overflow: hidden;}
	.dt-buttons{margin-left:350px;}
	.dt-buttons button{margin-left:20px;}
	</style>
	<div class="hiddenfile">
	<form name="importcsv_components_frm" method="POST" action="" id="importcsv_components_frm" enctype="multipart/form-data">
	  <input type="hidden" name="csvimport" value="1">
	  <input type="file" name="importcsv_components" id="importcsv_components"/>
	</form>
	<form name="delete_components_frm" method="POST" action="" id="delete_components_frm">
	  <input type="hidden" name="delc" value="1"/>
	  <input type="hidden" name="del_ids" id="del_ids"/>
	</form>
	</div>
	<table id="components_tbl" class="display" cellspacing="0" width="100%" style="display: none;">
		<thead>
			<tr>
				<th><input type="checkbox" name="selectAll" id="selectAllDomainListC" /></th>
				<th class="hidedata">Id</th>
				<th>Name</th>
				<th class="hidedata">Description</th>
				<th>Component Type</th>
				<th class="hidedata">Group</th>
				<th class="hidedata">Configuration</th>
				<th>Part Number</th>
				<th class="hidedata">Field Type</th>
				<th class="hidedata">Price</th>
				<th class="hidedata">Weight</th>
				<th class="hidedata">Canvas Image</th>
				<th class="hidedata">Canvas Image Part Number</th>
				<th class="hidedata">Menu Image</th>
				<th class="hidedata">Status(Active/Inactive)</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$data = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."components ORDER BY component_id  DESC");
				foreach ($data as $data_value)
				{
					if(!empty($data_value->canvas_image)){
						$image_arr = json_decode($data_value->canvas_image);
						$img_arr = array();$part_arr = array();
						foreach($image_arr as $img){
							if(!empty($img->img)) $img_arr[] = $img->img;
							if(!empty($img->partno)) $part_arr[] = $img->partno;
						}
						$canvas_images = (!empty($img_arr))?implode(';', $img_arr):'';
						$canvas_partno = (!empty($part_arr))?implode(';', $part_arr):'';
					}
					else{
						$canvas_images = '';
						$canvas_partno = '';
					}
					if(!empty($data_value->menu_image)){
						$image_arr = json_decode($data_value->menu_image);
						$img_arr = array();
						foreach($image_arr as $img){
							if(!empty($img)) $img_arr[] = $img;
						}
						$menu_images = (!empty($img_arr))?implode(';', $img_arr):'';
					}
					else{
						$menu_images = '';
					}
			?>
				<tr>
					<td><input class="remove-component" name="checkbox[]" type="checkbox" value="<?php echo $data_value->component_id; ?>"></td>
					<td class="hidedata"><?php echo $data_value->component_id; ?></td>
					<td><?php echo $data_value->name; ?></td>
					<td class="hidedata"><?php echo $data_value->description; ?></td>
					<td><?php echo $data_value->component_type; ?></td>
					<td class="hidedata">
						<?php
							$group_ids = json_decode($data_value->group_id, true);
							$group_name = array();
							foreach ($group_ids as $gid)
							{
								$group_info = get_term_by('id', $gid, 'group');
    						$group_name[] = $group_info->name;
							}
							echo implode(";", $group_name);
						?>
					</td>
					<td class="hidedata">
						<?php
							$config_ids = json_decode($data_value->configuration_id, true);
							$config_name = array();
							foreach ($config_ids as $cid)
							{
								$config_info = get_term_by('id', $cid, 'configuration');
    						$config_name[] = $config_info->name;
							}
							echo implode(";", $config_name);
						?>
					</td>
					<td><?php echo $data_value->part_number; ?></td>
					<td class="hidedata"><?php if($data_value->field_type == 0) { echo "fixed"; } else { echo "changeable"; } ?></td>
					<td class="hidedata"><?php echo $data_value->price; ?></td>
					<td class="hidedata"><?php echo $data_value->weight; ?></td>
					<td class="hidedata"><?php echo $canvas_images; ?></td>
					<td class="hidedata"><?php echo $canvas_partno; ?></td>
					<td class="hidedata"><?php echo $menu_images; ?></td>
					<td class="hidedata"><?php if($data_value->status == 0){ echo "Inactive"; } else { echo "Active"; } ?></td>
					<td><a href="<?php echo admin_url('edit.php?post_type=product&page=components_update&component_id='.$data_value->component_id); ?>" class='edit-btn' title="Edit">Edit</a> <a href='javascipt:void(0);' class='delete-btn deleteComponent' onclick='delete_data(this);return false' data-id="<?php echo $data_value->component_id; ?>" title="delete">Delete</a></td>
				</tr>
			<?php
				}
			?>
		</tbody>
	</table>


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

	$("#components_tbl").show();
	var table =  $('#components_tbl').DataTable({
	"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
     "dom": 'lfrtipB',
	"columnDefs": [{
	"targets": [0,15],
	"orderable": false
	}],

        buttons: [
          {
            text: 'Remove Selected',
            action: function ( e, dt, node, config ) {
            	var arrnew = [];
            	$('.remove-component').each(function(){
            		if($(this).is(':checked')){
            			arrnew.push($(this).val());
            		}
            	});
            	if(arrnew.length > 0){
            		if(confirm('Are you sure to remove '+arrnew.length+' component(s)')){
		            	ids = arrnew.join();
		            	$('#del_ids').val(ids);
		            	$('#delete_components_frm').submit();
		            }
            	}
            	else{
            		alert('No Component Selected');
            	}
            }
          },
          {
            extend: 'csv',
            text: 'Export',
            title: 'CableComponents',
            exportOptions:
            {
              columns: [1,2,3,4,5,6,7,8,9,10,11,12,13,14]
            }
          },
          {
            text: 'Import',
            action: function ( e, dt, node, config ) {
				$('#importcsv_components').focus().trigger('click');
            }
          }
        ]
    });
	table.order( [ 1, 'desc' ] ).draw();

	// Upload Import file
	$('#importcsv_components').change(function(){
		$( "button span:contains('Import')" ).text( 'Importing.....' );
	    $('#importcsv_components_frm').submit();
	});
});
function delete_data($this)
{
	var result = confirm("Want to delete?");
	if (result)
	{
		jQuery('.deleteComponent').prop('disabled', true);
		var id = jQuery($this).data('id');
		jQuery.ajax({
			type: "POST",
			url :"<?php echo plugin_dir_url(__FILE__)?>../../ajax/delete_component.php",
			data: {"delete_id" : id, "action" : "delete"},
			success: function(response)
			{
				if(jQuery.trim(response) == 1)
				{
					alert('Component Deleted Successfully.');
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
$('#selectAllDomainListC').click (function () {

		var checkedStatus = this.checked;
		$('#components_tbl tbody tr').find('td:first :checkbox').each(function () {
			$(this).prop('checked', checkedStatus);
		});
	});
</script>
<?php
} // components_callback ends

// Function to change the upload directory path for canvas images
function canvas_imgs_upload_dir( $dirs )
{
  $dirs['subdir'] = '/customcable/canvas';
  $dirs['path'] = $dirs['basedir'] . '/customcable/canvas';
  $dirs['url'] = $dirs['baseurl'] . '/customcable/canvas';
  return $dirs;
}

// Function to change the upload directory path for menu images
function menu_imgs_upload_dir( $dirs )
{
  $dirs['subdir'] = '/customcable/menu';
  $dirs['path'] = $dirs['basedir'] . '/customcable/menu';
  $dirs['url'] = $dirs['baseurl'] . '/customcable/menu';
  return $dirs;
}

add_action("admin_menu", "create_component_menu_add");
function create_component_menu_add()
{
	add_submenu_page("edit.php?post_type=product", "Add Component", "Add Component", 0, "component_add", "component_add_callback");
}
function component_add_callback()
{
?>
<style type="text/css">
	input[type='file']
	{
  	color: transparent;
	}
</style>
<div class="wrap">
	<div class="content">
		<h1>Add Components</h1>
		<?php
			session_start();
			if(isset($_POST['save']))
			{
				$component_name = $_POST['component_name'];
				$description = $_POST['description'];
				$component_type = $_POST['component_type'];
				if($_POST['group_id'])
				{
					$group_id = $_POST['group_id'];
				}
				else
				{
					$group_id = "";
				}
				if($_POST['config_id'])
				{
					$config_id  = $_POST['config_id'];
				}
				else
				{
					$config_id  = "";
				}

				if($_POST['field_type'] != 1)
				{
					$field_type = "0";
				}
				else
				{
					$field_type = $_POST['field_type'];
				}
				$part_number = $_POST['part_number'];
				$price = $_POST['price'];
				$weight = $_POST['weight'];
				$status = $_POST['status'];

				//Canvas Images
				if(isset($_FILES['canvas_files']) && ($_FILES['canvas_files']['size'][0] > 0))
				{
					//if file is passed
					if ( ! function_exists( 'wp_handle_upload' ) ) {
			      require_once( ABSPATH . 'wp-admin/includes/file.php' );
			    }

          $upload_overrides = array( 'test_form' => false );
          add_filter("upload_dir","canvas_imgs_upload_dir");

          $files = $_FILES['canvas_files'];
          $removed_imgs = explode(",",$_POST['removed_canvas_images']);
          $no =0;
			    foreach ($files['name'] as $key => $value)
			    {
			     	$file = array(
		          'name'     => $files['name'][$key],
		          'type'     => $files['type'][$key],
		          'tmp_name' => $files['tmp_name'][$key],
		          'error'    => $files['error'][$key],
		          'size'     => $files['size'][$key]
		        );

		        if(!in_array($file['name'], $removed_imgs) && $file['type'] == "image/png")
		        {
		        	$upload = wp_handle_upload($file,$upload_overrides);
		        	$img_name = substr($upload['file'], strrpos($upload['file'], "/") + 1);
		        	if($component_type == "boot")
			    		{
			    			$partno = $_POST['partno_'.$no];
			    		}
			    		else
			    		{
			    			$partno = "";
			    		}
		       		$canvas_img_arr[] =  array(
		       				'img' => $img_name,
		       				'partno' => $partno
		       			);
		     			$no++;
		        }
			    } // foreach closed
			    $canvas_imgs = json_encode($canvas_img_arr);
        }
        else
        {
        	// No file was passed
          $canvas_imgs =  "";
        }
        //Menu Images
        if(isset($_FILES['menu_files']) && ($_FILES['menu_files']['size'][0] > 0))
				{
					//if file is passed
					if ( ! function_exists( 'wp_handle_upload' ) ) {
			      require_once( ABSPATH . 'wp-admin/includes/file.php' );
			    }

          $upload_overrides = array( 'test_form' => false );
          add_filter("upload_dir","menu_imgs_upload_dir");

          $menu_files = $_FILES['menu_files'];
          $removed_menu_images = explode(",",$_POST['removed_menu_images']);

			    foreach ($menu_files['name'] as $m_key => $value)
			    {
			     	$m_file = array(
		          'name'     => $menu_files['name'][$m_key],
		          'type'     => $menu_files['type'][$m_key],
		          'tmp_name' => $menu_files['tmp_name'][$m_key],
		          'error'    => $menu_files['error'][$m_key],
		          'size'     => $menu_files['size'][$m_key]
		        );

		        if(!in_array($m_file['name'], $removed_menu_images)  && $m_file['type'] == "image/png")
		        {
		        	$upload = wp_handle_upload($m_file,$upload_overrides);
		        	$m_img_name = substr($upload['file'], strrpos($upload['file'], "/") + 1);
		        	$m_item[] = $m_img_name;
		        }
			    } // foreach closed
			   	$menu_imgs = json_encode($m_item);
        }
        else
        {
        	// No file was passed
          $menu_imgs =  "";
        }
        global $wpdb;
				$get_component = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."components WHERE name = '".$component_name."'", ARRAY_A);

				if(count($get_component) == 0)
				{
					$insert = $wpdb->insert($wpdb->prefix . 'components', array(
							'name' => $component_name,
							'description' => $description,
							'component_type' => $component_type,
							'group_id' => json_encode($group_id),
							'configuration_id' => json_encode($config_id),
							'field_type' => $field_type,
							'part_number' => $part_number,
							'price' => $price,
							'weight' => $weight,
							'canvas_image' => $canvas_imgs,
							'menu_image' => $menu_imgs,
							'status' => $status
							));
					if($insert)
					{
						$_SESSION['msg'] = "success";
						header("Location:".admin_url("edit.php?post_type=product&page=components"));
					}
					else
					{
						echo '<div class="error fade eMsg"><p><strong>Error! Please Try Again.</strong></p></div>';
					}
				}
				else
				{
					echo '<div class="error fade eMsg"><p><strong>Component with same name already exists.</strong></p></div>';
				}

			}
		?>
		<form name="add_component" method="POST" action="" id="add_component" enctype="multipart/form-data">
			<div id="col-full" class="main-div Add-Configuration addPro-comp">
				<div class="main-secondary form-wrap">
					<div class="form-field">
						<label>Name: </label>
						<input type="text" name="component_name" required="required">
					</div>
					<div class="form-field">
						<label>Description</label>
						<textarea rows="5" cols="15" name="description"></textarea>
					</div>
					<div class="form-field">
						<label>Component Type</label>
						<select name="component_type" id="component_type" required="required">
							<option value=""> -- Select --</option>
							<option value="none">None</option>
							<option value="wire">Wire</option>
							<option value="fanout">Fanout</option>
							<option value="connector">Connector</option>
							<option value="boot">Boot</option>
						</select>
					</div>
					<div class="form-field">
						<label>Group</label>
						  <?php
						   $group_terms = get_terms('group', array(
							'hide_empty' => false
						   ));
						   if (!empty($group_terms))
						   {
						  ?>
						   <ul class="optionList">
							<?php
							 foreach($group_terms as $g_term){

							  $g_term_id = $g_term->term_id;
							  $g_term_meta = get_option( "taxonomy_term_$g_term_id" );
							  if($g_term_meta['presenter_id'] == "configurable")
							  {
							?>
							  <li><input type="checkbox" name="group_id[]" value="<?php echo $g_term->term_id; ?>"> <?php echo $g_term->name; ?></li>
							<?php } }  ?>
						   </ul>
						  <?php } ?>
					</div>
					<div class="form-field">
						<label>Configuration</label>
						  <?php
						   $config_terms = get_terms('configuration', array(
							'hide_empty' => false
						   ));
						   if (!empty($config_terms))
						   {
						  ?>
						   <ul class="optionList">
							<?php
							 foreach ($config_terms as $c_term)
							 {
							  $c_term_id = $c_term->term_id;
							?>
							<li><input type="checkbox" name="config_id[]" value="<?php echo $c_term->term_id; ?>"> <?php echo $c_term->name; ?></li>
							<?php } ?>
						   </ul>
						  <?php } ?>
					</div>
					<div class="form-field">
						<label>Field Type</label>
						<input type="checkbox" name="field_type" value="1"> Changeable
					</div>
					<div class="form-field">
						<label>Part Number</label>
						<input type="text" name="part_number" required="required">
					</div>
					<div id="price_section" class="form-field">
						<label>Price</label>
						<input type="text" name="price" class="price">
					</div>
					<div id="weight_section" class="form-field">
						<label>Weight</label>
						<input type="text" name="weight" class="weight">
					</div>
					<div id="canvas_img_section" class="form-field">
						<label>Canvas Image</label>
            <input type="file" name="canvas_files[]" multiple="multiple" id="canvas_files" />
            <span class="note">Press Ctrl to select multiple images at a time.</span>
            <div class="err_msg" style="display: none;">Only Png Images are allowed.</div>
						<div class="images_container"></div>
						<input type="hidden" name="added_canvas_imgs" value="" class="added_canvas_imgs">
            <input type="hidden" name="removed_canvas_images" value="" class="removed_canvas_images">
					</div>
					<div id="menu_img_section" class="form-field">
						<label>Menu Image</label>
						<input type="file" name="menu_files[]" multiple="multiple" id="menu_files" />
						<span class="note">Press Ctrl to select multiple images at a time.</span>
						<div class="m_err_msg" style="display: none;">Only Png Images are allowed.</div>
						<div class="menu_images_container"></div>
						<input type="hidden" name="added_menu_imgs" value="" class="added_menu_imgs">
            <input type="hidden" name="removed_menu_images" value="" class="removed_menu_images">
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
<script type="text/javascript">
$(document).ready(function(){
jQuery.validator.addMethod("alphanumeric", function(value, element) {
    return this.optional(element) ||/^[a-zA-Z0-9]+$/i.test(value);
}, "Letters and numbers only please");


	$("#add_component").validate({
		rules:{
			component_name: "required",
			component_type: "required",
			part_number: {
          required: true,
          alphanumeric: true
      },
			price: {
      	number: true,
      	minStrict: 0,
    	}
		},
		messages : {
			component_name : "This is a required field",
			component_type : "This is a required field",
			part_number : {
				required : "This is a required field",
				alphanumeric : "Letters and numbers only please"
			},
			price : "This is an invalid entry"
		}
	});
});


function canvasreadURL(input)
{
	$(".images_container").empty();
	var fileList = input.files;

	var anyWindow = window.URL || window.webkitURL;
	var err = 0;
	for(var i = 0; i < fileList.length; i++)
	{
	  //get a blob to play with
	  var img_name = fileList[i].name;
	  var component_type = $("#component_type").val();

	  // var split_imgname = img_name.split(".");
	  var lastIndexOfDot = img_name.lastIndexOf(".");
	  var split_imgname = [img_name.slice(0, lastIndexOfDot), img_name.slice(lastIndexOfDot+1, img_name.length)];
	  // var name_part = img_name.lastIndexOf(".");

	  var img_disp_name = split_imgname[0].replace(/_/g,' ');

	  var objectUrl = anyWindow.createObjectURL(fileList[i]);
	  if(split_imgname[1] != "png")
	  {
	  	err = 1;
	  }

	  else
	  {
	  	if(component_type == "boot")
		  {
		  	$(".images_container").append('<div class="cnvs-imgDiv" id="image_'+i+'"><div class="img-outerDiv"><img src='+objectUrl+'><i class="fa fa-times" aria-hidden="true" onClick="return remove_canvas_image(this)" data-type="canvas" data-no='+i+' data-imgname='+fileList[i].name+'></i><input type="text" name="partno_'+i+'" id="partno_'+i+'" pattern="[a-zA-Z][a-zA-Z0-9\s]*" required="required" class="partno" placeholder="Part Number"></div><span class="display_name">'+img_disp_name+'</span></div>');
		 		$('#partno_'+i).rules('add', {
        	required: true,
        	alphanumeric: true
    		});
		  }
		  else
		  {
		  	$(".images_container").append('<div class="cnvs-imgDiv" id="image_'+i+'"><div class="img-outerDiv"><img src='+objectUrl+'><i class="fa fa-times" aria-hidden="true" onClick="return remove_canvas_image(this)" data-type="canvas" data-no='+i+' data-imgname='+fileList[i].name+'></i></div><span class="display_name">'+img_disp_name+'</span></div>');
		  }
	  }

	  // get rid of the blob
	  window.URL.revokeObjectURL(fileList[i]);
	}
	if(err == 1)
	{
		$(".err_msg").show();
	}
	if(err == 0)
	{
		$(".err_msg").hide();
	}
}

$("#canvas_files").change(function() {
  canvasreadURL(this);
});


function menureadURL(input)
{
	$(".menu_images_container").empty();
	var fileList = input.files;
	var anyWindow = window.URL || window.webkitURL;
	var err = 0;
	for(var i = 0; i < fileList.length; i++)
	{
	  //get a blob to play with
	  var img_name = fileList[i].name;
	  // var split_imgname = img_name.split(".");
	  var lastIndexOfDot = img_name.lastIndexOf(".");
	  var split_imgname = [img_name.slice(0, lastIndexOfDot), img_name.slice(lastIndexOfDot+1, img_name.length)];
  	var img_disp_name = split_imgname[0].replace(/_/g,' ');

	  var objectUrl = anyWindow.createObjectURL(fileList[i]);
	  if(split_imgname[1] != "png")
	  {
	  	err = 1;
	  }
	  else
	  {
	  	$(".menu_images_container").append('<div class="cnvs-imgDiv" id="menuimage_'+i+'"><div class="img-outerDiv"><img src='+objectUrl+'><i class="fa fa-times" aria-hidden="true" onClick="return remove_menu_image(this)" data-type="canvas" data-no='+i+' data-imgname='+fileList[i].name+'></i></div><span class="display_name">'+img_disp_name+'</span></div>');
	  }
	  // get rid of the blob
	  window.URL.revokeObjectURL(fileList[i]);
	}
	if(err == 1)
	{
		$(".m_err_msg").show();
	}
	if(err == 0)
	{
		$(".m_err_msg").hide();
	}
}


$("#menu_files").change(function() {
  menureadURL(this);
});


$('#component_type').on('change', function() {
  var c_type = this.value;
  var price = $(".price").val();
  var canvas_images = $(".added_canvas_imgs").val();

  if(price.length > 0 || canvas_images.length > 0)
  {
  	if (confirm("If you change this value, all its related field data will be removed.") == true)
  	{
    	$(".price").val('');
    	$(".added_canvas_imgs").val('');
	  }
	  else
	  {
	    $("#component_type").val(c_type);
	  }
  }


  if(c_type == "wire" || c_type == "fanout")
  {
  	$("#price_section").hide();
  	$("#canvas_img_section").hide();
  }
  else
  {
  	$("#price_section").show();
  	$("#canvas_img_section").show();
  }
  $(".cnvs-imgDiv").remove();
  $(".added_canvas_imgs").val("");
  $(".partno").val("");
});

function remove_canvas_image(element)
{
	var img_name = $(element).data("imgname");
	var n = $(element).data("no");
	var removed_imgs_val = $(".removed_canvas_images").val();
	if(removed_imgs_val == "" || removed_imgs_val == null)
	{
		var remove_imgs = img_name;
	}
	else
	{
		var remove_imgs = removed_imgs_val+","+img_name;
	}
	$(".removed_canvas_images").val(remove_imgs);
	jQuery("#image_"+n).remove();
}

function remove_menu_image(element)
{
	var img_name = $(element).data("imgname");
	var n = $(element).data("no");
	var removed_imgs_val = $(".removed_menu_images").val();
	if(removed_imgs_val == "" || removed_imgs_val == null)
	{
		var remove_imgs = img_name;
	}
	else
	{
		var remove_imgs = removed_imgs_val+","+img_name;
	}
	$(".removed_menu_images").val(remove_imgs);
	jQuery("#menuimage_"+n).remove();
}

</script>
<?php
} //component_add_callback ends

function remove_images_from_dir($removed_menu_images_arr, $type='canvas') {
		// Support string as well as array
		if (!is_array($removed_menu_images_arr)) {
				$removed_menu_images_arr = array($removed_menu_images_arr);
		}

		$uploads = wp_upload_dir();
		$upload_path = $uploads['basedir'] . "/". "customcable". "/". $type . "/";

		foreach ($removed_menu_images_arr as $value) {
				try {
					unlink($upload_path . $value);
				} catch(\Exception $e) {
					 // Nothing to worry about!!
				}
		}
}

add_action("admin_menu", "create_component_menu_update");
function create_component_menu_update()
{
	add_submenu_page("edit.php?post_type=product", "Components Update", "Components Update", 0, "components_update", "components_update_callback");
}
function components_update_callback()
{
	global $wpdb;
	session_start();
	$component_id = $_GET['component_id'];
	$get_component = $wpdb->get_row( "SELECT * FROM ".$wpdb->prefix."components WHERE component_id = ".$component_id."", ARRAY_A);
	$part_no = array_keys(json_decode($get_component['canvas_image'],true));
	$c_images = array_values(json_decode($get_component['canvas_image'],true));

	if(isset($_POST['update']))
	{
		$component_name = $_POST['component_name'];
		$description = $_POST['description'];
		$component_type = $_POST['component_type'];
		if($_POST['group_id'])
		{
			$group_id = $_POST['group_id'];
		}
		else
		{
			$group_id = "";
		}
		if($_POST['config_id'])
		{
			$config_id  = $_POST['config_id'];
		}
		else
		{
			$config_id  = "";
		}

		if($_POST['field_type'] != 1)
		{
			$field_type = "0";
		}
		else
		{
			$field_type = $_POST['field_type'];
		}
		$part_number = $_POST['part_number'];
		$price = $_POST['price'];
		$weight = $_POST['weight'];
		$status = $_POST['status'];

		$removed_canvas_imgs = explode(",",$_POST['removed_canvas_images']);
		remove_images_from_dir($removed_canvas_imgs, 'canvas');

		//Canvas Images
		if(isset($_FILES['canvas_files']) && ($_FILES['canvas_files']['size'][0] > 0))
		{
			//if file is passed
			if ( ! function_exists( 'wp_handle_upload' ) ) {
	      require_once( ABSPATH . 'wp-admin/includes/file.php' );
	    }

      $upload_overrides = array( 'test_form' => false );
      add_filter("upload_dir","canvas_imgs_upload_dir");

      $added_canv_imgs = json_decode($get_component['canvas_image'],true);
      $no = 0;
	    foreach ($added_canv_imgs as $c_value)
	    {
	    	if(!in_array($c_value['img'], $removed_canvas_imgs))
	    	{
	    		if($component_type == "boot")
	    		{
	    			$partno = $_POST['partno_'.$no];
	    		}
	    		else
	    		{
	    			$partno = "";
	    		}
	    		$canvas_img_arr[] = array(
	    				'img' => $c_value['img'],
	    				'partno' => $partno
	    			);
	    	}
	    	$no++;
	    }

      $files = $_FILES['canvas_files'];
	    foreach ($files['name'] as $key => $value)
	    {
	     	$file = array(
          'name'     => $files['name'][$key],
          'type'     => $files['type'][$key],
          'tmp_name' => $files['tmp_name'][$key],
          'error'    => $files['error'][$key],
          'size'     => $files['size'][$key]
        );

        if(!in_array($file['name'], $removed_canvas_imgs)  && $file['type'] == "image/png")
        {
        	$upload = wp_handle_upload($file,$upload_overrides);
					$img_name = substr($upload['file'], strrpos($upload['file'], "/") + 1);
					if($component_type == "boot")
	    		{
	    			$partno = $_POST['partno_'.$no];
	    		}
	    		else
	    		{
	    			$partno = "";
	    		}
       		$canvas_img_arr[] =  array(
       				'img' => $img_name,
       				'partno' => $partno
       			);
        }
        $no++;
	    } // foreach closed

	    $canvas_imgs = json_encode($canvas_img_arr);
    }
    else
    {
    	// No file was passed
    	if($get_component['canvas_image'] != "" || $get_component['canvas_image'] != null)
    	{
	    	$added_canvas_imgs = json_decode($get_component['canvas_image'],true);
	    	$no = 0;
	      foreach($added_canvas_imgs as $added_c_imgs)
	     	{
	     		if(!in_array($added_c_imgs['img'],$removed_canvas_imgs))
	     		{
	     			if($component_type == "boot")
			     	{
			     		$partno = $_POST['partno_'.$no];
			     	}
			     	else
			     	{
			     		$partno = "";
			     	}
			     	$canvas_arr[] =  array(
			     			'img' => $added_c_imgs['img'],
			     			'partno' => $partno
			     		);
	     		}

	     		$no++;
	     	}
	     	$canvas_imgs =  json_encode($canvas_arr);
     	}
     	else
     	{
     		$canvas_imgs = "";
     	}
    }

    $removed_menu_images = explode(",",$_POST['removed_menu_images']);
    remove_images_from_dir($removed_menu_images, 'menu');
    //Menu Images

    if(isset($_FILES['menu_files']) && ($_FILES['menu_files']['size'][0] > 0))
		{
			//if file is passed
			if ( ! function_exists( 'wp_handle_upload' ) ) {
	      require_once( ABSPATH . 'wp-admin/includes/file.php' );
	    }

      $upload_overrides = array( 'test_form' => false );
      add_filter("upload_dir","menu_imgs_upload_dir");

      $added_menu_imgs = json_decode($get_component['menu_image'],true);
	    foreach ($added_menu_imgs as $m_value)
	    {
	    	if(!in_array($m_value, $removed_menu_images)){
	    		$m_item[] = $m_value;
	    	}
	    }

      $menu_files = $_FILES['menu_files'];
	    foreach ($menu_files['name'] as $m_key => $value)
	    {
	     	$m_file = array(
          'name'     => $menu_files['name'][$m_key],
          'type'     => $menu_files['type'][$m_key],
          'tmp_name' => $menu_files['tmp_name'][$m_key],
          'error'    => $menu_files['error'][$m_key],
          'size'     => $menu_files['size'][$m_key]
        );

        if(!in_array($m_file['name'], $removed_menu_images)  && $m_file['type'] == "image/png")
        {
        	$upload = wp_handle_upload($m_file,$upload_overrides);
        	$m_img_name = substr($upload['file'], strrpos($upload['file'], "/") + 1);
					$m_item[] = $m_img_name;
        }

	    } // foreach closed

	   	$menu_imgs = json_encode($m_item);

    }
    else
    {
    	// No file was passed
    	if($get_component['menu_image'] != "" || $get_component['menu_image'] != null)
    	{
	    	$added_menu_images = json_decode($get_component['menu_image'],true);
	    	foreach($added_menu_images as $added_m_imgs)
	     	{
	     		if(!in_array($added_m_imgs,$removed_menu_images))
	     		{
	     			$m_item[] = $added_m_imgs;
	     		}
	     	}
		   	$menu_imgs = json_encode($m_item);
	   	}
	   	else
	   	{
	   		$menu_imgs = "";
	   	}
    }
    $get_components = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."components WHERE name = '".$component_name."' && component_id != ".$component_id."", ARRAY_A);
    if(count($get_components) == 0)
    {
			$update = $wpdb->update( $wpdb->prefix . 'components', array(
					'name' => $component_name,
					'description' => $description,
					'component_type' => $component_type,
					'group_id' => json_encode($group_id),
					'configuration_id' => json_encode($config_id),
					'field_type' => $field_type,
					'part_number' => $part_number,
					'price' => $price,
					'weight' => $weight,
					'canvas_image' => $canvas_imgs,
					'menu_image' => $menu_imgs,
					'status' => $status
					),
					array(
							'component_id' => $component_id
						)
				);

			if($update === 1)
			{
				$_SESSION['msg'] = "updated";
				header("Location:".admin_url("edit.php?post_type=product&page=components"));
			}
			else if($update === 0)
			{
				$_SESSION['msg'] = "updated";
				header("Location:".admin_url("edit.php?post_type=product&page=components"));
			}
			else if($update === false)
			{
				echo '<div class="error fade eMsg"><p><strong>Error! Please Try Again.</strong></p></div>';
			}
		}
		else
		{
			echo '<div class="error fade eMsg"><p><strong>Component already exists.</strong></p></div>';
		}
	}
	if($_GET['msg'] == "updated")
	{
		echo '<div class="updated notice sMsg"><p><strong>Component Updated Successfully.</strong></p></div>';
	}
?>
	<style type="text/css">
		input[type='file']
		{
			color: transparent;
		}
	</style>
<div class="wrap">
	<div class="content">
		<h1>Update Components</h1>
		<form enctype="multipart/form-data" name="update_component" method="POST" id="update_component">
			<div id="col-full" class="main-div Add-Configuration addPro-comp">
				<div class="main-secondary form-wrap">
					<div class="form-field">
						<label>Name: </label>
						<input type="text" name="component_name" value="<?php echo $get_component['name']; ?>">
					</div>
					<div class="form-field">
						<label>Description</label>
						<textarea rows="5" cols="15" name="description"><?php echo $get_component['description']; ?></textarea>
					</div>
					<div class="form-field">
						<label>Component Type</label>
						<select name="component_type" id="component_type">
							<option value=""> -- Select --</option>
							<option value="none" <?php if($get_component['component_type'] == "none"){ echo "selected"; } ?>>None</option>
							<option value="wire" <?php if($get_component['component_type'] == "wire"){ echo "selected"; } ?>>Wire</option>
							<option value="fanout" <?php if($get_component['component_type'] == "fanout"){ echo "selected"; } ?>>Fanout</option>
							<option value="connector" <?php if($get_component['component_type'] == "connector"){ echo "selected"; } ?>>Connector</option>
							<option value="boot" <?php if($get_component['component_type'] == "boot"){ echo "selected"; } ?>>Boot</option>
						</select>
					</div>
					<div class="form-field">
						<label>Group</label>
						  <?php
						  $group_arr = json_decode($get_component['group_id']);
						  $group_terms = get_terms('group', array(
							'hide_empty' => false
						   ));
						   if (!empty($group_terms))
						   {
						  ?>
						  <ul class="optionList">
							<?php
							 foreach($group_terms as $g_term)
							 {
							  $g_term_id = $g_term->term_id;
							  $g_term_meta = get_option( "taxonomy_term_$g_term_id" );
							  if($g_term_meta['presenter_id'] == "configurable")
							  {
							?>
							  <li><input type="checkbox" name="group_id[]" value="<?php echo $g_term->term_id; ?>" <?php if(in_array($g_term->term_id, $group_arr)){ echo "checked"; } ?> > <?php echo $g_term->name; ?></li>
							<?php } }  ?>
						   </ul>
						  <?php } ?>
					</div>
					<div class="form-field">
						<label>Configuration</label>
						  <?php
						  $config_arr = json_decode($get_component['configuration_id']);
						   $config_terms = get_terms('configuration', array(
							'hide_empty' => false
						   ));
						   if (!empty($config_terms))
						   {
						  ?>
						   <ul class="optionList">
							<?php
							 foreach ($config_terms as $c_term)
							 {
							  $c_term_id = $c_term->term_id;
							?>
							<li><input type="checkbox" name="config_id[]" value="<?php echo $c_term->term_id; ?>" <?php if(in_array($c_term->term_id,$config_arr)){ echo "checked"; } ?> > <?php echo $c_term->name; ?></li>
							<?php } ?>
						   </ul>
						  <?php } ?>
					</div>
					<div class="form-field">
						<label>Field Type</label>
						<input type="checkbox" name="field_type" value="1" <?php if($get_component['field_type'] == "1"){ echo "checked"; } ?>> Changeable
					</div>
					<div class="form-field">
						<label>Part Number</label>
						<input type="text" name="part_number" value="<?php echo $get_component['part_number']; ?>">
					</div>

					<div id="price_section" class="form-field">
						<label>Price</label>
						<input type="text" name="price" class="price" value="<?php echo $get_component['price']; ?>">
					</div>
					<div id="weight_section" class="form-field">
						<label>Weight</label>
						<input type="text" name="weight" class="weight" value="<?php echo $get_component['weight']; ?>">
					</div>
					<div id="canvas_img_section" class="form-field">
						<label>Canvas Image</label>
						<input type="file" name="canvas_files[]" multiple="multiple" id="canvas_files" />
						<span class="note">Press Ctrl to select multiple images at a time.</span>
						<div class="err_msg" style="display: none;">Only Png Images are allowed.</div>
            <div class="images_container">
	            <?php
	            	$canvas_images =  json_decode($get_component['canvas_image'],true);

	            	$upload_dir = wp_upload_dir();
	            	$added_img_count = count($canvas_images);
	            	$img_count = 0;
	            	foreach($canvas_images as $c_img)
	            	{
	            		$image_arr[] = $c_img['img'];
	            		$c_file = explode(".",$c_img['img']);
	            		$filename = str_replace("_"," ",$c_file[0]);
	            ?>
	            	<div class="cnvs-imgDiv" id="image_<?php echo $img_count; ?>">
	            		<div class="img-outerDiv">
		          			<img src="<?php echo $upload_dir['url']."/customcable/canvas/".$c_img['img'] ?>" >
		          			<i class="fa fa-times" aria-hidden="true" onClick="return remove_canvas_image(this)" data-type="canvas" data-no="<?php echo $img_count ?>" data-imgname="<?php echo $c_img['img']; ?>"></i>
		            		<?php	if($get_component['component_type'] == "boot"){ ?>
		            			<input type="text" name="<?php echo 'partno_'.$img_count; ?>" value="<?php echo $c_img['partno']; ?>"> <br>
		            		<?php } ?>
	            		</div>
	            		<span class="display_name"><?php echo $filename; ?></span>
	            	</div>
	            <?php
	            		$img_count++;
	            	}
	            	$added_canvas_images = implode(",",$image_arr);

	            ?>
            </div>
            <input type="hidden" name="added_canvas_imgs" value="<?php echo $added_canvas_images; ?>" class="added_canvas_imgs">
            <input type="hidden" name="removed_canvas_images" value="" class="removed_canvas_images">
					</div>
					<div id="menu_img_section" class="form-field">
						<label>Menu Image</label>
						<input type="file" name="menu_files[]" multiple="multiple" id="menu_files" />
						<span class="note">Press Ctrl to select multiple images at a time.</span>
						<div class="m_err_msg" style="display: none;">Only Png Images are allowed.</div>
						<div class="menu_images_container">
							<?php
	            	$menu_images =  json_decode($get_component['menu_image'],true);
	            	$upload_dir = wp_upload_dir();
	            	$m_added_img_count = count($menu_images);
	            	$m_img_count = 0;
	            	foreach($menu_images as $m_img)
	            	{
	            		$m_image_arr[] = $m_img;
	            		$m_file = explode(".",$m_img);
	            		$m_filename = str_replace("_"," ",$m_file[0]);
	            ?>
	            	<div class="cnvs-imgDiv" id="menuimage_<?php echo $m_img_count; ?>">
	            		<div class="img-outerDiv">
		          			<img src="<?php echo $upload_dir['url']."/customcable/menu/".$m_img ?>" >
		          			<i class="fa fa-times" aria-hidden="true" onClick="return remove_menu_image(this)" data-type="menu" data-no="<?php echo $m_img_count ?>" data-imgname="<?php echo $m_img; ?>"></i>
	            		</div>
	            		<span class="display_name"><?php echo $m_filename; ?></span>
	            	</div>
	            <?php
	            		$m_img_count++;
	            	}
	            	$added_menu_images = implode(",",$m_image_arr);
	            ?>
						</div>
						<input type="hidden" name="added_menu_imgs" value="<?php echo $added_menu_images; ?>" class="added_menu_imgs">
            <input type="hidden" name="removed_menu_images" value="" class="removed_menu_images">
					</div>
					<div class="form-field">
						<label>Status</label>
						<input type="radio" name="status" value="1" <?php if($get_component['status'] == "1"){ echo "checked"; } ?>> Active
						<input type="radio" name="status" value="0" <?php if($get_component['status'] == "0"){ echo "checked"; } ?> > Inactive
					</div>
					<input class="button button-primary" type="submit" name="update" value="Update">
				</div>
			</div>
		</form>
	</div>
</div>
<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	var component_type = "<?php echo $get_component['component_type']; ?>";
	if(component_type == "wire" || component_type =="fanout")
	{
		$("#price_section").hide();
		$("#canvas_img_section").hide();
	}
	if(component_type == "fanout" || component_type == "fanout")
	{
		$("#price_section").hide();
	}
	jQuery.validator.addMethod("alphanumeric", function(value, element) {
    return this.optional(element) ||/^[a-zA-Z0-9]+$/i.test(value);
}, "Letters and numbers only please");

	jQuery.validator.addMethod('minStrict', function (value, el, param) {
	    return value > param;
	}, "Please enter a valid value");

	$("#update_component").validate({
		rules:{
			component_name: "required",
			component_type: "required",
			part_number: {
				required: true,
				alphanumeric : true
			},
			price: {
      	number: true,
      	minStrict: 0,
    	}
		},
		messages : {
			component_name : "This is a required field",
			component_type : "This is a required field",
			part_number : {
				required : "This is a required field",
				alphanumeric : "Only numbers and alphabets are allowed"
			},
			price : "This is an invalid entry"
		}
	});
});

$('#component_type').on('change', function() {
  var c_type = this.value;
  var price = $(".price").val();
  var canvas_images = $(".added_canvas_imgs").val();

  if(price.length > 0 || canvas_images.length > 0)
  {
  	if (confirm("If you change this value, all its related field data will be removed.") == true)
  	{
    	$(".price").val('');
    	$("#canvas_files").val('');
    	$(".err_msg").hide();
    	$(".images_container").empty();
	  }
	  else
	  {
	    $("#component_type").val(c_type);
	  }
  }
  if(c_type == "wire" || c_type == "fanout")
  {
  	$("#price_section").hide();
  	$("#canvas_img_section").hide();
  }
  else
  {
  	$("#price_section").show();
  	$("#canvas_img_section").show();
  }
});

function canvasreadURL(input)
{
	$(".newupdated").remove();
	var count = "<?php echo $img_count ?>";
	var fileList = input.files;
	var anyWindow = window.URL || window.webkitURL;
	var err = 0;
	for(var i = 0; i < fileList.length; i++)
	{
	  //get a blob to play with
	  var img_name = fileList[i].name;
	  var component_type = $("#component_type").val();

	  // var split_imgname = img_name.split(".");
	  var lastIndexOfDot = img_name.lastIndexOf(".");
	  var split_imgname = [img_name.slice(0, lastIndexOfDot), img_name.slice(lastIndexOfDot+1, img_name.length)];

	  var img_disp_name = split_imgname[0].replace(/_/g,' ');

	  var objectUrl = anyWindow.createObjectURL(fileList[i]);

	  if(split_imgname[1] != "png")
	  {
	  	err = 1;
	  }
	  else
	  {
	  	if(component_type == "boot")
		  {
		  	$(".images_container").append('<div class="cnvs-imgDiv newupdated" id="image_'+count+'"><div class="img-outerDiv"><img src='+objectUrl+'><i class="fa fa-times" aria-hidden="true" onClick="return remove_canvas_image(this)" data-type="canvas" data-no='+count+' data-imgname='+fileList[i].name+'></i><input type="text" name="partno_'+count+'" class="partno" placeholder="Part Number"></div><span class="display_name">'+img_disp_name+'</span></div>');
		  	$('#partno_'+count).rules('add', {
        	required: true,
        	alphanumeric: true
    		});
		  }
		  else
		  {
		  	$(".images_container").append('<div class="cnvs-imgDiv newupdated" id="image_'+count+'"><div class="img-outerDiv"><img src='+objectUrl+'><i class="fa fa-times" aria-hidden="true" onClick="return remove_canvas_image(this)" data-type="canvas" data-no='+count+' data-imgname='+fileList[i].name+'></i></div><span class="display_name">'+img_disp_name+'</span></div>');
		  }
	  }
	  // get rid of the blob
	  window.URL.revokeObjectURL(fileList[i]);
	  count++;
	}
	if(err == 1)
	{
		$(".err_msg").show();
	}
	if(err == 0)
	{
		$(".err_msg").hide();
	}
}

$("#canvas_files").change(function() {
  canvasreadURL(this);
});


function menureadURL(input)
{
	$(".newupdated").remove();
	var count ="<?php echo $m_img_count ?>";
	var fileList = input.files;
	var anyWindow = window.URL || window.webkitURL;
	var err = 0;
	for(var i = 0; i < fileList.length; i++){
	  //get a blob to play with
	  var img_name = fileList[i].name;

	  // var split_imgname = img_name.split(".");
	  var lastIndexOfDot = img_name.lastIndexOf(".");
	  var split_imgname = [img_name.slice(0, lastIndexOfDot), img_name.slice(lastIndexOfDot+1, img_name.length)];

	  var img_disp_name = split_imgname[0].replace(/_/g,' ');
	  var objectUrl = anyWindow.createObjectURL(fileList[i]);

	  if(split_imgname[1] != "png")
	  {
	  	err = 1;
	  }
	  else
	  {
	  	$(".menu_images_container").append('<div class="cnvs-imgDiv newmenuupdated" id="menuimage_'+count+'"><div class="img-outerDiv"><img src='+objectUrl+'><i class="fa fa-times" aria-hidden="true" onClick="return remove_menu_image(this)" data-type="canvas" data-no='+count+' data-imgname='+fileList[i].name+'></i></div><span class="display_name">'+img_disp_name+'</span></div>');
	  }

	  // get rid of the blob
	  window.URL.revokeObjectURL(fileList[i]);
	  count++;
	}
	if(err == 1)
	{
		$(".m_err_msg").show();
	}
	if(err == 0)
	{
		$(".m_err_msg").hide();
	}
}


$("#menu_files").change(function() {
  menureadURL(this);
});

function remove_canvas_image(element)
{
	var img_name = $(element).data("imgname");
	var n = $(element).data("no");
	var removed_imgs_val = $(".removed_canvas_images").val();
	if(removed_imgs_val == "" || removed_imgs_val == null)
	{
		var remove_imgs = img_name;
	}
	else
	{
		var remove_imgs = removed_imgs_val+","+img_name;
	}
	$(".removed_canvas_images").val(remove_imgs);
	jQuery("#image_"+n).remove();
}

function remove_menu_image(element)
{
	var img_name = $(element).data("imgname");
	var n = $(element).data("no");
	var removed_imgs_val = $(".removed_menu_images").val();
	if(removed_imgs_val == "" || removed_imgs_val == null)
	{
		var remove_imgs = img_name;
	}
	else
	{
		var remove_imgs = removed_imgs_val+","+img_name;
	}
	$(".removed_menu_images").val(remove_imgs);
	jQuery("#menuimage_"+n).remove();
}
</script>
<?php
} //component update callback ends
?>
