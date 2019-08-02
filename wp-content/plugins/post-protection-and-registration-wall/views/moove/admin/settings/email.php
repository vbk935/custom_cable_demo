<?php if ( $data['current_type'] == 'input' ) : ?>
    <input name="moove_protection-email[<?php echo $data['parent_title'].$data['field_title']; ?>]" type="text" id="moove_protection-email<?php echo $data['parent_title'].$data['field_title']; ?>" value="<?php echo $data['options'][$data['parent_title'].$data['field_title']]; ?>" class="regular-text">
<?php else: ?>

    <?php
        $editor_id = 'moove_protection-email['.$data['parent_title'].$data['field_title'].']';
        $content = $data['options'][$data['parent_title'].$data['field_title']];
        $settings = array(
            'media_buttons' => false,
            'editor_height' => 200,
        );
        wp_editor( $content, $editor_id, $settings );
    ?>

<?php endif; ?>
