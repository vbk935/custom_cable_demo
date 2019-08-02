<?php

// Porto Recent Portfolios
add_shortcode( 'porto_recent_portfolios', 'porto_shortcode_recent_portfolios' );
add_action( 'vc_after_init', 'porto_load_recent_portfolios_shortcode' );

function porto_shortcode_recent_portfolios( $atts, $content = null ) {
	ob_start();
	if ( $template = porto_shortcode_template( 'porto_recent_portfolios' ) ) {
		include $template;
	}
	return ob_get_clean();
}

function porto_load_recent_portfolios_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'     => 'Porto ' . __( 'Recent Portfolios', 'porto-functionality' ),
			'base'     => 'porto_recent_portfolios',
			'category' => __( 'Porto', 'porto-functionality' ),
			'icon'     => 'porto_vc_recent_portfolios',
			'params'   => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Title', 'porto-functionality' ),
					'param_name'  => 'title',
					'admin_label' => true,
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'View Type', 'porto-functionality' ),
					'param_name' => 'view',
					'std'        => 'classic',
					'value'      => porto_sh_commons( 'portfolio_grid_view' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Info View Type', 'porto-functionality' ),
					'param_name' => 'info_view',
					'std'        => '',
					'value'      => array(
						__( 'Standard', 'porto-functionality' )  => '',
						__( 'Left Info', 'porto-functionality' ) => 'left-info',
						__( 'Centered Info', 'porto-functionality' ) => 'centered-info',
						__( 'Bottom Info', 'porto-functionality' ) => 'bottom-info',
						__( 'Bottom Info Dark', 'porto-functionality' ) => 'bottom-info-dark',
						__( 'Hide Info Hover', 'porto-functionality' ) => 'hide-info-hover',
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
						__( 'No Zoom', 'porto-functionality' ) => 'no-zoom',
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
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Portfolios Count', 'porto-functionality' ),
					'param_name'  => 'number',
					'value'       => '8',
					'admin_label' => true,
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Category IDs', 'porto-functionality' ),
					'param_name'  => 'cats',
					'admin_label' => true,
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Items to show on Desktop', 'porto-functionality' ),
					'param_name' => 'items_desktop',
					'value'      => '4',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Items to show on Tablets', 'porto-functionality' ),
					'param_name' => 'items_tablets',
					'value'      => '3',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Items to show on Mobile', 'porto-functionality' ),
					'param_name' => 'items_mobile',
					'value'      => '2',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Items Row', 'porto-functionality' ),
					'param_name' => 'items_row',
					'value'      => '1',
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Change Slider Config', 'porto-functionality' ),
					'param_name' => 'slider_config',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'group'      => __( 'Slider Options', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Nav', 'porto-functionality' ),
					'param_name' => 'show_nav',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'dependency' => array(
						'element'   => 'slider_config',
						'not_empty' => true,
					),
					'group'      => __( 'Slider Options', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Nav Position', 'porto-functionality' ),
					'param_name' => 'nav_pos',
					'value'      => array(
						__( 'Middle', 'porto-functionality' ) => '',
						__( 'Top', 'porto-functionality' )    => 'show-nav-title',
						__( 'Bottom', 'porto-functionality' ) => 'nav-bottom',
					),
					'dependency' => array(
						'element'   => 'show_nav',
						'not_empty' => true,
					),
					'group'      => __( 'Slider Options', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Nav Type', 'porto-functionality' ),
					'param_name' => 'nav_type',
					'value'      => porto_sh_commons( 'carousel_nav_types' ),
					'dependency' => array(
						'element' => 'nav_pos',
						'value'   => array( '', 'nav-bottom' ),
					),
					'group'      => __( 'Slider Options', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Nav on Hover', 'porto-functionality' ),
					'param_name' => 'show_nav_hover',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'dependency' => array(
						'element'   => 'show_nav',
						'not_empty' => true,
					),
					'group'      => __( 'Slider Options', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Dots', 'porto-functionality' ),
					'param_name' => 'show_dots',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'dependency' => array(
						'element'   => 'slider_config',
						'not_empty' => true,
					),
					'group'      => __( 'Slider Options', 'porto-functionality' ),
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Recent_Portfolios' ) ) {
		class WPBakeryShortCode_Porto_Recent_Portfolios extends WPBakeryShortCode {
		}
	}
}
