<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$settings 								= $this->getPluginSettings();
$code 										= $settings['auth_code'];
$engine 								= stripslashes($settings['engine_name']);
$engineCode						= $settings['engine_code'];
$indexed 								= $settings['index_posts'];
$imageCustomField     	= $settings['image_custom_field'];
$numPostsIndexed 		= $settings['num_indexed_posts'];
$showEngineButton 		= "";
if(!empty($settings['siq_engine_not_found'])){
	$showEngineButton = "style='display:block;'";
}

$classVerify 	= ($code!=""  && empty($showEngineButton)) ? "done" : "not-done";
$classIndexed 	= ($indexed!=""  && empty($showEngineButton)) ? "indexed open" : "not-indexed";
$engineCreated	= ($engine!="" && empty($showEngineButton)) ? "engine open" : "no-engine";

$textStep3 		= "Step 2: Submit posts for synchronization";
$textMessageStep3 = "Click \"Full Synchronize Posts\"</b> button to submit all posts for synchronization.";
$classReindex	= "";
if($indexed && empty($showEngineButton)){
	$textStep3 = "Synchronization Settings";
	$classReindex =  "reindex";
	$textMessageStep3 = "Click <b>\"Full Resynchronize Posts\"</b> button to submit posts for re-synchronization or else click <b>\"Delta Resynchronize Posts\"</b> button to submit only updated posts for re-synchronization";
}

$textIndexing	= ($indexed !="" && (int)$indexed > 0 && empty($showEngineButton)) ? "Full Resynchronize Posts" : "Full Synchronize Posts";

?>

<div class="wsplugin">
	<h2>SearchIQ: Configuration <a class="helpSign userGuide" target="_blank" style="text-decoration: none" href="<?php echo $this->userGuideLink;?>"><img style="vertical-align:bottom" src="<?php echo SIQ_BASE_URL;?>/assets/<?php echo SIQ_PLUGIN_VERSION;?>/images/help/help-icon.png"> User Guide</a></h2>
	<?php if($code == ""){ ?>
		<div class="dwAdminHeading">Get your API key from <a target="_blank" href="<?php echo $this->administerPanelLink; ?>">SearchIQ</a> account.</div>
	<?php } ?>
	<?php if($code != "" || $engine !="" || $indexed != ""){ ?>
		<div class="section section-top">
			<h2>Plugin Settings</h2>
			<div class="data">
				<ul>
					<li class="vcode">
						<label><?php echo $this->labels["verificationCode"];?></label> <span class="info"><?php echo $code; ?></span>
					</li>
					<li class="ename">
						<label><?php echo $this->labels["engineName"];?></label> <span class="info"><?php echo $engine; ?></span>
					</li>
					<li class="iposts">
						<label><?php echo $this->labels["postsIndexed"];?></label> <span class="info"><?php echo $numPostsIndexed; ?></span>
					</li>
				</ul>


			</div>
		</div>
	<?php } ?>
	<?php if($code == ""){ ?>
		<div class="section section-1 <?php echo $classVerify;?>">
			<h2>Step 1: Plugin Authentication</h2>
			<div class="data">

				<label>Enter API key:</label>
				<input type="text" class="textbox" name="siq_activation" id="siq_activation" value="<?php echo $code;?>" />
				<input type="button" name="btnSubmitcode" id="btnSubmitcode" value="Submit" class="btn" />
			</div>
		</div>
	<?php } ?>
	
	<div class="section section-2 <?php echo $classVerify;?> <?php echo $engineCreated;?>" <?php  echo $showEngineButton;?>>
		<h2>Create a Search Engine</h2>
		<div class="data">
			<h3>Click on the button to create search engine.<span class="engineExists"></span></h3>
			<input type="button" name="btnSubmitEngine" id="btnSubmitEngine" value="Create Search engine" class="btn" />
		</div>
	</div>
	
	<div class="section section-3 <?php echo $classVerify;?> <?php echo $classIndexed;?> <?php echo $engineCreated;?> <?php echo $classReindex;?>">
		<h2><?php echo $textStep3;?></h2>
		<div class="data">
			<?php echo $this->getFilterAndPostTypeHTML(); ?>
			<?php 
				if( is_null($settings["index_posts"]) || empty($settings["index_posts"]) || $settings["index_posts"] == 0 || !empty($settings['siq_engine_not_found']) ){ 
				echo $this->getResyncBlock($textIndexing, $textMessageStep3, ($settings["index_posts"] && empty($settings['siq_engine_not_found'])) ? 1: 0 );
				} 
			?>
		</div>
	</div>
	<?php if($settings["index_posts"] >= 1 &&  empty($showEngineButton)){ ?>
		<div class="section section-3-1">
			<div class="data">
				<?php echo $this->getResyncBlock($textIndexing, $textMessageStep3, $settings["index_posts"]); ?>
			</div>
		</div>
	<?php } ?>

	<div class="section section-4 <?php echo $classVerify;?> <?php echo $classIndexed;?> <?php echo $engineCreated;?>">
		<h2><?php echo "Regenerate thumbnails (optional)";?></h2>
		<?php if(!$this->enableThumbnailService){ ?>
			<h3>To optimize the thumbnails click the button below</h3>
			<div class="data dataPaddingBottom">
				<h5>Choose if you want to crop or resize the thumbnails</h5>
				<ul class="options inline">
				<?php
					foreach($this->siqCropResizeOptions as $k => $v){
					$checked = ($settings["siq_crop_resize_thumb"] == $k) ? "checked='checked'" : "";
					?>
					<li><input <?php echo $checked;?> type="radio" name="selectCropResize" value="<?php echo $k;?>" id="selectCropResize_<?php echo $k;?>" /><label for="selectCropResize_<?php echo $k;?>"><?php echo $v;?></label></li>
				<?php } ?>
				</ul>
			</div>
			<div class="data">
				<input type="button" name="btnGenerateThumbnails" id="btnGenerateThumbnails" value="Regenerate Thumbnails" class="btn <?php echo $classReindex;?>" />

				<div class="progress-wrap progress" data-progress-percent="25">
					<div class="progress-bar progress"></div>
				</div>
				<div class="progressText"></div>
			</div>
		<?php 
			} else { 
				echo $this->thumbServiceDisabledMsg; 
			} 
		?>
	</div>

	<div class="section section-5 <?php echo $classVerify;?> <?php echo $classIndexed;?> <?php echo $engineCreated;?>" <?php  echo $showEngineButton;?>>
		<input type="button" name="btnResetConfig" id="btnResetConfig" value="Reset Configuration" class="btn" />
		<h3>Resets the configuration but indexed data will remain on SearchIQ server. In order to delete the indexed data from SearchIQ server login to the dashboard and delete the search engine.</h3>
		<div class="data"></div>
	</div>
</div>

