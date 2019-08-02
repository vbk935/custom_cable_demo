<?php
if ( count( $data['fields'] ) ) :
    foreach ( $data['fields'] as $field ) { ?>
        <label for="moove_protection-validation[<?php echo $data['parent_title'].'_'.$data['field_title'].'_'.$field; ?>]">
            <?php echo ucfirst( str_replace( '-', ' ', $field ) ); ?>
        </label>
        <br />
        <input
        name="moove_protection-validation[<?php echo $data['parent_title'].'_'.$data['field_title'].'_'.$field; ?>]"
        type="text" id="moove_protection-validation<?php echo $data['parent_title'].'_'.$data['field_title'].'_'.$field; ?>"
        value="<?php echo $data['options'][$data['parent_title'].'_'.$data['field_title'].'_'.$field]; ?>"
        class="regular-text">
        <br />
        <br />
    <?php }
endif; ?>