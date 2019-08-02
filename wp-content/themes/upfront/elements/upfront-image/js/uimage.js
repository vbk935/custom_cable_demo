(function ($) {
define([
	'text!elements/upfront-image/tpl/image.html',
	'text!elements/upfront-image/tpl/image_editor.html',
	'elements/upfront-image/js/image-context-menu',
	'elements/upfront-image/js/image-settings',
	'elements/upfront-image/js/image-selector',
	'elements/upfront-image/js/image-editor',
	'elements/upfront-image/js/image-element',
	"scripts/upfront/link-model",
	'elements/upfront-image/js/model',
	'text!elements/upfront-image/tpl/preset-style.html',
	'scripts/upfront/preset-settings/util'
], function(imageTpl, editorTpl, ImageContextMenu, ImageSettings, ImageSelector, ImageEditor, ImageElement, LinkModel, UimageModel, settingsStyleTpl, PresetUtil) {

	var l10n = Upfront.Settings.l10n.image_element;
	var breakpointColumnPadding = Upfront.Views.breakpoints_storage.get_breakpoints().get_active().get('column_padding');
	breakpointColumnPadding = parseInt(breakpointColumnPadding, 10);
	breakpointColumnPadding = _.isNaN(breakpointColumnPadding) ? 15 : breakpointColumnPadding;

	var UimageView = Upfront.Views.ObjectView.extend({
		model: UimageModel,
		imageTpl: Upfront.Util.template(imageTpl),
		sizehintTpl: _.template($(editorTpl).find('#sizehint-tpl').html()),
		cropTimeAfterResize: 1,// making this longer makes image resize not save

		// Disable size hint as image element already has it's own
		display_size_hint: false,

		// Property used to speed resizing up;
		resizingData: {},

		initialize: function() {
			var me = this;
			this.setDefaults();

			if(! (this.model instanceof UimageModel)){
				this.model = new UimageModel({properties: this.model.get('properties')});
			}
			this.events = _.extend({}, this.events, {
				'click a.upfront-image-select': 'openImageSelector',
				'click div.upfront-quick-swap': 'openImageSelector',
				'dblclick .wp-caption': 'editCaption',
				'click a': 'handleLinkClick',
				'click .swap-image-overlay': 'openImageSelector'
			});
			this.delegateEvents();

			this.bodyEventHandlers = {
				dragover: function(e){
					e.preventDefault();
					me.handleDragEnter(e);
				},
				dragenter: function(e){
					me.handleDragEnter(e);
				},
				dragleave: function(e){
					me.handleDragLeave(e);
				}
			};

			$('body').on('dragover', this.bodyEventHandlers.dragover)
				.on('dragenter', this.bodyEventHandlers.dragenter)
				.on('dragleave', this.bodyEventHandlers.dragleave)
				.on('drop', this.bodyEventHandlers.drop)
			;

			// Set the full size current size if we don't have attachment id
			if (!this.property('image_id')) {
				this.property('srcFull', this.property('src'));
			}

			this.listenTo(this.model.get('properties'), 'change', this.update);
			this.listenTo(this.model.get('properties'), 'add', this.update);
			this.listenTo(this.model.get('properties'), 'remove', this.update);

			this.listenTo(this.model, 'uimage:edit', this.editRequest);

			//this.controls = this.createControls();

			if(this.property('image_status') !== 'ok' || this.property('quick_swap') || (this.isThemeImage() && !Upfront.themeExporter)) {
				this.property('has_settings', 0);
			}
			else {
				this.property('has_settings', 1);
			}

			this.listenTo(Upfront.Events, 'upfront:import_image:populate_theme_images', this.populate_theme_images);
			this.listenTo(Upfront.Events, 'upfront:import_image:imported', this.imported_theme_image);

			this.listenTo(Upfront.Events, 'upfront:element:edit:start', this.on_element_edit_start);
			this.listenTo(Upfront.Events, 'upfront:element:edit:stop', this.on_element_edit_stop);

			this.listenTo(Upfront.Events, 'command:layout:save', this.saveResizing);
			this.listenTo(Upfront.Events, 'command:layout:save_as', this.saveResizing);

			this.listenTo(Upfront.Events, "preset:image:updated", this.caption_updated, this);

			this.listenTo(Upfront.Events, 'upfront:layout_size:change_breakpoint', function(newMode){
				if(newMode.id !== 'desktop') {
					this.mobileMode = true;
				} else {
					this.unsetMobileMode();
				}
			});
			this.listenTo(Upfront.Events, "upfront:layout_size:change_breakpoint", this.on_change_breakpoint);
			this.listenTo(Upfront.Events, "upfront:layout_size:change_breakpoint:after", this.on_change_breakpoint_after);
			this.listenTo(Upfront.Events, "upfront:grid:updated", this.on_grid_update);

			if (this.property('link') === false) {
				this.link = new LinkModel({
					type: this.property('when_clicked'),
					url: this.property('image_link'),
					target: this.property('link_target')
				});
				this.property('link', this.link.toJSON());
			} else {
				this.link = new LinkModel(this.property('link'));
			}

			me.listenTo(this.link, 'change', function() {
				me.property('link', me.link.toJSON());
			});

			this.listenTo(Upfront.Events, 'entity:module:update', this.on_uimage_update);
			this.listenTo(this.model, "preset:updated", this.preset_updated);

			// Check if preset exist, if not replace with default
			this.check_if_preset_exist();
		},

		get_preset_properties: function() {
			var preset = this.model.get_property_value_by_name("preset"),
				props = PresetUtil.getPresetProperties('image', preset) || {};

			return props;
		},

		get_preset_property: function(prop_name) {
			var preset = this.model.get_property_value_by_name("preset"),
				props = PresetUtil.getPresetProperties('image', preset) || {};

			return props[prop_name];
		},
		preset_updated: function(preset) {
			this.render();
			Upfront.Events.trigger('preset:image:updated', preset);
		},

		caption_updated: function(preset) {
			var currentPreset = this.model.get_property_value_by_name("preset");

			//If element use updated preset re-render
			if(currentPreset === preset) this.render();
		},

		update_colors: function () {

			var props = this.get_preset_properties();

			if (_.size(props) <= 0) return false; // No properties, carry on

			PresetUtil.updatePresetStyle('gallery', props, settingsStyleTpl);

		},

		populate_theme_images: function (image_list) {
			if ( this.isThemeImage() ) image_list.push(this.property('srcFull'));
		},

		imported_theme_image: function (image) {
			var src = this.property('srcFull');
			if ( this.isThemeImage() && image.fullpath == src ) {
				this.property('image_id', image.id);
				this.property('srcFull', image.src);
				this.property('srcOriginal', image.src);
			}
		},

		setDefaults: function(){
			this.sizes = false;
			this.originalDesktopElementSize = {width: 0, height: 0};
			this.elementSize = {width: 0, height: 0};
			this.imageId = 0;
			this.imageSize = {width: 0, height: 0};
			this.imageOffset = {top: 0, left: 0};
			this.maskOffset = {top: 0, left: 0};
			this.imageInfo  = false;
			this.controls = false;
			this.editor = false;

			//Temporary props for element resizing and cropping
			this.temporaryProps = {
				size: false,
				position: false
			};
			this.cropTimer = false;
			this.stoppedTimer  = false;
		},

		getSelectedAlignment: function(){
			if(!this.property('include_image_caption') && this.get_preset_property("caption-position") === false && this.property('caption_alignment') === false) {
				return 'nocaption';
			}
			if(this.get_preset_property("caption-position") === 'below_image') {
				return 'below';
			}

			var align = this.property('caption_alignment');

			switch(align){
				case 'top':
					return 'topOver';
				case 'bottom':
					return 'bottomOver';
				case 'fill':
					return 'topCover';
				case 'fill_middle':
					return 'middleCover';
				case 'fill_bottom':
					return 'bottomCover';
			}

			return 'nocaption';
		},

		isThemeImage: function() {
			return this.property('srcFull') && this.property('srcFull').match(Upfront.mainData.currentThemePath);
		},

		replaceImage: function() {
			this.openImageSelector();
		},

		createLinkControl: function(){
			var me = this,
				control = new Upfront.Views.Editor.InlinePanels.LinkControl(),
				linkPanel;

			control.view = linkPanel = new Upfront.Views.Editor.LinkPanel({
				model: this.link,
				linkTypes: { image: true },
				imageUrl: this.property('srcFull')
			});

			this.listenTo(control, 'panel:ok', function() {
				if(linkPanel.model.get('type') == 'lightbox' && linkPanel.$el.find('.js-ulinkpanel-lightbox-input').val() !== '') {
					linkPanel.createLightBox();
				}
				control.close();
				this.render();
			});

			this.listenTo(control, 'panel:open', function() {
				me.$el.closest('.ui-draggable').draggable('disable');
				_.delay( function() {
					me.controls.$el.parent().parent().addClass('upfront-control-visible');
				}, 1000);
			});

			me.listenTo(control, 'panel:close', function(){
				me.controls.$el.parent().parent().removeClass('upfront-control-visible');
				me.$el.closest('.ui-draggable').draggable('enable');
			});

			// Close panel when event is triggered (enter key is hit).
			me.listenTo(linkPanel, 'linkpanel:close', function() {
				control.close();
			});

			// Update wrapper size
			me.listenTo(linkPanel, 'linkpanel:update:wrapper', function() {
				control.updateWrapperSize();
			});

			control.icon = 'link';
			control.tooltip = l10n.ctrl.image_link;
			control.id = 'link';

			return control;
		},

		postTypes: function(){
			var types = [];
			_.each(Upfront.data.ugallery.postTypes, function(type){
				if(type.name !== 'attachment') {
					types.push({name: type.name, label: type.label});
				}
			});
			return types;
		},

		editCaption: function(){
			var me = this,
				captionEl = $('#' + this.property('element_id')).find('.wp-caption')
			;


			if(captionEl.find('.uimage-caption-cover').length) {
				captionEl = captionEl.find('.uimage-caption-cover');
			}

			if(captionEl.data('ueditor') || ! captionEl.length) { //Already instantiated
				return;
			}

			captionEl.ueditor({
					autostart: false,
					focus: false,
					upfrontMedia: false,
					upfrontImages: false,
					linebreaks: false,
					airButtons: ['upfrontFormatting', 'bold', 'italic', 'stateAlign', 'upfrontLink', 'upfrontColor', 'upfrontIcons']
				})
				.on('start', function(){
					me.$el.addClass('upfront-editing');
				})
				.on('stop', function(){
					me.$el.removeClass('upfront-editing');
					me.render();
				})
				.on('syncAfter', function(){
					me.property('image_caption', captionEl.html());
				})
			;
		},

		createControl: function(icon, tooltip, click){
			var me = this,
				item = new Upfront.Views.Editor.InlinePanels.Control();
			item.icon = icon;
			item.tooltip = tooltip;
			if(click){
				this.listenTo(item, 'click', function(e){
					me[click](e);
				});
			}

			return item;
		},

		setImageInfo: function(){
			var maskSize, maskOffset,
				starting = this.$('.upfront-image-starting-select'),
				size = this.temporaryProps.size, //this.property('size'),
				position = this.temporaryProps.position, //this.property('position'),
				captionHeight = this.get_preset_property("caption-position") === 'below_image' ? this.$('.wp-caption').outerHeight() : 0
			;

			if (starting.length) {
				maskSize = {
					width: starting.outerWidth(),
					height: starting.outerHeight()
				};
				maskOffset = starting.offset();
				position = false;
			} else {
				starting = this.$('.uimage');
				maskSize = {
					width: starting.width(),
					height: starting.height() - captionHeight
				};
				maskOffset = {
					top: starting.offset().top,
					left: starting.offset().left
				};
			}
	/*
			//Fix for responsive images
			if(img.length){
				size = {
					width: img.width(),
					height: img.height()
				},
				position = {
					top: -img.position().top,
					left: -img.position().left
				}

			}
	*/
			this.imageInfo = {
				id: this.property('image_id'),
				src: this.property('src'),
				srcFull: this.property('srcFull'),
				srcOriginal: this.property('srcOriginal'),
				size: size,
				position: position,
				rotation: this.property('rotation'),
				fullSize: this.property('fullSize'),
				align: this.property('align'),
				valign: this.property('valign'),
				isDotAlign: this.property('isDotAlign'),
				maskSize: maskSize,
				maskOffset: maskOffset
			};

		},

		isSmallImage: function() {
			var elementSize = this.model.get_breakpoint_property_value('element_size', true);
			if (this.resizingData.data && this.resizingData.data.elementSize) {
				elementSize = this.resizingData.data.elementSize;
			}
			return elementSize.width < 100 || elementSize.height < 50;
		},

		disableCaption: function() {
			this.property('include_image_caption', false);
		},

		enableCaption: function() {
			this.property('include_image_caption', true);
		},

		hasCaptionPosition: function() {
			if (this.property('usingNewAppearance') === true) {
				return this.get_preset_property("caption-position") !== false || this.property('caption_alignment') !== false;
			}
			return this.property('include_image_caption');
		},

		setupBySize: function() {
			if (this.isSmallImage()) {
				this.disableCaption();
				this.parent_module_view.$el.addClass('uimage-small');
			} else if(this.hasCaptionPosition()) {
				this.enableCaption();
			}

			if (!this.isSmallImage()) {
				this.parent_module_view.$el.removeClass('uimage-small');
			}
		},

		get_content_markup: function () {
			var elementSize = this.model.get_breakpoint_property_value('element_size', true),
				me = this,
				props = this.extract_properties(),
				rendered,
				smallSwap,
				render,
				size,
				img;

			this.setupBySize();

			if(!this.temporaryProps || !this.temporaryProps.size) {
				this.temporaryProps = {
					size: props.size,
					position: props.position
				};
			}

			props = this.extract_properties();

			props.properties = this.get_preset_properties();

			props.url = this.property('when_clicked') ? this.property('image_link') : false;
			props.url = this.link.get('type') !== 'unlink' ? this.link.get('url') : false;
			props.link_target = this.link.get('target');
			props.element_size = elementSize;
			props.size = this.temporaryProps.size;
			props.position = this.temporaryProps.position;
			props.marginTop = Math.max(0, -props.position.top);

			props.in_editor = true;

			props.cover_caption = this.get_preset_property("caption-position") !== 'below_image';

			if(props.stretch) {
				props.imgWidth = '100%';
				props.stretchClass = ' uimage-stretch';
			} else {
				props.imgWidth = props.size.width + 'px';
				props.stretchClass = '';
			}

			props.containerWidth = Math.min(props.size.width, elementSize.width);

			props.display_caption = this.property('display_caption') ? this.property('display_caption') : 'showCaption';

			//Gif image handled as normal ones in the backend
			props.gifImage = '';
			props.gifLeft = 0;
			props.gifTop = 0;

			/* Commented to allow caption below image to have background
			if (props.caption_position === 'below_image') {
				props.captionBackground = false;
			}
			*/

			props.l10n = l10n.template;

			props.usingNewAppearance = props.usingNewAppearance || false;

			// Clean up hardcoded caption color
			if (props.usingNewAppearance) {
				props.image_caption = props.image_caption.replace(/^<span style=".+?"/, '<span ');
			}

			rendered = this.imageTpl(props);

			if (this.property('quick_swap')) {
				smallSwap = props.element_size.width < 150 || props.element_size.height < 90 ? 'uimage-quick-swap-small' : '';

				if(Upfront.Application.user_can_modify_layout()) {
					rendered += '<div class="upfront-quick-swap ' + smallSwap + '"><p>Change this image</p></div>';
				}
			} else if (this.property('image_status') === 'starting') {
				if(Upfront.Application.user_can_modify_layout()) {
					rendered = '<div class="upfront-image-starting-select upfront-ui" style="height:' + props.element_size.height + 'px"><div class="uimage-centered">' +
							'<span class="upfront-image-resizethiselement">' + l10n.ctrl.add_image + '</span><div class=""><a class="upfront-image-select" href="#" title="' + l10n.ctrl.add_image + '">+</a></div>'+
					'</div></div>';
				} else {
					rendered = '';
				}
			} else {
				render = $('<div></div>').append(rendered);
				size = props.size;
				img = render.find('img');
				props = this.temporaryProps;

				var newElementSize = this.update_style();

				if(newElementSize) {
					elementSize = newElementSize;
					size = this.temporaryProps.size;
				}

				// Let's load the full image to improve resizing
				render.find('.upfront-image-container').css({
					overflow: 'hidden',
					position: 'relative',
					width: Math.min(elementSize.width, size.width),
					height: Math.min(elementSize.height, size.height),
				});

				img.attr('src', me.property('srcFull'))
					.css({
						width: size.width,
						height: size.height,
						position: 'absolute',
						top: Math.min(0, -props.position.top),
						left: Math.min(0, -props.position.left),
						'margin-top': 0,
						'max-height': 'none',
						'max-width': 'none'
					})
				;

				rendered = render.html();
			}

			return rendered;
		},
		toggle_caption_controls: function(){
			if (!Upfront.Application.user_can_modify_layout()) return false;

			var me = this,
				panel = new Upfront.Views.Editor.InlinePanels.Panel()
				;

			panel.items = this.getControlItems();
			panel.render();
			_.delay( function(){
				me.controls.$el.html( panel.$el );
				me.updateControls();
			}, 400);
		},
		getElementShapeSize: function (elementSize) {
			var $container = this.$el.find('.upfront-image-container'),
				props = this.get_preset_properties(),
				newSize = {};

			if (props.imagestyle === "square") {
				newSize.height = elementSize.width;
				newSize.width = elementSize.width;

				if (elementSize.width !== elementSize.height) {
					// Keep old height in case style switches to default
					newSize.defaultHeight = elementSize.height;
				} else if (elementSize.defaultHeight) {
					newSize.defaultHeight = elementSize.defaultHeight;
				}

				return newSize;
			} else {
				if (elementSize.defaultHeight) {
					newSize = {
						width: elementSize.width,
						height: elementSize.defaultHeight
					};
					return newSize;
				}
			}

			return false;
		},

		update_style: function() {
			var elementSize = this.model.get_breakpoint_property_value('element_size', true),
				newSize = this.getElementShapeSize(elementSize)
			;

			if ( false !== newSize ) {
				if ( elementSize.width != newSize.width || elementSize.height != newSize.height ) {
					if ( ! this.resizeImage(newSize) ) {
						// Can't resize? At least set the element_size
						this.model.set_breakpoint_property('element_size', newSize);
					}
				}
				return newSize;
			}

			return false;
		},

		on_render: function() {
			var me = this,
				onTop = ['bottom', 'fill_bottom'].indexOf(this.property('caption_alignment')) !== -1 || this.get_preset_property("caption-position") === 'below_image' ? ' sizehint-top' : '',
				elementSize = this.model.get_breakpoint_property_value('element_size', true);

			//Bind resizing events
			if (!this.parent_module_view.$el.data('resizeHandling')) {
				this.parent_module_view.$el
					//.on('resizestart', $.proxy(this.onElementResizeStart, this))
					//.on('resize', $.proxy(this.onElementResizing, this))
					//.on('resizestop', $.proxy(this.onElementResizeStop, this))
					.data('resizeHandling', true);
			}

			if(this.property('when_clicked') === 'lightbox') {
				this.$('a').addClass('js-uimage-open-lightbox');
			}

			var newElementSize = this.update_style();

			if(newElementSize) {
				elementSize = newElementSize;
			}

			if (this.isThemeImage() && !Upfront.themeExporter) {
				if (Upfront.Application.user_can_modify_layout()) {
					this.$el.addClass('image-from-theme');
					this.$el.find('b.upfront-entity_meta').after('<div class="swap-image-overlay"><p class="upfront-icon upfront-icon-swap-image"><span>Click to </span>Swap Image</p></div>');
				}
			} else {
				var resizeHint = $('<div>').addClass('upfront-ui upfront-entity-size-hint uimage-resize-hint' + onTop);
				this.$el.append(resizeHint);
				setTimeout( function () {
					me.applyElementSize(false, false, false); // don't update property here
				}, 300 );
			}

			if(this.property('image_status') !== 'ok') {
				var starting = this.$('.upfront-image-starting-select');
				if(!this.elementSize.height){
					this.setElementSize();
					starting.height(this.elementSize.height);
				}
				return;
			}

			if (this.property('quick_swap')) { // Do not show image controls for swappable images.
				return false;
			}

			setTimeout(function() {
				me.updateControls();
				me.$el.removeClass('upfront-editing');

				me.editCaption();
			}, 300);

			// Show full image if we are in mobile mode
			if (this.mobileMode) {
				this.$('.uimage').addClass('uimage-mobile-mode');
				this.once('update_position', this.setMobileMode); // Run setMobileMode after positioning finished
			}

			this.setStuckToTop();

			setTimeout( function() {
				me.$el.closest('.ui-draggable').on('dragstop', function() {
					setTimeout(function() {
						me.setStuckToTop();
					}, 10);
				});

				me.$el.closest('.upfront-module-view').addClass('uimage-upfront-module-view');
			}, 100);

			setTimeout(function() {
				me.toggleResizableHandles();
			}, 100);

			this.toggle_caption_controls();
		},

		toggleResizableHandles: function() {
			var container = this.$el.parents('.upfront-objects_container');
			if (this.isThemeImage() && !Upfront.themeExporter) {
				container.siblings('.ui-resizable-handle').addClass('ui-resizable-handle-hidden');
			} else {
				container.siblings('.ui-resizable-handle').removeClass('ui-resizable-handle-hidden');
			}
		},

		setStuckToTop: function() {
			if (this.$el.offset().top + this.parent_module_view.$el.offset().top < 25) {
				this.$el.addClass('stuck-to-top');
			} else {
				this.$el.removeClass('stuck-to-top');
			}
		},

		updateControls: function() {
			var elementControlsTpl = '<div class="upfront-element-controls upfront-ui"></div>';

			if(this.paddingControl && typeof this.paddingControl.isOpen !== 'undefined' && this.paddingControl.isOpen)	return;

			if (!this.$control_el || this.$control_el.length === 0) {
				this.$control_el = this.$el;
			}
			if ( this.controls ) {
				this.controls.remove();
				this.controls = false;
				this.$control_el.find('>.upfront-element-controls').remove();
			}
			this.controls = this.createControls();

			if (this.controls === false) return;

			this.controls.render();
			if (this.$control_el.find('>.upfront-element-controls').length === 0) {
				this.$control_el.append(elementControlsTpl);
				this.$control_el.find('>.upfront-element-controls').html('').append(this.controls.$el);
			}
			this.updateAdvancedPadding();
			this.controls.delegateEvents();
		},


		on_edit: function(){
			return false;
		},

		extract_properties: function() {
			var props = {};
			this.model.get('properties').each(function(prop){
				props[prop.get('name')] = prop.get('value');
			});
			props.preset = props.preset || 'default';
			return props;
		},

		handleDragEnter: function(){
			var me = this;
			// todo Sam: re-enable this and start bug fixing
			return; // disabled for now
			if(!this.$('.uimage-drop-hint').length){
				var dropOverlay = $('<div class="uimage-drop-hint"><div>' + l10n.drop_image + '</div></div>')
					.on('drop', function(e){
						e.preventDefault();
						e.stopPropagation();
						me.openImageSelector();
						$('.uimage-drop-hint').remove();
						if(e.originalEvent.dataTransfer){
							var files = e.originalEvent.dataTransfer.files;
							// Only call the handler if 1 or more files was dropped.
							if (files.length){
								Upfront.Views.Editor.ImageSelector.uploadImage(files);
										}
								}
					})
					.on('dragenter', function(e){
						e.preventDefault();
						e.stopPropagation();
						$(this).addClass('uimage-dragenter');
					})
					.on('dragleave', function(e){
						e.preventDefault();
						e.stopPropagation();
						$(this).removeClass('uimage-dragenter');
						$(this).removeClass('uimage-drageover');
					}).on('dragover', function (e) {
											e.preventDefault();
											$(this).addClass('uimage-drageover');
									})
				;
				this.$('.upfront-image').append(dropOverlay);
			}
			if(this.dragTimer){
				clearTimeout(this.dragTimer);
				this.dragTimer = false;
			}
		},

		handleDragLeave: function(){
			var me = this;
			this.dragTimer = setTimeout(function(){
					me.$('.uimage-drop-hint').remove();
					this.dragTimer = false;
				}, 200)
			;
		},

		setMobileMode: function(){
			var props = this.extract_properties(),
				row = this.model.get_breakpoint_property_value('row', false)
			;
			this.mobileMode = true;
			this.$el
				.find('.uimage-resize-hint').hide().end()
				.find('.uimage').css('min-height', 'none')
				.find('.upfront-image-caption-container').css('width', '100%').end()
				.find('.upfront-image-container').css('width', '100%').css('height', 'auto').end()
				.find('img')
					.css({
						position: 'static',
						maxWidth: '100%',
						width: ( props.stretch ? '100%' : props.size.width ),
						height: 'auto'
					})
					.attr('src', this.property('src'))
			;
			if ( false === row ) { // No row defined in this element breakpoint, remove defined height
				this.$el.find('> .upfront-object').css('min-height', '');
				if ( this.parent_module_view ) {
					this.parent_module_view.$el.find('> .upfront-module').css('min-height', '');
				}
			}
		},

		unsetMobileMode: function(){
			this.mobileMode = false;
		},

		updateBreakpointPadding: function(breakpointColumnPadding) {
			var image_el = this.$el.find('.upfront-image');

			if(image_el.css("padding") !== "") {
				return parseInt(image_el.css("padding"), 10);
			}

			return breakpointColumnPadding;
		},

		after_breakpoint_change: function(){

			this.originalDesktopElementSize = this.property('element_size');

			//if(this.mobileMode) {
				this.render(); // @TODO prefer not to re-render on breakpoint change
			//}
		},

		/***************************************************************************/
		/*           Handling element resize events (jQuery resizeable)            */
		/***************************************************************************/

		on_element_resize_start: function(attr) {
			if(this.mobileMode) {
				return;
			}

			var starting = this.$('.upfront-image-starting-select'); // Add image panel

			if(this.get_preset_property("caption-position") !== 'below_image') {
				this.$('.wp-caption').fadeOut('fast');
			}

			// Store variables used in resize event handlers
			this.resizingData = {
				starting: starting,
				data: {
					position: this.property('position'),
					size: this.property('size'),
					checkSize: this.checkSize(),
					stretch: this.property('stretch'),
					vstretch: this.property('vstretch')
				},
				img: this.$('img'),
				setTextHeight: this.get_preset_property("caption-position") === 'below_image'
			};

			if(this.cropTimer) {
				clearTimeout(this.cropTimer);
				this.cropTimer = false;
			}

			if(starting.length) {
				return;
			}

			if(this.resizingData.data.checkSize !== "small") {
				//let's get rid of the image-caption-container to proper resizing
				this.$('.upfront-image-caption-container, .upfront-image-container').css({
					width: '100%',
					height: '100%',
					marginTop: 0
				});
			}

			this.$('.uimage').css('min-height', 'auto');
		},

		on_element_resizing: function(attr) {
			if(this.mobileMode) {
				return;
			}

			var starting = this.resizingData.starting,
				data = this.resizingData.data,
				img = this.resizingData.img,
				captionHeight = this.get_preset_property("caption-position") === 'below_image' ? this.$('.wp-caption').outerHeight() : 0,
				// padding = this.property('no_padding') == 1 ? 0 : this.updateBreakpointPadding(breakpointColumnPadding),
				column_padding = Upfront.Settings.LayoutEditor.Grid.column_padding,
				elementWidth = parseInt(attr.width, 10),
				elementHeight = parseInt(attr.height, 10) - captionHeight,
				hPadding = parseInt( this.model.get_breakpoint_property_value('left_padding_num') || column_padding, 10 ) + parseInt( this.model.get_breakpoint_property_value('right_padding_num') || column_padding, 10 ),
				vPadding = parseInt( this.model.get_breakpoint_property_value('top_padding_num') || column_padding, 10 ) + parseInt( this.model.get_breakpoint_property_value('bottom_padding_num') || column_padding, 10 ),
				ratio,
				newSize;

			// data.elementSize = {width: attr.width - (2 * padding), height: attr.height - (2 * padding) - captionHeight};
			data.elementSize = {width: elementWidth < 0 ? 10 : elementWidth, height: elementHeight < 0 ? 10 : elementHeight};

			newSize = this.getElementShapeSize(data.elementSize);
			if ( false !== newSize ) {
				data.elementSize = newSize;
			}

			this.applyElementSize(data.elementSize.width, data.elementSize.height, false); // This won't update element_size property

			if(starting.length){
				return starting.outerHeight(data.elementSize.height - vPadding);
			}

			//Wonderful stuff from here down
			this.$('.uimage').css('height', data.elementSize.height - vPadding);

			var is_locked = this.property('is_locked');

			if(is_locked === false) {
				//Resizing the stretching dimension has priority, the other dimension just alter position
				if(data.stretch && !data.vstretch){
					this.resizingH(img, data, true);
					this.resizingV(img, data);
				} else if(!data.stretch && data.vstretch){
					this.resizingV(img, data, true);
					this.resizingH(img, data);
				} else {
					//Both stretching or not stretching, calculate ratio difference
					ratio = data.size.width / data.size.height - data.elementSize.width / data.elementSize.height;

					//Depending on the difference of ratio, the resizing is made horizontally or vertically
					if(ratio > 0 && data.stretch || ratio < 0 && ! data.stretch){
						this.resizingV(img, data, true);
						this.resizingH(img, data);
					}
					else {
						this.resizingH(img, data, true);
						this.resizingV(img, data);
					}
				}
			} else {
				var vertical_align = this.property('valign'),
					current_position = this.property('position'),
					isDotAlign = this.property('isDotAlign'),
					containerHeight = this.$('.upfront-image-container').height(),
					sizeCheck = this.checkSize(),
					imgPosition = img.position(),
					maskSize = this.getMaskSize(),
					imageView = this.getImageViewport(),
					margin;

				if(sizeCheck === "small") {
					this.$('.upfront-image-caption-container, .upfront-image-container').css({
						width: maskSize.width,
						height: maskSize.height,
						position: 'relative',
						overflow: 'hidden'
					});
				}

				if(typeof imageView.width !== "undefined") {
					if(data.elementSize.width > imageView.width) {
						img.css({left: imgPosition.left + (data.elementSize.width - imageView.width)});
					}
				}

				if(typeof imageView.height !== "undefined") {
					if(data.elementSize.height > imageView.height) {
						img.css({top: imgPosition.top + (data.elementSize.height - imageView.height)});
					}
				}

				if(sizeCheck === "small" && isDotAlign === true) {
					if(vertical_align === "center") {
						if(data.size.height < data.elementSize.height) {
							margin = (data.size.height - data.elementSize.height) / 2;
						} else {
							margin = -(data.elementSize.height - containerHeight) / 2;
						}
					}

					if(vertical_align === "bottom") {
						if(data.size.height < data.elementSize.height) {
							margin = (data.size.height - data.elementSize.height);
						} else {
							margin = -(data.elementSize.height - containerHeight);
						}
					}

					this.$('.upfront-image-caption-container').css({
						'marginTop': -margin
					});

					this.property('marginTop', -margin);
					this.property('position', {top: margin, left: current_position.left});

				}

				if(sizeCheck === "small" && isDotAlign !== true) {
					this.property('marginTop', 0);
				}
			}

			this.updateControls();
			this.setupBySize();
		},

		on_element_resize: function(attr) {
			if(this.mobileMode) {
				return;
			}
			if(!this.resizingData || !this.resizingData.img || !this.resizingData.img.length) {
				return;
			}

			var starting = this.resizingData.starting,
				me = this,
				img = this.resizingData.img,
				imgSize = {width: img.width(), height: img.height()},
				imgPosition = img.position(),
				padding = this.property('no_padding') == 1 ? 0 : this.updateBreakpointPadding(breakpointColumnPadding),
				sizeCheck = this.checkSize(),
				isDotAlign = this.property('isDotAlign');

			if(sizeCheck === "small" && isDotAlign === true) {
				imgPosition = {top: 0, left: 0};
			}

			// Add/remove multiple module class.
			$object = this.$el.find('.upfront-editable_entity:first');
			this.add_multiple_module_class($object);

			if(starting.length) {
				this.elementSize = {
					height: attr.height - (2 * padding),
					width: attr.width - (2 * padding)
				};
				this.model.set_breakpoint_property('element_size', this.elementSize);
				return;

			//} else if (this.property('quick_swap')) {
			} else if (this.isThemeImage()) {
				return;
			}

			// Save resizing, be sure we have the good dimensions
			this.on_element_resizing(attr);

			// Change the sign
			imgPosition.top = -imgPosition.top;
			imgPosition.left = -imgPosition.left;

			this.temporaryProps = {
				size: imgSize,
				position: imgPosition
			};

			var elementSize = this.resizingData.data.elementSize;

			//this.model.set_breakpoint_property('element_size', this.resizingData.data.elementSize);
			this.applyElementSize(elementSize.width, ('default_height' in elementSize ? elementSize.default_height : elementSize.height), true);

			this.cropTimer = setTimeout(function(){
				me.saveTemporaryResizing();
			}, this.cropTimeAfterResize);

			this.resizingData = {};
			this.showCaption();

		},

		getMaskSize: function() {
			var me = this,
				size = this.property('size'),
				checkSize = this.checkSize(),
				elementSize = this.model.get_breakpoint_property_value('element_size', true),
				minWidth = Math.min(size.width, elementSize.width),
				minHeight = Math.min(size.height, elementSize.height),
				newSize;

			newSize = { width: minWidth, height: minHeight};

			return newSize;
		},

		getImageViewport: function() {
			var me = this,
				img = this.resizingData.img,
				imgPosition = img.position(),
				viewPort,
				viewWidth,
				viewHeight;

			if(imgPosition.left < 0) {
				viewWidth = img.width() - Math.abs(imgPosition.left);
			}

			if(imgPosition.top < 0) {
				viewHeight = img.height() - Math.abs(imgPosition.top);
			}

			viewPort = {width: viewWidth, height: viewHeight};

			return viewPort;

		},

		on_uimage_update: function (view) {
			if ( !this.parent_module_view || this.parent_module_view != view ) return;
			// Don't apply element size if on resizing
			if ( !_.isUndefined(this.resizingData) && 'starting' in this.resizingData && this.resizingData.starting ) return;

			this.applyElementSize();
		},

		showCaption: function() {
			this.$('.wp-caption').fadeIn('fast');
		},

		resizingH: function(img, data, size) {
			var elWidth = data.elementSize.width,
				width = size ? data.size.width : img.width(), // The width has been modified if we don't need to set the size
				left = data.position.left,
				css = {},
				align;

			if(data.stretch) {
				if(elWidth < width - left) {
					css.left = -left;
					if(size) {
						css.width = width;
					}
				} else if(width > elWidth && elWidth >= width - left) {
					css.left = elWidth - width;
					if(size) {
						css.width = width;
					}
				} else {
					css.left = 0;
					if(size) {
						css.width = elWidth;
					}
				}
				if(size) {
					css.height = 'auto';
				}
				img.css(css);
				return;
			}

			if(elWidth > width) {
				align = this.property('align');
				if(align === 'left') {
					css.left = 0;
				} else if(align === 'center') {
					css.left = (elWidth - width) / 2;
				} else {
					css.left = 'auto';
					css.right = 0;
				}
				if(size) {
					css.width = width;
					css.height = 'auto';
				}
				img.css(css);
				return;
			}

			css.left = 0;
			if(size) {
				css.width = elWidth;
				css.height = 'auto';
			}
			img.css(css);
		},

		resizingV: function(img, data, size) {
			var elHeight = data.elementSize.height,
				height = size ? data.size.height : img.height(),
				top = data.position.top,
				css = {};

			if(data.vstretch) {
				if(elHeight < height - top) {
					css.top = -top;
					if(size) {
						css.height = height;
					}
				} else if(height > elHeight && elHeight >= height - top){
					css.top = elHeight - height;
					if(size) {
						css.height = height;
					}
				} else{
					css.top = 0;
					if(size) {
						css.height = elHeight;
					}
				}
				if(size) {
					css.width = 'auto';
				}
				img.css(css);
				return;
			}

			if(elHeight > height - top) {
				css.top = -top;
				if(size) {
					css.height = height;
				}
			} else if(height - top >= elHeight && elHeight > height){
				css.top = elHeight - height;
				if(size) {
					css.height = height;
				}
			} else {
				css.top = 0;
				if(size) {
					css.height = elHeight;
				}
			}

			if(size) {
				css.width = 'auto';
			}
			img.css(css);
		},
		/***************************************************************************/
		/*       End Handling element resize events (jQuery resizeable)            */
		/***************************************************************************/

		saveTemporaryResizing: function() {
			var me = this,
				elementSize = me.model.get_breakpoint_property_value('element_size', true),
				crop = {},
				imageId = me.property('image_id'),
				resize = me.temporaryProps.size,
				position = me.temporaryProps.position,
				deferred = $.Deferred(),
				import_deferred = $.Deferred(),
				import_promise = import_deferred.promise()
			;


			crop.top = position.top;
			crop.left = position.left;

			crop.width = Math.min(elementSize.width, resize.width);
			crop.height = Math.min(elementSize.height, resize.height);

			import_promise.done(function(){
				imageId = me.property('image_id');
				Upfront.Views.Editor.ImageEditor.saveImageEdition(
					imageId,
					me.property('rotation'),
					resize,
					crop
				).done(function(results){
					var imageData = results.data.images[imageId];

					if(imageData.error && !me.isThemeImage()){
						Upfront.Views.Editor.notify(l10n.process_error, 'error');
						return;
					}

					me.property('size', resize);
					me.property('position', position);
					me.property('src', imageData.url);
					me.property('srcFull', imageData.urlOriginal, false);
					me.property('stretch', resize.width >= elementSize.width);
					me.property('vstretch', resize.height >= elementSize.height);
					me.property('gifImage', imageData.gif);
					clearTimeout(me.cropTimer);
					me.cropTimer = false;
					deferred.resolve();
				});
			});

			if ( this.isThemeImage() && 'themeExporter' in Upfront ) {
				this.importImage().always(function(){
					import_deferred.resolve();
				});
			}
			else {
				import_deferred.resolve();
			}

			return deferred.promise();
		},

		saveResizing: function() {

			// to fix responsive bug that crops the desktop image on save
			/*if(this.mobileMode) {
				this.property('element_size', this.originalDesktopElementSize);
			}*/

			var me = this,
				post_id = ( typeof _upfront_post_data.post_id !== 'undefined' ) ? _upfront_post_data.post_id : false,
				layout_ids = ( typeof _upfront_post_data.layout !== 'undefined' ) ? _upfront_post_data.layout : '',
				load_dev = ( _upfront_storage_key != _upfront_save_storage_key ? 1 : 0 )
			;

			if(this.cropTimer){
				clearTimeout(this.cropTimer);
				this.cropTimer = false;

				this.saveTemporaryResizing().done(function(){
					var saveData = {
						element: JSON.stringify(Upfront.Util.model_to_json(me.model)),
						post_id: post_id,
						layout_ids: layout_ids,
						load_dev: load_dev,
						action: 'upfront_update_layout_element'
					};
					Upfront.Util.post(saveData);
				});
			}
		},

		resizeImage: function (size) {
			var img = this.$('img'),
				data,
				imgSize,
				imgPosition
			;

			if ( img.length > 0 ) {
				data = {
					position: this.property('position'),
					size: this.property('size'),
					stretch: this.property('stretch'),
					vstretch: this.property('vstretch'),
					elementSize: size
				};

				if(data.stretch && !data.vstretch){
					this.resizingH(img, data, true);
					this.resizingV(img, data);
				} else if(!data.stretch && data.vstretch){
					this.resizingV(img, data, true);
					this.resizingH(img, data);
				} else {
					//Both stretching or not stretching, calculate ratio difference
					ratio = data.size.width / data.size.height - data.elementSize.width / data.elementSize.height;

					//Depending on the difference of ratio, the resizing is made horizontally or vertically
					if(ratio > 0 && data.stretch || ratio < 0 && ! data.stretch){
						this.resizingV(img, data, true);
						this.resizingH(img, data);
					}
					else {
						this.resizingH(img, data, true);
						this.resizingV(img, data);
					}
				}

				imgSize = {width: img.width(), height: img.height()};
				imgPosition = img.position();

				// Change the sign
				imgPosition.top = -imgPosition.top;
				imgPosition.left = -imgPosition.left;

				this.temporaryProps = {
					size: imgSize,
					position: imgPosition
				};
				this.model.set_breakpoint_property('element_size', size);
				this.saveTemporaryResizing();
				return true;
			}
			return false;
		},

		setElementSize: function(ui) {
			var me = this,
				parent = this.parent_module_view.$('.upfront-editable_entity:first'),
				resizer = ui ? $('.upfront-resize') : parent,
				padding = this.property('no_padding') == 1 ? 0 : this.updateBreakpointPadding(breakpointColumnPadding)
			;

			me.elementSize = {
				width: resizer.width() - (2 * padding) + 2,
				height: resizer.height() - (2 * padding)
			};

			if(this.get_preset_property("caption-position") === 'below_image') {
				this.elementSize.height -= parent.find('.wp-caption').outerHeight();
			}

			if(this.property('image_status') === 'starting') {
				this.$('.upfront-object-content').height(me.elementSize.height);
			}

		},
		applyElementSize: function (width, height, set_property) {
			if ( this.parent_module_view ) {
				var me = this,
					parent = this.parent_module_view.$el.find('.upfront-editable_entity:first'),
					resizer = parent,
					captionHeight = this.get_preset_property("caption-position") === 'below_image' ? this.$('.wp-caption').outerHeight() : 0,
					// padding = this.property('no_padding') == 1 ? 0 : this.updateBreakpointPadding(breakpointColumnPadding),
					borderWidth = parseInt(this.$el.find('.upfront-image-caption-container').css('borderWidth') || 0, 10), // || 0 part is needed because parseInt empty sting returns NaN and breaks element height
					column_padding = Upfront.Settings.LayoutEditor.Grid.column_padding,
					hPadding = parseInt( this.model.get_breakpoint_property_value('left_padding_num') || column_padding, 10 ) + parseInt( this.model.get_breakpoint_property_value('right_padding_num') || column_padding, 10 ),
					vPadding = parseInt( this.model.get_breakpoint_property_value('top_padding_num') || column_padding, 10 ) + parseInt( this.model.get_breakpoint_property_value('bottom_padding_num') || column_padding, 10 ),
					// elementSize = {width: resizer.width() - (2 * padding), height: resizer.height() - (2 * padding) - captionHeight}
					elementSize = {width: ( width && !isNaN(width) ? width : resizer.width() ) - hPadding, height: ( height && !isNaN(height) ? height : resizer.height() ) - vPadding - captionHeight - (2 * borderWidth)},
					newSize = this.getElementShapeSize(elementSize)
				;
				if ( false !== newSize ) {
					elementSize = newSize;
				}
				if ( _.isUndefined(set_property) || set_property ) {
					this.model.set_breakpoint_property('row', Upfront.Util.height_to_row(elementSize.height + vPadding), true);
					if ( this.parent_module_view ) {
						this.parent_module_view.model.set_breakpoint_property('row', Upfront.Util.height_to_row(elementSize.height + vPadding), true);
					}
					this.model.set_breakpoint_property('element_size', elementSize);
				}
				this.$el.find('.uimage-resize-hint').html(this.sizehintTpl({
						width: elementSize.width,
						height: elementSize.height,
						l10n: l10n.template
					})
				);
				// Let's override the min-height set to element
				this.$el.find('> .upfront-object').css('min-height', (elementSize.height + vPadding) + 'px');
				this.$el.closest('.upfront-module').css('min-height', (elementSize.height + vPadding) + 'px');
			}
		},

		openImageSelector: function(e){
			var me = this;
			if (e && e.preventDefault) e.preventDefault();

			Upfront.Views.Editor.ImageSelector.open({
				multiple_sizes: false
			}).done(function(images){
				var sizes = {};
				_.each(images, function(image, id){
					sizes = image;
					me.imageId = id;
				});

				var	imageInfo = {
						src: sizes.medium ? sizes.medium[0] : sizes.full[0],
						srcFull: sizes.full[0],
						srcOriginal: sizes.full[0],
						fullSize: {width: sizes.full[1], height: sizes.full[2]},
						size: sizes.medium ? {width: sizes.medium[1], height: sizes.medium[2]} : {width: sizes.full[1], height: sizes.full[2]},
						position: false,
						rotation: 0,
						id: me.imageId
					}
				;
				$('<img>')
					.load(function(){
						Upfront.Views.Editor.ImageSelector.close();
						me.openEditor(true, imageInfo);
					})
					.on("error", function () {
						Upfront.Views.Editor.ImageSelector.close();
						Upfront.Views.Editor.notify(l10n.process_error, 'error');
					})
					.attr('src', imageInfo.srcFull)
				;
			});
		},

		handleEditorResult: function(result){
			this.property('image_status', 'ok', true);
			this.property('has_settings', 1);
			this.property('src', result.src, true);
			this.property('srcFull', result.srcFull, true);
			this.property('srcOriginal', result.srcOriginal, true);
			this.property('size', result.imageSize, true);
			this.property('position', result.imageOffset, true);
			var marginTop = result.mode === 'horizontal' || result.mode === 'small' ? result.imageOffset.top * -1 : 0;
			this.property('marginTop', marginTop, true);
			this.property('rotation', result.rotation, true);
			this.property('fullSize', result.fullSize, true);

			this.property('element_size', result.maskSize, true);

			this.property('align', result.align, true);
			this.property('valign', result.valign, true);
			this.property('isDotAlign', result.isDotAlign, true);
			this.property('stretch', result.stretch, true);
			this.property('vstretch', result.vstretch, true);
			this.property('quick_swap', false, true);
			if(result.imageId) {
				this.property('image_id', result.imageId, true);
			}

			this.property('gifImage', result.gif);

			this.temporaryProps = false;
			this.render();

			if(result.elementSize){
				this.set_element_size(result.elementSize.columns, result.elementSize.rows, 'all', true);
			}
		},

		editRequest: function () {
			var me = this;

			if(this.property('image_status') === 'ok' && this.property('image_id')) {
				if (this.isThemeImage() && 'themeExporter' in Upfront) {
					this.importImage().always(function(){
						me.openEditor();
					});
				}
				else {
					this.openEditor();
				}
				return;
			}

			Upfront.Views.Editor.notify(l10n.external_nag, 'error');
		},

		lockImage: function () {
			var me = this,
				is_locked = this.property('is_locked'),
				sizeCheck = this.checkSize();

			if(typeof is_locked !== "undefined" && is_locked === true) {
				//Update icon
				this.controls.$el.find('.upfront-icon-region-lock-locked')
					.addClass('upfront-icon-region-lock-unlocked')
					.removeClass('upfront-icon-region-lock-locked');

				this.property('is_locked', false);

				if(sizeCheck === "small") {
					this.$('.upfront-image-caption-container, .upfront-image-container').css({
						width: '100%',
						height: '100%',
						marginTop: 0
					});

					this.fitImage();

					this.cropTimer = setTimeout(function(){
						me.saveTemporaryResizing();
					}, this.cropTimeAfterResize);
				}
			} else {
				//Update icon
				this.controls.$el.find('.upfront-icon-region-lock-unlocked')
					.addClass('upfront-icon-region-lock-locked')
					.removeClass('upfront-icon-region-lock-unlocked');

				this.property('is_locked', true);
			}
		},

		fitImage: function() {
			var maskSize = this.model.get_breakpoint_property_value('element_size', true),
				position = this.property('position'),
				size = this.property('size');

			var newSize = Upfront.Views.Editor.ImageEditor.getResizeImageDimensions(size, {width: maskSize.width, height: maskSize.height}, 'outer', 0);

			this.property('size', {width: newSize.width, height: newSize.height});

			this.temporaryProps = {
				size: {width: newSize.width, height: newSize.height},
				position: position
			};

			this.property('vstretch', true);

			this.$('.upfront-image-container img').css({
				width: newSize.width,
				height: newSize.height,
				left: '0px',
				top: '0px'
			});

			this.$('.upfront-image-wrapper').css({
				height: maskSize.height
			});
		},

		checkSize: function() {
			var maskSize = this.model.get_breakpoint_property_value('element_size', true),
				size = this.property('size');

			if(size.width >= maskSize.width && size.height >= maskSize.height) {
				return 'big';
			}

			return 'small';
		},

		getElementColumns: function(){
			var module = this.$el.closest('.upfront-module'),
				classes,
				found = false
			;

			if(!module.length) {
				return -1;
			}

			classes = module.attr('class').split(' ');

			_.each(classes, function(c){
				if(c.match(/^c\d+$/)) {
					found = c.replace('c', '');
				}
			});
			return found || -1;
		},

		openEditor: function(newImage, imageInfo){
			if(Upfront.Application.responsiveMode !== 'desktop') {
				return Upfront.Views.Editor.notify(l10n.desktop_nag, 'error');
			}

			var me = this,
				options = {
					setImageSize: newImage,
					saveOnClose: newImage,
					editElement: this
				}
			;

			this.setElementSize();
			this.setImageInfo();

			if(imageInfo) {
				_.extend(options, this.imageInfo, imageInfo);
			} else {
				_.extend(options, this.imageInfo);
			}

			if(this.cropTimer){
				this.stoppedTimer = true;
				clearTimeout(this.cropTimer);
				this.cropTimer = false;
			}

			options.element_id = me.model.get_property_value_by_name('element_id');

			options.element_cols = me.get_element_columns();

			//Remove controls when open image editor
			if(typeof this.controls !== "undefined") {
				this.controls.remove();
			}

			Upfront.Views.Editor.ImageEditor.open(options)
				.done(function(result){
					me.handleEditorResult(result);
					this.stoppedTimer = false;

					// Update controls after image editor
					me.updateControls();
				})
				.fail(function(data){
					if(data && data.reason === 'changeImage') {
						me.openImageSelector();
					} else if(me.stoppedTimer) {
						me.saveTemporaryResizing();
						me.stoppedTimer = false;
					}

					// Update controls after image editor
					me.updateControls();
				})
			;
		},

		handleLinkClick: function(event){
			var lightboxName;


			if (this.link.get('type') !== 'lightbox') {
				return;
			}

			event.preventDefault();

			lightboxName = this.link.get('url').substring(1);
			Upfront.Application.LayoutEditor.openLightboxRegion(lightboxName);
		},

		importImage: function () {
			var me = this,
				image_id = this.property('image_id'),
				src = this.property('srcFull'),
				opts = {
					action: 'upfront_import_image',
					images: [src]
				},
				deferred = $.Deferred()
			;
			Upfront.Util.post(opts).done(function(response){
				var images = response.data.images;
				if ( parseInt(response.data.error, 10) !== 0 ) {
					deferred.reject();
					return;
				}
				_.each(images, function (image) {
					var status = image.status;
					if ( status === 'import_success' || status === 'exists') {
						me.property('image_id', image.id);
						me.property('srcFull', image.src);
						me.property('srcOriginal', image.src);
					}
					else {
						deferred.reject();
						return;
					}
					deferred.resolve(me.property('image_id'));
				});
			});
			return deferred.promise();
		},

		cleanup: function(){
			// The default images on a new theme installation do not have controlls created, so putting a check here.
			if(this.controls)
				this.controls.remove();
			// if(this.bodyEventHandlers){
			//  _.each(this.bodyEventHandlers, function(f, ev){
			//    $('body').off(ev, f);
			//  });
			// }
		},

		property: function(name, value, silent) {
			if(typeof value !== 'undefined'){
				if(typeof silent === 'undefined') {
					silent = true;
				}
				return this.model.set_property(name, value, silent);
			}
			return this.model.get_property_value_by_name(name);
		},

		getControlItems: function(){
			var me = this,
				panel = new Upfront.Views.Editor.InlinePanels.ControlPanel(),
				moreOptions = new Upfront.Views.Editor.InlinePanels.SubControl(),
				is_locked = this.property('is_locked'),
				controls = [],
				lock_icon,
				lock_tooltip
			;
			if ( !this.mobileMode ) {
				if(typeof is_locked !== "undefined" && is_locked === true) {
					lock_icon = 'lock-locked';
					lock_tooltip = l10n.ctrl.lock_image;
				} else {
					lock_icon = 'lock-unlocked';
					lock_tooltip = l10n.ctrl.unlock_image;
				}

				moreOptions.icon = 'more';
				moreOptions.tooltip = Upfront.Settings.l10n.global.views.more_options;

				moreOptions.sub_items = {};
				moreOptions.sub_items['swap'] = this.createControl('swap', l10n.btn.swap_image, 'openImageSelector');
				moreOptions.sub_items['crop'] = this.createControl('crop', l10n.ctrl.edit_image, 'editRequest');
				moreOptions.sub_items['link'] = this.createLinkControl();
				moreOptions.sub_items['lock'] = this.createControl(lock_icon, lock_tooltip, 'lockImage');

				controls.push(moreOptions);
			}

			controls.push(this.createPaddingControl());
			controls.push(this.createControl('settings', Upfront.Settings.l10n.global.views.settings, 'on_settings_click'));

			return _(controls);
		}
	});

	Upfront.Application.LayoutEditor.add_object('Uimage', {
		'Model': UimageModel,
		'View': UimageView,
		'Element': ImageElement,
		'Settings': ImageSettings,
		'ContextMenu': ImageContextMenu,
		cssSelectors: {
			'.upfront-image-wrapper': {label: l10n.css.image_label, info: l10n.css.image_info},
			'.wp-caption': {label: l10n.css.caption_label, info: l10n.css.caption_info},
			'.upfront-image-container': {label: l10n.css.wrapper_label, info: l10n.css.wrapper_info}
		},
		cssSelectorsId: Upfront.data.uimage.defaults.type
	});

	Upfront.Views.Editor.ImageEditor = new ImageEditor();
	Upfront.Views.Editor.ImageSelector = new ImageSelector();
	Upfront.Models.UimageModel = UimageModel;
	Upfront.Views.UimageView = UimageView;

});
})(jQuery);
//# sourceURL=uimage.js
