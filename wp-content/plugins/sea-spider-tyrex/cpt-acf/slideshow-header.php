<?php

if( function_exists('register_field_group') ):

register_field_group(array (
	'key' => 'group_54f0cb8d9a005',
	'title' => 'Slideshow - Header',
	'fields' => array (
		array (
			'key' => 'field_54f0cb8d9e164',
			'label' => 'Slideshow',
			'name' => 'header_slideshow',
			'prefix' => '',
			'type' => 'repeater',
			'instructions' => 'This slideshow will override the header image.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'min' => 0,
			'max' => '',
			'layout' => 'row',
			'button_label' => 'Add Slide',
			'sub_fields' => array (
				array (
					'key' => 'field_54f0cb8da195f',
					'label' => 'Image',
					'name' => 'slide_image',
					'prefix' => '',
					'type' => 'image',
					'instructions' => 'Please add at least 2 images before saving.',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'return_format' => 'array',
					'preview_size' => 'thumbnail',
					'library' => 'all',
					'min_width' => 0,
					'min_height' => 0,
					'min_size' => 0,
					'max_width' => 0,
					'max_height' => 0,
					'max_size' => 0,
					'mime_types' => '',
				),
			),
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'page',
			),
		),
		array (
			array (
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'post',
			),
		),
		array (
			array (
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'article',
			),
		),
	),
	'menu_order' => 15,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
));

endif;

?>