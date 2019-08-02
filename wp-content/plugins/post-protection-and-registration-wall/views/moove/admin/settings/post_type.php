    <select name="moove_post_protect[<?php echo $data['post_type'];?>]" id="<?php echo $data['post_type'];?>">

    	<option value="1"<?php echo isset( $data['options'][ $data['post_type'] ] ) && intval( $data['options'][ $data['post_type'] ] ) == 1 ? ' selected="selected"':''?>>
            <?php _e ('Public', 'moove');?>
        </option>

    	<option value="2"<?php echo isset( $data['options'][ $data['post_type'] ] ) && intval( $data['options'][ $data['post_type'] ] )  == 2 ? ' selected="selected"':''?>>
            <?php _e ('Free membership', 'moove');?>
        </option>

    	<option value="3"<?php echo isset( $data['options'][ $data['post_type'] ] ) && intval( $data['options'][ $data['post_type'] ] ) == 3? ' selected="selected"':''?>>
            <?php _e ('Premium membership', 'moove');?>
        </option>

    </select>

    <br>
    <h4><?php __( 'Protection type','moove' ); ?></h4>

    <input
    type="radio"
    name="moove_post_protect[<?php echo $data['post_type'].'_protection_type'; ?>]"
    id="moove_post_protect[<?php echo $data['post_type'].'_protection_type'; ?>]"
    <?php echo isset( $data['options'][ $data['post_type'] . '_protection_type']) && esc_attr( $data['options'][ $data['post_type' ] . '_protection_type'] ) == 'protection_modal'? ' checked="checked"':''?>
    value="protection_modal">
    <?php _e('Protection Modal','moove');?>

    <br/>
     <input
    type="radio"
    name="moove_post_protect[<?php echo $data['post_type'] . '_protection_type'; ?>]"
    id="moove_post_protect[<?php echo $data['post_type'] . '_protection_type'; ?>]"
    <?php echo isset( $data['options'][ $data['post_type'] . '_protection_type'] ) && $data['options'][ $data['post_type'] . '_protection_type'] == 'protection_truncated'? ' checked="checked"':''?>
    value="protection_truncated">
    <?php _e('Truncated Content with Register button','moove'); ?>



