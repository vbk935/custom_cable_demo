
(function( $, window, document, undefined ) {
	"use strict";

	/******************************************
	 * CONSTRUCTOR
	 ******************************************/

	var ProductTable = function( $table ) {

		// Properties
		this.$table = $table;
		this.id = $table.attr( 'id' );
		this.$filters = [];
		this.$tableWrapper = [];
		this.$pagination = [];
		this.hasAdminBar = $( '#wpadminbar' ).length > 0;
		this.initialState = false;
		this.ajaxData = [];

		// Bind methods
		this.buildConfig = this.buildConfig.bind( this );
		this.checkFormAttributeSupport = this.checkFormAttributeSupport.bind( this );
		this.getDataTable = this.getDataTable.bind( this );
		this.initAddToCart = this.initAddToCart.bind( this );
		this.initFilters = this.initFilters.bind( this );
		this.initMultiCart = this.initMultiCart.bind( this );
		this.initPhotoswipe = this.initPhotoswipe.bind( this );
		this.initQuickView = this.initQuickView.bind( this );
		this.initResetButton = this.initResetButton.bind( this );
		this.initSearchOnClick = this.initSearchOnClick.bind( this );
		this.processAjaxData = this.processAjaxData.bind( this );
		this.registerMultiCartEvents = this.registerMultiCartEvents.bind( this );
		this.registerVariationEvents = this.registerVariationEvents.bind( this );
		this.resetProductAddons = this.resetProductAddons.bind( this );
		this.resetMultiCartCheckboxes = this.resetMultiCartCheckboxes.bind( this );
		this.resetQuantities = this.resetQuantities.bind( this );
		this.resetVariations = this.resetVariations.bind( this );
		this.scrollToTop = this.scrollToTop.bind( this );
		this.showHidePagination = this.showHidePagination.bind( this );

		// Register DataTables events
		$table.on( 'draw.dt', { table: this }, this.onDraw );
		$table.on( 'init.dt', { table: this }, this.onInit );
		$table.on( 'page.dt', { table: this }, this.onPage );
		$table.on( 'processing.dt', { table: this }, this.onProcessing );
		$table.on( 'responsive-display.dt', { table: this }, this.onResponsiveDisplay );
		$table.on( 'stateLoadParams.dt', { table: this }, this.onStateLoadParams );
		$table.on( 'xhr.dt', { table: this }, this.onAjaxLoad );

		// Register load event
		$( window ).on( 'load', { table: this }, this.onWindowLoad );

		// Show the table - loading class removed on init.dt
		$table.addClass( 'loading' ).css( 'visibility', 'visible' );

		// Initialise the DataTable instance
		this.getDataTable();
	};

	/******************************************
	 * STATIC PROPERTIES
	 ******************************************/

	ProductTable.blockConfig = {
		message: null,
		overlayCSS: {
			background: '#fff',
			opacity: 0.6
		}
	};

	/******************************************
	 * STATIC METHODS
	 ******************************************/

	ProductTable.addRowAttributes = function( $row ) {
		return function( key, value ) {
			if ( 'class' === key ) {
				$row.addClass( value );
			} else {
				$row.attr( key, value );
			}
		};
	};

	ProductTable.appendFilterOptions = function( $select, items, depth ) {
		depth = (typeof depth !== 'undefined') ? depth : 0;

		// Add each term to filter drop-down
		$.each( items, function( i, item ) {
			var name = item.name;
			var value = 'slug' in item ? item.slug : name;
			var pad = '';

			if ( depth ) {
				pad = Array( (depth * 2) + 1 ).join( '\u00a0' ) + '\u2013\u00a0';
			}

			$select.append( '<option value="' + value + '">' + pad + name + '</option>' );

			if ( 'children' in item ) {
				ProductTable.appendFilterOptions( $select, item.children, depth + 1 );
			}
		} );
	};

	ProductTable.flattenObjectArray = function( arr, childProp ) {
		var result = [];

		for ( var i = 0; i < arr.length; i++ ) {
			if ( typeof arr[i] !== 'object' ) {
				continue;
			}
			result.push( arr[i] );

			for ( var prop in arr[i] ) {
				if ( prop === childProp ) {
					Array.prototype.push.apply( result, ProductTable.flattenObjectArray( arr[i][prop], childProp ) );
					delete arr[i][prop];
				}
			}
		}
		return result;
	};

	ProductTable.getCurrentUrlWithoutFilters = function() {
		var url = window.location.href.split( '?' )[0];

		if ( window.location.search ) {
			var params = window.location.search.substring( 1 ).split( '&' );
			var newParams = [];

			for ( var i = 0; i < params.length; i++ ) {
				if ( params[i].indexOf( 'min_price' ) === -1 &&
					params[i].indexOf( 'max_price' ) === -1 &&
					params[i].indexOf( 'filter_' ) === -1 &&
					params[i].indexOf( 'rating_filter' ) === -1 &&
					params[i].indexOf( 'query_type' ) === -1
					) {
					newParams.push( params[i] );
				}
			}

			if ( newParams.length ) {
				url += '?' + newParams.join( '&' );
			}
		}
		return url;
	};

	ProductTable.initContent = function( $el ) {
		ProductTable.initMedia( $el );
		ProductTable.initVariations( $el );
		ProductTable.initProductAddons( $el );
	};

	ProductTable.initMedia = function( $el ) {
		if ( !$el || !$el.length ) {
			return;
		}

		if ( typeof WPPlaylistView !== 'undefined' ) {
			// Initialise audio and video playlists
			$el.find( '.wp-playlist' ).filter( function() {
				return $( '.mejs-container', this ).length === 0; // exclude playlists already initialized
			} ).each( function() {
				return new WPPlaylistView( { el: this } );
			} );
		}

		// Initialise audio and video shortcodes
		if ( 'wp' in window && 'mediaelement' in window.wp ) {
			$( window.wp.mediaelement.initialize );
		}

		// Run fitVids to ensure videos in table have correct proportions
		if ( $.fn.fitVids ) {
			$el.fitVids();
		}
	};

	ProductTable.initProductAddons = function( $el ) {
		// Initialise product add-ons

		// v2
		if ( 'init_addon_totals' in $.fn ) {
			$el.find( '.cart:not(.cart_group)' ).each( function() {
				$( this ).init_addon_totals();
			} );
		}

		// v3 - can't init this at the present time as no function exposed.
	};

	ProductTable.initVariations = function( $el ) {
		if ( !$el || !$el.length || typeof wc_add_to_cart_variation_params === 'undefined' ) {
			return;
		}

		$el.find( '.variations_form' ).filter( function() {
			return !$( this ).hasClass( 'initialised' ); // exclude variations already initialized
		} ).each( function() {
			$( this ).wc_variation_form();
		} );
	};

	ProductTable.removeUnusedTerms = function( terms, termSlugs ) {
		var term,
			result = terms.slice( 0 ); // clone the terms array, so original is unmodified.

		for ( var i = result.length - 1; i >= 0; i-- ) {
			term = result[i];

			if ( term.hasOwnProperty( 'children' ) ) {
				term.children = ProductTable.removeUnusedTerms( term.children, termSlugs );

				if ( 0 === term.children.length ) {
					// No children left, so delete property from term.
					delete term.children;
				}
			}
			// Keep the term if it's found in termSlugs or it has child terms, otherwise remove it.
			if ( -1 === termSlugs.indexOf( term.slug ) && !term.hasOwnProperty( 'children' ) ) {
				result.splice( i, 1 );
			}
		}

		return result;
	};

	ProductTable.responsiveRendererTableAll = function( api, rowIdx, columns ) {
		// Displays the child row when responsive_display="modal"
		var title = '';

		var data = $.map( columns, function( col, i ) {
			title = col.title ? col.title + ':' : '';
			return (api.column( col.columnIndex ).visible() ? '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '"><td>' + title + '</td><td class="child">' + col.data + '</td></tr>' : '');
		} ).join( '' );

		if ( data ) {
			var $modal = $( '<table class="modal-table wc-product-table" />' ).append( data );
			$modal = $( '<div class="' + product_table_params.wrapper_class + '"></div>' ).append( $modal );
			ProductTable.initContent( $modal );
			return $modal;
		} else {
			return false;
		}
	};

	ProductTable.setVariationImage = function( $form, variation ) {
		var $productRow = $form.closest( 'tr' );

		// If variations form is in a parent row, check for image in child row and vice versa
		if ( $productRow.hasClass( 'parent' ) ) {
			$productRow = $productRow.add( $productRow.next( '.child' ) );
		} else if ( $productRow.hasClass( 'child' ) ) {
			$productRow = $productRow.add( $productRow.prev( '.parent' ) );
		}

		var $productImg = $productRow.find( 'img.product-thumbnail' ).eq( 0 );

		if ( !$productImg.length ) {
			return;
		}

		var props = false,
			$productGalleryWrap = $productImg.closest( '.woocommerce-product-gallery__image', $productRow ).eq( 0 ),
			$productGalleryLink = false;

		if ( $productGalleryWrap.length ) {
			$productGalleryLink = $productGalleryWrap.find( 'a' ).eq( 0 );
		}

		if ( variation ) {
			if ( 'image' in variation ) {
				props = variation.image;
			} else if ( 'image_src' in variation ) {
				// Back compat: different object structure used in WC < 3.0
				props = {
					src: variation.image_src,
					src_w: '',
					src_h: '',
					full_src: variation.image_link,
					full_src_w: '',
					full_src_h: '',
					thumb_src: variation.image_src,
					thumb_src_w: '',
					thumb_src_h: '',
					srcset: variation.image_srcset,
					sizes: variation.image_sizes,
					title: variation.image_title,
					alt: variation.image_alt,
					caption: variation.image_caption
				};
			}
		}

		if ( props && props.thumb_src.length ) {
			$productImg.wc_set_variation_attr( 'src', props.thumb_src );
			$productImg.wc_set_variation_attr( 'title', props.title );
			$productImg.wc_set_variation_attr( 'alt', props.alt );
			$productImg.wc_set_variation_attr( 'data-src', props.full_src );
			$productImg.wc_set_variation_attr( 'data-caption', props.caption );
			$productImg.wc_set_variation_attr( 'data-large_image', props.full_src );
			$productImg.wc_set_variation_attr( 'data-large_image_width', props.full_src_w );
			$productImg.wc_set_variation_attr( 'data-large_image_height', props.full_src_h );
			if ( $productGalleryWrap.length ) {
				$productGalleryWrap.wc_set_variation_attr( 'data-thumb', props.thumb_src );
			}
			if ( $productGalleryLink.length ) {
				$productGalleryLink.wc_set_variation_attr( 'href', props.full_src );
			}
		} else {
			$productImg.wc_reset_variation_attr( 'src' );
			$productImg.wc_reset_variation_attr( 'width' );
			$productImg.wc_reset_variation_attr( 'height' );
			$productImg.wc_reset_variation_attr( 'title' );
			$productImg.wc_reset_variation_attr( 'alt' );
			$productImg.wc_reset_variation_attr( 'data-src' );
			$productImg.wc_reset_variation_attr( 'data-caption' );
			$productImg.wc_reset_variation_attr( 'data-large_image' );
			$productImg.wc_reset_variation_attr( 'data-large_image_width' );
			$productImg.wc_reset_variation_attr( 'data-large_image_height' );
			if ( $productGalleryWrap.length ) {
				$productGalleryWrap.wc_reset_variation_attr( 'data-thumb' );
			}
			if ( $productGalleryLink.length ) {
				$productGalleryLink.wc_reset_variation_attr( 'href' );
			}
		}
	};

	ProductTable.updateMultiHiddenField = function( field, val, $multiCheck ) {
		//Find the multi-cart input which corresponds to the changed cart input
		var $multiCartInput = $( 'input[data-input-name="' + field + '"]', $multiCheck );

		if ( $multiCartInput.length ) {
			// Update the hidden input to match the cart form value
			$multiCartInput.val( val );
		}
	};

	/******************************************
	 * INSTANCE METHODS
	 ******************************************/

	ProductTable.prototype.buildConfig = function() {
		if ( this.config ) {
			return this.config;
		}

		var config = {
			retrieve: true, // so subsequent calls to DataTable() return the same API instance
			responsive: true,
			processing: true, // display 'processing' indicator when loading
			orderMulti: false, // disable ordering by multiple columns at once
			stateSave: true,
			language: product_table_params.language
		};

		// Get config for this table instance.
		var tableConfig = this.$table.data( 'config' );

		if ( tableConfig ) {
			// We need to do deep copy for the 'language' property to be merged correctly.
			config = $.extend( true, { }, config, tableConfig );
		}

		// Build AJAX data for loading products
		var ajaxData = {
			table_id: this.id,
			action: 'wcpt_load_products',
			_ajax_nonce: product_table_params.ajax_nonce
		};

		// If query string present, add parameters to data to send (e.g. filter attributes)
		// .substring(1) removes the '?' at the beginning
		if ( window.location.search ) {
			var vars = window.location.search.substring( 1 ).split( '&' );

			for ( var i = 0; i < vars.length; i++ ) {
				var pair = vars[i].split( '=', 2 );

				if ( 2 === pair.length ) {
					ajaxData[pair[0]] = pair[1].replace( /%2C/g, ',' );
				}
			}
		}

		// Config for server-side processing
		if ( config.serverSide && 'ajax_url' in product_table_params ) {
			config.deferRender = true;
			config.ajax = {
				url: product_table_params.ajax_url,
				type: 'POST',
				data: ajaxData,
				xhrFields: {
					withCredentials: true
				}
			};
		}

		// Set responsive display and renderer functions
		if ( typeof config.responsive.details === 'object' && 'display' in config.responsive.details ) {
			if ( 'child_row_visible' === config.responsive.details.display ) {
				config.responsive.details.display = $.fn.dataTable.Responsive.display.childRowImmediate;
				config.responsive.details.renderer = $.fn.dataTable.Responsive.renderer.listHidden();

			}
			if ( 'modal' === config.responsive.details.display ) {
				config.responsive.details.display = $.fn.dataTable.Responsive.display.modal();
				config.responsive.details.renderer = ProductTable.responsiveRendererTableAll;
			}
		}

		// Legacy config for language (we now use Gettext for translation).
		if ( 'lang_url' in product_table_params ) {
			config.language = { url: product_table_params.lang_url };
		}

		return config;
	};

	ProductTable.prototype.checkFormAttributeSupport = function( $form ) {
		var table = this;

		// Check for support for HTML5 form attribute
		if ( !$form.is( 'form' ) ) {
			return table;
		}

		if ( !$form[0] || !('elements' in $form[0]) ) {
			return table;
		}

		if ( $form[0].elements.length > 2 ) {
			// If we have more than 2 form elements (i.e. the form button and hidden 'multi_cart' field)
			// then HTML5 form attribute must be supported natively in browser, so no need to continue.
			return table;
		}

		table.getDataTable()
			.$( '.multi-cart-check input[type="checkbox"]' ) // get all multi checkboxes in table
			.add( table.$table.find( 'td.child .multi-cart-check input[type="checkbox"]' ) ) // including checkboxes in responsive child rows
			.filter( ':checked' ) // now get just the checked products
			.each( function() {
				// Then add all multi fields for checked products to the parent multi-cart form
				$( this ).clone().appendTo( $form );
				$( this ).siblings( 'input[type="hidden"]' ).clone().appendTo( $form );
			} );

		return table;
	};

	ProductTable.prototype.getDataTable = function() {
		if ( !this.dataTable ) {
			// Build table config.
			this.config = this.buildConfig();

			// Initialize DataTables instance.
			this.dataTable = this.$table.DataTable( this.config );
		}
		return this.dataTable;
	};

	ProductTable.prototype.initAddToCart = function() {
		// AJAX for single add to cart buttons
		this.$table.on( 'submit', '.cart', { table: this }, this.onAddToCart );

		return this;
	};

	ProductTable.prototype.initFilters = function() {
		var table = this,
			filters = table.$table.data( 'filters' );

		if ( !filters ) {
			return table;
		}

		var dataTable = table.getDataTable(),
			$filtersWrap = $( '<div class="wc-product-table-select-filters" id="' + table.id + '_select_filters"><label class="filter-label">' + product_table_params.language.filterBy + '</label></div>' ),
			savedColumnSearches = { },
			filtersAdded = 0;

		if ( table.initialState && 'columns' in table.initialState ) {

			// If we have an initial state, convert to a more workable object of the form: { 'column_name': 'previous search' }
			for ( var i = 0; i < table.initialState.columns.length; i++ ) {
				if ( !('search' in table.initialState.columns[i]) || !table.initialState.columns[i].search.search ) {
					continue;
				}
				if ( (0 === dataTable.column( i ).length) || typeof dataTable.column( i ).dataSrc() !== 'string' ) {
					continue;
				}
				var search = table.initialState.columns[i].search.search;
				if ( search && table.initialState.columns[i].search.regex ) {
					search = search.replace( '(^|, )', '' ).replace( '(, |$)', '' );
				}
				// Bug in DataTables - column().name() not working so we need to pull name from header node
				savedColumnSearches[$( dataTable.column( i ).header() ).data( 'name' )] = search;
			}
		}

		// Build filters
		for ( var tax in filters ) {
			// Create the <select>
			var selectAtts = {
				'name': 'wcpt_filter_' + tax,
				'data-tax': tax,
				'data-column': filters[tax].column,
				'data-search-column': filters[tax]['search-column'],
				'aria-label': filters[tax].heading
			};
			if ( filters[tax].class ) {
				selectAtts['class'] = filters[tax].class;
			}
			var $select = $( '<select/>' )
				.attr( selectAtts )
				.append( '<option value="">' + filters[tax].heading + '</option>' )
				.on( 'change', { table: table }, table.onFilterChange );

			var terms = filters[tax].terms;

			// If not lazy-loading, find all terms for corresponding column in the table, so we can restrict filter to relevant terms only.
			if ( !table.config.serverSide ) {
				var searchData = dataTable
					.column( $select.data( 'searchColumn' ) + ':name' )
					.data();

				if ( searchData.any() ) {
					var termSlugs = searchData.join( ' ' ).split( ' ' );
					terms = ProductTable.removeUnusedTerms( terms, termSlugs );
				}
			}

			// Don't add this filter if we have no terms for it
			if ( !terms.length ) {
				continue;
			}

			// Add the <option> elements to filter
			ProductTable.appendFilterOptions( $select, terms );

			// Determine the initial filter selection (if any)
			var value = '';

			if ( 'selected' in filters[tax] && $select.children( 'option[value="' + filters[tax].selected + '"]' ).length ) {
				// Set selection based on active filter widget
				value = filters[tax].selected;

			} else if ( filters[tax].column in savedColumnSearches ) {
				// Set selection based on previous saved table state
				var prevSearch = savedColumnSearches[filters[tax].column];

				// Flatten terms to make searching through them easier
				var flatTerms = ProductTable.flattenObjectArray( filters[tax].terms, 'children' );

				// Search the filter terms for the previous search value, which will be the <option> text rather than its value.
				// We could use Array.find() here if browser support was better.
				$.each( flatTerms, function( i, term ) {
					if ( 'name' in term && term.name === prevSearch ) {
						value = 'slug' in term ? term.slug : term.name;
						return false; // break the $.each loop
					}
				} );
			}

			// Set the initial value and append select to wrapper
			$select.val( value ).appendTo( $filtersWrap );
			filtersAdded++;
		} // foreach filter

		// Add filters to table - before search box if present, otherwise as first element above table
		if ( filtersAdded > 0 ) {
			// Add filters to table
			var $searchBox = table.$tableWrapper.find( '.dataTables_filter' );

			if ( $searchBox.length ) {
				$filtersWrap.prependTo( $searchBox.closest( '.wc-product-table-controls' ) );
			} else {
				$filtersWrap.prependTo( table.$tableWrapper.children( '.wc-product-table-above' ) );
			}
		}

		// Store filters here as we use this when searching columns
		table.$filters = table.$tableWrapper.find( '.wc-product-table-select-filters select' );
		return table;
	};

	ProductTable.prototype.initMultiCart = function() {
		var table = this;

		if ( !table.config.multiAddToCart || !table.$tableWrapper.length ) {
			return table;
		}

		// Create the multi cart form and append above/below table
		var $multiForm =
			$( '<form class="multi-cart-form" method="post" />' )
			.append( '<input type="submit" class="' + product_table_params.multi_cart_button_class + '" value="' + product_table_params.language.multiCartButton + '" />' )
			.append( '<input type="hidden" name="multi_cart" value="1" />' )
			.on( 'submit', { table: table }, table.onAddToCartMulti );

		if ( $.inArray( table.config.multiCartLocation, ['top', 'both'] ) > -1 ) {
			table.$tableWrapper.children( '.wc-product-table-above' )
				.prepend( $multiForm )
				.addClass( 'with-multi-form' );
		}
		if ( $.inArray( table.config.multiCartLocation, ['bottom', 'both'] ) > -1 ) {
			table.$tableWrapper.children( '.wc-product-table-below' )
				.prepend( $multiForm.clone( true ) )
				.addClass( 'with-multi-form' );
		}

		table.registerMultiCartEvents();

		return table;
	};

	ProductTable.prototype.initPhotoswipe = function() {
		this.$table.on( 'click', '.woocommerce-product-gallery__image a', this.onOpenPhotoswipe );
		return this;
	};

	ProductTable.prototype.initQuickView = function() {
		if ( !window.WCQuickViewPro ) {
			return this;
		}

		// If links should open in Quick View, register events.
		if ( 'open_links_in_quick_view' in product_table_params && product_table_params.open_links_in_quick_view ) {

			// Handle clicks on single product links.
			this.$table.on( 'click', '.single-product-link', WCQuickViewPro.handleQuickViewClick );

			// Handle clicks on loop read more buttons (e.g. 'Select options', 'View products', etc).
			this.$table.on( 'click', '.add-to-cart-wrapper a[data-product_id]', function( event ) {

				// But don't open for external products.
				if ( $( this ).hasClass( 'product_type_external' ) ) {
					return true;
				}
				// Or AJAX add to cart buttons.
				if ( (this.href) && this.href.match( /add-to-cart=/ ) ) {
					return true;
				}

				WCQuickViewPro.handleQuickViewClick( event );
			} );
		}

		return this;
	};

	ProductTable.prototype.initResetButton = function() {
		var table = this;

		if ( !table.config.resetButton ) {
			return table;
		}

		var $resetButton =
			$( '<span class="wc-product-table-reset"><a class="reset" href="#">' + product_table_params.language.resetButton + '</a></span>' )
			.on( 'click', 'a', { table: table }, table.onReset );

		// Append reset button
		var $searchBox = table.$tableWrapper.find( '.dataTables_filter' );

		if ( table.$filters.length ) {
			$resetButton.appendTo( table.$tableWrapper.find( '.wc-product-table-select-filters' ) );
		} else if ( $searchBox.length ) {
			$resetButton.prependTo( $searchBox );
		} else {
			var $firstChild = table.$tableWrapper.children( '.wc-product-table-above' ).children( '.dataTables_length,.dataTables_info' ).eq( 0 );

			if ( $firstChild.length ) {
				$resetButton.appendTo( $firstChild );
			} else {
				$resetButton.prependTo( table.$tableWrapper.children( '.wc-product-table-above' ) );
			}
		}

		return table;
	};

	ProductTable.prototype.initSearchOnClick = function() {
		if ( this.config.clickFilter ) {
			// 'search_on_click' - add click handler for relevant links. When clicked, the table will filter by the link text.
			this.$table.on( 'click', 'a[data-column]', { table: this }, this.onClickSearch );
		}
		return this;
	};

	ProductTable.prototype.processAjaxData = function() {
		var table = this;

		if ( !table.config.serverSide || !table.ajaxData.length ) {
			return table;
		}

		var $rows = table.$table.find( 'tbody tr' );

		// Add row attributes to each row in table
		if ( $rows.length ) {
			for ( var i = 0; i < table.ajaxData.length; i++ ) {
				if ( '__attributes' in table.ajaxData[i] && $rows.eq( i ).length ) {
					$.each( table.ajaxData[i].__attributes, ProductTable.addRowAttributes( $rows.eq( i ) ) );
				}
			}
		}
		return table;
	};

	ProductTable.prototype.registerMultiCartEvents = function() {
		var table = this;

		if ( !table.config.multiAddToCart ) {
			return table;
		}

		// Quantities - update hidden fields when changed
		table.$table.on( 'change', '.cart .qty', function() {
			var $cartForm = $( this ).closest( 'form.cart' ),
				$multiCheck = $cartForm.siblings( '.multi-cart-check' ),
				$multiCheckbox = $multiCheck.children( 'input[type="checkbox"]' ),
				$multiCartQuantity = $multiCheck.children( 'input[data-input-name="quantity"]' ),
				qty = $( this ).val();

			// If quantity has increased, tick the multi cart checkbox
			if ( $multiCheckbox.is( ':enabled' ) && qty && (qty > $multiCartQuantity.val()) ) {
				$multiCheckbox.prop( 'checked', true );
			}

			// Update quantity field
			ProductTable.updateMultiHiddenField( 'quantity', qty, $multiCheck );
		} );

		// Variations - update hidden fields when changed
		table.$table.on( 'found_variation', '.variations_form', function( event, variation ) {
			var $cartForm = $( this );
			var $multiCheck = $cartForm.siblings( '.multi-cart-check' );

			// Variation attributes
			if ( 'attributes' in variation ) {
				for ( var attribute in variation.attributes ) {
					ProductTable.updateMultiHiddenField( attribute, variation.attributes[attribute], $multiCheck );
				}
			}
			// Variation ID
			if ( 'variation_id' in variation ) {
				ProductTable.updateMultiHiddenField( 'variation_id', variation.variation_id, $multiCheck );
			}
		} );

		// Enable/disable multi checkbox depending on whether current variation is purchasable
		table.$table.on( 'show_variation', '.variations_form', function( event, variation, purchasable ) {
			var $checkbox = $( this ).siblings( '.multi-cart-check' ).children( 'input[type="checkbox"]' );
			if ( purchasable ) {
				$checkbox.prop( 'disabled', false );
			} else {
				$checkbox.prop( { disabled: true, checked: false } );
			}
		} );

		// Disable multi checkbox on variation hide
		table.$table.on( 'hide_variation', '.variations_form', function() {
			$( this ).siblings( '.multi-cart-check' ).children( 'input[type="checkbox"]' ).prop( { disabled: true, checked: false } );
		} );

		// Product Addons - update hidden fields when changed
		table.$table.on( 'woocommerce-product-addons-update', function( event ) {
			var $input = $( event.target ),
				val = $input.val(),
				inputName = $input.prop( 'name' ),
				$cartForm = $input.closest( 'form.cart' );

			if ( !inputName || 'quantity' === inputName ) { // quantity change handled above.
				return;
			}

			// For checkbox addons the input names are arrays, e.g. addon-check[].
			// We need to add an integer index to the name to make sure we update the correct hidden field
			if ( 'checkbox' === $input.attr( 'type' ) ) {
				// Pull the index from the parent wrapper class (e.g. wc-pao-addon-123-collection-0)
				// 'addon-wrap-' match is for back compat with addons v2.
				var match = $input.closest( '.form-row', $cartForm.get( 0 ) ).attr( 'class' ).match( /(wc-pao-addon-|addon-wrap-).+?-(\d+)($|\s)/ );

				if ( match && 4 === match.length ) {
					// match[2] is the index of the checkbox within the checkbox group.
					inputName = inputName.replace( '[]', '[' + match[2] + ']' );
				}
			}

			// If input is a checkbox or radio, we need to clear the value if it's not checked
			if ( 'radio' === $input.attr( 'type' ) || 'checkbox' === $input.attr( 'type' ) ) {
				if ( !$input.prop( 'checked' ) ) {
					val = '';
				}
			}

			ProductTable.updateMultiHiddenField( inputName, val, $cartForm.siblings( '.multi-cart-check' ) );
		} );

		return table;
	};

	ProductTable.prototype.registerVariationEvents = function() {
		var table = this;

		if ( 'dropdown' !== this.config.variations ) {
			return table;
		}

		// Add class when form initialised so we can filter these out later
		table.$table.on( 'wc_variation_form', '.variations_form', function() {
			$( this ).addClass( 'initialised' );
		} );

		// Update image column when variation found
		table.$table.on( 'found_variation', '.variations_form', function( event, variation ) {
			ProductTable.setVariationImage( $( this ), variation );
		} );

		// Show variation and enable cart button
		table.$table.on( 'show_variation', '.variations_form', function( event, variation, purchasable ) {
			// Older versions of WC didn't pass the purchasable parameter, so we need to work this out
			if ( typeof purchasable === 'undefined' ) {
				purchasable = variation.is_purchasable && variation.is_in_stock && variation.variation_is_visible;
			}

			$( this ).find( '.added_to_cart' ).remove();
			$( this ).find( '.single_add_to_cart_button' ).prop( 'disabled', !purchasable ).removeClass( 'added disabled' );
			$( this ).find( '.single_variation' ).slideDown( 200 );
		} );

		// Hide variation and disable cart button
		table.$table.on( 'hide_variation', '.variations_form', function() {
			$( this ).find( '.single_add_to_cart_button' ).prop( 'disabled', true );
			$( this ).find( '.single_variation' ).slideUp( 200 );
		} );

		// Reset the variation image
		table.$table.on( 'reset_image', '.variations_form', function() {
			ProductTable.setVariationImage( $( this ), false );
		} );

		return table;
	};

	ProductTable.prototype.resetMultiCartCheckboxes = function() {
		this.getDataTable().$( '.multi-cart-check input[type="checkbox"]' ).prop( 'checked', false );
		return this;
	};

	ProductTable.prototype.resetQuantities = function( $form ) {
		var table = this;

		if ( !$form || !$form.length ) {
			$form = table.getDataTable().$( '.cart' );
		}

		$form.find( 'input[name="quantity"]' ).val( function( index, value ) {
			if ( $.isNumeric( $( this ).attr( 'min' ) ) ) {
				value = $( this ).attr( 'min' );
			}
			return value;
		} ).trigger( 'change' );
		return table;
	};

	ProductTable.prototype.resetVariations = function( $form ) {
		var table = this;

		if ( !$form || !$form.length ) {
			$form = table.getDataTable().$( '.variations_form' );
		}

		$form.each( function() {
			$( this ).find( 'select' ).val( '' );
			$( this ).find( '.single_variation' ).slideUp( 200 ).css( 'display', 'none' ); // ensure variation is hidden (e.g. on other results pages)
			$( this ).find( '.single_add_to_cart_button' ).addClass( 'disabled', true );
			$( this )
				.siblings( '.multi-cart-check' )
				.children( 'input[type="checkbox"]' )
				.prop( 'checked', false )
				.prop( 'disabled', true );

		} );

		return table;
	};

	ProductTable.prototype.resetProductAddons = function() {
		var table = this;

		var $addons = table.getDataTable().$( '.wc-pao-addon, .product-addon' );

		$addons.find( 'select, textarea' ).val( '' ).trigger( 'change' );
		$addons.find( 'input' ).each( function() {
			if ( 'radio' === $( this ).attr( 'type' ) || 'checkbox' === $( this ).attr( 'type' ) ) {
				$( this ).prop( 'checked', false );
			} else {
				$( this ).val( '' );
			}
			$( this ).trigger( 'change' );
		} );

		return table;
	};

	ProductTable.prototype.scrollToTop = function() {
		var table = this;
		var scroll = table.config.scrollOffset;

		if ( scroll !== false && !isNaN( scroll ) ) {
			var tableOffset = table.$tableWrapper.offset().top - scroll;

			if ( table.hasAdminBar ) { // Adjust offset for WP admin bar
				tableOffset -= 32;
			}
			$( 'html,body' ).animate( { scrollTop: tableOffset }, 300 );
		}
		return table;
	};

	ProductTable.prototype.showHidePagination = function() {
		var table = this;

		// Hide pagination if we only have 1 page
		if ( table.$pagination.length ) {
			var pageInfo = table.getDataTable().page.info();

			if ( pageInfo && pageInfo.pages <= 1 ) {
				table.$pagination.hide( 0 );
			} else {
				table.$pagination.show();
			}
		}

		return table;
	};

	/******************************************
	 * EVENTS
	 ******************************************/

	ProductTable.prototype.onAddToCart = function( event ) {
		var table = event.data.table;

		var $cartForm = $( this ),
			$button = $( '.single_add_to_cart_button', this ),
			productId = $cartForm.find( '[name="add-to-cart"]' ).val();

		// If not using AJAX, set form action to blank so current page is reloaded, rather than single product page
		if ( !table.config.ajaxCart ) {
			$cartForm.attr( 'action', '' );
			return true;
		}

		if ( typeof wc_add_to_cart_params === 'undefined' || typeof productId === 'undefined' || !$cartForm.length || $button.hasClass( 'disabled' ) ) {
			return true;
		}

		//@todo: Change to event.preventDefault()?
		event.stopImmediatePropagation();

		$cartForm.siblings( 'p.cart-error' ).remove();
		table.$tableWrapper.find( '.multi-cart-message' ).remove();

		$button
			.removeClass( 'added' )
			.addClass( 'loading' )
			.siblings( 'a.added_to_cart' )
			.remove();

		var data = $cartForm.serializeObject();
		delete data['add-to-cart']; // Make sure 'add-to-cart' isn't included as we use 'product_id'

		data.product_id = productId;
		data.action = 'wcpt_add_to_cart';
		data._ajax_nonce = product_table_params.ajax_nonce;

		$( document.body ).trigger( 'adding_to_cart', [$button, data] );

		$.ajax( {
			url: product_table_params.ajax_url,
			type: 'POST',
			data: data,
			xhrFields: {
				withCredentials: true
			}
		} ).done( function( response ) {
			if ( response.error ) {
				if ( response.error_message ) {
					$cartForm.before( response.error_message );
				}
				return;
			}

			// Product sucessfully added - redirect to cart or show 'View cart' link
			if ( wc_add_to_cart_params.cart_redirect_after_add === 'yes' ) {
				window.location = wc_add_to_cart_params.cart_url;
				return;
			} else {
				// Replace fragments
				//@todo: Is this needed? Triggering 'added_to_cart' does this in add-to-cart.js
				if ( response.fragments ) {
					$.each( response.fragments, function( key, value ) {
						$( key ).replaceWith( value );
					} );
				}

				$button
					.removeClass( 'loading' )
					.addClass( 'added' );

				// View cart text
				if ( $button.parent().find( '.added_to_cart' ).length === 0 ) {
					$button.after( ' <a href="' + wc_add_to_cart_params.cart_url + '" class="added_to_cart wc-forward" title="' + wc_add_to_cart_params.i18n_view_cart + '">' + wc_add_to_cart_params.i18n_view_cart + '</a>' );
				}

				// Reset variations
				if ( $cartForm.hasClass( 'variations_form' ) ) {
					table.resetVariations( $cartForm );
				}

				// Trigger event so themes can refresh other areas
				$( document.body ).trigger( 'added_to_cart', [response.fragments, response.cart_hash, $button] );
			}
		} ).always( function() {
			$cartForm.siblings( '.multi-cart-check' ).find( 'input[type="checkbox"]' ).prop( 'checked', false );
			$button.removeClass( 'loading' );
		} );

		return false;
	};

	// Submit event for multi add to cart form
	ProductTable.prototype.onAddToCartMulti = function( event ) {
		var table = event.data.table,
			dataTable = table.getDataTable(),
			$form = $( this ),
			data = { };

		// Add id="multi-cart" to form via JS as we can have several multi cart forms on a single page.
		// This keeps the HTML valid and makes sure each form can be submitted correctly.
		$form.attr( 'id', 'multi-cart' );

		table.$tableWrapper.find( '.multi-cart-message' ).remove();
		table.$table.find( 'p.cart-error, a.added_to_cart' ).remove();

		// Find all checked products and loop through each to build product IDs and quantities.
		// dataTable.$() doesn't work with :checked selector in responsive rows, so we need add them manually to the result set.
		dataTable
			.$( '.multi-cart-check input[type="checkbox"]' ) // all checkboxes
			.add( table.$table.find( 'td.child .multi-cart-check input[type="checkbox"]' ) ) // add checkboxes in responsive child rows
			.filter( ':checked' ) // just the checked products
			.each( function() {
				// Add all the hidden fields to our data to be posted
				$.extend( true, data, $( this ).siblings( 'input[type="hidden"]' ).serializeObject() );
			} );

		// Show error if no products were selected
		if ( $.isEmptyObject( data ) && product_table_params.language.multiCartNoSelection ) {
			ProductTable.setMultiCartMessage( '<p class="cart-error">' + product_table_params.language.multiCartNoSelection + '</p>', $form );
			return false;
		}

		// Return here if we're not using AJAX
		if ( !table.config.ajaxCart || typeof wc_add_to_cart_params === 'undefined' ) {
			table.checkFormAttributeSupport( $form );
			return true;
		}

		// AJAX enabled, so block table and do the AJAX post
		table.$tableWrapper.block( ProductTable.blockConfig );

		data.action = 'wcpt_add_to_cart_multi';
		data._ajax_nonce = product_table_params.ajax_nonce;

		$( document.body ).trigger( 'adding_to_cart', [$form, data] );

		$.ajax( {
			url: product_table_params.ajax_url,
			type: 'POST',
			data: data,
			xhrFields: {
				withCredentials: true
			}
		} ).done( function( response ) {
			if ( response.error ) {
				if ( response.error_message ) {
					ProductTable.setMultiCartMessage( response.error_message, $form );
				}
				return;
			}

			if ( wc_add_to_cart_params.cart_redirect_after_add === 'yes' ) { // redirect after add to cart?
				window.location = wc_add_to_cart_params.cart_url;
				return;
			} else {
				// Replace fragments
				if ( response.fragments ) {
					$.each( response.fragments, function( key, value ) {
						$( key ).replaceWith( value );
					} );
				}

				if ( response.cart_message ) {
					ProductTable.setMultiCartMessage( response.cart_message, $form );
				}

				// Reset all the things
				table
					.resetQuantities()
					.resetVariations()
					.resetProductAddons()
					.resetMultiCartCheckboxes();

				// Trigger event so themes can refresh other areas
				$( document.body ).trigger( 'added_to_cart', [response.fragments, response.cart_hash, table.$tableWrapper] );
			}
		} ).always( function() {
			table.$tableWrapper.unblock();
			$form.removeAttr( 'id' );
		} );

		return false;
	};

	ProductTable.setMultiCartMessage = function( message, $multiCartForm ) {
		$multiCartForm.parent().append( $( '<div class="multi-cart-message"></div>' ).append( message ) );
	};

	ProductTable.prototype.onAjaxLoad = function( event, settings, json, xhr ) {
		var table = event.data.table;

		if ( null !== json && 'data' in json && $.isArray( json.data ) ) {
			table.ajaxData = json.data;
		}

		table.$table.trigger( 'lazyload.wcpt', [table] );
	};

	ProductTable.prototype.onClickSearch = function( event ) {
		event.preventDefault();

		var table = event.data.table,
			$link = $( this ),
			columnName = $link.data( 'column' ),
			searchTerm = $link.text(),
			regex = true,
			slug = $link.children( '[data-slug]' ).length ? $link.children( '[data-slug]' ).data( 'slug' ) : '';

		if ( table.config.serverSide ) {
			searchTerm = slug;
			regex = false;
		} else {
			searchTerm = '(^|, )' + searchTerm + '(, |$)';
		}

		table.getDataTable()
			.column( columnName + ':name' )
			.search( searchTerm, regex, false )
			.draw();

		// If we have filters, update selection to match the value being searched for.
		if ( table.$filters.length ) {
			table.$filters.filter( '[data-column="' + columnName + '"]' ).each( function() {
				var filterVal = $( this ).children( 'option[value="' + slug + '"]' ).length ? slug : '';
				$( this ).val( filterVal );
			} );
		}

		event.data.table.scrollToTop();
		return false;
	};

	ProductTable.prototype.onDraw = function( event ) {
		var table = event.data.table;

		// Add row attributes to each <tr> if using lazy load
		if ( table.config.serverSide ) {
			table.processAjaxData();
		}

		// If using server side processing or not on first draw, initialise content (variations, etc)
		if ( table.config.serverSide || !table.$table.hasClass( 'loading' ) ) {
			ProductTable.initContent( table.$table );
		}

		if ( table.config.multiAddToCart && table.$tableWrapper.length ) {
			table.$tableWrapper.find( '.multi-cart-message' ).remove();
		}

		table.showHidePagination();

		table.$table.trigger( 'draw.wcpt', [table] );
	};

	ProductTable.prototype.onFilterChange = function( event ) {
		var table = event.data.table,
			dataTable = table.getDataTable(),
			$select = $( this );

		var searchVal = $select.val(),
			taxonomy = $select.data( 'tax' ),
			regex = true;

		if ( table.config.serverSide ) {
			regex = false;
		} else {
			searchVal = searchVal ? '(^| )' + searchVal + '( |$)' : '';
		}

		dataTable
			.column( $select.data( 'searchColumn' ) + ':name' )
			.search( searchVal, regex, false )
			.draw();

		// If the column is an attribute, set any variations to match the search term
		if ( !table.config.serverSide && 'pa_' === taxonomy.substring( 0, 3 ) && dataTable.column( 'add-to-cart:name' ).length ) {
			var searchSlug = $select.val();

			// Find variations which match this attribute
			dataTable.column( 'add-to-cart:name' ).nodes().to$().find( '.variations_form select[data-attribute_name="attribute_' + taxonomy + '"]' ).each( function() {
				var variationValue = $( this ).children( 'option[value="' + searchSlug + '"]' ).length ? searchSlug : '';
				$( this ).val( variationValue ).trigger( 'change' );
			} );
		}
	};

	ProductTable.prototype.onInit = function( event ) {
		var table = event.data.table;

		table.$tableWrapper = table.$table.parent();
		table.$pagination = table.$tableWrapper.find( '.dataTables_paginate' );

		table
			.initFilters()
			.initResetButton()
			.initAddToCart()
			.registerVariationEvents()
			.initMultiCart()
			.initSearchOnClick()
			.initPhotoswipe()
			.initQuickView()
			.showHidePagination();

		// fitVids will run on every draw event for lazy load, but for standard loading
		// we need to run fitVids onInit as well as initMedia only runs on subsequent draws.
		if ( !table.config.serverSide && $.fn.fitVids ) {
			table.$table.fitVids();
		}

		table.$table
			.removeClass( 'loading' )
			.trigger( 'init.wcpt', [table] );
	};

	ProductTable.prototype.onOpenPhotoswipe = function( event ) {
		event.preventDefault();

		var pswpElement = $( '.pswp' )[0],
			$target = $( event.target ),
			$galleryImage = $target.closest( '.woocommerce-product-gallery__image' ),
			items = [];

		if ( $galleryImage.length > 0 ) {
			$galleryImage.each( function( i, el ) {
				var img = $( el ).find( 'img' ),
					large_image_src = img.attr( 'data-large_image' ),
					large_image_w = img.attr( 'data-large_image_width' ),
					large_image_h = img.attr( 'data-large_image_height' ),
					item = {
						src: large_image_src,
						w: large_image_w,
						h: large_image_h,
						title: (img.attr( 'data-caption' ) && img.attr( 'data-caption' ).length) ? img.attr( 'data-caption' ) : img.attr( 'title' )
					};
				items.push( item );
			} );
		}

		var options = {
			index: 0,
			shareEl: false,
			closeOnScroll: false,
			history: false,
			hideAnimationDuration: 0,
			showAnimationDuration: 0
		};

		// Initializes and opens PhotoSwipe
		var photoswipe = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options );
		photoswipe.init();
	};

	ProductTable.prototype.onPage = function( event ) {
		// Animate back to top of table on next/previous page event
		event.data.table.scrollToTop();
	};

	ProductTable.prototype.onProcessing = function( event, settings, processing ) {
		if ( processing ) {
			event.data.table.$table.block( ProductTable.blockConfig );
		} else {
			event.data.table.$table.unblock();
		}
	};

	ProductTable.prototype.onReset = function( event ) {
		event.preventDefault();

		// Reload page without query params if we have them (e.g. layered nav filters)
		if ( window.location.search ) {
			window.location = ProductTable.getCurrentUrlWithoutFilters();
			return true;
		}

		var table = event.data.table,
			dataTable = table.getDataTable();

		// Reset responsive child rows
		table.$table.find( 'tr.child' ).remove();
		table.$table.find( 'tr.parent' ).removeClass( 'parent' );

		// Reset search filters
		if ( table.$filters.length ) {
			table.$filters.val( '' );
		}

		// Reset cart stuff
		table
			.resetQuantities()
			.resetVariations()
			.resetProductAddons()
			.resetMultiCartCheckboxes();

		// Remove add to cart notifications
		table.$tableWrapper.find( '.multi-cart-message' ).remove();
		table.$table.find( 'p.cart-error' ).remove();
		table.$table
			.find( '.cart .single_add_to_cart_button' )
			.removeClass( 'added' )
			.siblings( 'a.added_to_cart' ).remove();

		// Clear search for any filtered columns
		dataTable.columns( 'th[data-searchable="true"]' ).search( '' );

		// Reset ordering
		var initialOrder = table.$table.attr( 'data-order' );
		if ( initialOrder.length ) {
			var orderArray = initialOrder.replace( /[\[\]\" ]+/g, '' ).split( ',' );
			if ( 2 === orderArray.length ) {
				dataTable.order( orderArray );
			}
		}

		// Reset initial search term
		var searchTerm = ('search' in table.config && 'search' in table.config.search) ? table.config.search.search : '';

		// Set search, reset page length, then re-draw
		dataTable
			.search( searchTerm )
			.page.len( table.config.pageLength )
			.draw();

		return false;
	};

	ProductTable.prototype.onResponsiveDisplay = function( event, datatable, row, showHide, update ) {
		if ( showHide && (typeof row.child() !== 'undefined') ) {
			// Reset variation forms in child row to make sure it gets fully re-initialised on display
			row.child().find( '.variations_form' ).removeClass( 'initialised' );

			// Initialise media and other content in child row
			ProductTable.initContent( row.child() );

			var table = event.data.table;
			table.$table.trigger( 'responsive-display.wcpt', [table, row.child()] );
		}
	};

	ProductTable.prototype.onStateLoadParams = function( event, settings, data ) {
		var table = event.data.table;

		// Always reset to first page.
		data.start = 0;

		// If we have no active filter widgets, clear previous table search and reset ordering.
		if ( window.location.href === ProductTable.getCurrentUrlWithoutFilters() ) {

			// Reset page length
			if ( 'pageLength' in table.config ) {
				data.length = table.config.pageLength;
			}

			// Reset search
			if ( 'search' in table.config && 'search' in table.config.search ) {
				data.search.search = table.config.search.search;
			}

			// Clear any column searches
			for ( var i = 0; i < data.columns.length; i++ ) {
				data.columns[i].search.search = '';
			}

			// Reset ordering - use order from shortcode if specified, otherwise remove ordering
			if ( 'order' in table.config ) {
				data.order = table.config.order;
			}
		}

		// Store initial state
		table.initialState = data;
	};

	ProductTable.prototype.onWindowLoad = function( event ) {
		var table = event.data.table;

		// Ensure variations are initialised for standard loading
		if ( !table.$table.hasClass( 'loading' ) ) {
			ProductTable.initVariations( table.$table );
		}

		// Recalc column sizes on window load (e.g. to correctly contain media playlists)
		table.getDataTable()
			.columns.adjust()
			.responsive.recalc();

		table.$table.trigger( 'load.wcpt', [table] );
	};

	/******************************************
	 * JQUERY PLUGIN
	 ******************************************/

	/**
	 * @deprecated 2.0.2 Use $.productTable()
	 * @returns A jQuery object representing the product table (i.e. the table element)
	 */
	$.fn.product_table = function() {
		new ProductTable( this );
		return this;
	};

	/**
	 * jQuery plugin to create a product table for the current set of matched elements.
	 *
	 * @returns jQuery object - the set of matched elements the function was called with (for chaining)
	 */
	$.fn.productTable = function() {
		return this.each( function() {
			new ProductTable( $( this ) );
		} );
	};

	$( document ).ready( function() {
		// Add support for hyphens and non-Roman characters in input names/keys in jquery-serialize-object.js
		if ( 'undefined' !== typeof FormSerializer ) {
			$.extend( FormSerializer.patterns, {
				validate: /^[a-z][a-z0-9_\-\%]*(?:\[(?:\d*|[a-z0-9_\-\%]+)\])*$/i,
				key: /[a-z0-9_\-\%]+|(?=\[\])/gi,
				named: /^[a-z0-9_\-\%]+$/i
			} );
		}

		if ( 'DataTable' in $.fn && $.fn.DataTable.ext ) {
			// Change DataTables error reporting to throw rather than alert
			$.fn.DataTable.ext.errMode = 'throw';
		}

		// Initialise all product tables
		$( '.wc-product-table' ).productTable();
	} );

})( jQuery, window, document );