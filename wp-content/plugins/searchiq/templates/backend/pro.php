<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ($this->isProPack) {

    if (isset($_POST)
            && !empty($_POST['siqFacetSubmit'])) {
        $facets = array();
        if (isset($_POST['siqFacetType']) && is_array($_POST['siqFacetType'])
                && is_array($_POST['siqFacetLabel'])
                && is_array($_POST['siqFacetField'])
                && is_array($_POST['siqFacetDateFormat'])) {
            for ($i = 0; $i < count($_POST['siqFacetType']); ++$i) {
                $facet = array(
                    "postType" => sanitize_text_field($_POST['siqFacetPostType'][$i]),
                    "type" => sanitize_text_field($_POST['siqFacetType'][$i]),
                    "label" => sanitize_text_field($_POST['siqFacetLabel'][$i]),
                    "field" => sanitize_text_field($_POST['siqFacetField'][$i])
                );
                if ($facet["type"] == "date") {
                    $facet["dateFormat"] = stripslashes(sanitize_text_field($_POST['siqFacetDateFormat'][$i]));
                }
                array_push($facets, $facet);
            }
        }

        $facetsNoticeStatus = get_option(self::FACETS_NOTICE_KEY,0);
        $facetsCurrentValue = $this->pluginSettings["siq_facets"];
        $this->saveFacets($facets);
        $facetsNewValue     = $this->pluginSettings["siq_facets"];
        $preFacetsEnabledAc = $this->getAutocompleteFacetsEnabled();
        $preFacetsEnabledRp = $this->getResultPageFacetsEnabled();

        $this->setAutocompleteFacetsEnabled(isset($_POST['siq_enable_facets_autocomplete']) && $_POST['siq_enable_facets_autocomplete'] == "1");
        $this->setResultPageFacetsEnabled(isset($_POST['siq_enable_facets_result_page']) && $_POST['siq_enable_facets_result_page'] == "1");

        $postFacetsEnabledAc = $this->getAutocompleteFacetsEnabled();
        $postFacetsEnabledRp = $this->getResultPageFacetsEnabled();

        if( ( ($facetsCurrentValue !== $facetsNewValue ) || $facetsNoticeStatus == -1)  && ($postFacetsEnabledAc == 1 || $postFacetsEnabledRp == 1) && !empty($facetsNewValue)){
            update_option(self::FACETS_NOTICE_KEY, 1);
            apply_filters('_siq_check_facets_error',1);
        }else if(($facetsCurrentValue !== $facetsNewValue) || ($facetsNoticeStatus == 1 && $postFacetsEnabledAc == 0 && $postFacetsEnabledRp == 0)){
                update_option(self::FACETS_NOTICE_KEY, -1);
                apply_filters('_siq_check_facets_error',0);
        }

        $this->_siq_sync_settings();
    }else{
        $getNoticeStatus = get_option(self::FACETS_NOTICE_KEY, 0);
        if($getNoticeStatus > 0) {
            apply_filters('_siq_check_facets_error',1);
        }
    }

    $settings = $this->getPluginSettings();

    $facets = isset($settings["siq_facets"]) ? $settings["siq_facets"] : array();
    $excludeFields = array(
        "externalId", "title", "url", "body", "excerpt"
    );
    $postTypes = $this->getAllpostTypes();
    ?>

    <script>
        var SIQ_postTypes = <?php echo json_encode(array_values($postTypes));?>;
    </script>

<div class="wsplugin">
    <h2>Facets</h2>
    <div class="wpAdminHeading">Here you can add facets to display in autocomplete and on result page</div>
    <form action="<?php echo admin_url( 'admin.php?page=dwsearch&tab=tab-6');?>" method="post">
        <div class="section section-0">
            <div class="data">
                <label>Enable facets in autocomplete</label>
                <input type="checkbox" value="1" name="siq_enable_facets_autocomplete" <?php echo $this->getAutocompleteFacetsEnabled() ? "checked" : "";?> />
            </div>
            <div class="data">
                <label>Enable facets on result page</label>
                <input type="checkbox" value="1" name="siq_enable_facets_result_page" <?php echo $this->getResultPageFacetsEnabled() ? "checked" : "";?> />
            </div>

            <div id="siq-facet-form">
                <?php
                if (is_array($facets) && count($facets) > 0) {
                    for($i = 0; $i < count($facets); ++$i) {
                        $facet = $facets[$i];
                        ?>
                        <div id="siq-facet-item-<?php echo $i;?>" class="siq-facet-item">
                            <table>
                                <tr>
                                    <td><label>Label</label></td>
                                    <td>
                                        <input type="text" name="siqFacetLabel[]" value="<?php echo $facet["label"];?>" required />
                                    </td>
                                    <td><label>Post Type</label></td>
                                    <td>
                                        <select name="siqFacetPostType[]" onchange="SIQ_buildFacetFieldSelectBox(<?php echo $i;?>, this);">
                                            <option value="_siq_all_posts">All types</option>
                                            <?php
                                            foreach($postTypes as $postType) {
                                                ?><option value="<?php echo $postType;?>" <?php echo $facet['postType'] == $postType ? "selected" : "";?>><?php echo $postType;?></option><?php
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td><label>Type</label></td>
                                    <td>
                                        <select name="siqFacetType[]" required onchange="SIQ_changeFacetType(<?php echo $i;?>);">
                                            <option value=""></option>
                                            <option value="string" <?php echo $facet['type'] == "string" ? "selected" : "";?>>String</option>
                                            <option value="number" <?php echo $facet['type'] == "number" ? "selected" : "";?>>Number</option>
                                            <option value="date" <?php echo $facet['type'] == "date" ? "selected" : "";?>>Date</option>
                                        </select>
                                    </td>
                                    <td><label>Field</label></td>
                                    <td>
                                        <select name="siqFacetField[]" required onchange="SIQ_changeFacetType(<?php echo $i;?>);">
                                            <option value=""></option>
                                            <?php echo $this->getDocumentFieldsOptionList($facet['field'], $excludeFields, $facet['postType']); ?>
                                        </select>
                                    </td>
                                    <td class="siqDateFormat <?php echo ($facet["type"] != "date" || $facet["field"] == "timestamp") ? "hidden" : "";?>"><label>Date format</label></td>
                                    <td class="siqDateFormat <?php echo ($facet["type"] != "date" || $facet["field"] == "timestamp") ? "hidden" : "";?>">
                                        <input type="text" name="siqFacetDateFormat[]"
                                               value="<?php echo (isset($facet["dateFormat"]) && strlen($facet["dateFormat"]) > 0) ? $facet["dateFormat"] : "Y-m-d\\TH:i:s\\.\\0\\0\\0";?>"
                                               <?php echo $facet["type"] == "date" ? "required" : "";?> />
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="8">
                                        <a href="javascript:SIQ_moveFacetUp(<?php echo $i;?>);" class="siq-facet-move-up <?php echo $i == 0 ? "hidden" : "";?>">Move up</a>
                                        <a href="javascript:SIQ_moveFacetDown(<?php echo $i;?>);" class="siq-facet-move-down <?php echo $i + 1 == count($facets) ? "hidden" : "" ;?>">Move down</a>
                                        <a href="javascript:SIQ_removeFacet(<?php echo $i;?>);" class="siq-facet-remove">Remove</a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <?php
                    }
                } else {
                    echo "<div class='siq-no-facets'>No any facet created. Click &laquo;Add facet&raquo; button.</div>";
                }
                ?>
            </div>

            <div>
                <?php if (count($facets) < 5) { ?><input type="button" name="btnAddFacet" id="btnAddFacet" value="Add facet" class="btn" onclick="SIQ_addNewFacet();return false;"><br/><?php } ?>
                <input type="submit" name="siqFacetSubmit" class="btn" value="Save"/>
            </div>
        </div>
        <div class="section section-1 section-facets-resync">
            <h2>Please wait data synchronization is in progress</h2>
            <div class="data">
                <div class="progress-wrap progress" data-progress-percent="25">
                    <div class="progress-bar progress"></div>
                </div>
                <div class="progressText"></div>
            </div>
        </div>
    </form>
</div>

<?php } ?>