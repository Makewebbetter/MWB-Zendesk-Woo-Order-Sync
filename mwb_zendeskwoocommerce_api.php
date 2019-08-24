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
 *
 * @package    zndskwoo
 * @subpackage zndskwoo/
 */
include_once MWB_ZENDESK_DIR.'/Library/class-mwb-zendesk-woo-manager.php';

class MWB_ZENDESK_connect_api{
	
	private static $_instance;
	/**
	 * Initialize the class and set its object.
	 *
	 * @since    1.0.0
	 */
	public static function getInstance() {
		
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
	
		$this->mwb_zendeskconnect_Manager = MWB_ZENDESK_manager::getInstance();
	}
	/**
	 * Init steps for initializing data.
	 *
	 * @since    1.0.0
	 */
	function mwb_zndsk_init_steps() {
		
		update_option('mwb_zndsk_endpoint', "zndskwoo");
		update_option('mwb_zndsk_mobiapi', "zndskwoo");
		
	}
	/**
	 * Registering routes.
	 *
	 * @since    1.0.0
	 */
	function mwb_zndsk_register_routes(){
		
		$is_zndsk_enabled = get_option("mwb_zndsk_settings")['general'];
		
		$this->mwb_zendeskconnect_Manager->mwb_zndsk_register_routes();
	} 
}