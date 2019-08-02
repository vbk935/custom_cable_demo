<?php
if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array (
	'key' => 'group_54cd8a59185d9',
	'title' => 'Panel - Layout',
	'fields' => array (
		array (
			'key' => 'field_54cd94772d7e3',
			'label' => 'Panel Layout',
			'name' => 'panel_layout',
			'type' => 'select',
			'instructions' => 'Percentage of space to be given to the main image. At 100% the image would be centered at the top (or bottom).	The others you would need to image 2 columns, the left percentage would be how big the left column would be and the right percentage would be for the right.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array (
				'100top' => '100% Image on top',
				'100bottom' => '100% Image on bottom',
				1090 => '10% / 90%',
				2080 => '20% / 80%',
				3070 => '30% / 70%',
				4060 => '40% / 60%',
				5050 => '50% / 50%',
			),
			'default_value' => array (
				'' => '',
			),
			'allow_null' => 0,
			'multiple' => 0,
			'ui' => 0,
			'ajax' => 0,
			'placeholder' => '',
			'disabled' => 0,
			'readonly' => 0,
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'panel',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
));

acf_add_local_field_group(array (
	'key' => 'group_54cd7833942d7',
	'title' => 'Panels - Header',
	'fields' => array (
		array (
			'key' => 'field_54cd79a92c6f9',
			'label' => 'Header background color?',
			'name' => 'change_header',
			'type' => 'select',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => 33,
				'class' => '',
				'id' => '',
			),
			'choices' => array (
				'default' => 'Default (Grey)',
				'company' => 'Company Color',
				'other' => 'Other Color',
			),
			'default_value' => array (
				'' => '',
			),
			'allow_null' => 0,
			'multiple' => 0,
			'ui' => 0,
			'ajax' => 0,
			'placeholder' => '',
			'disabled' => 0,
			'readonly' => 0,
		),
		array (
			'key' => 'field_5552c60c648e9',
			'label' => 'Background Color',
			'name' => 'header_background_color',
			'type' => 'select',
			'instructions' => 'Color is set to be the site color, this will override it.',
			'required' => 0,
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_54cd79a92c6f9',
						'operator' => '==',
						'value' => 'company',
					),
				),
			),
			'wrapper' => array (
				'width' => 33,
				'class' => '',
				'id' => '',
			),
			'choices' => array (
				'arl' => 'ARL',
				'dli' => 'DLI',
				'irex' => 'iRex',
				'meg' => 'Megladon',
				'good' => 'RecognizeGood',
				'sbd' => 'Saber Data',
				'sbr' => 'Saberex',
				'tyr' => 'TyRex',
			),
			'default_value' => array (
				'' => '',
			),
			'allow_null' => 0,
			'multiple' => 0,
			'ui' => 0,
			'ajax' => 0,
			'placeholder' => '',
			'disabled' => 0,
			'readonly' => 0,
		),
		array (
			'key' => 'field_54cd78fa2c6f7',
			'label' => 'Other Color',
			'name' => 'other_color',
			'type' => 'color_picker',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_54cd79a92c6f9',
						'operator' => '==',
						'value' => 'other',
					),
				),
			),
			'wrapper' => array (
				'width' => 33,
				'class' => '',
				'id' => '',
			),
			'default_value' => '#777',
		),
		array (
			'key' => 'field_54cd79282c6f8',
			'label' => 'Text Color',
			'name' => 'text_color',
			'type' => 'color_picker',
			'instructions' => '',
			'required' => 1,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => 33,
				'class' => '',
				'id' => '',
			),
			'default_value' => '#fff',
		),
		array (
			'key' => 'field_553322a940a74',
			'label' => 'Subhead',
			'name' => 'subhead',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
			'readonly' => 0,
			'disabled' => 0,
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'panel',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
));

