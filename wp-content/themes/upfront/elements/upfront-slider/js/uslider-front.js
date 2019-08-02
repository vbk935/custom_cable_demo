jQuery(function($){
	var setHeight = function(texts){
		var max = 0;
		texts.find('.uslide-text').each(function(){
			max = Math.max(max, $(this).height());
		});
		texts.height(max);
	};

	setTimeout(function(){
		$('.uslider-below').each(function(){
			setHeight($(this).find('.uslider-texts'));
		});
		$('.uslider-above').each(function(){
			setHeight($(this).find('.uslider-texts'));
		});
	}, 300);

	var magOptions = {
		type: 'image',
		gallery: {
			enabled: 'true',
			tCounter: '<span class="mfp-counter">%curr% / %total%</span>'
		},
		titleSrc: 'title',
		verticalFit: true,
		image: {
			titleSrc: 'title',
			verticalFit: true
		}
	};
	$('.uslider').each( function() {
		$(this).find('.uslider_lightbox_link').magnificPopup(magOptions);
	});
});

jQuery(function($){
	$('.upfront-output-uslider').each( function() {
		var slider = $(this),
			options = slider.find('.uslider').data();

		/* if it is enclosed inside a lightbox,
		 * then set it up on first instance of
		 * the lightbox showing up. Also store a flag
		 * issetup in order to avoid repeating this process
		 */

		if(slider.closest('div.upfront-region-lightbox').length ) {

			// slider should stay hidden before the lightbox opens up,
			// or it kills the lightbox alignment because of its extra height
			slider.hide();

			var lightbox = slider.closest('div.upfront-region-lightbox');

			$(document).on("upfront-lightbox-open", function(e, which) {
				// no need to set it up again and again
				if(slider.data('issetup'))
					return;

				// identify that the event is triggered for the same lightbox
				if($(which).attr('id') !== lightbox.attr('id'))
					return;

				// do the thing
				slider.show();
				setupSlider(slider);

				slider.data('issetup', true);

			});

			return;
		}

		$(document).on("upfront-breakpoint-change", function (e, breakpoint) {
			onBreakpointChange(slider, breakpoint);
			updateSlider(slider);
		});

		setupSlider(slider);

		function setupSlider(slider) {
			slider.find('.uslides').upfront_default_slider(options);
			onBreakpointChange(slider, upfront_get_breakpoint());
			updateSlider(slider);
		}

		function updateSlider(slider) {
			var uslides = slider.find('.uslides'),
				caption_height = 0
			;
			slider.find('.uslide-above').each(function(){
				var slide = $(this);
				slide.find('.uslide-caption').remove().prependTo(slide);
				caption_height = 1;
			});
			slider.find('.uslide-below').each(function(){
				var slide = $(this);
				slide.find('.uslide-caption').remove().appendTo(slide);
				caption_height = 1;
			});
			slider.find('.uslide-left').each(function(){
				var slide = $(this);
				slide.find('.uslide-caption').remove().prependTo(slide);
			});
			slider.find('.uslide-right').each(function(){
				var slide = $(this);
				slide.find('.uslide-caption').remove().appendTo(slide);
			});
			slider.find('.uslide-bottomOver, .uslide-middleCover, .uslide-bottomCover, .uslide-topCover').each(function() {
				var slide = $(this);
				slide.find('.uslide-caption').remove().prependTo(slide.find('.uslide-image'));
			});
			uslides.attr('data-caption_height', caption_height).trigger('refresh');
		}

		function onBreakpointChange(slider, breakpoint) {
			breakpoint = !breakpoint ? 'desktop' : breakpoint;
			slider.find('.uslide').each(function(){
				var slide = $(this),
					breakpoint_data = slide.attr('data-breakpoint'),
					map = breakpoint_data ? JSON.parse(breakpoint_data) : {},
					default_style = slide.attr('data-style'),
					all_styles = ['uslide-' + default_style]
				;
				for ( var bp in map ) {
					if ( !map[bp]['style'] ) continue;
					all_styles.push('uslide-' + map[bp]['style']);
				}
				if ( 'desktop' == breakpoint ) {
					slide.removeClass(all_styles.join(' ')).addClass('uslide-' + default_style);
				}
				else if ( breakpoint in map && map[breakpoint]['style'] ) {
					slide.removeClass(all_styles.join(' ')).addClass('uslide-' + map[breakpoint]['style']);
				}
			});
		}
	});
	
	
	/**
	 * Fix DOM children responsive preset classes
	 *
	 * Legacy preset elements double up their preset classes in DOM children,
	 * which doesn't get pick up by the responsive preset processing in `layout.js`.
	 *
	 * This is where we handle those cases, for the current element
	 *
	 * @param {Object} e Event - ignore
	 * @param {String} breakpoint The current breakpoint to inherit
	 */
	$(document).on("upfront-responsive_presets-changed", function (e, breakpoint) {
		$(".upfront-uslider").each(function () {
			var $root = $(this),
				rmap = $root.attr("data-preset_map"),
				map = rmap ? JSON.parse(rmap) : {},
				$items
			;
			
			// we have to provide proper fallback here, mobile -> tablet -> desktop
			if ( breakpoint == 'mobile' ) {
				map[breakpoint] = map[breakpoint] || map['tablet'] || map['desktop'];
			} else if ( breakpoint == 'tablet' ) {
				map[breakpoint] = map[breakpoint] || map['desktop'];
			} else {
				map[breakpoint] = map[breakpoint];
			}
			
			$items = $root.find(".uslider-caption-background");
			$.each(map, function (bp, preset) {
				$items.removeClass('slider-preset-' + preset);
				if (bp === breakpoint && typeof preset !== "undefined") $items.addClass('slider-preset-' + preset);
			});

		});
	});
	
});
