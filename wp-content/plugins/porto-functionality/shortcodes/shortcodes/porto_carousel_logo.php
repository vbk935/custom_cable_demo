<?php
// Porto Call To Action Box

add_shortcode( 'porto_carousel_logo', 'porto_shortcode_carousel_logo' );
add_action( 'vc_after_init', 'porto_load_carousel_logo_shortcode' );

function porto_shortcode_carousel_logo( $atts, $content = null ) {

	ob_start();
	if ( $template = porto_shortcode_template( 'porto_carousel_logo' ) ) {
		include $template;
	}
	return ob_get_clean();
}

function porto_load_carousel_logo_shortcode() {

	$custom_class = porto_vc_custom_class();

	vc_map(
		array(
			'name'     => __( 'Porto Carousel Logo', 'ultimate_vc' ),
			'base'     => 'porto_carousel_logo',
			'class'    => 'porto_carousel_logo',
			'icon'     => 'porto4_vc_carousel_logo',
			'category' => __( 'Porto', 'porto-functionality' ),
			'params'   => array(
				array(
					'type'       => 'attach_image',
					'heading'    => __( 'Logo', 'porto-functionality' ),
					'param_name' => 'logo_img',
				),
				array(
					'type'       => 'attach_image',
					'heading'    => __( 'Logo on hover', 'porto-functionality' ),
					'param_name' => 'logo_hover_img',
				),
				array(
					'type'        => 'textarea_html',
					'class'       => '',
					'heading'     => __( 'Text ', 'porto-functionality' ),
					'param_name'  => 'content',
					'admin_label' => true,
					'value'       => '',
				),
				$custom_class,
			),
		)
	);

	if ( class_exists( 'WPBakeryShortCode' ) ) {
		class WPBakeryShortCode_porto_carousel_logo extends WPBakeryShortCode {
		}
	}
}
