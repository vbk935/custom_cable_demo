(function ($, undefined) {

define([
    'scripts/upfront/upfront-media/insert-options-item-control',
	'scripts/perfect-scrollbar/perfect-scrollbar'
], function(InsertOptions, perfectScrollbar) {

	var MEDIA_SIZES = {
		FULL: "full",
		to_size: function (size) {
			return size.width + 'x' + size.height;
		}
	};

	var l10n = Upfront.Settings.l10n.media;

    var INSERT_OPTIONS = InsertOptions.INSERT_OPTIONS;

// ----- Models -----

	var MediaItem_Model = Backbone.Model.extend({
		defaults: {
			thumbnail: "<span class='upfront-image-upload-placeholder'></span>",
            insert_option: "image_insert"
		}
	});

	var MediaCollection_Model = Backbone.Collection.extend({
		defaults:{
			thumbnail: '',
			title: ''
		}
	});
	var MediaCollection_Selection = Backbone.Collection.extend({
		model: MediaItem_Model,
		initialize: function () {
			this.listenTo(Upfront.Events, "media_manager:media:labels_loaded", this.global_labels_loaded);
		},
		get_shared_labels: function () {
			var known_labels = ActiveFilters.get("label"),
				selected_labels = [],
				shared_labels = [],
				tmp_shared = {}
			;
			this.each(function (item) {
				var labels = item.get("labels") || [];
				labels = _.map(labels, function(each){ return parseInt(each, 10); });
				tmp_shared[item.get("ID")] = labels;
			});
			selected_labels = _.intersection.apply(this, _(tmp_shared).values());
			known_labels.each(function (label) {
				if (
					selected_labels.indexOf(label.get("value")) >= 0
					||
					selected_labels.indexOf(parseInt(label.get("value"), 10)) >= 0
				) shared_labels.push(label);
			});
			return shared_labels;
		},
		get_additional_sizes: function () {
			if (!ActiveFilters.multiple_sizes) return false; // Do not use multiple sizes if we're told not to
			var all_item_sizes = this.invoke("get", "additional_sizes"),
				item_sizes = []
			;
			_(all_item_sizes).each(function (item, idx) {
				var tmp_sizes = [];
				if (!idx) item_sizes = _(item).map(function (size) { return MEDIA_SIZES.to_size(size);});
				_(item).each(function (size) {
					tmp_sizes.push(MEDIA_SIZES.to_size(size));
				});
				item_sizes = _.intersection(item_sizes, tmp_sizes);
			});
			item_sizes.push(MEDIA_SIZES.FULL);
			return item_sizes;
		},
		is_used_label: function (label) {
			return (_(this.get_shared_labels()).invoke("get", "value").indexOf(label.get("value")) >= 0);
		},
		update_label_state: function (label) {
			return this.is_used_label(label)
				? this.disassociate_label(label)
				: this.associate_label(label)
			;
		},
		associate_label: function (label) {
			this._update_label('', label);
		},
		disassociate_label: function (label) {
			this._update_label('dis', label);
		},
		_update_label: function (pfx, label) {
			pfx = pfx || '';
			var me = this,
				idx = label.get("value"),
				data = {
					action: "upfront-media-" + pfx + "associate_label",
					term: idx,
					post_ids: this.invoke("get", "ID")
				}
			;
			Upfront.Util.post(data)
				.success(function (response) {
					me.each(function (model) {
						var labels = response.data[model.get("ID")];
						if (labels) model.set({labels: labels}, {silent: true});
					});
					me.trigger("change");
				})
			;
		},
		add_new_label: function (label) {
			var me = this,
				data = {
					"action": "upfront-media-add_label",
					"term": label,
					"post_ids": this.invoke("get", "ID")
				}
			;
			return Upfront.Util.post(data)
				.success(function (response) {
					me.each(function (model) {
						var labels = response.data[model.get("ID")];
						if (labels) model.set({labels: labels}, {silent: true});
					});
					Upfront.Events.trigger("media_manager:media:labels_updated");
					me.trigger("change");
				})
			;
		},
		delete_media_items: function () {
			var me = this,
				data = {
					"action": "upfront-media-remove_item",
					"post_ids": this.invoke("get", "ID")
				}
			;
			Upfront.Util.post(data)
				.success(function (response) {
					me.reset([]);
					Upfront.Events.trigger("media_manager:media:list", ActiveFilters);
				})
			;
		},
		// Same as delete_media_items except for theme/UI images.
		delete_theme_items: function () {
			var me = this,
				data = {
					"action": "upfront-media-remove_theme_item",
					// Theme images use file names rather than post IDs.
					"post_ids": this.invoke("get", "post_title")
				}
			;
			Upfront.Util.post(data)
				.success(function (response) {
					me.reset([]);
					Upfront.Events.trigger("media_manager:media:list", ActiveFilters);
				})
			;
		},
		global_labels_loaded: function () {
			this.trigger("change");
		}
	});

	var MediaFilter_Item = Backbone.Model.extend({
	});

	var MediaFilter_Collection = Backbone.Collection.extend({
		model: MediaFilter_Item
	});

	var ActiveMediaFilter_Collection = Backbone.Model.extend({
		CONST: {
			CUTOFF_SIZE: 8,
			CUTOFF_BIT: 3
		},
		labels_cache: false,
		default_media_types: ['images', 'videos', 'audios', 'other'],
		allowed_media_types: [],
		image_sizes: true,
		showing_titles: true,
		current_keys: [],
		current_models: [],
		current_page: 1,
		max_pages: 1,
		max_items: 1,
		media_limit: 60,
		current_filter_control: 0,
		initialize: function () {
			this.to_defaults();
			this.listenTo(Upfront.Events, "media_manager:media:filters_updated", this.update_active_filters);
			this.listenTo(Upfront.Events, "media_manager:media:labels_updated", this.reload_labels);
			this.listenTo(Upfront.Events, "media_manager:media:toggle_titles", this.toggle_titles);
		},
		to_defaults: function () {
			var types = new MediaFilter_Collection([]),
				has_all = (this.allowed_media_types.indexOf('other') >= 0)
			;
			if (!this.allowed_media_types.length) this.allowed_media_types = this.default_media_types;

			if (this.allowed_media_types.indexOf('images') >= 0) types.add(new MediaFilter_Item({filter: l10n.filter.images, value: 'images', state: !has_all}), {silent: true});
			if (this.allowed_media_types.indexOf('videos') >= 0) types.add(new MediaFilter_Item({filter: l10n.filter.videos, value: 'videos', state: !has_all}), {silent: true});
			if (this.allowed_media_types.indexOf('audios') >= 0) types.add(new MediaFilter_Item({filter: l10n.filter.audios, value: 'audios', state: !has_all}), {silent: true});
			if (this.allowed_media_types.indexOf('other') >= 0) types.add(new MediaFilter_Item({filter: l10n.filter.all, value: 'other', state: has_all}), {silent: true});

			this.set("type", types, {silent: true});

			this.set("recent", new MediaFilter_Collection([
				new MediaFilter_Item({filter: "5", value: 5, state: false}),
				new MediaFilter_Item({filter: "10", value: 10, state: false}),
				new MediaFilter_Item({filter: "20", value: 20, state: false}),
				new MediaFilter_Item({filter: "40", value: 40, state: false}),
				new MediaFilter_Item({filter: "100", value: 100, state: false})
			]), {silent: true});

			this.set("order", new MediaFilter_Collection([
				new MediaFilter_Item({filter: l10n.filter.newest, value: 'date_desc', state: true}),
				new MediaFilter_Item({filter: l10n.filter.oldest, value: 'date_asc', state: false}),
				new MediaFilter_Item({filter: l10n.filter.a_z, value: 'title_asc', state: false}),
				new MediaFilter_Item({filter: l10n.filter.z_a, value: 'title_desc', state: false})
			]), {silent: true});

			this.set({"search": new MediaFilter_Collection([])}, {silent: true});

			this.themeImages =false;
			this.current_page = 1;
			this.current_filter_control = 0;

			this.set_labels_to_defaults();
		},
		set_max_pages: function (max) {
			this.max_pages = !_.isUndefined(max) ? max : 1;
		},
		set_max_items: function (max) {
			this.max_items = !_.isUndefined(max) ? max : 1;
		},
		prev_page: function () {
			if (this.current_page > 1) return this.set_page(this.current_page-1);
		},
		next_page: function () {
			if (this.current_page < this.max_pages) return this.set_page(this.current_page+1);
		},
		set_page: function (page) {
			if (!page) return false;
			if (page >= 1 && page < this.max_pages) {
				if (page == this.current_page) return false; // Already here.
				this.current_page = page;
				return true;
			} else return false;
		},
		toggle_titles: function () {
			this.showing_titles = !this.showing_titles;
		},
		set_labels_to_defaults: function () {
			if (this.labels_cache) {
				var arr = [];
				_(this.labels_cache).each(function (item) {
					arr.push(new MediaFilter_Item({filter: item.name, value: item.term_id, state: false}));
				});
				this.set("label", new MediaFilter_Collection(arr), {silent: true});
				Upfront.Events.trigger("media_manager:media:labels_loaded");
			} else this.reload_labels();
		},
		reload_labels: function () {
			var me = this;
			Upfront.Util.post({action: "upfront-media-get_labels"})
				.success(function (response) {
					var arr = [];
					if (response.data) {
						me.labels_cache = response.data;
						me.set_labels_to_defaults();
					}
				})
			;
		},
		update_active_filters: function (filter, data) {
			if (!filter || !this.get(filter)) {
				this.to_defaults();
				Upfront.Events.trigger("media_manager:media:filters_reset");
			} else {
				var collection = data && data.get ? data.get(filter).toArray() : data,
					me = this.get(filter)
				;
				_(collection).each(function (item) {
					if (item.get("state")) {
						var has = me.where({filter: item.get("filter")});
						if (has.length) {
							has[0].set({state: item.get("state")}, {silent: true});
						}
					}
				});
				// Reset page to 1
				this.set_page(1);
			}
			Upfront.Events.trigger("media_manager:media:list", this);
		},
		to_request_json: function () {
			var data = {},
				me = this
			;
			_(this.attributes).each(function (collection, idx) {
				var active = me.get(idx).where({state:true});
				data[idx] = _(active).invoke("get", "value");
			});
			data.page = this.current_page;
			return data;
		},
		to_list: function () {
			var data = {},
				me = this
			;
			_(this.attributes).each(function (collection, idx) {
				var active = me.get(idx).where({state:true}),
					active_non_defaults = []
				;
				active_non_defaults = _(active).filter(function (filter) {
					var value = filter.get("value");
					return "other" !== value && "date_desc" !== value;
				});
				data[idx] = _(active_non_defaults).invoke("get", "filter");
			});
			return data;
		},
		has_upload: function () {
			if ( Upfront.Settings.Application.PERMS.UPLOAD ) {
				if (!this.themeImages) return true; // Allow when not looking into theme images
				return true === Upfront.plugins.isRequiredByPlugin('media filter upload');
			} else {
				return false; // disabling upload when user role has no permission
			}
		}
	});

	var ActiveFilters = new ActiveMediaFilter_Collection();

// ----- Views -----


	var MediaManager_Controls_View = Backbone.View.extend({
		className: "upfront-media-controls",
		is_search_active: false,
		initialize: function (args) {
			this.listenTo(Upfront.Events, "media:item:selection_changed", this.switch_controls);
			this.listenTo(Upfront.Events, "media:search:requested", this.switch_to_search);
			this.listenTo(Upfront.Events, "media_manager:load:done", this.render_filters);
            this.options = args.options;
		},
		render: function () {
			if (!this.options.show_insert) {
				this.$el.addClass('upfront-media-controls-no-insert');
			}
			this.render_filters();
		},
		render_filters: function () {
			if (this.search) this.search.remove();
			this.search = new MediaManager_SearchControl();
			if (this.control) this.control.remove();
			this.control = this.is_search_active ? new MediaManager_SearchFiltersControl() : new MediaManager_FiltersControl();
			this.search.render();
			this.control.render();
			this.$el.empty().append(this.search.$el).append(this.control.$el);
			if ( this.is_search_active ) this.$el.removeClass('upfront-media-controls-search-selected').addClass('upfront-media-controls-search');
			else this.$el.removeClass('upfront-media-controls-search');
		},
		render_media: function (selected) {
			var item_control = new MediaManager_ItemControl({model: new MediaCollection_Selection(selected), options: this.options});
			item_control.render();
			this.$el.empty();
			if (this.is_search_active) {
				this.control = new MediaManager_SearchFiltersControl();
				this.control.render();
				this.$el.append(this.control.$el);
				this.$el.removeClass('upfront-media-controls-search').addClass('upfront-media-controls-search-selected');
			}
			else
				this.$el.removeClass('upfront-media-controls-search-selected');
			this.$el.append(item_control.$el);
		},
		switch_controls: function (media_collection) {
			var positive = media_collection.where({selected: true});
			if (positive.length) this.render_media(positive);
			else this.render_filters();
		},
		switch_to_search: function (search) {
			this.is_search_active = search && search.get("state");
			//this.render_filters(); // This will be rendered later in another event to prevent double rendering in a row
		},
		remove: function() {
			if (this.control) this.control.remove();
			Backbone.View.prototype.remove.call(this);
		}
	});

	var MediaManager_AuxControls_View = Backbone.View.extend({
		className: "upfront-media-aux_controls",
		initialize: function () {
			this.listenTo(Upfront.Events, "media:item:selection_changed", this.switch_controls);
		},
		render: function () {
			//this.render_selection();
		},
		/*render_selection: function () {
			var selection_control = new MediaManager_SelectionControl({model: this.model});
			selection_control.render();
			this.$el.empty().append(selection_control.$el);
			this.$el.removeClass('upfront-media-aux_controls-has-select');
		},*/
		render_delete: function (selected) {
			var delete_control = new MediaManager_DeleteControl({model: new MediaCollection_Selection(selected)});
			delete_control.render();
			//this.render_selection();
			this.$el.empty().append(delete_control.$el);
			this.$el.addClass('upfront-media-aux_controls-has-select');
		},
		switch_controls: function (media_collection) {
			var positive = media_collection && media_collection.where ? media_collection.where({selected: true}) : [];
			if (positive.length) this.render_delete(positive);
			//else this.render_selection();
		}
	});

		var MediaManager_SelectionControl = Backbone.View.extend({
			className: "select_control_container",
			events: {
				"click a.none": "select_none",
				"click a.all": "select_all"
			},
			initialize: function () {
				this.display_values = [
					{label: '20', value: 20},
					{label: '40', value: 40},
					{label: '60', value: 60},
					{label: '80', value: 80},
					{label: '100', value: 100}
				];
			},
			render: function () {
				var me = this;
				me.$el.empty().append(l10n.select + ' <a href="#all" class="all">' + l10n.all + '</a>&nbsp;|&nbsp;<a href="#none" class="none">' + l10n.none + '</a>');

				me.select_display_field = new Upfront.Views.Editor.Field.Select({
					label: l10n.display+":",
					className: "upfront-field-wrap upfront-field-wrap-select upfront-filter_display_control",
					name: "display-selection",
					width: '70px',
					values: me.display_values,
					multiple: false,
					default_value: ActiveFilters.media_limit,
					change: function(){
						me.select_display(this.get_value());
					}
				});
				me.select_display_field.render();
				me.$el.append(me.select_display_field.$el);
			},
			select_display: function (limit) {
				ActiveFilters.media_limit = limit;
				Upfront.Events.trigger("media_manager:media:list", ActiveFilters);
			},
			select_none: function (e) {
				e.preventDefault();
				e.stopPropagation();

				var positive = this.model.where({selected: true});
				if ( positive.length ) {
					this.model.each(function (item) {
						item.set({selected: false}, {silent: true});
					});
					this.model.trigger("change");
					Upfront.Events.trigger("media:item:selection_changed", this.model);

					// run again to apply the new list
					var selected_model = new MediaCollection_Selection(ActiveFilters.current_models);
					Upfront.Events.trigger("media:item:selection_changed", selected_model);
				}
			},
			select_all: function (e) {
				e.preventDefault();
				e.stopPropagation();
				var all = [];
				this.model.each(function (item) {
					item.set({selected: true}, {silent: true});
					all.push(item);
				});
				this.model.trigger("change");
				Upfront.Events.trigger("media:item:selection_changed", this.model);

				// run again to apply the new list
				var selected_model = new MediaCollection_Selection(ActiveFilters.current_models);
				Upfront.Events.trigger("media:item:selection_changed", selected_model);
			}
		});
		var MediaManager_DeleteControl = Backbone.View.extend({
			className: "delete_control_container upfront-icon upfront-icon-trash",
			events: {
				click: "delete_selection"
			},
			render: function () {
				this.$el.empty().append('<a href="#delete">' + l10n.del_command + '</a>');
			},
			delete_selection: function (e) {
				e.preventDefault();
				e.stopPropagation();

				if ( confirm(l10n.confirm_delete_items) ) {
					var show_nag = false;
					this.model.each(function (item) {
						if (item.get("parent")) show_nag = true;
					});
					if (!show_nag || (show_nag && confirm(l10n.item_in_use_nag))) {
						ActiveFilters.current_keys = [];
						ActiveFilters.current_models = [];
						// If theme image, delete via proper deletion method.
						if (ActiveFilters.themeImages) {
							return this.model.delete_theme_items();
						}
						// Otherwise delete media item.
						return this.model.delete_media_items();
					}
				}
			}
		});

		var MediaManager_ItemControl = Backbone.View.extend({
			className: "upfront-item-control",
            options: {
                insert_options: false,
                hide_sizes: false
            },
			templates: {
				caption: _.template('<label class="upfront-field-label upfront-field-label-block">{{title}}</label>'),
				shared_label: _.template('<a href="#remove" class="upfront-icon upfront-icon-media-label-delete" data-idx="{{value}}">{{filter}}</a>'),
				additional_size: _.template('<option value="{{size}}">{{size}}</option>')
			},
			events: {
				//"change .change_title :text": "change_title",
				"click .existing_labels a": "drop_label"//,
				//"change .additional_sizes select": "select_size"
			},
			initialize: function ( opts ) {
				this.listenTo(this.model, "change", this.render);
                this.options = _.extend( this.options, opts.options );
			},
			render: function () {
				var self = this,
					sections = _([
						'size_hints',
						'change_title',
						'change_alt',
						'copy_url',
						//'existing_labels',
						'add_labels',
						'insert_options',
						'additional_sizes'
					]),
					renderers = _([
						'render_size_hint',
						'render_title',
						'render_alt',
						'render_copy_url',
						//'render_shared_labels',
						'render_labels_adding',
						'render_insert_options',
						'render_additional_sizes'
					]);

				// remove prev sections
				this.$el.empty();

				// if insert_options is false remove insert options section
				if( !this.options.insert_options ){
					sections = _( sections.reject(function(section){
						return section === "insert_options";
					}) );

					renderers =  _( renderers.reject(function(renderer){
						return renderer === "render_insert_options";
					}) );
				}

				if (_.indexOf(this.options.media_type, 'videos') !== -1) {
					sections = _( sections.reject(function(section){
						return section === "additional_sizes";
					}) );

					renderers =  _( renderers.reject(function(renderer){
						return renderer === "render_additional_sizes";
					}) );
				}

				// add sections
				sections.each(function(section){
					self.$el.append( '<div class="' + section +  '" />' );
				});

				// render sections
				renderers.each(function(renderer){
					self[renderer]();
				});

			},
			_find_gcd: function (a, b) {
				return (b == 0) ? a : this._find_gcd(b, a%b);
			},
			render_size_hint: function() {
				var me = this,
					$hub = this.$el.find('.size_hints'),
					sizeWidth,
					sizeHeight;

				$hub.empty();

				if(this.model.length === 1) {
					var image = this.model.at(0).get('image'),
						$container = $('<div class="upfront-size-hints upfront-field-wrap upfront-field-wrap-text"><label class="upfront-field-label upfront-field-label-block">'+ l10n.information +'</label></div>')
					;
					if ( image !== undefined ) {
						var iwidth = parseInt((image || {}).width, 10),
							iheight = parseInt((image || {}).height, 10),
							gcd = me._find_gcd(iwidth, iheight)
						;
						// Only render size hint if we're actually able to
						if (iwidth && iheight) {
							$container.append(
								'<span class="upfront-size-hint-ratio">' +
									(iwidth/gcd) +
								':' +
									(iheight/gcd) +
								'</span>'
							);
							$container.append(
								'<span class="upfront-size-hint-width">' +
									l10n.width_label +
								': <span>' +
									image.width + l10n.px_label +
								'</span></span>'
							);
							$container.append(
								'<span class="upfront-size-hint-height">' +
									l10n.height_label +
								': <span>' +
									image.height + l10n.px_label +
								'</span></span>'
							);
							$hub.append($container);
						}
					}
				}
			},
			render_alt: function () {
				var	me = this,
					$hub = this.$el.find(".change_alt"),
					image = this.model.at(0).get('image');


				$hub.empty();
				if (this.model.length === 1) {
					this.alt_field = new Upfront.Views.Editor.Field.Text({
						model: this.model.at(0),
						label: l10n.media_alt,
						name: 'alt',
						change: function(){
							me.change_alt();
						}
					});
					this.alt_field.render();
					$hub.append(this.alt_field.$el);
				}
			},
			render_title: function () {
				var	me = this,
					$hub = this.$el.find(".change_title")
				;
				$hub.empty();
				if (this.model.length > 1) {
					var files = '<span class="selected_files">' + l10n.files.replace(/%d/, this.model.length) + '</span>',
						shared_labels = this.model.get_shared_labels()
					;
					$hub.append('<label class="upfront-field-label upfront-field-label-block">'+ l10n.information +'</label>');
					$hub.append('<span class="selected_length">' + ( shared_labels.length > 0 ? l10n.selected.replace(/%s/, files) : l10n.selected_no_label.replace(/%s/, files) ) + '</span>');
				} else {
					this.title_field = new Upfront.Views.Editor.Field.Text({
						model: this.model.at(0),
						label: l10n.media_title,
						compact: true,
						name: 'post_title',
						change: function(){
							me.change_title();
						}
					});
					this.title_field.render();
					$hub.append(this.title_field.$el);
				}
			},
			render_copy_url: function () {
				var	$hub = this.$el.find(".copy_url");
				if (this.model.length > 1) return false;
				
				this.copy_url = new Upfront.Views.Editor.Field.Button({
					model: this.model.at(0),
					label: l10n.copy_url,
					compact: true,
					name: "document_url",
					on_click: function () {
						Upfront.Util.add_to_clipboard(this.model.get('document_url'));
					}
				});
				this.copy_url.render();
				$hub.append(this.copy_url.$el);
			},
			render_labels_adding: function () {
				var me = this,
					$hub = this.$el.find(".add_labels"),
					container = new MediaManager_ItemControl_LabelsContainer({model: this.model})
				;
				$hub.empty().append(this.templates.caption({title: l10n.media_labels}));
				container.render();
				$hub.append(container.$el);
				this.$el.on("click", function (e) {
					e.stopPropagation();
					container.trigger("filters:selection:click");
				});
			},
			render_shared_labels: function () {
				var me = this,
					$hub = this.$el.find(".existing_labels"),
					shared_labels = this.model.get_shared_labels(),
					title = (shared_labels.length > 1 ? l10n.current_labels : '')
				;
				$hub.empty()
					.append(this.templates.caption({title: title}))
				;
				_(shared_labels).each(function (label) {
					$hub.append(me.templates.shared_label(label.toJSON()));
				});
			},
			render_additional_sizes: function () {
				var me = this,
					$hub = this.$el.find(".additional_sizes"),
					additional_sizes = this.model.get_additional_sizes(),
					title = l10n.additional_sizes,
					sizes = []
					;
				$hub.empty();

				if( ( this.options.insert_options &&  this.model.at(0).get("insert_option") === INSERT_OPTIONS.wp_insert) || ( !this.options.hide_sizes  && !this.options.insert_options )    ) {
					if (!additional_sizes.length) return false;
					_(additional_sizes).each(function (size) {
						if (size === MEDIA_SIZES.FULL) return;
						sizes.push({ label: size, value: size });
					});
					this.size_field = new Upfront.Views.Editor.Field.Radios({
						model: this.model.at(0),
						name: 'selected_size',
						width: '100%',
						values: sizes,
						default_value: additional_sizes[0],
						change: function(){
							me.select_size();
						}
					});
					this.size_field.render();
					$hub.append(this.size_field.$el);
					this.size_field.$el.on("click", function (e) {
						e.stopPropagation();
					});
				}

			},
			select_size: function (e) {
				//e.stopPropagation();
				var size = this.size_field.get_value() || MEDIA_SIZES.FULL;
				this.model.each(function (model) {
					model.set({selected_size: size}, {silent: true});
				});
			},
			change_title: function (e) {
				//e.stopPropagation();
				var model = this.model.at(0);
				model.set({post_title: this.title_field.get_value()});
				var me = this,
					data = {
						action: "upfront-media-update_media_item",
						data: model.toJSON()
					}
				;
				Upfront.Util.post(data)
					.done(function () {
						model.trigger("change");
					})
				;
				model.trigger("appearance:update");
			},
			change_alt: function (e) {
				//e.stopPropagation();
				var model = this.model.at(0);
				model.set({alt: this.alt_field.get_value()});
				var me = this,
					data = {
						action: "upfront-media-update_media_item",
						data: model.toJSON()
					}
				;
				Upfront.Util.post(data)
					.done(function () {
						model.trigger("change");
					})
				;
				model.trigger("appearance:update");
			},
			drop_label: function (e) {
				e.preventDefault();
				e.stopPropagation();
				var $label = $(e.target),
					idx = $label.attr("data-idx"),
					shared = this.model.get_shared_labels(),
					label_idx = _(shared).invoke("get", "value").indexOf(idx),
					label = label_idx >= 0 && shared[label_idx] ? shared[label_idx] : false
				;
				if (label) this.model.update_label_state(label);
			},
            render_insert_options: function(){
                var $this_section = this.$(".insert_options"),
                    view = new InsertOptions.Options_Control( {model: this.model} );
                view.render();

                $this_section.html(view.el);
            }
		});

			var MediaManager_ItemControl_LabelsContainer = Backbone.View.extend({
				className: "upfront-additive_multiselection",
				selection: '',
				events: {
					"click .labels_filter": "stop_prop",
					"keyup .labels_filter :text.filter": "update_selection",
					"click .new_labels .toggle-add-label": "show_add_label",
					"click .new_labels .submit-label": "add_new_labels",
					"focus :text.filter": "add_focus_state",
					"blur :text.filter": "remove_focus_state",
					"keyup .new_labels :text.add-label": "enter_new_labels"
				},
				initialize: function () {
					this.listenTo(Upfront.Events, "media_manager:media:labels_updated", this.render);
					this._adding_label = false;
				},
				stop_prop: function (e) {
					e.stopPropagation();
					// Also let's focus the text field
					this.$el.find(":text.filter").focus();
				},
				add_focus_state: function (e) {
					this.$el.addClass('focus');
				},
				remove_focus_state: function (e) {
					this.$el.removeClass('focus');
				},
				render: function () {
					var sel = this.selection || '';
					this.$el.empty()
						.append('<div class="title">' + l10n.assigned_labels + '</div>')
						.append('<div class="labels_filter"><ul></ul><input type="text" class="filter upfront-field upfront-field-text" value="' + sel + '" placeholder="' + l10n.type_labels_pick + '" /></div>')
						.append('<div class="labels_list"><ul></ul></div>')
						.append('<div class="title">' + l10n.create_new_label + '</div>')
						.append('<div class="new_labels"><a class="toggle-add-label upfront-icon-control upfront-icon-label-add"></a><input type="text" class="add-label upfront-field upfront-field-text" placeholder="' + l10n.type_labels_add + '" /><a class="submit-label upfront-icon-control upfront-icon-label-add-alt"></a></div>')
					;
					this.render_existing_labels();
					this.render_labels();
				},
				render_existing_labels: function () {
					var me = this,
						$hub = this.$el.find("div.labels_filter ul"),
						count = 0
					;
					$hub.empty();
					
					_.each(this.model.get_shared_labels(), function (label) {
						var item = new MediaManager_ItemControl_LabelItem({model: label});
						item.media_items = me.model;
						item.render();
						$hub.append(item.$el);
						count++;
					});
					
					if (count > 0) {
						// Remove placeholder
						this.$el.find(".labels_filter :text.filter").attr('placeholder', '');
					}
				},
				render_labels: function () {
					var me = this,
						$hub = this.$el.find(".labels_list ul"),
						known_labels = ActiveFilters.get("label"),
						shared_labels = this.model.get_shared_labels(),
						has_selection = false,
						match = 0
					;
					$hub.empty();
					if (!this.selection) return false;
					
					known_labels.each(function (label) {
						if (me.model.is_used_label(label)) return;
						
						var item = new MediaManager_ItemControl_LabelItem({model: label});
						item.shared = shared_labels;
						item.media_items = me.model;
						item.selection = me.selection;
						item.render();
						if (item.matched) {
							$hub.append(item.$el);
							match++;
						}
					});
					if (match > 0) this.$el.addClass('has_match');
					else this.$el.removeClass('has_match');
				},
				update_selection: function (e) {
					e.preventDefault();
					e.stopPropagation();
					var $text = this.$el.find(".labels_filter :text.filter"),
						selection = $text.val()
					;
					this.selection = selection;

					this.render_labels();
				},
				show_add_label: function (e) {
					e.preventDefault();
					e.stopPropagation();
					var $hub = this.$el.find(".new_labels"),
						$text = this.$el.find(".new_labels :text.add-label")
					;
					$hub.addClass('active');
					$text.focus();
				},
				enter_new_labels: function (e) {
					if (e.which == 13) {
						this.add_new_labels();
					}
				},
				add_new_labels: function (e) {
					if (e) {
						e.preventDefault();
						e.stopPropagation();
					}
					if (this._adding_label) return;
					var me = this,
						$hub = this.$el.find(".new_labels"),
						$text = this.$el.find(".new_labels :text.add-label"),
						selection = $text.val()
					;
					this._adding_label = true;
					$text.attr('disabled', true);
					this.model.add_new_label(selection)
						.always(function(){
							me._adding_label = false;
							$text.val('').attr('disabled', false);
						})
						.fail(function(jqxhr){
							var response = jqxhr.responseJSON;
							if (response && response.error) {
								Upfront.Views.Editor.notify(response.error, 'error');
							}
						});
				}
			});

			var MediaManager_ItemControl_LabelItem = Backbone.View.extend({
				tagName: 'li',
				events: {
					click: "toggle_label_assignment"
				},
				render: function () {
					var me = this,
						is_used = this.media_items.is_used_label(this.model),
						used = _.template('<input type="checkbox" id="{{id}}" class="upfront-field-checkbox" value="{{value}}" checked />'),
						free = _.template('<input type="checkbox" id="{{id}}" class="upfront-field-checkbox" value="{{value}}" />'),
						label = _.template('<label for="{{id}}">{{name}}</label>'),
						name = this.model.get("filter") || '',
						match_rx = this.selection ? new RegExp('(' + this.selection + ')', 'i') : false,
						obj = this.model.toJSON()
					;
					this.matched = false;
					this.$el.empty();
					if (match_rx && !name.match(match_rx)) return false;
					this.matched = true;
					obj.id = this.cid;
					obj.name = name.replace(match_rx, '<span class="selection">$1</span>');
					this.$el
						.append(label(obj))
						.append((is_used ? used : free)(obj))
					;
				},
				toggle_label_assignment: function (e) {
					e.preventDefault();
					e.stopPropagation();
					this.media_items.update_label_state(this.model);
				}
			});

		var MediaManager_SearchFiltersControl = Backbone.View.extend({
			className: "upfront-search_filter-control",
			events: {
				"click a": "clear_search"
			},
			render: function () {
				var search = ActiveFilters.get("search").first(),
					obj = search.toJSON();
				obj.total = ActiveFilters.max_items;
				this.$el.empty().append(
					_.template(l10n.showing_total_results + ' <b class="search-text">{{value}}</b> <a href="#clear" class="clear_search">' + l10n.clear_search + '</a>', obj)
				);
			},
			clear_search: function (e) {
				e.preventDefault();
				e.stopPropagation();
				var search = new MediaFilter_Item({filter: false, value: false, state: false});
				ActiveFilters.set({search: new MediaFilter_Collection([search])});
				Upfront.Events.trigger("media_manager:media:list", ActiveFilters);
				Upfront.Events.trigger("media:search:requested", search);
			}
		});

		var MediaManager_FiltersControl = Backbone.View.extend({
			className: "upfront-filter-control",
			events: {
				"click": "stop_prop"
			},
			stop_prop: function (e) {
				e.stopPropagation();
				if (this.filter_selection) this.filter_selection.trigger("filters:outside_click");
			},
			initialize: function () {
				this.filter_selection = new MediaManager_FiltersSelectionControl();
				//this.filters_selected = new MediaManager_FiltersSelectedControl({model: ActiveFilters});
			},
			render: function () {
				this.filter_selection.render();
				//this.filters_selected.render();
				this.$el.empty()
					.append(this.filter_selection.$el)
					//.append(this.filters_selected.$el)
				;
			},
			toggle_titles: function (e) {
				e.stopPropagation();
				Upfront.Events.trigger("media_manager:media:toggle_titles");
			},
			remove: function() {
				this.filter_selection.remove();
				//this.filters_selected.remove();
				Backbone.View.prototype.remove.call(this);
			}
		});

		var MediaManager_FiltersSelectedControl = Backbone.View.extend({
			className: "upfront-filter_selected-control",
			events: {
				"click a.filter": "drop_filter",
				"click a.all_filters": "drop_all"
			},
			initialize: function () {
				this.listenTo(Upfront.Events, "media_manager:media:list", this.set_filters);
			},
			render: function () {
				this.$el.empty();
				var me = this,
					_list = _(this.model.to_list()),
					_to_render = _([]),
					tpl = _.template(' <a href="#" class="filter upfront-icon upfront-icon-media-label-delete" data-type="{{type}}" data-filter="{{filter}}">{{filter}}</a>')
				;

				_list.each(function (filters, type) {
					_(filters).each(function (filter) {
						_to_render.push({filter: filter, type: type});
					});
				});
				if (!_to_render.size()) return false; // Do not render the empty filter array (ie. only defaults)

				this.$el.append('<label class="upfront-field-label upfront-field-label-block">' + l10n.active_filters + '</label>');

				_to_render.each(function (item) {
					me.$el.append(tpl(item));
				});
				this.$el.append(" <a href='#' class='all_filters'>" + l10n.clear_all_filters + "</a>");
			},
			set_filters: function (filters) {
				this.model = filters;
				this.render();
			},
			drop_filter: function (e) {
				e.preventDefault();
				e.stopPropagation();
				var $el = $(e.target),
					all = this.model,
					type = $el.attr("data-type"),
					filter = $el.attr("data-filter")
				;
				if (type && all.get(type)) {
					var has = all.get(type).where({filter: filter});
					if (has && has.length) _(has).invoke("set", {state: false}, {silent: true});
				} else {
					_(this.model.attributes).each(function (collection, idx) {
						var has = all.get(idx).where({filter: filter});
						if (has.length) {
							type = idx;
							_(has).invoke("set", {state: false}, {silent: true});
						}
					});
				}
				Upfront.Events.trigger("media_manager:media:filters_updated", type, this.model);
			},
			drop_all: function (e) {
				e.preventDefault();
				e.stopPropagation();
				Upfront.Events.trigger("media_manager:media:filters_updated", false, false);
			}
		});

		var MediaManager_FiltersSelectionControl = Backbone.View.extend({
			className: "upfront-filter_selection-control clearfix",
			initialize: function () {
				this.controls = _([
					new Control_MediaType(),
					//new Control_MediaDate(),
					//new Control_MediaFileName(),
					//new Control_MediaRecent(),
					new Control_MediaLabels()
				]);
				this.on("filters:outside_click", function () {
					this.controls.each(function (ctrl) {
						ctrl.trigger("filters:selection:click");
					});
				}, this);
			},
			render: function () {
				var me = this,
					tpl = _.template("<li style='display:none'><a href='#' data-idx='{{idx}}'>{{name}}</a></li>"),
					values = []
				;
				this.controls.each(function (ctl, idx) {
					values.push({label: ctl.get_name(), value: idx});
				});

				this.$el.empty();

				this.$el.append('<div class="upfront-filter_control" />');
				this.$control = this.$el.find("div.upfront-filter_control");

				this.control_field = new Upfront.Views.Editor.Field.Radios({
					label: l10n.filter_label,
					name: "filter-selection",
					className: "upfront-field-wrap upfront-field-wrap-multiple upfront-field-wrap-radios upfront-filter_selection-tab",
					values: values,
					multiple: false,
					default_value: ActiveFilters.current_filter_control,
					change: function(){
						me.select_control(this.get_value());
					}
				});
				this.control_field.render();
				this.$el.prepend(this.control_field.$el);
				this.control_field.trigger('changed');
			},
			select_control: function (idx) {
				this.$control.empty();
				if ('false' === idx) return false;

				ActiveFilters.current_filter_control = idx;

				var control = this.controls.toArray()[idx];
				control.render();
				this.$control.append(control.$el);
				return false;
			},
			remove: function () {
				this.controls.each(function (ctrl) {
					ctrl.remove();
				});
				Backbone.View.prototype.remove.call(this);
			}
		});

		var Media_FilterSelection_Multiselection = Backbone.View.extend({
			tagName: "ul",
			get_name: function () {
				return this.filter_name;
			},
			initialize_model: function () {
				this.model = ActiveFilters.get(this.filter_type);
				this.listenTo(this.model, "change", this.apply_changes);
			},
			render: function () {
				var me = this;
				this.$el.empty();
				this.model.each(function (model) {
					if (me.allowed_values && me.allowed_values.indexOf(model.get("value")) < 0) return false;
					var item = new Media_FilterSelection_Multiselection_Item({model: model});
					item.render();
					me.$el.append(item.$el);
				});
			},
			apply_changes: function () {
				var data = {},
					values = []
				;
				data = this.model.where({state:true});
				Upfront.Events.trigger("media_manager:media:filters_updated", this.filter_type, data);
			},
			update_selection: function () {
				var active = ActiveFilters.get(this.filter_type);
				if (!active) {
					this.model.invoke("set", {state: false});
				} else {
					this.model.each(function (model) {
						var has = active.where({filter: model.get("filter"), state: true});
						model.set({state: !!has.length});
					});
				}
				this.render();
				return false;
			}
		});

		var Media_FilterSelection_AdditiveMultiselection = Media_FilterSelection_Multiselection.extend({
			tagName: "div",
			className: "upfront-additive_multiselection",
			events: {
				click: "stop_prop",
				"keyup :text.filter": "show_matching_labels",
				"focus :text.filter": "add_focus_state",
				"blur :text.filter": "remove_focus_state"
			},
			stop_prop: function (e) {
				e.stopPropagation();
				// Also let's focus the text field
				this.$el.find(":text.filter").focus();
			},
			add_focus_state: function (e) {
				this.$el.addClass('focus');
			},
			remove_focus_state: function (e) {
				this.$el.removeClass('focus');
			},
			render: function () {
				var me = this,
					sel = this.selection || ''
				;
				this.$el
					.empty()
					.append('<div class="title">' + l10n.filter_by_labels + '</div>')
					.append('<div class="labels_filter"><ul></ul><input type="text" class="filter upfront-field upfront-field-text" value="' + sel + '" placeholder="' + l10n.type_labels + '" /></div>')
					.append('<div class="labels_list"><ul></ul></div>')
				;
				this.render_filtered_items();
				this.render_items();
			},
			render_filtered_items: function () {
				var me = this,
					$hub = this.$el.find("div.labels_filter ul"),
					count = 0
				;
				$hub.empty();
				
				this.model.each(function (model) {
					if (me.allowed_values && me.allowed_values.indexOf(model.get("value")) < 0) return false;
					if (!model.get("state")) return false;
					var item = new Media_FilterSelection_AdditiveMultiselection_Item({model: model});
					item.render();
					$hub.append(item.$el);
					count++;
				});
				
				if (count > 0) {
					// Remove placeholder
					this.$el.find(":text.filter").attr('placeholder', '');
				}
			},
			render_items: function () {
				var me = this,
					$hub = this.$el.find("div.labels_list ul"),
					match = 0
				;
				$hub.empty();
				if (!this.selection) {
					this.$el.removeClass('has_match');
					return false;
				}
				//if (!this.$el.is(".active")) return false; // Only actually render this if we can see it - it takes *a while* to do so

				this.model.each(function (model) {
					if (me.allowed_values && me.allowed_values.indexOf(model.get("value")) < 0) return false;
					if (model.get("state")) return false;
					var item = new Media_FilterSelection_AdditiveMultiselection_Item({model: model});
					item.selection = me.selection;
					item.render();
					if (item.matched) {
						$hub.append(item.$el);
						match++;
					}
				});
				if (match > 0) this.$el.addClass('has_match');
				else this.$el.removeClass('has_match');
			},
			show_matching_labels: function (e) {
				var $text = this.$el.find(":text.filter"),
					selection = $text.val()
				;
				this.selection = selection;
				this.render_items();
			}
		});

		var Media_FilterSelection_Uniqueselection = Media_FilterSelection_Multiselection.extend({
			render: function () {
				var me = this;
				this.$el.empty();
				this.model.each(function (model) {
					if (me.allowed_values && me.allowed_values.indexOf(model.get("value")) < 0) return false;
					var item = new Media_FilterSelection_Uniqueselection_Item({model: model});
					item.render();
					me.$el.append(item.$el);
					me.listenTo(item, "model:unique_state:change", me.change_state);
				});
			},
			change_state: function (model) {
				this.model.each(function (item) {
					if (item.get("value") != model.get("value")) item.set({state: false}, {silent: true});
				});
				model.set({state: true}, {silent: true});
				this.apply_changes(model);
				this.render();
			}
		});

			var Media_FilterSelection_Multiselection_Item = Backbone.View.extend({
				tagName: "li",
				events: {
					"click": "on_click"
				},
				initialize: function () {
					this.listenTo(this.model, "change", this.render);
				},
				render: function () {
					var name = this.model.get("filter");
					if ("other" === this.model.get("value")) return false; // DO NOT RENDER "ALL", special case
					if (this.model.get("state")) name = '<b>' + name + '</b>';
					this.$el.empty().append(name);
				},
				on_click: function (e) {
					e.preventDefault();
					e.stopPropagation();
					this.model.set({state: !this.model.get("state")});
				}
			});

			var Media_FilterSelection_Uniqueselection_Item = Media_FilterSelection_Multiselection_Item.extend({
				on_click: function (e) {
					e.preventDefault();
					e.stopPropagation();
					this.model.set({state: !this.model.get("state")}, {silent: true});
					this.trigger("model:unique_state:change", this.model);
				}
			});

			var Media_FilterSelection_AdditiveMultiselection_Item = Media_FilterSelection_Multiselection_Item.extend({
				render: function () {
					var checked = _.template('<input type="checkbox" for="{{id}}" class="upfront-field-checkbox" name="{{filter}}" value="{{value}}" checked />'),
						unchecked = _.template('<input type="checkbox" for="{{id}}" class="upfront-field-checkbox" name="{{filter}}" value="{{value}}" />'),
						label = _.template('<label for="{{id}}">{{name}}</label>'),
						name = this.model.get("filter") || '',
						match_rx = this.selection ? new RegExp('(' + this.selection + ')', 'i') : false,
						obj = this.model.toJSON()
					;
					this.matched = false;
					this.$el.empty();
					if (match_rx && !name.match(match_rx)) return false;
					this.matched = true;
					obj.id = this.cid;
					obj.name = name.replace(match_rx, '<span class="selection">$1</span>');
					this.$el
						.append(label(obj))
						.append((this.model.get("state") ? checked : unchecked)(obj))
					;
				}
			});

		var Media_FilterCollection = Backbone.View.extend({
			render: function () {
				var me = this;
				this.$el.empty();
				this.model.each(function (model) {
					var item = new Media_FilterItem({model: model});
					item.render();
					me.$el.append(item.$el);
				});
			}
		});

			var Media_FilterItem = Backbone.View.extend({
				render: function () {
					this.$el.empty().append(this.model.get("filter"));
				}
			});

		var Control_MediaType = Media_FilterSelection_Uniqueselection.extend({
			initialize: function () {
				this.filter_name = l10n.by_type;
				this.filter_type = "type";
				this.initialize_model();
				this.listenTo(Upfront.Events, "media_manager:media:filters_updated", this.update_selection);
				this.listenTo(Upfront.Events, "media_manager:media:filters_reset", this.initialize_model);
			},
			render: function () {
				var me = this,
					values = [],
					has_all = (this.model.indexOf("other") >= 0)
				;
				this.$el.empty();
				this.model.each(function (model) {
					if (me.allowed_values && me.allowed_values.indexOf(model.get("value")) < 0) return false;
					values.push({label: model.get("filter"), value: model.get("value")});
				});
				this.select_field = new Upfront.Views.Editor.Field.Select({
					name: "filter-type",
					values: values,
					multiple: false,
					default_value: has_all ? "other" : this.model.findWhere({state: true}).get("value"),
					change: function(){
						var model = me.model.findWhere({value: this.get_value()});
						model.set({state: !model.get("state")}, {silent: true});
						me.change_state(model);
					}
				});
				this.select_field.render();
				this.$el.append(this.select_field.$el);
			},
			/*apply_changes: function (model) {
				return;
				var all = this.model.where({state: true}),
					other = this.model.where({value: 'other'}),
					edited = model.previousAttributes()
				;
				if (other.length) other = other[0]; // Do the model
				else return;

				if (edited && edited.value && "other" === edited.value) {
					var no_other = !!edited.state;
					this.model.each(function (mod) {
						mod.set({state: no_other}, {silent: true});
					});
					other.set({state: !no_other}, {silent: true});
				} else if (other.get("state")) other.set({state: false}, {silent: true});

				Media_FilterSelection_Uniqueselection.prototype.apply_changes.call(this);

			}*/
		});

		var Control_MediaFileName = Media_FilterSelection_Uniqueselection.extend({
			allowed_values: ['title_desc', 'title_asc'],
			initialize: function () {
				this.filter_name = l10n.file_name;
				this.filter_type = "order";
				this.initialize_model();
				this.listenTo(Upfront.Events, "media_manager:media:filters_updated", this.update_selection);
				this.listenTo(Upfront.Events, "media_manager:media:filters_reset", this.initialize_model);
			}
		});

		var Control_MediaLabels = Media_FilterSelection_AdditiveMultiselection.extend({
			initialize: function () {
				this.filter_name = l10n.by_labels;
				this.filter_type = "label";
				this.initialize_model();
				this.listenTo(Upfront.Events, "media_manager:media:filters_updated", this.update_selection);
				this.listenTo(Upfront.Events, "media_manager:media:filters_reset", this.initialize_model);
				this.listenTo(Upfront.Events, "media_manager:media:labels_loaded", this.reinitialize_model);
			},
			reinitialize_model: function () {
				this.initialize_model();
				this.render();
			}
		});

		

	var MediaManager_SearchControl = Backbone.View.extend({
		className: "upfront-search-control",
		events: {
			"click .clear": "clear_search",
			"keyup :text": "on_keyup"
		},
		render: function () {
			var me = this,
				active = ActiveFilters.get("search"),
				search = !!active.length ? active.first() : false,
				has_search = !!search && search.get("state")
			;
			if ( _.isUndefined(this.search_field) ) {
				this.search_field = new Upfront.Views.Editor.Field.Text({
					label: l10n.search_media,
					name: 'search',
					placeholder: l10n.search_placeholder,
					default_value: has_search && search ? search.get("value") : ''
				});
			}
			if ( _.isUndefined(this.search_button) ) {
				this.search_button = new Upfront.Views.Editor.Field.Button({
					label: l10n.search,
					classname: 'upfront-media-search-button',
					compact: true,
					on_click: function(e) {
						e.preventDefault();
						e.stopPropagation();
						me.do_search();
					}
				});
			}
			this.search_field.render();
			this.search_button.render();
			this.$el.empty()
				.append(this.search_field.$el)
				.append(this.search_button.$el)
			;
			if (has_search) {
				this.$el.addClass("has_search");
			}
			else {
				this.$el.removeClass("has_search");
			}
		},
		do_search: function () {
			var text = this.search_field.get_value(),
				search = new MediaFilter_Item({filter: text, value: text, state: true})
			;
			if (!text) {
				search = new MediaFilter_Item({filter: false, value: false, state: false});
			}

			ActiveFilters.to_defaults();
			ActiveFilters.current_keys = [];
			ActiveFilters.current_models = [];
			ActiveFilters.set("search", new MediaFilter_Collection([search]));
			Upfront.Events.trigger("media_manager:media:list", ActiveFilters);
			Upfront.Events.trigger("media:search:requested", search);
			this.render();

			var selected_model = new MediaCollection_Selection(ActiveFilters.current_models);
			Upfront.Events.trigger("media:item:selection_changed", selected_model);
		},
		clear_search: function (e) {
			e.preventDefault();
			e.stopPropagation();
			this.search_field.set_value('');
			this.do_search(e);
		},
		on_keyup: function (e) {
			if ( e.keyCode == 13 )
				this.search_button.$el.trigger('click');
			//else if ( e.keyCode == 27 )
			//	this.$el.find('.clear').trigger('click');
		}
	});

	/**
	 * Top-level controls
	 */
	var MediaManager_Switcher = Backbone.View.extend({
		events: {
			"click .media-upload": "switch_to_upload",
			"click .media-info": "switch_controls"
		},
		info_template: _.template(
			'<button type="button" class="media-info upfront-icon-control upfront-icon-media-info">' + l10n.info + '</button>'
		),
		upload_template: _.template(
			'<button type="button" class="media-upload upfront-icon-control upfront-icon-media-upload">' + l10n.upload + '</button>'
		),
		initialize: function (options) {
			this.options = options;
			this.listenTo(Upfront.Events, "media:item:selection_changed", this.switch_delete);
		},
		render: function () {
			this.$el.empty().append(
				this.info_template({}) +
				(ActiveFilters.has_upload() ? this.upload_template({}) : '')
			);
			this.$el.addClass('upfront-no-select clearfix');
		},
		remove: function () {
			this.undelegateEvents();
			if (this.delete_control) {
				this.delete_control.remove();
				this.delete_control = false;
			}
			// We don't want to remove this element as it belongs to popup, so just empty it out and remove bound events
			this.$el.empty();
			this.stopListening();
		},
		switch_to_upload: function (e) {
			e.preventDefault();
			e.stopPropagation();
            this.trigger("media_manager:switcher:to_upload");
		},
		switch_controls: function (e) {
			if (e) {
				e.preventDefault();
				e.stopPropagation();
			}
			if (!this.options.can_toggle_control) return;
			this.$el.find('.media-info').toggleClass('media-info-active');
            this.trigger("media_manager:switcher:toggle_controls");
		},
		show_controls: function () {
			this.$el.find('.media-info').addClass('media-info-active');
            this.trigger("media_manager:switcher:toggle_controls", true);
		},
		switch_delete: function (media_collection) {
			var positive = media_collection && media_collection.where ? media_collection.where({selected: true}) : [];
			if (positive.length) {
				this.render_delete(positive);
			}
			else {
				if (this.delete_control) {
					this.delete_control.remove();
					this.delete_control = false;
				}
			}
		},
		render_delete: function (selected) {
			if ( this.delete_control ) this.delete_control.remove();
			this.delete_control = new MediaManager_DeleteControl({model: new MediaCollection_Selection(selected)});
			this.delete_control.render();
			//this.render_selection();
			this.$el.append(this.delete_control.$el);
			this.$el.addClass('upfront-media-manager-has-select');
		},
		/**
		 * Boolean helper for determining if we're in some sort of a free-form or text editing mode
		 */
		is_in_editing_mode: function () {
			var type = Upfront.Application.sidebar.prevented_usage_type;
			return !(type.match(/media/));
		}
	});

	/**
	 * Main media dispatcher, has main level views.
	 */
	var MediaManager_View = Backbone.View.extend({
		_request_in_progress: false,
		initialize: function (data) {
			data = _.extend({
				type: "PostImage",
				multiple_selection: false,
				can_toggle_control: false,
				show_control: true,
				show_insert: true
			}, data);

			var type = data.type,
				multiple_selection = data.multiple_selection,
				button_text = data.button_text,
				button_text_multiple = data.button_text_multiple
			;

			this.popup_data = data.data;
			this.can_toggle_control = data.can_toggle_control;
			this.show_control = data.show_control;

			ActiveFilters.to_defaults();
			this.switcher_view = new MediaManager_Switcher({
				el: this.popup_data.$top,
				can_toggle_control: this.can_toggle_control,
				show_control: this.show_control
			});

            this.listenTo(this.switcher_view, "media_manager:switcher:to_upload", this.render_upload, this);
            this.listenTo(this.switcher_view, "media_manager:switcher:toggle_controls", this.toggle_controls, this);

			this.command_view = new MediaManager_BottomCommand({
				el: this.popup_data.$bottom,
				button_text: button_text,
				button_text_multiple: button_text_multiple,
				ck_insert: data.ck_insert,
				show_insert: data.show_insert
			});
			
			this.library_view = new MediaManager_PostImage_View(data.collection, data);
			//this.embed_view = new MediaManager_EmbedMedia({});

			this.library_view.multiple_selection = multiple_selection;

			if(data.themeImages){
				ActiveFilters.themeImages = true;
				this.library_view.multiple_selection = false;
			}
			
			this.listenTo(Upfront.Events, "media:item:selection_changed", this.update_model);
		},
		remove: function() {
			this.library_view.remove();
			this.switcher_view.remove();
			this.command_view.remove();
			//this.library_view = new MediaManager_PostImage_View(this.collection);
			// We don't want to remove this element as it belongs to popup, so just empty it out and remove bound events
			this.$el.empty();
			this.stopListening();
		},
		render: function () {
			this.switcher_view.render();
			this.command_view.render();
			this.render_library();
			this.listenTo(Upfront.Events, "media_manager:media:list", this.switch_media_type);
		},
		render_library: function () {
			this.load();
			//this.embed_view.model.clear({silent:true});
			this.library_view.render();

			this.$el.empty().append(this.library_view.$el);
		},
		render_embed: function () {
			return false;
			/*
			this.embed_view.model.clear({silent:true});
			this.embed_view.render();
			this.embed_view.$el.css({
				'max-height': this.popup_data.height,
				'overflow-y': 'scroll'
			});
			this.$el.empty().append(this.embed_view.$el);
			*/
		},
		render_upload: function (e) {
			if (!this.library_view.$el.is(":visible")) this.render_library();

			// Check if we're actually allowing uploads
			if (!(window._upfront_media_upload && _upfront_media_upload.image_ref)) {
				alert(l10n.disabled);
				return false;
			}

			var me = this,
				uploaded = 0,
				progressing = 0,
				done = 0,
				processed = 0,
				new_media = [],
				media_library_view = me.library_view._subviews.media,
				uploadUrl = ActiveFilters.themeImages ? _upfront_media_upload.theme : _upfront_media_upload.normal
			;

			this.$("#fileupload").remove();
			this.$el.append('<input id="fileupload" type="file" style="display:none" name="media" data-url="' + uploadUrl + '" multiple >');
			this.$("#fileupload").off("click").on("click", function (e) { e.stopPropagation(); }).fileupload({
				dataType: 'json',
				add: function (e, data) {
					var media = data.files[0],
						count = uploaded,
						name = media.name || 'tmp'
					;
					uploaded++;
					new_media[count] = new MediaItem_Model({progress: 0});
					new_media[count].set({post_title: name});
					media_library_view.model.add(new_media[count], {at: 0});
					data.submit();
					new_media[count].on("upload:abort", function () {
						data.abort();
						// If normal Media.
						if (new_media[count].get("ID") && !ActiveFilters.themeImages) {
							// Already uploaded this file, remove on the server side
							Upfront.Util.post({
								action: "upfront-media-remove_item",
								item_id: new_media[count].get("ID")
							}).always(function () {
								media_library_view.model.trigger("change");
							});
						} 
						media_library_view.model.remove(new_media[count]);
						media_library_view.model.trigger("change");
					});
					new_media[count].trigger("upload:start", media);
				},
				progressall: function (e, data) {
					var count = progressing;
					progressing++;
					var progress = parseInt(data.loaded / data.total * 100, 10);
					if (new_media[count]) new_media[count].trigger("upload:progress", progress);
				},
				done: function (e, data) {
					var count = done;
					done++;
					processed++;
					if(ActiveFilters.themeImages){
						new_media[count].set(data.result.data, {silent: true});
						new_media[count].trigger("upload:finish", me);
						if (processed == uploaded) { // Refresh when all images has processed
							ActiveFilters.to_defaults();
							Upfront.Events.trigger("media_manager:media:list", ActiveFilters);
						}
						return;
					}

					var result = data.result.data || [],
						uploaded_id = result[0]
					;

					new_media[count].set({ID: uploaded_id}, {silent:true});
					Upfront.Util.post({
						action: "upfront-media-get_item",
						item_id: uploaded_id
					}).done(function (response) {
						new_media[count].set(response.data, {silent:true});
						new_media[count].trigger("upload:finish", me);
					}).always(function () {
						if (processed == uploaded) { // Refresh when all images has processed
							ActiveFilters.to_defaults();
							Upfront.Events.trigger("media_manager:media:list", ActiveFilters);
						}
					});
				},
				fail: function (e, data) {
					processed++;
					if (data.jqXHR.responseJSON && data.jqXHR.responseJSON.error) Upfront.Views.Editor.notify(data.jqXHR.responseJSON.error, 'error');
					if (processed == uploaded) { // Refresh when all images has processed
						ActiveFilters.to_defaults();
						Upfront.Events.trigger("media_manager:media:list", ActiveFilters);
					}
				}
			}).trigger("click");

		},
		render_shortcode: function () {
			//console.log("SHORTCODE YAY");
		},
		render_markup: function () {
			//console.log("MARKUP YAY");
		},
		toggle_controls: function (show) {
			if (!this.library_view) return false;
			this.library_view.toggle_controls(show);
			this.show_control = false;
		},
		load: function (data) {
			Upfront.Events.trigger("media_manager:load:start", data);
			this._request_in_progress = true;
			data = data && data.type ? data : ActiveFilters.to_request_json();
			data.media_limit = ActiveFilters.media_limit;
			data.action = ActiveFilters.themeImages ? 'upfront-media-list_theme_images' : "upfront-media-list_media";
			var me = this;
			if (this.library_view.media_view && this.library_view.media_view.start_loading) this.library_view.media_view.start_loading();
			Upfront.Util.post(data)
				.done(function (response) {
					ActiveFilters.set_max_pages(response.data.meta.max_pages);
					ActiveFilters.set_max_items(response.data.meta.max_items);
					me.library_view.update(response.data.items);
					me.command_view.render();
					if (me.show_control) me.switcher_view.show_controls();
					Upfront.Events.trigger("media_manager:load:done", response);
				})
				.fail(function (response) {
					me.library_view.update([]);
					me.command_view.render();
					Upfront.Events.trigger("media_manager:load:fail", response);
				})
				.always(function () {
					me._request_in_progress = false;
				})
			;
		},
		switch_media_type: function (what) {
			if (this._request_in_progress) return false;
			this.load(what.to_request_json());
		},
		update_model: function (selected) {
			// checking on all models on current page
			for ( var key in selected.models ) {
				var model = selected.models[key],
					modelAttributes = ((model || {}).attributes || {})
					index = ActiveFilters.current_keys.indexOf(modelAttributes.ID)
				;
				if( index == -1 ) {
					// inserting selected media models on the list
					if( modelAttributes.selected ) {
						ActiveFilters.current_keys.push(modelAttributes.ID);
						ActiveFilters.current_models.push(model);
					}
				} else {
					// removing media models on the list
					if( !modelAttributes.selected || modelAttributes.selected === undefined ) {
						ActiveFilters.current_keys.splice(index, 1);
						ActiveFilters.current_models.splice(index, 1);
					}
				}
			}
			Upfront.Events.trigger("media_manager:active_filters:updated");
		}
	});

	/**
	 * Bottom commands view (search etc)
	 */
	var MediaManager_BottomCommand = Backbone.View.extend({
		initialize: function(opts){
			this.options = opts;
		},
		render: function () {
			var button_text = this.options.button_text,
				button_text_multiple = this.options.button_text_multiple,
				pagination = new MediaManager_Pagination(),
				use = false
			;
			pagination.render();
			if (this.options.show_insert) {
				use = this.options.ck_insert 
					? new MediaManager_BottomCommand_UseSelection_MultiDialog({
							button_text: button_text,
							button_text_multiple: button_text_multiple
						}) 
					: new MediaManager_BottomCommand_UseSelection({
							button_text: button_text,
							button_text_multiple: button_text_multiple
						})
				use.render();
			}
			this.$el.empty()
				.append(pagination.$el)
				.append(use !== false ? use.$el : '')
				.addClass('upfront-no-select')
			;
		},
		switch_to_upload: function (e) {
			this.trigger("media_manager:switcher:to_upload");
		},
		remove: function () {
			// We don't want to remove this element as it belongs to popup, so just empty it out and remove bound events
			this.$el.empty();
			this.stopListening();
		}
	});

		var MediaManager_Pagination = Backbone.View.extend({
			events: {
				"click .upfront-pagination_item-prev": "prev_page",
				"click .upfront-pagination_item-next": "next_page",
				"click .upfront-pagination_page-item": "set_page",
				"click .upfront-pagination_page-current": "stop_prop",
				"keypress .upfront-pagination_page-current": "set_page_keypress"
			},
			stop_prop: function (e) { e.stopPropagation(); },
			render: function () {
				var markup = '';
				if (ActiveFilters.max_pages > 1) {
					markup += '<div id="upfront-entity_list-pagination">';
					markup += '<a class="upfront-pagination_item upfront-pagination_item-skip upfront-pagination_item-prev"></a>';

					// Input
					markup += '<div class="upfront-pagination_navigation">';
					markup += 	'<input type="text" class="upfront-pagination_page-current" value="' + ActiveFilters.current_page + '" />';
					markup += 	'&nbsp;' + l10n.n_of_x + '&nbsp;';
					markup += 	'<a class="upfront-pagination_page-item" data-idx="' + (ActiveFilters.max_pages - 1) + '">' + (ActiveFilters.max_pages - 1) + '</a>';
					markup += '</div>';

					markup += '<a class="upfront-pagination_item upfront-pagination_item-skip upfront-pagination_item-next"></a>';
					// Add max items
					markup += '<small>(' + _.template(l10n.entity_list_info, {items: ActiveFilters.max_items, pages: ActiveFilters.max_pages - 1}) + ')</small>';
					markup += '</div>';
				}

				this.$el.empty().append(markup);
			},
			prev_page: function (e) {
				e.preventDefault();
				e.stopPropagation();
				if (ActiveFilters.prev_page()) Upfront.Events.trigger("media_manager:media:list", ActiveFilters);
			},
			next_page: function (e) {
				e.preventDefault();
				e.stopPropagation();
				if (ActiveFilters.next_page()) Upfront.Events.trigger("media_manager:media:list", ActiveFilters);
			},
			set_page: function (e) {
				e.preventDefault();
				e.stopPropagation();
				if (ActiveFilters.set_page($(e.target).data("idx"))) Upfront.Events.trigger("media_manager:media:list", ActiveFilters);
			},
			set_page_keypress: function (e) {
				e.stopPropagation();
				//e.preventDefault();
				if (13 !== e.which) return true;

				var string = $.trim($(e.target).val()),
					num = parseInt(string, 10)
				;
				if (!num) return false;
				if (num > ActiveFilters.max_pages) num = ActiveFilters.max_pages;
				if (ActiveFilters.set_page(num)) Upfront.Events.trigger("media_manager:media:list", ActiveFilters);
			}
		});

		var MediaManager_BottomCommand_UseSelection = Backbone.View.extend({
			className: "use_selection_container",
			events: {
				"click a": "use_selection"
			},
			initialize: function (opts) {
				this.options = opts;
				this.listenTo(Upfront.Events, "media_manager:active_filters:updated", this.render);
			},
			render: function () {
				this.$el.empty();
				if ( ActiveFilters.current_models.length == 1 ) {
					this.$el.append('<a href="#use" class="use">' + ( this.options.button_text || l10n.ok )+ '</a>');
				}
				else if ( ActiveFilters.current_models.length > 1 ) {
					this.$el.append('<a href="#use" class="use">' + ( this.options.button_text_multiple || l10n.ok ) + '</a>');
				}
			},
			use_selection: function (e) {
				e.preventDefault();
				e.stopPropagation();

				// using the persistent list instead of current page media collection
				var model = new MediaCollection_Selection(ActiveFilters.current_models);
				Upfront.Popup.close(model);
			}
		});

			var MediaManager_BottomCommand_UseSelection_MultiDialog = MediaManager_BottomCommand_UseSelection.extend({
				use_selection: function (e) {
					e.preventDefault();
					e.stopPropagation();
					if ( this.model && this.model.length > 1 )
						this.open_dialog();
					else
						Upfront.Popup.close(this.model);
				},
				open_dialog: function () {
					var $dialog = $('<div id="media-manager-multi-dialog" class="upfront-ui" />');
					$dialog.append('<h3 class="multi-dialog-title">' + l10n.insertion_question + '</h3>');
					$dialog.append(
						'<ul class="multi-dialog-choices">' +
							'<li class="multi-dialog-choice upfront-icon upfront-icon-media-insert-multi-plain" data-choice="plain">' + l10n.plain_images + '</li>' +
							'<li class="multi-dialog-choice upfront-icon upfront-icon-media-insert-multi-slider" data-choice="slider">' + l10n.image_slider + '</li>' +
							'<li class="multi-dialog-choice upfront-icon upfront-icon-media-insert-multi-gallery" data-choice="gallery">' + l10n.image_gallery + '</li>' +
						'</ul>'
					);
					Upfront.Popup.$popup.find("#upfront-popup-content").append($dialog);
					$dialog.on('click', '.multi-dialog-choice', this, this.select_dialog);
				},
				select_dialog: function (e) {
					e.preventDefault();
					e.stopPropagation();
					var $dialog = $('#media-manager-multi-dialog'),
						choice = $(this).attr('data-choice') || 'plain',
						obj = e.data;
					obj.model.type = choice;
					$dialog.remove();
					Upfront.Popup.close(obj.model);
				}
			});

	/**
	 * Embed media from URL
	 */
	var MediaManager_EmbedMedia = Backbone.View.extend({
		className: "upfront-embed_media clearifx",
		initialize: function () {
			this.model = new MediaItem_Model();
		},
		render: function () {
			this.embed_pane = new MediaManager_Embed_DetailsPane({model: this.model});
			this.embed_pane.on("embed:editable:updated", this.embed_updated, this);

			this.preview_pane = new MediaManager_Embed_PreviewPane({model: this.model});

			this.embed_pane.render();
			this.preview_pane.render();
			this.$el.empty()
				.append(this.embed_pane.$el)
				.append(this.preview_pane.$el)
			;
		},
		embed_updated: function () {
			this.preview_pane.render_progress();
			var me = this;
			Upfront.Util.post(_.extend({
				action: "upfront-media-embed",
				media: this.model.get("original_url")
			}, _upfront_media_upload.embed_ref)).done(function (response) {
				me.model.set(response.data, {silent:true});
				me.preview_pane.trigger("embed:media:imported");
				me.embed_pane.clear_updating_flag();
				me.embed_pane.render();
				me.preview_pane.render();
			});
		}
	});
		var MediaManager_Embed_DetailsPane = Backbone.View.extend({
			className: "upfront-pane",
			embed_is_being_updated: false,
			events: {
				"click button": "save"
			},
			initialize: function () {
				this.editables = _([
					new MediaItem_EmbedableUrl({model: this.model}),
					new MediaItem_EditableTitle({model: this.model}),
					new MediaItem_EditableLabels({model: this.model})
				]);
			},
			render: function () {
				this.$el.empty();
				var me = this;
				this.editables.each(function (editable) {
					editable.render();
					editable.on("embed:updated", me.editable_updated, me);
					me.$el.append(editable.$el);
				});
				this.$el.append('<button type="button">' + l10n.ok + '</button>');
			},
			editable_updated: function () {
				this.embed_is_being_updated = true;
				this.trigger("embed:editable:updated");
			},
			clear_updating_flag: function () {
				this.embed_is_being_updated = false;
			},
			save: function () {
				if (!this.model) return false;
				var me = this;
				if (!this.model.get("ID") && this.embed_is_being_updated) {
					// A case when an embed is still being fetched but OK is clicked
					setTimeout(function () {
						me.save();
					}, 500);
					return false;
				}
				//this.editables.invoke("update"); // Do NOT!! invoke the update
				var data = {
					action: "upfront-media-update_media_item",
					data: this.model.toJSON()
				};
				Upfront.Util.post(data)
					.done(function () {
						me.model.trigger("change");
						// Swap back to media
						Upfront.Events.trigger("media_manager:media:show_library", data.data);
					})
				;
			}
		});

		var MediaManager_Embed_PreviewPane = Backbone.View.extend({
			className: "upfront-pane",
			initialize: function () {
			},
			render: function () {
				this.preview_view = new MediaManager_Embed_Preview({model: this.model});
				this.labels_view = new MediaItem_Labels({model: this.model});
				this.on("embed:media:imported", this.update_media_preview, this);

				this.preview_view.render();
				//this.labels_view.render();
				this.$el.empty()
					.append(this.preview_view.$el)
					//.append(this.labels_view.$el)
				;
			},
			render_progress: function () {
				this.$el.empty().append('<div class="preview_loader" />');
				var $loader = this.$el.find(".preview_loader");
				$loader.css({
					position: "relative",
					minHeight: "250px", // Ugh...
					width: "100%"
				});
				this.loading = new Upfront.Views.Editor.Loading({
					loading: l10n.loading_embeddable_preview,
					done: 'Loaded'
				});
				this.loading.render();
				$loader.append(this.loading.$el);
			},
			update_media_preview: function () {
				this.preview_view.render();
				this.labels_view.render();
				this.labels_view.delegateEvents();
			}
		});

			var MediaManager_Embed_Preview = Backbone.View.extend({
				className: "upfront-media_manager-embed-preview",
				template: _.template('{{thumbnail}} <div class="progress"></div>'),
				render: function () {
					if (!this.model.get("original_url")) return this.$el.empty();
					var me = this,
						is_image = this.is_image(),
						thumbnail = is_image ? this.model.get("thumbnail") : this.get_media_thumbnail()
					;
					this.$el.empty().append(this.template({thumbnail: thumbnail}));
				},
				is_image: function () {
					return (this.model.get("original_url") || "").match(/\.(jpe?g|gif|png)$/i);
				},
				get_media_thumbnail: function () {
					return this.model.get("thumbnail");
				}
			});

	/**
	 * Post images library implementation.
	 */
	var MediaManager_PostImage_View = MediaManager_View.extend({
		className: "upfront-media_manager upfront-media_manager-post_image upfront-no-select clearfix",
		_subviews: {
			media: false,
			aux: false,
			controls: false
		},
		show_controls: false,
		initialize: function (collection, opts) {
			var data = data || {};
			if((collection || {}).models)
				collection = new MediaCollection_Model(collection);
			else
				collection = new MediaCollection_Model();
			this.media_collection = collection;

            this.options = opts;
		},
		render: function () {
			if (!this._subviews.media) {
				this._subviews.media = new MediaCollection_View({model: this.media_collection});
			}
			var media = this._subviews.media;

			/*if (!this._subviews.aux) {
				this._subviews.aux = new MediaManager_AuxControls_View({model: this.media_collection});
			}
			var aux = this._subviews.aux;*/

			if (!this._subviews.controls) {
				this._subviews.controls = new MediaManager_Controls_View({model: this.media_collection, options: this.options });
			}
			var controls = this._subviews.controls;

			media.multiple_selection = this.multiple_selection;

			controls.render();
			//aux.render();
			media.render();
			this.$el
				.empty()
				.append(controls.$el)
				//.append(aux.$el)
				.append(media.$el)
			;
			controls.$el.hide(); // Hide controls by default
			this.media_view = media;
			this.media_view.start_loading();
		},
		update: function (collection) {
			var me = this;
			this.media_view.model.reset(collection);
			this.media_view.end_loading(function(){
				me.media_view.render();
			});
		},
		toggle_controls: function (show) {
			this.show_controls = _.isUndefined(show) ? !this.show_controls : show;
			this._subviews.controls.$el.toggle(show);
			this.$el.toggleClass('upfront-media_manager-show_controls', show);
		},
		remove: function() {
			_.each(this._subviews, function(subview, idx) {
				if (!subview) return true;
				subview.remove();
				this._subviews[idx] = false;
			}, this);
			Backbone.View.prototype.remove.call(this);
		}
	});

	var MediaCollection_View = Backbone.View.extend({
		tagName: 'ul',
		className: 'upfront-media_collection',
		initialize: function () {
			this.listenTo(this.model, "add", this.render);
			this.listenTo(this.model, "remove", this.render);
			this.listenTo(this.model, "change", this.update);
			this.listenTo(this.model, "change:selected", this.propagate_selection);
		},
		render: function () {
			this.subviews = [];
			var me = this;
			this.$el.empty();
			if (!this.model.length) {
				this.$el.append('&nbsp;');
			} else {
				this.model.each(function (model) {
					var view = new MediaItem_View({model: model});
					me.subviews.push(view);
					view.parent_view = me;
					view.render();
					me.$el.append(view.$el);

					// preserving selected media
					if( ActiveFilters.current_keys.length ) {
						var target_index = ActiveFilters.current_keys.indexOf(((model || {}).attributes || {}).ID);
						if( target_index != -1 ) {
							model.set({selected: true}, {silent: true});
							model.trigger("appearance:update");
						}
					}
				});

				// running change event to apply persistent list
				var selected_model = new MediaCollection_Selection(ActiveFilters.current_models);
				Upfront.Events.trigger("media:item:selection_changed", selected_model);

				// Add JS Scrollbar.
				perfectScrollbar.withDebounceUpdate(
					// Element.
					this.el,
					// Run First.
					true,
					// Event.
					false,
					// Initialize.
					true
				);
			}
		},
		update: function () {
			if (this.model.length) {
				this.model.each(function (model) {
					model.trigger("appearance:update");
				});
			}
		},
		start_loading: function () {
			this.loading = new Upfront.Views.Editor.Loading({
				loading: l10n.loading_media_files,
				timeout: 500,
				done: 'Loaded'
			});
			this.loading.render();
			this.loading.$el.insertAfter(this.$el);
		},
		end_loading: function (callback) {
			if (this.loading && this.loading.done) this.loading.done(callback);
			else callback();
		},
		propagate_selection: function (model) {
			if (!this.multiple_selection) {
				var has = this.model.where({selected: true}),
					selected = true === model.get("selected")
				;
				if (has.length) _(has).each(function (item) {
					item.set({selected: false}, {silent: true});
					item.trigger("appearance:update");
				});
				// Persistent list also need to be unselected in this case
				_.each(ActiveFilters.current_models, function(item){
					item.set({selected: false}, {silent: true});
					item.trigger("appearance:update");
				});
				if (selected) model.set({selected: true}, {silent: true});
				model.trigger("appearance:update");
			}
			Upfront.Events.trigger("media:item:selection_changed", this.model);

			// running again change event to apply persistent list
			var selected_model = new MediaCollection_Selection(ActiveFilters.current_models);
			Upfront.Events.trigger("media:item:selection_changed", selected_model);
		},
		remove: function() {
			_.each(this.subviews, function(subview) {
				subview.remove();
			});
			Backbone.View.prototype.remove.call(this);
		}
	});
		var MediaItem_View = Backbone.View.extend({
			tagName: 'li',
			className: 'upfront-media_item',
			events: {
				click: "toggle_item_selection"
			},
			initialize: function () {
				var cls = '';

				// Detect the default WP video icon being used as thumb
				// It's fugly, so let's add class so we can override
				if ((this.model.get("thumbnail") || '').match(/wp-includes\/images\/media\/video/i)) {
					cls += "override";
				}

				this.template = _.template("<div class='thumbnail " + cls + "'>{{thumbnail}}</div> <div class='title'>{{post_title}}</div> <div class='upfront-media_item-editor-container' />");
				this.listenTo(Upfront.Events, "media_manager:media:toggle_titles", this.toggle_title);

				this.listenTo(this.model, "appearance:update", this.update);

				this.listenTo(this.model, "upload:start", this.upload_start);
				this.listenTo(this.model, "upload:progress", this.upload_progress);
				this.listenTo(this.model, "upload:finish", this.upload_finish);
			},
			render: function () {
				this.$el.empty().append(
					this.template(this.model.toJSON())
				);
				this.update();
				this.toggle_title();
			},
			update: function () {
				if (this.model.get("parent")) this.$el.addClass("has-parent");
				else this.$el.removeClass("has-parent");
				if (this.model.get("selected") && !this.$el.hasClass("selected")) {
					this.$el.addClass("selected");
				}
				else if (!this.model.get("selected")) {
					this.$el.removeClass("selected");
				}
				this.$el.find(".title").text(this.model.get('post_title'));
			},
			toggle_title: function () {
				var state = ActiveFilters.showing_titles,
					$el = this.$el.find(".title")
				;
				if (state && !$el.is(":visible")) $el.show();
				else $el.hide();
			},
			toggle_item_selection: function (e) {
				e.stopPropagation();
				e.preventDefault();
				this.model.set({selected: !this.model.get("selected")});
			},
			upload_start: function (media) {
				/*$(".upfront-media_item-editor").remove();
				var editor = new MediaItem_EditorView({
					model: this.model,
					media: media
				});
				editor.render();
				this.$el.find(".upfront-media_item-editor-container").append(editor.$el);*/
				this.parent_view.$el.scrollTop(0);
				this.$el.find('.thumbnail').append('<div class="upfront-media-progress-bar" />');
			},
			upload_progress: function (progress) {
				//Upfront.Util.log(_.template("{{post.post_title}} progress changed to {{progress}}", {post:this.model.toJSON(), progress:progress}));
				this.$el.find('.upfront-media-progress-bar').css('width', progress+'%');
			},
			upload_finish: function (manager) {
				this.$el.find(".thumbnail .upfront-image-upload-placeholder").replaceWith(this.model.get("thumbnail"));
				this.$el.find('.upfront-media-progress-bar').remove();

				this.model.set({selected: true});
				// adding it on persistent list
				ActiveFilters.current_models.push(this.model);
				// Only do if regular media.
				if (!ActiveFilters.themeImages) {
					// Add as current keys so uploads are selected.
					ActiveFilters.current_keys.push(this.model.attributes.ID);
					// redraw media gallery
					//manager.render_library();
				}
			}
		});

// ----- Editor -----

	var MediaItem_EditorView = Backbone.View.extend({
		className: "upfront-media_item-editor",
		events: {
			"click button": "save"
		},
		initialize: function (data) {
			this.editables = _([
				new MediaItem_EditableTitle({model: this.model})
			]);
			if (data && data.media) this.media = data.media;
		},
		render: function () {
			this.$el.empty();
			var me = this;
			this.editables.each(function (editable) {
				editable.render();
				me.$el.append(editable.$el);
			});

			var labels = new MediaItem_Labels({model: this.model});
			labels.render();
			this.$el.append(labels.$el);

			this.$el.append('<button type="button">' + l10n.ok + '</button>');
			if (!this.media) return true;

			var type = this.media.type || 'application/octet-stream',
				is_image = type.match(/^image/i)
			;
			if (is_image) return true;

			var nag = new MediaItem_UploadNag({model: this.model, media:this.media});
			nag.render();
			this.$el.append(nag.$el);
		},
		save: function () {
			this.editables.invoke("update");
			var me = this,
				data = {
					action: "upfront-media-update_media_item",
					data: this.model.toJSON()
				}
			;
			Upfront.Util.post(data)
				.done(function () {
					//Upfront.Util.log('successfully saved data');
					me.model.trigger("change");
				})
			;
		}
	});

	var MediaItem_Labels = Backbone.View.extend({
		className: "upfront-media_labels",
		events: {
			"click button": "create_label",
			"click a.own_label": "drop_label",
			"click a.all_label": "add_label"
		},
		initialize: function () {
			this.labels = ActiveFilters.get("label");
			Upfront.Events.on("media_manager:media:labels_loaded", this.reset_labels, this);
		},
		reset_labels: function () {
			this.labels = ActiveFilters.get("label");
			this.render();
		},
		render: function () {
			var own_labels_template = _.template('<a class="own_label" href="#" data-idx="{{value}}">{{filter}}</a> '),
				all_labels_template = _.template('<a class="all_label" href="#" data-idx="{{value}}">{{filter}}</a> '),
				me = this,
				model_labels = this.model.get("labels"),
				own_labels = [],
				all_labels = []
			;
			this.$el.empty();
			if (this.labels) this.labels.each(function (item) {
				if (model_labels && model_labels.length && model_labels.indexOf(item.get("value")) >= 0) own_labels.push(own_labels_template(item.toJSON()));
				else all_labels.push(all_labels_template(item.toJSON()));
			});
			me.$el.append(l10n.applied_labels + "&nbsp;");
			_(own_labels).each(function (item) {
				me.$el.append(item);
			});
			this.$el.append(
				'<input type="text" placeholder="label..." />' +
				'<button type="button">' + l10n.add + '</button>'
			);
			me.$el.append("All labels: ");
			_(all_labels).each(function (item) {
				me.$el.append(item);
			});
		},
		create_label: function (e) {
			e.preventDefault();
			e.stopPropagation();

			var label = this.$el.find(":text").val(),
				data = {
					"action": "upfront-media-add_label",
					"term": label,
					"post_id": this.model.get("ID")
				}
			;
			Upfront.Util.post(data)
				.success(function (response) {
					Upfront.Events.trigger("media_manager:media:labels_updated");
				})
			;
		},
		add_label: function (e) {
			e.preventDefault();
			e.stopPropagation();
			this._update_labels(e);
		},
		drop_label: function (e) {
			e.preventDefault();
			e.stopPropagation();
			this._update_labels(e, 'dis');
		},
		_update_labels: function (e, pfx) {
			pfx = pfx || '';
			e.preventDefault();
			e.stopPropagation();
			var me = this,
				$label = $(e.target),
				idx = $label.attr("data-idx"),
				data = {
					action: "upfront-media-" + pfx + "associate_label",
					term: idx,
					post_id: this.model.get("ID")
				}
			;
			Upfront.Util.post(data)
				.success(function (response) {
					var id = me.model.get("ID"),
						data = response.data || {},
						labels = data[id] || data
					;
					me.model.set("labels", labels, {silent: true});
					me.render();
				})
			;
		}
	});

	var MediaItem_UploadNag = Backbone.View.extend({
		className: "upload_type-nag",
		events: {
			"click .keep": "keep_file",
			"click .remove": "remove_file"
		},
		render: function () {
			this.$el.empty()
				.append(l10n.video_recommendation_nag)
				.append('<a href="#" class="button keep">' + l10n.keep_file + '</a>')
				.append('<a href="#" class="button remove">' + l10n.remove_file + '</a>')
			;
		},
		keep_file: function (e) {
			e.preventDefault();
			e.stopPropagation();
		},
		remove_file: function (e) {
			e.preventDefault();
			e.stopPropagation();
			this.model.trigger("upload:abort");
		}
	});

		var MediaItem_EditorEditable = Backbone.View.extend({
			className: "upfront-media_item-editable",
			events:{
				change: "update"
			},
			template: _.template(
				"<label>{{label}}<input type='text' name='{{name}}' value='{{value}}' placeholder='{{placeholder}}' /></label>"
			),
			get_name: function () {},
			get_label: function () {},
			get_placeholder: function () {},
			get_value: function () {
				return this.$el.find('[name="' + this.get_name() + '"]:first').val();
			},
			render: function () {
				var name = this.get_name() || '',
					label = this.get_label() || '',
					placeholder = this.get_placeholder() || '',
					value = this.model.get(this.get_name()) || '',
					data = {
						name:  name,
						label: label,
						placeholder: placeholder,
						value: value
					}
				;
				this.$el.empty().append(
					this.template(data)
				);
			},
			update: function () {
				var obj = {};
				obj[this.get_name()] = this.get_value();
				this.model.set(obj, {silent: true});
			}
		});

		var MediaItem_EditorEmbedableEditable = MediaItem_EditorEditable.extend({
			update: function () {
				var obj = {};
				obj[this.get_name()] = this.get_value();
				this.model.set(obj, {silent: true});
				this.trigger("embed:updated");
			}
		});

		var MediaItem_EmbedableUrl = MediaItem_EditorEmbedableEditable.extend({
			get_name: function () { return "original_url"; },
			get_label: function () { return l10n.media_url; },
			get_placeholder: function () { return "http://sample.com/path-to-image/image.jpg"; }
		});

		var MediaItem_EditableTitle = MediaItem_EditorEditable.extend({
			get_name: function () { return "post_title"; },
			get_label: function () { return l10n.image_title; },
			get_placeholder: function () { return l10n.your_image_title; }
		});

		var MediaItem_EditableLabels = MediaItem_EditorEditable.extend({
			get_label: function () { return l10n.labels; },
			render: function () {
				var collection = new MediaCollection_Selection([this.model]),
					view = new MediaManager_ItemControl_LabelsContainer({model: collection})
				;
				view.render();
				collection.on("change", view.render_labels, view);
				this.$el.empty()
					.append('<label>' + this.get_label() + '</label>')
					.append(view.$el)
				;
			}
		});

// ----- Interface -----

	var ContentEditorUploader = Backbone.View.extend({
		initialize: function (opts) {
			this.options = opts;
		},
		open: function (options) {
			options = _.extend({
				media_type: ["images"],
				multiple_sizes: true,
				multiple_selection: true,
				button_text: l10n.insert_media_file,
				button_text_multiple: l10n.insert_media_files,
				ck_insert: false,
				hold_editor: false
			}, options);

			var me = this,
				popup = false,
				media_type = options.media_type,
				multiple_selection = options.multiple_selection
			;
			ActiveFilters.allowed_media_types = media_type;
			ActiveFilters.multiple_sizes = options.multiple_sizes;

			popup = Upfront.Popup.open(function (data, $top, $bottom) {
				me.out = this;
				me.popup_data = data;
				me.popup_data.$top = $top;
				me.popup_data.$bottom = $bottom;
				me.load(options);
			}, {width: 800, hold_editor: options.hold_editor}, 'media-manager');

			popup.always(_.bind(this.cleanup_active_filters, this));
			popup.progress($.proxy(this.clean_up, this));

			Upfront.Events.trigger('upfront:element:edit:start', 'media-upload');

			return popup;
		},
		/**
		 * Ensure everything is off when popup is closed.
		 */
		clean_up: function(flag) {
			if (flag === 'before_close') {
				Upfront.Events.trigger('upfront:element:edit:stop', 'media-upload');
			}
		},
		cleanup_active_filters: function () {
			ActiveFilters.allowed_media_types = [];
			ActiveFilters.current_keys = [];
			ActiveFilters.current_models = [];
			this.cleanup_manager_view();
		},
		cleanup_manager_view: function () {
			if (this.media_manager) {
				this.media_manager.undelegateEvents();
				this.media_manager.remove();
				this.media_manage_options = undefined;
			}
		},
		load: function (options) {

			this.cleanup_manager_view();

			if (_.isUndefined(this.media_manage_options)) {
				this.media_manage_options = _.extend({
					el: this.out,
					data: this.popup_data
				}, options);
				this.media_manager = new MediaManager_View(this.media_manage_options);
			} else if(!_.isEqual(this.media_manage_options,  _.extend({ el: this.out, data: this.popup_data }, options))) {
				this.media_manage_options = _.extend({
					el: this.out,
					data: this.popup_data
				}, options);
				this.media_manager = new MediaManager_View(this.media_manage_options);
			}

			this.media_manager.render();
			return false;
		},
		results_html: function (result) {
			var html = '';
			if (result && result.each) result.each(function (item) {
				var data = item.toJSON(),
					selected_size = item.get("selected_size") || MEDIA_SIZES.FULL,
					all_sizes = item.get("additional_sizes")
				;
				if (selected_size && MEDIA_SIZES.FULL != selected_size) {
					_(all_sizes).each(function (size) {
						if (MEDIA_SIZES.to_size(size) != selected_size) return true;
						data.image = size;
					});
				}
				if ( result.type == 'gallery' )
					data.link = {
						href: '#',
						"class": 'popup'
					};
				data.type = result.type;
				html += _.template( (data.link ? Upfront.Media.Templates.image_link : Upfront.Media.Templates.image), data);
			});
			if (result && result.length && result.length > 1) {
				if ( result.type == 'plain' )
					html = _.template(
						Upfront.Media.Templates.multiple,
						{content: html}
					);
				else if ( result.type == 'gallery' )
					html = _.template(
						Upfront.Media.Templates.gallery,
						{content: html}
					);
				else if ( result.type == 'slider' )
					html = _.template(
						Upfront.Media.Templates.slider,
						{content: html}
					);
			}
			return html;
		}
	});

Upfront.Media = {
	Manager: new ContentEditorUploader(),
	Templates: {
		image: '<p class="upfront-inserted_image-wrapper upfront-inserted_image-{{type}}"><img src="{{image.src}}" title="{{post_title}}" alt="{{post_title}}" height="{{image.height}}" width="{{image.width}}" /></p>',
		image_link: '<p class="upfront-inserted_image-wrapper upfront-inserted_image-{{type}}"><a href="{{link.href}}" class="{{link.class}}"><img src="{{image.src}}" title="{{post_title}}" alt="{{post_title}}" height="{{image.height}}" width="{{image.width}}" /></a></p>',
		embeddable: '<div>{{post_content}}<br />{{post_title}}</div>',
		gallery: '[upfront-gallery]{{content}}[/upfront-gallery]',
		slider: '[upfront-slider]{{content}}[/upfront-slider]',
		multiple: '{{content}}'
	},
	Transformations: {
		_transformations: _([]),
		add: function (f) {
			this._transformations.push(f);
		},
		apply: function (content) {
			this._transformations.each(function (t) {
				content = t.apply(this, [content]);
			});
			return content;
		}
	},
	Ref: (window._upfront_media_upload || {image_ref: ''}).image_ref
};

});
})(jQuery);
