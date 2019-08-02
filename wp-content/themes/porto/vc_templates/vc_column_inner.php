<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $el_class
 * @var $width
 * @var $css
 * @var $offset
 *
 * Extra Params
 * @var $is_sticky
 * @var $sticky_container_selector
 * @var $sticky_min_width
 * @var $sticky_top
 * @var $sticky_bottom
 * @var $sticky_active_class
 * @var $animation_type
 * @var $animation_duration
 * @var $animation_delay
 *
 * @var $content - shortcode content
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Column_Inner
 */
$output = '';
$atts   = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$width = wpb_translateColumnWidthToSpan( $width );
$width = vc_column_offset_class_merge( $offset, $width );

$el_class = $this->getExtraClass( $el_class );

$css_classes = array(
	$el_class,
	'vc_column_container',
	$width,
	vc_shortcode_custom_css_class( $css ),
);

if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
	$css_classes[] = 'wpb_column';
}

$wrapper_attributes = array();

$css_class            = preg_replace( '/\s+/', ' ', apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, implode( ' ', array_filter( $css_classes ) ), $this->settings['base'], $atts ) );
$wrapper_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';

if ( $animation_type ) {
	$wrapper_attributes[] = 'data-appear-animation="' . esc_attr( $animation_type ) . '"';
	if ( $animation_delay ) {
		$wrapper_attributes[] = 'data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$wrapper_attributes[] = 'data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
}

$output .= '<div ' . implode( ' ', $wrapper_attributes ) . '>';

if ( $is_sticky ) {
	$options                      = array();
	$options['containerSelector'] = $sticky_container_selector;
	$options['minWidth']          = (int) $sticky_min_width;
	$options['padding']['top']    = (int) $sticky_top;
	$options['padding']['bottom'] = (int) $sticky_bottom;
	$options['activeClass']       = $sticky_active_class;
	$options                      = json_encode( $options );

	$output .= '<div class="vc_column-inner" data-plugin-sticky data-plugin-options="' . esc_attr( $options ) . '">';
}

$output .= '<div class="wpb_wrapper' . ( $is_sticky ? '' : ' vc_column-inner' ) . '">';
$output .= wpb_js_remove_wpautop( $content );
$output .= '</div>';

if ( $is_sticky ) {
	$output .= '</div>';
}

$output .= '</div>';

echo porto_filter_output( $output );
