<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provides functions for the plugin settings page in the WordPress admin.
 *
 * Settings can be accessed at WooCommerce -> Settings -> Products -> Product tables.
 *
 * @package   WooCommerce_Product_Table\Admin
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_Product_Table_Admin_Settings_Page {

	const SHORTCODE_DEFAULTS_SECTION_ID = 'product_table_shortcode_defaults';

	private $license;

	public function __construct( Barn2_Plugin_License $license ) {
		$this->license = $license;

		// Add support links on settings page
		add_action( 'woocommerce_settings_product_table_options', array( $this, 'settings_page_support_links' ) );

		// Register custom field types
		foreach ( array( 'hidden', 'help_note', 'color_size', 'settings_start', 'settings_end' ) as $field ) {
			add_action( "woocommerce_admin_field_{$field}", array( 'WC_Settings_Additional_Field_Types', "{$field}_field" ) );
		}

		// Add sections & settings
		add_filter( 'woocommerce_get_sections_products', array( $this, 'add_section' ) );
		add_filter( 'woocommerce_get_settings_products', array( $this, 'add_settings' ), 10, 2 );

		// Shortcode defaults section end
		add_action( 'woocommerce_settings_' . self::SHORTCODE_DEFAULTS_SECTION_ID . '_after', array( $this, 'shortcode_defaults_section_end' ) );

		// Sanitize settings
		add_filter( 'woocommerce_admin_settings_sanitize_option_' . $this->license->license_key_option, array( $this->license, 'save' ) );
		add_filter( 'woocommerce_admin_settings_sanitize_option_' . WCPT_Settings::OPTION_TABLE_STYLING, array( __CLASS__, 'sanitize_option_table_styling' ), 10, 3 );
		add_filter( 'woocommerce_admin_settings_sanitize_option_' . WCPT_Settings::OPTION_TABLE_DEFAULTS, array( __CLASS__, 'sanitize_option_table_defaults' ), 10, 3 );
		add_filter( 'woocommerce_admin_settings_sanitize_option_' . WCPT_Settings::OPTION_MISC, array( __CLASS__, 'sanitize_option_misc' ), 10, 3 );
	}

	public function add_section( $sections ) {
		$sections[WCPT_Settings::SECTION_SLUG] = __( 'Product tables', 'woocommerce-product-table' );
		return $sections;
	}

	public function add_settings( $settings, $current_section ) {
		// Check we're on the correct settings section
		if ( WCPT_Settings::SECTION_SLUG !== $current_section ) {
			return $settings;
		}

		$plugin_settings = array(
			array(
				'id' => 'product_table_settings_start',
				'type' => 'settings_start',
				'class' => 'product-table-settings'
			)
		);

		// License key settings.
		$plugin_settings = array_merge( $plugin_settings, array(
			array(
				'title' => __( 'Product tables', 'woocommerce-product-table' ),
				'type' => 'title',
				'id' => 'product_table_settings_license',
				'desc' => '<p>' . __( 'The following options control the WooCommerce Product Table extension.', 'woocommerce-product-table' ) . '<p>'
				. '<p>'
				. $this->barn2_link( 'kb-categories/woocommerce-product-table-kb', __( 'View Documentation', 'woocommerce-product-table' ), true ) . ' | '
				. $this->barn2_link( 'our-wordpress-plugins/support-documentation', __( 'Get Help', 'woocommerce-product-table' ), true )
				. '</p>'
			),
			array(
				'title' => __( 'License key', 'woocommerce-product-table' ),
				'type' => 'text',
				'id' => $this->license->license_key_option,
				'desc' => $this->license->get_license_key_admin_message(),
				'desc_tip' => __( 'The licence key is contained in your order confirmation email.', 'woocommerce-product-table' ),
				'class' => 'regular-text'
			)
			) );

		if ( filter_input( INPUT_GET, 'license_debug' ) ) {
			$plugin_settings[] = array(
				'type' => 'hidden',
				'id' => 'license_debug',
				'default' => '1'
			);
		}
		if ( $override = filter_input( INPUT_GET, 'license_override', FILTER_SANITIZE_STRING ) ) {
			$plugin_settings[] = array(
				'type' => 'hidden',
				'id' => 'license_override',
				'default' => $override
			);
		}

		$plugin_settings[] = array(
			'type' => 'sectionend',
			'id' => 'product_table_settings_license'
		);

		// Table design settings.
		$plugin_settings = array_merge( $plugin_settings, array(
			array(
				'title' => __( 'Table design', 'woocommerce-product-table' ),
				'desc' => __( 'Choose whether to use the default design or customize it to suit your requirements.', 'woocommerce-product-table' ),
				'type' => 'title',
				'id' => 'product_table_settings_design',
			),
			array(
				'title' => __( 'Design', 'woocommerce-product-table' ),
				'type' => 'radio',
				'id' => WCPT_Settings::OPTION_TABLE_STYLING . '[use_theme]',
				'options' => array(
					'theme' => __( 'Default', 'woocommerce-product-table' ),
					'custom' => __( 'Custom', 'woocommerce-product-table' )
				),
				'default' => 'theme',
				'class' => 'toggle-parent',
				'custom_attributes' => array(
					'data-child-class' => 'custom-style',
					'data-toggle-val' => 'custom'
				)
			),
			array(
				'type' => 'help_note',
				'desc' => __( 'Choose your custom table styles below. Any settings you leave blank will default to your theme styles.', 'woocommerce-product-table' ),
				'class' => 'custom-style'
			),
			array(
				'title' => __( 'Borders', 'woocommerce-product-table' ),
				'type' => 'color_size',
				'id' => WCPT_Settings::OPTION_TABLE_STYLING . '[border_outer]',
				'desc' => $this->get_icon( 'external-border.svg', __( 'External border icon', 'woocommerce-product-table' ) ) . __( 'External', 'woocommerce-product-table' ),
				'desc_tip' => __( 'The border for the outer edges of the table.', 'woocommerce-product-table' ),
				'class' => 'custom-style',
			),
			array(
				'type' => 'color_size',
				'id' => WCPT_Settings::OPTION_TABLE_STYLING . '[border_header]',
				/* translators: 'Header' in this context refers to the heading row of a table. */
				'desc' => $this->get_icon( 'header-border.svg', __( 'Header border icon', 'woocommerce-product-table' ) ) . __( 'Header', 'woocommerce-product-table' ),
				'desc_tip' => __( 'The border for the bottom of the header row.', 'woocommerce-product-table' ),
				'class' => 'custom-style',
			),
			array(
				'type' => 'color_size',
				'id' => WCPT_Settings::OPTION_TABLE_STYLING . '[border_cell]',
				/* translators: 'Cell' in this context refers to a cell in a table or spreadsheet. */
				'desc' => $this->get_icon( 'cell-border.svg', __( 'Cell border icon', 'woocommerce-product-table' ) ) . __( 'Cell', 'woocommerce-product-table' ),
				'desc_tip' => __( 'The border between cells in your table.', 'woocommerce-product-table' ),
				'class' => 'custom-style',
			),
			array(
				'title' => __( 'Header background', 'woocommerce-product-table' ),
				'type' => 'color',
				'id' => WCPT_Settings::OPTION_TABLE_STYLING . '[header_bg]',
				'desc_tip' => __( 'The header background color.', 'woocommerce-product-table' ),
				'placeholder' => __( 'Color', 'woocommerce-product-table' ),
				'class' => 'custom-style ',
				'css' => 'width:6.7em'
			),
			array(
				'title' => __( 'Cell background', 'woocommerce-product-table' ),
				'type' => 'color',
				'id' => WCPT_Settings::OPTION_TABLE_STYLING . '[cell_bg]',
				'desc_tip' => __( 'The main background color used for the table contents.', 'woocommerce-product-table' ),
				'placeholder' => __( 'Color', 'woocommerce-product-table' ),
				'class' => 'custom-style ',
				'css' => 'width:6.7em'
			),
			array(
				'title' => __( 'Header font', 'woocommerce-product-table' ),
				'type' => 'color_size',
				'id' => WCPT_Settings::OPTION_TABLE_STYLING . '[header_font]',
				'desc_tip' => __( 'The font used in the table header.', 'woocommerce-product-table' ),
				'min' => 1,
				'class' => 'custom-style',
			),
			array(
				'title' => __( 'Cell font', 'woocommerce-product-table' ),
				'type' => 'color_size',
				'id' => WCPT_Settings::OPTION_TABLE_STYLING . '[cell_font]',
				'desc_tip' => __( 'The font used for the table contents.', 'woocommerce-product-table' ),
				'min' => 1,
				'class' => 'custom-style',
			),
			array(
				'type' => 'sectionend',
				'id' => 'product_table_settings_design'
			)
			) );

		$default_args	 = WCPT_Settings::table_args_to_settings( WC_Product_Table_Args::$default_args );
		$link_fmt		 = '<a href="%s" target="_blank">';

		$plugin_settings = array_merge( $plugin_settings, array(
			array(
				'title' => __( 'Choosing your products', 'woocommerce-product-table' ),
				'type' => 'title',
				'id' => 'product_table_settings_selecting',
				'desc' => '<p>' . __( 'The tables will display all products by default, or you can select products based on category, tag, custom field, taxonomy, or individual products.', 'woocommerce-product-table' ) . '</p>'
				. '<p>' . $this->barn2_link( 'kb/wpt-include-exclude', __( 'Learn how to select your products', 'woocommerce-product-table' ), true ) . '</p>'
			),
			array(
				'type' => 'sectionend',
				'id' => 'product_table_settings_selecting'
			)
			) );


		$plugin_settings = array_merge( $plugin_settings, array(
			array(
				'title' => __( 'Table content', 'woocommerce-product-table' ),
				'type' => 'title',
				'id' => 'product_table_settings_content',
				'desc' => '<p>' . __( 'These options set defaults for the [product_table] shortcode. They can be overridden for individual tables when you add the shortcode to the page.', 'woocommerce-product-table' ) . '</p>'
				. '<p>' . $this->barn2_link( 'kb/product-table-options', __( 'See the full list of shortcode options', 'woocommerce-product-table' ), true ) . '</p>'
			),
			array(
				'title' => __( 'Columns', 'woocommerce-product-table' ),
				'type' => 'text',
				'id' => WCPT_Settings::OPTION_TABLE_DEFAULTS . '[columns]',
				'desc' => __( 'The default columns for your product tables.', 'woocommerce-product-table' ) . $this->barn2_link( 'kb/product-table-columns' ),
				'default' => $default_args['columns'],
				'css' => 'width:600px'
			),
			array(
				'title' => __( 'Image size', 'woocommerce-product-table' ),
				'type' => 'text',
				'id' => WCPT_Settings::OPTION_TABLE_DEFAULTS . '[image_size]',
				'desc' => __( "W x H in pixels, e.g. 70x50", 'woocommerce-product-table' ),
				'desc_tip' => __( 'You can also enter standard image sizes here such as thumbnail, shop_thumbnail, medium, etc.', 'woocommerce-product-table' ),
				'default' => $default_args['image_size'],
				'css' => 'width:200px'
			),
			array(
				'title' => __( 'Image lightbox', 'woocommerce-product-table' ),
				'type' => 'checkbox',
				'id' => WCPT_Settings::OPTION_TABLE_DEFAULTS . '[lightbox]',
				'desc' => __( 'Display product images in a lightbox', 'woocommerce-product-table' ),
				'default' => $default_args['lightbox'],
			),
			array(
				'title' => __( 'Shortcodes', 'woocommerce-product-table' ),
				'type' => 'checkbox',
				'id' => WCPT_Settings::OPTION_TABLE_DEFAULTS . '[shortcodes]',
				'desc' => __( 'Display shortcodes, HTML and other formatting inside the table content', 'woocommerce-product-table' ),
				'default' => $default_args['shortcodes']
			),
			array(
				'title' => __( 'Description length', 'woocommerce-product-table' ),
				'type' => 'number',
				'id' => WCPT_Settings::OPTION_TABLE_DEFAULTS . '[description_length]',
				'desc' => __( 'words', 'woocommerce-product-table' ),
				'desc_tip' => __( 'Enter -1 to display the full product description including formatting.', 'woocommerce-product-table' ),
				'default' => $default_args['description_length'],
				'css' => 'width:75px',
				'custom_attributes' => array(
					'min' => -1
				)
			),
			array(
				'title' => __( 'Product links', 'woocommerce-product-table' ),
				'type' => 'text',
				'id' => WCPT_Settings::OPTION_TABLE_DEFAULTS . '[links]',
				'desc' => __( 'Include links to the relevant product, category, tag, or attribute.', 'woocommerce-product-table' ) . $this->barn2_link( 'kb/product-table-links' ),
				'desc_tip' => __( "Enter all, none, or a combination of: sku, name, image, tags, categories, terms, or attributes as a comma-separated list.", 'woocommerce-product-table' ),
				'default' => $default_args['links'],
			),
			array(
				'type' => 'sectionend',
				'id' => 'product_table_settings_content'
			)
			) );

		$plugin_settings = array_merge( $plugin_settings, array(
			array(
				'title' => __( 'Loading products', 'woocommerce-product-table' ),
				'type' => 'title',
				'id' => 'product_table_settings_loading'
			),
			array(
				'title' => __( 'Lazy load', 'woocommerce-product-table' ),
				'type' => 'checkbox',
				'id' => WCPT_Settings::OPTION_TABLE_DEFAULTS . '[lazy_load]',
				'desc' => __( 'Load products one page at a time', 'woocommerce-product-table' ),
				'desc_tip' => __( 'Enable this if you have many products or experience slow page load times.<br/><strong>Warning:</strong> Lazy load has some limitations - it limits the search, sorting, and display of variations. Only use it if you definitely need it.', 'woocommerce-product-table' ) . $this->barn2_link( 'kb/lazy-load' ),
				'default' => $default_args['lazy_load'],
				'class' => 'toggle-parent',
				'custom_attributes' => array(
					'data-child-class' => 'toggle-product-limit',
					'data-toggle-val' => 0
				)
			),
			array(
				'title' => __( 'Product limit', 'woocommerce-product-table' ),
				'type' => 'number',
				'id' => WCPT_Settings::OPTION_TABLE_DEFAULTS . '[product_limit]',
				'desc' => __( 'The maximum number of products to display in one table.', 'woocommerce-product-table' ),
				'desc_tip' => __( 'This option is ignored if lazy load is enabled.', 'woocommerce-product-table' ),
				'default' => $default_args['product_limit'],
				'class' => 'toggle-product-limit',
				'custom_attributes' => array(
					'min' => -1
				),
				'css' => 'width:75px'
			),
			array(
				'title' => __( 'Caching', 'woocommerce-product-table' ),
				'type' => 'checkbox',
				'id' => WCPT_Settings::OPTION_TABLE_DEFAULTS . '[cache]',
				'desc' => __( 'Cache table contents to improve load times', 'woocommerce-product-table' ),
				'default' => $default_args['cache'],
				'class' => 'toggle-parent',
				'custom_attributes' => array(
					'data-child-class' => 'toggle-cache'
				)
			),
			array(
				'title' => __( 'Cache expires after', 'woocommerce-product-table' ),
				'type' => 'number',
				'id' => WCPT_Settings::OPTION_MISC . '[cache_expiry]',
				'desc' => __( 'hours', 'woocommerce-product-table' ),
				'desc_tip' => __( 'Your table data will be refreshed after this length of time.', 'woocommerce-product-table' ),
				'default' => 6,
				'class' => 'toggle-cache',
				'css' => 'width:75px',
				'custom_attributes' => array(
					'min' => 1,
					'max' => 9999
				)
			),
			array(
				'title' => __( 'Rows per page', 'woocommerce-product-table' ),
				'type' => 'number',
				'id' => WCPT_Settings::OPTION_TABLE_DEFAULTS . '[rows_per_page]',
				'desc' => __( 'The number of products per page of results.', 'woocommerce-product-table' ),
				'desc_tip' => __( "Use '-1' to display all products on one page of results.", 'woocommerce-product-table' ),
				'default' => $default_args['rows_per_page'],
				'css' => 'width:75px',
				'custom_attributes' => array(
					'min' => -1
				)
			),
			array(
				'type' => 'sectionend',
				'id' => 'product_table_settings_loading'
			)
			) );

		$plugin_settings = array_merge( $plugin_settings, array(
			array(
				'title' => __( 'Sorting', 'woocommerce-product-table' ),
				'type' => 'title',
				'id' => 'product_table_settings_sorting'
			),
			array(
				'title' => __( 'Sort by', 'woocommerce-product-table' ),
				'type' => 'select',
				'id' => WCPT_Settings::OPTION_TABLE_DEFAULTS . '[sort_by]',
				'options' => array(
					'menu_order' => __( 'As listed in the Products screen (menu order)', 'woocommerce-product-table' ),
					'sku' => __( 'SKU', 'woocommerce-product-table' ),
					'name' => __( 'Name', 'woocommerce-product-table' ),
					'id' => __( 'ID', 'woocommerce-product-table' ),
					'price' => __( 'Price', 'woocommerce-product-table' ),
					'popularity' => __( 'Number of sales', 'woocommerce-product-table' ),
					'reviews' => __( 'Average reviews', 'woocommerce-product-table' ),
					'date' => __( 'Date added', 'woocommerce-product-table' ),
					'modified' => __( 'Date last modified', 'woocommerce-product-table' ),
					'custom' => __( 'Other', 'woocommerce-product-table' )
				),
				'desc' => __( 'The initial sort order applied to the table.', 'woocommerce-product-table' ) . $this->barn2_link( 'kb/product-table-sort-options' ),
				'default' => $default_args['sort_by'],
				'class' => 'toggle-parent',
				'custom_attributes' => array(
					'data-child-class' => 'custom-sort',
					'data-toggle-val' => 'custom'
				)
			),
			array(
				'title' => __( 'Sort column', 'woocommerce-product-table' ),
				'type' => 'text',
				'id' => WCPT_Settings::OPTION_TABLE_DEFAULTS . '[sort_by_custom]',
				'class' => 'custom-sort',
				'desc' => __( 'Enter a column name, e.g. description, att:size, etc. Will only work when lazy load is disabled.', 'woocommerce-product-table' ),
				'css' => 'width:200px'
			),
			array(
				'title' => __( 'Sort direction', 'woocommerce-product-table' ),
				'type' => 'select',
				'id' => WCPT_Settings::OPTION_TABLE_DEFAULTS . '[sort_order]',
				'options' => array(
					'' => __( 'Automatic', 'woocommerce-product-table' ),
					'asc' => __( 'Ascending (A to Z, 1 to 99)', 'woocommerce-product-table' ),
					'desc' => __( 'Descending (Z to A, 99 to 1)', 'woocommerce-product-table' )
				),
				'default' => $default_args['sort_order']
			),
			array(
				'type' => 'sectionend',
				'id' => 'product_table_settings_sorting'
			)
			) );

		$plugin_settings = array_merge( $plugin_settings, array(
			array(
				'title' => __( 'Add to cart column', 'woocommerce-product-table' ),
				'type' => 'title',
				'id' => 'product_table_settings_cart'
			),
			array(
				'title' => __( 'Add to cart button', 'woocommerce-product-table' ),
				'type' => 'select',
				'id' => WCPT_Settings::OPTION_TABLE_DEFAULTS . '[cart_button]',
				'options' => array(
					'button' => __( 'Button only', 'woocommerce-product-table' ),
					'checkbox' => __( 'Checkbox only', 'woocommerce-product-table' ),
					'button_checkbox' => __( 'Button and checkbox', 'woocommerce-product-table' )
				),
				'desc' => __( "How 'Add to Cart' buttons are displayed in the table.", 'woocommerce-product-table' ) . $this->barn2_link( 'kb/add-to-cart-buttons' ),
				'default' => $default_args['cart_button']
			),
			array(
				'title' => __( 'Add to cart behavior', 'woocommerce-product-table' ),
				'type' => 'checkbox',
				'id' => WCPT_Settings::OPTION_TABLE_DEFAULTS . '[ajax_cart]',
				'desc' => __( 'Use AJAX when adding to the to the cart', 'woocommerce-product-table' ),
				'default' => $default_args['ajax_cart']
			),
			array(
				'title' => __( 'Quantities', 'woocommerce-product-table' ),
				'type' => 'checkbox',
				'id' => WCPT_Settings::OPTION_TABLE_DEFAULTS . '[show_quantity]',
				'desc' => __( "Show quantity selectors in the Add to Cart column", 'woocommerce-product-table' ),
				'default' => $default_args['show_quantity']
			),
			array(
				'title' => __( 'Variations', 'woocommerce-product-table' ),
				'type' => 'select',
				'id' => WCPT_Settings::OPTION_TABLE_DEFAULTS . '[variations]',
				'options' => array(
					'false' => __( 'Link to product page', 'woocommerce-product-table' ),
					'dropdown' => __( 'Dropdown lists in add to cart column', 'woocommerce-product-table' ),
					'separate' => __( 'Separate rows in table (one per variation)', 'woocommerce-product-table' )
				),
				'desc' => __( 'How to display options for variable products.', 'woocommerce-product-table' ) . $this->barn2_link( 'kb/product-variations' ),
				'default' => $default_args['variations']
			),
			array(
				'title' => __( "'Add Selected' position", 'woocommerce-product-table' ),
				'type' => 'select',
				'id' => WCPT_Settings::OPTION_TABLE_DEFAULTS . '[add_selected_button]',
				'options' => array(
					'top' => __( 'Above table', 'woocommerce-product-table' ),
					'bottom' => __( 'Below table', 'woocommerce-product-table' ),
					'both' => __( 'Above and below table', 'woocommerce-product-table' )
				),
				'desc' => __( "The position of the 'Add Selected To Cart' button for adding multiple products.", 'woocommerce-product-table' ),
				'desc_tip' => __( "Only applicable if using 'Checkbox' or 'Button + checkbox' for the add to cart buttons", 'woocommerce-product-table' ),
				'default' => $default_args['add_selected_button']
			),
			array(
				'title' => __( "'Add Selected' button text", 'woocommerce-product-table' ),
				'type' => 'text',
				//@todo: Move to misc settings
				'id' => WCPT_Settings::OPTION_TABLE_DEFAULTS . '[add_selected_text]',
				'desc' => __( "The text for the 'Add Selected To Cart' button.", 'woocommerce-product-table' ),
				'default' => WCPT_Settings::add_selected_to_cart_default_text()
			),
			array(
				'type' => 'sectionend',
				'id' => 'product_table_settings_cart'
			)
			) );

		$plugin_settings = array_merge( $plugin_settings, array(
			array(
				'title' => __( 'Table controls', 'woocommerce-product-table' ),
				'type' => 'title',
				'id' => 'product_table_settings_controls' ),
			array(
				'title' => __( 'Product filters', 'woocommerce-product-table' ),
				'type' => 'select',
				'id' => WCPT_Settings::OPTION_TABLE_DEFAULTS . '[filters]',
				'options' => array(
					'false' => __( 'Disabled', 'woocommerce-product-table' ),
					'true' => __( 'Show based on columns in table', 'woocommerce-product-table' ),
					'custom' => __( 'Custom', 'woocommerce-product-table' )
				),
				'desc' => __( 'Dropdown lists to filter the table by category, tag, attribute, or custom taxonomy.', 'woocommerce-product-table' ) . $this->barn2_link( 'kb/wpt-filters/#filter-dropdowns' ),
				'default' => $default_args['filters'],
				'class' => 'toggle-parent',
				'custom_attributes' => array(
					'data-child-class' => 'custom-search-filter',
					'data-toggle-val' => 'custom'
				)
			),
			array(
				'title' => __( 'Custom filters', 'woocommerce-product-table' ),
				'type' => 'text',
				'id' => WCPT_Settings::OPTION_TABLE_DEFAULTS . '[filters_custom]',
				'desc' => __( 'Enter your filters as a comma-separated list.', 'woocommerce-product-table' ) . $this->barn2_link( 'kb/wpt-filters/#filter-dropdowns' ),
				'desc_tip' => _x( 'E.g. categories, tags, att:color', 'toolip for search filter option', 'woocommerce-product-table' ),
				'class' => 'regular-text custom-search-filter'
			),
			array(
				'title' => __( 'Page length', 'woocommerce-product-table' ),
				'type' => 'select',
				'id' => WCPT_Settings::OPTION_TABLE_DEFAULTS . '[page_length]',
				'options' => array(
					'top' => __( 'Above table', 'woocommerce-product-table' ),
					'bottom' => __( 'Below table', 'woocommerce-product-table' ),
					'both' => __( 'Above and below table', 'woocommerce-product-table' ),
					'false' => __( 'Hidden', 'woocommerce-product-table' )
				),
				'desc' => __( "The position of the 'Show [x] products' dropdown list.", 'woocommerce-product-table' ),
				'default' => $default_args['page_length']
			),
			array(
				'title' => __( 'Search box', 'woocommerce-product-table' ),
				'type' => 'select',
				'id' => WCPT_Settings::OPTION_TABLE_DEFAULTS . '[search_box]',
				'options' => array(
					'top' => __( 'Above table', 'woocommerce-product-table' ),
					'bottom' => __( 'Below table', 'woocommerce-product-table' ),
					'both' => __( 'Above and below table', 'woocommerce-product-table' ),
					'false' => __( 'Hidden', 'woocommerce-product-table' )
				),
				'desc' => __( 'The position of the product search box.', 'woocommerce-product-table' ),
				'default' => $default_args['search_box']
			),
			array(
				'title' => __( 'Product totals', 'woocommerce-product-table' ),
				'type' => 'select',
				'id' => WCPT_Settings::OPTION_TABLE_DEFAULTS . '[totals]',
				'options' => array(
					'top' => __( 'Above table', 'woocommerce-product-table' ),
					'bottom' => __( 'Below table', 'woocommerce-product-table' ),
					'both' => __( 'Above and below table', 'woocommerce-product-table' ),
					'false' => __( 'Hidden', 'woocommerce-product-table' )
				),
				'desc' => __( "The position of the product totals, e.g. 'Showing 1 to 5 of 10 products'.", 'woocommerce-product-table' ),
				'default' => $default_args['totals']
			),
			array(
				'title' => __( 'Pagination buttons', 'woocommerce-product-table' ),
				'type' => 'select',
				'id' => WCPT_Settings::OPTION_TABLE_DEFAULTS . '[pagination]',
				'options' => array(
					'top' => __( 'Above table', 'woocommerce-product-table' ),
					'bottom' => __( 'Below table', 'woocommerce-product-table' ),
					'both' => __( 'Above and below table', 'woocommerce-product-table' ),
					'false' => __( 'Hidden', 'woocommerce-product-table' )
				),
				'desc' => __( 'The position of the paging buttons which scroll between results.', 'woocommerce-product-table' ),
				'default' => $default_args['pagination']
			),
			array(
				'title' => __( 'Pagination type', 'woocommerce-product-table' ),
				'type' => 'select',
				'id' => WCPT_Settings::OPTION_TABLE_DEFAULTS . '[paging_type]',
				'options' => array(
					'numbers' => __( 'Numbers only', 'woocommerce-product-table' ),
					'simple' => __( 'Prev/next', 'woocommerce-product-table' ),
					'simple_numbers' => __( 'Prev/next + numbers', 'woocommerce-product-table' ),
					'full' => __( 'Prev/next/first/last', 'woocommerce-product-table' ),
					'full_numbers' => __( 'Prev/next/first/last + numbers', 'woocommerce-product-table' )
				),
				'default' => $default_args['paging_type']
			),
			array(
				'title' => __( 'Reset button', 'woocommerce-product-table' ),
				'type' => 'checkbox',
				'id' => WCPT_Settings::OPTION_TABLE_DEFAULTS . '[reset_button]',
				'desc' => __( 'Show the reset button above the table', 'woocommerce-product-table' ),
				'default' => $default_args['reset_button']
			),
			array(
				'type' => 'sectionend',
				'id' => 'product_table_settings_controls'
			)
			) );

		if ( WCPT_Util::is_quick_view_pro_active() ) {
			$plugin_settings = array_merge( $plugin_settings, array(
				array(
					'title' => __( 'Quick View Pro', 'woocommerce-product-table' ),
					'type' => 'title',
					'id' => 'product_table_settings_quick_view'
				),
				array(
					'title' => __( 'Product links', 'woocommerce-product-table' ),
					'type' => 'checkbox',
					'id' => WCPT_Settings::OPTION_MISC . '[quick_view_links]',
					'desc' => __( 'Replace all links to product page with Quick View', 'woocommerce-product-table' ),
					'desc_tip' => sprintf(
						__( '%sLearn how%s to correctly configure this option.', 'woocommerce-product-table' ),
						'<a href="' . $this->barn2_url( 'kb/product-table-quick-view/#replace-links-to-the-single-product-page-with-quick-view-links' ) . '" target="_blank">',
						'</a>'
					),
					'default' => 'no'
				),
				array(
					'type' => 'sectionend',
					'id' => 'product_table_settings_quick_view'
				)
				) );
		}

		if ( WCPT_Util::is_wc_product_addons_active() ) {
			$plugin_settings = array_merge( $plugin_settings, array(
				array(
					'title' => __( 'Product Addons', 'woocommerce-product-table' ),
					'type' => 'title',
					'id' => 'product_table_settings_addons'
				),
				array(
					'title' => __( 'Addons layout', 'woocommerce-product-table' ),
					'type' => 'select',
					'options' => array(
						'block' => __( 'Vertical', 'woocommerce-product-table' ),
						'inline' => __( 'Horizontal', 'woocommerce-product-table' ),
					),
					'id' => WCPT_Settings::OPTION_MISC . '[addons_layout]',
					'desc_tip' => __( 'Should product addons display horizontally or vertically within the table?', 'woocommerce-product-table' ),
					'default' => 'block'
				),
				array(
					'title' => __( 'Addon options layout', 'woocommerce-product-table' ),
					'type' => 'select',
					'options' => array(
						'block' => __( 'Vertical', 'woocommerce-product-table' ),
						'inline' => __( 'Horizontal', 'woocommerce-product-table' ),
					),
					'id' => WCPT_Settings::OPTION_MISC . '[addons_option_layout]',
					'desc_tip' => __( 'Should individual options for each addon display horizontally or vertically?', 'woocommerce-product-table' ),
					'default' => 'block'
				),
				array(
					'type' => 'sectionend',
					'id' => 'product_table_settings_addons'
				)
				) );
		}

		$plugin_settings[] = array(
			'id' => 'product_table_settings_end',
			'type' => 'settings_end'
		);

		return $plugin_settings;
	}

	public static function sanitize_option_table_defaults( $value, $option, $raw_value ) {
		$error	 = false;
		$setting = self::get_setting_name( $option, WCPT_Settings::OPTION_TABLE_DEFAULTS );

		if ( ! $setting ) {
			return $value;
		}

		// Check for empty settings.
		if ( '' === $value ) {
			if ( in_array( $setting, array( 'columns', 'image_size', 'links' ) ) ) {
				$value = WC_Product_Table_Args::$default_args[$setting];
			} elseif ( 'add_selected_text' === $setting ) {
				$value = WCPT_Settings::add_selected_to_cart_default_text();
			}
		}

		switch ( $setting ) {
			case 'columns':
				$parsed_columns = WC_Product_Table_Args::parse_columns_arg( $value );
				if ( ! $parsed_columns ) {
					$error	 = __( 'The columns option is invalid. Please check you have entered valid column names.', 'woocommerce-product-table' );
					$value	 = '';
				}
				break;
			case 'image_size':
				$value = preg_replace( '/[^\wx\-]/', '', $value );
				break;
			case 'rows_per_page':
			case 'description_length':
			case 'product_limit':
				// Check integer settings.
				if ( 0 === (int) $value ) {
					$value = -1;
				}
				if ( ! is_numeric( $value ) || (int) $value < -1 ) {
					$value = WC_Product_Table_Args::$default_args[$setting];
				}
				break;
		}

		if ( $error ) {
			WC_Admin_Settings::add_error( $error );
		}

		return $value;
	}

	public static function sanitize_option_table_styling( $value, $option, $raw_value ) {

		if ( 'color_size' === $option['type'] && ! empty( $value['color'] ) ) {
			$value['color'] = sanitize_hex_color( $value['color'] );
		} elseif ( 'color' === $option['type'] && ! empty( $value ) ) {
			$value = sanitize_hex_color( $value );
		}

		return $value;
	}

	public static function sanitize_option_misc( $value, $option, $raw_value ) {
		$setting = self::get_setting_name( $option, WCPT_Settings::OPTION_MISC );

		if ( ! $setting ) {
			return $value;
		}

		if ( 'cache_expiry' === $setting ) {
			$value = absint( $value );
		}

		return $value;
	}

	private static function get_setting_name( $option, $option_name ) {
		$option_name_array = array();
		parse_str( $option['id'], $option_name_array );

		return isset( $option_name_array[$option_name] ) ? key( $option_name_array[$option_name] ) : false;
	}

	public function shortcode_defaults_section_end() {
		?>
		<p>
			<?php echo sprintf( __( 'Please see the Knowledge Base for the %sfull list of shortcode options%s.', 'woocommerce-product-table' ), sprintf( '<a href="%s" target="_blank">', $this->barn2_url( 'kb/product-table-options' ) ), '</a>' ); ?>
		</p>
		<?php
	}

	public function settings_page_support_links() {
		?>
		<p>
			<a href="https://barn2.co.uk/kb-categories/wpt-getting-started/" target="_blank"><?php _e( 'Getting Started Guide', 'woocommerce-product-table' ); ?></a> |
			<a href="https://barn2.co.uk/kb-categories/woocommerce-product-table-kb/" target="_blank"><?php _e( 'Knowledge Base', 'woocommerce-product-table' ); ?></a>
		</p>
		<?php
	}

	private function get_icon( $icon, $alt_text = '' ) {
		return sprintf( '<img src="%s" alt="%s" width="20" height="20" style="display:inline-block;position:relative;top:5px;padding:0 12px 0 8px;" />', WCPT_Util::get_asset_url( 'images/' ) . $icon, $alt_text );
	}

	private function barn2_link( $page_slug, $link_text = '', $external = false ) {
		if ( empty( $link_text ) ) {
			$link_text = __( 'Read more', 'woocommerce-product-table' );
		}
		$external_icon = $external ? '<span class="dashicons dashicons-external"></span>' : '';
		return sprintf( ' <a href="%1$s" target="_blank">%2$s%3$s</a>', $this->barn2_url( $page_slug ), esc_html( $link_text ), $external_icon );
	}

	private function barn2_url( $page_slug ) {
		return esc_url( 'https://barn2.co.uk/' . ltrim( $page_slug, '/' ) );
	}

}

// class WC_Admin_Product_Table_Settings

