<?php

// Porto Page Header
add_shortcode( 'porto_page_header', 'porto_shortcode_page_header' );
add_action( 'vc_after_init', 'porto_load_page_header_shortcode' );
add_action( 'save_post', 'porto_check_page_header_shortcode', 10, 1 );

function porto_shortcode_page_header( $atts, $content = null ) {
	ob_start();
	if ( $template = porto_shortcode_template( 'porto_page_header' ) ) {
		include $template;
	}
	return ob_get_clean();
}

function porto_check_page_header_shortcode( $post_id ) {
	if ( ! function_exists( 'get_current_screen' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'post' == $screen->base && isset( $_POST['content'] ) ) {
		if ( stripos( $_POST['content'], '[porto_page_header ' ) !== false ) {
			preg_match( '/\[porto_page_header\sbreadcrumbs_type=([^ ]*)([^]]*)\]/', $_POST['content'], $matches );
			$breadcrumbs_type = '1';
			if ( isset( $matches[1] ) ) {
				$breadcrumbs_type = str_replace( array( '\\', '"' ), '', $matches[1] );
			}
			update_post_meta( $post_id, 'porto_page_header_shortcode_type', $breadcrumbs_type );
		} else {
			delete_post_meta( $post_id, 'porto_page_header_shortcode_type' );
		}
	}
}

function porto_load_page_header_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			/* translators: %s: Theme name */
			'name'     => sprintf( __( '%s Page Header', 'porto-functionality' ), 'Porto' ),
			'base'     => 'porto_page_header',
			'category' => __( 'Porto', 'porto-functionality' ),
			'icon'     => 'porto4_vc_page_header',
			'controls' => 'full',
			'params'   => array(
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Breadcrumbs Type', 'porto-functionality' ),
					'param_name'  => 'breadcrumbs_type',
					'value'       => array(
						__( 'Type 1', 'porto-functionality' ) => '1',
						__( 'Type 2', 'porto-functionality' ) => '2',
						__( 'Type 3', 'porto-functionality' ) => '3',
						__( 'Type 4', 'porto-functionality' ) => '4',
						__( 'Type 5', 'porto-functionality' ) => '5',
						__( 'Type 6', 'porto-functionality' ) => '6',
						__( 'Type 7', 'porto-functionality' ) => '7',
					),
					'admin_label' => true,
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Page Title', 'porto-functionality' ),
					'param_name'  => 'page_title',
					'value'       => '',
					'description' => __( 'Please leave this field blank to display default page title.', 'porto-functionality' ),
					'admin_label' => true,
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Page Sub Title', 'porto-functionality' ),
					'param_name'  => 'page_sub_title',
					'value'       => '',
					'admin_label' => true,
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Breadcrumbs Text Color', 'porto-functionality' ),
					'param_name' => 'breadcrumbs_text_color',
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Breadcrumbs Link Color', 'porto-functionality' ),
					'param_name' => 'breadcrumbs_link_color',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Page Title Font Size', 'porto-functionality' ),
					'param_name' => 'page_title_font_size',
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Page Title Color', 'porto-functionality' ),
					'param_name' => 'page_title_color',
				),
				array(
					'type'       => 'number',
					'heading'    => __( 'Page Title Margin Bottom', 'porto-functionality' ),
					'param_name' => 'page_title_margin_bottom',
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Page Sub Title Color', 'porto-functionality' ),
					'param_name' => 'page_subtitle_color',
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Page_Header' ) ) {
		class WPBakeryShortCode_Porto_Page_Header extends WPBakeryShortCode {
		}
	}
}
