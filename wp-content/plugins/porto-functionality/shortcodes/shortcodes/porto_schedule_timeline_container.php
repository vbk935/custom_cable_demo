<?php
// Porto Schedule Timeline Container
add_shortcode( 'porto_schedule_timeline_container', 'porto_shortcode_schedule_timeline_container' );
add_action( 'vc_after_init', 'porto_load_schedule_timeline_container_shortcode' );
function porto_shortcode_schedule_timeline_container( $atts, $content = null ) {
	ob_start();
	if ( $template = porto_shortcode_template( 'porto_schedule_timeline_container' ) ) {
		include $template;
	}
	return ob_get_clean();
}
function porto_load_schedule_timeline_container_shortcode() {
	$custom_class = porto_vc_custom_class();
	vc_map(
		array(
			'name'            => 'Porto ' . __( 'Schedule Timeline Container', 'porto-functionality' ),
			'base'            => 'porto_schedule_timeline_container',
			'category'        => __( 'Porto', 'porto-functionality' ),
			'icon'            => 'porto_vc_schedule_timeline',
			'as_parent'       => array( 'only' => 'porto_schedule_timeline_item' ),
			'content_element' => true,
			'controls'        => 'full',
			'js_view'         => 'VcColumnView',
			'params'          => array(
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Type', 'porto-functionality' ),
					'param_name' => 'type',
					'value'      => array(
						__( 'Schedule', 'porto-functionality' ) => 'schedule',
						__( 'History', 'porto-functionality' )  => 'history',
					),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Title', 'porto-functionality' ),
					'param_name'  => 'title',
					'admin_label' => true,
					'dependency'  => array(
						'element' => 'type',
						'value'   => array( 'schedule' ),
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Subtitle', 'porto-functionality' ),
					'param_name' => 'subtitle',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'schedule' ),
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Circle Type', 'porto-functionality' ),
					'param_name' => 'circle_type',
					'value'      => array(
						__( 'Filled', 'porto-functionality' ) => 'filled',
						__( 'Simple', 'porto-functionality' ) => 'simple',
					),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'schedule' ),
					),
				),
				array(
					'type'       => 'label',
					'heading'    => __( 'Title Settings', 'porto-functionality' ),
					'param_name' => 'label',
					'group'      => 'Typography',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'schedule' ),
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'param_name' => 'title_color',
					'group'      => 'Typography',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'schedule' ),
					),
				),
				array(
					'type'       => 'label',
					'heading'    => __( 'Subtitle Settings', 'porto-functionality' ),
					'param_name' => 'label',
					'group'      => 'Typography',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'schedule' ),
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'param_name' => 'subtitle_color',
					'group'      => 'Typography',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'schedule' ),
					),
				),
				$custom_class,
			),
		)
	);
	if ( ! class_exists( 'WPBakeryShortCode_Porto_Schedule_Timeline_Container' ) ) {
		class WPBakeryShortCode_Porto_Schedule_Timeline_Container extends WPBakeryShortCodesContainer {

		}
	}
}
