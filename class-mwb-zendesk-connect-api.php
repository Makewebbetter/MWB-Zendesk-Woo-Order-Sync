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
		add_action( 'wp_ajax_mwb_zndsk_ticket', array( $this, 'mwb_zndsk_ticket' ) );
		add_action( 'wp_ajax_mwb_zndsk_tickt_email', array( $this, 'mwb_zndsk_tickt_email' ) );
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
	 * Save suggestion in DB
	 *
	 * @since    1.0.0
	 */
	public function mwb_zndsk_ticket() {
		$url   = get_site_url();
		$nonce = isset( $_GET['nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : '';
		$check = wp_verify_nonce( $nonce, 'zndsk_ticket' );
		if ( $check ) {
			if ( isset( $_GET['id'] ) ) {
				$all_user_ticket_id = isset( $_GET['id'] ) ? sanitize_text_field( wp_unslash( $_GET['id'] ) ) : '';
				$ticket = MWB_ZENDESK_Manager::mwb_fetch_useremail( $all_user_ticket_id );
				$ticket = json_encode( $ticket );
				require_once  MWB_ZENDESK_DIR_PATH . 'admin-templates/zndsk_all_ticket.php' ;
			}
		}
	}
	/**
	 * Show all email ticket functiomn function
	 *
	 * @return void
	 */
	public function mwb_zndsk_tickt_email() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		$check = wp_verify_nonce( $nonce, 'zndsk_ticket_email' );
		if ( $check ) {
			$all_user_ticket_email = isset( $_POST['email'] ) ? sanitize_text_field( wp_unslash( $_POST['email'] ) ) : '';
			require_once MWB_ZENDESK_DIR . '/Library/class-mwb-zendesk-manager.php';
			$object = new MWB_ZENDESK_Manager();
			$object->mwb_my_account_endpoint_content( $all_user_ticket_email );
			wp_die();
		}
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
