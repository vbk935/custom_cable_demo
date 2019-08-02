<strong><label for="moove_protection_data"><?php _e('Accessibility level', 'moove');?></label></strong>

<select name="moove_protection_data" id="moove_protection_data">
	<option value="0"<?php echo esc_attr( $data['moove_protection_level'] ) == -1 ? ' selected="selected"':''?>>
        <?php _e ('Post type default', 'moove');?> ( <?php echo $data['def_value_text'];?> )
    </option>
	<option value="1"<?php echo esc_attr( $data['moove_protection_level'] ) == 1 ? ' selected="selected"':''?>>
        <?php _e ('Public', 'moove');?>
    </option>
	<option value="2"<?php echo esc_attr( $data['moove_protection_level'] ) == 2? ' selected="selected"':''?>>
        <?php _e ('Free membership', 'moove');?>
    </option>
	<option value="3"<?php echo esc_attr( $data['moove_protection_level'] ) == 3? ' selected="selected"':''?>>
        <?php _e ('Premium membership', 'moove');?>
    </option>
</select>
<br />
<br />
<strong>
    <label for="moove_post_protect_data">
        <?php _e('Protection type', 'moove');?>
    </label>
</strong>
<p style="margin-top: 0;">
    <i>
        <?php _e('Default','moove'); echo ': '. esc_attr( $data['prot_type_text'] ); ?>
    </i>
</p>
<input
type="radio"
name="moove_post_protect_data"
value="protection_modal" <?php echo esc_attr( $data['protection_selected'] ) == 'protection_modal' ? ' checked="checked" ' : '' ?> >
<?php _e('Protection Modal','moove');?>

<br/>

 <input
type="radio"
name="moove_post_protect_data"
value="protection_truncated" <?php echo esc_attr( $data['protection_selected'] ) == 'protection_truncated' ? ' checked="checked" ' : '' ?> >
<?php _e('Truncated Content','moove'); ?>
