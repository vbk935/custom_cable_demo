<?php
// Porto Portfolios
add_shortcode( 'porto_portfolios', 'porto_shortcode_portfolios' );
add_action( 'vc_after_init', 'porto_load_portfolios_shortcode' );
function porto_shortcode_portfolios( $atts, $content = null ) {
	ob_start();
	if ( $template = porto_shortcode_template( 'porto_portfolios' ) ) {
		include $template;
	}
	return ob_get_clean();
}
function porto_load_portfolios_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();
	$order_by_values    = porto_vc_woo_order_by();
	$order_way_values   = porto_vc_woo_order_way();

	vc_map(
		array(
			'name'     => 'Porto ' . __( 'Portfolios', 'porto-functionality' ),
			'base'     => 'porto_portfolios',
			'category' => __( 'Porto', 'porto-functionality' ),
			'icon'     => 'porto_vc_portfolios',
			'params'   => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Title', 'porto-functionality' ),
					'param_name'  => 'title',
					'admin_label' => true,
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Portfolio Layout', 'porto-functionality' ),
					'param_name'  => 'portfolio_layout',
					'std'         => 'timeline',
					'value'       => porto_sh_commons( 'portfolio_layout' ),
					'admin_label' => true,
				),
				array(
					'type'       => 'porto_animation_type',
					'heading'    => __( 'Content Animation', 'porto-functionality' ),
					'param_name' => 'content_animation',
					'dependency' => array(
						'element' => 'portfolio_layout',
						'value'   => array( 'large' ),
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Columns', 'porto-functionality' ),
					'param_name' => 'columns',
					'dependency' => array(
						'element' => 'portfolio_layout',
						'value'   => array( 'grid', 'masonry' ),
					),
					'std'        => '3',
					'value'      => porto_sh_commons( 'portfolio_grid_columns' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'View Type', 'porto-functionality' ),
					'param_name' => 'view',
					'dependency' => array(
						'element' => 'portfolio_layout',
						'value'   => array( 'grid', 'masonry' ),
					),
					'std'        => 'classic',
					'value'      => porto_sh_commons( 'portfolio_grid_view' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Info View Type', 'porto-functionality' ),
					'param_name' => 'info_view',
					'dependency' => array(
						'element' => 'portfolio_layout',
						'value'   => array( 'grid', 'masonry', 'timeline' ),
					),
					'std'        => '',
					'value'      => array(
						__( 'Standard', 'porto-functionality' )  => '',
						__( 'Left Info', 'porto-functionality' ) => 'left-info',
						__( 'Centered Info', 'porto-functionality' ) => 'centered-info',
						__( 'Bottom Info', 'porto-functionality' ) => 'bottom-info',
						__( 'Bottom Info Dark', 'porto-functionality' ) => 'bottom-info-dark',
						__( 'Hide Info Hover', 'porto-functionality' ) => 'hide-info-hover',
						__( 'Plus Icon', 'porto-functionality' ) => 'plus-icon',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Info View Type Style', 'porto-functionality' ),
					'param_name' => 'info_view_type_style',
					'dependency' => array(
						'element' => 'portfolio_layout',
						'value'   => array( 'grid', 'masonry', 'timeline' ),
					),
					'std'        => '',
					'value'      => array(
						__( 'Standard', 'porto-functionality' )  => '',
						__( 'Alternate', 'porto-functionality' ) => 'alternate-info',
						__( 'Alternate with Plus', 'porto-functionality' ) => 'alternate-with-plus',
						__( 'No Style', 'porto-functionality' )  => 'no-style',
					),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Image Size', 'porto-functionality' ),
					'param_name'  => 'image_size',
					'std'         => '',
					'description' => __( 'Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme). Alternatively enter size in pixels (Example: 200x100 (Width x Height)).', 'js_composer' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Image Overlay Background', 'porto-functionality' ),
					'param_name' => 'thumb_bg',
					'std'        => '',
					'value'      => array(
						__( 'Standard', 'porto-functionality' ) => '',
						__( 'Darken', 'porto-functionality' ) => 'darken',
						__( 'Lighten', 'porto-functionality' ) => 'lighten',
						__( 'Transparent', 'porto-functionality' ) => 'hide-wrapper-bg',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Hover Image Effect', 'porto-functionality' ),
					'param_name' => 'thumb_image',
					'std'        => '',
					'value'      => array(
						__( 'Standard', 'porto-functionality' ) => '',
						__( 'Zoom', 'porto-functionality' ) => 'zoom',
						__( 'Slow Zoom', 'porto-functionality' ) => 'slow-zoom',
						__( 'No Zoom', 'porto-functionality' ) => 'no-zoom',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Image Counter', 'porto-functionality' ),
					'param_name' => 'image_counter',
					'dependency' => array(
						'element' => 'portfolio_layout',
						'value'   => array( 'grid', 'masonry', 'timeline' ),
					),
					'std'        => '',
					'value'      => array(
						__( 'Default', 'porto-functionality' ) => '',
						__( 'Show', 'porto-functionality' ) => 'show',
						__( 'Hide', 'porto-functionality' ) => 'hide',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Show Image Lightbox Icon', 'porto-functionality' ),
					'param_name' => 'show_lightbox_icon',
					'value'      => array(
						__( 'Default', 'porto-functionality' ) => '',
						__( 'Show', 'porto-functionality' ) => 'show',
						__( 'Hide', 'porto-functionality' ) => 'hide',
					),
					'std'        => '',
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Category IDs', 'porto-functionality' ),
					'description' => __( 'comma separated list of category ids', 'porto-functionality' ),
					'param_name'  => 'cats',
					'admin_label' => true,
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Portfolio IDs', 'porto-functionality' ),
					'description' => __( 'comma separated list of portfolio ids', 'porto-functionality' ),
					'param_name'  => 'post_in',
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Order by', 'porto-functionality' ),
					'param_name'  => 'orderby',
					'value'       => $order_by_values,
					/* translators: %s: Wordpres codex page */
					'description' => sprintf( __( 'Select how to sort retrieved portfolios. More at %s.', 'porto-functionality' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Order way', 'porto-functionality' ),
					'param_name'  => 'order',
					'value'       => $order_way_values,
					/* translators: %s: Wordpres codex page */
					'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'porto-functionality' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Slider on Portfolio', 'porto-functionality' ),
					'description' => __( 'comma separated list of portfolio ids. <br /> Will Only work with ajax on page settings', 'porto-functionality' ),
					'param_name'  => 'slider',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Portfolios Count', 'porto-functionality' ),
					'param_name' => 'number',
					'value'      => '8',
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Load More Posts', 'porto-functionality' ),
					'param_name' => 'load_more_posts',
					'std'        => '',
					'value'      => array(
						__( 'Select', 'porto-functionality' ) => '',
						__( 'Pagination', 'porto-functionality' ) => 'pagination',
						__( 'Load More (Button)', 'porto-functionality' ) => 'load-more-btn',
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Archive Link', 'porto-functionality' ),
					'param_name' => 'view_more',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Extra class name for Archive Link', 'porto-functionality' ),
					'param_name' => 'view_more_class',
					'dependency' => array(
						'element'   => 'view_more',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Filter', 'porto-functionality' ),
					'param_name' => 'filter',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Filter Style', 'porto-functionality' ),
					'param_name' => 'filter_style',
					'std'        => '',
					'value'      => array(
						__( 'Style 1', 'porto-functionality' ) => '',
						__( 'Style 2', 'porto-functionality' ) => 'style-2',
						__( 'Style 3', 'porto-functionality' ) => 'style-3',
					),
					'dependency' => array(
						'element'   => 'filter',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Enable Ajax Load', 'porto-functionality' ),
					'param_name' => 'ajax_load',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),

				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Ajax Load on Modal', 'porto-functionality' ),
					'param_name' => 'ajax_modal',
					'dependency' => array(
						'element'   => 'ajax_load',
						'not_empty' => true,
					),
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),

				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);
	if ( ! class_exists( 'WPBakeryShortCode_Porto_Portfolios' ) ) {
		class WPBakeryShortCode_Porto_Portfolios extends WPBakeryShortCode {
		}
	}
}
