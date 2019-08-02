<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	$settings 		= $this->getPluginSettings();
	$code 			= $settings['auth_code'];
	$engine 		= stripslashes($settings['engine_name']);
	$engineCode		= $settings['engine_code'];
	$indexed 		= $settings['index_posts'];
	
	$tab1Selected = isset($_GET['tab']) ? ( ($_GET['tab']=='tab-1')? 'selected': 'notselected') : "selected";
	$tab2Selected = isset($_GET['tab']) ? ( ($_GET['tab']=='tab-2')? 'selected': 'notselected') : "notselected";
	$tab3Selected = isset($_GET['tab']) ? ( ($_GET['tab']=='tab-3')? 'selected': 'notselected') : "notselected";
	$tab4Selected = isset($_GET['tab']) ? ( ($_GET['tab']=='tab-4')? 'selected': 'notselected') : "notselected";
    $tab5Selected = isset($_GET['tab']) ? ( ($_GET['tab'] == 'tab-5') ? 'selected' : 'notselected') : 'notselected';
	$tab6Selected = !!$this->pluginSettings["facets_enabled"] && isset($_GET['tab']) && $_GET['tab'] == "tab-6" ? "selected" : "notselected";
    if (isset($_GET['tab']) && $_GET['tab'] == 'tab-6' && $tab6Selected != "selected") {
        $tab1Selected = "selected";
    }

	$tab2Selected .= ($code == "" && $engineCode=="" && ($indexed == "" || $indexed == 0)) ? " hide": "";
	$tab3Selected .= ($code == "" && $engineCode=="" && ($indexed == "" || $indexed == 0)) ? " hide": "";
	$tab4Selected .= ($code == "" && $engineCode=="" && ($indexed == "" || $indexed == 0)) ? " hide": "";
    $tab5Selected .= ($code == "" && $engineCode=="" && ($indexed == "" || $indexed == 0)) ? " hide": "";
    $tab6Selected .= (!$this->pluginSettings["facets_enabled"] || empty($code) || empty($engineCode) || empty($indexed)) ? " hide" : "";
?>
<div class="backendTabbed" id="searchIqBackend">
	<div class="tabsHeading">
		<ul>
			<li id="tab-1" class="<?php echo $tab1Selected;?>">
				<a href="<?php echo admin_url( 'admin.php?page=dwsearch&tab=tab-1'); ?>">Configuration</a>
			</li>
			<li id="<?php echo empty($engineCode) ? "" : "tab-2";?>" class="<?php echo $tab2Selected;?>">
				<a href="<?php echo admin_url( 'admin.php?page=dwsearch&tab=tab-2'); ?>">Options</a>
			</li>
			<li id="<?php echo empty($engineCode) ? "" : "tab-3";?>" class="<?php echo $tab3Selected;?>">
				<a href="<?php echo admin_url( 'admin.php?page=dwsearch&tab=tab-3'); ?>">Results Page</a>
			</li>
			<li id="<?php echo empty($engineCode) ? "" : "tab-4";?>" class="<?php echo $tab4Selected;?>">
				<a href="<?php echo admin_url( 'admin.php?page=dwsearch&tab=tab-4'); ?>">Autocomplete</a>
			</li>
			<li id="<?php echo empty($engineCode) ? "" : "tab-5";?>" class="<?php echo $tab5Selected;?>">
				<a href="<?php echo admin_url( 'admin.php?page=dwsearch&tab=tab-5'); ?>">Mobile</a>
			</li>
            <?php
            if (!!$this->pluginSettings["facets_enabled"]) {
            	echo $this->facetsTabHtml("", $tab6Selected, (empty($engineCode) ? "" : "tab-6"));
            }
            ?>
		</ul>
	</div>
	<div class="tabsContent showLoader">
		<div class="tab tab-1 <?php echo $tab1Selected;?>">
			<?php
                if(!isset($_GET['tab']) || (isset($_GET['tab']) && ($_GET['tab']=='tab-1' || $_GET['tab']=='tab-6'))) {
                    include_once(SIQ_BASE_PATH . '/templates/backend/config.php');
                }
            ?>
		</div>
		<div class="tab tab-2 <?php echo $tab2Selected;?>">
			<?php
                if(isset($_GET['tab']) && $_GET['tab']=='tab-2') {
                    include_once(SIQ_BASE_PATH . '/templates/backend/optionsPage.php');
                }
            ?>
		</div>
		<div class="tab tab-3 <?php echo $tab3Selected;?>">
			<?php
                if(isset($_GET['tab']) && $_GET['tab']=='tab-3') {
                    include_once(SIQ_BASE_PATH.'/templates/backend/appearance.php');
                }
            ?>
		</div>
		<div class="tab tab-4 <?php echo $tab4Selected;?>">
			<?php
                if(isset($_GET['tab']) && $_GET['tab']=='tab-4') {
                    include_once(SIQ_BASE_PATH.'/templates/backend/appearance-autocomplete.php');
                }
            ?>
		</div>
        <div class="tab tab-5 <?php echo $tab5Selected;?>">
			<?php
                if(isset($_GET['tab']) && $_GET['tab']=='tab-5') {
                    include_once(SIQ_BASE_PATH.'/templates/backend/appearance-mobile.php');
                }
            ?>
		</div>
        <?php
        if (!!$this->pluginSettings["facets_enabled"]) {
            ?>
            <div class="tab tab-6 <?php echo $tab6Selected;?>">
                <?php
                    if(isset($_GET['tab']) && $_GET['tab']=='tab-6') {
                        include_once(SIQ_BASE_PATH . '/templates/backend/facets.php');
                    }
                ?>
            </div>
            <?php
        }
        ?>
	</div>
	<script type="text/javascript">

	</script>
</div>
<script type="text/javascript">
    var adminUrl  		= window.location.href;
    var adminPort 		= '<?php echo $_SERVER['SERVER_PORT']; ?>';
    var adminAjax 		= '<?php echo admin_url( 'admin-ajax.php' );?>';
    var adminBaseUrl 	= '<?php echo admin_url( 'admin.php?page=dwsearch' );?>';
    if(adminUrl.indexOf(adminPort) > -1 && adminAjax.indexOf(adminPort) == -1){
        adminAjax 		= adminAjax.replace(/\/wp-admin/g, ':'+adminPort+'/wp-admin');
        adminBaseUrl 	= adminBaseUrl.replace(/\/wp-admin/g, ':'+adminPort+'/wp-admin');
    }
    var siq_admin_nonce = "<?php  echo wp_create_nonce( $this->adminNonceString ); ?>";
    var searchEngineText = 'You already have search engines created for this domain. ';
    $jQu	= jQuery;
    $jQu(document).on('click', '.clearColor', function(){
        $jQu(this).prev('.color').val("").attr("style", "").attr("value", "");
    });
</script>
