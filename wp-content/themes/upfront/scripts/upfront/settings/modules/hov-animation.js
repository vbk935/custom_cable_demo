/*
* Field names properies
* `duration` - Animation duration
* `easing` - Animation effect
*/
define([
	'scripts/upfront/settings/modules/base-module'
], function(BaseModule) {
	var l10n = Upfront.Settings.l10n.preset_manager;
	var HovAnimationSettingsModule = BaseModule.extend({
		className: 'settings_module hov_animation_settings_item clearfix',
		group: false,

		initialize: function(options) {
			this.options = options || {};
			var me = this,
				state = this.options.state,
				toggleClass = 'no-toggle';

			if(me.options.toggle === true) {
				toggleClass = 'element-toggled';
			}

			this.fields = _([
				new Upfront.Views.Editor.Field.Number({
					model: this.model,
					className: state + '-duration duration field-grid-half ' + toggleClass,
					name: me.options.fields.duration,
					min: 0,
					step: 0.1,
					label: '',
					change: function(value) {
						me.model.set(me.options.fields.duration, value);
					}
				}),
				new Upfront.Views.Editor.Field.Select({
					model: this.model,
					name: me.options.fields.easing,
					step: 0.1,
					values: [
						{ label: 'ease', value: 'ease' },
						{ label: 'linear', value: 'linear' },
						{ label: 'ease-in', value: 'ease-in' },
						{ label: 'ease-out', value: 'ease-out' },
						{ label: 'ease-in-out', value: 'ease-in-out' }
					],
					className: state + '-transition transition field-grid-half field-grid-half-last ' + toggleClass,
					change: function(value) {
						me.model.set(me.options.fields.easing, value);
					}
				})
			]);

			//Add toggle typography checkbox
			if(this.options.toggle === true) {
				this.group = false;
				this.fields.unshift(
					new Upfront.Views.Editor.Field.Toggle({
						model: this.model,
						className: 'useAnimation checkbox-title upfront-toggle-field',
						name: me.options.fields.use,
						label: '',
						default_value: 1,
						multiple: false,
						values: [
							{ label: l10n.animate_hover_changes, value: 'yes' }
						],
						change: function(value) {
							me.model.set(me.options.fields.use, value);
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
		}
	});

	return HovAnimationSettingsModule;
});
