!function(e){upfrontrjs.define(["scripts/upfront/cache/storage"],function(t){var n={__whitelisted__:"test",upfront_list_google_fonts:"fonts","upfront-media-get_labels":"media","upfront-media-list_media":"media","upfront-post_data-post-specific":"post","upfront_posts-load":"post","upfront_posts-terms":"post","upfront_posts-data":"post","upfront_posts-list_meta":"post","upfront_post-data-load":"post","upfront-wp-model":"post","this_post-get_thumbnail":"post",upfront_new_menu_from_slug:"menu",upfront_new_load_menu_array:"menu",upfront_get_breakpoints:"responsive",upfront_get_post_image_variants:"image","upfront-media-image_sizes":"image","upfront-media-video_info":"image",upfront_get_theme_color:"theme-color","upfront-login_element-get_markup":"elements",uwidget_load_widget_list:"elements",uwidget_get_widget_markup:"elements",uwidget_get_widget_admin_form:"elements"},i={__trapped__:"test",upfront_new_create_menu:"menu",upfront_new_delete_menu:"menu",upfront_new_update_menu_order:"menu",upfront_new_delete_menu_item:"menu",upfront_new_update_menu_item:"menu",upfront_update_single_menu_item:"menu","upfront-media-add_label":"media","upfront-media-remove_item":"media","upfront-media-remove_theme_item":"media","upfront-media-update_media_item":"media","upfront-media-update_media_item":"media","upfront-media-remove_item":"media","upfront-media-embed":"media","upfront-media-add_label":"media","upfront-media-upload-theme-image":"media","upfront-media-upload":"media","upfront-save-video-info":"media","upfront-media-associate_label":"media","upfront-media-disassociate_label":"media","upfront-edit-publish":"post","upfront-edit-draft":"post","upfront-post-update_slug":"post","upfront-post-update_status":"post","upfront-post-update_password":"post","upfront-wp-model:save_post":"post",upfront_update_breakpoints:"responsive",upfront_update_post_image_variants:"image","upfront-media-image-create-size":"image","upfront-media-save-images":"image",upfront_update_theme_colors:"theme-color",upfront_save_layout:"elements"},o={BUCKET:"request",send:function(t,n){return e.post(Upfront.Settings.ajax_url,t,function(){},n?n:"json")},get_promise:function(e){var t=e.promise();return t.success=t.done,t.error=t.fail,t},get_response:function(n,i){var r=(n||{}).action||!1;if(!r)return o.send(n,i);if(o.is_trapped_action(r))Upfront.Events.trigger("cache:request:action",r);else{var _=!("upfront-wp-model"!==r||!(n||{}).model_action)&&r+":"+n.model_action;o.is_trapped_action(_)&&Upfront.Events.trigger("cache:request:action",_)}if(!o.is_whitelisted_action(r))return o.send(n,i);var a=t.get_valid_key(n),u=o.get_cached(n),s=e.Deferred(),p=this;return p.__waiting=p.__waiting||{},u?(setTimeout(function(){s.resolveWith(this,[u])}),o.get_promise(s)):p.__waiting[a]?o.get_promise(p.__waiting[a]):(s=o.send(n,i),p.__waiting[a]=s,s.done(function(e){o.set_cached(n,e)}),s.always(function(){delete p.__waiting[a]}),s)},get_cached:function(e){var n=(e||{}).action||!1;if(!n||!o.is_whitelisted_action(n))return!1;var i=t.get_valid_key(e);return!!t.has(i,this.get_bucket(n))&&t.get(i,this.get_bucket(n))},set_cached:function(e,n){var i=(e||{}).action||!1;return!(!i||!o.is_whitelisted_action(i))&&t.set(e,n,this.get_bucket(i))},unset_cached:function(e){var n=(e||{}).action||!1;return!(!n||!o.is_whitelisted_action(n))&&t.unset(e,this.get_bucket(n))},purge:function(){var e=!0;return _.each(o.get_buckets(),function(n){t.purge_bucket(n)||(e=!1)}),e},purge_bucket:function(e){return t.purge_bucket(o.get_bucket(e))},is_whitelisted_action:function(e){return _(n).keys().indexOf(e)>=0},is_trapped_action:function(e){return _(i).keys().indexOf(e)>=0},get_shard:function(e){return n[e]||i[e]},get_shards:function(){return _.uniq(_(n).values())},get_bucket:function(e){var t=this.get_shard(e)||"general";return this.get_sharded_bucket(t)},get_sharded_bucket:function(e){return e=e||"general",o.BUCKET+"-"+e},get_buckets:function(){var e=[];return _.each(o.get_shards(),function(t){e.push(o.get_sharded_bucket(t))}),e.push(this.get_sharded_bucket("general")),e},listen:function(){return o.stop_listening(),Upfront.Events.on("cache:request:action",this.purge_bucket,this),!0},stop_listening:function(){return Upfront.Events.off("cache:request:action",this.purge_bucket),!1}};return o})}(jQuery);