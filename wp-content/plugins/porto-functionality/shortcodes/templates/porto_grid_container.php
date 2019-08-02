<?php
$output = $grid_size = $gutter_size = $max_width = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'grid_size'          => '0',
			'gutter_size'        => '2%',
			'max_width'          => '767px',
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
		),
		$atts
	)
);

wp_enqueue_script( 'isotope' );

if ( ! $gutter_size ) {
	$gutter_size = '0%';
}
$valid_characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
$rand_escaped     = '';
$length           = 32;
for ( $n = 1; $n < $length; $n++ ) {
	$whichcharacter = rand( 0, strlen( $valid_characters ) - 1 );
	$rand_escaped  .= $valid_characters{$whichcharacter};
}

$el_class = porto_shortcode_extract_class( $el_class );

$output = '<div class="porto-grid-container"';
if ( $animation_type ) {
	$output .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
	if ( $animation_delay ) {
		$output .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$output .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
}
$output .= '>';
preg_match_all( '/\[porto_grid_item\s[^]]*width="([^]"]*)"[^]]*\]/', $content, $matches );

$column_width     = 0;
$column_width_str = '';
if ( isset( $matches[1] ) && is_array( $matches[1] ) ) {
	foreach ( $matches[1] as $index => $item ) {
		$w = preg_replace( '/[^.0-9]/', '', $item );
		if ( $column_width > (float) $w || 0 == $index ) {
			$column_width     = (float) $w;
			$column_width_str = $item;
		}
	}
}

if ( $column_width > 0 ) {
	$replace_count = 1;
	$content       = str_replace( '[porto_grid_item width="' . esc_attr( $column_width_str ) . '"', '[porto_grid_item width="' . esc_attr( $column_width_str ) . '" column_class="true"', $content, $replace_count );
}

$iso_options = array();
if ( ! ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) ) {
	$iso_options['itemSelector'] = '.porto-grid-item';
} else {
	$iso_options['itemSelector'] = '.vc_porto_grid_item';
}

$iso_options['layoutMode']      = 'masonry';
$iso_options['masonry']         = array( 'columnWidth' => '.iso-column-class' );
$iso_options['animationEngine'] = 'best-available';
$iso_options['resizable']       = false;

$output .= '<div id="grid-' . $rand_escaped . '" class="' . esc_attr( $el_class ) . ' wpb_content_element clearfix" data-plugin-masonry data-plugin-options=\'' . json_encode( $iso_options ) . '\'>';
$output .= do_shortcode( $content );
$output .= '</div>';

$gutter_size_number  = preg_replace( '/[^.0-9]/', '', $gutter_size );
$gutter_size         = str_replace( $gutter_size_number, (float) ( $gutter_size_number / 2 ), $gutter_size );
$gutter_size_escaped = esc_html( $gutter_size );

$output .= '<style>
				#grid-' . $rand_escaped . ' .porto-grid-item {
					padding: ' . $gutter_size_escaped . ';
				}

				#grid-' . $rand_escaped . ' {
					margin: -' . $gutter_size_escaped . ' -' . $gutter_size_escaped . ' ' . $gutter_size_escaped . ';
				}

				@media (max-width:' . esc_html( $max_width ) . ') {
					#grid-' . $rand_escaped . ' {
						height: auto !important;
					}
					#grid-' . $rand_escaped . ' .porto-grid-item:first-child {
						margin-top: 0;
					}
					#grid-' . $rand_escaped . ' .porto-grid-item {

						width: 100% !important;
						position: static !important;
						float: none;

					}
				}';
if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
	$output .= '.porto-grid-container .porto-grid-item { float: none; } .porto-grid-container .vc_porto_grid_item { float: left; }';
	$output .= '.porto-grid-container .porto-grid-item .wpb_single_image { margin-bottom: 0; }';
}
$output .= '</style>';

$output .= '</div>';

echo porto_filter_output( $output );

if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
	?>
	<script>
		var column_class<?php echo $rand_escaped; ?> = 100;
		$('.porto-grid-container .vc_porto_grid_item').each(function() {
			if ($(this).children('.porto-grid-item').length) {
				var widthAttr = $(this).children('.porto-grid-item').attr('style'),
					width = widthAttr.replace('width:', '').replace('%', '').replace(' ', '');
				try {
					width = parseInt(width, 10);
				} catch(e) {
					width = 0;
				}
				if (column_class<?php echo $rand_escaped; ?> > width) {
					column_class<?php echo $rand_escaped; ?> = width;
				}
				$(this).children('.porto-grid-item').css('width', '');
				$(this).attr('style', widthAttr);
			}
		});
		$('.porto-grid-container .vc_porto_grid_item').each(function() {
			if ($(this).children('.porto-grid-item').length) {
				var widthAttr = $(this).attr('style'),
					width = widthAttr.replace('width:', '').replace('%', '').replace(' ', '');
				try {
					width = parseInt(width, 10);
				} catch(e) {
					width = 0;
				}
				if (width === column_class<?php echo $rand_escaped; ?>) {
					$(this).addClass('iso-column-class');
				}
			}
		});
	</script>
	<?php
}
