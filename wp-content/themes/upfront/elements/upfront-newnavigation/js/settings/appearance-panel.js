define([
	'scripts/upfront/preset-settings/util',
	'text!elements/upfront-newnavigation/tpl/preset-style.html'
], function(Util, styleTpl) {
		var l10n = Upfront.Settings.l10n.newnavigation_element;

		var AppearancePanel = {
			mainDataCollection: 'navPresets',
			styleElementPrefix: 'nav-preset',
			ajaxActionSlug: 'nav',
			panelTitle: l10n.settings,
			presetDefaults: Upfront.mainData.presetDefaults.nav,
			styleTpl: styleTpl,
			stateModules: {
				Global: [
					{
						moduleType: 'MenuStyle',
						options: {
							title: l10n.panel.menu_kind_label,
							state: 'global'
						}
					}
				],
				Static: [
					{
						moduleType: 'Typography',
						options: {
							title: l10n.panel.typography_label,
							state: 'static',
							toggle: false,
							fields: {
								typeface: 'static-font-family',
								fontstyle: 'static-font-style',
								weight: 'static-weight',
								style: 'static-style',
								size: 'static-font-size',
								line_height: 'static-line-height',
								color: 'static-font-color'
							}
						}
					},
					{
						moduleType: 'Colors',
						options: {
							title: l10n.panel.colors_label,
							multiple: false,
							single: true,
							abccolors: [
								{
									name: 'static-nav-bg',
									label: l10n.panel.item_background_label
								}
							]
						}
					}
				],
				Hover: [
					{
						moduleType: 'Typography',
						options: {
							title: l10n.panel.typography_label,
							state: 'hover',
							toggle: true,
							prepend: 'hover-',
							prefix: 'static',
							fields: {
								use: 'hover-use-typography',
								typeface: 'hover-font-family',
								fontstyle: 'hover-font-style',
								weight: 'hover-weight',
								style: 'hover-style',
								size: 'hover-font-size',
								line_height: 'hover-line-height',
								color: 'hover-font-color'
							}
						}
					},
					{
						moduleType: 'Colors',
						options: {
							title: l10n.panel.colors_label,
							multiple: false,
							single: true,
							toggle: true,
							prepend: 'hover-',
							prefix: 'static',
							fields: {
								use: 'hover-use-color'
							},
							abccolors: [
								{
									name: 'hover-nav-bg',
									label: l10n.panel.item_background_label
								}
							]
						}
					}
				],
				Focus: [
					{
						moduleType: 'Typography',
						options: {
							title: l10n.panel.typography_label,
							state: 'focus',
							toggle: true,
							prepend: 'focus-',
							prefix: 'static',
							fields: {
								use: 'focus-use-typography',
								typeface: 'focus-font-family',
								fontstyle: 'focus-font-style',
								weight: 'focus-weight',
								style: 'focus-style',
								size: 'focus-font-size',
								line_height: 'focus-line-height',
								color: 'focus-font-color'
							}
						}
					},
					{
						moduleType: 'Colors',
						options: {
							title: l10n.panel.colors_label,
							multiple: false,
							single: true,
							toggle: true,
							prepend: 'focus-',
							prefix: 'static',
							fields: {
								use: 'focus-use-color'
							},
							abccolors: [
								{
									name: 'focus-nav-bg',
									label: l10n.panel.item_background_label
								}
							]
						}
					}
				]
			},

			migrateDefaultStyle: function(styles) {
					//replace image wrapper class
					styles = styles.replace(/(div)?\.upfront-navigation\s/g, '');
					styles = styles.replace(/(div)?\.upfront-object\s/g, '');

					return styles;
			},

			migratePresetProperties: function(newPreset) {
				var props = {};

				this.model.get('properties').each( function(prop) {
					props[prop.get('name')] = prop.get('value');
				});

				if (props.breakpoint) {
					// Convert "burger_menu" and "menu_style" properties to "menu_style" property
					if (props.breakpoint.desktop) {
						if (props.breakpoint.desktop.burger_menu === 'yes') {
							props.breakpoint.desktop.menu_style = 'burger';
						}
						delete props.breakpoint.desktop.burger_menu;
					}
					if (props.breakpoint.tablet) {
						if (props.breakpoint.tablet.burger_menu === 'yes') {
							props.breakpoint.tablet.menu_style = 'burger';
						}
						delete props.breakpoint.tablet.burger_menu;
					}
					if (props.breakpoint.mobile) {
						if (props.breakpoint.mobile.burger_menu === 'yes') {
							props.breakpoint.mobile.menu_style = 'burger';
						}
						delete props.breakpoint.mobile.burger_menu;
					}
				} else {
					props.breakpoint = {
						desktop: {},
						tablet: {},
						mobile: {}
					};
				}

				// Setup breakpoint property for preset
				newPreset.set({'breakpoint': props.breakpoint});
			}

		};

		// Generate presets styles to page
		Util.generatePresetsToPage('nav', styleTpl);

		return AppearancePanel;
});
