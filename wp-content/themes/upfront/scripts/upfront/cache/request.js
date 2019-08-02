;(function ($) {
define([
	'scripts/upfront/cache/storage'
], function (Storage) {

	/**
	 * Hash of cacheable AJAX actions
	 * and their corresponding bucket shards
	 *
	 * Used for request queueing and caching
	 *
	 * @var {Object}
	 */
	var WHITELISTED_ACTIONS = {
		'__whitelisted__': 'test',
		'upfront_list_google_fonts': 'fonts',

		'upfront-media-get_labels': 'media',
		'upfront-media-list_media': 'media',

		'upfront-post_data-post-specific': 'post',
		'upfront_posts-load': 'post',
		'upfront_posts-terms': 'post',
		'upfront_posts-data': 'post',
		'upfront_posts-list_meta': 'post',
		'upfront_post-data-load': 'post',
		'upfront-wp-model': 'post',
		'this_post-get_thumbnail': 'post',

		'upfront_new_menu_from_slug': 'menu',
		'upfront_new_load_menu_array': 'menu',

		'upfront_get_breakpoints': 'responsive',

		'upfront_get_post_image_variants': 'image',
		'upfront-media-image_sizes': 'image',
		'upfront-media-video_info': 'image',

		'upfront_get_theme_color': 'theme-color',

		'upfront-login_element-get_markup': 'elements',
		'uwidget_load_widget_list': 'elements',
		'uwidget_get_widget_markup': 'elements',
		'uwidget_get_widget_admin_form': 'elements',
	};

	/**
	 * Hash of actions that will be trapped
	 * and their corresponding bucket shards
	 *
	 * Used for cache purging on certain AJAX actions
	 *
	 * @var {Object}
	 */
	var TRAPPED_ACTIONS = {
		'__trapped__': 'test',

		'upfront_new_create_menu': 'menu',
		'upfront_new_delete_menu': 'menu',
		'upfront_new_update_menu_order': 'menu',
		'upfront_new_delete_menu_item': 'menu',
		'upfront_new_update_menu_item': 'menu',
		'upfront_update_single_menu_item': 'menu',

		'upfront-media-add_label': 'media',
		'upfront-media-remove_item': 'media',
		'upfront-media-remove_theme_item': 'media',
		'upfront-media-update_media_item': 'media',
		'upfront-media-update_media_item': 'media',
		'upfront-media-remove_item': 'media',
		'upfront-media-embed': 'media',
		'upfront-media-add_label': 'media',
		'upfront-media-upload-theme-image': 'media',
		'upfront-media-upload': 'media',
		'upfront-save-video-info': 'media',
		'upfront-media-associate_label': 'media',
		'upfront-media-disassociate_label': 'media',

		'upfront-edit-publish': 'post',
		'upfront-edit-draft': 'post',
		'upfront-post-update_slug': 'post',
		'upfront-post-update_status': 'post',
		'upfront-post-update_password': 'post',
		'upfront-wp-model:save_post': 'post',

		'upfront_update_breakpoints': 'responsive',

		'upfront_update_post_image_variants': 'image',
		'upfront-media-image-create-size': 'image',
		'upfront-media-save-images': 'image',

		'upfront_update_theme_colors': 'theme-color',

		'upfront_save_layout': 'elements',
	};

	var Request = {

		BUCKET: 'request',

		/**
		 * Actually sends the POST reuest
		 *
		 * @param {Object} request AJAX POST request object
		 * @param {String} data_type Optional data type, defaults to 'json'
		 *
		 * @return {Object} Deferred
		 */
		send: function (request, data_type) {
			return $.post(
				Upfront.Settings.ajax_url,
				request,
				function () {},
				data_type ? data_type : "json"
			);
		},

		/**
		 * Gets deferred promise augmented with jQuery legacy methods
		 *
		 * @param {Object} dfr jQuery.Deferred object
		 *
		 * @return {Object} Augmented promise
		 */
		get_promise: function (dfr) {
			var prm = dfr.promise();
			// Shim in legacy jQuery promise handlers
			prm.success = prm.done;
			prm.error = prm.fail;
			return prm;
		},

		/**
		 * Proxies the actual POST request and caches the data
		 *
		 * @param {Object} request AJAX POST request object
		 * @param {String} data_type Optional data type, defaults to 'json'
		 *
		 * @return {Object} Deferred promise
		 */
		get_response: function (request, data_type) {
			var action = (request || {}).action || false;
			if (!action) return Request.send(request, data_type);

			if (Request.is_trapped_action(action)) {
				Upfront.Events.trigger('cache:request:action', action);
			} else {
				// Allow normalized action trapping to catch the wp-model insanity
				var normalized_action = 'upfront-wp-model' === action && (request || {}).model_action
					? action + ':' + request.model_action
					: false
				;
				if (Request.is_trapped_action(normalized_action)) {
					Upfront.Events.trigger('cache:request:action', normalized_action);
				}
			}

			if (!Request.is_whitelisted_action(action)) return Request.send(request, data_type);

			var cache_key = Storage.get_valid_key(request),
				cached = Request.get_cached(request),
				dfr = $.Deferred(),
				me = this
			;
			// Set up request queue, if not already present.
			// This is tp handle multiple requests for the same thing being fired over and over again
			me.__waiting = me.__waiting || {};

			if (cached) {
				setTimeout(function () {
					dfr.resolveWith(this, [cached]);
				});
				return Request.get_promise(dfr);
			}

			if (me.__waiting[cache_key]) {
				// FFS just give it a sec and wait for the current one to finish pl0x.
				// Patience is a virtue, they say.
				return Request.get_promise(me.__waiting[cache_key]);
			}

			dfr = Request.send(request, data_type);

			// Add me to "bear with me" queue
			me.__waiting[cache_key] = dfr;

			dfr.done(function (data) {
				// I'm done, and I wanna be cached
				Request.set_cached(request, data);
			});

			dfr.always(function () {
				// I don't care about my status, and this is all I got.
				// Just drop me from the "bear with me" queue.
				delete me.__waiting[cache_key];
			});

			return dfr;
		},

		/**
		 * Cache getter proxying method
		 *
		 * Used in POST requests, to proxy the result via cache
		 *
		 * @param {Object} payload AJAX POST object
		 *
		 * @return {Object|Boolean} Cached object on success, (bool)false on failure
		 */
		get_cached: function (payload) {
			var action = (payload || {}).action || false;
			if (!action || !Request.is_whitelisted_action(action)) return false;

			var key = Storage.get_valid_key(payload);
			if (!Storage.has(key, this.get_bucket(action))) return false;

			return Storage.get(key, this.get_bucket(action));
		},

		/**
		 * Cache setter proxying method
		 *
		 * Used to set POST request value on success
		 *
		 * @param {Object} payload AJAX POST object
		 * @param {mixeed} Response
		 *
		 * @return {Boolean} Status
		 */
		set_cached: function (payload, value) {
			var action = (payload || {}).action || false;
			if (!action || !Request.is_whitelisted_action(action)) return false;

			return Storage.set(payload, value, this.get_bucket(action));
		},

		/**
		 * Cache unsetter proxying method
		 *
		 * Used to set POST request value on success
		 *
		 * @param {Object} payload AJAX POST object
		 *
		 * @return {Boolean} Status
		 */
		unset_cached: function (payload) {
			var action = (payload || {}).action || false;
			if (!action || !Request.is_whitelisted_action(action)) return false;

			return Storage.unset(payload, this.get_bucket(action));
		},

		/**
		 * Purges whole request cache
		 *
		 * @return {Boolean} Status
		 */
		purge: function () {
			var status = true;
			_.each(Request.get_buckets(), function (bucket) {
				if (!Storage.purge_bucket(bucket)) status = false;
			});
			return status;
		},

		/**
		 * Purges specific bucket shard
		 *
		 * @param {String} action Action to clear
		 *
		 * @return {Boolean} Status
		 */
		purge_bucket: function (action) {
			return Storage.purge_bucket(Request.get_bucket(action));
		},

		/**
		 * Checks if a given action is cacheable
		 *
		 * @param {String} action Action to check
		 *
		 * @return {Boolean}
		 */
		is_whitelisted_action: function (action) {
			return _(WHITELISTED_ACTIONS).keys().indexOf(action) >= 0;
		},

		/**
		 * Checks if a given action is to be trapped
		 *
		 * @param {String} action Action to check
		 *
		 * @return {Boolean}
		 */
		is_trapped_action: function (action) {
			return _(TRAPPED_ACTIONS).keys().indexOf(action) >= 0;
		},

		/**
		 * Gets cache bucket shard
		 *
		 * @param {String} action Action to check
		 *
		 * @return {String|Boolean} Cache bucket shard or (bool)false on failure
		 */
		get_shard: function (action) {
			return WHITELISTED_ACTIONS[action] || TRAPPED_ACTIONS[action];
		},

		/**
		 * Gets all known cache bucket shards
		 *
		 * @return {Array} List of known bucket shards
		 */
		get_shards: function () {
			return _.uniq(_(WHITELISTED_ACTIONS).values());
		},

		/**
		 * Gets sharded bucket name
		 *
		 * @param {String} action Action to get a shard from
		 *
		 * @return {String} Proper bucket name
		 */
		get_bucket: function (action) {
			var shard = this.get_shard(action) || 'general';
			return this.get_sharded_bucket(shard);
		},

		/**
		 * Gets sharded bucket name
		 *
		 * @param {String} shard Shard suffix
		 *
		 * @return {String} Proper bucket name
		 */
		get_sharded_bucket: function (shard) {
			shard = shard || 'general';
			return Request.BUCKET + '-' + shard;
		},

		/**
		 * Gets all known bucket shards
		 *
		 * @return {Array} List of known bucket shards as strings
		 */
		get_buckets: function () {
			var buckets = [];
			_.each(Request.get_shards(), function (shard) {
				buckets.push(Request.get_sharded_bucket(shard));
			});
			buckets.push(this.get_sharded_bucket('general'));
			return buckets;
		},

		/**
		 * Bootstraps automatic cache listening
		 *
		 * @return {Boolean} Status
		 */
		listen: function () {
			Request.stop_listening();

			Upfront.Events.on('cache:request:action', this.purge_bucket, this);

			return true;
		},

		/**
		 * Stops automatic cache listening
		 *
		 * @return {Boolean} Status
		 */
		stop_listening: function () {
			//Storage.purge_bucket(Request.BUCKET);

			Upfront.Events.off('cache:request:action', this.purge_bucket);

			return false;
		}
	};

	return Request;
});
})(jQuery);
