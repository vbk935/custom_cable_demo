<?php
if( function_exists('register_field_group') ):

register_field_group(array (
	'key' => 'group_54c2ff8674eb5',
	'title' => 'Newsletter',
	'fields' => array (
		array (
			'key' => 'field_5404f7f43c65b',
			'label' => 'Newsletter PDF',
			'name' => 'newsletter_pdf',
			'prefix' => '',
			'type' => 'file',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'library' => 'all',
			'return_format' => 'array',
			'min_size' => 0,
			'max_size' => 0,
			'mime_types' => '',
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'newsletter',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'side',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => array (
	),
));

endif;
?>