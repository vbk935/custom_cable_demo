
;(function($,sr){

  // debouncing function from John Hann
  // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
  var debounce = function (func, threshold, execAsap) {
	  var timeout;

	  return function debounced () {
		  var obj = this, args = arguments;
		  function delayed () {
			  if (!execAsap)
				  func.apply(obj, args);
			  timeout = null;
		  }

		  if (timeout)
			  clearTimeout(timeout);
		  else if (execAsap)
			  func.apply(obj, args);

		  timeout = setTimeout(delayed, threshold || 400);
	  };
  };
  // smartresize
  jQuery.fn[sr] = function(fn){  return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr); };

})(jQuery,'smartresize');

jQuery(document).ready(function($) {

	var $win = $(window),
		_cache = {}
	;
	var FloatNav = function ($el) {
		var adminbarheight = ($('div#wpadminbar').length > 0)?$('div#wpadminbar').outerHeight():0;
		var start_position = {
			top: 0,
			left: 0
		};
		var start_size = {
			width: 0,
			height: 0
		};
		var $root = $el;

		var start_floating = function () {

			$current_offset = $root.offset();

			$root.attr("data-already_floating", "yes");

			$root.closest('.upfront-output-wrapper').css('z-index', 999);
			$root.closest('.upfront-output-region-container').css('z-index', 999);

			if($root.hasClass('responsive_nav_toggler'))
				$root.offset($current_offset);
			else
				$root.css(start_size);

			if(adminbarheight > 0)
				$root.css('margin-top', adminbarheight);
		};

		var stop_floating = function () {
			$root
				.attr("style", "")
				.attr("data-already_floating", "no")
			;
			$root.closest('.upfront-output-wrapper').css('z-index', '');
			$root.closest('.upfront-output-region-container').css('z-index', '');
			if(adminbarheight > 0)
				$root.css('margin-top', '');
		};

		var dispatch_movement = function () {
			var top = $win.scrollTop();

			if (top > (start_position.top-adminbarheight) && !$root.is('[data-already_floating="yes"]')) start_floating();
			else if (top <= (start_position.top-adminbarheight) && $root.is('[data-already_floating="yes"]')) stop_floating();
		};

		var destroy = function () {
			start_position = {
				top: 0,
				left: 0
			};
			start_size = {
				width: 0,
				height: 0
			};
			$root = false;
			$win.off("scroll", dispatch_movement);
		};

		var init = function () {

			start_position = $root.offset();
			start_size = {width: $root.width(), height: $root.height()};
			$win
				.off("scroll", dispatch_movement)
				.on("scroll", dispatch_movement)
			;
		};
		init();

		return {
			destroy: destroy
		};
	};

	function floatInit () {
		//lets do the clean up first
		$(".upfront-navigation").each(function () {
			var $me = $(this);

			if ($me.data('style') === 'burger' || $me.data('style') === 'burger') {
				$toggler = $me.children('.responsive_nav_toggler');
				$toggler.attr('id', $me.attr('id')+'-toggler');
				if (_cache[$toggler.attr("id")]) _cache[$toggler.attr("id")].destroy();
			} else {
				if (_cache[$me.attr("id")]) _cache[$me.attr("id")].destroy();
			}
		});

		$(".upfront-navigation.upfront-navigation-float").each(function () {
			var $me = $(this);

			if($me.data('style') === 'burger' || $me.data('style') == 'burger') {
				$toggler = $me.children('.responsive_nav_toggler');
				$toggler.attr('id', $me.attr('id')+'-toggler');
				//if (_cache[$toggler.attr("id")]) _cache[$toggler.attr("id")].destroy();
				_cache[$toggler.attr("id")] = new FloatNav($toggler);
			}
			else {
				//if (_cache[$me.attr("id")]) _cache[$me.attr("id")].destroy();
				_cache[$me.attr("id")] = new FloatNav($me);
			}
		});
	}

	$win.on('load', floatInit);
	
	function hasNavInit() {
		//Work around for having the region container have a higher z-index if it contains the nav, so that the dropdowns, if overlapping to the following regions should not loose "hover" when the mouse travels down to the next region.
		$('div.upfront-navigation').each(function() {
			if (
				$(this).find('ul.sub-menu').length > 0
				//Also fix issues with overlapping elements with expanded hamburger menu.
				|| $(this).find('ul#menu-top-nav-menu').length > 0
			) {
				$(this).closest('.upfront-output-region-container, .upfront-output-region-sub-container').each(function() {
					$(this).addClass('upfront-region-container-has-nav');
				});

				//Make sure parent wrapper have higher z-index
				$(this).closest('.upfront-output-module').css({'z-index': '9999', position: 'relative'});
			}
		});
	}
	
	hasNavInit();
	
	// Show burger nav on enter
	$('.responsive_nav_toggler, .burger_nav_close').keydown(function(e) {
		if (e.which == 13) {
			$(this).closest('.upfront-navigation').find('.responsive_nav_toggler').trigger('click');
		}
	});
	
	// Hide burger nav if latest link
	$('.menu li a').on('focusout', function() {
		if($(this).parent().is(":last-child")) {
			$(this).closest('.upfront-navigation').find('.responsive_nav_toggler').trigger('click');
		}
	});

	$('body').on('touchstart click', '.burger_nav_close, .burger_overlay', null, function() {
		$(this).closest('.upfront-navigation').find('.responsive_nav_toggler').trigger('click');
	});

	$('body').on('touchstart click', '.upfront-navigation .upfront-navigation .responsive_nav_toggler', null, function(e) {
		e.preventDefault();
		if($(this).parent().find('ul.menu').css('display') == 'none') {
			$(this).closest('div.upfront-output-wrapper').addClass('on_the_top');

			if($(this).parent().attr('data-burger_over') != 'pushes' && $(this).parent().attr('data-burger_alignment') != 'whole') {
				$('<div class="burger_overlay"></div>').insertBefore($(this).parent().find('ul.menu'));
			}

			$(this).parent().attr('data-burger_open', "1");

			$(this).parent().find('ul.menu').show();
			//$(this).parent().find('ul.sub-menu').show();


			var offset = $(this).parent().find('ul.menu').position();

			//$(e.target).closest('.responsive_nav_toggler').css({position: 'fixed', left: offset.left, top: offset.top+(($('div#wpadminbar').length && $('div#wpadminbar').css('display') == 'block')?$('div#wpadminbar').outerHeight():0)});
			//$(this).parent().find('ul.menu').css('padding-top', '60px');
			var close_icon = $('<button class="burger_nav_close"></button>');

			$(this).parent().find('ul.menu').prepend($('<li>').addClass('wrap_burger_nav_close').append(close_icon));

			//close_icon.css({position: 'fixed', left: offset.left+$(this).parent().find('ul.menu').width()-close_icon.width()-10, top: offset.top+(($('div#wpadminbar').length && $('div#wpadminbar').css('display') == 'block')?$('div#wpadminbar').outerHeight():0) + 10});

			/*

			if($(this).parent().attr('data-burger_over') == 'pushes')
				pushContent($(this).parent());
			*/


			if($(this).parent().attr('data-burger_over') == 'pushes' && $(this).parent().attr('data-burger_alignment') == 'top') {

				$('div#page').css('margin-top', $(this).parent().find('ul.menu').outerHeight());


				//var topbar_height = $('div#upfront-ui-topbar').outerHeight();
				var adminbar_height = ($('div#wpadminbar').length > 0)?$('div#wpadminbar').outerHeight():0;

				$(this).parent().find('ul.menu').offset({top:adminbar_height, left:$('div').offset().left});
				$(this).parent().find('ul.menu').css('width', $('div#page').width() + 'px');

			}


			$(this).closest('.upfront-output-region-container').each(function() {
				$(this).addClass('upfront-region-container-has-nav');
			});

			$(document).trigger('upfront-responsive-nav-open');

		}
		else {
			$(this).parent().find('ul.menu').hide();
			$(this).parent().find('ul.menu').siblings('.burger_overlay').remove();
			//$(this).parent().find('ul.sub-menu').hide();

			$(this).parent().removeAttr('data-burger_open');

			//$(e.target).closest('.responsive_nav_toggler').css({position: '', left: '', top: ''});
			$(this).parent().find('ul.menu').css({
				top: '',
				left: '',
				width: ''
			});

			$('.burger_nav_close').parent('li.wrap_burger_nav_close').remove();

			$(this).closest('div.upfront-output-wrapper').removeClass('on_the_top');

			/*
			if($(this).parent().attr('data-burger_over') == 'pushes')
				pullContent($(this).parent());
			*/

			if($(this).parent().attr('data-burger_over') == 'pushes')
				$('div#page').css('margin-top', '');


			$(this).closest('.upfront-output-region-container').each(function() {
				$(this).removeClass('upfront-region-container-has-nav');
			});

			$(document).trigger('upfront-responsive-nav-close');
		}
	});

	function pushContent(nav) {
		var currentwidth = $('div#page').width();
		var navwidth = nav.find('ul.menu').width();
		var navheight = nav.find('ul.menu').height();

		$('div#page').css('margin-'+nav.data('burger_alignment'), (nav.data('burger_alignment') == 'top' || nav.data('burger_alignment') == 'whole')?navheight:navwidth);

		if(nav.data('burger_alignment') == 'left' || nav.data('burger_alignment') == 'right') {
			$('div#page').css('width', currentwidth-navwidth);
			$('div#page').css('minWidth', currentwidth-navwidth);
		}
	}

	function pullContent(nav) {
		$('div#page').css('margin-'+nav.data('burger_alignment'), '');
		$('div#page').css('width', '');
		$('div#page').css('minWidth', '');
	}

	// the following is used to find the current breakpoint on resize
	var previous_breakpoint = '';
	var current_breakpoint = '';

	function get_breakpoint(){
		if (!window.getComputedStyle) {
				window.getComputedStyle = function(el, pseudo) {
				this.el = el;
				this.getPropertyValue = function(prop) {
					var re = /(\-([a-z]){1})/g;
					if (prop == 'float') prop = 'styleFloat';
					if (re.test(prop)) {
						prop = prop.replace(re, function () {
							return arguments[2].toUpperCase();
						});
					}
					return el.currentStyle[prop] ? el.currentStyle[prop] : null;
				};
				return this;
			};
		}

		var breakpoint = window.getComputedStyle(document.body,':after').getPropertyValue('content');

		if(breakpoint === null && $('html').hasClass('ie8')) {
			breakpoint = window.get_breakpoint_ie8($( window ).width());
			$(window).trigger('resize');
		}

		if(breakpoint) {
			breakpoint = breakpoint.replace(/['"]/g, '');
			if (current_breakpoint != breakpoint) {
				previous_breakpoint = current_breakpoint;
				current_breakpoint = breakpoint;
			}
			return breakpoint;
		}
	}

	var upfrontIsLoaded = function() {
		// Check if cssEditor is defined, than we have Upfront loaded
		if (typeof Upfront !== 'undefined' && typeof Upfront.Application !== 'undefined' && typeof Upfront.Application.cssEditor !== 'undefined') {
			return true;
		}

		return false;
	};


	function roll_responsive_nav(selector, bpwidth) {
		if (upfrontIsLoaded()) return;

		var elements = (typeof(selector) == 'object')?selector:$(selector);
		elements.each(function () {

			var breakpoints = $(this).data('breakpoints');

			var usingNewAppearance = $(this).data('new-appearance');

			var currentwidth = (typeof(bpwidth) != 'undefined') ? parseInt(bpwidth, 10) : $(window).width();

			var currentKey, preset, responsive_css;

			if (breakpoints.preset) {
				currentKey = get_breakpoint();
				if(currentKey === '')
					currentKey = 'desktop';

				preset = breakpoints.preset[currentKey];

				if (!preset) return;

				/** if breakpoint has menu_style set to burger, but no
				 burger_alignment is defined, set it to default
				 **/
				if(preset && preset.menu_style === 'burger' && !preset.burger_alignment ) {
					preset.burger_alignment= 'left';
				}

				if (preset.menu_style == 'burger') {
					$(this).attr('data-style', 'burger');
					$(this).attr('data-stylebk', 'burger');
					$(this).attr('data-alignment', ( preset.menu_alignment ? preset.menu_alignment : $(this).data('alignmentbk') ));
					$(this).attr('data-burger_alignment', preset.burger_alignment);
					$(this).attr('data-burger_over', preset.burger_over);

					// Add responsive nav toggler
					if(!$(this).find('.responsive_nav_toggler').length)
						$(this).prepend($('<button class="responsive_nav_toggler"><div></div><div></div><div></div></button>'));

					//offset a bit if admin bar or side bar is present
					if($('div#wpadminbar').length && $('div#wpadminbar').css('display') == 'block') {
						$(this).find('ul.menu').css('margin-top', $('div#wpadminbar').outerHeight());
					}

					/*if ($(this).hasClass('upfront-output-unewnavigation')) {
						$('head').find('style#responsive_nav_sidebar_offset').remove();
						responsive_css = 'div.upfront-navigation div[data-style="burger"][ data-burger_alignment="top"] ul.menu, div.upfront-navigation div[data-style="burger"][ data-burger_alignment="whole"] ul.menu {left:'+parseInt($('div.upfront-regions').offset().left)+'px !important; right:'+parseInt(($(window).width()-currentwidth-$('div#sidebar-ui').outerWidth()) / 2)+'px !important; } ';
						responsive_css = responsive_css + 'div.upfront-navigation div[data-style="burger"][ data-burger_alignment="left"] ul.menu {left:'+parseInt($('div.upfront-regions').offset().left)+'px !important; right:inherit !important; width:'+parseInt(30/100*$('div.upfront-regions').outerWidth())+'px !important;} ';
						responsive_css = responsive_css + 'div.upfront-navigation div[data-style="burger"][ data-burger_alignment="right"] ul.menu {left:inherit !important; right:'+parseInt(($(window).width()-currentwidth-$('div#sidebar-ui').outerWidth()) / 2)+'px !important; width:'+parseInt(30/100*$('div.upfront-regions').outerWidth())+'px !important; } ';
						responsive_css = responsive_css + 'div.upfront-navigation div[data-style="burger"] ul.menu {top:'+parseInt($('div#upfront-ui-topbar').outerHeight())+'px !important; } ';

						$('head').append($('<style id="responsive_nav_sidebar_offset">'+responsive_css+'</style>'));
					}
					//Z-index the container module to always be on top, in the layout edit mode
					$(this).closest('div.upfront-newnavigation_module').css('z-index', 3);*/


					$(this).find('ul.menu').hide();
				} else {
					if(typeof usingNewAppearance !== "undefined" && usingNewAppearance) {
						$(this).attr('data-style', ( preset.menu_style ? preset.menu_style : $(this).data('stylebk') ));
						$(this).attr('data-alignment', ( preset.menu_alignment ? preset.menu_alignment : $(this).data('alignmentbk') ));
					} else {
						//We should reset the data-style and data-alignment if element is not migrated
						if(typeof breakpoints[currentKey] !== "undefined" && typeof breakpoints[currentKey].menu_style !== "undefined") {
							$(this).attr('data-style', ( breakpoints[currentKey].menu_style ));
						}
						if(typeof breakpoints[currentKey] !== "undefined" && typeof breakpoints[currentKey].menu_alignment !== "undefined") {
							$(this).attr('data-alignment', ( breakpoints[currentKey].menu_alignment ));
						}
					}

					$(this).removeAttr('data-burger_alignment','');
					$(this).removeAttr('data-burger_over', '');

					// Remove responsive nav toggler
					$(this).find('.responsive_nav_toggler').remove();
					$(this).find('ul.menu').show();

					//remove any display:block|none specifications from the sub-menus
					$(this).find('ul.menu, ul.sub-menu').each(function() {
						$(this).css('display', '');
					});

					// remove any adjustments done because of the sidebar or the adminbar
					if($('div#wpadminbar').length) {
						$(this).find('ul.menu').css('margin-top', '');
					}


					//remove the z-index from the container module
					//$(this).closest('div.upfront-newnavigation_module').css('z-index', '');
				}

				$(this).find('ul.menu').siblings('.burger_overlay').remove();

				if(preset.is_floating && preset.is_floating == 'yes')
					$(this).addClass('upfront-navigation-float');
				else
					$(this).removeClass('upfront-navigation-float');
			} else {
				// Leave old code for backward compatibility
				var bparray = [];
				for (var key in breakpoints) {
					if (key !== 'preset') bparray.push(breakpoints[key]);
				}

				bparray.sort(function(a, b) {
					if (a && b && a.width && b.width) {
						return a.width - b.width;
					}
					return 0;
				});

				for (key in bparray) {
					if(bparray[key] && bparray[key]['width'] && parseInt(currentwidth, 10) >= parseInt(bparray[key]['width'], 10)) {

						if(bparray[key]['burger_menu'] == 'yes') {

							$(this).attr('data-style', 'burger');
							$(this).attr('data-alignment', ( bparray[key]['menu_alignment'] ? bparray[key]['menu_alignment'] : $(this).data('alignmentbk') ));
							$(this).attr('data-burger_alignment', bparray[key]['burger_alignment']);
							$(this).attr('data-burger_over', bparray[key]['burger_over']);

							// Add responsive nav toggler
							if(!$(this).find('.responsive_nav_toggler').length)
								$(this).prepend($('<button class="responsive_nav_toggler"><div></div><div></div><div></div></button>'));

							//offset a bit if admin bar or side bar is present
							if($('div#wpadminbar').length && $('div#wpadminbar').css('display') == 'block') {
								$(this).find('ul.menu').css('margin-top', $('div#wpadminbar').outerHeight());
								//$(this).find('div.responsive_nav_toggler').css('margin-top', $('div#wpadminbar').outerHeight());
							}

							/*if($(this).hasClass('upfront-output-unewnavigation')) {

								$('head').find('style#responsive_nav_sidebar_offset').remove();
								responsive_css = 'div.upfront-navigation div[data-style="burger"][ data-burger_alignment="top"] ul.menu, div.upfront-navigation div[data-style="burger"][ data-burger_alignment="whole"] ul.menu {left:'+parseInt($('div.upfront-regions').offset().left)+'px !important; right:'+parseInt(($(window).width()-currentwidth-$('div#sidebar-ui').outerWidth()) / 2)+'px !important; } ';

								responsive_css = responsive_css + 'div.upfront-navigation div[data-style="burger"][ data-burger_alignment="left"] ul.menu {left:'+parseInt($('div.upfront-regions').offset().left)+'px !important; right:inherit !important; width:'+parseInt(30/100*$('div.upfront-regions').outerWidth())+'px !important;} ';

								responsive_css = responsive_css + 'div.upfront-navigation div[data-style="burger"][ data-burger_alignment="right"] ul.menu {left:inherit !important; right:'+parseInt(($(window).width()-currentwidth-$('div#sidebar-ui').outerWidth()) / 2)+'px !important; width:'+parseInt(30/100*$('div.upfront-regions').outerWidth())+'px !important; } ';
								responsive_css = responsive_css + 'div.upfront-navigation div[data-style="burger"] ul.menu {top:'+parseInt($('div#upfront-ui-topbar').outerHeight())+'px !important; } ';

								$('head').append($('<style id="responsive_nav_sidebar_offset">'+responsive_css+'</style>'));
							}
							//Z-index the container module to always be on top, in the layout edit mode
							$(this).closest('div.upfront-newnavigation_module').css('z-index', 3);*/

							$(this).find('ul.menu').siblings('.burger_overlay').remove();
							$(this).find('ul.menu').hide();
						}
						else {
							$(this).attr('data-style', ( bparray[key]['menu_style'] ? bparray[key]['menu_style'] : $(this).data('stylebk') ));
							$(this).attr('data-alignment', ( bparray[key]['menu_alignment'] ? bparray[key]['menu_alignment'] : $(this).data('alignmentbk') ));
							$(this).removeAttr('data-burger_alignment','');
							$(this).removeAttr('data-burger_over', '');

							// Remove responsive nav toggler
							$(this).find('.responsive_nav_toggler').remove();
							$(this).find('ul.menu').show();

							//remove any display:block|none specifications from the sub-menus
							$(this).find('ul.menu, ul.sub-menu').each(function() {
								$(this).css('display', '');
							});

							// remove any adjustments done because of the sidebar or the adminbar
							if($('div#wpadminbar').length) {
								$(this).find('ul.menu').css('margin-top', '');
							}

							//remove the z-index from the container module
							//$(this).closest('div.upfront-newnavigation_module').css('z-index', '');
						}

						if(bparray[key]['is_floating'] && bparray[key]['is_floating'] == 'yes')
							$(this).addClass('upfront-navigation-float');
						else
							$(this).removeClass('upfront-navigation-float');
					}
				}
			}
		});
	}
	roll_responsive_nav(".upfront-output-unewnavigation > .upfront-navigation");

	$(window).smartresize(function() {
		$('div#page').css('margin-top', '');
		$('.responsive_nav_toggler').css({position: '', left: '', top: ''});
		$('ul.menu').css('padding-top', '');
		$('.burger_nav_close').parent('li.wrap_burger_nav_close').remove();

		roll_responsive_nav(".upfront-output-unewnavigation > .upfront-navigation");
		floatInit();
	});

	$(document).on('changed_breakpoint', function(e) {
		roll_responsive_nav( e.selector, e.width);
	});
	
	/**
		TOGGLING BREAKPOINT MENU
	**/
	$(document).on('upfront-breakpoint-change', function(e, breakpoint) {
		hasNavInit();
		toggle_breakpoint_menu(breakpoint);
	});
	function toggle_breakpoint_menu(breakpoint) {
		breakpoint = breakpoint || 'desktop';
		$('.upfront-output-object.upfront-output-unewnavigation').each(function(){
			var $this = $(this),
				$target = get_target_breakpoint_menu($this, breakpoint)
			;
			if ( $target.length ) {
				$this.find('.upfront-breakpoint-navigation').hide();
				$target.show();
			}
		});
	}
	// proper fallback to higher menu if target breakpoint menu not set
	function get_target_breakpoint_menu(parent, breakpoint) {
		var $target = parent.find('.upfront-'+ breakpoint +'-breakpoint-navigation');
		if ( breakpoint == 'mobile' ) {
			// fallback to tablet menu
			if ( $target.length == 0 ) $target = parent.find('.upfront-tablet-breakpoint-navigation');
			// fallback to desktop menu
			if ( $target.length == 0 ) $target = parent.find('.upfront-desktop-breakpoint-navigation');
		} else if ( breakpoint == 'tablet' ) {
			// fallback to desktop menu
			if ( $target.length == 0 ) $target = parent.find('.upfront-desktop-breakpoint-navigation');
		} 
		return $target;
	}
	toggle_breakpoint_menu(window.upfront_get_breakpoint());
});
