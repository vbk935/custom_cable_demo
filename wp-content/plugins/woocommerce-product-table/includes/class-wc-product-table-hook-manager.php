<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Responsible for managing the actions and filter hooks for an individual product table.
 *
 * Hooks are registered in a temporary hook environment (@see class WP_Scoped_Hooks), and only
 * apply while the data is loaded into the table.
 *
 * @package   WooCommerce_Product_Table
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_Product_Table_Hook_Manager extends WP_Scoped_Hooks {

	public $args;

	public function __construct( WC_Product_Table_Args $args, $start_hook = '', $end_hook = '' ) {
		$this->args = $args;
		parent::__construct( $start_hook, $end_hook );
	}

	public function register() {
		// Maybe add target="_blank" for add to cart buttons
		$this->add_filter( 'woocommerce_loop_add_to_cart_link', 'WCPT_Util::format_loop_add_to_cart_link' );

		// Adjust class for button when using loop add to cart template
		$this->add_filter( 'woocommerce_loop_add_to_cart_args', array( $this, 'loop_add_to_cart_args' ) );

		// Remove srcset and sizes for images in table as they don't apply (to reduce bandwidth)
		$this->add_filter( 'wp_get_attachment_image_attributes', array( $this, 'remove_image_srcset' ) );

		// Filter stock HTML
		$this->add_filter( 'woocommerce_get_stock_html', array( $this, 'get_stock_html' ), 10, 2 );

		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			// Back compat: HTML filter changed to woocommerce_get_stock_html in 3.0
			$this->add_filter( 'woocommerce_stock_html', array( $this, 'get_stock_html_legacy' ), 10, 3 );
		}

		// Wrap quantity and add to cart button with extra div
		$this->add_action( 'woocommerce_before_add_to_cart_button', array( __CLASS__, 'before_add_to_cart_button' ), 30 );
		$this->add_action( 'woocommerce_after_add_to_cart_button', array( __CLASS__, 'after_add_to_cart_button' ) );

		$this->add_filter( 'woocommerce_product_add_to_cart_text', array( __CLASS__, 'set_external_product_button_text' ), 10, 2 );
		$this->add_filter( 'woocommerce_quantity_input_args', array( __CLASS__, 'set_quantity_input_args' ), 10, 2 );

		if ( 'dropdown' === $this->args->variations ) {
			// Move variation description, price & stock below the add to cart button and variations.
			$this->remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation', 10 );
			$this->add_action( 'woocommerce_after_variations_form', array( __CLASS__, 'woocommerce_single_variation' ) );

			// Use custom template for the add to cart area for variable products.
			$this->remove_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
			$this->add_action( 'woocommerce_variable_add_to_cart', array( __CLASS__, 'woocommerce_variable_add_to_cart' ), 30 );

			// Format variation price
			$this->add_filter( 'woocommerce_get_price_html', array( __CLASS__, 'format_price_for_variable_products' ), 10, 2 );

			// Set image variation props
			$this->add_filter( 'woocommerce_available_variation', array( $this, 'variations_dropdown_set_variation_image_props' ), 10, 3 );
		} elseif ( 'separate' === $this->args->variations ) {
			// Custom add to cart for separate variations.
			$this->add_action( 'woocommerce_variation_add_to_cart', array( __CLASS__, 'woocommerce_variation_add_to_cart' ), 30 );
			$this->add_action( 'woocommerce_get_children', array( __CLASS__, 'variations_separate_remove_filtered' ), 10, 3 );
		}

		if ( $this->args->shortcodes ) {
			$this->add_filter( 'wc_product_table_data_custom_field', 'do_shortcode' );
		} else {
			$this->remove_filter( 'woocommerce_short_description', 'do_shortcode', 11 );
		}

		// Stripe extension - remove Apple Pay button. We could add support for this later - @see display_apply_pay_button for details.
		if ( class_exists( 'WC_Stripe_Apple_Pay' ) ) {
			if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
				$this->remove_action( 'woocommerce_after_add_to_cart_button', array( WC_Stripe_Apple_Pay::instance(), 'display_apple_pay_button' ), 1 );
			} else {
				$this->remove_action( 'woocommerce_after_add_to_cart_quantity', array( WC_Stripe_Apple_Pay::instance(), 'display_apple_pay_button' ), 1 );
			}
		}

		// Product Addons extension
		if ( WCPT_Util::is_wc_product_addons_active() ) {
			// Adjust template for <select> type product addons.
			$this->add_filter( 'wc_get_template', array( __CLASS__, 'product_addons_select_template' ), 10, 5 );

			// Reset the product add-ons hooks after displaying add-ons for variable products, as it affects subsequent products in the table.
			$this->add_action( 'woocommerce_after_variations_form', array( __CLASS__, 'product_addons_reset_display_hooks' ) );

			if ( isset( $GLOBALS['Product_Addon_Display'] ) ) {
				// Move the product add-on totals below the add to cart form
				$this->remove_action( 'woocommerce-product-addons_end', array( $GLOBALS['Product_Addon_Display'], 'totals' ), 10 );

				if ( defined( 'WC_PRODUCT_ADDONS_VERSION' ) && version_compare( WC_PRODUCT_ADDONS_VERSION, '3.0.0', '<' ) ) {
					$this->add_action( 'woocommerce_after_add_to_cart_button', array( __CLASS__, 'product_addons_show_totals' ) );
				} else {
					$this->add_filter( 'woocommerce_product_addons_show_grand_total', '__return_false' );
				}
			}
		}

		do_action( 'wc_product_table_hooks_before_register', $this );

		parent::register();

		do_action( 'wc_product_table_hooks_after_register', $this );
	}

	public function get_stock_html( $html, $product = false ) {
		if ( ! $product ) {
			return $html;
		}

		$types_to_check = ( 'dropdown' === $this->args->variations ) ? array( 'variable', 'variation' ) : array( 'variable' );

		// Hide stock text in add to cart column, unless it's out of stock or a variable product
		if ( ! in_array( $product->get_type(), $types_to_check ) && $product->is_in_stock() ) {
			$html = '';
		}
		return apply_filters( 'wc_product_table_stock_html', $html, $product );
	}

	// For WC < 3.0 only
	public function get_stock_html_legacy( $html, $availability = false, $product = false ) {
		return $this->get_stock_html( $html, $product );
	}

	public function loop_add_to_cart_args( $args ) {
		if ( isset( $args['class'] ) ) {
			if ( false === strpos( $args['class'], 'alt' ) ) {
				$args['class'] = $args['class'] . ' alt';
			}
			if ( ! $this->args->ajax_cart ) {
				$args['class'] = str_replace( ' ajax_add_to_cart', '', $args['class'] );
			}
		}
		return $args;
	}

	public function remove_image_srcset( $attr ) {
		unset( $attr['srcset'] );
		unset( $attr['sizes'] );
		return $attr;
	}

	public function variations_dropdown_set_variation_image_props( $variation_data, $product, $variation ) {
		if ( empty( $variation_data['image'] ) || ! is_array( $variation_data['image'] ) ) {
			return $variation_data;
		}

		// Replace thumb with correct size needed for table
		if ( ! empty( $variation_data['image']['thumb_src'] ) ) {
			$thumb = wp_get_attachment_image_src( $variation->get_image_id(), $this->args->image_size );

			if ( is_array( $thumb ) && $thumb ) {
				$variation_data['image']['thumb_src']	 = $thumb[0];
				$variation_data['image']['thumb_src_w']	 = $thumb[1];
				$variation_data['image']['thumb_src_h']	 = $thumb[2];
			}
		}

		// Caption fallback
		if ( empty( $variation_data['image']['caption'] ) ) {
			$variation_data['image']['caption'] = trim( strip_tags( wcpt_get_name( $product ) ) );
		}

		return $variation_data;
	}

	public static function format_price_for_variable_products( $price_html, $product ) {
		if ( 'variation' === $product->get_type() ) {
			$price_html = '<strong>' . $price_html . '</strong>';
		}
		return $price_html;
	}

	public static function product_addons_reset_display_hooks() {
		if ( isset( $GLOBALS['Product_Addon_Display'] ) && false === has_action( 'woocommerce_before_add_to_cart_button', array( $GLOBALS['Product_Addon_Display'], 'display' ) ) ) {
			add_action( 'woocommerce_before_add_to_cart_button', array( $GLOBALS['Product_Addon_Display'], 'display' ), 10 );
		}
	}

	public static function product_addons_select_template( $located, $template_name, $args, $template_path, $default_path ) {
		if ( 'woocommerce-product-addons' === $template_path ) {

			if ( defined( 'WC_PRODUCT_ADDONS_VERSION' ) && version_compare( WC_PRODUCT_ADDONS_VERSION, '3.0.0', '<' ) ) {
				// Back compat addons v2.
				$template_name = str_replace( 'addons/', 'addons/v2/', $template_name );
			}

			$template = plugin_dir_path( WC_Product_Table_Plugin::FILE ) . 'templates/' . $template_name;

			if ( file_exists( $template ) ) {
				$located = $template;
			}
		}
		return $located;
	}

	public static function product_addons_show_totals() {
		global $product;

		if ( isset( $GLOBALS['Product_Addon_Display'] ) ) {
			$GLOBALS['Product_Addon_Display']->totals( wcpt_get_id( $product ) );
		}
	}

	public static function simple_product_button_open_wrapper() {
		global $product;

		if ( 'simple' === $product->get_type() ) {
			echo '<div class="woocommerce-simple-add-to-cart">';
		}
	}

	public static function simple_product_button_close_wrapper() {
		global $product;

		if ( 'simple' === $product->get_type() ) {
			echo '</div>';
		}
	}

	public static function before_add_to_cart_button() {
		echo '<div class="add-to-cart-button">';
	}

	public static function after_add_to_cart_button() {
		echo '</div>';
	}

	/**
	 * When using separate variation rows with the layered nav widgets, we need to filter out variations which don't match the current search criteria.
	 *
	 * @param type $child_ids
	 * @param type $product
	 * @param type $visible_only
	 * @return type
	 */
	public static function variations_separate_remove_filtered( $child_ids, $product = false, $visible_only = false ) {
		if ( ! $child_ids || ! is_array( $child_ids ) ) {
			return $child_ids;
		}

		$child_products = array_filter( array_map( 'wc_get_product', $child_ids ) );

		if ( empty( $child_products ) ) {
			return $child_ids;
		}

		$hide_out_of_stock	 = 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' );
		$min_price			 = filter_input( INPUT_GET, 'min_price', FILTER_VALIDATE_FLOAT );
		$max_price			 = filter_input( INPUT_GET, 'max_price', FILTER_VALIDATE_FLOAT );
		$chosen_attributes	 = wcpt_get_layered_nav_chosen_attributes();

		if ( ! $hide_out_of_stock && ! is_float( $min_price ) && ! is_float( $max_price ) && ! $chosen_attributes ) {
			return $child_ids;
		}

		foreach ( $child_products as $key => $child_product ) {
			$child_attributes = $child_product->get_attributes();

			if ( $hide_out_of_stock && ! $child_product->is_in_stock() ) {
				unset( $child_ids[$key] );
				continue;
			}

			if ( $chosen_attributes ) {
				foreach ( $chosen_attributes as $attribute => $chosen_attribute ) {
					if ( isset( $child_attributes[$attribute] ) && ! empty( $chosen_attribute['terms'] ) ) {
						if ( ! in_array( $child_attributes[$attribute], $chosen_attribute['terms'] ) ) {
							unset( $child_ids[$key] );
							continue 2;
						}
					}
				}
			}

			if ( is_float( $min_price ) || is_float( $max_price ) ) {
				$price = (float) $child_product->get_price();

				if ( ( is_float( $min_price ) && $price < $min_price ) || ( is_float( $max_price ) && $price > $max_price ) ) {
					unset( $child_ids[$key] );
					continue;
				}
			}
		} // foreach product

		return array_values( $child_ids );
	}

	// Make sure external product button text is not blank
	public static function set_external_product_button_text( $button_text, $product ) {
		if ( ! $button_text && 'external' === $product->get_type() ) {
			return __( 'Buy product', 'woocommerce-product-table' );
		}
		return $button_text;
	}

	// Make sure the quantity input value matches the min_value on page load
	public static function set_quantity_input_args( $args, $product ) {
		if ( isset( $args['min_value'] ) && is_numeric( $args['min_value'] ) ) {
			$args['input_value'] = $args['min_value'];
		}
		return $args;
	}

	public static function woocommerce_single_variation() {
		global $product;

		if ( 'variable' === $product->get_type() ) {
			// Back compat: Add 'single_variation_wrap' class for compatibilitiy with WC 2.4
			$single_variation_wrap = version_compare( WC_VERSION, '2.5', '<' ) ? ' single_variation_wrap' : '';
			echo '<div class="woocommerce-variation single_variation' . $single_variation_wrap . '"></div>';
		}
	}

	/**
	 * The add to cart template for variable products.
	 *
	 * @global WC_Product $product
	 */
	public static function woocommerce_variable_add_to_cart() {
		global $product;

		// Get available variations?
		$get_variations			 = sizeof( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );
		$available_variations	 = $get_variations ? $product->get_available_variations() : false;

		do_action( 'woocommerce_before_add_to_cart_form' );
		?>

		<form class="variations_form cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo esc_attr( wcpt_get_id( $product ) ); ?>" data-product_variations="<?php echo htmlspecialchars( json_encode( $available_variations ) ); ?>">
			<?php do_action( 'woocommerce_before_variations_form' ); ?>

			<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
				<p class="stock out-of-stock"><?php _e( 'This product is currently out of stock and unavailable.', 'woocommerce-product-table' ); ?></p>
			<?php else : ?>
				<?php
				$variation_attributes	 = WCPT_Util::get_variation_attributes( $product );
				$active_filters			 = self::get_selected_variations_from_filters( $variation_attributes );
				?>
				<div class="variations">
					<?php foreach ( $variation_attributes as $attribute_name => $options ) : ?>
						<?php
						// Work out initial selection for variation
						$selected = false;
						if ( empty( $active_filters ) ) {
							// Only get the product default if we have no filters selected
							$selected = $product->get_variation_default_attribute( $attribute_name );
						} elseif ( isset( $active_filters[$attribute_name] ) ) {
							// Otherwise use active filter if we have one
							$selected = $active_filters[$attribute_name];
						}

						wc_dropdown_variation_attribute_options( array(
							'options' => $options,
							'attribute' => $attribute_name,
							'product' => $product,
							'selected' => $selected,
							'show_option_none' => WCPT_Util::get_attribute_label( $attribute_name, $product )
						) );
						?>
					<?php endforeach; ?>
				</div>

				<div class="single_variation_wrap">
					<?php
					do_action( 'woocommerce_before_single_variation' );
					do_action( 'woocommerce_single_variation' );
					do_action( 'woocommerce_after_single_variation' );
					?>
				</div>

			<?php endif; // if available variations       ?>

			<?php do_action( 'woocommerce_after_variations_form' ); ?>
		</form>

		<?php
		do_action( 'woocommerce_after_add_to_cart_form' );
	}

	private static function get_selected_variations_from_filters( $variation_attributes ) {
		$lazy_load				 = WCPT_Util::doing_lazy_load();
		$filter_widget_params	 = WCPT_Util::get_layered_nav_params( $lazy_load );

		if ( empty( $filter_widget_params ) && ! $lazy_load ) {
			return array();
		}

		$active_filters			 = array();
		$lazy_load_columns		 = $lazy_load ? filter_input( INPUT_POST, 'columns', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) : array();
		$lazy_load_column_names	 = $lazy_load_columns ? wp_list_pluck( $lazy_load_columns, 'name' ) : array();

		foreach ( array_keys( $variation_attributes ) as $attribute_name ) {
			$selected			 = false;
			$filter_widget_param = 'filter_' . sanitize_title( str_replace( 'pa_', '', $attribute_name ) );

			if ( isset( $filter_widget_params[$filter_widget_param] ) ) {
				// Filter widget takes precedence - get the first attribute value (multiple selections are comma-separated)
				$selected = strtok( wc_clean( $filter_widget_params[$filter_widget_param] ), ',' );
			} elseif ( $lazy_load_columns && ( false !== ( $attribute_col_index = array_search( 'hf_att_' . $attribute_name, $lazy_load_column_names ) ) ) ) {
				// Next check for active filter dropdown if it's lazy load request (standard loading is handled by JS)
				if ( ! empty( $lazy_load_columns[$attribute_col_index]['search']['value'] ) ) {
					$selected = $lazy_load_columns[$attribute_col_index]['search']['value'];
				}
			}

			if ( false !== $selected ) {
				$active_filters[$attribute_name] = $selected;
			}
		}

		return $active_filters;
	}

	public static function woocommerce_variation_add_to_cart() {
		global $product;

		if ( ! $product->is_purchasable() ) {
			return;
		}

		echo wc_get_stock_html( $product );

		if ( ! $product->is_in_stock() ) {
			return;
		}

		do_action( 'woocommerce_before_add_to_cart_form' );
		?>

		<form class="cart" method="post" enctype='multipart/form-data'>
			<?php
			do_action( 'woocommerce_before_add_to_cart_button' );
			do_action( 'woocommerce_before_add_to_cart_quantity' );

			wcpt_woocommerce_quantity_input( $product );

			do_action( 'woocommerce_after_add_to_cart_quantity' );
			?>

			<button type="submit" name="add-to-cart" value="<?php echo absint( wcpt_get_parent_id( $product ) ); ?>" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

			<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

			<input type="hidden" name="variation_id" value="<?php echo absint( wcpt_get_id( $product ) ); ?>" />

			<div class="variations hidden">
				<?php foreach ( WCPT_Util::get_variation_attributes( $product ) as $attribute => $value ) : ?>
					<input type="hidden" name="<?php echo esc_attr( sanitize_title( $attribute ) ); ?>" value="<?php echo esc_attr( $value ); ?>" />
				<?php endforeach; ?>
			</div>
		</form>

		<?php
		do_action( 'woocommerce_after_add_to_cart_form' );
	}

}