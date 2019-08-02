<?php

/**
 * Class for handling Upfront access permissions
 */
class Upfront_Permissions {

	const BOOT = 'boot_upfront';
	const EDIT = 'edit_others_posts';
	const EDIT_OWN = 'edit_posts';
	const EMBED = 'embed_stuff';
	const UPLOAD = 'upload_stuff';
	const MODIFY_ELEMENT_PRESETS = 'modify_element_presets';
	const DELETE_ELEMENT_PRESETS = 'delete_element_presets';
	const SWITCH_ELEMENT_PRESETS = 'switch_element_presets';
	const SAVE = 'save_changes';
	const SAVE_REVISION = 'save_changes';
	const OPTIONS = 'change_options';
	const CREATE_POST_PAGE = 'create_post_page';
	const SEE_USE_DEBUG = 'see_use_debug';
	const MODIFY_RESTRICTIONS = 'modify_restrictions';

	const DEFAULT_LEVEL = 'save_changes';

	const LAYOUT_MODE = 'layout_mode';
	const SINGLEPOST_LAYOUT_MODE = 'singlepost_layout_mode';
	const SINGLEPAGE_LAYOUT_MODE = 'singlepage_layout_mode';
	const HOME_LAYOUT_MODE = 'home_layout_mode';
	const ARCHIVE_LAYOUT_MODE = 'archive_layout_mode';
	const CONTENT_MODE = 'content_mode';
	const THEME_MODE = 'theme_mode';
	const POSTLAYOUT_MODE = 'postlayout_mode';
	const RESPONSIVE_MODE = 'responsive_mode';

	const ANONYMOUS = '::anonymous::';
	const NOT_LOAD_WP_ROLES = 'not_load_wp_default_roles'; // only load wp default roles on fresh setup
	const RESTRICTIONS_KEY = "upfront-options-user_restrictions";

	/**
	 * Internal levels map
	 *
	 * @var array
	 */
	private $_levels_map = array();

	/**
	 * Internal restrictions cache
	 *
	 * @var array
	 */
	private $_cached_restrictions = array();

	/**
	 * Instance of Upfront_Permissions class
	 *
	 * @var Upfront_Permissions
	 */
	private static $_me;


	/**
	 * Checks if current user is able to do $level
	 *
	 * @param string $level Upfront level to check
	 * @param mixed $arg Optional additional argument
	 *
	 * @return bool
	 */
	public static function current ($level, $arg=false) {
		self::boot();
		return self::$_me->_current_user_can($level, $arg);
	}

	/**
	 * Checks if current user able to modify layout
	 *
	 * @param mixed $arg Optional additional argument
	 *
	 * @return bool
	 */
	public static function can_modify_layout ($arg=false) {
		$layout = Upfront_Layout::get_parsed_cascade();
		$is_archive = ('archive' === $layout['type']);
		$is_home = ($is_archive && !empty($layout['item']) && 'archive-home' === $layout['item']);
		$is_single = ('single' === $layout['type']);
		$is_single_page = ($is_single && !empty($layout['item']) && 'single-page' === $layout['item']);
		if ( !self::current(self::LAYOUT_MODE, $arg) ) return false;
		if ( $is_home && self::current(self::HOME_LAYOUT_MODE, $arg) ) return true;
		if ( !$is_home && $is_archive && self::current(self::ARCHIVE_LAYOUT_MODE, $arg) ) return true;
		if ( $is_single_page && self::current(self::SINGLEPAGE_LAYOUT_MODE, $arg) ) return true;
		if ( !$is_single_page && $is_single && self::current(self::SINGLEPOST_LAYOUT_MODE, $arg) ) return true;
		return false;
	}

	/**
	 * User ability check public access point
	 *
	 * @param int $user_id WP user ID
	 * @param string $level Upfront access level to check
	 *
	 * @return bool
	 */
	public static function user ($user_id, $level) {
		self::boot();
		return self::$_me->_user_can($user_id, $level);
	}

	/**
	 * Role ability check public access point
	 *
	 * @param string $role_id WP role ID
	 * @param string $level Upfront access level to check
	 *
	 * @return bool
	 */
	public static function role ($role_id, $level) {
		self::boot();
		return self::$_me->_role_can($role_id, $level);
	}

