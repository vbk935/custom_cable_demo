<?php
/**
 * WoopraEvents_Frontend and WoopraEvents_Admin Class for Woopra
 *
 * This class contains all event related code including the API for other plugins to use.
 *
 * @since 1.4.1
 * @package woopra
 * @subpackage events
 */
 
/**
 * Main Woopra Events Class
 * @since 1.4.1
 * @package events
 * @subpackage woopra
 */
class WoopraEvents extends WoopraFrontend {
	
	/**
	 * Woopra's Built in Events.
	 * @since 1.4.1
	 * @var
	 */
	var $default_events;
	
	/**
	 * What are the current event's going on?
	 * @since 1.4.1
	 * @var object
	 */
	var $current_event;
	
	/**
	 * Are there events present?
	 * @var 1.4.3
	 */
	var $present_event;
	
	/**
	 * Events Contructor Class
	 * @since 1.4.1
	 * @return 
	 * @constructor
	 */
	function __construct() {
		Woopra::__construct();
		
		// Register Events!
		$this->register_events();
	}

	/**
	 * Register Events
	 * @since 1.4.1
	 * @return 
	 */
	function register_events() {
		/*
		 * 
		 * These are all standard events that WordPress
		 * has that Woopra built-in it's system.
		 * 
		 * 
		 * VALID FIELDS:
		 * 
		 * name* - The name the Woopra App will see.
		 * label* - What the description of the event in WordPress admin panel
		 * function - If a function is required to get the event data.
		 * object - Depending if the function returns an object, this would be the object name to get.
		 * value - Simple value when processed.
		 * 
		 * action** - The action that this event triggers.
		 * filter** - The filter that this event triggers.
		 * 
		 * setting*** - If the 'action' or 'filter' have duplicities, they must have unique setting names.
		 * 
		 */
		$default_events = array(
			array(
				'name'		=>	__('comments', 'woopra'),
				'label'		=>	__('Show comments as they are posted.', 'woopra'),
				'action'	=>	'comment_post',
			),
			array(
				'name'		=>	__('search', 'woopra'),
				'label'		=>	__('Show users search queries.', 'woopra'),
				'action'	=>	'search_query',
			),
			array(
				'name'		=>	__('signup', 'woopra'),
				'label'		=>	__('Show users sign up.', 'woopra'),
				'action'	=>	'signup',
			)
		);
		
		$this->default_events = $default_events;

		$default_woocommerce_events = array(
			array(
				'name'		=>	__('cart update', 'woopra'),
				'label'		=>	__('Show cart updates.', 'woopra'),
				'action'	=>	'cart',
			),
			array(
				'name'		=>	__('checkout', 'woopra'),
				'label'		=>	__('Show users checkouts.', 'woopra'),
				'action'	=>	'checkout',
			),
			array(
				'name'		=>	__('coupon', 'woopra'),
				'label'		=>	__('Track coupons applied.', 'woopra'),
				'action'	=>	'coupon',
			)
		);

		$this->default_woocommerce_events = $default_woocommerce_events;
	}
	

}

?>
