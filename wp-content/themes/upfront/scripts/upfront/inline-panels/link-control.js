(function ($) {
define([
	'scripts/upfront/inline-panels/control',
	'text!scripts/upfront/inline-panels/templates/link-control-template.html'
], function (Control, panelControlTemplate) {
	var l10n = Upfront.mainData.l10n.image_element;

	var LinkControl = Control.extend({
		multiControl: true,
		hideOnClick: true,

		events: {
			'click': 'onClickControl',
			'click .upfront-apply': 'save',
			'click .upfront-link-back': 'onClickControl'
		},
		
		initialize: function(options) {
			var me = this;
			this.options = options || {};

			// Allow only one control to be open at a time
			this.listenTo(Upfront.Events, 'dialog-control:open', function(dialogControl) {
				if (me === dialogControl) {
					return;
				}

				me.close();
			});
		},
		
		save: function() {
			// If lightbox type do not close linkpanel 
			if(typeof this.options.model !== "undefined" && this.options.model.get('type') === 'lightbox' && this.$el.find('.js-ulinkpanel-lightbox-input').val() !== '') return;
			
			this.close();
		},

		render: function(){
			Control.prototype.render.call(this, arguments);
			var me = this,
				panel;

			if(!this.$el.hasClass('link-control-panel-item')) {
				this.$el.addClass('link-control-panel-item');
			}

			if(this.options.firstLevel === true && !this.$el.hasClass('link-control-panel-first-level')) {
				this.$el.addClass('link-control-panel-first-level');
			}

			if(this.view){
				this.view.render();
				this.view.delegateEvents();
			}

			if(!this.panel){
				//this is like initialize
				panel = $(_.template(panelControlTemplate, {l10n: l10n.template, hideOkButton: this.hideOkButton}));
				panel.addClass('inline-panel-control-dialog');
				panel.addClass('inline-panel-control-dialog-' + this.id);
				this.$el.append(panel);
				panel.find('.link-control-panel-content').html('').append(this.view.$el);
				this.panel = panel;
				$(document).on('click.dialog-control.'+me.cid, me, me.onDocumentClick);
			}
			
			// Prepend arrow, it is not set like pseudo element because we cant update its styles with jQuery
			var panelArrow = '<span class="upfront-control-arrow"></span>';
			this.$el
				.find('.link-control-panel').prepend(panelArrow);

			return this;
		},

		remove: function() {
			$(document).off('click.dialog-control.'+this.cid);
		},

		onDocumentClick: function(e) {
			var	target = $(e.target),
				me = e.data;

			if(target.closest('#page').length && target[0] !== me.el && !target.closest(me.el).length && me.isopen) {
				me.close();
			}
		},

		onClickControl: function(e){
			
			this.$el.siblings('.upfront-control-dialog-open').removeClass('upfront-control-dialog-open');

			if((!$(e.target).hasClass('upfront-link-back') && !$(e.target).closest('.upfront-icon').length) || $(e.target).closest('upfront-icon-media-label-delete').length) {
				e.stopPropagation();
				return;
			}

			e.preventDefault();

			this.clicked(e);

			this.$el.siblings('.upfront-control-dialog-open').removeClass('upfront-control-dialog-open');

			if(this.isopen) {
				this.close();
			} else {
				this.open();
			}
		},

		onClickOk: function(e){
			e.preventDefault();
			this.trigger('panel:ok', this.view);
		},

		bindEvents: function(){
			this.panel.find('button').on('click', function(){
			});
		},

		open: function() {
			this.isopen = true;
			this.$el.addClass('upfront-control-dialog-open');
			this.trigger('panel:open');
			Upfront.Events.trigger('dialog-control:open', this);
			
			// Set position of padding container
			this.update_position();
			
			var parent = this.$el.closest('.image-sub-control');
			parent.removeClass('upfront-panels-shadow');
			
			this.updateWrapperSize();

			// add class if last region to allocate clearance for link panel so will not get cut
			if ( this.$el.is('#link') ) {
				var $region = this.$el.closest('.upfront-region-container'),
					$lastRegion = $('.upfront-region-container').not('.upfront-region-container-shadow').last()
				;
				if ( $lastRegion.get(0) == $region.get(0) ) $region.addClass('upfront-last-region-padding');
			}

			return this;
		},
		close: function() {			
			if ( !this.isopen ) return this; // Not opened, don't need to trigger close
			
			this.isopen = false;
			this.$el.removeClass('upfront-control-dialog-open');
			this.trigger('panel:close');

			// remove class that was previously added on last region
			this.$el.closest('.upfront-region-container').removeClass('upfront-last-region-padding');
			
			var parent = this.$el.closest('.image-sub-control');
			
			if(!parent.hasClass('upfront-panels-shadow')) {
				parent.addClass('upfront-panels-shadow');
			}
			
			return this;
		},
		update_position: function() {
			// Get number of elements before padding
			var elementsNumber = this.$el.prevAll().length - 1,
				leftPosition = elementsNumber * 28,
				dir = Upfront.Util.isRTL() ? "right" : "left";

			// Set container position
			this.$el.find('.link-control-panel-content').css(dir, -leftPosition);
			
			this.$el.find('.ulinkpanel-dark').css('minWidth', this.$el.parent().width());
			
			// Update arrow position under padding button
			this.$el.find('.upfront-control-arrow').css(dir, -leftPosition);
		},
		updateWrapperSize: function() {
			var totalWidth = 0;

			this.$el.find('.ulinkpanel-dark').children().each(function(i, element) {
				var elementWidth = $(element).hasClass('upfront-settings-link-target') ? 0 : parseInt($(element).width());
				totalWidth = totalWidth + elementWidth;
			});

			this.$el.find('.ulinkpanel-dark').css('width', totalWidth + 10);
		}
	});

	return LinkControl;
});
})(jQuery);
