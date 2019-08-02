<?php

/**
 * Settings Page
 * Handles to settings
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

?>
<div class="wrap">

    <h2><?php _e( 'CSV Import - Export', 'esbcie' ); ?></h2>
    
    <?php
    
    $current = 'product';
    $tabs = esb_cie_post_type_tabs();

    if( isset( $_GET['cie_import'] ) || isset( $_GET['cie_update'] ) || isset( $_GET['cie_ignore'] ) ) {

        $item_name    = !empty( $_GET['cie_type'] ) ? ( $_GET['cie_type'] == 'term' ? __( 'categories', 'esbcie' ) : __( 'posts', 'esbcie' ) ) : __( 'items', 'esbcie' );
        $total_import = !empty( $_GET['cie_import'] ) ? $_GET['cie_import'] : 0;
        $total_update = !empty( $_GET['cie_update'] ) ? $_GET['cie_update'] : 0;
        $total_ignore = !empty( $_GET['cie_ignore'] ) ? $_GET['cie_ignore'] : 0;

        echo "<div class='updated below-h2'>
        <p><strong>{$total_import}</strong> {$item_name} was successfully imported. <strong>{$total_update}</strong> {$item_name} updated. <strong>{$total_ignore}</strong> {$item_name} ignored.</p>
    </div>";
}

echo '<h2 class="nav-tab-wrapper">';
foreach( $tabs as $tab => $name ) {
    $class = ( $tab == $current ) ? ' nav-tab-active ' : '';
    echo "<a class='nav-tab$class' href='#$tab'>  Import - Export Configration </a>";
}
echo '</h2>';

?>

<!-- beginning of the settings meta box -->
<div id="esb_cie_settings" class="post-box-container">

    <div class="esb-cie-content">

        <?php foreach( $tabs as $tab => $name ) { ?>

        <div class="esb-cie-tab-content" id="<?php echo $tab ?>">


            <?php
            $taxonomies = esb_cie_get_all_taxonomies( $tab );
            if( !empty( $taxonomies ) ) {
                foreach( $taxonomies as $taxonomy_key => $taxonomy ) {
                 if(	($taxonomy->labels->menu_name == 'Configurations') || ($taxonomy->labels->menu_name == 'Groups')){
                    $menu_title = !empty( $taxonomy->labels ) && !empty( $taxonomy->labels->menu_name ) ? $taxonomy->labels->menu_name : $taxonomy->label;

                    ?>
                    <!-- Taxonomy View Start -->
                    <div class="metabox-holder">	
                        <div class="meta-box-sortables ui-sortable">
                            <div id="esb_cie_<?php echo $tab ?>_<?php echo $taxonomy_key ?>" class="postbox">

                                <!-- settings box title -->
                                <h3 class="hndle">
                                    <span style='vertical-align: top;'><?php echo $menu_title; ?></span>
                                </h3>

                                <div class="inside">



                                    <?php
                                    $all_options = esb_cie_get_all_term_fields();
                                    if( !empty( $all_options ) ) {
                                        ?>
                                        <form method="post">

                                            <?php
                                            $extra_options = array(
                                                array(
                                                   'key'       => 'unit_name',
                                                   'label'     => __( 'Unit Name', 'esbcie' ),
                                                   'notice'    => ''
                                                   ),
                                                array(
                                                 'key'       => 'presenter_id',
                                                 'label'     => __( 'Taxonomy Type', 'esbcie' ),
                                                 'notice'    => ''
                                                 ),
                                                array(
                                                 'key'       => 'hide_config',
                                                 'label'     => __( 'Hide Config', 'esbcie' ),
                                                 'notice'    => ''
                                                 ),
                                                array(
                                                 'key'       => 'is_unit_type',
                                                 'label'     => __( 'Is Unit Type', 'esbcie' ),
                                                 'notice'    => ''
                                                 ),
                                                array(
                                                 'key'       => 'config_price',
                                                 'label'     => __( 'Config Price', 'esbcie' ),
                                                 'notice'    => ''
                                                 ),
                                                array(
                                                 'key'       => 'image',
                                                 'label'     => __( 'Canvas Image URL', 'esbcie' ),
                                                   'notice'    => ''
                                                   ),
                                                array(
                                                   'key'       => 'part_image',
                                                   'label'     => __( 'Part Image', 'esbcie' ),
                                                   'notice'    => ''
                                                   )
                                                );
                                            $group_extra_options = array(
                                             array(
                                               'key'       => 'presenter_id',
                                               'label'     => __( 'Configuration Type', 'esbcie' ),
                                               'notice'    => ''
                                               ),
                                             array(
                                               'key'       => 'show_configuration',
                                               'label'     => __( ' Configuration', 'esbcie' ),
                                               'notice'    => ''
                                               )
                                             );
                                            if( isset( $taxonomy->hierarchical ) && $taxonomy->hierarchical == '1' ) {

                                                $all_options[] = array(
                                                 'key'    => 'parent',
                                                 'label'  => __( 'Parent Category', 'esbcie' ),
                                                 'notice' => __( 'Parent category name (slug)', 'esbcie' )
                                                 );
                                            }
                                            foreach ( $all_options as $opt => $option_data ) {

                                                $row_class = ( $opt % 2 == 0 ) ? ' alternate ' : '';
                                                $key    = isset( $option_data['key'] ) ? $option_data['key'] : '';
                                                $label  = isset( $option_data['label'] ) ? $option_data['label'] : '';
                                                $notice = isset( $option_data['notice'] ) ? $option_data['notice'] : '';
                                                ?>
                                                <input type="hidden" id="esb_cie_<?php echo $key ?>_<?php echo $tab ?>" name="esb_cie_column_name[]" value="<?php echo $key ?>" checked="checked" />
                                                <?php } 
                                                if(	($taxonomy->labels->menu_name == 'Configurations') || ($taxonomy->labels->menu_name != 'Groups')){
                                                  foreach ( $extra_options as $opt => $extra_option_data ) {

                                                    $row_class = ( $opt % 2 == 0 ) ? ' alternate ' : '';
                                                    $key    = isset( $extra_option_data['key'] ) ? $extra_option_data['key'] : '';
                                                    $label  = isset( $extra_option_data['label'] ) ? $extra_option_data['label'] : '';
                                                    $notice = isset( $extra_option_data['notice'] ) ? $extra_option_data['notice'] : '';
                                                    ?>
                                                    <input type="hidden" id="esb_cie_<?php echo $key ?>_<?php echo $tab ?>" name="esb_cie_column_name[]" value="<?php echo $key ?>" checked="checked" />
                                                    <?php }
                                                } 
                                                if(	($taxonomy->labels->menu_name == 'Groups') || ($taxonomy->labels->menu_name != 'Configurations')){
                                                  foreach ( $group_extra_options as $opt => $group_extra_opt ) {

                                                    $row_class = ( $opt % 2 == 0 ) ? ' alternate ' : '';
                                                    $key    = isset( $group_extra_opt['key'] ) ? $group_extra_opt['key'] : '';
                                                    $label  = isset( $group_extra_opt['label'] ) ? $group_extra_opt['label'] : '';
                                                    $notice = isset( $group_extra_opt['notice'] ) ? $group_extra_opt['notice'] : '';
                                                    ?>
                                                    <input type="hidden" id="esb_cie_<?php echo $key ?>_<?php echo $tab ?>" name="esb_cie_column_name[]" value="<?php echo $key ?>" checked="checked" />

                                                    <?php }
                                                } ?>
                                                <p>
                                                    <input type="hidden" name="esb_cie_csv_file_name" value="<?php echo $menu_title ?>" />
                                                    <input type="hidden" name="esb_cie_export_taxonomy" value="<?php echo $taxonomy_key ?>" />
                                                    <input type="submit" name="esb_cie_export_terms_csv" class="button-secondary" value="<?php _e( 'Export/Download CSV', 'esbcie' ) ?>" />
                                                    <!--<input type="submit" name="esb_cie_download_sample_csv" class="button-secondary" value="<?php /*_e( 'Download Sample CSV', 'esbcie' ) */?>" />-->
                                                </p>
                                            </form>
                                            <?php } ?>
                                            <!-- CSV description End -->

                                            <!-- Import from file Start -->
                                            <table class="form-table esb-cie-form-table">

                                                <tr>
                                                    <td colspan="2" valign="top" scope="row">
                                                        <strong><?php _e( 'Import from file', 'esbcie' ); ?></strong>
                                                    </td>
                                                </tr>

                                            </table>

                                            <form method="post" enctype="multipart/form-data">
                                                <input type="hidden" id="esb_cie_term_new" name="esb_cie_import_choice" value="new" checked="checked" />

                                                <p>
                                                    <input type="hidden" name="esb_cie_csv_taxonomy_name" value="<?php echo $taxonomy_key ?>" />
                                                    <input type="file" name="esb_cie_import_file" />
                                                    <input type="submit" name="esb_cie_import_term_csv" class="button-secondary" value="<?php _e( 'Import From CSV', 'esbcie' ) ?>" />
                                                </p>
                                            </form>
                                            <!-- Import from file End -->

                                        </div><!-- .inside -->

                                    </div><!-- #settings -->
                                </div><!-- .meta-box-sortables ui-sortable -->
                            </div><!-- .metabox-holder -->
                            <!-- Taxonomy View End -->

                            <?php
                        }
                    }
                }
                ?>

            </div><!-- .esb-cie-tab-content -->
            
            <?php } ?>

        </div><!-- .esb-cie-content -->

    </div><!-- #esb_cie_settings -->
</div>