	/**
	 * Gets created nonces for all known keys
	 *
	 * @return array
	 */
	public static function nonces () {
		static $nonces = array();
		if (!empty($nonces)) return $nonces;

		$keys = self::_get_nonce_keys();
		foreach ($keys as $key) {
			$nonces[$key] = wp_create_nonce(self::_to_nonce_key($key));
		}
		return $nonces;
	}

	/**
	 * Gets a nonce corresponding to level
	 *
	 * @param string $level Level
	 *
	 * @return mixed Nonce as string or (bool)false
	 */
	public static function nonce ($level) {
		$nonces = self::nonces();
		if (!self::current($level) && !empty($nonces[self::ANONYMOUS])) return $nonces[self::ANONYMOUS];

		if (!empty($nonces[$level])) return $nonces[$level];

		return !empty($nonces[self::ANONYMOUS])
			? $nonces[self::ANONYMOUS]
			: false
		;
	}

	/**
	 * Checkes if value is an upfront permission nonce
	 *
	 * @param string $level Level
	 * @param string $value Value
	 *
	 * @return bool
	 */
	public static function is_nonce ($level, $value) {
		$keys = self::_get_nonce_keys();
		if (!in_array($level, $keys)) return false;

		$result = wp_verify_nonce($value, self::_to_nonce_key($level));
		return (bool)$result;
	}

	/**
	 * Spawns an instance
	 *
	 * Never to the outside world.
	 */
	private function __construct () {
		$this->_levels_map = $this->get_restrictions();
	}

	/**
	 * Sets default access levels
	 *
	 * @return array
	 */
	private function _get_default_levels_map () {
		return apply_filters('upfront-access-permissions-map', array(
			self::BOOT => 'edit_theme_options',

			self::OPTIONS => 'manage_options',

			self::CREATE_POST_PAGE => 'edit_posts',
			self::EDIT_OWN => 'edit_theme_options',
			self::EDIT => 'edit_theme_options',

			self::UPLOAD => 'upload_files',
			self::EMBED => 'edit_theme_options',

			self::LAYOUT_MODE => 'edit_theme_options',
			self::RESPONSIVE_MODE => 'edit_theme_options',

			self::SWITCH_ELEMENT_PRESETS => 'edit_theme_options',
			self::MODIFY_ELEMENT_PRESETS => 'edit_theme_options',
			self::DELETE_ELEMENT_PRESETS => 'edit_theme_options',

			self::SINGLEPOST_LAYOUT_MODE => 'edit_theme_options',
			self::SINGLEPAGE_LAYOUT_MODE => 'edit_theme_options',
			self::HOME_LAYOUT_MODE => 'edit_theme_options',
			self::ARCHIVE_LAYOUT_MODE => 'edit_theme_options',

			self::MODIFY_RESTRICTIONS => 'promote_users',
			self::SEE_USE_DEBUG => "edit_themes",

			self::SAVE => 'edit_theme_options',
			self::CONTENT_MODE => 'edit_theme_options',
			self::THEME_MODE => 'edit_theme_options',
			self::DEFAULT_LEVEL => 'edit_theme_options',
		));
	}

	/**
	 * Returns upfront capability map
	 *
	 * @return array
	 */
	function get_upfront_capability_map () {
		$levels = $this->_get_default_levels_map();
		if (isset($levels[self::DEFAULT_LEVEL])) unset($levels[self::DEFAULT_LEVEL]);
		if (isset($levels[self::CONTENT_MODE])) unset($levels[self::CONTENT_MODE]);
		if (isset($levels[self::THEME_MODE])) unset($levels[self::THEME_MODE]);
		return $levels;
	}

	/**
	 * Initiates class and returns an instance
	 *
	 * @return Upfront_Permissions
	 */
	public static function boot () {
		if (!empty(self::$_me)) return self::$_me;
		self::$_me = new self;
		return self::$_me;
	}

	/**
	 * Sets stored userland restrictions
	 *
	 * @param array $restrictions Access levels map to store
	 *
	 * @return bool
	 */
	public function set_restrictions ($restrictions) {
		if (!$this->_current_user_can(self::MODIFY_RESTRICTIONS)) return false;

		// putting flag to avoid loading default WP roles and to avoid empty return from DB
		// loading default WP roles only happens on first load at fresh setup
		$restrictions = wp_parse_args(
			$restrictions,
			array(self::NOT_LOAD_WP_ROLES => 'not_load')
		);

		return !!update_site_option(self::RESTRICTIONS_KEY, $restrictions, false);
	}

