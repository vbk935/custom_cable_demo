<?php

/**
 * HTML Data Table classes and functions.
 *
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   1.3
 */
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'b2_format_html_attributes' ) ) {

	/**
	 * Formats an array of attributes into a string to be used inside a HTML tag.
	 * The first attribute will contain a single space before it.
	 *
	 * E.g.
	 * <code>b2_format_html_attributes( array( 'data-thing' => 'foo', 'class' = 'test' ) )</code>
	 *
	 * would give this string:
	 *
	 * <code>' data-thing="foo" class="test"'</code>
	 *
	 * @param array $atts The attributes to format
	 * @return string The attribute string
	 */
	function b2_format_html_attributes( $atts ) {
		if ( empty( $atts ) ) {
			return '';
		}
		$result = '';

		foreach ( $atts as $name => $value ) {
			// Ignore null attributes and empty strings
			if ( '' === $value || null === $value ) {
				continue;
			}

			if ( ! is_string( $value ) ) {
				$value = var_export( $value, true );
			}

			// If attribute contains a double-quote, wrap it in single-quotes to avoid parsing errors
			if ( false === strpos( $value, '"' ) ) {
				$result .= sprintf( ' %s="%s"', $name, esc_attr( $value ) );
			} else {
				// Escape the attribute, then convert double-quotes back
				$result .= sprintf( " %s='%s'", $name, str_replace( '&quot;', '"', esc_attr( $value ) ) );
			}
		}
		return $result;
	}

}

if ( ! class_exists( 'Html_Table_Cell' ) ) {

	/**
	 * Represents a cell in a <code>Html_Table_Row</code>.
	 *
	 * @package   Util
	 * @author    Barn2 Media <info@barn2.co.uk>
	 * @license   GPL-3.0
	 * @copyright Barn2 Media Ltd
	 */
	class Html_Table_Cell {

		public $data		 = '';
		private $attributes	 = array();
		private $is_heading	 = false;

		public function __construct( $data, $attributes = array(), $is_heading = false ) {
			if ( ! is_scalar( $data ) ) {
				if ( is_array( $data ) ) {
					$data = implode( '', $data );
				} else {
					$data = serialize( $data );
				}
			}
			$this->data			 = $data;
			$this->attributes	 = $attributes ? (array) $attributes : array();
			$this->is_heading	 = (bool) $is_heading;
		}

		public function is_heading() {
			return $this->is_heading;
		}

		public function to_array() {
			return array(
				'attributes' => $this->attributes,
				'data' => $this->data
			);
		}

		public function to_html() {
			$format = $this->is_heading ? '<th%s>%s</th>' : '<td%s>%s</td>';
			return sprintf( $format, b2_format_html_attributes( $this->attributes ), $this->data );
		}

	}

}

if ( ! class_exists( 'Html_Table_Row' ) ) {

	/**
	 * Represents a row in a <code>Html_Data_Table</code>.
	 *
	 * @package   Util
	 * @author    Barn2 Media <info@barn2.co.uk>
	 * @license   GPL-3.0
	 * @copyright Barn2 Media Ltd
	 */
	class Html_Table_Row {

		private $attributes	 = array();
		private $cells		 = array();

		public function __construct( $atttributes = array() ) {
			$this->attributes = $atttributes ? (array) $atttributes : array();
		}

		public function add_cell( $data, $attributes = array(), $key = false, $is_heading = false ) {
			if ( false === $key ) {
				$this->cells[] = new Html_Table_Cell( $data, $attributes, $is_heading );
			} else {
				$this->cells[$key] = new Html_Table_Cell( $data, $attributes, $is_heading );
			}
		}

		public function length() {
			return count( $this->cells );
		}

		public function is_empty( $check_data = false ) {
			if ( 0 === $this->length() ) {
				return true;
			}
			return $check_data && '' === trim( implode( '', wp_list_pluck( $this->cells, 'data' ) ) );
		}

		public function to_html() {
			if ( $this->is_empty() ) {
				return '';
			}
			$cells = '';

			foreach ( $this->cells as $cell ) {
				$cells .= $cell->to_html();
			}
			return sprintf( '<tr%s>%s</tr>', b2_format_html_attributes( $this->attributes ), $cells );
		}

		public function to_array() {
			if ( $this->is_empty() ) {
				return array();
			}
			return array(
				'attributes' => $this->attributes,
				'cells' => array_map( array( __CLASS__, 'cell_to_array' ), $this->cells )
			);
		}

		private static function cell_to_array( $cell ) {
			return $cell->to_array();
		}

	}

}

