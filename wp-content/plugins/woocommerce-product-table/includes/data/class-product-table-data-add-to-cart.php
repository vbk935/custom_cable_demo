<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets data for the add to cart column.
 *
 * @package   WooCommerce_Product_Table\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Product_Table_Data_Add_To_Cart extends Abstract_Product_Table_Data {

	const MULTI_CART_FORM_ID = 'multi-cart';

	private $variations;
	private $quantities;
	private $cart_button;
	private $multi_cart;
	private $addons;

	public function __construct( $product, $variations = false, $quantities = false, $cart_button = 'button', $multi_cart = false ) {
		parent::__construct( $product );

		$this->variations	 = $variations;
		$this->quantities	 = $quantities;
		$this->cart_button	 = $cart_button;
		$this->multi_cart	 = $multi_cart;
	}

	public function get_data() {
		$purchasable_from_table = $this->product->is_purchasable() && $this->product->is_in_stock();

		// If variations are disabled then variable products cannot be purchased.
		if ( 'variable' === $this->product->get_type() && 'dropdown' !== $this->variations ) {
			$purchasable_from_table = false;
		}

		// Composite products cannot be purchased from table.
		if ( in_array( $this->product->get_type(), array( 'bundle', 'composite', 'variable-subscription' ) ) ) {
			$purchasable_from_table = false;
		}

		$purchasable_from_table = apply_filters( 'wc_product_table_product_purchasable_from_table', $purchasable_from_table, $this->product );

		ob_start();
		?>
		<div class="<?php echo esc_attr( $this->get_add_to_cart_class() ); ?>">
			<?php
			if ( $purchasable_from_table ) {
				woocommerce_template_single_add_to_cart();
			} else {
				// e.g out of stock, grouped product, external product, etc.
				woocommerce_template_loop_add_to_cart();
			}

			if ( $this->multi_cart ) :
				?>
				<div class="multi-cart-check">
					<input type="checkbox" name="product_ids[]" value="<?php echo esc_attr( $this->get_product_id() ); ?>"<?php
					if ( ! $purchasable_from_table ) {
						echo ' disabled="disabled"';
					}
					?> form="<?php echo esc_attr( self::MULTI_CART_FORM_ID ); ?>" />
						   <?php $this->add_multi_cart_hidden_fields(); ?>
				</div>
			<?php endif; ?>
		</div>

		<?php
		return apply_filters( 'wc_product_table_data_add_to_cart', ob_get_clean(), $this->product );
	}

	private function get_add_to_cart_class() {
		$cart_wrapper_class = array( 'add-to-cart-wrapper' );

		if ( $this->multi_cart ) {
			$cart_wrapper_class[] = 'multi-cart';
		}
		if ( 'checkbox' === $this->cart_button ) {
			$cart_wrapper_class[] = 'no-cart-button';
		}
		if ( ! $this->quantities ) {
			$cart_wrapper_class[] = 'no-quantity';
		}

		if ( WCPT_Util::is_wc_product_addons_active() ) {
			$misc_settings = WCPT_Settings::get_setting_misc();

			if ( ! empty( $misc_settings['addons_layout'] ) ) {
				$cart_wrapper_class[] = esc_attr( 'addons-' . $misc_settings['addons_layout'] );
			}

			if ( ! empty( $misc_settings['addons_option_layout'] ) ) {
				$cart_wrapper_class[] = esc_attr( 'addons-options-' . $misc_settings['addons_option_layout'] );
			}
		}

		return implode( ' ', apply_filters( 'wc_product_table_add_to_cart_class', $cart_wrapper_class ) );
	}

	private function add_multi_cart_hidden_fields() {
		$data = array(
			'quantity' => apply_filters( 'woocommerce_quantity_input_min', wcpt_get_min_purchase_quantity( $this->product ), $this->product )
		);

		// Variation data
		if ( $this->variations && 'variable' === $this->product->get_type() ) {
			$data['variation_id'] = 0;

			foreach ( array_keys( WCPT_Util::get_variation_attributes( $this->product ) ) as $attribute ) {
				$attribute_name			 = 'attribute_' . sanitize_title( $attribute );
				$data[$attribute_name]	 = '';
			}
		} elseif ( $this->variations && 'variation' === $this->product->get_type() ) {
			$data['variation_id']	 = $this->get_product_id();
			$data['parent_id']		 = wcpt_get_parent_id( $this->product );

			foreach ( WCPT_Util::get_variation_attributes( $this->product ) as $attribute => $value ) {
				$attribute_name			 = 'attribute_' . sanitize_title( str_replace( 'attribute_', '', $attribute ) );
				$data[$attribute_name]	 = $value;
			}
		}

		// Product Addons data
		if ( $product_addons = WCPT_Util::get_product_addons( $this->get_product_id() ) ) {
			foreach ( $product_addons as $addon ) {
				$key = 'addon-' . $addon['field_name'];

				if ( 'checkbox' === $addon['type'] ) {
					if ( ! empty( $addon['options'] ) ) {
						foreach ( $addon['options'] as $option_key => $option ) {
							$sub_key		 = $key . '[' . $option_key . ']';
							$data[$sub_key]	 = '';
						}
					}
				} else {
					if ( 'multiple_choice' === $addon['type'] && 'radiobutton' === $addon['display'] ) {
						// Data key for radio buttons needs to end with [] to match the one in the cart form.
						$key .= '[]';
					}

					$data[$key] = '';
				}
			}
		}

		// Filter data to allow more to be added
		$data = apply_filters( 'wc_product_table_multi_add_to_cart_data', $data, $this->product );

		// Loop through each piece of cart data and render a hidden field
		foreach ( $data as $name => $value ) {
			$bracket_pos = strpos( $name, '[' );
			if ( false === $bracket_pos ) {
				$bracket_pos = strlen( $name );
			}

			$name_key = '[' . substr_replace( $name, ']', $bracket_pos, 0 );
			if ( ']' !== substr( $name_key, -1 ) ) {
				$name_key .= ']';
			}

			// Render the hidden field - we need to use 'p' in front of product ID to allow serializeObject in JS to work.
			printf( '<input type="hidden" name="cart_data[p%1$u]%2$s" data-input-name="%3$s" value="%4$s" form="%5$s" />', esc_attr( $this->get_product_id() ), esc_attr( $name_key ), esc_attr( $name ), esc_attr( $value ), esc_attr( self::MULTI_CART_FORM_ID ) );
		}
	}
}
