<?php

$output = $title = $view = $per_page = $columns = $column_width = $addlinks_pos = $orderby = $order = $category = $pagination = $navigation = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'shortcode'          => 'products',
			'title'              => '',
			'title_border_style' => '',
			'title_align'        => '',
			'view'               => 'grid',
			'grid_layout'        => '1',
			'grid_height'        => 600,
			'spacing'            => '',

			'per_page'           => '',
			'columns'            => 4,
			'columns_mobile'     => '',
			'column_width'       => '',

			'count'              => '',
			'pagination_style'   => '',
			'category_filter'    => '',

			'orderby'            => 'date',
			'order'              => 'desc',
			'category'           => '',
			'ids'                => '',
			'attribute'          => '',
			'filter'             => '',

			'addlinks_pos'       => '',
			'overlay_bg_opacity' => '30',
			'image_size'         => '',
			'navigation'         => 1,
			'nav_pos'            => '',
			'nav_pos2'           => '',
			'nav_type'           => '',
			'show_nav_hover'     => false,
			'pagination'         => 0,
			'dots_pos'           => '',
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
			'className'          => '',
			'status'             => '',
		),
		$atts
	)
);

global $porto_settings;

$el_class = porto_shortcode_extract_class( $el_class );

if ( $className ) {
	if ( $el_class ) {
		$el_class = ' ' . $className;
	} else {
		$el_class = $className;
	}
}

$wrapper_id = 'porto-products-' . rand( 1000, 9999 );

$output = '<div id="' . $wrapper_id . '" class="porto-products wpb_content_element' . ( $category_filter ? ' show-category' : '' ) . ( $pagination_style ? ' archive-products' : '' ) . ( $title_border_style ? ' title-' . esc_attr( $title_border_style ) : '' ) . ' ' . esc_attr( trim( $el_class ) ) . '"';
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

if ( $category_filter || $pagination_style ) {
	$output .= '<form class="pagination-form d-none">';
	$output .= '<input type="hidden" name="count" value="' . esc_attr( $count ) . '" >';
	$output .= '<input type="hidden" name="original_orderby" value="' . esc_attr( $orderby ) . '" >';
	$output .= '<input type="hidden" name="orderby" value="' . esc_attr( $orderby ) . '" >';
	$output .= '<input type="hidden" name="category" value="' . esc_attr( $category ) . '" >';
	$output .= '<input type="hidden" name="ids" value="' . esc_attr( $ids ) . '" >';
	$output .= '<input type="hidden" name="columns" value="' . esc_attr( $columns ) . '" >';
	$output .= '<input type="hidden" name="pagination_style" value="' . esc_attr( $pagination_style ) . '" >';
	$output .= '</form>';
}

if ( $title ) {
	$output .= '<h2 class="section-title' . ( $title_align ? ' text-' . esc_attr( $title_align ) : '' ) . ( 'products-slider' == $view ? ' slider-title' : '' ) . '"><span class="inline-title">' . esc_html( $title ) . '</span><span class="line"></span></h2>';
}

if ( $category_filter ) {
	$terms          = get_terms( 'product_cat', array( 'hide_empty' => true ) );
	$category_html  = '<h4 class="section-title">' . esc_html__( 'Sort By', 'porto-functionality' ) . '</h4>';
	$category_html .= '<ul class="product-categories">';
	$category_html .= '<li><a href="javascript:void(0)" data-sort_id="date">' . esc_html__( 'New Arrivals', 'porto-functionality' ) . '</a></li>';
	foreach ( $terms as $term_cat ) {
		if ( 'Uncategorized' == $term_cat->name ) {
			continue;
		}
		$id             = $term_cat->term_id;
		$name           = $term_cat->name;
		$slug           = $term_cat->slug;
		$category_html .= '<li><a href="' . esc_url( get_term_link( $id, 'product_cat' ) ) . '" data-cat_id="' . esc_attr( $slug ) . '">' . esc_html( $name ) . '</a></li>';
	}
	$category_html .= '</ul>';
	$output        .= '<div class="products-filter">';
	if ( apply_filters( 'porto_wooocommerce_products_shortcode_sticky_filter', true ) ) {
		$output .= '<div data-plugin-sticky data-plugin-options="{&quot;autoInit&quot;: true, &quot;minWidth&quot;: 991, &quot;containerSelector&quot;: &quot;.porto-products&quot;, &quot;autoFit&quot;:true, &quot;paddingOffsetBottom&quot;: 10}">';
	}
				$output .= apply_filters( 'porto_wooocommerce_products_shortcode_categories_html', $category_html );
	if ( apply_filters( 'porto_wooocommerce_products_shortcode_sticky_filter', true ) ) {
		$output .= '</div>';
	}
	$output .= '</div>';
}

