<tr>
	<td><label><?php esc_html_e( 'Calendar Localization', 'formidable-pro' ) ?></label></td>
	<td>
		<select name="field_options[locale_<?php echo absint( $field['id'] ) ?>]">
			<?php
			foreach ( $locales as $locale_key => $locale ) {
				$selected = ( isset( $field['locale'] ) && $field['locale'] == $locale_key ) ? ' selected="selected"' : '';
				?>
				<option value="<?php echo esc_attr( $locale_key ) ?>"<?php echo $selected; ?>><?php echo esc_html( $locale ) ?></option>
			<?php } ?>
		</select>
	</td>
</tr>
<tr>
	<td>
		<label><?php esc_html_e( 'Year Range', 'formidable-pro' ) ?></label>
		<span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php esc_attr_e( 'Use four digit years or +/- years to make it dynamic. For example, use -5 for the start year and +5 for the end year.', 'formidable-pro' ) ?>" ></span>
	</td>
	<td>
		<span><?php esc_html_e( 'Start Year', 'formidable-pro' ) ?></span>
		<input type="text" name="field_options[start_year_<?php echo absint( $field['id'] ) ?>]" value="<?php echo esc_attr( isset( $field['start_year'] ) ? $field['start_year'] : '' ); ?>" size="4"/>

		<span><?php esc_html_e( 'End Year', 'formidable-pro' ) ?></span>
		<input type="text" name="field_options[end_year_<?php echo absint( $field['id'] ) ?>]" value="<?php echo esc_attr( isset( $field['end_year'] ) ? $field['end_year'] : '' ); ?>" size="4"/>
	</td>
</tr>

<?php
if ( ! function_exists( 'frm_dates_autoloader' ) && is_callable( 'FrmAddonsController::install_link' ) ) {
	$install_data = '';
	$class        = ' frm_noallow';
	$upgrading    = FrmAddonsController::install_link( 'dates' );
	if ( isset( $upgrading['url'] ) ) {
		$install_data = json_encode( $upgrading );
		$class        = '';
	}
	?>
	<tr>
		<td>
			<?php esc_attr_e( 'Blackout Dates, Inline Datepicker, and Dynamic Start and End Dates', 'formidable-pro' ); ?>
		</td>
		<td>
			<a href="javascript:void(0)" class="frm_show_upgrade<?php echo esc_attr( $class ); ?>" data-upgrade="<?php esc_attr_e( 'Extra Datepicker options', 'formidable' ); ?>" data-medium="datepicker-options" data-oneclick="<?php echo esc_attr( $install_data ); ?>">
				<i class="frm_add_tag frm_icon_font"></i>
				<?php esc_html_e( 'More Datepicker Options', 'formidable' ); ?>
			</a>
		</td>
	</tr>
	<?php
}
?>
