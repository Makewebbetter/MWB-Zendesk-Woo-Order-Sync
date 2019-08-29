<?php
/**
 * Exit if accessed directly
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
 * @package    zendesk-woocommerce-order-sync
 * @subpackage zendesk-woocommerce-order-sync/Library
 */

if ( ! class_exists ( 'MWB_ZENDESK_Manager' ) ) {
	/**
	* This file manages to send order details to Zendesk.
	*
	* @link       https://makewebbbetter.com/
	* @since      1.0.0
	*
	* @package    zendesk-woocommerce-order-sync
	* @subpackage zendesk-woocommerce-order-sync/Library
	*/
	
	class MWB_ZENDESK_Manager{
		
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

		public function __construct() {
			
			$this->endpoint = get_option('mwb_zndsk_endpoint','zndskwoo');
			$this->mwb_zndsk_set_locale();
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
		 * Load the plugin text domain for translation.
		 *
		 * @since    1.0.0
		 */

		public function mwb_zndsk_load_plugin_textdomain() {

			
			$var = load_plugin_textdomain(
				'zndskwoo',
				false,
				dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/language/'
			);
		}
		/**
		 * Register routes for the order details class.
		 *
		 * @since    1.0.0
		 */

		public function mwb_zndsk_register_routes() {
			
			register_rest_route( $this->endpoint, '/order_details' , array(
				array(
				'methods' => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'mwb_zndsk_woo_order_sync' ),
				'permission_callback' => 'mwb_zndsk_get_items_permissions_check'
				)
			) );
		}
		/**
		 * Sends response to zendesk.
		 *
		 * @since    1.0.0
		 * @param array   $request   requested parameters from zendesk
		 * @return array  $data      order details
		 */

		public function mwb_zndsk_woo_order_sync( $request ) {

			$post_params = $request->get_params();
	
			$response = array();

			$email = $post_params['email'];

			if( isset( $email ) && !empty( $email ) ) {

				$total_orders = wc_get_orders(
					array (
						'numberposts' => 5,
						'email'       => $email

					) 
				);
				if( !empty( $total_orders && isset( $total_orders ) ) ) {
					foreach ( $total_orders as $key => $single_order ) {

						$order = wc_get_order( $total_orders[$key]->id );
						$order_data = $order->get_data();
						$customer_name = $order_data['billing']['first_name'] ." ". $order_data['billing']['last_name'];

						$data[] = array(
							"customer_name" => $customer_name,
							"order_id" => $order_data['id'],
							"order_add1" => $order_data['billing']['address_1'],
							"order_add2" => $order_data['billing']['address_2'],
							"city"		 => $order_data['billing']['city'],
							"state"		 => $order_data['billing']['state'],
							"postalcode" => $order_data['billing']['postcode'],
							"country"	 => $order_data['billing']['country'],
							"order_currency" => $order_data['currency'],
							"order_date_created" => $order_data['date_created']->date('Y-m-d H:i:s'),
							"order_total" => $order_data['total'],
							"order_status" => $order_data['status'],
						);	
					}				
				}
				else {

					$data[] = array(
						"message" => __("No orders found", "zndskwoo")
					);
				}
				$data = json_encode( $data );
				return $data;
			}
		}
	}
}	