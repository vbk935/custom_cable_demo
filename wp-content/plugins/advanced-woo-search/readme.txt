=== Advanced Woo Search ===
Contributors: Mihail Barinov
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=GSE37FC4Y7CEY
Tags: widget, plugin, woocommerce, search, product search, woocommerce search, ajax search, live search, custom search, ajax, shortcode, better search, relevance search, relevant search, search by sku, search plugin, shop, store, wordpress search, wp ajax search, wp search, wp search plugin, sidebar, ecommerce, merketing, products, category search, instant-search, search highlight, woocommerce advanced search, woocommerce live search, WooCommerce Plugin, woocommerce product search
Requires at least: 4.0
Tested up to: 5.2
Stable tag: 1.76
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Advanced AJAX search plugin for WooCommerce

== Description ==

Advanced Woo Search - powerful live search plugin for WooCommerce. Just start typing and you will immediately see the products that you search.

= Main Features =

* **Products search** - Search across all your WooCommerce products
* **Search in** - Search in product **title**, **content**, **excerpt**, **categories**, **tags** and **sku**. Or just in some of them
* **Settings page** - User-friendly settings page with lot of options
* **Shortcode** and **widget** - Use shortcode and widget to place search box anywhere you want
* **Product image** - Each search result contains product image
* **Product price** - Each search result contains product price
* **Terms search** - Search for product categories and tags
* **Smart ordering** - Search results ordered by the priority of source where they were found
* **Fast** - Nothing extra. Just what you need for proper work
* **Stop Words** support to exclude certain words from search.
* Supports **variable products**
* Support for your current **search page**. Plugin search results will be integrated to your current page layout.
* Automatically synchronize all products data. No need to re-index all content manually after avery change.
* Plurals support
* Diacritical marks support
* Google Analytics support
* Seamless integration option for easy replacing your current search form
* **WPML**, **Polylang**, **WooCommerce Multilingual**, **qTranslate** support
* **WPML multi-currency** support
* Custom Product Tabs for WooCommerce plugin support
* Search Exclude plugin support

= Premium Features =

Additional features available only in PRO plugin version.

