<?php
/**
 * Exit if accessed directly
 *
 * @package mwb-zendesk-woo-order-sync/Library
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * This file manages to send order details to Zendesk.
 *
 * @link       https://makewebbbetter.com/
 * @since      1.0.0
 *
 * @package    mwb-zendesk-woo-order-sync
 * @subpackage mwb-zendesk-woo-order-sync/Library
 */

if ( ! class_exists( 'MWB_ZENDESK_Manager' ) ) {
	/**
	 * This file manages to send order details to Zendesk.
	 *
	 * @link       https://makewebbbetter.com/
	 * @since      1.0.0
	 *
	 * @package    mwb-zendesk-woo-order-sync
	 * @subpackage mwb-zendesk-woo-order-sync/Library
	 */
	class MWB_ZENDESK_Manager {
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

			$this->mwb_zndsk_set_locale();
			$this->mwb_load_dependecy();

			add_action( 'admin_init', array( $this, 'mwb_zndsk_save_account_details' ) );
		}
		/**
		 * Define the locale for this plugin for internationalization.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function mwb_zndsk_set_locale() {

			$this->mwb_zndsk_load_plugin_textdomain();
		}
		/**
		 * Load file dependency.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function mwb_load_dependecy() {
			require_once MWB_ZENDESK_DIR . '/Library/class-mwb-zendesk-settings.php';
		}
		/**
		 * Load the plugin text domain for translation.
		 *
		 * @since    1.0.0
		 */
		public function mwb_zndsk_load_plugin_textdomain() {

			$var = load_plugin_textdomain(
				'zndskwoo',
				false,
				dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
			);
		}
		/**
		 * Register routes for the order details class.
		 *
		 * @since    1.0.0
		 */
		public function mwb_zndsk_register_routes() {

			register_rest_route(
				'zndskwoo', '/order_details', array(
					array(
						'methods'             => WP_REST_Server::CREATABLE,
						'callback'            => array( $this, 'mwb_zndsk_woo_order_sync' ),
						'permission_callback' => 'mwb_zndsk_get_items_permissions_check',
					),
				)
			);
		}
		/**
		 * Send zendesk client's details
		 *
		 * @since    1.0.0
		 */
		public function send_clients_details() {

			$status          = false;
			$suggestion_sent = get_option( 'zendesk_suggestions_sent', false );
			if ( $suggestion_sent ) {
				$status = 'already-sent';
				return $status;
			}
			$email     = get_option( 'admin_email', '' );
			$admin     = get_user_by( 'email', $email );
			$admin_id  = $admin->ID;
			$firstname = get_user_meta( $admin_id, 'first_name', true );
			$lastname  = get_user_meta( $admin_id, 'last_name', true );
			$site_url  = ! empty( $admin->user_url ) ? $admin->user_url : '';
			$to        = sanitize_email( 'integrations@makewebbetter.com' );
			$subject   = "Zendesk Woo Customer's Details";
			$headers   = array( 'Content-Type: text/html; charset=UTF-8' );
			$message   = 'First Name:- ' . $firstname . '<br/>';
			$message  .= 'Last Name:- ' . $lastname . '<br/>';
			$message  .= 'Admin Email:- ' . $email . '<br/>';
			$message  .= 'Site Url:- ' . $site_url . '<br/>';
			$status    = wp_mail( $to, $subject, $message, $headers );
			return $status;
		}
		/**
		 * Sends response to zendesk.
		 *
		 * @since    1.0.0
		 * @param array $request   requested parameters from zendesk.
		 * @return array  $data      order details
		 */
		public function mwb_zndsk_woo_order_sync( $request ) {

			$post_params = $request->get_params();

			$response = array();

			$email = $post_params['email'];

			if ( isset( $email ) && ! empty( $email ) ) {

				$total_orders = wc_get_orders(
					array(
						'numberposts' => 100, // Fetch 100 latest Orders.
						'email'       => $email,

					)
				);

				if ( ! empty( $total_orders ) && is_array( $total_orders ) ) {
					foreach ( $total_orders as $key => $single_order ) {

						$order         = wc_get_order( $total_orders[ $key ]->id );
						$order_data    = $order->get_data();
						$customer_name = $order_data['billing']['first_name'] . ' ' . $order_data['billing']['last_name'];

						$data['order_fields'][] = array(
							'customer_name'      => $customer_name,
							'order_id'           => $order_data['id'],
							'order_add1'         => $order_data['billing']['address_1'],
							'order_add2'         => $order_data['billing']['address_2'],
							'city'               => $order_data['billing']['city'],
							'state'              => $order_data['billing']['state'],
							'postalcode'         => $order_data['billing']['postcode'],
							'country'            => $order_data['billing']['country'],
							'order_currency'     => $order_data['currency'],
							'order_date_created' => $order_data['date_created']->date( 'Y-m-d H:i:s' ),
							'order_total'        => $order_data['total'],
							'order_status'       => $order_data['status'],
						);

						$data['custom_fields'][ $order_data['id'] ] = '';
					}
				} else {

					$data['order_fields'][] = array(
						'message' => __( 'No orders found', 'zndskwoo' ),
					);
				}
				$data = wp_json_encode( $data );
				return $data;
			}
		}
		/**
		 * Saving zendesk account details.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		public function mwb_zndsk_save_account_details() {

			if( ! empty( $_REQUEST['zndsk_secure_check'] ) ) {

				$sanitized_nonce = sanitize_text_field( wp_unslash( $_REQUEST['zndsk_secure_check'] ) );

				$nonce_value = wc_get_var( $sanitized_nonce );
			}
			else {
				$nonce_value = '';
			}

			if ( isset( $nonce_value ) && wp_verify_nonce( $nonce_value, 'zndsk_submit' ) ) {

				if ( isset( $_POST['zndsk_setting_save_btn'] ) ) { // Input var okay.

					if ( isset( $_POST['zndsk_setting_zendesk_user_email'] ) ) {
						$email = sanitize_text_field( wp_unslash( $_POST['zndsk_setting_zendesk_user_email'] ) );// Input var okay.
					}
					if ( isset( $_POST['zndsk_setting_zendesk_url'] ) ) {
						$website = sanitize_text_field( wp_unslash( $_POST['zndsk_setting_zendesk_url'] ) );// Input var okay.
					}
					
					$api_token = ! empty( $_POST['zndsk_setting_zendesk_api_token'] ) ? sanitize_text_field( wp_unslash( $_POST['zndsk_setting_zendesk_api_token'] ) ) : ''; // Input var okay.

					$emailerror   = '';
					$websiteerror = '';

					if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
						$emailerror = true;
					}

					if ( ! filter_var( $website, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED ) ) {
						$websiteerror = true;
					}

					update_option( 'zendesk_email_error', $emailerror );
					update_option( 'zendesk_url_error', $websiteerror );

					$zendesk_acc_details = array(
						'acc_url'  		=> $website,
						'acc_email' 	=> $email,
						'acc_api_token' => $api_token,
					);

					if ( true == $emailerror || true == $websiteerror ) {

						delete_option( 'mwb_zndsk_account_details' );
					} else {
						update_option( 'mwb_zndsk_account_details', $zendesk_acc_details );
					}
				}
			}
		}
		/**
		 * Getting email of contact from zendesk.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		public static function mwb_fetch_useremail() {
			global $post;

			$zndsk_acc_details = get_option( 'mwb_zndsk_account_details' );

			if( empty( $zndsk_acc_details['acc_email'] ) ) {

				return 'empty_zndsk_account_details';
			}

			$order = wc_get_order( $post->ID );

			$url = $zndsk_acc_details['acc_url'] . '/api/v2/users/search.json?query=' . $order->get_billing_email();

			$basic = '';

			if( ! empty( $zndsk_acc_details['acc_api_token'] ) ) {

				$basic = base64_encode( $zndsk_acc_details['acc_email'] . '/token:' . $zndsk_acc_details['acc_api_token'] );
			}

			else if( ! empty( $zndsk_acc_details['acc_pass'] ) ) {

				$basic = base64_encode( $zndsk_acc_details['acc_email'] . ':' . $zndsk_acc_details['acc_pass'] );
			}

			else {

				return 'empty_zndsk_account_details';
			}
			
			$headers = array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Basic ' . $basic,
			);

			$response = wp_remote_get( $url, array(
				'headers'   => $headers,
				'sslverify' => false,
			));

			if ( is_wp_error( $response ) ) {
				$status_code = $response->get_error_code();
				$res_message = $response->get_error_message();
			} else {
				$status_code = wp_remote_retrieve_response_code( $response );
				$res_message = wp_remote_retrieve_response_message( $response );
			}

			if ( 200 == $status_code ) {

				$api_body = wp_remote_retrieve_body( $response );

				if ( $api_body ) {
					$api_body = json_decode( $api_body );
				}
			} else {
				return 'zndsk_api_error';
			}

			$users = $api_body->users;

			foreach ( $users as $key => $value ) {
				if ( $value->email == $order->get_billing_email() ) {
					$response = self::mwb_fetch_user_tickets( $value->id );
					return $response;
				}
			}
		}
		/**
		 * Getting user tickets from zendesk.
		 *
		 * @since    1.0.0
		 * @param array $user_id   user id of contact.
		 * @return array  $data      order details
		 */
		public static function mwb_fetch_user_tickets( $user_id ) {

			$zndsk_acc_details = get_option( 'mwb_zndsk_account_details' );

			$url = $zndsk_acc_details['acc_url'] . '/api/v2/users/' . $user_id . '/tickets/requested.json';

			if( ! empty( $zndsk_acc_details['acc_api_token'] ) ) {

				$basic = base64_encode( $zndsk_acc_details['acc_email'] . '/token:' . $zndsk_acc_details['acc_api_token'] );
			}

			else if( ! empty( $zndsk_acc_details['acc_pass'] ) ) {

				$basic = base64_encode( $zndsk_acc_details['acc_email'] . ':' . $zndsk_acc_details['acc_pass'] );
			}

			$headers = array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Basic ' . $basic,
			);

			$response = wp_remote_get( $url, array(
				'headers'   => $headers,
				'sslverify' => false,
			) );

			if ( is_wp_error( $response ) ) {
				$status_code = $response->get_error_code();
				$res_message = $response->get_error_message();
			} else {
				$status_code = wp_remote_retrieve_response_code( $response );
				$res_message = wp_remote_retrieve_response_message( $response );
			}

			if ( 200 == $status_code ) {

				$api_body = wp_remote_retrieve_body( $response );

				if ( $api_body ) {
					$api_body = json_decode( $api_body );
				}
			}

			$tickets       = $api_body->tickets;
			$ticket_fields = array();
			$count         = 0;
			foreach ( $tickets as $key => $single_ticket ) {
				$ticket_fields[] = array(
					'id'          => $single_ticket->id,
					'subject'     => $single_ticket->subject,
					'description' => $single_ticket->description,
					'status'      => $single_ticket->status,
				);
			}

			return $ticket_fields;
		}
	}
}
