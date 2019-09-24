<?php
/**
 * Exit if accessed directly
 *
 * @package mwb-zendesk-woo-order-sync
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

require_once MWB_ZENDESK_DIR . '/Library/class-mwb-zendesk-manager.php';
/**
 * The api-specific functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 * @package    mwb-zendesk-woo-order-sync
 * @author     MakeWebBetter <webmaster@makewebbetter.com>
 */
class MWB_ZENDESK_Connect_Api {
	/**
	 * Initialize the class and set its object.
	 *
	 * @since    1.0.0
	 * @var $_instance
	 */
	private static $_instance;
	/**
	 * Initialize the class and set its object.
	 *
	 * @since    1.0.0
	 */
	public static function get_instance() {

		self::$_instance = new self();
		if ( ! self::$_instance instanceof self ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
	/**
	 * Constructor of the class for fetching the endpoint.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->mwb_zendeskconnect_manager = MWB_ZENDESK_Manager::get_instance();
		add_action( 'wp_ajax_mwb_zndsk_suggest_accept', array( $this, 'mwb_zndsk_suggest_accept' ) );
		add_action( 'wp_ajax_mwb_zndsk_suggest_later', array( $this, 'mwb_zndsk_suggest_later' ) );
	}
	/**
	 * Registering routes.
	 *
	 * @since    1.0.0
	 */
	public function mwb_zndsk_register_routes() {

		$this->mwb_zendeskconnect_manager->mwb_zndsk_register_routes();
	}
	/**
	 * Save suggestion in DB
	 *
	 * @since    1.0.0
	 */
	public function mwb_zndsk_suggest_later() {
		check_ajax_referer( 'zndsk_security', 'zndskSecurity' );
		update_option( 'zendesk_suggestions_later', true );
		return true;
	}
	/**
	 * Check status of mail sent and save suggestion in DB
	 *
	 * @since    1.0.0
	 */
	public function mwb_zndsk_suggest_accept() {
		check_ajax_referer( 'zndsk_security', 'zndskSecurity' );
		$status = $this->mwb_zendeskconnect_manager->send_clients_details();
		if ( $status && 'already-sent' !== $status ) {
			update_option( 'zendesk_suggestions_sent', true );
			echo wp_json_encode( 'success' );
		} elseif ( 'already-sent' === $status ) {
			echo wp_json_encode( 'alreadySent' );
		} else {
			update_option( 'zendesk_suggestions_later', true );
			echo wp_json_encode( 'failure' );
		}
		wp_die();
	}
}
