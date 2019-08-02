jQuery( document ).ready( function( $ ) {
	$( '.ced_my_account_reorder, .ced_my_account_place_same_order' ).each( function(){
		$( this ).attr( 'data-order_id', $( this ).attr( 'href' ).split( 'http://' )[1] );
		$( this ).attr( 'href', 'javascript:void(0);' ); 
	});
	
	$( '.ced_my_account_reorder' ).on('click',function() {
		$( this ).css( 'opacity', '0.5');
		var order_id = $( this ).data( 'order_id' );	
		ced_cng_ajax( order_id );
	});

	function ced_cng_ajax( order_id ) {
		jQuery.ajax({
			url 	: global_var.ajaxurl,
			data 	: {
			   action		: 'get_order_cart',
			   nonce_check	: global_var.ajax_nonce,
			   order_id		: order_id
			},
			type 	: 'post',
			success	: function( data ) {
			  	window.location = global_var.cart_url;
			}
		});	
	}
	
	$( document ).on( 'click', '.ced_my_account_place_same_order', function() {
		var $this = $( this ),
		order_id = $this.data( 'order_id' );
		$this.css( 'opacity', '0.5' );
		ced_cng_get_order_products( order_id );
	});

	$( document ).on( 'click', '.ced_cng_placeorder', function() {
		var $this = $( this ),
		order_id = $this.data( 'id' );
		$this.css( 'opacity', '0.5' );
		ced_cng_get_order_products( order_id );
	});
	
	function ced_cng_get_order_products( order_id ) {
		jQuery.ajax({
			url 	: global_var.ajaxurl,
			data 	: {
				action		: 'get_oreder_products',
				order_id	: order_id,
				ajax_nonce	: global_var.ajax_nonce
			},
			type 	: 'post',
			success	: function( response ) {
				try {
	            	response = jQuery.parseJSON( response );
	            	
	            	var products 	= response.prodcuts,
	            	popup 			= createPopup( products, 'order', order_id );
					$( document.body ).prepend( popup );
				} catch( e ) {
					console.log( e );
				}
            }
		});
	}

	var createPopup = function ( products, popup_type, order_id ) {
		if ( popup_type == '' ) {
			popup_type = 'order';
		}

		var no_content_class = '',
		total 	= Object.keys( products ).length,
		popup 	= '';
		
		if ( jQuery.isEmptyObject( products ) || total < 3 ) {
			no_content_class = 'ced_cng_no_content';
		}
		
		popup 	= '<div id="ced_cng_prodcts_exclude" class="ced_cng_popup-wrapper">';
			popup += '<div class="ced_cng_popup-overlay">';
				popup += '<div class="ced_cng_popup-container '+ no_content_class +'">';
					popup += '<div class="ced_cng_popup-heading">';
						if ( popup_type != 'order' || popup_type == 'basket' ) {
							popup += '<h1><strong>'+ global_var.exc_basket_item_head +'</strong></h1>';
						} else {
							popup += '<h1><strong>'+ global_var.exclude_products_head +'</strong></h1>';
						}
						popup += '<a class="ced_cng_close ced_cng_close_wrapper">&times;</a>';
					popup += '</div>';
					if ( jQuery.isEmptyObject( products ) ) {
						popup += '<img src="'+ global_var.plugi_dir_url +'assets/images/not-found.png">';
					} else {
						popup += '<div class="ced_cng_popup-content">';
							popup += '<div class="ced_cng_tbl_header">';
								popup += '<div class="ced_cng_product_info">';
									popup += '<div class="ced_cng_popup-column">';
										popup += '<span class="ced_cng_heading_text">'+ global_var.exclude +'</span>';
									popup += '</div>';
									popup += '<div class="ced_cng_popup-column">';
										popup += '<span class="ced_cng_heading_text">'+ global_var.image +'</span>';
									popup += '</div>';
									popup += '<div class="ced_cng_popup-column">';
										popup += '<span class="ced_cng_heading_text">'+ global_var.product_name +'</span>';
									popup += '</div>';
									popup += '<div class="ced_cng_popup-column">';
										popup += '<span class="ced_cng_heading_text">'+ global_var.stock +'</span>';
									popup += '</div>';
									popup += '<div class="ced_cng_popup-column">';
										popup += '<span class="ced_cng_heading_text">'+ global_var.quantity +'</span>';
									popup += '</div>';
								popup += '</div>';
							popup += '</div>';
							popup += '<div class="ced_cng_popup-content ced_cng_tbl_body">';

			            	for( var id in products ) {
			            		if ( ! products.hasOwnProperty( id ) ) {
			            			continue;
			            		}

			            		var title 	= products[ id ].title;
			            		availablity = products[ id ].availability,
			            		image 		= products[ id ].image,
			            		qty 		= products[ id ].qty,
			            		stock 		= products[ id ].stock == 'out_of_stock' ? 'Out of stock' : 'In stock',
			            		stockClass 	= products[ id ].stock == 'out_of_stock' ? 'ced_cng_txt_red' : 'ced_cng_txt_green',
			            		excluded 	= products[ id ].stock == 'out_of_stock' ? 'checked readonly' : '',
			            		exclude_dsc = products[ id ].stock == 'out_of_stock' ? global_var . out_of_stock_desc : global_var .exclude_desc,
			            		permalink 	= products[ id ].permalink,
			            		item_id 	= products[ id ].item_id == 'undefined' || popup_type == 'order' ? id : products[ id ].item_id;
								
			            		if ( availablity == 'not_exist' ) {
			            			continue;
			            		}
			            		
			            		if ( availablity == 'available' ) {
									popup += '<div class="ced_cng_product_info ced_cng_product-'+ item_id +'">';
										popup += '<div class="ced_cng_popup-column">';
											popup += '<input class="ced_cng_exclude_item" type="checkbox" value="'+ item_id +'" '+ excluded +' title="'+ exclude_dsc +'">';
										popup += '</div>';
										popup += '<div class="ced_cng_popup-column">'+ image +'</div>';
										popup += '<div class="ced_cng_popup-column">';
											popup += '<a href="'+ permalink +'" target="_blank">'+ title +'</a>';
											if ( products[ id ].type == 'variable' ) {
												popup += products[ id ].attributes;
											}
										popup += '</div>';
										popup += '<div class="ced_cng_popup-column"><span class="'+ stockClass +'">'+ stock +'</span></div>';
										popup += '<div class="ced_cng_popup-column">';
											popup += '<input step="1" min="1" max="" value="'+ qty +'" data-id="'+ item_id +'" onkeydown="return false" class="ced_cng_qty" size="4" pattern="[0-9]*" inputmode="numeric" type="number">';
										popup += '</div>';
									popup += '</div>';
			            		} else {
			            			excluded = 'checked disabled';
			            			popup += '<div class="ced_cng_product_info ced_cng_product-'+ item_id +'">';
										popup += '<div class="ced_cng_popup-column">';
											popup += '<input class="ced_cng_exclude_item" type="checkbox" value="'+ item_id +'" '+ excluded +' title="'+ exclude_dsc +'">';
										popup += '</div>';
										popup += '<div class="ced_cng_popup-column"><a href="'+ permalink +'" class="ced_cng_strikethrough" target="_blank">'+ title +'</a></div>';
										popup += '<div class="ced_cng_popup-column"><span class="'+ stockClass +'">'+ stock +'</span></div>';
										popup += '<div class="ced_cng_popup-column">';
											popup += '<input step="1" min="1" max="" value="'+ qty +'" data-id="'+ item_id +'" onkeydown="return false" class="ced_cng_qty" size="4" pattern="[0-9]*" inputmode="numeric" type="number">';
										popup += '</div>';
									popup += '</div>';
			            		}
			            		total++;
							}
							if ( total <= 0 ) {
								popup += '<div class="ced_cng_product_info ced_cng_product-'+ item_id +'">';
									popup += '<div class="ced_cng_popup-no-product">';
										popup += global_var.product_not_exist;
									popup += '</div>';
								popup += '</div>';
							}
							popup += '</div>';
						popup += '</div>';
						popup += '<div class="ced_cng_popup-buttons">';
							if ( total > 0 ) {
								if ( order_id != '' && order_id != 'undefined' && popup_type == 'order' ) {
									popup += '<a id="ced_cng_popup-submit-btn" class="ced_cng_popup-btn" data-order_id="'+ order_id +'"><span>'+ global_var.submit +'</span></a>';
								} else {
									popup += '<a id="ced_cng_popup_atc" class="ced_cng_popup-btn"><span>'+ global_var.atc +'</span></a>';
								}
							}
						popup += '</div>';
					}
				popup += '</div>';
			popup += '</div>';
		popup += '</div>';
		return popup;
	}

	$( document ).on( 'click', '#ced_cng_popup-submit-btn', function() {
		var $this = $( this ),
		order_id = $this.data( 'order_id' );
		
		ced_ocor_before_popup_submit( $this, 'checkout', 'order', order_id );
	});

	function ced_ocor_before_popup_submit( $this, action_type, popup_type, order_id ) {
		if ( action_type == '' ) {
			action_type = 'add_to_cart';
		}

		if ( popup_type == '' ) {
			action_type = 'order';
		}

		var excluded_products 	= [],
		quantities 				= {};
		if ( $this.is( '[disabled="disabled"]' ) ) {
			return false;
		}

		$( document ).find( '.ced_cng_exclude_item' ).each( function() {
			if ( $( this ).is( ':checked' ) ) {
				excluded_products.push( $( this ).val() );
			}
		});

		$( document ).find( '.ced_cng_qty' ).each( function() {
			quantities[ $( this ).data( 'id' ) ] = $( this ).val();
		});
		$this.css( 'opacity', '0.5');
		$this.attr( 'disabled', 'disabled' );

		if ( popup_type == 'order' ) {
			if ( action_type != 'add_to_cart' ) {
				ced_cng_same_order_ajax( order_id, excluded_products, quantities );
			}
		} else {
			if ( action_type == 'add_to_cart' ) {
				ced_ocor_add_to_cart( excluded_products, quantities );
			}
		}
	}

	function ced_cng_same_order_ajax( order_id, excluded_products, quantities ) {
		jQuery.ajax({
			url 	: global_var.ajaxurl,
			data 	: {
				action				: 'get_same_order_cart',
				ajax_nonce 			: global_var.ajax_nonce,
				order_id		 	: order_id,
				excluded_products	: excluded_products,
				quantities			: quantities
			},
			type 	: 'post',
			success	: function( response ) {
				window.location = global_var.checkouturl;
            }
		});
	}

	$( document ).on( 'click', '.ced_cng_close_wrapper', function() {
		$( this ).parents( '#ced_cng_prodcts_exclude' ).remove();

		if ( $( document ).find( '.ced_ocor_floating_basket_wrapper' ).hasClass( 'disabled' ) ) {
			$( document ).find( '.ced_ocor_floating_basket_wrapper' ).removeClass( 'disabled' );
		}
	});

	/*=============================================
	=            Add to basket section            =
	=============================================*/
	
	if ( $( '.variation_id' ).length > 0 ) {
		$( document ).on( 'change', '.variation_id', function() {
			var $this = $( this );
			if ( $this.val() != '' && typeof $this.val() != 'undefined' ) {
				if ( $( document ).find( 'a[ data-variation_id="'+ $this.val() +'"]' ).length > 0 ) {
					$( document ).find( '.ced_ocor_rfb' ).addClass( 'ced_cng_hide' );
					$( document ).find( 'a[ data-variation_id="'+ $this.val() +'"]' ).removeClass( 'ced_cng_hide' );
				} else {
					$( document ).find( '.ced_ocor_rfb' ).addClass( 'ced_cng_hide' );
					$( document ).find( '.ced_ocor_atb' ).removeClass( 'ced_cng_hide' );
				}
			} else {
				$( document ).find( '.ced_ocor_rfb' ).addClass( 'ced_cng_hide' );
				$( document ).find( '.ced_ocor_atb' ).addClass( 'ced_cng_hide' );
			}
		});
	}

	/**
	 * Add items into basket.
	 */
	$( document ).on( 'click', '.ced_ocor_atb', function( event ) {
		var $this 		= $( this ),
		item_id 		= $this.data( 'id' ),
		user_id 		= $this.data( 'user_id' ),
		type 			= $this.data( 'type' ),
		qty 			= $( 'input[name="quantity"]' ).length > 0 ? $( 'input[name="quantity"]' ).val() != 'undefined' ? $( 'input[name="quantity"]' ).val() > 0 ? $( 'input[name="quantity"]' ).val() : 1 : 1 : 1,
		variation_id 	= '';

		if ( item_id == '' || typeof item_id == 'undefined' || user_id == '' || typeof user_id == 'undefined' ) {
			return false;
		}

		if ( type == 'variable' ) {

			/**
			 * If no variations are selected yet.
			 */
			if ( $this.hasClass( 'ced_cng_hide' ) ) {
				return false;
			}

			/**
			 * Checking for variation id.
			 */
			variation_id = $( '.variation_id' ).val();
			if ( typeof variation_id == 'undefined' || variation_id == '' ) {
				return false;
			}
		}

		$this.addClass( 'loading' );
		$.ajax({
			url 	: global_var.ajaxurl,
			type 	: 'POST',
			data 	: {
				action 		: 'ced_ocor_add_to_basket',
				item_id 	: item_id,
				user_id 	: user_id,
				type 		: type,
				qty 		: qty,
				variation_id: variation_id,
				ajax_nonce 	: global_var.ajax_nonce,
			},
			success: function( response ) {
				try {
					$this.removeClass( 'loading' );
					if ( response.success ) {
						/**
						 * Apply animation effect into the basket icon.
						 */
						$( document ).find( '.ced_ocor_floating_basket_wrapper' ).addClass( 'rubberBand animated' );
						setTimeout(function() {
							$( document ).find( '.ced_ocor_floating_basket_wrapper' ).removeClass( 'rubberBand animated' );
						}, 1000 );

						/**
						 * Increase quantity into the basket after adding items into basket.
						 */
						var total_item_in_basket = $( document ).find( '.ced_ocor_basket_item_count' ).data( 'total' );
						total_item_in_basket = parseInt( total_item_in_basket );
						total_item_in_basket = total_item_in_basket + 1;
						$( document ).find( '.ced_ocor_basket_item_count' ).data( 'total', total_item_in_basket ).html( total_item_in_basket );
						
						/**
						 * If added item is not variable product.
						 */
						if ( type != 'variable' ) {
							var rfb_html = '<a rel="nofollow" class="ced_ocor_rfb button" id="ced_ocor_rfb_btn_'+ item_id +'" href="javascript:void(0);" data-id="'+ item_id +'" data-user_id="'+ user_id +'" title="'+ global_var.rfbBtnText +'">'+ global_var.rfbBtnText +'</a>';
							$this.parent( 'p.ced_ocor_basket' ).html( rfb_html );

						/**
						 * If added item is of variable type.
						 */
						} else {
							window.location = '';
						}
					} else {
						window.location = '';
					}
				} catch( e ) {
					console.log( e );
				}
			}
		});
		
	});

	/**
	 * Remove items from basket.
	 */
	$( document ).on( 'click', '.ced_ocor_rfb', function( event ) {
		var $this = $( this ),
		item_id = $this.data( 'id' ),
		user_id = $this.data( 'user_id' ),
		type 	= $this.data( 'type' ) != 'undefined' ? $this.data( 'type' ) : '',
		variation_id = '';

		if ( item_id == '' || typeof item_id == 'undefined' || user_id == '' || typeof user_id == 'undefined' ) {
			return false;
		}

		if ( type == 'variable' ) {

			/**
			 * Checking for variation id.
			 */
			variation_id = $( '.variation_id' ).val();
			if ( typeof variation_id == 'undefined' || variation_id == '' ) {
				return false;
			}
		}

		$this.addClass( 'loading' );
		$.ajax({
			url 	: global_var.ajaxurl,
			type 	: 'POST',
			data 	: {
				action 		: 'ced_ocor_remove_from_basket',
				item_id 	: item_id,
				user_id 	: user_id,
				type 		: type,
				variation_id: variation_id,
				ajax_nonce 	: global_var.ajax_nonce,
			},
			success: function( response ) {
				try {
					$this.removeClass( 'loading' );
					if ( response.success ) {
						/**
						 * Apply animation effect into the basket icon.
						 */
						$( document ).find( '.ced_ocor_floating_basket_wrapper' ).addClass( 'rubberBand animated' );
						setTimeout(function() {
							$( document ).find( '.ced_ocor_floating_basket_wrapper' ).removeClass( 'rubberBand animated' );
						}, 1000 );

						/**
						 * Decrease quantity into the basket after removing items from basket.
						 */
						var total_item_in_basket = $( document ).find( '.ced_ocor_basket_item_count' ).data( 'total' );
						total_item_in_basket = parseInt( total_item_in_basket );
						total_item_in_basket = total_item_in_basket - 1;

						/**
						 * If removing item is not of variable type.
						 */
						if ( type != 'variable' ) {
							var atb_html = '<a rel="nofollow" class="ced_ocor_atb button" id="ced_ocor_atb_btn_'+ item_id +'" href="javascript:void(0);" data-id="'+ item_id +'" data-user_id="'+ user_id +'" title="'+ global_var.atbBtnText +'">'+ global_var.atbBtnText +'</a>';
							$( document ).find( '.ced_ocor_basket_item_count' ).data( 'total', total_item_in_basket ).html( total_item_in_basket );

							$this.parent( 'p.ced_ocor_basket' ).html( atb_html );

						/**
						 * If removing items is of variable type.
						 */
						} else {
							window.location = '';
						}
					} else {
						var msg = '<span class="ced_ocor_atb_error">'+ response.data +'</span>';
						$this.parent( 'p.ced_ocor_basket' ).append( msg );
					}
				} catch( e ) {
					console.log( e );
				}
			}
		});
	});

	$( '.ced_ocor_floating_basket_wrapper' ).draggable({
		containment: 'window'
	});

	$( document ).on( 'click', '.ced_ocor_floating_basket_wrapper', function() {
		var $this 	= $( this ),
		totalItems 	= $this.find( 'ced_ocor_basket_item_count' ).data( 'total' );


		if ( $this.hasClass( 'disabled' ) ) {
			return false;
		}

		$this.addClass( 'disabled' );

		$.ajax({
			url 	: global_var.ajaxurl,
			data 	: {
				action		: 'ced_ocor_get_basket_items',
				ajax_nonce 	: global_var.ajax_nonce
			},
			type 	: 'post',
			success	: function( response ) {
				try {
					if ( response.success ) {
						var products 	= response.data;
						popup 			= createPopup( products, 'basket' );
						$( document.body ).prepend( popup );
					} else {
						window.location = '';
					}
				} catch( e ) {
					console.log( e );
				}
            }
		});
	});

	$( document ).on( 'click', '#ced_cng_popup_atc', function() {
		var $this 		= $( this );

		if ( $this.hasClass( 'disabled' ) ) {
			return false;
		}

		$this.addClass( 'disabled' );
		ced_ocor_before_popup_submit( $this, 'add_to_cart', 'basket' );
	});


	function ced_ocor_add_to_cart( excluded_items, quantities ) {
		$.ajax({
			url 	: global_var.ajaxurl,
			data 	: {
				action			: 'ced_ocor_add_basket_items_to_cart',
				ajax_nonce 		: global_var.ajax_nonce,
				excluded_items 	: excluded_items,
				quantities 		: quantities
			},
			type 	: 'post',
			success	: function( response ) {
				try {
					if ( response.success ) {
						window.location = global_var.cart_url;
					} else {
						console.log( response.data );
					}
				} catch( e ) {
					console.log( e );
				}
            }
		});
	}
	
	/*=====  End of Add to basket section  ======*/
	
});
