(function($){
		var l10n = Upfront.Settings && Upfront.Settings.l10n
						? Upfront.Settings.l10n.global.views
						: Upfront.mainData.l10n.global.views
				;
		define([
				'scripts/upfront/upfront-views-editor/commands/command',
				'scripts/upfront/upfront-views-editor/content-editor'
		], function (Command, ContentEditor) {

				return ContentEditor.SidebarCommand.extend({
						tagName: 'li',
						className: 'command-popup-list',
						$popup: {},
						views: {},
						currentPanel: false,
						render: function () {
								this.$el.addClass("upfront-entity_list upfront-icon upfront-icon-browse");
								// Comment out comments functionality for now.
								//if ( Upfront.Application.is_single( "post" ) )
										//this.$el.html('<a title="'+ l10n.posts_pages_comments +'">' + l10n.posts_pages_comments + '</a>');
								//else
										this.$el.html('<a title="'+ l10n.posts_pages +'">' + l10n.posts_pages + '</a>');
						},
						on_click: function () {
								var me = this,
									postTypes = '',
									popup = Upfront.Popup.open(function (data, $top, $bottom) {
											var $me = $(this);
											$me.empty()
													.append('<p class="upfront-popup-placeholder">' + l10n.popup_preloader + '</p>')
											;
											me.$popup = {
													"top": $top,
													"content": $me,
													"bottom": $bottom
											};
									})
								;
								// Add class for styling purposes.
								me.$popup.top.parent().addClass('upfront-popup-posts');
								var has_comments = false,
										current_post_id = Upfront.data.currentPost && Upfront.data.currentPost.id
												? Upfront.data.currentPost.id
												: _upfront_post_data.post_id
										;
								has_comments = !!current_post_id;
								if (current_post_id && Upfront.data.posts && Upfront.data.posts.length) {
										has_comments = Upfront.data.posts[current_post_id] && Upfront.data.posts[current_post_id].get
												? !!(parseInt(Upfront.data.posts[current_post_id].get("comment_count"), 10))
												: false
										;
								}

								if(typeof Upfront.mainData.content_settings.post_types !== "undefined") {
									if(Upfront.mainData.content_settings.post_types.length > 3) {
										postTypes = '<li data-type="cpts">' + l10n.cpts + '</li>';
									}
								}

								me.$popup.top.html(
										'<ul class="upfront-tabs">' +
											'<li data-type="posts" class="active">' + l10n.posts + '</li>' +
											'<li data-type="pages">' + l10n.pages + '</li>' +
											// If Custom Post Type, display tab.
											postTypes +
											// Comment out comments functionality for now.
											//(has_comments ? '<li data-type="comments">' + l10n.comments + '</li>' : '') +
										'</ul>' +
										me.$popup.top.html()
									+ '<div class="upfront-icon upfront-icon-popup-search"></div>'
								).find('.upfront-tabs li').on("click", function () {
										me.dispatch_panel_creation(this);
								} );

								// Add Toggle Filter button functionality.
								me.$popup.top.find('.upfront-icon-popup-search').click(me.toggle_filter);

								me.dispatch_panel_creation();

								popup.done(function () {
										Upfront.Events.off("upfront:posts:sort");
										Upfront.Events.off("upfront:posts:post:expand");
										Upfront.Events.off("upfront:pages:sort");
										Upfront.Events.off("upfront:comments:sort");
								});
						},
						// Hide/Show filtering section.
						toggle_filter: function(e) {
							var filterSection = $(e.target).parents('.upfront-popup-posts').find('#upfront-entity_list-search');
							var contentSection = filterSection.parent();
							if (filterSection.css('display') === 'none') {
								// Keep button darker when clicked.
								$(e.target).addClass('upfront-popup-icon-search-open');
								// Add class for correct height.
								contentSection.addClass('upfront-filter-panel-open');
								return filterSection.slideToggle('fast');
							}
							// Keep button lighter when clicked.
							$(e.target).removeClass('upfront-popup-icon-search-open');
							// Remove class for correct height.
							contentSection.removeClass('upfront-filter-panel-open');
							return filterSection.slideToggle('fast');
						},
	
						// Close filter panel and button styling.
						hide_filter_panel: function() {
							var filterSection = $('#upfront-entity_list-search');
							var contentSection = filterSection.parent();
							// Adjust popup height.
							contentSection.removeClass('upfront-filter-panel-open');
							// Reset Filter panel toggle button.
							$('.upfront-popup-icon-search-open').removeClass('upfront-popup-icon-search-open');
							// Hide filter panel.
							filterSection.hide();
							// Hide search results header.
							$('.upfront-popup-content-search-ran').removeClass('upfront-popup-content-search-ran');
						},

						dispatch_panel_creation: function (data) {
								var me = this,
										$el = data ? $(data) : me.$popup.top.find('.upfront-tabs li.active'),
										panel = $el.attr("data-type"),
										class_suffix = panel.charAt(0).toUpperCase() + panel.slice(1).toLowerCase(),
										send_data = data || {},
										collection = false,
										postId = this.post.id,
										fetchOptions = {}
										;

								me.$popup.top.find('.upfront-tabs li').removeClass('active');
								$el.addClass('active');

								// Reset filter panel on panel change.
								this.hide_filter_panel();

								this.currentPanel = panel;

								//Already loaded?
								if(me.views[panel]){
										if(panel != 'pages' && panel != 'posts') {
												if(panel != 'comments' || (Upfront.data.currentPost && Upfront.data.currentPost.id && me.views[panel].view.collection.postId == Upfront.data.currentPost.id))
														return this.render_panel(me.views[panel]);
										}
								}

								if(panel == 'posts'){
										collection = new Upfront.Collections.PostList([], {postType: 'post'});
										collection.orderby = 'post_date';
										fetchOptions = {filterContent: true, withAuthor: true, limit: 25, post_status: 'any'};
								}
								else if(panel == 'pages'){
										collection = new Upfront.Collections.PostList([], {postType: 'page'});
										fetchOptions = {limit: 25, hierarchical: true};
								}
								else if(panel == 'cpts'){
										var postTypes = Upfront.mainData.content_settings.post_types
											collection
										;
										var postTypeNames= [];
										postTypes.map(function(postType) {
											var name = postType.name;
											// Ignore WP post types.
											if (name === 'attachment' || name === 'post' || name === 'page') return;
											postTypeNames.push(name);
										});
										collection = new Upfront.Collections.PostList([], {postType: postTypeNames});
										collection.orderby = 'post_date';
										fetchOptions = {limit: 25, withThumbnail: true};
								}
								else{
										var post_id = Upfront.data.currentPost && Upfront.data.currentPost.id
														? Upfront.data.currentPost.id
														: _upfront_post_data.post_id
												;
										collection = new Upfront.Collections.CommentList([], {postId: post_id});
										collection.orderby = 'comment_date';
								}

								collection.fetch(fetchOptions).done(function(response){
					var cachedElements, collectionElements;
										switch(panel){
												case "posts":
														//Check if we have rendered the panel once
														cachedElements = null;
														if(typeof me.views[panel] !== "undefined") {
																cachedElements = me.views[panel].view.collection.pagination.totalElements;
														}
														//Check collection total elements
														collectionElements = collection.pagination.totalElements;

														//Compare total items, if same return cached panel
														if(cachedElements == collectionElements) {
																return me.render_panel(me.views[panel]);
														}

														collection.on('reset sort', me.render_panel, me);
														views = {
																view: new ContentEditor.Posts({collection: collection, $popup: me.$popup}),
																search: new ContentEditor.Search({collection: collection, postType: 'post'}),
																pagination: new ContentEditor.Pagination({collection: collection, postType: l10n.posts})
														};
														me.views.posts = views;
														break;
												case "pages":
														//Check if we have rendered the panel once
														cachedElements = null;
														if(typeof me.views[panel] !== "undefined") {
																cachedElements = me.views[panel].view.collection.pagination.totalElements;
														}
														//Check collection total elements
														collectionElements = collection.pagination.totalElements;

														//Compare total items, if same return cached panel
														if(cachedElements == collectionElements) {
																return me.render_panel(me.views[panel]);
														}

														collection.on('reset sort', me.render_panel, me);
														views = {
																view: new ContentEditor.Pages({collection: collection, $popup: me.$popup}),
																search: new ContentEditor.Search({collection: collection, postType: 'page'}),
																pagination: new ContentEditor.Pagination({collection: collection, postType: l10n.pages})
														};
														me.views.pages = views;
														break;
												case "comments":
														collection.on('reset sort', me.render_panel, me);
														views = {
																view: new ContentEditor.Comments({collection: collection, $popup: me.$popup}),
																search: new ContentEditor.Search({collection: collection}),
																pagination: new ContentEditor.Pagination({collection: collection})
														};
														me.views.comments = views;
														break;
												case "cpts":
														//Check if we have rendered the panel once
														cachedElements = null;
														if(typeof me.views[panel] !== "undefined") {
																cachedElements = me.views[panel].view.collection.pagination.totalElements;
														}
														//Check collection total elements
														collectionElements = collection.pagination.totalElements;

														//Compare total items, if same return cached panel
														if(cachedElements == collectionElements) {
																return me.render_panel(me.views[panel]);
														}

														collection.on('reset sort', me.render_panel, me);
														views = {
																view: new ContentEditor.Cpt({collection: collection, $popup: me.$popup}),
																search: new ContentEditor.Search({collection: collection, postType: postTypeNames}),
																pagination: new ContentEditor.Pagination({collection: collection, postType: l10n.cpts})
														};
														me.views.cpts = views;
														break;
										}
										me.render_panel();
								});

								return false;
						},

						render_panel: function(){
								var me = this,
										views = this.views[this.currentPanel];

								// Content
								views.view.render();
								me.$popup.content.html(views.view.$el);
								views.view.setElement(views.view.$el);

								// Search.
								views.search.render();
								me.$popup.content.prepend(views.search.$el);
								views.search.setElement(views.search.$el);

								// Clear Bottom.
								me.$popup.bottom.empty();
								// Pagination.
								if (views.pagination) {
										views.pagination.render();
										me.$popup.bottom.html(views.pagination.$el);
										views.pagination.setElement(views.pagination.$el);
								}
						}
						
				});

		});
}(jQuery));
