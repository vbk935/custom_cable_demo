(function($){
		var l10n = Upfront.Settings && Upfront.Settings.l10n
						? Upfront.Settings.l10n.global.views
						: Upfront.mainData.l10n.global.views
				;
		define([
			"text!upfront/templates/popup.html",
			'scripts/perfect-scrollbar/perfect-scrollbar'
		], function ( popup_tpl, perfectScrollbar ) {


				return Backbone.View.extend({
						className: "upfront-entity_list-posts",
						postListTpl: _.template($(popup_tpl).find('#upfront-post-list-tpl').html()),
						postSingleTpl: _.template($(popup_tpl).find('#upfront-post-single-tpl').html()),
						paginationTpl: _.template($(popup_tpl).find('#upfront-pagination-tpl').html()),
						events: {
								//"click #upfront-list-meta .upfront-list_item-component": "handle_sort_request",
								"click .editaction.edit": "handle_post_edit",
								"click #upfront-list-page-path a.upfront-path-back": "handle_return_to_posts",
								"click .editaction.trash": "trash_confirm",
								"click .upfront-posts-delete-cancel-button": "trash_cancel",
								"click .upfront-posts-delete-button": "trash_post"
						},
						initialize: function(options){
								this.collection.on('change reset', this.render, this);
								this.listenTo(Upfront.Events, 'post:saved', this.post_saved);
						},
						render: function () {
								this.$el.empty().append(
										this.postListTpl({
												posts: this.collection.getPage(this.collection.pagination.currentPage),
												orderby: this.collection.orderby,
												order: this.collection.order,
												canEdit: Upfront.Application.user_can("EDIT"),
												canEditOwn: Upfront.Application.user_can("EDIT_OWN")
										})
								);

								// Add JS Scrollbar.
								perfectScrollbar.withDebounceUpdate(
									// Element.
									this.$el.find('.upfront-scroll-panel')[0],
									// Run First.
									true,
									// Event.
									false,
									// Initialize.
									true
								);

								// Add tooltips to inline edit/trash buttons.
								this.add_tooltips();
						},

						// Add tooltips to inline edit/trash buttons.
						add_tooltips: function() {
								// Add Edit tooltip.
								this.$el.find('.editaction.edit').utooltip({
									fromTitle: false,
									content: Upfront.Settings.l10n.global.content.edit_post,
									panel: 'postEditor'
								});

								// Add trash tooltip.
								this.$el.find('.editaction.trash').utooltip({
									fromTitle: false,
									content: Upfront.Settings.l10n.global.content.trash_post,
									panel: 'postEditor'
								});
						},

						handle_sort_request: function (e) {
								var $option = $(e.target).closest('.upfront-list_item-component'),
										sortby = $option.attr('data-sortby'),
										order = this.collection.order;
								if(sortby){
										if(sortby == this.collection.orderby)
												order = order == 'desc' ? 'asc' : 'desc';
										this.collection.reSort(sortby, order);
								}
						},

						/*
						 handle_post_reveal: function (e) {
						 var me = this,
						 postId = $(e.currentTarget).closest('.upfront-list_item-post').attr('data-post_id');

						 e.preventDefault();

						 me.$('#upfront-list').after(me.postSingleTpl({post: me.collection.get(postId)}));
						 me.expand_post(me.collection.get(postId));
						 },
						 */
						handle_post_edit: function (e) {
								e.preventDefault();
								var postId = $(e.currentTarget).closest('.upfront-list_item-post').attr('data-post_id');
								if(_upfront_post_data) _upfront_post_data.post_id = postId;
								Upfront.Application.navigate('/edit/post/' + postId, {trigger: true});
								Upfront.Events.trigger('click:edit:navigate', postId);
						},
						handle_post_view: function (e) {
								e.preventDefault();
								var postId = $(e.currentTarget).closest('.upfront-list_item-post').attr('data-post_id');
								window.location.href = this.collection.get(postId).get('permalink');
						},
						trash_confirm: function(e) {
							e.preventDefault();
							// Show delete confirmation.
							$(e.target).parents('.upfront-list_item').find('.upfront-delete-confirm').show();
						},
						trash_cancel: function(e) {
							// Hide delete confirmation.
							$(e.target).parents('.upfront-delete-confirm').hide();
						},
						trash_post: function (e) {
								var me = this;
								var postelement = $(e.currentTarget).closest('.upfront-list_item-post.upfront-list_item');
								var postId = postelement.attr('data-post_id');
								// Hide delete confirmation.
								$(e.target).parents('.upfront-delete-confirm').hide();
								// Delete Post.
								this.collection.get(postId).set('post_status', 'trash').save().done(function(){
										me.collection.remove(me.collection.get(postId));

								});
						},
						expand_post: function(post){
								var me = this;
								if(!post.featuredImage){
										this.collection.post({action: 'get_post_extra', postId: post.id, thumbnail: true, thumbnailSize: 'medium'})
												.done(function(response){
														if(response.data.thumbnail && response.data.postId == post.id){
																me.$('#upfront-page_preview-featured_image img').attr('src', response.data.thumbnail[0]).show();
																me.$('.upfront-thumbnailinfo').hide();
																post.featuredImage = response.data.thumbnail[0];
														}
														else{
																me.$('.upfront-thumbnailinfo').text(l10n.no_image);
																me.$('.upfront-page_preview-edit_feature a').html('<i class="icon-plus"></i> ' + l10n.add);
														}

												})
										;
								}
								$("#upfront-list-page").show('slide', { direction: "right"}, 'fast');
								this.$el.find("#upfront-list").hide();
								$("#upfront-page_preview-edit button").one("click", function () {
										//window.location = Upfront.Settings.Content.edit.post + post.id;
										var path = '/edit/post/' + post.id;
										// Respect dev=true
										if (window.location.search.indexOf('dev=true') > -1) path += '?dev=true';
										Upfront.Popup.close();
										if(_upfront_post_data) _upfront_post_data.post_id = post.id;
										Upfront.Application.navigate(path, {trigger: true});
								});

								this.bottomContent = $('#upfront-popup-bottom').html();

								$('#upfront-popup-bottom').html(
										$('<a href="#" id="upfront-back_to_posts">' + l10n.back_to_posts + '</a>').on('click', function(e){
												me.handle_return_to_posts();
										})
								);
						},

						post_saved: function () {
								// We should fetch colletion after post / page update to retrieve any title changes
								this.collection.fetch();
						},

						handle_return_to_posts: function () {
								var me = this;
								this.$el.find("#upfront-list").show('slide', { direction: "left"}, function(){
										me.collection.trigger('reset');
								});
								$("#upfront-list-page").hide();
						}

				});
		});
}(jQuery));
