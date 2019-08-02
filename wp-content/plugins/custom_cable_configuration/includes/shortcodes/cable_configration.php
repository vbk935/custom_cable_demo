<?php
if(isset($_REQUEST['group_id']))
{
	add_filter('wp_title', 'filter_pagetitle', 99,1);
	function filter_pagetitle($title) {
			$group_info = get_term_by('id', $_REQUEST['group_id'], 'group');
			$title = $group_info->name ." | ". get_bloginfo('name');    
			return $title;
	}
}


function cable_configuration_func($atts) {
	if ( is_user_logged_in() ) {
		$user = wp_get_current_user();
		$user_role = $user->roles;
	} else {
		$user_role = "guest_user";
	}
	$group_id = $_REQUEST['group_id'];
	$term_meta = get_option("taxonomy_term_$group_id");
	$restrict_role = $term_meta['restrict_role'];
	if(isset($restrict_role) && in_array($user_role,$restrict_role))
	{		
	?>
		<script>window.location= "<?php echo home_url(); ?>"; </script>
	<?php		
	} else {
	
    global $wpdb;
    /**
     * Redirect incase of non-loggedin user
     */

    if (!empty($atts['config'])) {
        $config_id = $atts['config'];
    } elseif (!empty($_GET['config'])) {
        $config_id = $_GET['config'];
    }

    
    $group_info = get_term_by('id', $group_id, 'group');
    $group_name = $group_info->name;

    $uploads_dir = wp_upload_dir();

    $terms_group = get_term($_GET['config'], 'configuration');
    $args = wp_parse_args($args, apply_filters('woocommerce_breadcrumb_defaults', array(
        'delimiter' => '&nbsp;&nbsp;&#187;&nbsp;&nbsp;',
        'wrap_before' => '<nav class="woocommerce-breadcrumb" ' . ( is_single() ? 'itemprop="breadcrumb"' : '' ) . '>',
        'wrap_after' => '<div class="groupNameText"> » ' . $group_name . '</div></nav>',
        'before' => '',
        'after' => '',
        'home' => _x('Home', 'breadcrumb', 'woocommerce'),
        'products' => _x('Products', 'breadcrumb', 'woocommerce'),
    )));

    $breadcrumbs = new WC_Breadcrumb();

    if (!empty($args['home'])) {
        $breadcrumbs->add_crumb($args['home'], apply_filters('woocommerce_breadcrumb_home_url', home_url()));
    }
    if (!empty($args['products'])) {
        $breadcrumbs->add_crumb($args['products'], site_url('products'));
    }

    $args['breadcrumb'] = $breadcrumbs->generate();

    echo wc_get_template('global/breadcrumb.php', $args);

    $user_discount = get_user_meta( wp_get_current_user()->id, 'user_discount', true );


    ?>
    <link rel="stylesheet" type="text/css" href="<?php echo plugins_url(); ?>/custom_cable_configuration/css/animate.css">
    <link rel="stylesheet" type="text/css" href="https://printjs-4de6.kxcdn.com/print.min.css">

    <style type="text/css">
        /*.left-box-container{
            overflow: hidden;
            height: auto;
            width: auto;
        }*/
        ul.sub-content>li>a:hover,
        ul.sub-content>li>a.sub-menu-active{
            background-color: rgba(242, 242, 242, 1);
        }
        ul.sub-content>li:first-child::before{
            content: "Heyaaaaa";
        }
        ul.sub-content>li>a{
          min-height: 101px;
        }
        .bouton-image:before {
          content: "";
          width: 22px;
          height: 22px;
          display: inline-block;
          margin-right: 5px;
          vertical-align: text-top;
          background-color: transparent;
          background-position : center center;
          background-repeat:no-repeat;
          background-size: cover;
        }

        .monBouton{
            font-size: 1em;
            padding: 0.5em !important;
        }

        .monBouton:before{
           background-image : url(<?php echo $uploads_dir["baseurl"] .'/customcable/request-quote-icon.png'; ?>);
        }
        .monBouton:active,
        .monBouton:hover,
        .monBouton:focus,
        .monBouton:visited{
          color: rgb(255, 255, 255);
        }

        /**
         * CSS3 Loader
         */
        /* Absolute Center Spinner */
.loading {
  position: fixed;
  z-index: 999;
  height: 2em;
  width: 2em;
  overflow: show;
  margin: auto;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
}

/* Transparent Overlay */
.loading:before {
  content: '';
  display: block;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.3);
}

/* :not(:required) hides these rules from IE9 and below */
.loading:not(:required) {
  /* hide "loading..." text */
  font: 0/0 a;
  color: transparent;
  text-shadow: none;
  background-color: transparent;
  border: 0;
}

.loading:not(:required):after {
  content: '';
  display: block;
  font-size: 10px;
  width: 1em;
  height: 1em;
  margin-top: -0.5em;
  -webkit-animation: spinner 1500ms infinite linear;
  -moz-animation: spinner 1500ms infinite linear;
  -ms-animation: spinner 1500ms infinite linear;
  -o-animation: spinner 1500ms infinite linear;
  animation: spinner 1500ms infinite linear;
  border-radius: 0.5em;
  -webkit-box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.5) -1.5em 0 0 0, rgba(0, 0, 0, 0.5) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
  box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) -1.5em 0 0 0, rgba(0, 0, 0, 0.75) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
}

/* Animation */

@-webkit-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@-moz-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@-o-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
        /**
         *  CSS3 Loader
         */

.loaderO {
  border: 7px solid #f3f3f3;
  border-radius: 58%;
  border-top: 7px solid #3498db;
  width: 40px;
  height: 40px;
  -webkit-animation: spin 2s linear infinite;
  animation: spin 2s linear infinite;
  position: absolute;
  right: 20px;
  margin-top: -41px;
}

/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.guser_err{
	color:red;
	font-size:12px;	
}

#specialOrderPopupGuser .modal-header {
    padding: 15px;
    border-bottom: none;
}
#specialOrderPopupGuser .dyn-content form {
    clear: left;
}
#specialOrderPopupGuser .dyn-content p {
    margin-bottom: 17px;
    padding: 0 10px;
}
#specialOrderPopupGuser .dyn-content form div {
    width: 50%;
    float: left;
    padding: 0 10px;
}
#specialOrderPopupGuser .dyn-content form div input {
    width: 100%;
    height: 37px;
    border: solid 1px #ddd;
    border-radius: 3px;
    background: #eee;
    padding-left: 15px;
    outline: none;
}
#specialOrderPopupGuser .dyn-content form div:nth-child(4) {
    width: 100%;
    margin-top: 15px;
}

#specialOrderPopupGuser .dyn-content form .btn-group {
    float: left;
    width: 100%;
    padding: 0;
    text-align: right;
    padding-right: 10px;
}

#specialOrderPopupGuser .dyn-content form .btn-group button.btn {
    border-radius: 4px;
    float: none;
}

#specialOrderPopupGuser .dyn-content h3 {
    font-family: 'Alfa Slab One', sans-serif;
    text-align: center;
    margin: 20px 0 20px 0px;
    font-size: 33px;
    color: #34456c;
    border-bottom: solid 1px #eee;
    padding-bottom: 25px;
}
#specialOrderPopupGuser .dyn-content form button.btn {
    border: none;
    padding: 8px 17px;
    margin-left: 5px;
}
#specialOrderPopupGuser .dyn-content {
    padding: 10px 10px 10px 10px;
}
#specialOrderPopupGuser .modal-header button.close {
    opacity: 1;
    position: absolute;
    top: 2px;
    right: 0;
    width: 30px;
    height: 30px;
}
#specialOrderPopupGuser .dyn-content form #empty_err { float: left; width: 100%; padding-left: 10px; }



</style>

