<?php

// Porto Sort Filters
add_shortcode( 'porto_sort_filters', 'porto_shortcode_sort_filters' );
add_action( 'vc_after_init', 'porto_load_sort_filters_shortcode' );

function porto_shortcode_sort_filters( $atts, $content = null ) {
	ob_start();
	if ( $template = porto_shortcode_template( 'porto_sort_filters' ) ) {
		include $template;
	}
	return ob_get_clean();
}

function porto_load_sort_filters_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'            => 'Porto ' . __( 'Sort Filters', 'porto-functionality' ),
			'base'            => 'porto_sort_filters',
			'category'        => __( 'Porto', 'porto-functionality' ),
			'icon'            => 'porto_vc_sort_filters',
			'as_parent'       => array( 'only' => 'porto_sort_filter' ),
			'content_element' => true,
			'controls'        => 'full',
			//'is_container' => true,
			'js_view'         => 'VcColumnView',
			'params'          => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Sort Container ID', 'porto-functionality' ),
					'param_name'  => 'container',
					'admin_label' => true,
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Sort Style', 'porto-functionality' ),
					'param_name' => 'style',
					'std'        => '',
					'value'      => porto_sh_commons( 'sort_style' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Sort Align', 'porto-functionality' ),
					'param_name' => 'align',
					'value'      => porto_sh_commons( 'align' ),
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Sort_Filters' ) ) {
		class WPBakeryShortCode_Porto_Sort_Filters extends WPBakeryShortCodesContainer {
		}
	}
}
