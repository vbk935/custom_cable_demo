<?php
/**
 * Function for Content Type
 */
if ( ! function_exists( 'array_insert_before' ) ) :
	function array_insert_before( $key, array &$array, $new_key, $new_value ) {
		if ( array_key_exists( $key, $array ) ) {
			$new = array();
			foreach ( $array as $k => $value ) {
				if ( $k === $key ) {
					$new[ $new_key ] = $new_value;
				}
				$new[ $k ] = $value;
			}
			return $new;
		}
		return false;
	}
endif;


if ( ! function_exists( 'array_insert_after' ) ) :
	function array_insert_after( $key, &$array, $new_key, $new_value ) {
		if ( array_key_exists( $key, $array ) ) {
			$new = array();
			foreach ( $array as $k => $value ) {
				$new[ $k ] = $value;
				if ( $k === $key ) {
					$new[ $new_key ] = $new_value;
				}
			}
			return $new;
		}
		return false;
	}
endif;

if ( ! function_exists( 'porto_stringify_attributes' ) ) :
	function porto_stringify_attributes( $attributes ) {

		$atts = array();
		foreach ( $attributes as $name => $value ) {
			$atts[] = $name . '="' . esc_attr( $value ) . '"';
		}

		return implode( ' ', $atts );
	}
endif;

if ( ! function_exists( 'porto_filter_output' ) ) :
	function porto_filter_output( $output_escaped ) {
		return $output_escaped;
	}
endif;

