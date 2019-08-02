<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!empty( $_POST ) && isset($_POST['btnSubmitMobileStyleOptions']) && check_admin_referer($this->updateMobileNonce)){
    
		$siq_styling						= 	$_POST['styling'];
		$siq_styling_						= array();
                $offsetStyling = array();
		if(count($siq_styling) > 0){
			foreach($siq_styling as $k => $v){
                            if (isset($this->mobileStyling[$k]) && $this->mobileStyling[$k]['default'] != sanitize_text_field($v) && $k !== "searchIconTopOffset" && $k !== "searchIconTopOffsetUnit") {
				$siq_styling_[$k] = sanitize_text_field($v);
                            } else if ($k === "searchIconTopOffset" || $k === "searchIconTopOffsetUnit") {
                                $offsetStyling[$k] = sanitize_text_field($v);
                            }
			}
		}
		$error=array();
                if (isset($offsetStyling['searchIconTopOffset']) && $offsetStyling['searchIconTopOffset']
                        && isset($offsetStyling['searchIconTopOffsetUnit']) && $offsetStyling['searchIconTopOffsetUnit']) {
                    if ($offsetStyling['searchIconTopOffset'] !== $this->mobileStyling['searchIconTopOffset']['default']
                            || $offsetStyling['searchIconTopOffsetUnit'] !== $this->mobileStyling['searchIconTopOffsetUnit']['default']) {
                        $siq_styling_['searchIconTopOffset'] = $offsetStyling['searchIconTopOffset'];
                        $siq_styling_['searchIconTopOffsetUnit'] = $offsetStyling['searchIconTopOffsetUnit'];
                    }
                }

                $faviconMode = isset($_POST['mobile_favicon_mode']) ? (int) $_POST['mobile_favicon_mode'] : 0;
        $this->setMobileFaviconMode($faviconMode);

        switch ($faviconMode) {
            case 0:
                $siq_styling_['barFavicon'] = "";
                break;
            case 1:
                $siq_styling_['barFavicon'] = $this->getDefaultFaviconURL();
                break;
        }

		if(isset($siq_styling_) && count($siq_styling_) > 0){	
			update_option($this->pluginOptions['mobile_style'], $siq_styling_);
		}

		if(isset($_POST['search_icon_selector']) && !empty($_POST['search_icon_selector'])){	
			update_option($this->pluginOptions['search_icon_selector'], $_POST['search_icon_selector']);
		}else{
			delete_option($this->pluginOptions['search_icon_selector']);
		}
                
                $this->setMobileEnabled(isset($_POST['mobile_enabled']) && $_POST['mobile_enabled'] === "yes");
                $this->setMobileIconEnabled(isset($_POST['mobile_icon_enabled']) && $_POST['mobile_icon_enabled'] === "yes");
                $this->setMobileFloatBarEnabled(isset($_POST['mobile_float_bar_enabled']) && $_POST['mobile_float_bar_enabled'] === "yes");

                $this->_siq_sync_settings();
	
}

$settings 					= $this->getPluginSettings();
$stylingVar					= $settings['mobile_style'];
$styling					= $this->getMobileStyling($stylingVar);


 ?>