$wrapper_class = '';
if ( 'products-slider' == $view ) {
	$output .= '<div class="slider-wrapper">';
} elseif ( 'divider' == $view ) {
	$wrapper_class .= 'divider-line';
	$view           = 'grid';
} elseif ( 'creative' == $view && ! in_array( $addlinks_pos, array( 'onimage', 'onimage2', 'onimage3' ) ) ) {
	$addlinks_pos = 'onimage';
}

global $porto_woocommerce_loop;

$porto_woocommerce_loop['view']    = $view;
$porto_woocommerce_loop['columns'] = $columns;
if ( $columns_mobile ) {
	$porto_woocommerce_loop['columns_mobile'] = $columns_mobile;
}
$porto_woocommerce_loop['column_width'] = $column_width;
$porto_woocommerce_loop['pagination']   = $pagination;
$porto_woocommerce_loop['navigation']   = $navigation;
$porto_woocommerce_loop['addlinks_pos'] = $addlinks_pos;

$extra_atts = '';
if ( $category ) {
	$extra_atts .= ' category="' . esc_attr( $category ) . '"';
}
if ( $per_page ) {
	$extra_atts .= ' per_page="' . esc_attr( $per_page ) . '"';
}
if ( $count ) {
	$extra_atts .= ' limit="' . esc_attr( $count ) . '"';
}
if ( $ids ) {
	$extra_atts .= ' ids="' . esc_attr( $ids ) . '"';
	$orderby     = 'include';
	$order       = 'ASC';
}
if ( $category ) {
	$extra_atts .= ' category="' . esc_attr( $category ) . '"';
}
if ( $attribute ) {
	$extra_atts .= ' attribute="' . esc_attr( $attribute ) . '"';
}
if ( $filter ) {
	$extra_atts .= ' filter="' . esc_attr( $filter ) . '"';
}
if ( $orderby ) {
	$extra_atts .= ' orderby="' . esc_attr( $orderby ) . '"';
}
if ( $order ) {
	$extra_atts .= ' order="' . esc_attr( $order ) . '"';
}
if ( $pagination_style ) {
	$extra_atts                        .= ' paginate="true"';
	$porto_settings_backup              = $porto_settings['product-infinite'];
	$porto_settings['product-infinite'] = $pagination_style;

	$shop_action1 = false;
	$shop_action2 = false;
	$shop_action3 = false;
	if ( has_action( 'woocommerce_before_shop_loop', 'porto_woocommerce_open_before_clearfix_div' ) ) {
		$shop_action1 = true;
		remove_action( 'woocommerce_before_shop_loop', 'porto_woocommerce_open_before_clearfix_div', 11 );
	}
	if ( has_action( 'woocommerce_before_shop_loop', 'porto_woocommerce_close_before_clearfix_div' ) ) {
		$shop_action2 = true;
		remove_action( 'woocommerce_before_shop_loop', 'porto_woocommerce_close_before_clearfix_div', 80 );
	}
	if ( has_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering' ) ) {
		$shop_action3 = true;
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
	}
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_pagination', 50 );
}

if ( 'featured' == $status ) {
	$extra_atts .= ' visibility="featured"';
} elseif ( 'on_sale' == $status ) {
	$extra_atts .= ' on_sale="1"';
}

if ( $navigation ) {
	if ( $nav_pos ) {
		$wrapper_class .= ' ' . $nav_pos;
	}
	if ( ( empty( $nav_pos ) || 'nav-center-images-only' == $nav_pos ) && $nav_pos2 ) {
		$wrapper_class .= ' ' . $nav_pos2;
	}
	if ( $nav_type ) {
		$wrapper_class .= ' ' . $nav_type;
	} else {
		$wrapper_class .= ' show-nav-middle';
	}
	if ( $show_nav_hover ) {
		$wrapper_class .= ' show-nav-hover';
	}
}

if ( $pagination && $dots_pos ) {
	$wrapper_class .= ' ' . $dots_pos;
}