if ( ! function_exists( 'porto_ct_layouts' ) ) :
	function porto_ct_layouts() {

		return array(
			'widewidth'          => __( 'Wide Width', 'porto' ),
			'wide-left-sidebar'  => __( 'Wide Left Sidebar', 'porto' ),
			'wide-right-sidebar' => __( 'Wide Right Sidebar', 'porto' ),
			'wide-both-sidebar'  => __( 'Wide Left & Right Sidebars', 'porto' ),
			'fullwidth'          => __( 'Without Sidebar', 'porto' ),
			'left-sidebar'       => __( 'Left Sidebar', 'porto' ),
			'right-sidebar'      => __( 'Right Sidebar', 'porto' ),
			'both-sidebar'       => __( 'Left & Right Sidebars', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_post_archive_layouts' ) ) :
	function porto_ct_post_archive_layouts() {

		return array(
			'full'        => __( 'Full', 'porto' ),
			'large'       => __( 'Large', 'porto' ),
			'large-alt'   => __( 'Large Alt', 'porto' ),
			'medium'      => __( 'Medium', 'porto' ),
			'grid'        => __( 'Grid', 'porto' ),
			'masonry'     => __( 'Masonry', 'porto' ),
			'timeline'    => __( 'Timeline', 'porto' ),
			'woocommerce' => __( 'Woocommerce', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_post_single_layouts' ) ) :
	function porto_ct_post_single_layouts() {

		return array(
			'full'        => __( 'Full', 'porto' ),
			'large'       => __( 'Large', 'porto' ),
			'large-alt'   => __( 'Large Alt', 'porto' ),
			'medium'      => __( 'Medium', 'porto' ),
			'woocommerce' => __( 'Woocommerce', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_portfolio_archive_layouts' ) ) :
	function porto_ct_portfolio_archive_layouts() {

		return array(
			'grid'     => __( 'Grid', 'porto' ),
			'masonry'  => __( 'Masonry', 'porto' ),
			'timeline' => __( 'Timeline', 'porto' ),
			'full'     => __( 'Full', 'porto' ),
			'large'    => __( 'Large', 'porto' ),
			'medium'   => __( 'Medium', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_portfolio_single_layouts' ) ) :
	function porto_ct_portfolio_single_layouts() {

		return array(
			'medium'      => __( 'Medium Slider', 'porto' ),
			'large'       => __( 'Large Slider', 'porto' ),
			'full'        => __( 'Full Slider', 'porto' ),
			'gallery'     => __( 'Gallery', 'porto' ),
			'gallery'     => __( 'Gallery', 'porto' ),
			'carousel'    => __( 'Carousel', 'porto' ),
			'medias'      => __( 'Medias', 'porto' ),
			'full-video'  => __( 'Full Width Video', 'porto' ),
			'masonry'     => __( 'Masonry Images', 'porto' ),
			'full-images' => __( 'Full Images', 'porto' ),
			'extended'    => __( 'Extended', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_event_single_layouts' ) ) :
	function porto_ct_event_single_layouts() {

		return array(
			'medium'      => __( 'Medium Slider', 'porto' ),
			'large'       => __( 'Large Slider', 'porto' ),
			'full'        => __( 'Full Slider', 'porto' ),
			'gallery'     => __( 'Gallery', 'porto' ),
			'gallery'     => __( 'Gallery', 'porto' ),
			'carousel'    => __( 'Carousel', 'porto' ),
			'medias'      => __( 'Medias', 'porto' ),
			'full-video'  => __( 'Full Width Video', 'porto' ),
			'masonry'     => __( 'Masonry Images', 'porto' ),
			'full-images' => __( 'Full Images', 'porto' ),
			'extended'    => __( 'Extended', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_sidebars' ) ) :
	function porto_ct_sidebars() {

		global $wp_registered_sidebars;
		$sidebar_options = array();

		if ( ! empty( $wp_registered_sidebars ) ) {

			foreach ( $wp_registered_sidebars as $sidebar ) {
				if ( ! in_array( $sidebar['id'], array( 'content-bottom-1', 'content-bottom-2', 'content-bottom-3', 'content-bottom-4', 'footer-top', 'footer-column-1', 'footer-column-2', 'footer-column-3', 'footer-column-4', 'footer-bottom' ) ) ) {
					$sidebar_options[ $sidebar['id'] ] = $sidebar['name'];
				}
			}
		};
		return $sidebar_options;
	}
endif;

if ( ! function_exists( 'porto_ct_banner_pos' ) ) :
	function porto_ct_banner_pos() {

		return array(
			''              => __( 'Default', 'porto' ),
			'before_header' => __( 'Before Header', 'porto' ),
			'below_header'  => __( 'Behind Header', 'porto' ),
			'fixed'         => __( 'Fixed', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_banner_type' ) ) :
	function porto_ct_banner_type() {

		return array(
			'rev_slider'    => __( 'Revolution Slider', 'porto' ),
			'master_slider' => __( 'Master Slider', 'porto' ),
			'banner_block'  => __( 'Banner Block', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_header_view' ) ) :
	function porto_ct_header_view() {

		return array(
			'default' => __( 'Default', 'porto' ),
			'fixed'   => __( 'Fixed', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_footer_view' ) ) :
	function porto_ct_footer_view() {
		return array(
			''       => __( 'Default', 'porto' ),
			'simple' => __( 'Simple', 'porto' ),
			'fixed'  => __( 'Simple and Fixed', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_master_sliders' ) ) :
	global $porto_master_sliders, $porto_check_master_sliders;
	$porto_master_sliders = null;

	$porto_check_master_sliders = false;

	function porto_ct_master_sliders() {

		global $wpdb, $porto_master_sliders, $porto_check_master_sliders;
		if ( $porto_master_sliders ) {
			return $porto_master_sliders;
		}
		if ( ! class_exists( 'Master_Slider' ) ) {
			return array();
		}
		if ( ! $porto_check_master_sliders ) {

			$table_name = $wpdb->prefix . 'masterslider_sliders';
			if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) == $table_name ) {
				$sliders        = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . esc_sql( $table_name ) . ' WHERE status=%s ORDER BY ID DESC', 'published' ) );
				$master_sliders = array();

				if ( ! empty( $sliders ) ) {
					foreach ( $sliders as $slider ) {
						$master_sliders[ $slider->ID ] = '#' . $slider->ID . ': ' . $slider->title;
					}
				}
				$porto_master_sliders = $master_sliders;
			}
			$porto_check_master_sliders = true;
		}
		return $porto_master_sliders;
	}
endif;

if ( ! function_exists( 'porto_ct_rev_sliders' ) ) :
	global $porto_rev_sliders, $porto_check_rev_sliders;
	$porto_rev_sliders = null;

	$porto_check_rev_sliders = false;
	function porto_ct_rev_sliders() {

		global $wpdb, $porto_rev_sliders, $porto_check_rev_sliders;
		if ( $porto_rev_sliders ) {
			return $porto_rev_sliders;
		}
		if ( ! class_exists( 'RevSliderFront' ) ) {
			return array();
		}
		if ( ! $porto_check_rev_sliders ) {
			$table_name = $wpdb->prefix . 'revslider_sliders';

			if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) == $table_name ) {
				$sliders     = $wpdb->get_results( 'SELECT * FROM ' . esc_sql( $table_name ) );
				$rev_sliders = array();
				if ( ! empty( $sliders ) ) {
					foreach ( $sliders as $slider ) {
						$rev_sliders[ $slider->alias ] = '#' . $slider->id . ': ' . $slider->title;
					}
				}
				$porto_rev_sliders = $rev_sliders;
			}
			$porto_check_rev_sliders = true;
		}
		return $porto_rev_sliders;
	}
endif;

if ( ! function_exists( 'porto_ct_related_product_columns' ) ) :
	function porto_ct_related_product_columns() {

		return array(
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
			'6' => '6',
		);
	}
endif;

if ( ! function_exists( 'porto_ct_product_columns' ) ) :
	function porto_ct_product_columns() {

		return array(
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
			'6' => '6',
			'7' => __( '7 (widthout sidebar)', 'porto' ),
			'8' => __( '8 (widthout sidebar)', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_category_addlinks_pos' ) ) :
	function porto_ct_category_addlinks_pos() {

		$layouts = porto_sh_commons( 'products_addlinks_pos' );
		$result  = array();
		foreach ( $layouts as $key => $value ) {
			$result[ $value ] = $key;
		}
		return $result;
	}
endif;

if ( ! function_exists( 'porto_ct_bg_repeat' ) ) :
	function porto_ct_bg_repeat() {

		return array(
			''          => __( 'Default', 'porto' ),
			'no-repeat' => __( 'No Repeat', 'porto' ),
			'repeat'    => __( 'Repeat All', 'porto' ),
			'repeat-x'  => __( 'Repeat Horizontally', 'porto' ),
			'repeat-y'  => __( 'Repeat Vertically', 'porto' ),
			'inherit'   => __( 'Inherit', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_bg_size' ) ) :
	function porto_ct_bg_size() {

		return array(
			''        => __( 'Default', 'porto' ),
			'inherit' => __( 'Inherit', 'porto' ),
			'cover'   => __( 'Cover', 'porto' ),
			'contain' => __( 'Contain', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_bg_attachment' ) ) :
	function porto_ct_bg_attachment() {

		return array(
			''        => __( 'Default', 'porto' ),
			'fixed'   => __( 'Fixed', 'porto' ),
			'scroll'  => __( 'Scroll', 'porto' ),
			'inherit' => __( 'Inherit', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_bg_position' ) ) :
	function porto_ct_bg_position() {

		return array(
			''              => __( 'Default', 'porto' ),
			'left top'      => __( 'Left Top', 'porto' ),
			'left center'   => __( 'Left Center', 'porto' ),
			'left bottom'   => __( 'Left Bottom', 'porto' ),
			'center top'    => __( 'Center Top', 'porto' ),
			'center center' => __( 'Center Center', 'porto' ),
			'center bottom' => __( 'Center Bottom', 'porto' ),
			'right top'     => __( 'Right Top', 'porto' ),
			'right center'  => __( 'Right Center', 'porto' ),
			'right bottom'  => __( 'Right Bottom', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_category_view_mode' ) ) :
	function porto_ct_category_view_mode() {

		return array(

			''     => __( 'Default', 'porto' ),
			'grid' => __( 'Grid', 'porto' ),
			'list' => __( 'List', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_categories_orderby' ) ) :
	function porto_ct_categories_orderby() {

		return array(

			'id'    => __( 'ID', 'porto' ),
			'name'  => __( 'Name', 'porto' ),
			'slug'  => __( 'Slug', 'porto' ),
			'count' => __( 'Count', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_categories_order' ) ) :
	function porto_ct_categories_order() {

		return array(
			'asc'  => __( 'Asc', 'porto' ),
			'desc' => __( 'Desc', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_categories_sort_pos' ) ) :
	function porto_ct_categories_sort_pos() {

		return array(
			'content'     => __( 'In Content', 'porto' ),
			'breadcrumbs' => __( 'In Breadcrumbs', 'porto' ),
			'sidebar'     => __( 'In Sidebar', 'porto' ),
			'hide'        => __( 'Hide', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_share_options' ) ) :
	function porto_ct_share_options() {

		return array(
			''    => __( 'Default', 'porto' ),
			'yes' => __( 'Yes', 'porto' ),
			'no'  => __( 'No', 'porto' ),
		);

	}
endif;

if ( ! function_exists( 'porto_ct_show_options' ) ) :
	function porto_ct_show_options() {

		return array(
			''    => __( 'Default', 'porto' ),
			'yes' => __( 'Show', 'porto' ),
			'no'  => __( 'Hide', 'porto' ),
		);

	}
endif;

if ( ! function_exists( 'porto_ct_enable_options ' ) ) :
	function porto_ct_enable_options() {

		return array(
			''    => __( 'Default', 'porto' ),
			'yes' => __( 'Enable', 'porto' ),
			'no'  => __( 'Disable', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_slideshow_types' ) ) :
	function porto_ct_slideshow_types() {

		return array(
			'images' => __( 'Featured Images', 'porto' ),
			'video'  => __( 'Video & Audio or Content', 'porto' ),
			'none'   => __( 'None', 'porto' ),
		);
	}
endif;

if ( ! function_exists( 'porto_ct_post_media_types' ) ) :
	function porto_ct_post_media_types() {

		return array(
			'images' => __( 'Slideshow', 'porto' ),
			'grid'   => __( 'Grid Images', 'porto' ),
			'video'  => __( 'Video & Audio or Content', 'porto' ),
			'none'   => __( 'None', 'porto' ),
		);
	}
endif;
