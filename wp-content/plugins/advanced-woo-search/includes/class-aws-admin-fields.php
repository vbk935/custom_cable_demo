<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'AWS_Admin_Fields' ) ) :

    /**
     * Class for plugin admin ajax hooks
     */
    class AWS_Admin_Fields {

        /**
         * @var AWS_Admin_Fields The array of options that is need to be generated
         */
        private $options_array;

        /**
         * @var AWS_Admin_Fields Current plugin instance options
         */
        private $plugin_options;

        /*
         * Constructor
         */
        public function __construct( $options ) {

            $this->options_array = $options;
            $this->plugin_options = get_option( 'aws_settings' );

            $this->generate_fields();

        }

        /*
         * Generate options fields
         */
        private function generate_fields() {

            if ( empty( $this->options_array ) ) {
                return;
            }

            $plugin_options = $this->plugin_options;

            echo '<table class="form-table">';
            echo '<tbody>';

            foreach ( $this->options_array as $k => $value ) {

                switch ( $value['type'] ) {

                    case 'text': ?>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html( $value['name'] ); ?></th>
                            <td>
                                <input type="text" name="<?php echo esc_attr( $value['id'] ); ?>" class="regular-text" value="<?php echo isset( $plugin_options[ $value['id'] ] ) ? esc_attr( stripslashes( $plugin_options[ $value['id'] ] ) ) : ''; ?>">
                                <br><span class="description"><?php echo $value['desc']; ?></span>
                            </td>
                        </tr>
                        <?php break;

                    case 'image': ?>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html( $value['name'] ); ?></th>
                            <td>
                                <input type="text" name="<?php echo esc_attr( $value['id'] ); ?>" class="regular-text" value="<?php echo esc_attr( stripslashes( $plugin_options[ $value['id'] ] ) ); ?>">
                                <br><span class="description"><?php echo $value['desc']; ?></span>
                                <img style="display: block;max-width: 100px;margin-top: 20px;" src="<?php echo esc_url( $plugin_options[ $value['id'] ] ); ?>">
                            </td>
                        </tr>
                        <?php break;

                    case 'number': ?>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html( $value['name'] ); ?></th>
                            <td>
                                <input type="number" name="<?php echo esc_attr( $value['id'] ); ?>" class="regular-text" value="<?php echo esc_attr( stripslashes( $plugin_options[ $value['id'] ] ) ); ?>">
                                <br><span class="description"><?php echo $value['desc']; ?></span>
                            </td>
                        </tr>
                        <?php break;

                    case 'textarea': ?>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html( $value['name'] ); ?></th>
                            <td>
                                <textarea id="<?php echo esc_attr( $value['id'] ); ?>" name="<?php echo esc_attr( $value['id'] ); ?>" cols="65" rows="4"><?php print stripslashes( $plugin_options[ $value['id'] ] ); ?></textarea>
                                <br><span class="description"><?php echo $value['desc']; ?></span>
                            </td>
                        </tr>
                        <?php break;

                    case 'checkbox': ?>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html( $value['name'] ); ?></th>
                            <td>
                                <?php $checkbox_options = $plugin_options[ $value['id'] ]; ?>
                                <?php foreach ( $value['choices'] as $val => $label ) { ?>
                                    <input type="checkbox" name="<?php echo esc_attr( $value['id'] . '[' . $val . ']' ); ?>" id="<?php echo esc_attr( $value['id'] . '_' . $val ); ?>" value="1" <?php checked( $checkbox_options[$val], '1' ); ?>> <label for="<?php echo esc_attr( $value['id'] . '_' . $val ); ?>"><?php echo esc_html( $label ); ?></label><br>
                                <?php } ?>
                                <br><span class="description"><?php echo $value['desc']; ?></span>
                            </td>
                        </tr>
                        <?php break;

                    case 'radio': ?>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html( $value['name'] ); ?></th>
                            <td>
                                <?php foreach ( $value['choices'] as $val => $label ) { ?>
                                    <input class="radio" type="radio" name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $value['id'].$val ); ?>" value="<?php echo esc_attr( $val ); ?>" <?php checked( $plugin_options[ $value['id'] ], $val ); ?>> <label for="<?php echo esc_attr( $value['id'].$val ); ?>"><?php echo esc_html( $label ); ?></label><br>
                                <?php } ?>
                                <br><span class="description"><?php echo $value['desc']; ?></span>
                            </td>
                        </tr>
                        <?php break;

                    case 'select': ?>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html( $value['name'] ); ?></th>
                            <td>
                                <select name="<?php echo esc_attr( $value['id'] ); ?>">
                                    <?php foreach ( $value['choices'] as $val => $label ) { ?>
                                        <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $plugin_options[ $value['id'] ], $val ); ?>><?php echo esc_html( $label ); ?></option>
                                    <?php } ?>
                                </select>
                                <br><span class="description"><?php echo $value['desc']; ?></span>
                            </td>
                        </tr>
                        <?php break;

                    case 'select_advanced': ?>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html( $value['name'] ); ?></th>
                            <td>
                                <select name="<?php echo esc_attr( $value['id'].'[]' ); ?>" multiple class="chosen-select">
                                    <?php $values = $plugin_options[ $value['id'] ]; ?>
                                    <?php foreach ( $value['choices'] as $val => $label ) {  ?>
                                        <?php $selected = ( is_array( $values ) && in_array( $val, $values ) ) ? ' selected="selected" ' : ''; ?>
                                        <option value="<?php echo esc_attr( $val ); ?>"<?php echo $selected; ?>><?php echo esc_html( $label ); ?></option>
                                    <?php } ?>
                                </select>
                                <br><span class="description"><?php echo $value['desc']; ?></span>

                                <?php if ( $value['sub_option'] ): ?>
                                    <?php $sub_options = $value['sub_option']; ?>
                                    <br><br>
                                    <p>
                                        <label for="<?php echo esc_attr( $sub_options['id'] ); ?>">
                                            <input type="checkbox" value="1" id="<?php echo esc_attr( $sub_options['id'] ); ?>" name="<?php echo esc_attr( $sub_options['id'] ); ?>" <?php checked( $plugin_options[ $sub_options['id'] ], '1' ); ?>>
                                            <?php echo esc_html( $sub_options['desc'] ); ?>
                                        </label>
                                    </p>
                                <?php endif; ?>

                            </td>
                        </tr>
                        <?php break;

                    case 'radio-image': ?>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html( $value['name'] ); ?></th>
                            <td>
                                <ul class="img-select">
                                    <?php foreach ( $value['choices'] as $val => $img ) { ?>
                                        <li class="option">
                                            <input class="radio" type="radio" name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $value['id'].$val ); ?>" value="<?php echo esc_attr( $val ); ?>" <?php checked( $plugin_options[ $value['id'] ], $val ); ?>>
                                            <span class="ico" style="background: url('<?php echo esc_url( AWS_URL . '/assets/img/' . $img ); ?>') no-repeat 50% 50%;"></span>
                                        </li>
                                    <?php } ?>
                                </ul>
                                <br><span class="description"><?php echo $value['desc']; ?></span>
                            </td>
                        </tr>
                        <?php break;

                    case 'sortable': ?>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html( $value['name'] ); ?></th>
                            <td>

                                <script>
                                    jQuery(document).ready(function() {

                                        jQuery( "#sti-sortable1, #sti-sortable2" ).sortable({
                                            connectWith: ".connectedSortable",
                                            placeholder: "highlight",
                                            update: function(event, ui){
                                                var serviceList = '';
                                                jQuery("#sti-sortable2 li").each(function(){

                                                    serviceList = serviceList + ',' + jQuery(this).attr('id');

                                                });
                                                var serviceListOut = serviceList.substring(1);
                                                jQuery('#<?php echo esc_attr( $value['id'] ); ?>').attr('value', serviceListOut);
                                            }
                                        }).disableSelection();

                                    });
                                </script>

                                <span class="description"><?php echo $value['desc']; ?></span><br><br>

                                <?php
                                $all_buttons = $value['choices'];
                                $active_buttons = explode( ',', $plugin_options[ $value['id'] ] );
                                $inactive_buttons = array_diff($all_buttons, $active_buttons);
                                ?>

                                <div class="sortable-container">

                                    <div class="sortable-title">
                                        <?php esc_html_e( 'Active sources', 'aws' ) ?><br>
                                        <?php esc_html_e( 'Change order by drag&drop', 'aws' ) ?>
                                    </div>

                                    <ul id="sti-sortable2" class="sti-sortable enabled connectedSortable">
                                        <?php
                                        if ( count( $active_buttons ) > 0 ) {
                                            foreach ($active_buttons as $button) {
                                                if ( ! $button ) continue;
                                                echo '<li id="' . esc_attr( $button ) . '" class="sti-btn sti-' . esc_attr( $button ) . '-btn">' . $button . '</li>';
                                            }
                                        }
                                        ?>
                                    </ul>

                                </div>

                                <div class="sortable-container">

                                    <div class="sortable-title">
                                        <?php esc_html_e( 'Deactivated sources', 'aws' ) ?><br>
                                        <?php esc_html_e( 'Excluded from search results', 'aws' ) ?>
                                    </div>

                                    <ul id="sti-sortable1" class="sti-sortable disabled connectedSortable">
                                        <?php
                                        if ( count( $inactive_buttons ) > 0 ) {
                                            foreach ($inactive_buttons as $button) {
                                                echo '<li id="' . esc_attr( $button ) . '" class="sti-btn sti-' . esc_attr( $button ) . '-btn">' . $button . '</li>';
                                            }
                                        }
                                        ?>
                                    </ul>

                                </div>

                                <input type="hidden" id="<?php echo esc_attr( $value['id'] ); ?>" name="<?php echo esc_attr( $value['id'] ); ?>" value="<?php echo esc_attr( $plugin_options[ $value['id'] ] ); ?>" />

                            </td>
                        </tr>
                        <?php break;

                    case 'heading': ?>
                        <tr valign="top">
                            <th scope="row"><h3 class="aws-heading"><?php echo esc_html( $value['name'] ); ?></h3></th>
                        </tr>
                        <?php break;

                }

            }

            echo '</tbody>';
            echo '</table>';

            echo '<p class="submit"><input name="Submit" type="submit" class="button-primary" value="' . esc_attr__( 'Save Changes', 'aws' ) . '" /></p>';

        }



    }

endif;