if ( $wrapper_class ) {
	$porto_woocommerce_loop['el_class'] = $wrapper_class;
}

if ( $image_size ) {
	$porto_woocommerce_loop['image_size'] = $image_size;
}

if ( 'creative' == $view || ( 'grid' == $view && '' !== $spacing ) || ( '0' == $overlay_bg_opacity || ( '30' != $overlay_bg_opacity && $overlay_bg_opacity ) ) ) {
	echo '<style scope="scope">';

	if ( 'grid' == $view && '' !== $spacing ) {
		echo '#' . $wrapper_id . ' ul.products { margin-left: ' . ( (int) $spacing / 2 * -1 ) . 'px; margin-right: ' . ( (int) $spacing / 2 * -1 ) . 'px; }';
		echo '#' . $wrapper_id . ' li.product { padding-left: ' . ( (int) $spacing / 2 ) . 'px; padding-right: ' . ( (int) $spacing / 2 ) . 'px; margin-bottom: ' . ( (int) $spacing / 2 ) . 'px; }';
		if ( 0 === (int) $spacing && 'onimage2' != $addlinks_pos && 'onimage3' != $addlinks_pos ) {
			echo '#' . $wrapper_id . ' li.product:nth-child(even) .product-image .inner:after { content: ""; position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: rgba(33, 37, 41, .01); }';
			if ( 'outimage' == $addlinks_pos || 'outimage_aq_onimage' == $addlinks_pos ) {
				echo '#' . $wrapper_id . ' .product-content { padding-left: 10px; padding-right: 10px; }';
			}
		}
	} elseif ( 'creative' == $view ) {
		$porto_woocommerce_loop['grid_height']  = $grid_height;
		$porto_woocommerce_loop['grid_layout']  = porto_creative_grid_layout( $grid_layout );
		$porto_woocommerce_loop['grid_spacing'] = $spacing;

		wp_enqueue_script( 'isotope' );

		porto_creative_grid_style( $porto_woocommerce_loop['grid_layout'], $grid_height, $wrapper_id, $spacing, false );
	}

	if ( ( 'onimage2' == $addlinks_pos || 'onimage3' == $addlinks_pos ) && ( '0' == $overlay_bg_opacity || ( '30' != $overlay_bg_opacity && $overlay_bg_opacity ) ) ) {
		echo '#' . $wrapper_id . ' li.product .product-image .inner:after { background-color: rgba(27, 27, 23, ' . ( (int) $overlay_bg_opacity / 100 ) . '); }';
		if ( 'onimage3' == $addlinks_pos ) {
			echo '#' . $wrapper_id . ' li.product:hover .product-image .inner:after { background-color: rgba(27, 27, 23, ' . ( ( $overlay_bg_opacity > 45 ? (int) $overlay_bg_opacity - 15 : (int) $overlay_bg_opacity + 15 ) / 100 ) . '); }';
		}
	}

	echo '</style>';
}

$output .= do_shortcode( '[' . esc_html( $shortcode ) . ' columns="' . $columns . '"' . $extra_atts . ']' );

if ( 'products-slider' == $view ) {
	$output .= '</div>';
}

$output .= '</div>';

if ( $pagination_style ) {
	if ( isset( $porto_settings_backup ) ) {
		$porto_settings['product-infinite'] = $porto_settings_backup;
	}
	if ( $shop_action1 ) {
		add_action( 'woocommerce_before_shop_loop', 'porto_woocommerce_open_before_clearfix_div', 11 );
	}
	if ( $shop_action2 ) {
		add_action( 'woocommerce_before_shop_loop', 'porto_woocommerce_close_before_clearfix_div', 80 );
	}
	if ( $shop_action3 ) {
		add_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
	}
	add_action( 'woocommerce_before_shop_loop', 'woocommerce_pagination', 50 );
}

if ( $wrapper_class ) {
	unset( $porto_woocommerce_loop['el_class'] );
}

if ( $image_size || 'creative' == $view ) {
	unset( $porto_woocommerce_loop['image_size'] );
}

if ( 'creative' == $view ) {
	unset( $porto_woocommerce_loop['grid_height'] );
	unset( $porto_woocommerce_loop['grid_layout'] );
	unset( $porto_woocommerce_loop['grid_spacing'] );
}

echo porto_filter_output( $output );