acf_add_local_field_group(array (
	'key' => 'group_54cd7d39c25f9',
	'title' => 'Panels - Links',
	'fields' => array (
		array (
			'key' => 'field_54cd7d61d88f1',
			'label' => 'Add Links',
			'name' => 'add_links',
			'type' => 'true_false',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => 50,
				'class' => '',
				'id' => '',
			),
			'message' => '',
			'default_value' => 0,
		),
		array (
			'key' => 'field_5501d5f8aa917',
			'label' => 'Make into Buttons',
			'name' => 'make_buttons',
			'type' => 'true_false',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => 50,
				'class' => '',
				'id' => '',
			),
			'message' => '',
			'default_value' => 0,
		),
		array (
			'key' => 'field_54dc0c05b89b5',
			'label' => 'Link List',
			'name' => 'link_list',
			'type' => 'repeater',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_54cd7d61d88f1',
						'operator' => '==',
						'value' => '1',
					),
				),
			),
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'min' => '',
			'max' => '',
			'layout' => 'block',
			'button_label' => 'Add Link',
			'sub_fields' => array (
				array (
					'key' => 'field_54dc1b958149c',
					'label' => 'Link Text',
					'name' => 'link_text',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
					'readonly' => 0,
					'disabled' => 0,
				),
				array (
					'key' => 'field_54dc0c7cb89b6',
					'label' => 'Link Type',
					'name' => 'link_type',
					'type' => 'select',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'choices' => array (
						'external' => 'External',
						'internal' => 'Internal',
						'anchor' => 'Internal with anchor',
					),
					'default_value' => array (
						'' => '',
					),
					'allow_null' => 0,
					'multiple' => 0,
					'ui' => 0,
					'ajax' => 0,
					'placeholder' => '',
					'disabled' => 0,
					'readonly' => 0,
				),
				array (
					'key' => 'field_54dc0d85b89b7',
					'label' => 'Link URL',
					'name' => 'link_url',
					'type' => 'url',
					'instructions' => 'Be sure the url starts with http://',
					'required' => 0,
					'conditional_logic' => array (
						array (
							array (
								'field' => 'field_54dc0c7cb89b6',
								'operator' => '==',
								'value' => 'external',
							),
						),
					),
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => 'http://www.google.com',
				),
				array (
					'key' => 'field_54dc0dd0b89b8',
					'label' => 'Internal Page',
					'name' => 'internal_page',
					'type' => 'page_link',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => array (
						array (
							array (
								'field' => 'field_54dc0c7cb89b6',
								'operator' => '==',
								'value' => 'internal',
							),
						),
						array (
							array (
								'field' => 'field_54dc0c7cb89b6',
								'operator' => '==',
								'value' => 'anchor',
							),
						),
					),
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'post_type' => array (
					),
					'taxonomy' => array (
					),
					'allow_null' => 0,
					'multiple' => 0,
				),
				array (
					'key' => 'field_54dc0e15b89b9',
					'label' => 'Anchor',
					'name' => 'internal_anchor',
					'type' => 'post_object',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => array (
						array (
							array (
								'field' => 'field_54dc0c7cb89b6',
								'operator' => '==',
								'value' => 'anchor',
							),
						),
					),
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'post_type' => array (
						0 => 'panel',
					),
					'taxonomy' => array (
					),
					'allow_null' => 0,
					'multiple' => 0,
					'return_format' => 'id',
					'ui' => 1,
				),
			),
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'panel',
			),
		),
	),
	'menu_order' => 3,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
));

acf_add_local_field_group(array (
	'key' => 'group_54cd839d846cb',
	'title' => 'Panels - Downloads',
	'fields' => array (
		array (
			'key' => 'field_54cd83c26ca92',
			'label' => 'Add Downloads?',
			'name' => 'add_downloads',
			'type' => 'true_false',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => 50,
				'class' => '',
				'id' => '',
			),
			'message' => '',
			'default_value' => 0,
		),
		array (
			'key' => 'field_54cd84076ca93',
			'label' => 'Download Type',
			'name' => 'download_type',
			'type' => 'radio',
			'instructions' => 'The "icon" option can be changed in the Site Settings.',
			'required' => 0,
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_54cd83c26ca92',
						'operator' => '==',
						'value' => '1',
					),
				),
			),
			'wrapper' => array (
				'width' => 50,
				'class' => '',
				'id' => '',
			),
			'choices' => array (
				'button' => 'Button',
				'text' => 'Text',
			),
			'other_choice' => 0,
			'save_other_choice' => 0,
			'default_value' => '',
			'layout' => 'horizontal',
		),
		array (
			'key' => 'field_54cd8668e6719',
			'label' => 'Downloads',
			'name' => 'downloads_repeater',
			'type' => 'repeater',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_54cd83c26ca92',
						'operator' => '==',
						'value' => '1',
					),
				),
			),
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'min' => '',
			'max' => '',
			'layout' => 'block',
			'button_label' => 'Add Download',
			'sub_fields' => array (
				array (
					'key' => 'field_54cd869be671a',
					'label' => 'Download',
					'name' => 'download',
					'type' => 'file',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'return_format' => 'array',
					'library' => 'all',
					'min_size' => 0,
					'max_size' => 0,
					'mime_types' => '',
				),
				array (
					'key' => 'field_54cd86dde671b',
					'label' => 'Download Text',
					'name' => 'download_name',
					'type' => 'text',
					'instructions' => 'If you wish to use the Media name, you can leave this blank.',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
					'readonly' => 0,
					'disabled' => 0,
				),
			),
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'panel',
			),
		),
	),
	'menu_order' => 4,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
));

endif;
?>