if ( ! class_exists( 'Html_Data_Table' ) ) {

	/**
	 * Represents a HTML table. This class allows you to build a table by sequentially adding headings, rows, data, etc, and then
	 * outputting to either HTML, and array or JSON.
	 *
	 * For example, the full HTML for the table can then be obtained by calling the <link>to_html()</link> method. This makes it a
	 * much cleaner way of producing the HTML required for a table.
	 *
	 * @package   Util
	 * @author    Barn2 Media <info@barn2.co.uk>
	 * @license   GPL-3.0
	 * @copyright Barn2 Media Ltd
	 */
	class Html_Data_Table {

		private $attributes	 = array();
		private $header;
		private $footer;
		private $data		 = array();
		private $current_row;
		private $above		 = array(); // deprecated
		private $below		 = array(); // deprecated

		public function __construct() {
			$this->header		 = new Html_Table_Row();
			$this->footer		 = new Html_Table_Row();
			$this->current_row	 = new Html_Table_Row();
		}

		public function add_attribute( $name, $value ) {
			$this->attributes[$name] = $value;
		}

		public function add_header( $heading, $attributes = false, $key = false, $use_th = true ) {
			$this->header->add_cell( $heading, $attributes, $key, $use_th );
		}

		public function add_footer( $heading, $attributes = false, $key = false, $use_th = true ) {
			$this->footer->add_cell( $heading, $attributes, $key, $use_th );
		}

		public function new_row( $atts = false ) {
			if ( $this->current_row && ! $this->current_row->is_empty( true ) ) {
				$this->data[] = $this->current_row;
			}
			$this->current_row = new Html_Table_Row( $atts );
		}

		public function add_data( $data, $attributes = false, $key = false ) {
			if ( is_array( $data ) ) {
				$data = implode( '', $data );
			}
			$this->current_row->add_cell( $data, $attributes, $key );
		}

		public function get_data() {
			$this->new_row();
			return $this->data;
		}

		public function set_data( $data ) {
			$this->data = (array) $data;
		}

		/**
		 * @deprecated 1.3
		 */
		public function add_above( $above ) {
			if ( $above ) {
				$this->above[] = $above;
			}
		}

		/**
		 * @deprecated 1.3
		 */
		public function add_below( $below ) {
			if ( $below ) {
				$this->below[] = $below;
			}
		}

		public function to_html( $data_only = false ) {
			$data = '';

			foreach ( $this->get_data() as $row ) {
				$data .= $row->to_html();
			}

			if ( $data_only ) {
				return $data;
			} else {
				$thead	 = ! $this->header->is_empty() ? '<thead>' . $this->header->to_html() . '</thead>' : '';
				$tfoot	 = ! $this->footer->is_empty() ? '<tfoot>' . $this->footer->to_html() . '</tfoot>' : '';
				$tbody	 = $data ? '<tbody>' . $data . '</tbody>' : '';

				// @deprecated
				$above	 = $this->above ? implode( "\n", $this->above ) : '';
				$below	 = $this->below ? implode( "\n", $this->below ) : '';
			}
			return sprintf( '%5$s<table%1$s>%2$s%3$s%4$s</table>%6$s', b2_format_html_attributes( $this->attributes ), $thead, $tbody, $tfoot, $above, $below );
		}

		public function to_array( $data_only = false ) {
			$data	 = $this->get_data();
			$body	 = array();

			foreach ( $data as $row ) {
				$body[] = $row->to_array();
			}

			if ( $data_only ) {
				return $body;
			} else {
				return array(
					'attributes' => $this->attributes,
					'thead' => $this->header->to_array(),
					'tbody' => $body,
					'tfoot' => $this->footer->to_array(),
					'above' => $this->above,
					'below' => $this->below
				);
			}
		}

		public function to_json( $data_only = false ) {
			return wp_json_encode( $this->to_array( $data_only ) );
		}

		public function reset() {
			$this->attributes	 = array();
			$this->header		 = new Html_Table_Row();
			$this->footer		 = new Html_Table_Row();
			$this->above		 = array();
			$this->below		 = array();

			$this->reset_data();
		}

		public function reset_data() {
			$this->current_row	 = new Html_Table_Row();
			$this->data			 = array();
		}

	}

	// class Html_Data_Table
} // if class doesn't exist