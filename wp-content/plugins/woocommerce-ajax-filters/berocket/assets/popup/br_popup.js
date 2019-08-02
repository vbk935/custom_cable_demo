;(function ( $ ) {
    $.br_popup_list = [];
    $.fn.br_popup = function( options ) {
        if (this.length > 1){
            this.each(function() { $(this).br_popup(options) });
            return this;
        }
        this.open_popup = function () {
            var popup_data = this.data('br_popup_data');
            if( ! popup_data.opened ) {
                this.reset_option();
                this.create_popup();
                this.add_content();
                this.add_events();
                this.show_popup();
            }
        };
        this.reset_option = function () {
            var popup_data = {timer_interval: undefined, close_delay: undefined, can_close_popup: true, opened:false};
            this.data('br_popup_data', popup_data);
        };
        this.create_popup = function() {
            var settings = this.data('br_popup_settings');
                $header = settings.title;

				$popup_html = '<div id="br_popup" class="br_popup animated';
                if ( settings.theme != 'default' && settings.theme != '' ) {
                    $popup_html += ' ' + settings.theme;
                }
                $popup_html += '"><div class="br_popup_wrapper" style="';
				if ( settings.width ) {
					$popup_html += 'width: '+settings.width+';';
				}
				if ( settings.height ) {
					$popup_html += 'height: '+settings.height+';';
				}
				
				$popup_html += '"><div class="animated popup_animation';
				if ( settings.yes_no_buttons.show == true
                     && settings.yes_no_buttons.custom == false
                     && settings.yes_no_buttons.location == 'popup'
                     || settings.print_button == true
                ) {
					$popup_html += ' with_yes_no_buttons';
				}
				$popup_html += ' yes_no_buttons_'+settings.yes_no_buttons.align;
				if ( $header != '' ) {
					$popup_html += ' with_header';
				}
                if ( settings.print_button == true ) {
                    $popup_html += ' with_print_button';
                }
				$popup_html += '">';
				
				if ( settings.no_x_button == false ) {
					$popup_html += '<a href="#" class="br_popup_close">×</a>';
				}
				
				if ( settings.close_delay * 1 > 0 ) {
					$popup_html += '<span class="counters after_close"><span>' + ( settings.close_delay * 1 ) + '</span> second(s) before close</span>';
				}

                if ( $header != '' ) {
                    $popup_html += '<div class="br_popup_header popup_header_' + settings.header_align + '">' + $header + '</div>';
                }
				
				$popup_html += '<div class="br_popup_inner">';
				if (
                    ( settings.yes_no_buttons.show == true
                      && settings.yes_no_buttons.custom == false
                      || settings.print_button == true
                    )
                    && settings.yes_no_buttons.location == 'content'
                ) {
					$popup_html += '<div class="br_popup_buttons">';
                    if ( settings.yes_no_buttons.show == true && settings.yes_no_buttons.custom == false ) {
                        $popup_html += '<a href="' + settings.yes_no_buttons.yes_text + '" class="br_yes_button ' +
                            settings.yes_no_buttons.yes_classes + '">' + settings.yes_no_buttons.yes_text + '</a><a href="' +
                            settings.yes_no_buttons.no_text + '" class="br_no_button ' + settings.yes_no_buttons.no_classes + '">' +
                            settings.yes_no_buttons.no_text + '</a>';
                    }
                    if ( settings.print_button == true ) {
                        $popup_html += '<a href="Print" class="print_button">Print</a>';
                    }
                    $popup_html += '</div>';
				}
				$popup_html += '</div>';
				
				if (
                    ( settings.yes_no_buttons.show == true
                      && settings.yes_no_buttons.custom == false
                      || settings.print_button == true
                    )
                    && settings.yes_no_buttons.location == 'popup'
                ) {
                    $popup_html += '<div class="br_popup_buttons">';
                    if ( settings.yes_no_buttons.show == true && settings.yes_no_buttons.custom == false ) {
                        $popup_html += '<a href="' + settings.yes_no_buttons.yes_text +'" class="br_yes_button ' +
                            settings.yes_no_buttons.yes_classes + '">' + settings.yes_no_buttons.yes_text + '</a><a href="' +
                            settings.yes_no_buttons.no_text +'" class="br_no_button ' +
                            settings.yes_no_buttons.no_classes + '">' + settings.yes_no_buttons.no_text + '</a>';
                    }
                    if ( settings.print_button == true ) {
                        $popup_html += '<a href="Print" class="print_button">Print</a>';
                    }
                    $popup_html += '</div>';
				}
				
				$popup_html += '</div></div>';
				
				if ( settings.no_overlay == false ) {
					$popup_html += '<div class="br_popup_overlay"></div>';
				}
				
				$popup_html += '</div>';
				$popup_html = $($popup_html);
                $popup_html.appendTo('body');
                $popup_html.data('br_popup_main', this);
                this.data('br_popup_object', $popup_html);
		};
		this.add_content = function() {
            var settings = this.data('br_popup_settings');
            if( settings.content ) {
                this.data('br_popup_object').find('.br_popup_inner').prepend( settings.content );
            } else {
                this.data('br_popup_object').find('.br_popup_inner').prepend( this.html() );
            } 
		};
        this.add_events = function() {
            var settings = this.data('br_popup_settings');
            var $this = this;
            if ( settings.close_with.includes('overlay') ) {
                $(this.data('br_popup_object')).on("click", ".br_popup_overlay", function (event){
                    event.preventDefault();
                    $this.hide_popup();
                });
            }
            
            if ( settings.close_with.includes('x_button') ) {
                $(this.data('br_popup_object')).on("click", ".br_popup_close", function (event){
                    event.preventDefault();
                    $this.hide_popup();
                });
            }

            if ( settings.close_with.includes('esc_button') ) {
                $(document).on("keydown", function (event){
                    if ( event.keyCode === 27 ) {
                        $this.hide_popup();
                    }
                });
            }
            
            if ( settings.yes_no_buttons.show == true && settings.yes_no_buttons.custom == false ) {
                $(this.data('br_popup_object')).on("click", settings.yes_no_buttons.yes_button, function (event){
                    event.preventDefault();
                    var popup_data = $this.data('br_popup_data');
                    if( popup_data.can_close_popup == false ) {
                        return;
                    }
                    if ( settings.close_with.includes('yes_button') ) {
                        $this.hide_popup();
                    }
                    
                    jQuery($this).trigger('br_popup-yes_button', $this);
                    if ( typeof settings.yes_no_buttons.yes_func === 'function' ) {
                        settings.yes_no_buttons.yes_func();
                    } else if( settings.yes_no_buttons.yes_func) {
                        try {
                            eval(settings.yes_no_buttons.yes_func);
                        } catch( error ) {
                            console.log('Incorrect function settings.yes_no_buttons.yes_func');
                        }
                    }
                });
            }
            
            if ( settings.yes_no_buttons.show == true && settings.yes_no_buttons.custom == false ) {
                $(this.data('br_popup_object')).on("click", settings.yes_no_buttons.no_button, function (event){
                    event.preventDefault();
                    var popup_data = $this.data('br_popup_data');
                    if( popup_data.can_close_popup == false ) {
                        return;
                    }
                    if ( settings.close_with.includes('no_button') ) {
                        $this.hide_popup();
                    }
                    
                    jQuery($this).trigger('br_popup-no_button', $this);
                    if ( typeof settings.yes_no_buttons.no_func === 'function' ) {
                        settings.yes_no_buttons.no_func();
                    } else if( settings.yes_no_buttons.no_func) {
                        try {
                            eval(settings.yes_no_buttons.no_func);
                        } catch( error ) {
                            console.log('Incorrect function settings.yes_no_buttons.no_func');
                        }
                    }
                });
            }

            if ( settings.print_button == true ) {
                $(this.data('br_popup_object')).on("click", '.print_button', function (event){
                    event.preventDefault();
                    $this.print();
                });
            }
        };
        this.show_popup = function() {
            var settings = this.data('br_popup_settings');
            var popup_data = this.data('br_popup_data');
			if ( this.data('br_popup_object') && this.data('br_popup_object').is(':hidden') ) {
                var $this = this;
                popup_data.opened = true;

                $('body').addClass('br_popup_opened');

                if ( settings.hide_body_scroll ) {
                    $('body').addClass('hide_scroll');
                }

                jQuery(this).trigger('br_popup-show_popup', this);
				this.data('br_popup_object').css({display:'block'});
				this.animateCss(this.data('br_popup_object'), 'fadeIn');
				this.animateCss(this.data('br_popup_object').find('.popup_animation'), 'fadeInDown');
				
				if ( settings.close_delay * 1 > 0 ) {
					this.data('br_popup_object').addClass('counting');
					popup_data.can_close_popup = false;
					
					popup_data.close_delay = settings.close_delay * 1 - 1;
					popup_data.timer_interval = setInterval(function (){
						if ( popup_data.close_delay <= 0 ) {
                            popup_data.can_close_popup = true;
							clearInterval(popup_data.timer_interval);
                            $this.data('br_popup_object').removeClass('counting');
						} else {
							$this.data('br_popup_object').find('.counters span').text(popup_data.close_delay);
						}
						popup_data.close_delay--;
                        $this.data('br_popup_data', popup_data);
					}, 1000);
				}
                this.data('br_popup_data', popup_data);
			}
		};
		this.hide_popup = function () {
            var $this = this;
            var popup_data = this.data('br_popup_data');
			if ( popup_data.can_close_popup == true && this.data('br_popup_object').hasClass('br_popup') && this.data('br_popup_object').is(':visible') ) {
                var $this = this;
                
                jQuery(this).trigger('br_popup-hide_popup', this);
                clearInterval(popup_data.timer_interval);
				this.animateCss(this.data('br_popup_object').find('.popup_animation'), 'fadeOutUp');
				this.animateCss(this.data('br_popup_object'), 'fadeOut', function (){
					$this.data('br_popup_object').remove();
                    popup_data.opened = false;
					popup_data.can_close_popup = true;
                    $('body').removeClass('br_popup_opened hide_scroll');
                    $this.data('br_popup_data', popup_data);
				});
			}
		};
		this.animateCss = function (element, animationName, callback) {
            element = $(element);
            element.addClass('animated').addClass(animationName);

			function handleAnimationEnd() {
				element.removeClass('animated').removeClass(animationName);
				element.off('animationend', handleAnimationEnd);

				if ( typeof callback === 'function' ) callback()
			}

			element.on('animationend', handleAnimationEnd);
		};
        this.print = function() {
            $('body').addClass('print');
            window.print();
            $('body').removeClass('print');
        };

        var settings = this.data('br_popup_settings');
        if ( settings ) {
            settings = $.extend( true, settings, options );
        } else {
            this.reset_option();
            $.br_popup_list.push(this);
            settings = $.extend( true, {
                title:          '',                 // title for popup header
                content:        '',                 // html from this element will be duplicated to the popup
                height:         '',  	            // popup height ( with px or %)
                width:          '',                 // popup weight ( with px or %)
                no_overlay:     false,              // don't use overlay
                no_x_button:    false,              // don't show x button
                header_align:   'left',             // align header text
                yes_no_buttons: {      				// yes and no buttons to catch the action from user
                    custom: 	false, 				// show own buttons or use default
                    show:   	false,            	// show buttons
                    yes_button: '.br_yes_button',   // class or id for the yes button. Don't change it unless custom is true
                    no_button:  '.br_no_button',    // class or id for the no button. Don't change it unless custom is true
                    yes_func:   '',					// your function to run when yes is clicked
                    no_func:    '',					// your function to run  when no is clicked
                    yes_text:   'Accept',			// text shown on yes button
                    no_text:    'Decline',			// text shown on no button
                    yes_classes:'',					// text shown on yes button
                    no_classes: '',					// text shown on no button
                    location:   'popup',			// where to show buttons: 'content' - under the content, 'popup' - bottom of popup
                    align:      'right' 			// align text: 'right', 'left', 'center'
                },
                print_button: false,                // show print button for popup
                close_with:     [
                    'overlay',         				// popup will be closed if catch click on overlay
                    'x_button', 	   				// popup will be closed if catch click on x button
                    'yes_button',      				// popup will be closed if catch click on yes overlay
                    'no_button',       				// popup will be closed if catch click on no overlay
                    'esc_button'       				// popup will be closed if catch esc mouse down
                ],
                close_delay:    0,					// don't allow popup close for X seconds
                effects: {							// effects list is here - https://github.com/daneden/animate.css
                    open: {			   				// when popup is opening
                        effect: 'fadeInDown'
                    },
                    close: {		   				// when popup is closing
                        effect: 'fadeOutUp'
                    }
                },
                theme: "default",                   // default, sweet-alert, simple-shadow
                themes_folder_url: "./themes/",     // url where themes are located if you want popup to load theme
                hide_body_scroll: false             // if true body will get overflow hidden on popup opened
            }, options );
            settings = $.extend( true, settings, $(this).data());
        }
        this.data('br_popup_settings', settings);
        return this;
    }
	
}( jQuery ));