[Premium Version Demo](https://advanced-woo-search.com/?utm_source=wp-repo&utm_medium=listing&utm_campaign=aws-repo)
	
* Search **results layouts**
* Search **form layouts**
* **Filters**. Switch between tabs to show different search results
* **Unlimited** amount of search form instances
* Search for custom taxonomies and attributes **archive pages**
* Support for **variable products**: show child products, parent product or both in search results.
* Product **attributes** search ( including custom attributes)
* Product **custom taxonomies** search
* Product **custom fields** search
* **Advanced settings page** with lot of options
* **Exclude/include** spicific products by its ids, taxonomies or attributes from search results
* Ability to specify **source of image** for search results: featured image, gallery, product content, product short description or set default image if there is no other images
* **Visibility/stock status option** - choose what catalog visibility and stock status must be for product to displayed in search results
* Show product **categories** and **tags** in search results
* AND or OR search logic
* **Add to cart** button in search results
* Support for [WooCommerce Brands plugin](https://woocommerce.com/products/brands/)
* Support for Advanced Custom Fields plugin

[Features list](https://advanced-woo-search.com/features/?utm_source=wp-repo&utm_medium=listing&utm_campaign=aws-repo)

== Installation ==

1. Upload advanced-woo-search to the /wp-content/plugins/ directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place the plugin shortcode [aws_search_form] into your template or post-page or just use build-in widget

== Frequently Asked Questions ==

= Knowledge Base =

You can find solutions and answers at the [Advanced Woo Search guide](https://advanced-woo-search.com/guide/?utm_source=wp-repo&utm_medium=listing&utm_campaign=aws-repo).

= How to insert search form? =

You can use build-in widget to place plugins search form to your sidebar.

Or just use shortcode for displaying form inside your post/page:

`[aws_search_form]`

Or insert this function inside php file ( often it used to insert form inside page templates files ):

`echo do_shortcode( '[aws_search_form]' );`

= Is this plugin compatible with latest version of Woocommerce? =

Yep. This plugin is always compatible with the latest version of Woocommerce?

== Screenshots ==

1. Search from front-end view
1. Search form in sidebar added as widget
2. Plugin settings page. General options
3. Plugin settings page. Search form options
4. Plugin settings page. Search results options

== Changelog ==

= 1.76 =
* Update - Markup of search form
* Dev - Add new parameters for ajax call

= 1.75 =
* Fix - Brands filter for search results page
* Fix - Polylang plugin search results page URL
* Update - filters parameters

= 1.74 =
* Fix - Issue with not working search page when using Elementor page builder
* Fix - Issue with not working search page when using Divi page builder
* Update - Order by statement for products

= 1.73 =
* Add - Relevance search for terms
* Dev – Add aws_search_terms_number filter

= 1.72 =
* Fix - Tax search exact matching bug
* Fix - Empty tax in search results bug
* Update - Settings page text

= 1.71 =
* Fix - Index table sync for WPML translations

= 1.70 =
* Dev - Update security checks
* Dev - Update nonce check

= 1.69 =
* Dev - Update security checks
* Dev - Add aws_front_data_parameters filter

= 1.68 =
* Update - Styles for plugin settings page
* Dev - Add aws_search_results_tax_archives filter
* Dev - Clear code for all unused stuff

= 1.67 =
* Dev - Add aws_search_query_array filter
* Dev - Send page url with ajax request

= 1.66 =
* Update search page support

= 1.65 =
* Fix YITH WooCommerce Ajax Product Filter plugin support

= 1.64 =
* Fix issue with Polylang plugin support
* Fix filters for search results page
* Add aws_title_search_result filter
* Add aws_excerpt_search_result filter

= 1.63 =
* Update porto theme support
* Add aws_search_tax_results filter
* Add support for old WooCommerce versions ( 2.x )

= 1.62 =
* Fix Google Analytics events
* Fix jQuery errors
* Add Enfold theme support
* Add Porto theme support
* Add aws_indexed_data filter

= 1.61 =
* Fix stopwords
* Fix markup for finded words in products content

= 1.60 =
* Update cron job action
* Update Protected Categories plugin integration
* Update B2B Market plugin integration
* Add WC Marketplace plugin integration
* Add new option to disable auto sync for index page
* Add aws_filter_yikes_woo_products_tabs_sync filter
* Add aws_search_data_params filter
* Add aws_search_pre_filter_products filter

= 1.59 =
* Add aws_search_start action
* Update caching naming
* Update cron job action. Now its must works fine with large amount of products
* Fix singular form of terms
* Add aws_search_current_lang filter
* Add B2B Market plugin support
* Add Datafeedr WooCommerce Importer plugin support
* Add option to hide price for out-of-stock products

= 1.58 =
* Add option for preventing submit of empty search form
* Add support for Protected Categories plugin
* Add aws_exclude_products filter
* Add aws_terms_exclude_product_cat filter
* Add aws_terms_exclude_product_tag filter

= 1.57 =
* Update search query string
* Fix style for search icon
* Fix search field style
* Fix clear button style

= 1.56 =
* Update stopwords list
* Add search box layout options

= 1.55 =
* Update search behavior on text paste

= 1.54 =
* Update plugin index table
* Update WooCommerce version support

= 1.53 =
* Fix bug with search results page ordering
* Add svg loading icon
* Mark featured products in search results
* Add aws_search_results_products_ids filter

= 1.52 =
* Fix terms translation for multilingual plugins
* Update special chars filter
* Add diacritic chars filter

= 1.51 =
* Update seamless integration filter hook
* Fix WPML language select

= 1.50 =
* Fix bug with cyrillic letters search

= 1.49 =
* Add option to set text for 'show more' button
* add aws_search_terms filter

= 1.48 =
* Add option to display 'Clear search results' buttom on desktop devices 

= 1.47 =
* Add seamless integration option
* Fix styling

= 1.46 =
* Add support for WPML plugin multi currency
* Fix css styles

= 1.45 =
* Fix bug with re-index process ( too much requests error )
* Add timeout for keyup event
* Fix bug with special characters search

= 1.44 =
* Make SKU string in search results translatable
* Strip some new special chars from products content
* Add 'aws_extracted_string' and 'aws_extracted_terms' filters
* Fix bug with empty excerpt

= 1.43 =
* Add 'aws_search_results_all' filter
* Update WPML string translation
* Fix bug with term_source column in index table

= 1.42 =
* Add option to display ‘View All Results’ button in the bottom of search results list
* Fix bug with stop words option
* Fix bug with links in 'noresults' fiels
* Add 'aws_search_results_products', 'aws_search_results_categories', 'aws_search_results_tags' filters

= 1.41 =
* Add new column form index table - term_id. With id help it is possible to sunc any changes in product term
* Add shortcode to settings page
* Update search page integration

= 1.40 =
* Fix bug with not working stop-words for taxonomies
* Fix bug with hided search form if its parent node has fixed layout
* Add second argument for the_title and the_content filters
* Update view of settings page

= 1.39 =
* Add option to disable ajax search results

= 1.38 =
* Add 'Clear form' buttom for search form on mobile devices
* Fix bug with not srtiped shortcodes on product description
* Fix bug with aws_reindex_table action

= 1.37 =
* Add 'aws_indexed_content', 'aws_indexed_title', 'aws_indexed_excerpt' filters

= 1.36 =
* Update re-index function
* Add support for custom tabs content made with Custom Product Tabs for WooCommerce plugin
* Add support for Search Exclude plugin

= 1.35 =
* Fix issue with position of search results box
* Add 'aws_page_results' filter
* Add support for 'order by' box in search results page
* Fix issue fix re-index timeout

= 1.34 =
* Add arrows navigation for search results
* Fix bug with php 7+ vesion

= 1.33 =
* Fix re-index bug
* Fix bug with search page

= 1.32 =
* Fix shortcodes stripping from product content
* Fix qTranslate plugin issue with product name 
* Fix reindex issue

= 1.31 =
* Add WooCommerce version check

= 1.30 =
* Add qTranslate plugin support

= 1.29 =
* Fix bug with search results page

= 1.28 =
* Add caching table to store cached search result instead of store them in wp_options table
* Fix deprecated action 'woocommerce_variable_product_sync'

= 1.27 =
* Add option to show stock status in search results
* Add 'aws_special_chars' filter

= 1.26 =
* Add Polylang plugin support

= 1.25 =
* Add markdown support for 'Nothing found' field
* Fix WPML bug

= 1.24 =
* Add plurals support
* Fix Polylang plugin conflict
* Fix SKU search bug
* Add function for cron job

= 1.23 =
* Add 'Stop-words list' option

= 1.22 =
* Fix reindex bug
* Hide empty taxonomies from search results
* Add support for old WooCommerce versions

= 1.21 =
* Fix search page switching to degault language

= 1.20 =
* Add WPML, WooCommerce Multilingual support

= 1.19 =
* Fix bugs

= 1.18 =
* Fix bugs

= 1.17 =
* Fix layout bugs
* Fix bugs with older versions of WooCommerce
* Add Google Analytics support

= 1.16 =
* Option for 'Out of stock' products
* Fix bugs

= 1.15 =
* Exclude 'Out of stock' products from search
* Fix bugs

= 1.14 =
* Fix number of search results on search page
* Exclude draft products from search
* Fix bugs

= 1.13 =
* Add support for variable products
* Fix bugs

= 1.12 =
* Fix small bugs in search results output

= 1.11 =
* Fix issue with indexing large amount of products
* Fix bag with search page query

= 1.10 =
* Update search results page
* Fix some layout issues

= 1.09 =
* Make indexing of the products content much more fuster
* Fix several bugs

= 1.08 =
* Update check for active WooCommerce plugin
* Add hungarian translation ( big thanks to hunited! )

= 1.07 =
* Exclude hidden products from search
* Update translatable strings

= 1.06 =
* Cache search results to increase search speed

= 1.05 =
* Improve search speed

= 1.04 =
* Fix issue with SKU search
* Add option to display product SKU in search results

= 1.03 =
* Add search in product terms ( categories, tags )
* Fix issue with not saving settings

= 1.02 =
* Add single page search for 'product' custom post type
* Fix problem with dublicate products in the search results

= 1.01 =
* Fix problem with result block layout

= 1.00 =
* First Release