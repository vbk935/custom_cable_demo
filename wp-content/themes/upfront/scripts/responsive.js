(function($){
	$.fn.responsiveElement = function(){
		return this.each(function(){
			var r_id = 'responsive-'+(Math.floor(Math.random()*100000)),
				bind = $(this).attr('data-bind'),
				editor = ( $(this).attr('data-editor') == 1 ),
				on_editor = false,
				min_width = normalize_size($(this).attr('data-min-width')),
				max_width = normalize_size($(this).attr('data-max-width')),
				min_height = normalize_size($(this).attr('data-min-height')),
				max_height = normalize_size($(this).attr('data-max-height')),
				styles = $(this).html(),
				applied_styles = {},
				lazyApplyBinding,
				delayedStyleRender = true,
				styleRenderQueue = [],


			lazyApplyBinding = _.throttle(function(e){
				if ( e.target == this )
					apply_binding_all();
				else
					apply_binding($(e.target), true);
			}, 100);

			$(window).on('load', apply_binding_all);
			$(window).on('resize', lazyApplyBinding);
			$(document).on('upfront-load', function(){
				if ( typeof Upfront.Events != 'undefined' ) {
					// Attach events after render complete to improve load time
					Upfront.Events.once("layout:render", function(){
						on_editor = true;
						// Run once on startup
						apply_binding_all();
						Upfront.Events.on("layout:after_render", function(){
							apply_binding_all();
						});
						Upfront.Events.on("entity:modules:render_module", apply_binding_view);
						Upfront.Events.on("entity:regions:render_region", apply_binding_view_region);
						Upfront.Events.on("entity:regions:render_container", apply_binding_view);
						//Upfront.Events.on("entity:resize_stop", apply_binding_view_region);
						//Upfront.Events.on("entity:drag_stop", apply_binding_view_region);
						Upfront.Events.on("upfront:wrappers:after_fix_height", apply_binding_view);
						Upfront.Events.on("entity:region_container:resize_stop", apply_binding_view);
						Upfront.Events.on("upfront:editor:image_on", function(sel){
							apply_binding_all();
						});
						Upfront.Events.on("upfront:editor:image_align", function(sel, align){
							apply_binding_all();
						});
					});

					Upfront.Events.on('upfront:renderingqueue:done', function() {
						delayedStyleRender = false;
						_.each(styleRenderQueue, function(callback) {
							callback();
						});
						styleRenderQueue = [];
					});
					Upfront.Events.on('upfront:renderingqueue:start', function() {
						delayedStyleRender = true;
					});
				}
			});
			function apply_binding_all (sel) {
				var $sel = _.isString(sel) && sel != '' ? $(sel) :  $('.upfront-layout');
				return apply_binding($sel);
			}
			function apply_binding_view (view) {
				return apply_binding(view.$el.parent());
			}
			function apply_binding_view_region (view) {
				if ( bind.match(/\.upfront-region-container/) ) {
					apply_binding_all();
				}
				else {
					var $region = view.$el.hasClass('.upfront-region') ? view.$el : view.$el.closest('.upfront-region');
					apply_binding( bind.match(/\.upfront-region/) ? $region.parent() : $region );
				}
			}
			function apply_binding ($sel, single) {
				var changed = false;

				if ( editor && !on_editor ) return;
				$sel.find(bind+':visible').each(function(){
					var $el = single ? $(this).closest('.upfront-module') : $(this),
						id = $(this).attr('id') || 'bind-'+(Math.floor(Math.random()*100000)),
						width = parseFloat($el.css('width')),
						height = parseFloat($el.css('height'))
					;
					if ( ! $(this).attr('id') ) {
						$(this).attr('id', id);
					}
					var matched = (
						( (min_width && width >= min_width) || !min_width ) &&
						( (max_width && width <= max_width) || !max_width ) &&
						( (min_height && height >= min_height) || !min_height ) &&
						( (max_height && height <= max_height) || !max_height )
					);
					if ( typeof applied_styles[id] == 'undefined' ) {
						applied_styles[id] = '';
					}
					if ( matched ) {
						if ( applied_styles[id] == '' ) {
							applied_styles[id] = styles.replace(/\( ?this ?\)/igm, '#'+id);
							changed = true;
						}
					}
					else {
						if ( applied_styles[id] != '' ) {
							applied_styles[id] = '';
							changed = true;
						}
					}
				});
				if ( !changed ) {
					return;
				}

				var appendStyles = function() {
					var $style;
					var styles_all = $.map(applied_styles, function(style, id){
						return style;
					});
					if ( !$('#'+r_id).length ) {
						$style = $('<style id="' + r_id + '">' + styles_all.join("\n") + '</style>');
						$('body').append($style);
					} else {
						$('#'+r_id).html( styles_all.join("\n") );
					}
				};

				if (delayedStyleRender) {
					styleRenderQueue.push(appendStyles);
				} else {
					appendStyles();
				}
			}
			function normalize_size (size) {
				if ( typeof size == 'undefined' )
					return false;
				var px_regex = /^(\d+)px$/i,
					col_regex = /^(\d+)col$/i,
					row_regex = /^(\d+)row$/i;
				if ( size.match(px_regex) ) // px
					return size.match(px_regex)[1];
			}
		});
	};

	$('[type="text/responsive_css"]').responsiveElement();

})(jQuery);