	/**
	 * Gets stored userland restrictions
	 *
	 * @return array Upfront access levels map
	 */
	public function get_restrictions () {
		$map = get_site_option(self::RESTRICTIONS_KEY, array());
		return !empty($map)
			? wp_parse_args($map, $this->_get_default_levels_map())
			: $this->_get_default_levels_map()
		;
	}

	/**
	 * Returns an array of content-specific restrictions
	 *
	 * These Upfront restrictions need to be additionally checked
	 * against WP capabilities model (particularly, `edit_posts`)
	 *
	 * @return array
	 */
	public function get_content_restrictions () {
		return array(
		    self::EDIT,
		    self::CREATE_POST_PAGE,
		    self::CONTENT_MODE,
		);
	}

	/**
	 * Returns an array of admin-specific restrictions
	 *
	 * These Upfront restrictions need to be additionally checked
	 * against WP capabilities model (particularly, `manage_options`)
	 *
	 * @return array
	 */
	public function get_admin_restrictions () {
		return array(
			self::OPTIONS,
			self::SEE_USE_DEBUG,
			self::MODIFY_RESTRICTIONS,
		);
	}

	/**
	 * Returns an array of upload-specific restrictions
	 *
	 * These Upfront restrictions need to be additionally checked
	 * against WP capabilities model (particularly, `upload_files`)
	 *
	 * @return array
	 */
	public function get_upload_restrictions () {
		return array(
			self::UPLOAD,
			self::EMBED,
		);
	}

	/**
	 * Returns an array of restrictions that require `SAVE` to be allowed
	 *
	 * @return array
	 */
	public function get_saveable_restrictions () {
		return array(
			self::LAYOUT_MODE,
			self::SINGLEPOST_LAYOUT_MODE,
			self::MODIFY_ELEMENT_PRESETS,
			self::DELETE_ELEMENT_PRESETS,
			self::SWITCH_ELEMENT_PRESETS,
			self::CREATE_POST_PAGE,
			self::EDIT,
			self::EDIT_OWN,
			self::EMBED,
			self::RESPONSIVE_MODE,
		);
	}

	/**
	 * Resolves Upfront access level to an actual WordPress capability
	 *
	 * @param string $level Access level to resolve
	 *
	 * @return array WP capabilities/roles array
	 */
	private function _resolve_level_to_capability ($level) {
		$level = in_array($level, array_keys($this->_levels_map)) && !empty($this->_levels_map[$level])
			? $this->_levels_map[$level]
			: $this->_levels_map[self::DEFAULT_LEVEL]
		;
		return is_array($level)
			? $level
			: array($level)
		;
	}

	/**
	 * Checks if current user is able to perform $level
	 *
	 * @param string $_level Level
	 * @param mixed $arg Optional additional agument
	 *
	 * @return bool
	 */
	private function _current_user_can ($_level, $arg=false) {
		if (empty($_level)) return false;
		if ( current_user_can("administrator") && !is_super_admin()  ) return self::role( "administrator", $_level );

		$level = $this->_resolve_level_to_capability($_level);
		if (empty($level)) return false;
		if (
			!is_user_logged_in() &&
			!(defined('UPFRONT_ALLOW_ANONYMOUS_BOOT') && UPFRONT_ALLOW_ANONYMOUS_BOOT)
		) return false;

		// Allow anonymous boot
		if (defined('UPFRONT_ALLOW_ANONYMOUS_BOOT') && UPFRONT_ALLOW_ANONYMOUS_BOOT && array(self::ANONYMOUS) === $level) return true;

		$allowed = false;
		foreach ($level as $lev) {
			$allowed = !empty($arg)
				? current_user_can($lev, $arg)
				: current_user_can($lev)
			;
			if ($allowed) break;
		}

		return (bool)$allowed;
	}

