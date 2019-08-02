<?php 
/* Shortcode for frontend [cable_configuration] Config term ID can be passed in shortcode or as Query String
 *  [cable_configuration config="config_id"]
 *  Config Url.com?config=config_ID
 */
function cable_configuration_func($atts) {
	
	
    if (!empty($atts['config'])) {
        $config_id = $atts['config'];
    } elseif (!empty($_GET['config'])) {
        $config_id = $_GET['config'];
    }

    if (empty($config_id)) {
        return false;
    }
    if (!is_user_logged_in()) { ?>
        <p><a href="<?php
            echo site_url(). '/?page_id=1426'
            ?>">Login</a> for Preferred Pricing</p>
            <?php
            return false;
        }
        	
        ?>
        <div class="row">
	<div class="col-sm-12">
		<?php if(!empty(get_user_meta(wp_get_current_user()->id, 'company_logo', TRUE))){ ?>
	<div class="custom-sol"><span>Custom solution for</span> <img alt="" src="<?php echo get_user_meta(wp_get_current_user()->id, 'company_logo', TRUE);?>" /></div>
	<?php }?>
	</div>
	</div>
  <div class="row">
	<div class="col-sm-3">
    <ol id="steps-customs">
		
		<?php
                $main_args = array(
                    'parent' => $config_id,
                    'hide_empty' => false
                );
                $i = 0 ;
                $main_configurations = get_terms('configuration', $main_args);
                if (!empty($main_configurations)) {
                    foreach ($main_configurations as $key => $configuration) {
                ?>
   <!--apply loop over-->
   
    <li class="parent-element" data-value="<?php echo ++$i ; ?>"  parent_type="<?php echo $configuration->term_id; ?>" group_id="<?php  echo $config_id;?>"><a  href="javascipt:void(0);"><?php echo $configuration->name;?></a>
    <ul class="sub-content">
		<!--internal loop-->
		<?php
			$terms_child = get_terms('configuration', array(
				'hide_empty' => false,
				'parent' => $configuration->term_id
			));
			
			if (!empty($terms_child)) {
				foreach ($terms_child as $child) {
				    $childTerm = (array)$child ;
					/*echo '<pre>' ;
						print_r($childTerm);
					echo '</pre>' ;*/
					$t_id = $child->term_id;
					$term_meta = get_option("taxonomy_term_$t_id");
					/*echo '<pre>' ;
						print_r($term_meta);
					echo '</pre>' ;*/
					$part_image = $term_meta['part_image'] ? $term_meta['part_image'] : '';
					$allfields = array_merge($childTerm,$term_meta);
					/*echo '<pre>' ;
					print_r($allfields);
					echo '</pre>' ;*/
			
		?>
    <li>
    <button type="button" class="question-icon" data-toggle="popover" data-trigger="hover" title="<?php echo $child->name;?>" data-content="<?php echo $child->description;?>">?</button>
    <a class="child-element"  href="javascipt:void(0);" onclick="add_part_image(this);return false;" data-id="<?php echo $t_id;?>">
    <img alt="" src="<?php echo $part_image; ?>" />
    <span class="pro-lbl"><?php echo $child->name;?></span>
    </a>
    </li>
    <?php } }?>
    
    <!--internal loop end here-->
    </ul>
    </li>
    <!--end loop here-->
    <?php } }?>
    </ol>
    </div>
    <div class="col-sm-9 pad0 sm-canvas">
	<div id="loader-image" class="hide" style="margin-top:-85px">
	<img src="<?php echo plugins_url().'/custom_cable_configuration/images/loader.gif"/>
    </div>
   <canvas id="canvas_config" width="900" height="530">
    Sorry. Your browser does not support HTML5 canvas element.
    </canvas></div>
    <div class="col-sm-12 text-right pad0 btm-btns">
	<button id="reset_config" type="button" class="btn custom-btn"><i class="fa fa-refresh" aria-hidden="true"></i> Reset</button>
	<!--<button type="button" class="btn custom-btn add-to-cart disable-btn"><i class="fa fa-shopping-cart" aria-hidden="true"></i> Add to Cart</button>-->
	<?php
	if (isset($_REQUEST['cart-request'])){
		//echo "treasure will be set if the form has been submitted (to TRUE, I believe)";
		//exit();
	}
	?>
	<form class="cart-config" action="<?php echo site_url() . '/cart'?>" method ="post">
		<div class="pro_id"><input type="hidden" name="add-to-cart" value="" /></div>
		<input type="submit" value="Add to cart" class="btn custom-btn add-to-cart disable-btn" disabled />
	</form>

	</div>
	<div class="col-sm-12 pad-right0">
	<div class="search-row">
	<div class="col-sm-4 col-xs-6">
	<form class="form-inline history-frm"> 
	<div class="form-group">
	<label>Order Historyssss</label>
	<input type="email" class="form-control" id="exampleInputEmail2" placeholder="Click here to see and modify Order">
	<button type="submit" class="btn btn-default"><i class="fa fa-angle-right" aria-hidden="true"></i></button>
	</div>	
	</form>
	</div>
	<div class="col-sm-4 col-xs-6 pull-right">
	<form class="form-inline history-frm"> 
	<div class="form-group">
	<label>Fiber Optic Cables</label>
	<input type="text" class="form-control" id="exampleInputEmail2" placeholder="Enter Part # here for quick search">
	<button type="submit" class="btn btn-default"><i class="fa fa-angle-right" aria-hidden="true"></i></button>
	</div>	
	</form>
	</div>
	</div>
	</div>
	</div>	
    <!-- Script for canvas */ -->
    <!--<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    
    <script>
	 var ajaxPath= "<?php echo plugins_url().'/custom_cable_configuration/ajax/';?>";
	
    </script>
    <script>
		$("#steps-customs li a").click(function () {
		//$("#steps-customs li")
       // .not($(this).parent())
       // .removeClass("active");
		//$(this).parent().toggleClass("active");
		});		
        $(function () {
            jQuery('[data-toggle="popover"]').popover()

        })
    </script>
    <script>
        function add_part_image($this) {
            var cat_id = jQuery($this).attr('data-id');
            jQuery.ajax({
                method: "POST",
                url: "<?php
            echo plugins_url().'/custom_cable_configuration/';
            ?>ajax/get_image.php",
                dataType: "json",
                data: {this_id: cat_id},
                success: function(response) {
                    /* Add image to canvas */
                    var canvas = document.getElementById('canvas_config');
                    var context = canvas.getContext('2d');
                    var imageObj = new Image();

                    imageObj.onload = function() {
                        context.drawImage(imageObj, response.coordinate_x, response.coordinate_y);
                    };
                    imageObj.src = response.image;
                }
            });
            return false;
        }
        jQuery(document).ready(function() {
            /* Add logo to canvas */
            var canvas = document.getElementById('canvas_config');
            var context = canvas.getContext('2d');
            var imageObj = new Image();
            var cord_x = canvas.width - 200;
            imageObj.onload = function() {
                context.drawImage(imageObj, cord_x, 10);
            };
            imageObj.src = '<?php $if_added= get_option( 'watermark_logo_url' ); if(!empty($if_added)) { echo $if_added; }else { echo plugins_url().'/custom_cable_configuration/images/logo/logo.png'; } ?>'
                        
        });
    </script>
    <?php
}

add_shortcode('cable_configuration', 'cable_configuration_func');
?>