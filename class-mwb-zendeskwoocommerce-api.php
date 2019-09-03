<?php

/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * The api-specific functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 * @package    mwb-zendesk-woo-order-sync
 */

include_once MWB_ZENDESK_DIR.'/Library/class-mwb-zendesk-manager.php';
/**
 * The api-specific functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 * @package    mwb-zendesk-woo-order-sync
 * @author     MakeWebBetter <webmaster@makewebbetter.com>
 */

class MWB_ZENDESK_Connect_Api{
	
	private static $_instance;
	/**
	 * Initialize the class and set its object.
	 *
	 * @since    1.0.0
	 */

	public static function get_instance() {
		
		self::$_instance = new self;
		if( !self::$_instance instanceof self )
			self::$_instance = new self;
	
		return self::$_instance;
	}
	/**
	 * Constructor of the class for fetching the endpoint.
	 *
	 * @since    1.0.0
	 */

	public function __construct(){
	
		$this->mwb_zendeskconnect_manager = MWB_ZENDESK_Manager::get_instance();
	}
	/**
	 * Registering routes.
	 *
	 * @since    1.0.0
	 */

	public function mwb_zndsk_register_routes(){
				
		$this->mwb_zendeskconnect_manager->mwb_zndsk_register_routes();
	} 
}