<div class="wsplugin">
 <h2>SearchIQ: Mobile <a class="helpSign userGuide" target="_blank" style="text-decoration: none" href="<?php echo $this->userGuideLink;?>"><img style="vertical-align:bottom" src="<?php echo SIQ_BASE_URL;?>/assets/<?php echo SIQ_PLUGIN_VERSION;?>/images/help/help-icon.png"> User Guide</a></h2>
 <div class="dwAdminHeading">You can change appearance for mobile here.</div>
 <form method="POST" novalidate action="<?php echo admin_url( 'admin.php?page=dwsearch&tab=tab-5'); ?>" onsubmit="return SIQ_validateMobileSettingsForm();" class="custom_page_options" name="custom_options">
	<div class="section section-1">
		<h2>Mobile</h2>
        <div class="data">
            <label>Enable</label>
            <input type="checkbox" name="mobile_enabled" value="yes" <?php echo $this->isMobileEnabled() ? "checked='checked'" : "";?>/>
        </div>

        <div class="data">
            <label>Search Bar Placeholder</label>
            <input type="text" value="<?php echo $styling['barPlaceholder'];?>" class="long-input" name="styling[barPlaceholder]"/>
        </div>
        <div class="data">
            <label>Search Result Header Text</label>
            <input type="text" value="<?php echo $styling['SearchHeadTxt'];?>" class="long-input" name="styling[SearchHeadTxt]"/>
        </div>
        <div class="data">
            <label>Search Result Stat Text</label>
            <input type="text" value="<?php echo $styling['SearchResultStatTxt'];?>" class="long-input" name="styling[SearchResultStatTxt]"/>
        </div>
        <div class="data">
            <label>Search Result Loading text</label>
            <input type="text" value="<?php echo $styling['SearchLoadingTxt'];?>" class="long-input" name="styling[SearchLoadingTxt]"/>
        </div>
        <div class="data">
            <label>Search Bar Placeholder Text Color</label>
            <input type="text" value="<?php echo $styling['barPlaceholderTextColor'];?>" name="styling[barPlaceholderTextColor]" class="color {required:false}"/>
            <div class="clearColor"><span></span></div>
        </div>
        <div class="data">
            <label>Search Bar Favicon</label>
            <select class="auto-width" id="mobile_favicon_mode" name="mobile_favicon_mode" onchange="var custom = document.getElementById('mobileSearchBarFavicon'); this.value == '2' ? custom.setAttribute('type', 'text') : custom.setAttribute('type', 'hidden');">
                <option value="0" <?php echo $this->pluginSettings['mobile_favicon_mode'] == 0 ? "selected" : "";?>>Disable</option>
                <option value="1" <?php echo $this->pluginSettings['mobile_favicon_mode'] == 1 ? "selected" : "";?>>Auto-detect</option>
                <option value="2" <?php echo $this->pluginSettings['mobile_favicon_mode'] == 2 ? "selected" : "";?>>Custom URL</option>
            </select>
            <input class="long-input" id="mobileSearchBarFavicon" type="<?php echo $this->pluginSettings['mobile_favicon_mode'] == 2 ? "text" : "hidden";?>" name="styling[barFavicon]" value="<?php echo $styling['barFavicon'];?>"/>
        </div>
        <div class="data">
			<label>Search Bar Background</label>
			<input type="text" value="<?php echo $styling['barBgColor']; ?>" name="styling[barBgColor]" class="color {required:false}"/>
			<div class="clearColor"><span></span></div>
		</div>
        <div class="data">
            <label>Search Input Background</label>
            <input type="text" value="<?php echo $styling['barInputBgColor'];?>" name="styling[barInputBgColor]" class="color {required:false}"/>
            <div class="clearColor"><span></span></div>
        </div>
        <div class="data">
            <label>Search Input Text Color</label>
            <input type="text" value="<?php echo $styling['barInputTextColor'];?>" name="styling[barInputTextColor]" class="color {required:false}"/>
            <div class="clearColor"><span></span></div>
        </div>
        <div class="data">
            <label>Result Title Font Size</label>
            <?php echo $this->getFontSizeBox('styling[resultTitleFontSize]', $styling['resultTitleFontSize'], 10, 60); ?>
        </div>
        <div class="data">
            <label>Enable Float Search Bar</label>
            <input type="checkbox" name="mobile_float_bar_enabled" value="yes" <?php echo $this->isMobileFloatBarEnabled() ? "checked='checked'" : "";?>/>
        </div>

        <div class="data">
            <label>Enable Float Search Icon</label>
            <input type="checkbox" name="mobile_icon_enabled" value="yes" <?php echo $this->isMobileIconEnabled() ? "checked='checked'" : "";?>/>
        </div>
		
		<div class="data">
            <label>Search icon selector</label>
            <input type="text" name="search_icon_selector" value="<?php echo $settings['search_icon_selector']; ?>" />
        </div>
		
        <div class="data">
            <label>Search icon box background</label>
            <input value="<?php echo $styling['searchIconBoxBg'] ? $styling['searchIconBoxBg'] : $this->mobileStyling['searchIconBoxBg']['default']; ?>" name="styling[searchIconBoxBg]" class="color {required:false}"/>
            <div class="clearColor"><span></span></div>
        </div>
        <div class="data">
            <label>Search icon color</label>
            <input value="<?php echo $styling['searchIconColor'] ? $styling['searchIconColor'] : $this->mobileStyling['searchIconColor']['default']; ?>" name="styling[searchIconColor]" class="color {required:false}"/>
            <div class="clearColor"><span></span></div>
        </div>
        <div class="data">
            <label>Position offset from top</label>
            <input value="<?php echo $styling['searchIconTopOffset'];?>" type="number" name="styling[searchIconTopOffset]" onblur="if(!/(^(\d+\.?)(\d+)?$|^(\d+)?(\.\d+)$)/.test(this.value)){this.value=this.value.replace(/[^\d\.]*/, '');}"/>
            <select name="styling[searchIconTopOffsetUnit]" style="max-width: 50px;top: -3px;position: relative;">
                <option <?php echo $styling['searchIconTopOffset'] && $styling['searchIconTopOffsetUnit'] == "%" || !$styling['searchIconTopOffset'] && $this->mobileStyling['searchIconTopOffsetUnit']['default'] == "%" ? "selected" : "";?> value="%">%</option>
                <option <?php echo $styling['searchIconTopOffset'] && $styling['searchIconTopOffsetUnit'] == "px" || !$styling['searchIconTopOffset'] && $this->mobileStyling['searchIconTopOffsetUnit']['default'] == "px" ? "selected" : "";?> value="px">px</option>
            </select>
        </div>
	</div>
	<div class="section submit">
            <div class="data">
                <input type="submit" name="btnSubmitMobileStyleOptions" id="btnSubmitMobileStyleOptions" value="Submit" class="btn">
                <input type="button" name="btnClearMobileStyle" id="btnClearMobileStyle" value="Reset Style to default" class="btn">
                <?php wp_nonce_field( $this->updateMobileNonce );?>
            </div>
	</div>
 </form>
</div>
