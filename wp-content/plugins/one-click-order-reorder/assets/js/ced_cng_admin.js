jQuery( document ).ready( function( $ ) {
	if ( $( '.ced_ocor_enable_basket_for' ).length > 0 ) {
		$( '.ced_ocor_enable_basket_for' ).select2();
	}

	$( document ).on( 'click', '.ced_ocor_enabling_basket', function( event ) {
		var $this = $( this ),
		basket_section = $( '#ced_ocor_basket_section' );
		if ( $this.val() == 'enable' ) {
			basket_section.removeClass( 'ced_cng_hide' );
		} else {
			basket_section.addClass( 'ced_cng_hide' );
		}
	});

	$( document ).on( 'click', '#ced_ocor_save_general_setting', function( event ) {
		var $this 		= $( this ),
		same_order 		= $( '.ced_ocor_enable_same_order:checked' ).val(),
		selectedUser 	= $( document ).find( '#ced_ocor_enable_basket_for_users' ).val(),
		basketEnable 	= $( '.ced_ocor_enabling_basket:checked' ).val(),
		basketPages 	= $( document ).find( '#ced_ocor_enable_basket_for_pages' ).val(),
		icon_uri 		= $( document ).find( '#cec_ocor_saved_icon_url' ).val(),
		atbBtnText 		= $( document ).find( '#ced_ocor_basket_btn_text' ).val(),
		rfbBtnText 		= $( document ).find( '#ced_ocor_basket_remove_btn_text' ).val();
		
		if ( same_order == '' || typeof same_order == 'undefined' || typeof selectedUser == 'undefined' ) {
			$( '#ced_ocor_empty_messages' ).removeClass( 'ced_cng_hide' );
			return false;
		}

		$this.next( 'span.spinner' ).addClass( 'is-active' );
		$.ajax({
			url 	: globals.ajaxurl, 
			type 	: 'POST',
			data 	: {
				action 		: 'ced_ocor_save_general_setting',
				same_order 	: same_order,
				selectedUser: selectedUser,
				basketEnable: basketEnable,
				basketPages : basketPages,
				atbBtnText 	: atbBtnText,
				rfbBtnText 	: rfbBtnText,
				icon_uri 	: icon_uri,
				nonce_check : globals.nonce_check
			},
			success: function( response ) {
				try {
					$this.next( 'span.spinner' ).removeClass( 'is-active' );
					var msgHtml = '';
					if ( response.success ) {
						msgHtml += '<div class="updated notice notice-success is-dismissible">';
							msgHtml += '<p>'+ response.data +'</p>';
							msgHtml += '<button type="button" class="notice-dismiss">';
								msgHtml += '<span class="screen-reader-text">Dismiss this notice.</span>';
							msgHtml += '</button>';
						msgHtml += '</div>';
					} else {
						msgHtml += '<div class="error notice notice-error is-dismissible">';
							msgHtml += '<p>'+ response.data +'</p>';
							msgHtml += '<button type="button" class="notice-dismiss">';
								msgHtml += '<span class="screen-reader-text">Dismiss this notice.</span>';
							msgHtml += '</button>';
						msgHtml += '</div>';
					}
					$( '#ced_ocor_messages' ).html( msgHtml );
				} catch( e ) {
					console.log( e );
				}
			}
		});
	});

	$( document ).on( 'click', '.notice-dismiss', function( event ) {
		$( this ).parents( '#ced_ocor_messages' ).html( '' );
	});

	$( document ).on( 'click', '#ced_ocor_icon_for_basket', function() {
		tb_show( 'Upload custom icon image for basket', 'media-upload.php?type=image&amp;TB_iframe=true');
		window.original_send_to_editor = window.send_to_editor;
		window.send_to_editor = function( html ) {
			if( html ) {
				var attchUrl = $( html ).attr( 'src' );
				$( '#ced_ocor_attachment_section img' ).attr( 'src', attchUrl );
				$( '#cec_ocor_saved_icon_url' ).val( attchUrl );
			} else {
				window.original_send_to_editor( html );
			}
			tb_remove();
		};
		return false;
	});

	$( document ).on( 'click', '.ced_ocor_attachment_selector', function() {
		$( this ).prev( 'img' ).attr( 'src', '' );
	});
});