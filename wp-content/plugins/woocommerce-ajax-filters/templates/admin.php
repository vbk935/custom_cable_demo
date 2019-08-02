<div style="font-size:1.5em;padding:10px 0;line-height: 1.2em;">
    <?php _e('Widget will be removed in future please use <strong>AAPF Filters Group</strong> instead.', 'BeRocket_AJAX_domain'); ?>
    <div><?php echo sprintf(__('You can add filter to %s that has limitation', 'BeRocket_AJAX_domain'), '<a href="' . admin_url('edit.php?post_type=br_filters_group') . '">' . __('Filters group', 'BeRocket_AJAX_domain') . '</a>'); ?></div>
    <div>Or you can replace deprecated widgets with new automatically in <a href="<?php echo admin_url('admin.php?page=br-product-filters'); ?>">Plugin settings</a>->Advanced tab</div>
</div>
<div class="berocket_disable_deprecated">
<div>
    <label class="br_admin_center"><?php _e('Widget Type', 'BeRocket_AJAX_domain') ?></label>
    <select id="<?php echo $this->get_field_id( 'widget_type' ); ?>" name="<?php echo $this->get_field_name( 'widget_type' ); ?>" class="berocket_aapf_widget_admin_widget_type_select br_select_menu_left">
        <?php if ( $instance['widget_type'] == 'filter' or ! $instance['widget_type'] ) { ?>
        <option selected value="filter"><?php _e('Filter', 'BeRocket_AJAX_domain') ?></option>
        <?php } if ( $instance['widget_type'] == 'update_button' ) { ?>
        <option selected value="update_button"><?php _e('Update Products button', 'BeRocket_AJAX_domain') ?></option>
        <?php } if ( $instance['widget_type'] == 'reset_button' ) { ?>
        <option selected value="reset_button"><?php _e('Reset Products button', 'BeRocket_AJAX_domain') ?></option>
        <?php } if ( $instance['widget_type'] == 'selected_area' ) { ?>
        <option selected value="selected_area"><?php _e('Selected Filters area', 'BeRocket_AJAX_domain') ?></option>
        <?php } if ( $instance['widget_type'] == 'search_box' ) { ?>
        <option selected value="search_box"><?php _e('Search Box', 'BeRocket_AJAX_domain') ?></option>
        <?php } ?>
    </select>
</div>

<hr />

<div>
    <label class="br_admin_center" for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title', 'BeRocket_AJAX_domain') ?> </label>
    <input class="br_admin_full_size" id="<?php echo $this->get_field_id( 'title' ); ?>" type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>"/>
