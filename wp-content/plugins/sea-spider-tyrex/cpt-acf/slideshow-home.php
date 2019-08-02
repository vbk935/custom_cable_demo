<?php 
if( function_exists('register_field_group') ):

register_field_group(array (
	'key' => 'group_5536ab9bb6307',
	'title' => 'Slideshow (NEW)',
	'fields' => array (
		array (
			'key' => 'field_5549a6ea8d3f1',
			'label' => '1/2',
			'name' => '1/2',
			'prefix' => '',
			'type' => 'column',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'column-type' => '1_2',
		),
		array (
			'key' => 'field_5536abef2050d',
			'label' => 'Text or Logo header?',
			'name' => 'add_a_logo',
			'prefix' => '',
			'type' => 'radio',
			'instructions' => '',
			'required' => 1,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array (
				'logo' => 'Logo',
				'text' => 'Text',
			),
			'other_choice' => 0,
			'save_other_choice' => 0,
			'default_value' => 'text',
			'layout' => 'vertical',
		),
		array (
			'key' => 'field_553ff54b9f379',
			'label' => 'Logo',
			'name' => 'slideshow_logo',
			'prefix' => '',
			'type' => 'image',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_5536abef2050d',
						'operator' => '==',
						'value' => 'logo',
					),
				),
			),
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'array',
			'preview_size' => 'thumbnail',
			'library' => 'all',
			'min_width' => '',
			'min_height' => '',
			'min_size' => '',
			'max_width' => '',
			'max_height' => '',
			'max_size' => '',
			'mime_types' => '',
		),
		array (
			'key' => 'field_5549a7538d3f3',
			'label' => '1/2 (copy)',
			'name' => '1/2_copy',
			'prefix' => '',
			'type' => 'column',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'column-type' => '1_2',
		),
		array (
			'key' => 'field_5536ac002050e',
			'label' => 'Add A Link?',
			'name' => 'add_a_link',
			'prefix' => '',
			'type' => 'radio',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array (
				'anchor' => 'Anchor',
				'oldanchor' => 'Anchor (Using old panels)',
				'article' => 'Article (Full Page Version)',
				'external' => 'External',
				'internal' => 'Internal',
				'none' => 'None',
			),
			'other_choice' => 0,
			'save_other_choice' => 0,
			'default_value' => 'article',
			'layout' => 'horizontal',
		),
		array (
			'key' => 'field_5536ad716be1f',
			'label' => 'Internal Link',
			'name' => 'link_internal',
			'prefix' => '',
			'type' => 'page_link',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_5536ac002050e',
						'operator' => '==',
						'value' => 'internal',
					),
				),
				array (
					array (
						'field' => 'field_5536ac002050e',
						'operator' => '==',
						'value' => 'anchor',
					),
				),
				array (
					array (
						'field' => 'field_5536ac002050e',
						'operator' => '==',
						'value' => 'oldanchor',
					),
				),
			),
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'post_type' => array (
				0 => 'page',
			),
			'taxonomy' => '',
			'allow_null' => 1,
			'multiple' => 0,
		),
		array (
			'key' => 'field_5536adbe6be20',
			'label' => 'External Link',
			'name' => 'link_external',
			'prefix' => '',
			'type' => 'url',
			'instructions' => 'Don\'t forget the HTTP://',
			'required' => 0,
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_5536ac002050e',
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
			'placeholder' => '',
		),
		array (
			'key' => 'field_5536ae406be22',
			'label' => 'Panel Anchor',
			'name' => 'link_anchor',
			'prefix' => '',
			'type' => 'post_object',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_5536ac002050e',
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
			'taxonomy' => '',
			'allow_null' => 1,
			'multiple' => 0,
			'return_format' => 'object',
			'ui' => 1,
		),
		array (
			'key' => 'field_5551bd1acbbea',
			'label' => 'OLD PANEL ANCHOR',
			'name' => 'old_panel',
			'prefix' => '',
			'type' => 'text',
			'instructions' => 'If you are still using the old style panels, then you may need to add the anchor text in here.',
			'required' => 0,
			'conditional_logic' => array (
				array (
					array (
						'field' => 'field_5536ac002050e',
						'operator' => '==',
						'value' => 'oldanchor',
					),
				),
			),
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '#',
			'append' => '',
			'maxlength' => '',
			'readonly' => 0,
			'disabled' => 0,
		),
		array (
			'key' => 'field_553fdfec12a32',
			'label' => 'Background Color',
			'name' => 'background_color',
			'prefix' => '',
			'type' => 'select',
			'instructions' => 'Color is set to be the site color, this will override it.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array (
				'default' => 'Site Default',
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
			'key' => 'field_5549a7638d3f4',
			'label' => 'col',
			'name' => 'col',
			'prefix' => '',
			'type' => 'column',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'column-type' => '1_1',
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'article',
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

endif;
?>