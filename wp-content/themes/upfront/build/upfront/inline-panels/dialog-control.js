!function(t){upfrontrjs.define(["scripts/upfront/inline-panels/control","text!scripts/upfront/inline-panels/templates/panel-control-template.html"],function(n,e){var o=Upfront.mainData.l10n.image_element,i=n.extend({multiControl:!0,hideOnClick:!0,events:{"click .upfront-icon":"onClickControl","click button":"onClickOk",click:"onClick"},initialize:function(t){var n=this;this.options=t||{},this.listenTo(Upfront.Events,"dialog-control:open",function(t){n!==t&&n.close()})},render:function(){n.prototype.render.call(this,arguments);var i,l=this;return this.$el.hasClass("uimage-control-panel-item")||this.$el.addClass("uimage-control-panel-item"),this.view&&(this.view.render(),this.view.delegateEvents()),this.panel||(i=t(_.template(e,{l10n:o.template,hideOkButton:this.hideOkButton})),i.addClass("inline-panel-control-dialog"),i.addClass("inline-panel-control-dialog-"+this.id),this.$el.append(i),i.find(".uimage-control-panel-content").html("").append(this.view.$el),this.panel=i,t(document).on("click.dialog-control."+l.cid,l,l.onDocumentClick)),this},remove:function(){t(document).off("click.dialog-control."+this.cid)},onDocumentClick:function(n){var e=t(n.target),o=n.data;e.closest("#page").length&&e[0]!==o.el&&!e.closest(o.el).length&&o.isopen&&o.close()},onClickControl:function(n){return this.$el.siblings(".upfront-control-dialog-open").removeClass("upfront-control-dialog-open"),!t(n.target).closest(".upfront-icon").length||t(n.target).closest("upfront-icon-media-label-delete").length?void n.stopPropagation():(n.preventDefault(),this.clicked(n),this.$el.siblings(".upfront-control-dialog-open").removeClass("upfront-control-dialog-open"),void(this.isopen?this.close():this.open()))},onClickOk:function(t){t.preventDefault(),this.trigger("panel:ok",this.view)},bindEvents:function(){this.panel.find("button").on("click",function(){})},onClick:function(t){if(!this.hideOnClick)return t.preventDefault(),void t.stopPropagation()},open:function(){if(this.isopen=!0,this.$el.addClass("upfront-control-dialog-open"),this.trigger("panel:open"),Upfront.Events.trigger("dialog-control:open",this),this.$el.is("#link")){var n=this.$el.closest(".upfront-region-container"),e=t(".upfront-region-container").not(".upfront-region-container-shadow").last();e.get(0)==n.get(0)&&n.addClass("upfront-last-region-padding")}return this},close:function(){return this.isopen?(this.isopen=!1,this.$el.removeClass("upfront-control-dialog-open"),this.trigger("panel:close"),this.$el.closest(".upfront-region-container").removeClass("upfront-last-region-padding"),this):this}});return i})}(jQuery);