<style type="text/css" media="print">
@page { size: auto;  margin: 0mm; }
</style>

    <div class="alert alert-dismissable" style="display:none;">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <span class="alert_msg"></span>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <?php
            $upload_dir = wp_upload_dir();
            $c_logo_path = $upload_dir['url'] . "/ultimatemember/" . wp_get_current_user()->id . "/";
            $c_logo = get_user_meta(wp_get_current_user()->id, 'company_logo', TRUE);
            $c_logo = str_replace(" ", "-", $c_logo);
            $c_logo = strtolower($c_logo);
            if (!empty(get_user_meta(wp_get_current_user()->id, 'company_logo', TRUE))) {
                ?>
                <div class="custom-sol"><span>Custom solution for</span> <img alt="" src="<?php echo $c_logo_path . $c_logo; ?>" height="40px" width="80px" /></div>
            <?php } ?>
        </div>
    </div>
    <?php
    //get hard-coded conditions
    include('hardcode_conditions.php');

    // echo "<pre>";
    // echo "hard code conditions";
    // print_r($conditions_arr);
    $hardcoded_conds = $conditions_arr;
    //Get Conditions from db
    $get_conditions = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "cable_pricing", ARRAY_A);
    $db_conds = $get_conditions;
    // echo "db conditions";
    // print_r($get_conditions);

    foreach ($db_conds as $key => $value) {
        echo "<meta data-type='db-conds'";
        foreach ($value as $k => $v) {
            echo " data-{$k}='{$v}' ";
        }
        echo " />";
    }

    foreach ($hardcoded_conds as $key => $value) {

        echo "<meta data-type='hardcoded-conds'";
        echo " data-for='{$key}' ";
        foreach ($value as $k => $v) {
            foreach ($v as $ikey => $ivalue) {
                $ival = preg_replace('/\s+/', '-', $ikey);
                echo " data-sub-type-{$k}-{$ival}='{$ivalue}' ";
            }
        }
        echo " />";
    }

    ?>
    <div class="row canvas-topsec">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-lg-offset-3 col-md-offset-3 col-xs-12 col-xs-12">
            <h2>custom cable configurator</h2>
        </div>
		<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 customer-block">
          <?php  $company_logo = get_user_meta(get_current_user_id(),'company_logo');

           ?>
            <p><img width="100" src="<?php echo $company_logo[0]; ?>"></p>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 customer-block">
          <?php  $title = get_user_meta(get_current_user_id(),'title'); ?>
            <p><?php echo $title[0]; ?></p>
        </div>

    </div>
    <div class="row-outer row">
	   <div class="col-md-12">
        <div class ="canvas-outer" id="canvas-outerDiv">
           <div class="row">
			<div class="col-sm-4 col-md-3 col-lg-3 col-xl-2 leftmenu-sec">
                <ol id="steps-customs" style="overflow: hidden; width: auto; height: 541px;">
                    <?php
                    $config_terms = get_terms('configuration', array(
                        'hide_empty' => false
                    ));
                    if (!empty($config_terms)) {
                        foreach ($config_terms as $config_value) {
                            $config_id = $config_value->term_id;
                            $config_name = $config_value->name;
                            $term_meta = get_option("taxonomy_term_$config_id");
                            $get_components = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."components WHERE group_id like '%".$group_id."%' and configuration_id like '%".$config_id."%'", ARRAY_A);
                            /**
                             * Positions:
                             * #1 Wire
                             * #2 Fanout
                             * #3 Left-connector
                             * #4 right-connector
                             * #5 boot
                             */

                            /**
                             * Conditions to be applied in-case of MTP-Cassettes
                             * Hiding connector-b incase of Cassettes
                             */
                            if ( stripos($group_name, 'cassettes') !== false && stripos($config_name, 'connector a') !== false)
                              continue;

                            if ( stripos($group_name, 'cassettes') !== false && stripos($config_name, 'connector b') !== false )
                                $config_name = "Output Connector";

                            if(count($get_components) > 0)
                            {
                            ?>

                            <li
                                class="parent-element"
                                data-config-name="<?php echo preg_replace('/\s+/', '_', strtolower($config_name)); ?>"
                                <?php
                                    // if ( stripos($group_name, 'cassettes') !== false )
                                    echo "data-group-name='". $group_name ."'";
                                ?>
                              >
                              <a href="javascipt:void(0);" data-configid="<?php echo $config_id; ?>"
                                data-config-name="<?php echo $config_name; ?>"
                                data-cgui-component-name="<?php echo $config_name; ?>"
                              ><?php echo $config_name; ?></a>
                              <ul class="sub-content width-div">

                            <?php

                              // Check if the element requires a different layout to be diplayed with .

                              $custom_layouts = array('breakout options', 'length');

                              if ( in_array(strtolower($config_name), $custom_layouts) ) {


                                  // Display data in a diferent format for breakout options
                                  if (strtolower($config_name) == 'breakout options' ) {

                                    foreach($get_components as $component_value) {
                                        $description = $component_value['description'];
                                        $menu_img = "";
                                        $component_id = $component_value['component_id'];
                                        $component_name = $component_value['name'];

                                        $canvas_imgs = json_decode($component_value['canvas_image'], true);

                                        if ($component_value['menu_image'] != "" || $component_value['menu_image'] != null) {
                                            $menu_imgs = json_decode($component_value['menu_image'], true);
                                            $menu_imgs_arr = json_decode($component_value['menu_image']);
                                            $menu_img = $upload_dir['url'] . "/customcable/menu/" . $menu_imgs[0];
                                        }
                                        $field_type = $component_value['field_type'];
                                        $part_number = $component_value['part_number'];
                                        $weight = $component_value['weight'];
                                      ?>

                                          <li>
                                              <a href="#"
                                                title="<?php echo $component_name; ?>"
                                                data-cGui-img-uri="<?php echo $menu_img; ?>"
                                                data-cGui-component-id="<?php echo $component_id; ?>"
                                                data-cGui-component-name="<?php echo $component_name; ?>"
                                                data-cGui-component-description="<?php echo $description; ?>"
                                                data-config-id="<?php echo $config_id; ?>"
                                                data-config-name="<?php echo $config_name; ?>"
                                                data-config-term-meta="<?php echo $term_meta[0]; ?>"
                                                data-canvas-image="<?php echo htmlspecialchars(json_encode($canvas_imgs), ENT_QUOTES, 'UTF-8'); ?>"
                                                data-field-type="<?php echo $field_type; ?>"
                                                data-cGui-component-part-number="<?php echo $part_number; ?>"
                                                data-cGui-component-price="<?php echo $component_value['price']; ?>"
                                                data-cGui-component-weight="<?php echo $weight; ?>"
                                                >
                                              </a>
                                              <div class="select-boxbreakout">
                                                <p><?php echo $component_name; ?></p>
                                                <select class="selectpicker" data-component-name="<?php echo $component_name; ?>">
                                                      <?php if (strpos(strtolower($component_name), 'side a') !== false) { ?>
                                                      <option value="NA" data-part-number="Z">Not Applicable</option>
                                                      <option data-part-number="1">12 inches</option>
                                                      <option data-part-number="2">18 inches</option>
                                                      <option data-part-number="3">24 inches</option>
                                                      <option data-part-number="4">36 inches</option>
                                                      <?php } ?>

                                                      <?php if (strpos(strtolower($component_name), 'side b') !== false) { ?>
                                                      <option value="NA" data-part-number="Z">Not Applicable</option>
                                                      <option data-part-number="1">12 inches</option>
                                                      <option data-part-number="2">18 inches</option>
                                                      <option data-part-number="3">24 inches</option>
                                                      <option data-part-number="4">30 inches</option>
                                                      <option data-part-number="5">36 inches</option>

                                                      <?php } ?>

                                                      <?php if (strpos(strtolower($component_name), 'furcation') !== false) { ?>
                                                      <option data-part-number="1">2 mm</option>
                                                      <option data-part-number="2">3 mm</option>
                                                      <option data-part-number="3">NA</option>
                                                      <?php } ?>

                                                </select>
                                                </div>
                                          </li>

                                    <?php } // End of foreach ?>
                                      <li class="custom-btnsec">
                                          <a href="#" class="btn-outer">
                                              <button type="button" data-canvas-menu-trigger class="btn btn-default btn-centerpostion breakout-option-trigger">Go!</button> </a>
                                      </li>

                                  <?php } // End of If

                                  // Display data in a diferent format for breakout options
                                  if (strtolower($config_name) == 'length' ) {

                                        $description = $component_value['description'];
                                        $menu_img = "";
                                        $component_id = $component_value['component_id'];
                                        $component_name = $component_value['name'];

                                        $canvas_imgs = json_decode($component_value['canvas_image'], true);

                                        if ($component_value['menu_image'] != "" || $component_value['menu_image'] != null) {
                                            $menu_imgs = json_decode($component_value['menu_image'], true);
                                            $menu_imgs_arr = json_decode($component_value['menu_image']);
                                            $menu_img = $upload_dir['url'] . "/customcable/menu/" . $menu_imgs[0];
                                        }

                                  ?>
                                      <li>
                                        <!-- //Hack for layout to work| DO NOT REMOVE -->
                                        <a href="#"></a>
                                        <!-- // Hack for layout to work -->
                                        <div class="select-boxbreakout container">
                                            <p></p>
                                            <input type="text" id="inputField" maxlength="7" class="form-control" style="">
                                        </div>
                                      </li>

                                      <li>
                                          <a
                                            href="#"
                                            title="<?php echo $component_name; ?>"
                                            data-cGui-img-uri="<?php echo $menu_img; ?>"
                                            data-cGui-component-id="<?php echo $component_id; ?>"
                                            data-cGui-component-name="<?php echo $component_name; ?>"
                                            data-cGui-component-description="<?php echo $description; ?>"
                                            data-config-id="<?php echo $config_id; ?>"
                                            data-config-name="<?php echo $config_name; ?>"
                                            data-config-term-meta="<?php echo $term_meta[0]; ?>"
                                            data-canvas-image="<?php echo htmlspecialchars(json_encode($canvas_imgs), ENT_QUOTES, 'UTF-8'); ?>"
                                            data-field-type="<?php echo $field_type; ?>"
                                            data-cGui-component-part-number="<?php echo $part_number; ?>"
                                            data-cGui-component-price="<?php echo $component_value['price']; ?>"
                                            data-cGui-component-weight="<?php echo $weight; ?>"
                                            ></a>
                                          <div class="select-boxbreakout">
                                            <p></p>
                                            <select class="selectpicker length" data-component-name="<?php echo $component_name; ?>">
                                                <?php
                                                      foreach($get_components as $component_value) {
                                                          $description = $component_value['description'];
                                                          $menu_img = "";
                                                          $component_id = $component_value['component_id'];
                                                          $component_name = $component_value['name'];

                                                          $canvas_imgs = json_decode($component_value['canvas_image'], true);

                                                          $field_type = $component_value['field_type'];
                                                          $part_number = $component_value['part_number'];
                                                          $weight = $component_value['weight'];
                                                    ?>
                                                      <option data-part-number="<?php echo $part_number; ?>"><?php echo $component_name; ?></option>
                                                <?php } // End of foreach ?>
                                            </select>
                                          </div>
                                      </li>

                                      <li class="custom-btnsec">
                                          <a
                                              href="#"
                                              class="btn-outer"
                                              data-cGui-img-uri="<?php echo $menu_img; ?>"
                                              data-cGui-component-id="<?php echo $component_id; ?>"
                                              data-cGui-component-name="<?php echo $component_name; ?>"
                                              data-config-id="<?php echo $config_id; ?>"
                                              data-config-name="<?php echo $config_name; ?>"
                                              data-config-term-meta="<?php echo $term_meta[0]; ?>"
                                              data-canvas-image="<?php echo json_encode($canvas_imgs); ?>"
                                              data-field-type="<?php echo $field_type; ?>"
                                              data-cGui-component-part-number="<?php echo $part_number; ?>"
                                          ><button
                                              data-canvas-menu-trigger
                                              type="button"
                                              class="btn btn-default btn-centerpostion length-trigger">Go! </button></a>
                                      </li>
                                          <!-- <button
                                                data-canvas-menu-trigger
                                                data-cGui-img-uri="<?php //echo $menu_img; ?>"
                                                data-cGui-component-id="<?php //echo $component_id; ?>"
                                                data-cGui-component-name="<?php //echo $component_name; ?>"
                                                data-config-id="<?php //echo $config_id; ?>"
                                                data-config-name="<?php //echo $config_name; ?>"
                                                data-config-term-meta="<?php //echo $term_meta[0]; ?>"
                                                data-canvas-image="<?php //echo json_encode($canvas_imgs); ?>"
                                                data-field-type="<?php //echo $field_type; ?>"
                                                data-cGui-component-part-number="<?php //echo $part_number; ?>"
                                                type="button" class="btn btn-sm btn-default">Go!</button> -->



                              <?php } // End of If(measure check)

                              } // End of outer if conditions(check if element requires different layout)
                              else { ?>

                                <?php
                                    $i=0;
                                  foreach($get_components as $component_value)
                                  {
                                    $description = $component_value['description'];
                                    $menu_img = "";
                                    $component_id = $component_value['component_id'];
                                    $component_name = $component_value['name'];
                                    $canvas_imgs = json_decode($component_value['canvas_image'], true);
                                    if ($component_value['menu_image'] != "" || $component_value['menu_image'] != null) {
                                        $menu_imgs = json_decode($component_value['menu_image'], true);
                                        $menu_imgs_arr = json_decode($component_value['menu_image']);
                                        $menu_img = $upload_dir['url'] . "/customcable/menu/" . $menu_imgs[0];
                                    }
                                    $field_type = $component_value['field_type'];
                                    $part_number = $component_value['part_number'];
                                    $weight = $component_value['weight'];
                                ?>
                                    <li>
                                        <a
                                            href="#"
                                            <?php if ($i >0) echo " disabled "; ?>
                                            data-cGui-img-uri="<?php echo $menu_img; ?>"
                                            data-cGui-component-id="<?php echo $component_id; ?>"
                                            data-cGui-component-name="<?php echo $component_name; ?>"
                                            data-cGui-component-description="<?php echo $description; ?>"
                                            data-config-id="<?php echo $config_id; ?>"
                                            data-config-name="<?php echo $config_name; ?>"
                                            data-config-term-meta="<?php echo $term_meta[0]; ?>"
                                            data-canvas-image="<?php echo htmlspecialchars(json_encode($canvas_imgs), ENT_QUOTES, 'UTF-8'); ?>"
                                            data-field-type="<?php echo $field_type; ?>"
                                            data-cGui-component-part-number="<?php echo $part_number; ?>"
                                            data-cGui-component-price="<?php echo $component_value['price']; ?>"
                                            data-cGui-component-weight="<?php echo $weight; ?>"
                                            <?php
                                              // Adding class if no menu image(For styling)
                                              if($menu_img == '')
                                                echo "class='no-graphics-img'";

                                                // Clickable menu only if the field type is not input
                                                if($field_type != 1) { ?>
                                                    data-canvas-menu-trigger
                                            <?php } ?>
                                        >
                                        <?php if($field_type != 1) { ?>
                                            <span class="graphics-img">
                                                <?php if($menu_img != ''){ ?>
                                                    <img class="pro-lbl" alt="<?php echo $component_name; ?>" src="<?php echo $menu_img; ?>">
                                                <?php } ?>
                                            </span>
                                        <?php } ?>
                                        <?php if($field_type != 1) { ?>
                                            <p><?php echo $component_name; ?></p>
                                            <?php  if ($description !== '') { ?>
                                                <label  data-toggle="tooltip" data-placement="top" title="<?php echo $description; ?>" class="tooltip-icn"><i class="fa fa-info" aria-hidden="true"></i></label>
                                            <?php } ?>
                                        <?php } ?>
                                        <?php if($field_type == 1) { ?>
                                            <input type="text" id="inputField" maxlength="7" class="form-control" style="">
                                            <button
                                                data-canvas-menu-trigger
                                                data-cGui-img-uri="<?php echo $menu_img; ?>"
                                                data-cGui-component-id="<?php echo $component_id; ?>"
                                                data-cGui-component-name="<?php echo $component_name; ?>"
                                                data-config-id="<?php echo $config_id; ?>"
                                                data-config-name="<?php echo $config_name; ?>"
                                                data-config-term-meta="<?php echo $term_meta[0]; ?>"
                                                data-canvas-image="<?php echo json_encode($canvas_imgs); ?>"
                                                data-field-type="<?php echo $field_type; ?>"
                                                data-cGui-component-part-number="<?php echo $part_number; ?>"
                                                type="button" class="btn btn-sm btn-default">Go!</button>
                                        <?php } ?>
                                        </a>

                                        <?php if (strpos(strtolower($config_name), 'boot type') !== false && $menu_img != '') { ?>
                                            <div class="color-optionDiv"><?php
                                                    if ( count($menu_imgs_arr) > 0 ) {
                                                      foreach($menu_imgs_arr as $img) {
                                                          $img = str_replace(".png", "", $img);
                                                          $img_arr = explode("_", $img);
                                                  ?><button class='btn <?php echo $img_arr[count($img_arr)-1]."-btn boot-color-trigger"; ?>' data-canvas-menu-trigger data-color="<?php echo $img_arr[count($img_arr)-1]; ?>"></button><?php } } ?>
                                            </div>
                                        <?php } ?>

                                    </li>


                                <?php $i++;  }  // get_components foreach closed here ?>

                            <?php }// End of else for custom layouts conditions ?>
								
                              </ul>
                              <!-- <div class="left-box-container">
                                  <div class="left-box"></div>
                              </div> -->
                            </li>
                        <?php


                                } // get components count if closed here
                              } // configterms foreach closed here
                            } //config terms if closed here
                        ?>

                    <!--end loop here-->

                    <!-- <li class="parent-element hidden" data-config-name="Breakout">
                        <a href="javascipt:void(0);" data-configid="956">Breakout options</a>
                        <ul class="sub-content">

                            <li>
                                <a href="#" data-cgui-img-uri="" data-cgui-component-id="73" data-cgui-component-name="XXX" data-config-id="956" data-config-name="Length" data-config-term-meta="" data-canvas-image="null" data-field-type="1" data-cgui-component-part-number="-" data-cgui-component-price="">
                                    <p>Breakout Length (Side A)</p>
                                 <select class="selectpicker">
                                        <option>12 inches</option>
                                        <option>13 inches</option>
                                        <option>14 inches</option>
                                    </select></a> </li>

                                    <li>
                                <a href="#" data-cgui-img-uri="" data-cgui-component-id="73" data-cgui-component-name="XXX" data-config-id="956" data-config-name="Length" data-config-term-meta="" data-canvas-image="null" data-field-type="1" data-cgui-component-part-number="-" data-cgui-component-price="">
                                    <p>Breakout Length (Side B)</p>
                                 <select class="selectpicker">
                                        <option>12 inches</option>
                                        <option>13 inches</option>
                                        <option>14 inches</option>
                                    </select></a> </li>

                                    <li>
                                <a href="#" data-cgui-img-uri="" data-cgui-component-id="73" data-cgui-component-name="XXX" data-config-id="956" data-config-name="Length" data-config-term-meta="" data-canvas-image="null" data-field-type="1" data-cgui-component-part-number="-" data-cgui-component-price="">
                                    <p>Breakout Length (Side C)</p>
                                 <select class="selectpicker">
                                        <option>12 inches</option>
                                        <option>13 inches</option>
                                        <option>14 inches</option>
                                    </select></a> </li>
                          </ul>
                          </li> -->
                </ol>

                <div class="row row-block">
                  <div class="col-sm-12">
                    <div class="qty-sec text-right forDesktop-view">

                        <h3>
                          $<span data-canvas-gui="price">00.00</span> <p class="qty-p">ea.</p>
                        </h3>
                        <p class="part-no hidden">PART #: <span data-canvas-gui="part-number" 215ABB1345M>XXXXXXXXXXX</span> </p>

                        <a data-canvas-gui="cartBtn" hre="<?php echo site_url() . '/cart/?add-to-cart=2164&action=orderData&quantity=1'; ?>" class="btn btn-sm addtocart hidden" disabled="disabled">add to cart</a>

                        <button data-canvas-gui="specialOrderPopupBtn" class="btn btn-sm bouton-image hidden monBouton <?php if($user_role == "guest_user"){ echo "guserquote"; } ?>"><i class="quote-icon" aria-hidden="true"></i> Request a Quote!</button>

                    </div>
                  </div>
                </div>


            </div>

            <div class="col-sm-8 col-md-9 col-lg-9 col-xl-10 sm-canvas">
                <!-- <canvas id="canvas_config" width="900" height="530">
                    Sorry. Your browser does not support HTML5 canvas element.
                </canvas> -->
                <div style="position: relative;" class="canvasouter-div">
                    <canvas id="canvas_config" width="900" height="515" style="position: absolute; left: 0; top: 0; z-index: 1;">Sorry. Your browser does not support HTML5 canvas element.</canvas>
                    <canvas id="canvas_config2" width="900" height="515" style="position: absolute; left: 0; top: 0; z-index: 2;">Sorry. Your browser does not support HTML5 canvas element.</canvas>
                     <div class="loader-imgSec hidden" id="loader-divsec">
                        <img src="<?php echo site_url(); ?>/wp-content/uploads/ajax_loader_gray_512.gif" class="img-responsive">
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="row">
				  <div class="col-md-12">
					<!--<p style="color:red;">Note: While configuring if required to change previous selection, simply click on the green tab of item and proceed from that particular step.</p>-->
                  <button data-canvas-gui="resetCanvasBtn" class="btn btn-sm resetbtn pull-right hidden"><i class="fa fa-refresh" aria-hidden="true"></i> Reset</button>
                  <button data-canvas-gui="printCanvasBtn" class="btn btn-sm btn-info hidden pull-right printBtn"><i class="fa fa-print" aria-hidden="true"></i> Print</button>
				  </div>
                </div>
                <div class="row"><div class="col-lg-12 col-xs-12">
                  <div class="qty-sec text-right forMobile-view">

                    <h3>$<span data-canvas-gui="price">0.00</span>  <p class="qty-p">ea.</p> </h3>
                    <p class="part-no">PART #: <span data-canvas-gui="part-number" 215ABB1345M>XXXXXXXXXXX</span> </p>

                    <a data-canvas-gui="cartBtn" hre="<?php echo site_url() . '/cart/?add-to-cart=2164&action=orderData&quantity=1'; ?>" class="btn addtocart" disabled="disabled">add to cart</a>
                    </div></div>
                </div>

            </div>
		   </div>	
        </div>
	   </div>	
        <!--<div class="col-sm-12 text-right pad0 btm-btns">-->
        <div class="offset-sm-4 offset-md-4 offset-lg-3  col-sm-8 col-md-8 col-lg-9 col-xs-12 text-right pad0 btm-btns">
                <!-- <a class="post-edit-link" href="<?php //echo site_url('products');                   ?>">Return to products</a>-->

            <div class="partNo"></div>

                                                        <!-- <button id="reset_config" type="button" class="btn custom-btn pull-left"><i class="fa fa-refresh" aria-hidden="true"></i> Reset</button> -->


          <?php
                if (isset($_REQUEST['cart-request'])) {

                }
          ?>
			
			<!-- Hidden values to check if confirm box required on page unload -->
			<input type="hidden" name="activity_status" id="activity_status" value="0">
			<input type="hidden" name="request_status" id="request_status" value="0">
			<input type="hidden" name="addcart_status" id="addcart_status" value="0">
          
            <form class="cart-config" action="<?php echo site_url() . '/cart' ?>" method ="post">
                <div class="pro_id"><input type="hidden" name="add-to-cart" value="" /></div>
                <input type="button" value="Request Quote" class="btn custom-btn request-to-admin disable-btn" disabled style="display:none;" onclick="requestToAdmin(<?php echo $config_id ?>)" />
                <span class="price"></span>
                <input type="submit" value="Add to cart" class="btn custom-btn add-to-cart disable-btn" disabled style="display:none;" />
            </form>

            <div class="product_desc" style="display:none;">this is product short description</div>
        </div>
        <div class="col-sm-12 col-xs-12 pad-right0">
            <?php
                $user_id = wp_get_current_user()->id;
                $customer_orders = get_posts( array(
                    'meta_key'    => '_customer_user',
                    'meta_value'  => $user_id,
                    'post_type'   => 'shop_order',
                    'post_status' => array_keys( wc_get_order_statuses() ),
                    'numberposts' => -1
                ));
                foreach($customer_orders as $orders)
                {
                    $partno = array();
                    $get_order = wc_get_order($orders->ID);
                    $items = $get_order->get_items();
                    $date_created = date_create($orders->post_date);
                    $date_created = date_format($date_created, 'Y-m-d');
                    
                    $total_amount = number_format((float) $get_order->get_total(), 2, '.', '');  // Outputs -> 105.00
                    $total = $total_amount.' for '.$get_order->get_item_count().' item';

                    // $total = number_format((float) $get_order->get_total(), 2, '.', '');
                    $status = ucfirst($get_order->get_status());

                    foreach($items as $key => $item){
                        $order_meta = wc_get_order_item_meta($key,'Part Number');
                        if(!empty($order_meta))
                        {
                           echo "<input type='hidden' data-q='***' name='partno_orderid' id='".$order_meta."' value='".$orders->ID."' data-date='". $date_created ."' data-status='".$status."' data-total='".$total."'>";
                           $partno_arr[] =  $order_meta;
                        }
                    }
                    $orderid = (string)$orders->ID;
                    $orderid_arr[] = $orderid;
                }
            ?>
            <?php if (is_user_logged_in()) { ?>
            <div class="search-row">
                <div class="col-sm-4 col-xs-6">
                    <form class="form-inline history-frm" method="post" id="order_history">
                        <div class="form-group">
                            <label>Search Order</label>
                            <input type="text" class="form-control" id="order_name" name="orderId" placeholder="Enter Order id for Re-order">
                            <a href="javascript:void(0)" type="submit" id="btn-submit" class="btn btn-default"><i class="fa fa-angle-right" aria-hidden="true"></i></a>
                        </div>
                        <div id="results"></div>
                    </form>
                </div>

                <div class="col-sm-4 col-xs-6 pull-right">

                    <form method="post" class="form-inline history-frm">
                        <div class="form-group">
                            <label>Search Part Number</label>
                            <input type="hidden" name="action" value="partno">
                            <input type="text" class="form-control" id="partNumber" name="partNumber" placeholder="Enter Part no here for quick search">
                            <a id="getPartNumberButton" href="javascript:void(0)" class="btn btn-default"><i class="fa fa-angle-right" aria-hidden="true"></i></a>

                        </div>
                    </form>
                </div>

                <img id="loadingDiv" style="position: relative; left: 50%; display: none;" src="<?php echo plugins_url('custom_cable_configuration/images/ajx.gif'); ?>">


            </div>
            <?php } ?>
        </div>
        <div class="col-sm-12 history-frm-outer">
            <img id="loading-image" src="<?php echo plugins_url() . '/custom_cable_configuration/' ?>/images/small_loader.gif" style="display:none;"/>
            <table id="table" class="hidden">
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </table>
        </div>
         <!-- <div class="col-sm-12 serachPartNoForm">
            <table border="1">
                <thead>
                    <tr>
                        <th>Part Number</th>
                        <th>Group Name</th>
                        <th>Product Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="items">
                    <tr id="tr0">
                        <td><div class="part_no"></div></td>
                        <td><div class="group_name"></div></td>
                        <td><div class="product_name"></div></td>
                        <td class="term_id"></td>
                    </tr>
                </tbody>
            </table>
        </div> -->
        <p class="noResultFound" style="display: none;text-align: center;">No record found</p>
        <p class="invalid_partno" style="display: none;text-align: center;"></p>
    </div>

   <!-- Modal For Configurations -->
    <div id="configurationsummary" class="modal fade configurationsummary" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Configuration Summary</h4>
          </div>
          <div class="modal-body">
              <div class="custom-content">

              </div>
              <!-- <div class="outer-dv"><div class="left-sec">Glass Type</div><div class="right-sec">SM (9/125um)</div></div> -->

           <!-- <div class="outer-dv"> <div class="left-sec">Jacket Type</div> <div class="right-sec">INDOOR PLENUM 250um</div></div>
            <div class="outer-dv"><div class="left-sec">Fiber Count</div> <div class="right-sec">SIMPLEX</div></div>
            <div class="outer-dv"><div class="left-sec">Connector A</div> <div class="right-sec"> SC UPC</div></div>
            <div class="outer-dv"><div class="left-sec">Connector B</div> <div class="right-sec">SC APC</div></div>
            <div class="outer-dv"><div class="left-sec">Boot Type</div> <div class="right-sec"> Ribbed </div></div>
            <div class="outer-dv"><div class="left-sec">Boot Color</div> <div class="right-sec"> Yellow</div></div>
            <div class="outer-dv"><div class="left-sec">Length</div> <div class="right-sec">10 Meters </div></div>
           <div class="outer-dv"> <div class="left-sec">Options</div> <div class="right-sec">Test Reference Cord </div></div>
            <div class="outer-dv price-dv"><div class="left-sec">Unit Price</div> <div class="right-sec"> $100</div></div> -->
            <input type="button" name="reset" class="reset" data-canvas-trigger="reset" value="Reset">
            <input type="button" name="editconfig" class="editconfig" value="Edit Configuration">

            <div class="loaderO" style="display: none"></div>
            <input type="button" class="cart-button" name="addtocart"  value="Add To Cart">
          </div>

        </div>

      </div>
    </div>
    <!-- Modal -->

    <!-- Modal For Configurations -->
    <div class="modal fade configurationsummary" id="specialOrderPopup" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <div class="dyn-content"></div>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


	 <!-- Modal For GuestUser Contact Details -->
    <div class="modal fade configurationsummary" id="specialOrderPopupGuser" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <div class="dyn-content">
				<h3>Request Pricing</h3>
				<p>Our team will contact you today with more details on lead time and cost for this assembly. How can we contact you?</p>
				<form name="guestusercontact" method="post">
					<span id="empty_err" class="guser_err" style="display:none;"></span>
					<div>
					<label>Name</label>
					<input name="contact_name" id="contact_name" value="" onkeyup="check_cname()"><br>
					<span id="cname_err" class="guser_err" style="display:none;">Only alphabets and spaces are allowed.</span>
					</div>
					<div>
					<label>Email</label>
					<input name="contact_email" id="contact_email" value="" onkeyup="check_cemail()"><br>
					<span id="cemail_err" class="guser_err" style="display:none;">Please provide a valid email.</span>
					</div>
					<div>
					<label>Phone Number</label>
					<input name="contact_phno" id="contact_phno" value="" onkeyup="check_cphno()"><br>
					<span id="phno_err" class="guser_err" style="display:none;">Invalid Phone Number.</span>
					</div>
					<div class="btn-group">
				<button type="button" class="btn btn-primary guyes" data-trigger="specialOrderModalguser" data-btn="yes"><i></i>Submit</button>
				<!-- <button type="button" class="btn btn-default guno" data-trigger="specialOrderModalguser" data-btn="no" data-dismiss="modal"> No, Continue Shopping</button>-->
				</div>
				</form>
            </div>             
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    
    <div class="modal fade configurationsummary" id="specialOrderPopupGuserMsg" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <div class="msg-content"></div>             
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    

	<div class="modal fade configurationsummary" id="specialOrderPopupGuserMsg" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <div class="dyn-content"></div>             
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- Modal for login -->
    <!-- //End of Modal for Login --> 
    <div class="modal fade ajaxLogin" id="specialOrderPopup" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <div class="dyn-content">
                <?php echo do_shortcode('[lwa]'); ?>
            </div>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- Script for canvas */ -->

     <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> -->
     <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
      <script type="text/javascript" src="<?php echo plugins_url() . '/custom_cable_configuration/js/jquery.redirect.js'; ?>"></script>

    <script type="text/javascript" src="<?php echo plugins_url() . '/custom_cable_configuration/js/jQuery-slimScroll-1.3.8/jquery.slimscroll.min.js'; ?>"></script>
    <script type="text/javascript" src="<?php echo plugins_url() . '/custom_cable_configuration/js/dataset.1.0.1.js'; ?>"></script>

    <script type="text/javascript" src="<?php echo plugins_url() . '/custom_cable_configuration/js/check-login.js'; ?>"></script>

    <script type="text/javascript" src="https://printjs-4de6.kxcdn.com/print.min.js"></script>
    <!-- Include Custom Canvas Tool -->
    <script type="text/javascript" src="<?php echo plugins_url() . '/custom_cable_configuration/js/custom_canvas_tool.js'; ?>"></script>
	<script src="https://rawgit.com/RobinHerbots/jquery.inputmask/3.x/dist/jquery.inputmask.bundle.js"></script>
    <script type="text/javascript">
      $ = jQuery.noConflict();
		$('#contact_phno').inputmask("(999) 999-9999");
	window.addEventListener('beforeunload', function(e) {
		var activity_status = $("#activity_status").val();
		var request_status = $("#request_status").val();
		var addcart_status = $("#addcart_status").val();
		
		if(activity_status == "1" && (request_status == "0" && addcart_status == "0"))
		{
			e.preventDefault(); //per the standard
			e.returnValue = ''; //required for Chrome
		}		  		  		  		 
	});	
	function check_cname(){
		$("#empty_err").hide();			
		var regex = /^[a-zA-Z ]*$/;
		if (regex.test($("#contact_name").val())) {
			$("#cname_err").hide();						
			$(".guyes").removeAttr("disabled");
        } else {
            $("#cname_err").show();            
            $(".guyes").attr('disabled', 'disabled');
        }
	}

	function check_cemail(){
		$("#empty_err").hide();
		var regex = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
		if (regex.test($("#contact_email").val())) {
			$("#cemail_err").hide();			
			$(".guyes").removeAttr("disabled");						
        } else {
            $("#cemail_err").show();            
            $(".guyes").attr('disabled', 'disabled');
        }
	}
	function check_cphno(){			
		$("#empty_err").hide();	
		var regex = /^[0-9\s()-]*$/;
		if (!regex.test($("#contact_phno").val()) || $("#contact_phno").val().length > 16) {
			$("#phno_err").show();			
            $(".guyes").attr('disabled', 'disabled');
        } else {
            $("#phno_err").hide();            
            $(".guyes").removeAttr("disabled");         
        }
	}
	
    var BASE_PATH = "<?php echo site_url(); ?>";
    var ajaxPath = "<?php echo plugins_url() . '/custom_cable_configuration/ajax/'; ?>";
    var REQUEST_PRODUCT_QUOTE_URI = "<?php echo plugins_url() . '/custom_cable_configuration/ajax/request_product_quote.php'; ?>";
    var USER_DISCOUNT_PERCENTAGE = "<?php echo $user_discount; ?>";

    var test;
    //Init global Canvas Obj
    var c, CGui, test;

    var checkLoginUrl = "<?php echo plugins_url() . '/custom_cable_configuration/ajax/ajax.php' ?>";
    var checkLoginFormData = {
       action: 'checkLogin', 
    };


    function showLoginPopup() {
        $(".ajaxLogin").modal({
            show: true,
            backdrop: 'static'
        })
    }

    function hideLoginPopup() {
        $(".ajaxLogin").modal({
            show: false,
        })
    }
    
    (function($){

        var CANVAS_LOGO_URL = "<?php echo plugins_url() . '/custom_cable_configuration/images/logo/logo_canvas.png'; ?>";
        // Older JS for ref
        /*var divHeight = $("#canvas_config").height();
         $("#steps-customs").css('height', divHeight + 'px');*/

        $("#steps-customs").slimScroll({

        });
        var divHeight = $("#canvas_config").height();
        $("#steps-customs").css('height', divHeight + 'px');

        var divHeight = $("#canvas_config2").height();
        $("#loader-divsec").css('height', divHeight + 'px');

        var divHeight = $("#canvas_config2").height();
        $(".canvasouter-div").css('height', divHeight + 'px');


        /**
         * Printing canvas output instance
         *  -@CGui CanvasObject
         */
        function printCanvas() {
            var base64CanvasOutput = CGui.printCanvasOutput();
            //console.log("Drawing", base64CanvasOutput);
            // if (base64CanvasOutput !== false)
            //   window.open(base64CanvasOutput);
        }

        jQuery('[data-canvas-gui="printCanvasBtn"]').on('click', function(e) {
            e.preventDefault();
            //console.log("Reqesting print")
            printCanvas(CGui);
        });

        /**
         * Handling products incase of price not available
         */



        //Add to Cart Function
        // Per discount refers to discount applied per product
        function addToCart(p_id, part_number, price,metaData, productLabel, productWeight, perProductDiscount) {
          if (price === undefined) 
            price=false;
          /* Custom Add to cart function.php woocommerce_add_cart_item_data */

          var myJSON = JSON.stringify(metaData);

          $.ajax({
            type: "POST",
            url: window.location.href,
            data: {
              "post_type": "product",
              "add-to-cart": p_id,
              "part_number": part_number,
              "price": price,
              "product_label": productLabel,
              "product_weight": productWeight,
              "products_additional_details": myJSON,
              "per_product_discount": perProductDiscount,
            },
            success: function(data) {
              //console.log("Success", data);
              jQuery('.loaderO').hide();
                 window.location = BASE_PATH+'/cart';
            },
            error: function(error) {
              //console.log("Error", error);
               jQuery('.loaderO').hide();
               window.location = BASE_PATH+'/cart';
            },
            dataType: 'json'
          });

        }

        function suggest_options(suggest, matches) {
            //console.log("Matches:"+ matches);
            checkLogin(checkLoginUrl, checkLoginFormData, suggest(matches));
        }

        $(document).ready(function () {	
			$(".config-page-loader").addClass("hidden");					

			//Set activity status to 1 when any config is selected.
			$(".sub-content", "li", "a" ).click(function(){				
				$("#activity_status").val("1");
				$("#request_status").val("0");
				$("#addcart_status").val("0");
			});
			
            //autocomplete function for order id search
            $('input[name="orderId"]').autoComplete({
                  minChars: 2,
                  source: function(term, suggest){
                      term = term.toLowerCase();
                      var choices = <?php echo json_encode($orderid_arr); ?>;
                      var matches = [];
                      for (i=0; i<choices.length; i++)
                        if (~choices[i].toLowerCase().indexOf(term)) matches.push(choices[i]);
                      suggest_options(suggest, matches);
                  }
              });


              //autocomplete function for part number search
              $('input[name="partNumber"]').autoComplete({
                  minChars: 2,
                  source: function(term, suggest){
                      term = term.toLowerCase();
                      var choices = ['ActionScript', 'AppleScript'];
                      var choices = <?php echo json_encode($partno_arr); ?>;
                      var matches = [];
                      for (i=0; i<choices.length; i++)
                          if (~choices[i].toLowerCase().indexOf(term)) matches.push(choices[i]);
                      // suggest(matches);
                      suggest_options(suggest, matches);
                  }
              }); 

            //Function for Search by Part Number

            $("#getPartNumberButton").click(function(){
              $('.noResultFound').hide();
              $('.history-frm-outer').hide();
              $('.serachPartNoForm').hide();
              $('.invalid_partno').hide();

              var partNumber = $("#partNumber").val();
              if(partNumber == undefined || partNumber == "" || partNumber == null)
              {
                $('.invalid_partno').show();
                $('.invalid_partno').html("Please enter Part Number");
              }
              else
              {
                // var orderId = $("#"+partNumber).val();
                var searchedOrders = $(document).find("[id='"+partNumber+"']");

                if(searchedOrders.length == 0)
                {
                  $('.invalid_partno').show();
                  $('.invalid_partno').html("Invalid Part Number");
                }
                else
                {
                  $('#loadingDiv').show();
                  // window.location.href = "<?php //echo get_site_url(); ?>/my-account/view-order/"+orderId;

                  $('.noResultFound').hide();
                  $('.history-frm-outer').hide();
                  $('.serachPartNoForm').hide();

                  
                  // if (o) {
                      $('.history-frm-outer').show();
                      $(".table1").remove();
                      // var res = [];
                      // var res = JSON.parse(response);
                      if (searchedOrders) {
                          var len = searchedOrders.length;
                          var txt = [];
                          var order_id_already_added = [];
                          $.each(searchedOrders, function (i, val) {
                              var orderId = $(val).val();
                              if (jQuery.inArray(orderId, order_id_already_added) == -1) {
                                var partNumber = $(val).attr('id');
                                var date = $(val).attr('data-date');
                                var total = $(val).attr('data-total');
                                var status = $(val).attr('data-status');
                                txt += '<tr class="table1"><td><a href="<?php echo site_url() ?>/my-account/view-order/' + orderId + '">#' + orderId + '</a></td><td>' + date + '</td><td>' + status + '</td><td>' + '$' + total + '</td><td> <a  href="<?php echo site_url() ?>/my-account/view-order/' + orderId + '">View Order</a></td></tr>';
                                order_id_already_added.push(orderId);
                              }
                          });
                          if (txt != "") {
                              $("#table").append(txt).removeClass("hidden");
                          }
                      }
                      $('#loadingDiv').hide();
                  // }
                  // else {
                  //   $('.history-frm-outer').hide();
                  //   $('#loadingDiv').hide();
                  //   $('.noResultFound').show();  
                  // }

                }
              }
            });

            //Add Product To Cart
            $('a.addtocart').on('click', function(e) {

                if ($(this).attr('disabled') == 'disabled') {
                    e.preventDefault();
                    return;
                }
				showAddToCartPopup()
                //checkLogin(checkLoginUrl, checkLoginFormData, showAddToCartPopup, showLoginPopup);

            })

            $('input[data-canvas-trigger="reset"], .resetbtn').on('click', function(e) {
                  e.preventDefault();
                  if ( !confirm("Are you sure you want to clear the canvas? You won't be able to recover the current progress!"))
                      return;

                  CGui.CanvasToolObj.hidePrintBtn(CGui)
                  closeModal();
                  CGui.resetCanvas();
                  $("#activity_status").val("0");
                  $("#request_status").val("0");
                  $("#addcart_status").val("0");
                  
            })

            

            function showAddToCartPopup() {
                $('#configurationsummary').find('.custom-content').empty();
                var htmlToAdd = '';
                //  Get and display the part number in popup

                var partNumber = CGui.CanvasToolObj.partNumber;
                htmlToAdd += '<div class="outer-dv"><div class="left-sec">Part Number</div> <div class="right-sec">'+ partNumber +'</div></div>';

                for (var i=0; i<CGui.selectedOptions.length; i++) {
                    var label = CGui.selectedOptions[i].value.configName;
                    var value =  CGui.selectedOptions[i].value.cguiComponentName;
                    if (CGui.selectedOptions[i].key == 'length')
                        value = CGui.selectedOptions[i].value.userInput.toString() + '-' + CGui.selectedOptions[i].value.unitSelected;
                    if (CGui.selectedOptions[i].key == 'breakout_options'){
                        var value = '';
                        //console.log("nisde breakout options selected options process");
                        $('li[data-config-name="breakout_options"]').find('select').each(function(key, val){
                            value += $(val).data('component-name').replace('Breakout', '');
                            value += " "+$(val).find(":selected").text();
                            value += "<br>";
                        });
                        value = $.trim(value);
                    }

                    htmlToAdd += '<div class="outer-dv"><div class="left-sec">'+ label +'</div><div class="right-sec">'+ value +'</div></div>';
                }

                // End

                htmlToAdd += '<div class="outer-dv price-dv"><div class="left-sec">Unit Price</div> <div class="right-sec"> $'+ CGui.CanvasToolObj.price +'</div></div>';



                $('#configurationsummary').find('.custom-content').html(htmlToAdd);

                $("#configurationsummary").modal({
                    show: true,
                    backdrop: 'static',
                });
            }

            function populateSearchResults() {
                $('.noResultFound').hide();
                $('.history-frm-outer').hide();
                $('.serachPartNoForm').hide();
            }

            function closeModal() {
                //$("#configurationsummary").modal('hide');
				jQuery("#configurationsummary").modal('hide');
            }

            $('input.editconfig').on('click', function() {
                closeModal();
            })

            $("input[name=addtocart]").click(function(e){
				//Set AddtoCart status to 1 if user has added item to cart.
				$("#addcart_status").val("1");
			
                jQuery('.loaderO').show();
                jQuery(this).hide();
                e.preventDefault();
                if ($(this).attr("disabled") == 'disabled')
                    return false;

                var productLabel = '';
                for (var i=0; i<CGui.CanvasToolObj.textLabels.bottom.length; i++) {
                    if (CGui.CanvasToolObj.textLabels.bottom[i] === undefined)
                        continue;
                    productLabel += CGui.CanvasToolObj.textLabels.bottom[i].text + " ";
                }
                productLabel = $.trim(productLabel);

                var productWeight = CGui.CanvasToolObj.weight;
                var price = CGui.CanvasToolObj.price;
                var part_number = CGui.CanvasToolObj.partNumber;
                var per_product_discount = parseFloat(CGui.CanvasToolObj.user_discount);
                var jsonData = [];
                for (var i=0; i<CGui.selectedOptions.length; i++) {
                    var label = CGui.selectedOptions[i].value.configName;
                    var value =  CGui.selectedOptions[i].value.cguiComponentName;
                    if (CGui.selectedOptions[i].key == 'length')
                        value = CGui.selectedOptions[i].value.userInput.toString() + '-' + CGui.selectedOptions[i].value.unitSelected;

                    if (CGui.selectedOptions[i].key == 'breakout_options'){
                        var value = '';
                        //console.log("nisde breakout options selected options process");
                        $('li[data-config-name="breakout_options"]').find('select').each(function(key, val){
                            value += $(val).data('component-name').replace('Breakout', '');
                            value += " "+$(val).find(":selected").text();
                            value += "<br>";
                        });
                        value = $.trim(value);
                    }
                    jsonData.push({configName: label,  cguiComponentName: value});
                }

                addToCart(2164, part_number, price,jsonData, productLabel, productWeight, per_product_discount);
                return true;
            });

        });

        //jQuery('[data-toggle="popover"]').popover();

        function initSpecialOrderModal() {
            var body = "<p>This cable has special order requirements. Would you like a representative to contact you?</p>";
            body += '<button type="button" class="btn btn-primary" data-trigger="specialOrderModal" data-btn="yes"><i></i> Yes!</button>';
            body += ' <button type="button" class="btn btn-default" data-trigger="specialOrderModal" data-btn="no" data-dismiss="modal"> No, Continue Shopping</button>';
            jQuery('#specialOrderPopup').find('div.dyn-content').html(body);
            jQuery('#specialOrderPopup').modal({
                backdrop: 'static',
            });
        }

        function initSpecialOrderConfirmationModal(body) {
            body += '<button type="button" class="btn btn-default" data-dismiss="modal">Continue Shopping</button>';
            jQuery('#specialOrderPopup').find('div.dyn-content').html(body);
            jQuery('#specialOrderPopup').modal({
                backdrop: 'static',
            });
        }

        // function actionForQuote() {
        jQuery('body').on('click', '[data-trigger="specialOrderModal"]', function(e) {
            var $btnClicked = jQuery(this);
            //console.log("Clicked on specialordermodal trigger");
            // Incase of Yes
            if (jQuery(this).attr('data-btn') == 'yes') {

                $btnClicked
                    .attr('disabled', 'disabled')
                    .find('i')
                    .addClass('fa fa-circle-o-notch fa-spin');
                $btnClicked.parents('.dyn-content')
                    .find('button')
                    .attr("disabled", 'disabled');

                var jsonData = [];
                for (var i=0; i<CGui.selectedOptions.length; i++) {
                    var label = CGui.selectedOptions[i].value.configName;
                    var value =  CGui.selectedOptions[i].value.cguiComponentName;
                    if (CGui.selectedOptions[i].key == 'length')
                        value = CGui.selectedOptions[i].value.userInput.toString() + '-' + CGui.selectedOptions[i].value.unitSelected;

                    if (CGui.selectedOptions[i].key == 'breakout_options'){
                        var value = '';
                        //console.log("nisde breakout options selected options process");
                        $('li[data-config-name="breakout_options"]').find('select').each(function(key, val){
                            value += $(val).data('component-name').replace('Breakout', '');
                            value += " "+$(val).find(":selected").text();
                            value += "<br>";
                        });
                        value = $.trim(value);
                    }
                    jsonData.push({configName: label,  cguiComponentName: value});
                }

                // Sending mail with selected configs
                jQuery.ajax({
                    method: 'POST',
                    url: REQUEST_PRODUCT_QUOTE_URI,
                    dataType: "json",
                    data: {
                        productData: jsonData,
                        partNumber: CGui.CanvasToolObj.partNumber,
                    },
                    success: function(data) {
                        test=data;
                        //console.log("Success", data);
                        msgHtml = '<p>'+data.msg+'</p>';
                        initSpecialOrderConfirmationModal(msgHtml);

                        $btnClicked
                            .removeAttr('disabled')
                            .find('i')
                            .removeClass('fa fa-circle-o-notch fa-spin');

                        $btnClicked.parents('.dyn-content')
                            .find('button')
                            .removeAttr("disabled");
                    },
                    error: function(req) {
                        test=req;
                        //console.log("req=>"+ req);
                        msgHtml = '<p>Something went wrong processing your request!</p>';
                        initSpecialOrderConfirmationModal(msgHtml);

                        $btnClicked
                            .removeAttr('disabled')
                            .find('li')
                            .removeClass("fa fa-circle-o-notch fa-spin");

                        $btnClicked.parents('.dyn-content')
                            .find('button')
                            .removeAttr("disabled");
                    }
                });

            }
            else
            {
                //console.log("Not cool! dun send mail");
                // Do Something Else
            }
            // Incase of negative response
        })
        // }

		
		jQuery('body').on('click', '[data-trigger="specialOrderModalguser"]', function(e) {
            var $btnClicked = jQuery(this);                        
            // Incase of Yes
            if (jQuery(this).attr('data-btn') == 'yes') {
				
				//Set Request Status to 1 if user has already requested quote for the selected configuration.
				
				
				var gu_name = $("#contact_name").val();
				var gu_email = $("#contact_email").val();
				var gu_phno = $("#contact_phno").val();	
				if(gu_name == "" && gu_email == "" && gu_phno == "")
				{
					$("#empty_err").html("Please provide Name, Email or Phone Number.");
					$("#empty_err").show();
					$(".guyes").attr('disabled', 'disabled');
				} else if (gu_name != "" && (gu_email == "" && gu_phno == "")){
					$("#empty_err").html("Please provide Email or Phone Number.");
					$("#empty_err").show();
					$(".guyes").attr('disabled', 'disabled');
				} else if(gu_name == "" && (gu_email != "" || gu_phno != "")){
					$("#empty_err").html("Please provide Name.");
					$("#empty_err").show();
					$(".guyes").attr('disabled', 'disabled');
				}
				else {					
					$btnClicked.attr('disabled', 'disabled').find('i').addClass('fa fa-circle-o-notch fa-spin');
					$btnClicked.parents('.dyn-content').find('button').attr("disabled", 'disabled');
					
					$("#empty_err").hide();
					var jsonData = [];
					for (var i=0; i<CGui.selectedOptions.length; i++) {
						var label = CGui.selectedOptions[i].value.configName;
						var value =  CGui.selectedOptions[i].value.cguiComponentName;
						if (CGui.selectedOptions[i].key == 'length')
							value = CGui.selectedOptions[i].value.userInput.toString() + '-' + CGui.selectedOptions[i].value.unitSelected;

						if (CGui.selectedOptions[i].key == 'breakout_options'){
							var value = '';
							//console.log("nisde breakout options selected options process");
							$('li[data-config-name="breakout_options"]').find('select').each(function(key, val){
								value += $(val).data('component-name').replace('Breakout', '');
								value += " "+$(val).find(":selected").text();
								value += "<br>";
							});
							value = $.trim(value);
						}
						jsonData.push({configName: label,  cguiComponentName: value});
					}

					// Sending mail with selected configs
					jQuery.ajax({
						method: 'POST',
						url: REQUEST_PRODUCT_QUOTE_URI,
						dataType: "json",
						data: {
							productData: jsonData,
							partNumber: CGui.CanvasToolObj.partNumber,
							contact_name : gu_name,
							contact_email : gu_email,
							contact_phno : gu_phno
						},
						success: function(data) {
							test=data;
							//console.log("Success", data);
							$("#request_status").val("1");
							msgHtml = '<p>'+data.msg+'</p><button type="button" class="btn btn-default" data-dismiss="modal">Continue Shopping</button>';
							$('#specialOrderPopupGuser').modal('toggle');
							$('#specialOrderPopupGuserMsg').modal('toggle');
							$('.msg-content').html(msgHtml);
								
							$btnClicked
								.removeAttr('disabled')
								.find('i')
								.removeClass('fa fa-circle-o-notch fa-spin');

							$btnClicked.parents('.dyn-content')
								.find('button')
								.removeAttr("disabled");
						},
						error: function(req) {
							test=req;
							//console.log("req=>"+ req);
							msgHtml = '<p>Something went wrong processing your request!</p><button type="button" class="btn btn-default" data-dismiss="modal">Continue Shopping</button>';
							$('#specialOrderPopupGuser').modal('toggle');
							$('#specialOrderPopupGuserMsg').modal('toggle');
							$('.msg-content').html(msgHtml);

							$btnClicked
								.removeAttr('disabled')
								.find('li')
								.removeClass("fa fa-circle-o-notch fa-spin");

							$btnClicked.parents('.dyn-content')
								.find('button')
								.removeAttr("disabled");
						}
					});
				}
														
            }
            else
            {
                //console.log("Not cool! dun send mail");
                // Do Something Else
            }
            // Incase of negative response
        });


        jQuery('body').on('click', '[data-canvas-gui="specialOrderPopupBtn"]', function(e) {
            //checkLogin(checkLoginUrl, checkLoginFormData, initSpecialOrderModal, showLoginPopup);
            if($(this).hasClass("guserquote"))
            {
				if($("#request_status").val() == "1")
				{										
					jQuery('#specialOrderPopupGuserMsg').modal('toggle');
					$('.msg-content').html('<p>You have already requested pricing for same configuration. You can reset or change selection to get quote for a different configuration.</p><button type="button" class="btn btn-default" data-dismiss="modal">Continue Shopping</button>');
				} else {
					jQuery('#specialOrderPopupGuser').modal({
						show: false
					}); 
				}
			} else {
				initSpecialOrderModal();
			}
        });

        // jQuery('body').on('click', '#specialOrderPopup [data-btn="yes"]', function(e) {

        // })

        $(document).ready(function () {
            /* Add logo to canvas */
            var canvas = document.getElementById('canvas_config');
            var context = canvas.getContext('2d');
            var imageObj = new Image();
            var width = 152;
            var height = 97;
            var cord_x = canvas.width - width - 10;
            imageObj.onload = function () {
                context.drawImage(imageObj, cord_x - 25, 15, width, height);
            };
            imageObj.src = CANVAS_LOGO_URL;
        });

        // Function for Search By Order ID
        $(document).ready(function ($) {
            $("#btn-submit").click(function () {
                $('.noResultFound').hide();
                $('.history-frm-outer').hide();
                $('.serachPartNoForm').hide();
                $('.invalid_partno').hide();
                $.ajax({
                    url: "<?php echo plugins_url() . '/custom_cable_configuration/' ?>ajax/get_order_ajax.php",
                    data: {
                        action: 'footer_form_sbmt',
                        order_id: $("#order_name").val()
                    },
                    datatype: 'json',
                    beforeSend: function () {
                        $('#loadingDiv').show();
                    },
                    success: function (response) {
                        if (response != 3) {
                            $('.history-frm-outer').show();
                            $(".table1").remove();
                            var res = [];
                            var res = JSON.parse(response);
                            if (res) {
                                var len = res.length;
                                var txt = [];
                                $.each(res, function (i, val) {
                                    var orderId = val.order_id;
                                    txt += '<tr class="table1"><td><a href="<?php echo site_url() ?>/my-account/view-order/' + val.order_id + '">#' + val.order_id + '</a></td><td>' + val.date + '</td><td>' + val.status + '</td><td>' + '$' + val.total + '</td><td> <a  href="<?php echo site_url() ?>/my-account/view-order/' + val.order_id + '">View Order</a></td></tr>';
                                });
                                if (txt != "") {
                                    $("#table").append(txt).removeClass("hidden");
                                }
                            }
                            $('#loadingDiv').hide();
                        } else {

                            $('.history-frm-outer').hide();
                            $('#loadingDiv').hide();
                            $('.noResultFound').show();
                        }
                    }
                });
                return false;
            });
        });

        /**
         * Testing images/static
         * Connectors: L 105, R 630 H(260)
         *
         */
        var testCanvas;
        var testContext;
        // var testImageObj;
        var clearCanvas;
        var testDefaultWireColor = 'yellow';
        var wireX = 230;
        // var wireX = 230 - 50;
        var wireY = 265;
        var wireH = 10;
        var wireW = 450;

        // var mediaDir = "http://112.196.26.253/megladonmfg/wp-content/uploads/test/";

        var testInit;
        // var testDrawWire;
        var testDrawImage;
        var testDrawBoots;
        var testDrawFanouts;

        var l_boots = [];
        var r_boots = [];
        var l_connectors = [];
        var r_connectors = [];

        //Get dynamic height of the left menus
        var leftMenuHeight = $('ol#steps-customs').innerHeight();

        // document.styleSheets[0].addRule('#steps-customs > li.active ul:after','height: "275px !important;');

        var defaultAnimation = 'fadeIn';
        var animationSet = 'fadeInUp fadeInSlow fadeInUpBig fadeInRightBig fadeInLeftBig fadeIn fadeInDown fadeInDownBig animated flipInX pulse flash';

        $('select[data-animate]').on('change', function (e) {
            defaultAnimation = this.value;
        })

        $(document).ready(function ($) {

            // Init the CanvasGUI JS-Class
            CGui = new CanvasGUI();
            CGuiForLogo = new CanvasGUI();
            CGui.disableRightClickOnCanvas();

            var dataForCGuiModels = [];
            var $db_conds = document.querySelectorAll('meta[data-type=db-conds]');
            var $hardcoded_conds = document.querySelectorAll('meta[data-type=hardcoded-conds]');

            var $db_price_conds = document.querySelectorAll('meta[data-type=db-price-conds]');

            var $menusElems = document.querySelectorAll(CGui.menuContainer)[0].querySelectorAll('li>ul.sub-content');

            for (var i = 0; i < $db_conds.length; i++) {
                CGui.setDbConditions(dataset($db_conds[i]));
            }

            for (var i = 0; i < $hardcoded_conds.length; i++) {
                CGui.setStaticConditions(dataset($hardcoded_conds[i]));
            }

            for (var i = 0; i < $menusElems.length; i++) {
                var $innerLiTags = $menusElems[i].querySelectorAll('li');
                var jsonCollection = [];
                for (var j = 0; j < $innerLiTags.length; j++) {
                    jsonCollection.push(dataset($innerLiTags[j].querySelectorAll('a')[0]));
                }
                CGui.setInitialData(jsonCollection);
            }


            CGui.init();
            CGui.preloadImages();
            CGui.CanvasToolObj.initCanvas();
            CGui.updateLogoImgUrl(CANVAS_LOGO_URL);
            
        });

      $('#inputField').on('keypress', function(event) {
          if (event.keyCode === 13) {
              $(this).parents('ul.sub-content').find('button[data-canvas-menu-trigger]').eq(0).click();
          }
          return;
      })

    })(jQuery);



    </script>
    <?php
	} //Restrict User else loop ends here
}

add_shortcode('cable_configuration', 'cable_configuration_func');

//Function to update cart price as per configuration
//add_action( 'woocommerce_before_calculate_totals', 'add_custom_price' );
function add_custom_price($cart_object) {
    $custom_price = $_SESSION['config_price_sum']; // This will be your custom price
    foreach ($cart_object->cart_contents as $key => $value) {
        $value['data']->price = $custom_price;
    }
}

add_action('wp_footer', 'my_footer_scripts');

function my_footer_scripts() {
    ?>
    <script type="text/javascript">
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });

    </script>
    <?php
}

function custom_content_after_body_open_tag() {
	$page_link = get_permalink();
	if (strpos($page_link, 'configure-product') !== false) {
?>
	<div class="config-page-loader" >
		<img src="<?php echo site_url(); ?>/wp-content/uploads/ajax_loader_gray_512.gif" class="img-responsive">
	</div>
<?php
	}
}

add_action('after_body_open_tag', 'custom_content_after_body_open_tag');
?>
