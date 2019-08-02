<?php

$output = $title = $view = $number = $columns = $column_width = $hide_empty = $orderby = $order = $parent = $ids = $addlinks_pos = $hide_count = $pagination = $navigation = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'title'              => '',
			'view'               => 'grid',
			'number'             => 12,
			'columns'            => 4,
			'columns_mobile'     => '',
			'column_width'       => '',
			'grid_layout'        => '1',
			'grid_height'        => '600',
			'spacing'            => '',
			'text_position'      => 'middle-center',
			'overlay_bg_opacity' => '15',
			'text_color'         => 'light',

			'orderby'            => 'title',
			'order'              => 'asc',
			'hide_empty'         => '',
			'parent'             => '',
			'ids'                => '',
			'addlinks_pos'       => '',
			'hide_count'         => 0,
			'hover_effect'       => '',
			'image_size'         => '',
			'navigation'         => 1,
			'pagination'         => 0,
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

if ( $hide_count ) {
	$el_class .= ' hide-count';
}
if ( $hover_effect ) {
	$el_class .= ' show-count-on-hover';
}

$hide_empty = $hide_empty ? 1 : 0;

$wrapper_id = 'porto-product-categories-' . rand( 1000, 9999 );

$output = '<div id="' . $wrapper_id . '" class="porto-products wpb_content_element' . ( $el_class ? ' ' . esc_attr( $el_class ) : '' ) . '"';
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

if ( $title ) {
	if ( 'products-slider' == $view ) {
		$output .= '<h2 class="slider-title"><span class="inline-title">' . esc_html( $title ) . '</span><span class="line"></span></h2>';
	} else {
		$output .= '<h2 class="section-title">' . esc_html( $title ) . '</h2>';
	}
}

if ( 'products-slider' == $view ) {
	$output .= '<div class="slider-wrapper">';
}

global $porto_woocommerce_loop, $woocommerce_loop;

$porto_woocommerce_loop['view']    = $view;
$porto_woocommerce_loop['columns'] = $columns;
if ( $columns_mobile ) {
	$porto_woocommerce_loop['columns_mobile'] = $columns_mobile;
}
$porto_woocommerce_loop['column_width'] = $column_width;
$porto_woocommerce_loop['pagination']   = $pagination;
$porto_woocommerce_loop['navigation']   = $navigation;
$porto_woocommerce_loop['addlinks_pos'] = $addlinks_pos;

if ( $image_size ) {
	$porto_woocommerce_loop['image_size'] = $image_size;
}

$porto_woocommerce_loop['category-view'] = 'category-pos-' . explode( '-', $text_position )[0] . ( isset( explode( '-', $text_position )[1] ) ? ' category-text-' . explode( '-', $text_position )[1] : '' ) . ( 'light' != $text_color ? ' category-color-' . $text_color : '' );

if ( 'creative' == $view ) {
	$porto_woocommerce_loop['grid_height']  = $grid_height;
	$porto_woocommerce_loop['grid_layout']  = porto_creative_grid_layout( $grid_layout );
	$porto_woocommerce_loop['grid_spacing'] = $spacing;

	if ( '4' == $grid_layout ) {
		$porto_woocommerce_loop['creative_grid'] = 'true';
	} else {
		wp_enqueue_script( 'isotope' );
	}

	porto_creative_grid_style( $porto_woocommerce_loop['grid_layout'], $grid_height, $wrapper_id, $spacing );
}

if ( '0' == $overlay_bg_opacity || ( '15' != $overlay_bg_opacity && $overlay_bg_opacity ) ) {
	echo '<style>';
		echo '#' . $wrapper_id . ' li.product-category .thumb-info-wrapper:after { background-color: rgba(27, 27, 23, ' . ( (int) $overlay_bg_opacity / 100 ) . '); }';
		echo '#' . $wrapper_id . ' li.product-category:hover .thumb-info-wrapper:after { background-color: rgba(27, 27, 23, ' . ( ( $overlay_bg_opacity > 45 ? (int) $overlay_bg_opacity - 15 : (int) $overlay_bg_opacity + 15 ) / 100 ) . '); }';
	echo '</style>';
}

if ( ! empty( $ids ) ) {
	$orderby = 'include';
	$order   = 'ASC';
}

$output .= do_shortcode( '[product_categories number="' . $number . '" columns="' . $columns . '" orderby="' . $orderby . '" order="' . $order . '" hide_empty="' . $hide_empty . '" parent="' . $parent . '" ids="' . $ids . '"]' );

if ( 'products-slider' == $view ) {
	$output .= '</div>';
}


$output .= '</div>';

if ( $image_size || 'creative' == $view ) {
	unset( $porto_woocommerce_loop['image_size'] );
}

unset( $porto_woocommerce_loop['category-view'] );

if ( 'creative' == $view ) {
	unset( $porto_woocommerce_loop['grid_height'] );
	unset( $porto_woocommerce_loop['grid_layout'] );
	unset( $porto_woocommerce_loop['grid_spacing'] );
}

echo porto_filter_output( $output );