</div>
<?php if( empty($instance['filter_type']) ) $instance['filter_type'] = ''; ?>
<div class="berocket_aapf_admin_filter_widget_content" <?php if ( $instance['widget_type'] == 'update_button' or $instance['widget_type'] == 'reset_button' or $instance['widget_type'] == 'selected_area' or $instance['widget_type'] == 'search_box'  ) echo 'style="display: none;"'; ?>>
    <div class="br_admin_half_size_left">
        <label class="br_admin_center"><?php _e('Filter By', 'BeRocket_AJAX_domain') ?></label>
        <select id="<?php echo $this->get_field_id( 'filter_type' ); ?>" name="<?php echo $this->get_field_name( 'filter_type' ); ?>" class="berocket_aapf_widget_admin_filter_type_select br_select_menu_left">
            <?php if ( $instance['filter_type'] == 'attribute' || empty($instance['filter_type']) ) { ?>
            <option selected value="attribute"><?php _e('Attribute', 'BeRocket_AJAX_domain') ?></option>
            <?php } if ( $instance['filter_type'] == '_stock_status' ) { ?>
            <option selected value="_stock_status"><?php _e('Stock status', 'BeRocket_AJAX_domain') ?></option>
            <?php } if ( $instance['filter_type'] == 'product_cat' ) { ?>
            <option selected value="product_cat"><?php _e('Product sub-categories', 'BeRocket_AJAX_domain') ?></option>
            <?php } if ( $instance['filter_type'] == 'tag' ) { ?>
            <option selected value="tag"><?php _e('Tag', 'BeRocket_AJAX_domain') ?></option>
            <?php } if ( $instance['filter_type'] == 'custom_taxonomy' ) { ?>
            <option selected value="custom_taxonomy"><?php _e('Custom Taxonomy', 'BeRocket_AJAX_domain') ?></option>
            <?php } if ( $instance['filter_type'] == 'date' ) { ?>
            <option selected value="date"><?php _e('Date', 'BeRocket_AJAX_domain') ?></option>
            <?php } if ( $instance['filter_type'] == '_sale' ) { ?>
            <option selected value="_sale"><?php _e('Sale', 'BeRocket_AJAX_domain') ?></option>
            <?php } if ( $instance['filter_type'] == '_rating' ) { ?>
            <option selected value="_rating"><?php _e('Rating', 'BeRocket_AJAX_domain') ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="br_admin_half_size_right berocket_aapf_widget_admin_filter_type_ berocket_aapf_widget_admin_filter_type_attribute" <?php if ( $instance['filter_type'] and $instance['filter_type'] != 'attribute') echo 'style="display: none;"'; ?>>
        <label class="br_admin_center"><?php _e('Attribute', 'BeRocket_AJAX_domain') ?></label>
        <select id="<?php echo $this->get_field_id( 'attribute' ); ?>" name="<?php echo $this->get_field_name( 'attribute' ); ?>" class="berocket_aapf_widget_admin_filter_type_attribute_select br_select_menu_right">
            <option <?php if ( $instance['attribute'] == 'price' ) echo 'selected'; ?> value="price"><?php _e('Price', 'BeRocket_AJAX_domain') ?></option>
            <?php foreach ( $attributes as $k => $v ) {
                if ( $instance['attribute'] == $k ) {?>
                <option selected value="<?php echo $k ?>"><?php echo $v ?></option>
                <?php }
            } ?>
        </select>
    </div>
    <div class="br_admin_half_size_right berocket_aapf_widget_admin_filter_type_ berocket_aapf_widget_admin_filter_type_custom_taxonomy" <?php if ( $instance['filter_type'] != 'custom_taxonomy') echo 'style="display: none;"'; ?>>
        <label class="br_admin_center"><?php _e('Custom Taxonomies', 'BeRocket_AJAX_domain') ?></label>
        <select id="<?php echo $this->get_field_id( 'custom_taxonomy' ); ?>" name="<?php echo $this->get_field_name( 'custom_taxonomy' ); ?>" class="berocket_aapf_widget_admin_filter_type_custom_taxonomy_select br_select_menu_right">
            <?php foreach( $custom_taxonomies as $k => $v ){
                if ( $instance['custom_taxonomy'] == $k ) { ?>
                <option selected value="<?php echo $k ?>"><?php echo $v ?></option>
                <?php }
                } ?>
        </select>
    </div>
    <div class="br_clearfix"></div>
    <div class="br_admin_three_size_left br_type_select_block"<?php if( $instance['filter_type'] == 'date' ) echo 'style="display: none;"'; ?>>
        <label class="br_admin_center"><?php _e('Type', 'BeRocket_AJAX_domain') ?></label>
        <select id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>" class="berocket_aapf_widget_admin_type_select br_select_menu_left">
            <?php if ( $instance['filter_type'] and $instance['filter_type'] != 'attribute' or $instance['attribute'] != 'price' ) { ?>
                <option <?php if ( $instance['type'] == 'checkbox' ) echo 'selected'; ?> value="checkbox">Checkbox</option>
                <option <?php if ( $instance['type'] == 'radio' ) echo 'selected'; ?> value="radio">Radio</option>
                <option <?php if ( $instance['type'] == 'select' ) echo 'selected'; ?> value="select">Select</option>
                <?php if ( $instance['filter_type'] != '_stock_status' && $instance['filter_type'] != '_sale' && $instance['filter_type'] != '_rating' ) { ?>
                    <option <?php if ( $instance['type'] == 'color' ) echo 'selected'; ?> value="color">Color</option>
                    <option <?php if ( $instance['type'] == 'image' ) echo 'selected'; ?> value="image">Image</option>
                <?php } ?>
            <?php } ?>
            <?php if ( $instance['filter_type'] and $instance['filter_type'] != 'tag' and $instance['filter_type'] != '_stock_status' and $instance['filter_type'] != '_sale' and $instance['filter_type'] != '_rating' and $instance['filter_type'] != 'product_cat' and ( $instance['filter_type'] != 'custom_taxonomy' or ( $instance['custom_taxonomy'] != 'product_tag' and $instance['custom_taxonomy'] != 'product_cat' ) ) ) {?>
                <option <?php if ( $instance['type'] == 'slider') echo 'selected'; ?> value="slider">Slider</option>
            <?php }
            if ( $instance['filter_type'] and $instance['filter_type'] == 'attribute' and $instance['attribute'] == 'price' ) {?>
                <option <?php if ( $instance['type'] == 'ranges') echo 'selected'; ?> value="ranges">Ranges</option>
            <?php }
            if ( $instance['filter_type'] and $instance['filter_type'] == 'tag' ) { ?>
                <option <?php if ( $instance['type'] == 'tag_cloud' ) echo 'selected'; ?> value="tag_cloud">Tag cloud</option>
            <?php } ?>
        </select>
    </div>
    <div class="br_admin_three_size_left" <?php if ( ( ! $instance['filter_type'] or $instance['filter_type'] == 'attribute' ) and  $instance['attribute'] == 'price' or $instance['type'] == 'slider' or $instance['filter_type'] == 'date' or $instance['filter_type'] == '_sale' or $instance['filter_type'] == '_rating' ) echo " style='display: none;'"; ?> >
        <label class="br_admin_center"><?php _e('Operator', 'BeRocket_AJAX_domain') ?></label>
        <select id="<?php echo $this->get_field_id( 'operator' ); ?>" name="<?php echo $this->get_field_name( 'operator' ); ?>" class="berocket_aapf_widget_admin_operator_select br_select_menu_left">
            <option <?php if ( $instance['operator'] == 'AND' ) echo 'selected'; ?> value="AND">AND</option>
            <option <?php if ( $instance['operator'] == 'OR' ) echo 'selected'; ?> value="OR">OR</option>
        </select>
    </div>
    <div class="berocket_aapf_order_values_by br_admin_three_size_left" <?php if ( ! $instance['filter_type'] or $instance['filter_type'] == 'date' or $instance['filter_type'] == '_sale' or $instance['filter_type'] == '_rating' or $instance['filter_type'] == '_stock_status' or ( $instance['filter_type'] == 'attribute' and $instance['type'] == 'slider' )) echo 'style="display: none;"'; ?>>
        <label class="br_admin_center"><?php _e('Values Order', 'BeRocket_AJAX_domain') ?></label>
        <select id="<?php echo $this->get_field_id( 'order_values_by' ); ?>" name="<?php echo $this->get_field_name( 'order_values_by' ); ?>" class="berocket_aapf_order_values_by_select br_select_menu_left">
            <option value=""><?php _e('Default', 'BeRocket_AJAX_domain') ?></option>
            <?php foreach ( array( 'Alpha', 'Numeric' ) as $v ) { ?>
                <option <?php if ( $instance['order_values_by'] == $v ) echo 'selected'; ?> value="<?php _e( $v, 'BeRocket_AJAX_domain' ) ?>"><?php _e( $v, 'BeRocket_AJAX_domain' ) ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="br_clearfix"></div>
    <div class="berocket_ranges_block"<?php if ( ! $instance['filter_type'] or $instance['filter_type'] != 'attribute' or $instance['attribute'] != 'price' or $instance['type'] != 'ranges' ) echo ' style="display: none;"'; ?>>
    <?php
        if ( isset( $instance['ranges'] ) && is_array( $instance['ranges'] ) && count( $instance['ranges'] ) > 0 ) {
            foreach ( $instance['ranges'] as $range ) {
                ?><div class="berocket_ranges">
                    <input type="number" min="1" id="<?php echo $this->get_field_id( 'ranges' ); ?>" name="<?php echo $this->get_field_name( 'ranges' ); ?>[]" value="<?php echo $range; ?>">
                    <a href="#remove" class="berocket_remove_ranges"><i class="fa fa-times"></i></a>
                </div><?php
            }
        } else {
            ?><div class="berocket_ranges">
                <input type="number" min="1" id="<?php echo $this->get_field_id( 'ranges' ); ?>" name="<?php echo $this->get_field_name( 'ranges' ); ?>[]" value="1">
                <a href="#remove" class="berocket_remove_ranges"><i class="fa fa-times"></i></a>
            </div>
            <div class="berocket_ranges">
                <input type="number" min="1" id="<?php echo $this->get_field_id( 'ranges' ); ?>" name="<?php echo $this->get_field_name( 'ranges' ); ?>[]" value="50">
                <a href="#remove" class="berocket_remove_ranges"><i class="fa fa-times"></i></a>
            </div> <?php
        }
        ?><div><a href="#add" class="berocket_add_ranges" data-html='<div class="berocket_ranges"><input type="number" min="1" id="<?php echo $this->get_field_id( 'ranges' ); ?>" name="<?php echo $this->get_field_name( 'ranges' ); ?>[]" value="1"><a href="#remove" class="berocket_remove_ranges"><i class="fa fa-times"></i></a></div>'><i class="fa fa-plus"></i></a></div>
        <label>
            <input type="checkbox" name="<?php echo $this->get_field_name( 'hide_first_last_ranges' ); ?>" <?php if ( $instance['hide_first_last_ranges'] ) echo 'checked'; ?> value="1" />
            <?php _e('Hide first and last ranges without products', 'BeRocket_AJAX_domain') ?>
        </label>
    </div>
    <div <?php if ( $instance['filter_type'] != 'attribute' || $instance['attribute'] != 'price' ) echo " style='display: none;'"; ?> class="berocket_aapf_widget_admin_price_attribute" >
        <label class="br_admin_center" for="<?php echo $this->get_field_id( 'text_before_price' ); ?>"><?php _e('Text before price:', 'BeRocket_AJAX_domain') ?> </label>
        <input class="br_admin_full_size"  id="<?php echo $this->get_field_id( 'text_before_price' ); ?>" type="text" name="<?php echo $this->get_field_name( 'text_before_price' ); ?>" value="<?php echo $instance['text_before_price']; ?>"/>
        <label class="br_admin_center" for="<?php echo $this->get_field_id( 'text_after_price' ); ?>"><?php _e('after:', 'BeRocket_AJAX_domain') ?> </label>
        <input class="br_admin_full_size"  id="<?php echo $this->get_field_id( 'text_after_price' ); ?>" type="text" name="<?php echo $this->get_field_name( 'text_after_price' ); ?>" value="<?php echo $instance['text_after_price']; ?>" /><br>
        <span>%cur_symbol% will be replaced with currency symbol($), %cur_slug% will be replaced with currency code(USD)</span><br>
        <input  id="<?php echo $this->get_field_id( 'enable_slider_inputs' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'enable_slider_inputs' ); ?>" value="1"<?php if( ! empty($instance['enable_slider_inputs']) ) echo ' checked'; ?>/>
        <label for="<?php echo $this->get_field_id( 'enable_slider_inputs' ); ?>"><?php _e('Enable Slider Inputs', 'BeRocket_AJAX_domain') ?> </label>
    </div>
    <div <?php if ( $instance['filter_type'] != 'attribute' || $instance['attribute'] != 'price' ) echo " style='display: none;'"; ?> class="berocket_aapf_widget_admin_price_attribute" >
        <label for="<?php echo $this->get_field_id( 'price_values' ); ?>"><?php _e('Use custom values(comma separated):', 'BeRocket_AJAX_domain') ?> </label>
        <input class="br_admin_full_size" id="<?php echo $this->get_field_id( 'price_values' ); ?>" type="text" name="<?php echo $this->get_field_name( 'price_values' ); ?>" value="<?php echo br_get_value_from_array($instance, 'price_values'); ?>"/>
        <small><?php _e('* use numeric values only, strings will not work as expected', 'BeRocket_AJAX_domain') ?></small>
    </div>
    <div class="br_clearfix"></div>
    <div class="berocket_aapf_product_sub_cat_current" <?php if( $instance['filter_type'] != 'product_cat' ) echo 'style="display:none;"'; ?>>
        <div>
            <label>
                <input class="berocket_aapf_product_sub_cat_current_input" type="checkbox" name="<?php echo $this->get_field_name( 'parent_product_cat_current' ); ?>" <?php if ( $instance['parent_product_cat_current'] ) echo 'checked'; ?> value="1" />
                <?php _e('Use current product category to get child', 'BeRocket_AJAX_domain') ?>
            </label>
        </div>
        <div>
            <label for="<?php echo $this->get_field_id( 'depth_count' ); ?>"><?php _e('Deep level:', 'BeRocket_AJAX_domain') ?></label>
            <input id="<?php echo $this->get_field_id( 'depth_count' ); ?>" type="number" min=0 name="<?php echo $this->get_field_name( 'depth_count' ); ?>" value="<?php echo $instance['depth_count']; ?>" />
        </div>
    </div>
    <div class="berocket_aapf_product_sub_cat_div" <?php if( $instance['filter_type'] != 'product_cat' || $instance['parent_product_cat_current'] ) echo 'style="display:none;"'; ?>>
        <label><?php _e('Product Category:', 'BeRocket_AJAX_domain') ?></label>
        <ul class="berocket_aapf_advanced_settings_categories_list">
            <li>
                <?php
                echo '<input type="radio" name="' . ( $this->get_field_name( 'parent_product_cat' ) ) . '" ' .
                     ( empty($instance['parent_product_cat']) ? 'checked' : '' ) . ' value="" ' .
                     'class="berocket_aapf_widget_admin_height_input" />';
                ?>
                <?php _e('None', 'BeRocket_AJAX_domain') ?>
            </li>
            <?php
            $selected_category = false;
            foreach ( $categories as $category ) {
                if ( (int) $instance['parent_product_cat'] == (int) $category->term_id ) {
                    $selected_category = true;
                }
                if( $selected_category ) {
                    echo '<li>';
                    echo '<input type="radio" name="' . ( $this->get_field_name( 'parent_product_cat' ) ) . '" ' .
                         'checked value="' . ( $category->term_id ).'" ' .
                         'class="berocket_aapf_widget_admin_height_input" />' . ( $category->name );
                    echo '</li>';
                    $selected_category = false;
                }
            }
            ?>
        </ul>
    </div>
    <br />
    <div class="br_clearfix"></div>
</div>
<div class="berocket_disable_deprecated_hide"></div>
</div>
<script>
    if( typeof(br_widget_set) == 'function' )
        br_widget_set();
</script>