	/**
	 * Checks if an user is able to perform a certain Upfront action
	 *
	 * @param int $user_id User ID
	 * @param string $level Upfront action level to check
	 *
	 * @return bool
	 */
	private function _user_can ($user_id, $level) {
		$level = $this->_resolve_level_to_capability($level);
		if (empty($level)) return false;

		if (empty($user_id)) return defined('UPFRONT_ALLOW_ANONYMOUS_BOOT') && 'UPFRONT_ALLOW_ANONYMOUS_BOOT' && array(self::ANONYMOUS) === $level;

		$allowed = false;
		foreach ($level as $lev) {
			$allowed = (bool)user_can($user_id, $lev);
			if ($allowed) break;
		}

		return (bool)$allowed;
	}

	/**
	 * Checks if a certain user role is able to perform a certain Upfront action
	 *
	 * @param string $role_id WordPress role ID
	 * @param string $level Upfront action level to check
	 *
	 * @return bool
	 */
	private function _role_can ($role_id, $level) {
		if (empty($role_id)) return false;

		$level = $this->_resolve_level_to_capability($level);
		if (empty($level)) return false;

		$role = get_role( $role_id );
		if (!is_object($role)) return false;

		$allowed = false;
		foreach ($level as $lev) {
			if ( $role->name === $lev ) {
				$allowed = true;
			} elseif ( !isset($this->_levels_map[self::NOT_LOAD_WP_ROLES]) ) {
				$allowed = (bool)$role->has_cap('manage_options');
			}

			if ($allowed) break;
		}

		return (bool)$allowed;
	}

	/**
	 * Returns nonce keys
	 *
	 * @return array
	 */
	private static function _get_nonce_keys () {
		return array(
			self::BOOT,
			self::EDIT,
			self::EMBED,
			self::UPLOAD,
			self::SAVE,

			self::ANONYMOUS,
		);
	}

	/**
	 * Converts $key to upfront permission nonce
	 *
	 * @param string $key Key
	 *
	 * @return string
	 */
	private static function _to_nonce_key ($key) {
		return "upfront-{$key}";
	}

	/**
	 * Returns upfront capability labels
	 *
	 * @return mixed|void
	 */
	public function get_capability_labels () {

		return apply_filters('upfront-access-permissions-labels', array(

			self::BOOT => __('Can Access Upfront Editor Mode', Upfront::TextDomain ),
			self::LAYOUT_MODE => __('Can Modify Upfront Layouts', Upfront::TextDomain ),
			self::SINGLEPOST_LAYOUT_MODE => __('Can Modify Single Post Layout', Upfront::TextDomain ),
			self::SINGLEPAGE_LAYOUT_MODE => __('Can Modify Page Layout', Upfront::TextDomain ),
			self::HOME_LAYOUT_MODE => __('Can Modify Homepage Layout', Upfront::TextDomain ),
			self::ARCHIVE_LAYOUT_MODE => __('Can Modify Archive Layout', Upfront::TextDomain ),
			self::UPLOAD => __('Can Upload Media', Upfront::TextDomain ),
			self::MODIFY_ELEMENT_PRESETS => __('Can Create / Modify Element Presets', Upfront::TextDomain ),
			self::DELETE_ELEMENT_PRESETS => __('Can Delete Element Presets', Upfront::TextDomain ),
			self::SWITCH_ELEMENT_PRESETS => __('Can Switch Between Element Presets', Upfront::TextDomain ),
			self::OPTIONS => __('Can Modify / Save Global Options <p class="description">(Theme Colors, Comments etc.)</p>', Upfront::TextDomain ),
			self::CREATE_POST_PAGE => __('Can Create Posts & Pages From Upfront', Upfront::TextDomain ),
			self::EDIT => __('Can Edit Anyone\'s Existing Posts & Pages', Upfront::TextDomain ),
			self::EDIT_OWN => __('Can Edit Own Existing Posts & Pages', Upfront::TextDomain ),
			self::EMBED => __('Can Use Embeds (Code El, Media Embeds)', Upfront::TextDomain ),
			self::RESPONSIVE_MODE => __('Can Enter & Modify Layouts in Responsive Mode', Upfront::TextDomain ),
			self::MODIFY_RESTRICTIONS => __('Can Modify User Restrictions', Upfront::TextDomain ),
			self::SEE_USE_DEBUG => __('Can See / Use Debug Controls', Upfront::TextDomain ),
		));
	}

	/**
	 * Returns levels map
	 *
	 * @return array
	 */
	public function get_level_map() {
		return $this->_levels_map;
	}

}