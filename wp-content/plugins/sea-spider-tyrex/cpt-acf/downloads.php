<?php

if( function_exists('register_field_group') ):

register_field_group(array (
	'key' => 'group_54c2ff884a7fb',
	'title' => 'Downloads',
	'fields' => array (
		array (
			'key' => 'field_5408a30f1edaf',
			'label' => 'External Link',
			'name' => 'external_link',
			'prefix' => '',
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
			'key' => 'field_554bb27759fb2',
			'label' => 'Local File',
			'name' => 'PDF',
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
			'return_format' => 'array',
			'library' => 'all',
			'min_size' => '',
			'max_size' => '',
			'mime_types' => '',
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'download',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'side',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => array (
		0 => 'custom_fields',
		1 => 'discussion',
		2 => 'comments',
		3 => 'revisions',
		4 => 'author',
		5 => 'format',
		6 => 'send-trackbacks',
	),
));

endif;

?>