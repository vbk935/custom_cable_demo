<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Settings_Additional_Field_Types' ) ) {

	/**
	 * Additional field types for WooCommerce settings pages.
	 *
	 * @author    Barn2 Media <info@barn2.co.uk>
	 * @license   GPL-3.0
	 * @copyright Barn2 Media Ltd
	 * @version   1.2.1
	 */
	class WC_Settings_Additional_Field_Types {

		public static function color_size_field( $value ) {
			$field_description = WC_Admin_Settings::get_field_description( $value );

			// Redo the description as WC runs wp_kes_post() on it which messes up any inline CSS
			if ( ! empty( $value['desc'] ) ) {
				$field_description['description'] = '<span class="description">' . $value['desc'] . '</span>';
			}

			$option_value	 = WC_Admin_Settings::get_option( $value['id'], $value['default'] );
			$color_value	 = isset( $option_value['color'] ) ? $option_value['color'] : '';
			$size_value		 = isset( $option_value['size'] ) ? $option_value['size'] : '';
			$size_min		 = isset( $value['min'] ) ? (int) $value['min'] : 0;
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $value['id'] . "[color]" ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
					<?php echo $field_description['tooltip_html']; ?>
				</th>
				<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">&lrm;
					<span class="colorpickpreview" style="background: <?php echo esc_attr( $color_value ); ?>">&nbsp;</span>
					<input
						name="<?php echo esc_attr( $value['id'] . "[color]" ); ?>"
						id="<?php echo esc_attr( $value['id'] . "[color]" ); ?>"
						type="text"
						dir="ltr"
						style="width:6.7em; display:inline-block;"
						value="<?php echo esc_attr( $color_value ); ?>"
						class="<?php echo esc_attr( $value['class'] ); ?> colorpick"
						placeholder="<?php _e( 'Color', 'woocommerce-product-table' ); ?>"
						/>&lrm;
					<div id="colorPickerDiv_<?php echo esc_attr( $value['id'] ); ?>" class="colorpickdiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div>
					<input
						name="<?php echo esc_attr( $value['id'] . "[size]" ); ?>"
						id="<?php echo esc_attr( $value['id'] . "[size]" ); ?>"
						type="number"
						style="max-width:60px;"
						value="<?php echo esc_attr( $size_value ); ?>"
						class="small-text"
						min="<?php echo esc_attr( $size_min ); ?>"
						placeholder="<?php _e( 'Size', 'woocommerce-product-table' ); ?>"
						/> <?php echo $field_description['description']; ?>
				</td>
			</tr>
			<?php
		}

		public static function help_note_field( $value ) {
			$field_description = WC_Admin_Settings::get_field_description( $value );
			?>
			<tr valign="top">
				<th scope="row" class="titledesc <?php echo esc_attr( $value['class'] ); ?>" style="padding:0;">
					<?php echo esc_html( $value['title'] ); ?>
					<?php echo $field_description['tooltip_html']; ?>
				</th>
				<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>" style="padding-top:0;padding-bottom:5px;">
					<?php echo $field_description['description']; ?>
				</td>
			</tr>
			<?php
		}

		public static function hidden_field( $value ) {
			if ( ! empty( $value['id'] ) && isset( $value['default'] ) ) :
				?>
				<input type="hidden" name="<?php echo esc_attr( $value['id'] ); ?>" value="<?php echo esc_attr( $value['default'] ); ?>" />
				<?php
			endif;
		}

		public static function multi_text_field( $value ) {
			// Get current values
			$option_values = (array) get_option( $value['id'], $value['default'] );

			if ( empty( $option_values ) ) {
				$option_values = array( '' );
			}

			$field_description = WC_Admin_Settings::get_field_description( $value );
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
					<?php echo $field_description['tooltip_html']; ?>
				</th>
				<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">

					<div class="multi-field-container">
						<?php foreach ( $option_values as $i => $option_value ) : ?>
							<?php $first_field = $i === 0; ?>
							<div class="multi-field-input">
								<input
									type="text"
									name="<?php echo esc_attr( $value['id'] ); ?>[]"
									<?php if ( $first_field ) {
										echo 'id="' . esc_attr( $value['id'] ) . '"';
									} ?>
									value="<?php echo esc_attr( $option_value ); ?>"
									class="<?php echo esc_attr( $value['class'] ); ?>"
									placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
									/>
								<span class="multi-field-actions">
									<a class="multi-field-add" data-action="add" href="#"><span class="dashicons dashicons-plus"></span></a>
										<?php if ( $i > 0 ) : ?>
										<a class="multi-field-remove" data-action="remove" href="#"><span class="dashicons dashicons-minus"></span></a>
								<?php endif; ?>
								</span>
							<?php if ( $first_field ) {
								echo $field_description['description'];
							} ?>
							</div>
			<?php endforeach; ?>
					</div>
				</td>
			</tr><?php
		}

		public static function settings_start_field( $value ) {
			$id		 = ! empty( $value['id'] ) ? sprintf( ' id="%s"', esc_attr( $value['id'] ) ) : '';
			$class	 = ! empty( $value['class'] ) ? sprintf( ' class="%s"', esc_attr( $value['class'] ) ) : '';

			echo "<div{$id}{$class}>";
		}

		public static function settings_end_field( $value ) {
			echo '</div>';
		}

	}

	// class WC_Settings_Additional_Field_Types
}