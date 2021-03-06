<?php
if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array (
	'key' => 'group_54dd2365801f9',
	'title' => 'Article Sidebar',
	'fields' => array (
		array (
			'key' => 'field_54dd249273f7f',
			'label' => 'Which Side',
			'name' => 'which_side',
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
				'left' => 'Left',
				'right' => 'Right',
			),
			'other_choice' => 0,
			'save_other_choice' => 0,
			'default_value' => '',
			'layout' => 'horizontal',
		),
		array (
			'key' => 'field_54eca4c134ae3',
			'label' => 'Width',
			'name' => 'image_width',
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
				'col-xs-2' => 'Small',
				'col-xs-3' => 'Medium',
				'col-xs-4' => 'Large',
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
			'key' => 'field_54eca7590ae9b',
			'label' => 'Sidebar',
			'name' => 'sidebar',
			'type' => 'flexible_content',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'button_label' => 'Add Item',
			'min' => '',
			'max' => '',
			'layouts' => array (
				array (
					'key' => '54eca8270aea2',
					'name' => 'file',
					'label' => 'File',
					'display' => 'row',
					'sub_fields' => array (
						array (
							'key' => 'field_54eca8310aea3',
							'label' => 'File',
							'name' => 'file',
							'type' => 'file',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array (
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'return_format' => 'url',
							'library' => 'all',
							'min_size' => 0,
							'max_size' => 0,
							'mime_types' => '',
						),
						array (
							'key' => 'field_54eca84c0aea4',
							'label' => 'Image',
							'name' => 'image',
							'type' => 'image',
							'instructions' => 'If added the image will display and link to the file.',
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
						array (
							'key' => 'field_54eca8a60aea5',
							'label' => 'Text',
							'name' => 'extra_text',
							'type' => 'text',
							'instructions' => 'If added it will be the text that shows.	If there is an image already there, it will be below the image.',
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
					'min' => '',
					'max' => '',
				),
				array (
					'key' => '54eca762afbf6',
					'name' => 'image',
					'label' => 'Image',
					'display' => 'block',
					'sub_fields' => array (
						array (
							'key' => 'field_54eca77f0ae9c',
							'label' => 'Image',
							'name' => 'image_file',
							'type' => 'image',
							'instructions' => '',
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
						array (
							'key' => 'field_54eca7ac0ae9d',
							'label' => 'Link',
							'name' => 'link',
							'type' => 'url',
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
						),
						array (
							'key' => 'field_54eca7c20ae9e',
							'label' => 'Text Below',
							'name' => 'extra_text',
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
					'min' => '',
					'max' => '',
				),
				array (
					'key' => '54eca8000ae9f',
					'name' => 'text_link',
					'label' => 'Text Link',
					'display' => 'row',
					'sub_fields' => array (
						array (
							'key' => 'field_54eca80a0aea0',
							'label' => 'Link',
							'name' => 'link',
							'type' => 'url',
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
						),
						array (
							'key' => 'field_54eca8170aea1',
							'label' => 'Display Text',
							'name' => 'extra_text',
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
					'min' => '',
					'max' => '',
				),
			),
		),
		array (
			'key' => 'field_55e3dd54084a5',
			'label' => 'Clear?',
			'name' => 'clear',
			'type' => 'true_false',
			'instructions' => 'If something is pushed to the left or right it is called "Floated", when you do this you pull the item out of the flow of the document. For text this is usually nothing to worry about, but other content can get pushed under the floated item.	When that happens and you do not wish that to happen you have to "Clear" the "Float". This makes anything after the "clear" to drop down after the "floated" item has ended.	For the most part, a terrible idea that has no better way of accomplishing the same effect.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'message' => '',
			'default_value' => 0,
		),
	),
	'location' => array (
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
				'value' => 'page',
			),
		),
		array (
			array (
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'article',
			),
		),
		array (
			array (
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'press-releases',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'left',
	'instruction_placement' => 'field',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => '',
));

endif;
?>