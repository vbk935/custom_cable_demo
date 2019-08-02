<?php

$output = $subtitle = $image_url = $image_id = $heading = $shadow = $heading_color = $subtitle_color = $animation_type = $animation_duration = $animation_delay = $el_class = '';

extract(
	shortcode_atts(
		array(
			'subtitle'           => '',
			'image_url'          => '',
			'image_id'           => '',
			'heading'            => '',
			'shadow'             => '',
			'heading_color'      => '',
			'subtitle_color'     => '',
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

if ( ! $image_url && $image_id ) {
	$image_url = wp_get_attachment_url( $image_id );
}

$image_url = str_replace( array( 'http:', 'https:' ), '', $image_url );

global $porto_schedule_timeline_count;

if ( ! isset( $porto_schedule_timeline_count ) ) {
	$output         .= '<div class="timeline-balloon p-b-lg m-b-sm ' . esc_attr( $el_class ) . '">';
		$output     .= '<div class="balloon-cell balloon-time">';
			$output .= '<span' . ( $subtitle_color ? ' style="color:' . esc_attr( $subtitle_color ) . ' !important"' : '' ) . ' class="time-text text-color-dark font-weight-bold font-size-sm">' . esc_html( $subtitle ) . '</span>';
			$output .= '<div class="time-dot background-color-light"></div>';
		$output     .= '</div>';
		$output     .= '<div class="balloon-cell"';
	if ( $animation_type ) {
		$output .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
		if ( $animation_delay ) {
			$output .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
		}
		if ( $animation_duration && 1000 != $animation_duration ) {
			$output .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
		}
	}
		$output     .= '>';
			$output .= '<div class="balloon-content ';
	if ( $shadow ) {
		$output .= ' balloon-shadow ';
	}
			$output     .= 'background-color-light">';
				$output .= '<span class="balloon-arrow background-color-light"></span>';
	if ( $image_url ) {
		$output     .= '<div class="balloon-photo">';
			$output .= '<img src="' . esc_url( $image_url ) . '" class="img-responsive img-circle" alt="' . esc_attr( $heading ) . '">';
		$output     .= '</div>';
	}
				$output .= '<div class="balloon-description">';
	if ( $heading ) {
		$output .= '<h5' . ( $heading_color ? ' style="color:' . esc_attr( $heading_color ) . ' !important"' : '' ) . ' class="text-color-dark font-weight-bold p-t-xs m-none">' . esc_html( $heading ) . '</h5>';
	}

	if ( $content ) {
		$output .= '<p class="font-weight-normal m-t-sm m-b-xs">' . do_shortcode( $content ) . '</p>';
	}
				$output .= '</div>';
			$output     .= '</div>';
		$output         .= '</div>';
	$output             .= '</div>';

} else {

	$porto_schedule_timeline_count++;

	if ( $subtitle ) {
		$output .= '<div class="timeline-date"><h3' . ( $subtitle_color ? ' style="color:' . esc_attr( $subtitle_color ) . '"' : '' ) . ' class="time-text font-weight-bold font-size-sm">' . esc_html( $subtitle ) . '</h3></div>';
	}
	$attrs = '';
	if ( $animation_type ) {
		$attrs .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
		if ( $animation_delay ) {
			$attrs .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
		}
		if ( $animation_duration && 1000 != $animation_duration ) {
			$attrs .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
		}
	}

	if ( 1 == $porto_schedule_timeline_count % 2 ) {
		$position_class = ' left';
	} else {
		$position_class = ' right';
	}
	$output         .= '<article class="timeline-box' . $position_class . '"' . $attrs . '>';
		$output     .= '<div>';
			$output .= '<img src="' . esc_url( $image_url ) . '" class="img-responsive" alt="' . esc_attr( $heading ) . '">';
			$output .= '<h4' . ( $heading_color ? ' style="color:' . esc_attr( $heading_color ) . ' !important"' : '' ) . ' class="timeline-item-title">' . esc_html( $heading ) . '</h4>';
			$output .= '<div class="timeline-item-content">' . do_shortcode( $content ) . '</div>';
		$output     .= '</div>';
	$output         .= '</article>';
}

echo porto_filter_output( $output );
