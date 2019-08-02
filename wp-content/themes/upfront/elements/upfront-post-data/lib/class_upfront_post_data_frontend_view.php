<?php

/**
 * Front-end rendering class.
 */
class Upfront_PostDataView extends Upfront_Object_Group {


	private $_post;
	protected $_child_instances = array();
	protected $_breakpoint = false;

	public function __construct ($data, $breakpoint = false) {
		$this->_breakpoint = $breakpoint;
		parent::__construct($data);
	}

	public function get_css_class () {
		$classes = parent::get_css_class();

		$this->get_post();

		$classes .= !empty($this->_post->ID) && is_sticky( $this->_post->ID ) ? " uf-post-data uf-post-data-sticky" : " uf-post-data";

		// if the post does not have a theme image, assign a class to denote that
		if(!empty($this->_post->ID) && !has_post_thumbnail($this->_post->ID)) {
			$classes .= ' no-feature-image';
		}

		return $classes;
	}

	public function get_post () {
		if (empty($this->_post)) {
			$data = $this->_properties_to_array();
			if (empty($data)) $data = Upfront_Post_Data_Data::get_defaults();
			$this->_post = Upfront_Post_Data_Model::spawn_post($data);
		}
		return $this->_post;
	}

	public function instantiate_child ($child_data, $idx) {
		$key = md5(serialize($child_data));
		if (!empty($this->_child_instances[$key])) return $this->_child_instances[$key];

		$view_class = upfront_get_property_value("view_class", $child_data);
		$view = $view_class
			? "Upfront_{$view_class}"
			: $this->_child_view_class
		;
		if (!class_exists($view)) $view = $this->_child_view_class;

		$this->_child_instances[$key] = new $view($child_data, $this->_data, $this);
		return $this->_child_instances[$key];
	}

	private function _properties_to_array(){
		$out = array();
		foreach($this->_data['properties'] as $prop) {
			$out[$prop['name']] = !empty($prop['value']) ? $prop['value'] : '';
		}
		return $out;
	}

	public static function default_properties () {
		return Upfront_Post_Data_Data::get_defaults();
	}

	public function get_propagated_classes () {
		$classes = array();
		foreach ($this->_child_instances as $part_view) {
			if (!is_callable(array($part_view, 'get_propagated_classes'))) continue;
			$classes = array_merge($classes, $part_view->get_propagated_classes());
		}
		return array_unique($classes);
	}

	public function get_attr () {
		$attr = parent::get_attr();
		$propagated = array($attr);
		foreach ($this->_child_instances as $part_view) {
			if (!is_callable(array($part_view, 'get_propagated_attr'))) continue;
			$propagated[] = $part_view->get_propagated_attr();
		}
		$propagated = array_values(array_unique(array_filter($propagated)));
		return empty($propagated)
			? $attr
			: join(' ', $propagated) . ' '
		;
	}

	public function get_preset() {
		$preset_map = $this->_get_preset_map($this->_data);
		$preset = $this->_get_preset($this->_data, $preset_map, $this->_breakpoint);
		return $preset;
	}
}


class Upfront_PostDataPartView extends Upfront_Object {

	private $_parent;

	private $_part_type;
	private $_part_view;
	private $_preset_id;

	private $_markup = array();

	public function __construct ($data, $parent_data = '', $parent_obj = false) {
		parent::__construct($data, $parent_data);
		if ($parent_obj !== false) {
			$this->_parent = $parent_obj;
		}

		$this->_part_type = $this->_get_property('part_type');

		$props = !empty($parent_data['properties'])
			? upfront_properties_to_array($parent_data['properties'])
			: Upfront_Post_Data_Data::get_defaults()
		;

		$props['preset'] = $this->_parent->get_preset();

		$props = Upfront_Post_Data_Data::apply_preset($props);
		$this->_preset_id = Upfront_Post_Data_Data::get_preset_id($props);

		$view_class = Upfront_Post_Data_PartView::_get_view_class($props);
		$this->_part_view = new $view_class(array_merge(
			$props,
			$parent_data
		));

	}

	public function get_markup () {
		if (empty($this->_markup)) {
			$post = $this->_parent->get_post();
			// Try to get content from plugins compatibility
			$content = apply_filters('upfront-postdata_get_markup_before', false, $post->post_type);
			if (!empty($content)) {
				// Show only content part and insert plugins rendering to it
				$this->_markup['content'] = $content;
			}

			if (empty($this->_markup)) {
				$this->_markup = $this->_part_view->get_markup($post);
			}
		}
		return isset($this->_markup[$this->get_part_type()])
			? $this->_markup[$this->get_part_type()]
			: ''
		;
	}

	public function get_part_type () {
		return $this->_part_type;
	}

	public function get_propagated_classes () {
		$part_type = $this->get_part_type();

		$cls = $this->_part_view->get_propagated_classes();
		$cls[] = $part_type;

		// Add `upost-data-object-{part type}` class to allow applying custom css per post part type
		// For each type there are part parts that need to be translated to element type class
		// Post data
		if (in_array($part_type, array('title', 'date_posted', 'content') )) {
			$cls[] = 'upost-data-object-post_data';
		// Post taxonomy
		} else if (in_array($part_type, array('categories', 'tags') )) {
			$cls[] = 'upost-data-object-taxonomy';
		// Post author
		} else if (in_array($part_type, array('author', 'author-email', 'author-url', 'author-bio', 'gravatar') )) {
			$cls[] = 'upost-data-object-author';
		// Post comments
		} else if (in_array($part_type, array('comment_count', 'comments', 'comment_form', 'comment_pagination') )) {
			$cls[] = 'upost-data-object-comments';
		} else {
			// Meta and featured image have single class that matches type
			$cls[] = 'upost-data-object-' . $part_type;
		}
		// We apply preset class on ObjectGroup, commented this so we are not adding double class
		//if (!empty($this->_preset_id)) $cls[] = esc_attr($this->_preset_id);

		return $cls;
	}

	/**
	 * Check the part view for propagated attributes other than class.
	 *
	 * @return string
	 */
	public function get_propagated_attr () {
		return is_callable(array($this->_part_view, 'get_propagated_attr'))
			? $this->_part_view->get_propagated_attr()
			: ''
		;
	}

	public function get_style_for($point, $scope, $col = false) {
		$part_type = $this->_get_property('part_type');
		$css = '';
		if ( 'content' == $part_type && $col !== false ) {
			$left_indent = $this->_part_view->get_property('left_indent');
			$right_indent = $this->_part_view->get_property('right_indent');
			$max_col = $col - intval($left_indent) - intval($right_indent);
			$variants = Upfront_ChildTheme::getPostImageVariants();
			if (is_array($variants)) foreach ( $variants as $variant ) {
				$left = intval($variant->group->left);
				$margin_left = intval($variant->group->margin_left);
				$margin_right = intval($variant->group->margin_right);
				$variant_max_col = $max_col - $margin_left - $margin_right;
				if ( isset($variant->group->float) && 'none' == $variant->group->float && $point->is_default() ) {
					$variant_max_col -= $left;
				}
				$variant_max_col = $variant_max_col > $col ? $col : $variant_max_col;
				if (0 === $col) $col = 1;
				$max_width = sprintf('%.3f%%', floor(($variant_max_col/$col*100)*1000)/1000);
				$css .= sprintf('%s #%s %s {%s}',
						'.' . ltrim($scope, '. '),
						$this->get_id(),
						'[data-variant="' . $variant->vid . '"]',
						'max-width: ' . $max_width . ';'
					) . "\n";
			}
		}
		return $css;
	}

}