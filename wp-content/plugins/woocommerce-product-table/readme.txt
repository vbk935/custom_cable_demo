=== WooCommerce Product Table ===
Contributors: andykeith, barn2media
Tags: woocommerce, table, product, list, grid
Requires at least: 4.4
Tested up to: 5.1.1
Requires PHP: 5.3
Stable tag: 2.4.1
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Display and purchase WooCommerce products from a searchable and sortable table. Filter by anything.

== Description ==

WooCommerce Product Table is a WordPress plugin that lets you display your WooCommerce products
in a searchable and sortable table or list.

Perhaps you want a more compact list of your WooCommerce products to use as a trade store or
product catalogue? Do you have lots of products which you want to display in a user-friendly view,
with instant searching and filtering? Do you want your customers to be able to click through to
view each product, or select a quantity and add to cart straight from the product table?

Now you can do all this and more with WooCommerce Product Table!

== Installation ==

1. Download the plugin from the Order Confirmation page or using the link in your order email
1. Go to Plugins -> Add New -> Upload and select the plugin ZIP file.
1. Once installed, click to Activate the plugin
1. Enter your license key under WooCommerce -> Settings -> Products -> Product Tables
1. Create or edit a page (or post) and add the shortcode `[product_table]` and customise as you wish using the various [shortocde options](https://barn2.co.uk/our-wordpress-plugins/woocommerce-product-table-documentation)
1. View the page on your website!

== Frequently Asked Questions ==

Please refer to [our support page](https://barn2.co.uk/our-wordpress-plugins/woocommerce-product-table-docs-support).

== Changelog ==

= 2.4.1 =
Release date 16 April 2019

 * New: Support for sorting by custom field with lazy load.
 * New: Support for WooCommerce 3.6.
 * Fix: Fix issue when sorting by date custom field when using the product limit option.
 * Fix: WooCommerce Product Addons - fix issue when using the Short Text addon when bulk adding products to cart.
 * Fix: Fix bug with rating widget when filtering by 2 or more ratings.
 * Fix: Remove default mediaelement stylesheet when using X theme.

= 2.4 =
Release date 27 February 2019

 * New: Support for WooCommerce Quick View Pro plugin.
 * Fix: Ensure variations for a variable product are valid when filtering them for the filter widgets.
 * Fix: Ensure price sorting works correctly when prices are entered without decimals.
 * Fix: Ensure sorting for date custom field works correctly when 1 or more posts is missing custom field data.
 * Fix: Ensure global $product is reset after adding products to table.
 * Change default links option to 'all' (will not affect existing product tables).
 * Styling tweaks and theme improvements.
 * Tested up to WP 5.1 and WC 3.5.5.

= 2.3.1 =
Release date 19 December 2018

 * Tested up to WP 5.0.1 and WC 3.5.2, including testing with new block editor (Gutenberg).
 * Fix: Prevent accidental update of images added via custom field when selecting variations.
 * Fix: Prevent images bleeding beyond width of table on mobile.
 * Fix: Ensure correct product limits and totals in search results when using archive-product.php.
 * Fix: Ensure responsive + / - icon is visible when first column is empty.
 * Fix: Formatting and sorting of date custom fields in EU/AU date format (dd/mm/yyyy or dd/mm/yy).
 * Fix: Ensure sorting for date custom field works correctly when 1 or more products is missing custom field data.
 * Fix: Ensure custom taxonomies used as dates are correctly formatted when using the date_format option.
 * Dev: New filters 'wc_product_table_custom_field_is_eu_au_date' and 'wc_product_table_taxonomy_is_eu_au_date'.

= 2.3 =
Release date 16 November 2018

 * New: Support for WooCommerce Product Addons v3.
 * New: Sorting by SKU now always defaults to alphabetical sorting - use the 'wc_product_table_use_numeric_skus' filter to use numerical sorting.
 * Fix: 'button' column was broken after 2.2.5 update.
 * Tweak: Improved table reset when using product addons.
 * Tweak: Load scripts for 'add-to-cart' and 'image' columns only when column is present in table.
 * Tweak: Better support for custom columns and overriding data for default columns.

= 2.2.5 =
Release date 22 October 2018

 * Fix: Fatal error in PHP 5.5 and below.

= 2.2.4 =
Release date 21 October 2018

 * Added Chinese translations (simplified and traditional).
 * Tested with WooCommerce 3.5.
 * Fix: Bug in product query when selecting from multiple categories, tags, or terms in shortcode and applying filter dropdowns.
 * Fix: Bug with lazy load which prevented cache being loaded when sorting by date.
 * Fix: Incompatibility with servers running PHP 5.5 and below.
 * Fix: Prevent invalid columns being entered on settings page.
 * Fix: No data being added for certain attribute columns when using separate variations.
 * Fix: Attribute dropdown filters didn't include all values when using separate variations.
 * Fix: WooCommerce Product Addons - fix bug with display of product-specific addons.
 * Fix: WooCommerce Product Addons - intermittent add to cart bug when adding multiple products.
 * Tweak: Improve validation of plugin settings and shortcode options.
 * Tweak: Minor styling improvements for add to cart column.
 * Tweak: Improve styling for product add-ons.
 * Tweak: Minor changes to settings page.

= 2.2.3 =
Release date 13 September 2018

 * Fix: Improve reliability of search by SKU for lazy load.
 * Fix: Search by SKU was overriding custom field query when using "cf" shortcode option.
 * Fix: Bug with caching of product totals when using lazy load.
 * Fix: Bug with product totals and pagination when searching with lazy load.
 * Fix: Bug with table reset when using filter widgets with lazy load.
 * Fix: Ensure initial search term is restored when resetting table.
 * Fix: Improve generation of table IDs to avoid clashes.
 * Fix: Improve validation of columns and search term shortcode options.
 * Fix: Improve theme compatibility.
 * Fix: Kallyas theme - fix javascript error when adding multiple products to cart.
 * Tweak: Image column no longer links to single product page by default.
 * Dev: Improve table caching code.
 * Dev: New filter to adjust minimum search term length.

= 2.2.2 =
Release date 3 September 2018

 * Fix: Compatibility issue with WooCommerce Ajax Filters plugin by BeRocket.

= 2.2.1 =
Release date 31 August 2018

 * Fix: Javascript error after plugin update when caching enabled and table has dropdown filters.

= 2.2 =
Release date 31 August 2018

 * New: Allow sorting and searching by SKU when using lazy load.
 * Fix: When adding multiple products and 'redirect to cart' option is enabled in WooCommerce, it will now correctly redirect to the cart.
 * Fix: ID column now correctly supported in 'links' option.
 * Fix: Bug when adding variable products which have attribute slugs that contain non-Roman characters (e.g. Hebrew, Russian, etc).
 * Fix: Bug with filter dropdowns when slugs contained entirely numeric values.
 * Fix: Fix date parsing for custom fields, to ensure date columns are sorted correctly.
 * Fix: Reintroduce code which restricted dropdown filters when 'category' or 'term' option is set.
 * Tweak: Improve sanitizing for color settings in admin and add placeholder text.
 * Tweak: Remove extra database call to get product total when using lazy load.
 * Tweak: CSS improvements for RTL languages.
 * Tweak: Improve column headings for text attributes.
 * Tweak: Date columns no longer have to present in the table.
 * Tweak: Add wc_product_table_search_filter_class filter, to allow class to be added to filter dropdowns.
 * Dev: Restructure data retrieval code - add new Product_Table_Data interface and data classes, one for each column.
 * Dev: Improvements to WC_Product_Table class - get_table and get_data methods now allow 4 possible return types.
 * Dev: Removed inline Javascript for table config and instead use data-config and data-filters attributes on table element.
 * Dev: Improvements to filter dropdowns.

= 2.1.6 =
Release date 8 August 2018

 * New: Updated archive-product.php template to include woocommerce_before_shop_loop and woocommerce_after_shop_loop hooks. Get the latest version from the /templates directory in the plugin.
 * Fix: No longer restrict categories in dropdown filter when using the category shortcode option (or archive-template.php) as this prevents valid categories being displayed.
 * Fix: Bug with filter dropdowns where items were incorrectly removed if the item name contained a special character.
 * Fix: Bug with filter dropdowns where child items were not being removed correctly when they're not applicable to the table.
 * Fix: Improve compatibility with sites running older PHP versions.
 * Fix: Improve compatibility with Jupiter theme.
 * Dev: Added backwards compatibility for woocommerce_product_loop function.
 * Dev: Additional filters for custom field data, and disabling whether a column is searchable or sortable.

= 2.1.5 =
Release date 9 July 2018

 * New: Added full Swedish translation.
 * New: Updated Brazilian (Portuguese) translation.
 * Fix: Table sizing error caused content to extend beyond width of container.
 * Fix: Accidental redirect to category/tag/attribute page when using search on click feature.
 * Fix: JS error when using search on click and 'add to cart' column not present.
 * Fix: Remove quotes from search term when using lazy load to match main WP search.
 * Fix: Improve compatibility with WC Password Protected Categories.
 * Fix: Bug with FontAwesome icons.
 * Tweak: Updated PHPDoc comments.
 * Tweak: Updated FontAwesome to v5.1.

= 2.1.4 =
Release date 1 June 2018

 * Fix: Variation description and price was being shown twice in WooCommerce 3.4.
 * Fix: Prevent table hooks being registered multiple times when there are several tables on one page.
 * Tweak: Improve display of cart message when adding multiple products.
 * Tweak: Improve script loading.
 * Tweak: Remove before and after_add_to_cart_button hooks for variable product template as now included in WooCommerce.
 * Dev: Make $args property public in WC_Product_Table_Query and other classes.
 * Dev: Made $data_table and $hooks properties public in WC_Product_Table.
 * Dev: add_above() and add_below() functions added to data table class to allow easier customization.
 * Dev: New hooks 'wc_product_table_hooks_before_register' and 'wc_product_table_hooks_after_register'.
 * Dev: Updated license code.

= 2.1.3 =
Release date 21 May 2018

 * New: Support for Time Picker field in Advanced Custom Fields.
 * New: Complete Dutch and Hebrew translations.
 * Fix: Selecting products by ACF custom field where field value is stored as an array (e.g. checkboxes).
 * Fix: Error when 'Add Selected To Cart' text option was blank.
 * Fix: Filters and variations options being set incorrectly in some circumstances.
 * Fix: Don't add filter dropdown if there are no terms applicable to products in table.
 * Fix: Problem with lazy load which caused pagination to be set incorrectly when resetting table.
 * Fix: Problem with 'Rows per page' plugin setting when using lazy load.
 * Fix: Improve display of audio and video shortcodes in responsive child row.
 * Fix: Conflict between WooCommerce and Advanced Custom Fields which caused bug loading ACF field object.
 * Fix: Potential infinite loop when there are product tables contained within product tables.
 * Fix: Minor error in archive-template.php template and updated to sversion 3.4.
 * Fix: Potential bug when using table in archive template and plugin settings were modified.
 * Fix: Categories filter was not always displayed when the 'category' option was set.
 * Fix: Ensure multi cart is only enabled when the 'add to cart' column is present.
 * Tweak: 'search_term' option now correctly pre-fills the search box above table.
 * Tweak: Improve error handling in add to cart functions.
 * Tweak: Improve support with Beaver Builder.
 * Tweak: Improve table reset function.
 * Tweak: Improve table formatting in Jupiter theme.
 * Tweak: Add filter & action to allow custom product types to be added to cart.
 * Tweak: Remove extra database call for total product count when using lazy load.
 * Dev: Tested up to WP 4.9.6 and WC 3.4.
 * Dev: Update DataTables to version 1.10.16.
 * Dev: Update FontAwesome to version 5.

= 2.1.2 =
Release date 21 March 2018

 * New: Disabled table caching by default.
 * Tweak: Reposition 'Add Select to Cart' button to fit better with search box and filter dropdowns.
 * Tweak: New caching options added to plugin settings.
 * Tweak: Added complete Italian translation.
 * Tweak: Tested in WC 3.3.4.
 * Fix: Hide disabled or out of stock variations when using variations="separate".
 * Fix: Bug in Flatsome theme when quantity boxes are not displayed.

= 2.1.1 =
Release date 16 March 2018

 * Fix: PHP error when using custom table design option.

= 2.1 =
Release date 16 March 2018

 * New: Image lightbox for product table images using Photoswipe (requires WooCommerce 3.0 or later).
 * New: Search term shortcode option (search_term) to restrict products to specified term (not available with lazy load).
 * New: Variation description now displayed instead of product description when using separate variations.
 * New: Improve table caching and performance.
 * New: Moved multi cart form inside table controls section and improved CSS for controls.
 * New: Ignore product limit if using lazy load, unless set explicitly in shortcode.
 * Tweak: Always use smallest available image size (based on image_size option) to save bandwidth.
 * Tweak: Improve image attributes and alt tags.
 * Tweak: Remove stock quantity filter which prevented decimal quantities in stock column.
 * Tweak: Re-structure plugin settings page.
 * Tweak: Update WPML config.
 * Tweak: Added complete translations for German, Polish & Finnish.
 * Tweak: Improve RTL support.
 * Fix: Bug with default variation selection when using lazy load.
 * Fix: Bug with product add to cart checkbox when default variation is selected.
 * Fix: When ajax cart was disabled, adding simple products to the cart redirected to single product page.
 * Fix: Don't link to single product page if it's not visible.
 * Fix: Improve validation of plugin settings.
 * Fix: Bug which prevented loading of theme compat hooks.
 * Fix: Incompatibility with Avada and Salient themes which prevented quantity +/- buttons being displayed.
 * Fix: Bug in PHP 5.2 when using lazy load.
 * Various other fixes and improvements.

= 2.0.7 =
Release date 23 February 2018

 * Fix: Filter widgets not registered correctly in 2.0.6 release.

= 2.0.6 =
Release date 21 February 2018

 * Fix: Box sizing issue in Firefox and IE.
 * Fix: Quantity selector bug in Avada when two or more tables on one page.
 * Tweak: CSS tweak for quantity selector in Flatsome.
 * Tweak: Small tweaks to settings page.

= 2.0.5 =
Release date 26 January 2018

 * Tested with WooCommerce 3.3.
 * Fix: Bug with column sorting introduced in version 2.0.4.
 * Fix: Bug with attribute filter when variations="dropdown" and attribute is not used for variations
 * Fix: Bug in Shopkeeper theme where variations were not initialised correctly for standard load.
 * Tweak: Update product table widgets to reflect recent changes in WooCommerce.
 * Tweak: Add support for SelectWoo to 'Filter by Attribute' widget.

= 2.0.4 =
Release date 19 January 2018

 * New: Plugin option to set the 'Add Selected to Cart' button text.
 * Fix: Add to cart button for variable products in responsive row was not working under certain conditions.
 * Fix: Quantity selector in Enfold was not working in Safari.
 * Fix: Quantity + and - buttons in Avada were not working correctly in responsive display.
 * Fix: Date sorting was not working correctly when using date_columns option.
 * Fix: Add to cart button now always displays in responsive modal window, regardless of cart_button setting.
 * Fix: Audio and video shortcodes were initialised twice, and were not working correctly in responsive display.
 * Fix: Product add-ons were not displayed inline in responsive display when inline was selected in plugin settings.
 * Tweak: Removed 'scroll offset' and 'show footer headings' plugin options.
 * Tweak: Updated wording in plugin options and added extra links to documentation.
 * Tweak: Improve styling and compatibility in Enfold.

= 2.0.3 =
Release date 13 December 2017

 * Fix: Bug with reset button where multi select checkboxes were incorrectly ticked.
 * Fix: Bug when adding products to the cart where quantity is a fractional value (e.g. 1.5 or 0.75).
 * Fix: Bug in Avada, Flatsome, Jupiter and XStore themes with quantity + and - buttons.
 * Tweak: Minor CSS tweaks and theme compatibility changes.
 * Tweak: Change DataTables error reporting.
 * Tweak: Extra filters for product table tax query and meta query.

= 2.0.2 =
Release date 1 December 2017

 * Fix: Bug with categories filter where categories were shown in the wrong order, or weren't shown in the drop-down list.
 * Fix: Bug with categories filter when selecting a parent category - products which belong to a child of the selected category are now correctly shown in the results.
 * Fix: Bug with categories filter where selection was lost after applying a layered nav filter widget.
 * Fix: Bug with plugin settings where some settings were not correctly applied to the product tables.
 * Fix: Bug in Flatsome (and other themes) where the quantity up/down buttons were only working on the first page of results.
 * Fix: Bug with other WooCommerce plugins which allow fractional product quantities to be used (e.g. 0.25).
 * Fix: Potential edge case where $_SERVER['REQUEST_METHOD'] hasn't been set.
 * Tweak: Removed additional wrapper div so simplify HTML structure.
 * Tweak: Minor styling improvements in certain themes.
 * Tweak: Product ID now added to each row in table.

= 2.0.1 =
Release date 23 November 2017

 * Fix: Fatal error in PHP 5.6 and below.

= 2.0 =
Release date 22 November 2017

 * New: Support for WooCommerce Product Add-ons.
 * New: Category filter now sorted using correct sort order set in admin, and displays category hierarchy.
 * Code restructure and performance improvements.
 * Tested in latest versions of WordPress (4.9) and WooCommerce (3.2.5).
 * Added complete French translation.
 * Added Hebrew translation (credit: Josef Major).
 * Improved translations and number formatting across different locales.
 * Improved RTL support.
 * All translation now handled through PO files (gettext). JSON translation files removed from plugin but support kept for sites using custom JSON translation file.
 * Quantity selector no longer resets to 1 after adding to cart with AJAX.
 * Fix: Bug with 'Add selected to cart' form in Edge browser (version 15 and below) and IE version 10 & 11 when AJAX cart is disabled.
 * Fix: Allow ACF fields which store multiple values (e.g. checkbox fields) to be used as field selections with the "cf" option.
 * Fix: Bug when using column names containing a dot "." with lazy load enabled.

= 1.8.3 =
Release date 25 October 2017

 * Fix: Replace missing language files.

= 1.8.2 =
Release date 17 October 2017

 * New: Tested with WooCommerce 3.2.1.
 * New: Added Brazilian Portuguese translation (credit: Milo Moskorz).
 * Fix: Bug with logic for display of product table widgets.
 * Fix: Bug with WooCommerce (version 2.7 and below) when using the 'image' column.
 * Tweak: Improve styling with Hestia theme.
 * Tweak: Improvements to CSS when using custom border styles.

= 1.8.1 =
Release date 28 September 2017

 * Fix: Error in 1.8 update on servers running PHP 5.5 and below.

= 1.8 =
Release date 27 September 2017

 * New: Added new plugin settings, including table styling options and defaults for the [product_table] shortcode.
 * New: Added button to TinyMCE toolbar to insert the [product_table] shortcode when editing a page/post.
 * New: Added WPML config.
 * Fix: Bug with tax query when using '+' indicator in 'term' option.
 * Fix: Potential conflict caused by incorrect triggering of 'in_the_loop' property on global $wp_query.
 * Fix: Bug with 'wc_product_table_open_products_in_new_tab' filter for variable products.
 * Fix: Prevent bug when using two of the same column in table.
 * Fix: Ensure videos are displayed in correct proportions when table first loads.
 * Fix: Minor bug with date custom fields.
 * Tweak: Added support for ACF 'date_time_picker' field.
 * Tweak: Update Polish translation.
 * Tweak: Improved compatibility with Jupiter, Salient and Shopkeeper and X themes.
 * Tweak: Improved accessibility.

= 1.7.5 =
Release date 19 August 2017

 * Added Finnish translation.
 * Added 'numeric_terms' option - set to true if you use categories/terms which have numeric slugs.
 * Fix: Bug with multi cart checkboxes when using variations="separate".
 * Fix: Bug with variation data not being added to cart correctly.
 * Fix: Allow shortcodes to work in all custom field columns.
 * Fix: Bug with reset of table ordering on state load.
 * Fix: Bug with formatting of date custom fields.
 * Fix: Deprecated hook warning for WooCommerce stock filter.
 * Tweak: Added 'wc_product_table_enable_quantity_button_handler' hook to allow plus/minus quantity buttons to work in product tables.
 * Tweak: Allow sorting by ID when using lazy load.
 * Tweak: Added hook to allow ordering to be disabled for specific columns.

= 1.7.4 =
Release date 27 July 2017

 * Fix: Change markup used for 'button' column to work in all browsers.
 * Fix: Bug when search filters contain terms which have numeric slugs.
 * Fix: Bug in hook/filter registration which was causing duplicate variation data and price to be shown when using variations="dropdown".
 * Fix: Bug with quantity selectors which prevented minimum quantity being set via the WooCommerce filter.
 * Fix: Bug with automatic selection of add to cart checkboxes.
 * Fix: Bug with cart checkboxes being accessible before a variation is selected when lazy_load=true.
 * Fix: Bug with multi add to cart form when ajax_cart is disabled.
 * Tweak: Changed CSS class for button to 'product-details-button'.
 * Tweak: Improved CSS for plus/minus quantity buttons.
 * Tweak: Improved theme compatibility with Flatsome, Uncode and XStore themes.

= 1.7.3 =
Release date 10 July 2017

 * Fix: Bug with 'nowrap' class being applied to tables.
 * Fix: CSS issue with reset button.
 * Fix: Spacing issue when 'Add Selected to Cart' is below table.
 * Fix: Warning generated under certain conditions for Advanced Custom Field 'select' fields.
 * Fix: Potential JS bug when disabling responsive details row using inline_config filter.

= 1.7.2 =
Release date 8 July 2017

 * New: Added new 'button' column to link to the single product page (product details), and new 'button_text' option to set the text for these buttons.
 * New: Added new 'add_selected_button' option to control where the button for adding multiple products goes. Options: top, bottom or both.
 * New: Added CSS classes to each row in table (configurable via filter) to indicate whether a product is purchasable, out of stock, etc.
 * New: You can now sort the table by the product ID (sort_by="id") or last modified date (sort_by="modified").
 * Tweak: Added 'wc_product_table_open_products_in_new_tab' filter to allow product links in table to be opened in a new tab/window.
 * Tweak: Renamed all options beginning 'display_' (e.g. 'display_page_length') so they are shorter/easier to use. The old options have been kept for backwards-compatibility.
 * Tweak: Improve compatibility with Savoy theme.
 * Tweak: CSS improvements.
 * Fix: Table now defaults to the WooCommerce product ordering setting if the 'sort_by' option is not used.
 * Fix: Bug with Reset button when using plain permalinks.

= 1.7.1 =
Release date 26 June 2017

 * Fix: Fatal error in WooCommerce Product Add-Ons plugin when displaying pages containing product tables.

= 1.7 =
Release date 23 June 2017

 * New: Product variations can now be displayed as separate rows in the table - use variations="separate" in the product table shortcode. Note: not currently supported for lazy loaded tables.
 * New: The image for variable products now updates when selecting the variation from the dropdown list.
 * New: All text strings (including above and below table) are now passed through standard WordPress gettext functions and therefore translated via the POT file.
 * New: Added Greek tranlsation.
 * Fix: Javascript bug when 'AJAX add to cart' option is disabled in WooCommerce settings.
 * Fix: Remove duplicate 'wc_add_to_cart_params' added to script.
 * Fix: Bug with table pagination when using filter widgets with lazy load enabled.
 * Fix: Add 'woocommerce' body class to ensure filter widgets pick up theme styles.
 * Tweak: Improved support for Shopkeeper theme.
 * Tweak: Updated German translation.

= 1.6.1 =
Release date 5 June 2017

 * Fix: License key activation bug (introduced in 1.6).

= 1.6 =
Release date 3 June 2017

 * New: New widgets added for filtering products in the table - layered nav (attribute) filter, price filter, average rating and active filters.
 * New: Table state is now saved between page loads, so current, ordering, search filters, etc are maintained.
 * New: Improved compatibility with previous versions of WooCommerce.
 * New: Swedish and German (formal) translations.
 * Fix: Bug with multi add to cart for checkboxes contained in collapsed columns (e.g. on mobiles).
 * Fix: Javascript bug which prevented language options being set when multiple tables present on page.
 * Fix: Support for custom field date columns when stored as UNIX timestamps.
 * Tweak: Tested with WordPress 4.8.

= 1.5.7 =
Release date 3 May 2017

 * Tweak: Update DataTables to version 1.10.15.
 * Fix: Bug with license key validation when invalid key entered.
 * Fix: Remove Apple Pay button from product table if enabled (temporary).

= 1.5.6 =
Release date 19 April 2017

 * New: Custom field option 'cf' now accepts just the field key to allow products to be selected based on whether a custom field exists.
 * Fix: Out of stock products and hidden catalogue products were showing in table in WC 3.0. Change to use new visibility taxonomy.
 * Fix: Always pass credentials for AJAX requests to ensure session is maintained.
 * Tweak: Update license code.

= 1.5.5 =
Release date 12 April 2017

 * Fix: Include support for custom product types for AJAX add to cart.
 * Fix: Improve theme compatibility for AJAX add to cart.
 * Tweak: Update DataTables to version 1.10.13.

= 1.5.4 =
Release date 6 April 2017

 * New: Ensure plugin is compatible with WooCommerce v3.0.
 * New: Added support for ACF Repeater fields.
 * New: Added template tags 'wc_get_product_table' and 'wc_the_product_table' for easier use in themes/plugins and WooCommerce templates.
 * Fix: Ensure width of image column matches the image size used.
 * Fix: Compatibility with date picker fields ACF Pro.
 * Fix: Multi cart will now add products from multiple results pages, if products on more than one page are selected.
 * Fix: Bug with custom column names appearing in search filter drop-downs.
 * Tweak: Replaced + / - icons for collapsed columns with FontAwesome icons.
 * Tweak: Removed $table parameter from 'wc_product_table_shortcode_output' filter.
 * Tweak: Added filters for separator used in item lists (e.g. product categories).
 * Tweak: Improvements for table reset button.

= 1.5.3 =
Release date 31 March 2017

 * Fix: Reset product variations after adding to cart using AJAX.
 * Fix: Bug with multi cart when product attribute names contained upper case characters.
 * Fix: Bug with displaying several add to cart buttons when multiple tables on one page.
 * Several other tweaks and bug fixes.

= 1.5.2 =
Release date 23 February 2017

 * Fix: Bug with search filters and search on click when using lazy load option.
 * Fix: Bug with search filters which prevented first search term in table being added to list.
 * Fix: Intermittent bug with boolean shortcode options.
 * Tweak: Ensure shortcode doesn't run in admin.
 * Tweak: Display default variation for variable products.
 * Tweak: Ensure shortcode doesn't run on search results page.

= 1.5.1 =
Release date 11 February 2017

 * New: Added translations for Spanish, French, German, Italian, Danish, Polish and Norweigen.
 * Fix: Bug with responsive display functions (responsive_display="modal" or "child_row_visible").
 * Fix: Hidden columns are no loner shown in modal child row display.
 * Fix: Ensure WooCommerce add to cart script is always loaded when ajax_cart="true".
 * Fix: Bug with AJAX add to cart feature, where requests would fail on the second (and subsequent) attempts.
 * Fix: Bug with date format for date custom fields.
 * Fix: Search filters now work for hidden columns.
 * Fix: Bug with license code.
 * Tweak: Restructure Javascript array functions to prevent issues with other frameworks.
 * Tweak: Added filter 'wc_product_table_acf_value' for ACF custom field values.
 * Tweak: Added filter 'wc_product_table_inline_config' to modify inline table config.
 * Tweak: Replaced ID with WC_Product_Table object in filter hooks.

= 1.5 =
Release date 8 February 2017

 * New: AJAX add to cart functionality. AJAX is now used whenever products are added to the cart, including when adding multiple products. You can
disable this by setting shortcode option ajax_cart=false.
 * New: Support for custom taxonomies. You can now add custom taxonomies to the table or select products by custom taxonomy term. See documentation for details.
 * New: 'term' shortcode option to allow filtering by custom taxonomy term.
 * New: 'cf' shortcode option to allow table to be filtered by custom field values.
 * Fix: If search filter is present as a column in table, use data in column as items for drop-down.
 * Fix: Betting error handling in Javascript code.
 * Fix: Bug with search filters and 'search on click' searching.
 * Fix: Bug with custom taxonomy column heading.
 * Tweak: Replace Dashicons with Font Awesome.

= 1.4.1 =
Release date 31 January 2017

 * Fix: Javascript bug when not displaying variations.
 * Fix: Small bug with table config code for servers running older versions of PHP.

= 1.4 =
Release date 30 January 2017

 * New: Multiple add to cart. You can now select multiple products in the table and add them to the cart with one click. See documentation for details.
 * New: Added 'display_reset_button' option to show or hide the reset button above the table.
 * New: Added column classes to every row in table to allow easier styling of table contents.
 * New: Deactivate license button on plugin settings page to make it easier to switch sites or move from development to production.
 * Fix: Change Advanced Custom Fields code to ensure any theme-added hooks run.
 * Fix: Bug with select, text area and WYSIWYG fields in ACF.
 * Fix: Bug with display of search filters on mobile devices.
 * Tweak: Ability to set a blank column heading. Use "blank" after column name, e.g. columns="price:blank".
 * Tweak: Format URL custom fields as HTML links.
 * Tweak: Improved support for ACF fields.
 * Tweak: Added 'wc_product_table_search_label' filter to allow the search box label to be customised.
 * Tweak: Added various filters to filter all data in the table (price, add to cart, variations, attributes, etc.)
 * Tweak: Added product ID to most filters to allow table specific filtering.
 * Tweak: Changed custom field filter to 'wc_product_table_data_custom_field' (old filter still valid but deprecated) and added $product object.
 * Tweak: Adjust table styles to enable easier theme customisation.

= 1.3.2 =
Release date 21 January 2017

 * New: Custom fields which represent dates can now be sorted in correct date order. Use new option "date_columns" to
specify which additional columns should be treated as dates, e.g. date_columns="cf:start_date,cf:end_date".
 * Fix: Prevent first column from hiding on smaller screens when using responsive_control="inline".
 * Fix: Restrict search filters to show only child terms when 'category' or 'tag' option specified.
 * Fix: Bug with custom field dates stored in European format (d/m/y).
 * Tweak: Moved inline table config directly below table element.
 * Tweak: Add dashicons dependency for plugin stylesheet.
 * Tweak: Updated license activation code.
 * Tweak: Re-structure Javacript code.

= 1.3.1 =
Release date 10 January 2017

 * Fix fatal error when displaying attribute columns and links="none".
 * Fix bug with display of date picker fields in Advanced Custom Fields.
 * Table now scrolls to top when clicking category/tag/attribute in table and search_on_click="true".

= 1.3 =
Release date 6 January 2017

 * Added support for product variations in table - use variations="true" to enable. Variations appear as drop-down lists in the 'add to cart' column.
 * Added support for product attributes. Add the attribute slug with the prefix "att:" to the shortcode, e.g. [product_table columns="name,price,att:color,att:size"]
 * The filters option now supports product attributes so the table can be filtered by any attribute.
 * The filters option is now more flexible and you can include categories, tags or attributes, even if they are not displayed as a column in your table. For
example, filters="categories,att:color". If you use filters="attributes" it will display all product attributes as filters.
 * The "sort_by" option now supports product attributes, e.g. sort_by="att:color"
 * Styling tweak to reset button.
 * Fixed bug when sorting by price column.
 * Fixed table layout issue when displaying videos in table.
 * Updated license activation code.
 * Various other bug fixes and minor improvements.

= 1.2 =
Release date 7 December 2016

* Added support for search filters (drop-down lists) to allow filtering by product category or tag. Use new option filters="true" to enable.
* Added Reset button next to search box to clear the current table search and reset filters.
* Added new option 'shortcodes' to allow shortcode content to be displayed in the table.
* Added support for WordPress embedded media using video, audio and playlist shortcodes. Use shortcodes="true" to enable.
* Added fitVids.js to enable responsive video for tables displaying video files (supports YouTube and Vimeo).
* Added support for 'search on click' feature when using lazy load.
* Added filter 'wc_product_table_custom_field_value' to filter custom field values in table.
* Tested with WordPress 4.7.
* Various bug fixes and styling tweaks.

= 1.1.1 =
Release date 24 November 2016

* Ensure quantity input in table is always '1' when first loaded.
* Fix bug with 'search on click' feature for product categories & tags. Search is now restricted to the column clicked on.
* Fix compatibility issue with WordPress versions below 4.5.
* Support links on settings page.

= 1.1 =
Release date 8 November 2016

* Added 'exclude_category' option to allow product categories to be excluded from table. Use category slugs or IDs.
* Added 'include' option to only display the products specified (use a list of IDs).
* Added 'product_limit' option to limit the total number of products displayed in the table.
* Added four new options to show or hide the page length drop-down, search box, results totals and pagination buttons.
Options are 'display_page_length', 'display_search_box', 'display_totals' and 'display_pagination' and each
can be set to either 'top', 'bottom', 'both' or 'false' to hide the element.
* Added 'paging_type' option to allow control over pagination style used - see documentation for details.
* Added 'auto_width' option to enable or disable the automatic column width calculations (default: true).
* The 'Show <x> products' drop-down now includes an 'All' option to display all products.
* The 'Show <x> products' drop-down above table now defaults to the 'rows_per_page' setting in the shortcode.
* Added support for custom fields - use cf:<field name> in the 'columns' option.
* Add support for ID column.
* Allow 'image_size' to be set using one number for both width and height (e.g. image_size=50)
* Improved table styling
* Fix conflict with class names and WooCommerce
* Pass the current post and product objects to the 'wc_product_table_custom_data' filter

= 1.0.1 =
Release date 27 October 2016

* Fix bug with lazy load option.

= 1.0 =
Release date 26 October 2016

* Initial release.