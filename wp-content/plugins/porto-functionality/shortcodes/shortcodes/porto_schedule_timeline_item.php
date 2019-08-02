<?php

// Porto Schedule Timeline Item
add_shortcode( 'porto_schedule_timeline_item', 'porto_shortcode_schedule_timeline_item' );
add_action( 'vc_after_init', 'porto_load_schedule_timeline_item_shortcode' );

function porto_shortcode_schedule_timeline_item( $atts, $content = null ) {
	ob_start();
	if ( $template = porto_shortcode_template( 'porto_schedule_timeline_item' ) ) {
		include $template;
	}
	return ob_get_clean();
}

function porto_load_schedule_timeline_item_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'     => __( 'Schedule Timeline Item', 'porto-functionality' ),
			'base'     => 'porto_schedule_timeline_item',
			'category' => __( 'Porto', 'porto-functionality' ),
			'icon'     => 'porto_vc_schedule_timeline',
			'as_child' => array( 'only' => 'porto_schedule_timeline_container' ),
			'params'   => array(
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Subtitle/time', 'porto-functionality' ),
					'param_name' => 'subtitle',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Image URL', 'porto-functionality' ),
					'param_name' => 'image_url',
				),
				array(
					'type'       => 'attach_image',
					'heading'    => __( 'Image', 'porto-functionality' ),
					'param_name' => 'image_id',
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Heading', 'porto-functionality' ),
					'param_name'  => 'heading',
					'admin_label' => true,
				),
				array(
					'type'       => 'textarea_html',
					'heading'    => __( 'Details', 'porto-functionality' ),
					'param_name' => 'content',
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Shadow', 'porto-functionality' ),
					'param_name' => 'shadow',
				),
				array(
					'type'       => 'label',
					'heading'    => __( 'Heading Settings', 'porto-functionality' ),
					'param_name' => 'label',
					'group'      => 'Typography',
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'param_name' => 'heading_color',
					'group'      => 'Typography',
				),
				array(
					'type'       => 'label',
					'heading'    => __( 'Subtitle Settings', 'porto-functionality' ),
					'param_name' => 'label',
					'group'      => 'Typography',
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'param_name' => 'subtitle_color',
					'group'      => 'Typography',
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,

			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Schedule_Timeline_Item' ) ) {
		class WPBakeryShortCode_Porto_Schedule_Timeline_Item extends WPBakeryShortCode {
		}
	}
}
