define([
	'scripts/upfront/settings/modules/base-module'
], function(BaseModule) {
	var l10n = Upfront.Settings.l10n.preset_manager;
	var ColorsSettingsModule = BaseModule.extend({
		className: 'settings_module colors_settings_item clearfix',
		group: true,

		get_title: function() {
			return this.options.title;
		},

		initialize: function(options) {
			this.options = options || {};
			this.fieldCounter = 0;
			var me = this,
				state = this.options.state,
				per_row = 'single',
				toggleClass = 'no-toggle',
				fields = [];

			if(this.options.toggle === true) {
				this.fieldCounter++;
			}

			if(this.options.single !== true) {
				per_row = 'two';
			}
			_.each(this.options.abccolors, function(color) {
				if(me.options.toggle === true) {
					toggleClass = 'element-toggled';
				}
				var colorField = new Upfront.Views.Editor.Field.Color({
					className: state + '-color-field upfront-field-wrap-color color-module module-color-field ' + toggleClass + ' ' + per_row,
					blank_alpha : 0,
					model: me.model,
					name: color.name,
					default_value: me.model.get(color.name),
					label_style: 'inline',
					label_position: me.options.label_position || 'right',
					label: color.label,
					spectrum: {
						preferredFormat: 'hex',
						change: function(value) {
							if (!value) return false;
							var c = value.get_is_theme_color() !== false ? value.theme_color : value.toRgbString();
							me.model.set(color.name, c);
						},
						move: function(value) {
							if (!value) return false;
							var c = value.get_is_theme_color() !== false ? value.theme_color : value.toRgbString();
							me.model.set(color.name, c);
						}
					}
				});
				fields.push(colorField);
			});

			this.fields = _(fields);

			//Add toggle colors checkbox
			if(this.options.toggle === true) {
				this.group = false;
				this.fields.unshift(
					new Upfront.Views.Editor.Field.Toggle({
						model: this.model,
						className: 'useColors checkbox-title upfront-toggle-field',
						name: me.options.fields.use,
						label: '',
						multiple: false,
						values: [
							{
								label: l10n.color,
								value: 'yes',
								checked: this.model.get(me.options.fields.use)
							}
						],
						change: function(value) {
							me.model.set(me.options.fields.use, value);
							me.reset_fields(value);
						},
						show: function(value, $el) {
							var stateSettings = $el.closest('.upfront-settings-item-content');
							//Toggle color fields
							if(value == "yes") {
								stateSettings.find('.' + state + '-toggle-wrapper').show();
							} else {
								stateSettings.find('.' + state + '-toggle-wrapper').hide();
							}
						}
					})
				);
			}
		},

		reset_fields: function(value) {
			var me = this;
			if(typeof value !== "undefined" && value === "yes") {
				_.each(this.options.abccolors, function(color) {
					var settings = me.get_static_field_value(color, me.options.prepend);
					me.update_field(color, settings);
					me.save_static_value(color, settings);
					this.fieldCounter++;
				});

				this.$el.empty();
				this.render();
			}
		},

		save_static_value: function(color, settings) {
			//Save preset values from static state
			this.model.set(color.name, settings.color);
		},

		get_static_field_value: function(color, prepend) {
			var settings = {},
				prefix = '';

			if(typeof this.options.prefix !== "undefined") {
				prefix = this.options.prefix + '-';
			}

			settings.color = this.model.get(this.clear_prepend(prefix + color.name, prepend)) || '';

			return settings;
		},

		clear_prepend: function(field, prepend) {
			return field.replace(prepend, '');
		},

		update_field: function(color, settings) {
			//Update selected element
			this.fields._wrapped[this.fieldCounter].set_value(settings.color);
			this.fields._wrapped[this.fieldCounter].update_input_border_color(settings.color);
		},
		render: function() {
			var me = this;
			this.constructor.__super__.render.call(this);
			this.fields.each( function (field) {
				if(typeof field.spectrumOptions !== "undefined") {
					var color = me.model.get(field.name);
					field.set_value(color);
					field.update_input_border_color(Upfront.Util.colors.to_color_value(color));
				}
			});
		}
	});

	return ColorsSettingsModule;
});
