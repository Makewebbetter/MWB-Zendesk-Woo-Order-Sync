<?php
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * The file defines the global plugin functions.
 *
 * @link       https://makewebbbetter.com/
 * @since      2.0.2
 *
 * @package    mwb-zendesk-woo-order-sync
 * @subpackage mwb-zendesk-woo-order-sync/Library
 */
/**
 * Update user tickets.
 *
 * @return void
 */
function update_user_ticket() {
	$update_from_nonce = isset( $_POST['nonce-for-update'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce-for-update'] ) ) : ' ' ;
	$update_check      = wp_verify_nonce( $update_from_nonce, 'zndsk_ticket_updates' );
	if ( $update_check ) {
		if ( isset( $_POST['update_ticket_all'] ) ) {
			$update_ticket_id      = isset( $_POST['mwb-ticket-no'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb-ticket-no'] ) ) : '';
			$update_ticket_comment = isset( $_POST['mwb-update-comment'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb-update-comment'] ) ) : '';
			$update_ticket_subject = isset( $_POST['mwb-update-subject'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb-update-subject'] ) ) : '';
			$update_ticket_email   = isset( $_POST['mwb-update-email'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb-update-email'] ) ) : '';
			$priority = 'Low';
			$requester_id = get_option( get_current_user_id());
			$ticket   = array(
				'ticket' => array(
					'subject'   => $update_ticket_subject,
					'comment'   => array(
						'body' => $update_ticket_comment,
						'requester_id' => $requester_id,
						'author_id'    => $requester_id,
					),
					'requester' => array(
						'name'     => 'user',
						'requester_id' => $requester_id,
						'email'    => $update_ticket_email,
						'priority' => $priority,
						'author_id'    => $requester_id,
					),
				),
			);
			$ticket            = wp_json_encode( $ticket );
			$zndsk_acc_details = get_option( 'mwb_zndsk_account_details' );

			$basic = base64_encode( $zndsk_acc_details['acc_email'] . '/token:' . $zndsk_acc_details['acc_api_token'] );
			$url   = $zndsk_acc_details['acc_url'] . 'api/v2/tickets/' . $update_ticket_id;
			$data  = wp_remote_request( $url,
				array(
					'headers' => array(
						'Content-Type'  => 'application/json',
						'Authorization' => 'Basic ' . $basic,
					),
					'body'      => $ticket,
					'method'    => 'PUT',
				)
			);
			if ( is_wp_error( $data ) ) {
				wc_add_notice( 'Comment Not Updated', 'error' );
			} else {
				wc_add_notice( 'Comment Updated', 'success' );
			}
		}
	}
}
/**
 * Create user tickets.
 *
 * @return void
 */
function create_user_ticket() {
	$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : ' ';
	$check = wp_verify_nonce( $nonce, 'zndsk_ticket_check' );
	if ( $check ) {
		if ( isset( $_POST['submit_ticket_all'] ) ) {
			$email    = isset( $_POST['mwb-create-email'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb-create-email'] ) ) : '';
			$comment  =  isset( $_POST['mwb-create-comment'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb-create-comment'] ) ) : '';
			$priority = 'Low';
			$subject  = isset( $_POST['mwb-create-subject'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb-create-subject'] ) ) : '';
			$ticket   = array(
				'ticket' => array(
					'subject'   => $subject,
					'comment'   => array(
						'body' => $comment,
					),
					'requester' => array(
						'name'     => 'user',
						'email'    => $email,
						'priority' => $priority,
					),
				),
			);
			$ticket            = wp_json_encode( $ticket );
			$zndsk_acc_details = get_option( 'mwb_zndsk_account_details' );

			$basic = base64_encode( $zndsk_acc_details['acc_email'] . '/token:' . $zndsk_acc_details['acc_api_token'] );
			$url   = $zndsk_acc_details['acc_url'] . 'api/v2/tickets.json';
			$data  = wp_remote_post( $url,
				array(
					'headers' => array(
						'Content-Type'  => 'application/json',
						'Authorization' => 'Basic ' . $basic,
					),
					'body'    => $ticket,
					'method'  => 'POST',
				)
			);
			if ( is_wp_error( $data ) ) {
				wc_add_notice( 'Ticket Not Created', 'error');
			} else {
				wc_add_notice( 'Ticket Created', 'success');
			}
		}
	}
}
/**
 * Get Zendesk Order Configuration options.
 * Saved/Default options.
 *
 * @since    2.0.2
 */
function mwb_zndskwoo_get_order_config_options() {

	$order_config_options = get_option( 'mwb_zndsk_order_config_options', 'not_saved' );

	$handled_order_config_options = array();

	if( 'not_saved' === $order_config_options ) {

		$handled_order_config_options = mwb_zndskwoo_default_order_config_options();
	}

	else {

		$handled_order_config_options['latest_orders_count'] = ! empty( $order_config_options['latest_orders_count'] ) ? $order_config_options['latest_orders_count'] : '20';

		$handled_order_config_options['source_kpi_fields'] = ! empty( $order_config_options['source_kpi_fields'] ) ? $order_config_options['source_kpi_fields'] : array();
		$handled_order_config_options['selected_kpi_fields'] = ! empty( $order_config_options['selected_kpi_fields'] ) ? $order_config_options['selected_kpi_fields'] : array();

		$handled_order_config_options['source_order_fields'] = ! empty( $order_config_options['source_order_fields'] ) ? $order_config_options['source_order_fields'] : array();
		$handled_order_config_options['selected_order_fields'] = ! empty( $order_config_options['selected_order_fields'] ) ? $order_config_options['selected_order_fields'] : array();
	}

	return $handled_order_config_options;
}

/**
 * Zendesk default Order Configuration options.
 *
 * @since    2.0.2
 */
function mwb_zndskwoo_default_order_config_options() {

	$default_order_config_options = array();

	$default_order_config_options['latest_orders_count'] = '20';

	// KPI fields.
	$all_kpi_fields = mwb_zndskwoo_order_config_get_all_kpi_fields();

	$default_order_config_options['selected_kpi_fields'] = array( 
		'total_order_count',
		'average_order_value', 
		'total_spend', 
	);

	$default_order_config_options['source_kpi_fields'] = array();

	foreach ( $all_kpi_fields as $field_key => $field_name ) {
		
		if( ! in_array( $field_key, $default_order_config_options['selected_kpi_fields'] ) ) {

			$default_order_config_options['source_kpi_fields'][] = $field_key;
		}
	}

	// Order fields.
	$all_order_fields = mwb_zndskwoo_order_config_get_all_order_fields();

	$default_order_config_options['selected_order_fields'] = array( 
		'order_date_created', 
		'payment_method_title', 
		'total' 
	);

	$default_order_config_options['source_order_fields'] = array();

	foreach ( $all_order_fields as $field_key => $field_name ) {
		
		if( ! in_array( $field_key, $default_order_config_options['selected_order_fields'] ) ) {

			$default_order_config_options['source_order_fields'][] = $field_key; 
		}
	}

	return $default_order_config_options;
}

/**
 * Zendesk Order Configuration.
 * Get all KPI fields.
 *
 * @since    2.0.2
 */
function mwb_zndskwoo_order_config_get_all_kpi_fields() {

	$kpi_fields = array(); // All KPI Fields.

	$kpi_fields['total_order_count']    	     		 = esc_html__( 'Total Order Count', 'zndskwoo' );
	$kpi_fields['average_order_value']         		     = esc_html__( 'Average Order Value', 'zndskwoo' );
	$kpi_fields['total_spend']         		     		 = esc_html__( 'Total Spend', 'zndskwoo' );
	$kpi_fields['last_purchase_date']         		   	 = esc_html__( 'Last Purchase', 'zndskwoo' );
	$kpi_fields['first_purchase_date']         		     = esc_html__( 'First Purchase', 'zndskwoo' );
	$kpi_fields['average_days_bw_purchase']         	 = esc_html__( 'Average Days between Purchase', 'zndskwoo' );

	return $kpi_fields;
}

/**
 * Zendesk Order Configuration.
 * Get all Order fields.
 *
 * @since    2.0.2
 */
function mwb_zndskwoo_order_config_get_all_order_fields() {

	$order_fields = array(); // All Order Fields.

	$order_fields['order_date_created']           	  	 = esc_html__( 'Order date', 'zndskwoo' );

	$order_fields['billing_customer_name']  		 	 = esc_html__( 'Billing Customer Name', 'zndskwoo' );
	$order_fields['billing_address_1']      		 	 = esc_html__( 'Billing Address 1', 'zndskwoo' );
	$order_fields['billing_address_2']      		 	 = esc_html__( 'Billing Address 2', 'zndskwoo' );
	$order_fields['billing_city']           		 	 = esc_html__( 'Billing City', 'zndskwoo' );
	$order_fields['billing_state']          		 	 = esc_html__( 'Billing State', 'zndskwoo' );
	$order_fields['billing_postcode']       		 	 = esc_html__( 'Billing Post Code', 'zndskwoo' );
	$order_fields['billing_country']        		 	 = esc_html__( 'Billing Country', 'zndskwoo' );
	$order_fields['billing_phone']          		 	 = esc_html__( 'Billing Phone', 'zndskwoo' );
	$order_fields['billing_company']        		 	 = esc_html__( 'Billing Company', 'zndskwoo' );

	$order_fields['shipping_customer_name'] 		 	 = esc_html__( 'Shipping Customer Name', 'zndskwoo' );
	$order_fields['shipping_address_1']     		 	 = esc_html__( 'Shipping Address 1', 'zndskwoo' );
	$order_fields['shipping_address_2']     		 	 = esc_html__( 'Shipping Address 2', 'zndskwoo' );
	$order_fields['shipping_city']          		 	 = esc_html__( 'Shipping City', 'zndskwoo' );
	$order_fields['shipping_state']         		 	 = esc_html__( 'Shipping State', 'zndskwoo' );
	$order_fields['shipping_postcode']      		 	 = esc_html__( 'Shipping Postcode', 'zndskwoo' );
	$order_fields['shipping_country']       		 	 = esc_html__( 'Shipping Country', 'zndskwoo' );
	$order_fields['shipping_company']       		 	 = esc_html__( 'Shipping Company', 'zndskwoo' );

	// Exact field keys from Order Data.
	$order_fields['payment_method_title']         		 = esc_html__( 'Payment Method', 'zndskwoo' );
	$order_fields['customer_ip_address']    		 	 = esc_html__( 'Customer IP Aaddress', 'zndskwoo' );
	$order_fields['currency']         			 		 = esc_html__( 'Currency', 'zndskwoo' );
	$order_fields['discount_total']          		 	 = esc_html__( 'Discount', 'zndskwoo' );
	$order_fields['discount_tax']      		 		 	 = esc_html__( 'Discount Tax', 'zndskwoo' );
	$order_fields['shipping_total']         		     = esc_html__( 'Shipping Amount', 'zndskwoo' );
	$order_fields['shipping_tax']     		 			 = esc_html__( 'Shipping Tax', 'zndskwoo' );
	$order_fields['total_tax']              		 	 = esc_html__( 'Order Tax', 'zndskwoo' );
	$order_fields['total']            		 		 	 = esc_html__( 'Order Total', 'zndskwoo' );

	return $order_fields;
}

/**
 * Get Customer data - KPI fields values.
 *
 * @since    2.0.2
 */
function mwb_zndskwoo_get_customer_kpi_fields_for_zendesk( $customer_email = '', $handled_order_config_options = array() ) {

	if( empty( $customer_email ) || empty( $handled_order_config_options ) ) {

		return false;
	}

	$all_kpi_fields = mwb_zndskwoo_order_config_get_all_kpi_fields();

	$selected_kpi_fields = $handled_order_config_options['selected_kpi_fields'];

	$all_kpi_fields_values = array();
	$zendesk_kpi_fields_array = array();

	$customer_orders = wc_get_orders(
		array(
			'numberposts' => -1, // All Orders as we have to calculate KPI metrics.
			'email'       => $customer_email,
			'post_status' => array_keys( wc_get_order_statuses() ), // All statuses for now, give select option later.
			'order'	  => 'DESC', // get last order first.

		)
	);

	$total_order_count = 0;
	$total_spend = 0;

	$counter = 0;

	if( ! empty( $customer_orders ) && is_array( $customer_orders ) ) {

		$total_order_count = count( $customer_orders );

		foreach ( $customer_orders as $key => $single_order ) {

			$order_total = $single_order->get_total();

			$total_spend += floatval( $order_total );
			
			// Retrieve last Order details when counter is eqaul to 0.
			if( ! $counter ) {

				$last_purchase_date =  $single_order->get_date_created()->date( 'M d, Y' );
			}

			// Retrieve first Order details.
			if( $counter === count( $customer_orders ) - 1 ) {

				$first_purchase_date =  $single_order->get_date_created()->date( 'M d, Y' );

			}

			$counter++;
		}

		$store_currency = get_woocommerce_currency();

		// Total Order count.
		$all_kpi_fields_values['total_order_count'] = $total_order_count;

		// Average Order Value.
		$all_kpi_fields_values['average_order_value'] = round( $total_spend / $total_order_count, 2 ) . ' ' . $store_currency;

		// Total Spend.
		$all_kpi_fields_values['total_spend'] = $total_spend . ' ' . $store_currency;

		// Last Purchase.
		$all_kpi_fields_values['last_purchase_date'] = $last_purchase_date;

		// First Purchase.
		$all_kpi_fields_values['first_purchase_date'] = $first_purchase_date;

		// Calculate Average days between Purchase.
		$adbp_first_order_date = new DateTime( $first_purchase_date );
		$adbp_last_order_date  = new DateTime( $last_purchase_date );
		$adbp_date_diff    	   = date_diff( $adbp_first_order_date, $adbp_last_order_date, true );

		$adbp_date_diff_days = $adbp_date_diff->days;

		$adbp = round( $adbp_date_diff_days / $total_order_count, 2 );

		// Average Days between Purchase.
		$all_kpi_fields_values['average_days_bw_purchase'] = $adbp . ' ' . esc_html( _n( 'day', 'days', $adbp, 'zndskwoo' ) );

		foreach ( $all_kpi_fields as $key => $title ) {
			
			if( in_array( $key, $selected_kpi_fields ) ) {

				$zendesk_kpi_fields_array[$key]['title'] = $title;
				$zendesk_kpi_fields_array[$key]['value'] =  isset( $all_kpi_fields_values[$key] ) ? $all_kpi_fields_values[$key] : '';
			}
		}
	}

	return $zendesk_kpi_fields_array;

	
}

/**
 * Get Customer data - Order fields values.
 *
 * @since    2.0.2
 */
function mwb_zndskwoo_get_customer_order_fields_for_zendesk( $customer_email = '', $handled_order_config_options = array() ) {

	if( empty( $customer_email ) || empty( $handled_order_config_options ) ) {

		return false;
	}

	$all_order_fields = mwb_zndskwoo_order_config_get_all_order_fields();

	$selected_order_fields = $handled_order_config_options['selected_order_fields'];

	$latest_orders_count = $handled_order_config_options['latest_orders_count'];

	$customer_orders = wc_get_orders(
		array(
			'numberposts' => $latest_orders_count,
			'email'       => $customer_email,
			'post_status' => array_keys( wc_get_order_statuses() ), // All statuses for now, give select option later.
			'order'	  => 'DESC', // get last order first.

		)
	);

	$zendesk_order_fields_array = array();

	if( ! empty( $customer_orders ) && is_array( $customer_orders ) ) {

		foreach ( $customer_orders as $key => $single_order ) {

			$single_order_id = $single_order->get_id();
			$single_order_status = $single_order->get_status();
			$single_order_data = $single_order->get_data();

			$single_order_zendesk_data = array();

			// Order ID.
			$single_order_zendesk_data['order_id'] = array(
				'title' => esc_html__( 'Order ID', 'zndskwoo' ),
				'value' => $single_order_id
			);

			// Order Status.
			$single_order_zendesk_data['order_status'] = array(
				'title' => esc_html__( 'Order Status', 'zndskwoo' ),
				'value' => wc_get_order_status_name( $single_order_status )
			);

			// Order Date created.
			if( in_array( 'order_date_created', $selected_order_fields ) ) {

				$single_order_zendesk_data['order_date_created'] = array(
					'title' => isset( $all_order_fields['order_date_created'] ) ? $all_order_fields['order_date_created'] : '',
					'value' => $single_order->get_date_created()->date( 'M d, Y' )
				);
			}

			// Order Billing details.
			if( in_array( 'billing_customer_name', $selected_order_fields ) ) {

				$single_order_zendesk_data['billing_customer_name'] = array(
					'title' => isset( $all_order_fields['billing_customer_name'] ) ? $all_order_fields['billing_customer_name'] : '',
					'value' => $single_order_data['billing']['first_name'] . ' ' . $single_order_data['billing']['last_name']
				);
			}
			if( in_array( 'billing_address_1', $selected_order_fields ) ) {

				$single_order_zendesk_data['billing_address_1'] = array(
					'title' => isset( $all_order_fields['billing_address_1'] ) ? $all_order_fields['billing_address_1'] : '',
					'value' => $single_order_data['billing']['address_1']
				);
			}
			if( in_array( 'billing_address_2', $selected_order_fields ) ) {

				$single_order_zendesk_data['billing_address_2'] = array(
					'title' => isset( $all_order_fields['billing_address_2'] ) ? $all_order_fields['billing_address_2'] : '',
					'value' => $single_order_data['billing']['address_2']
				);
			}
			if( in_array( 'billing_city', $selected_order_fields ) ) {

				$single_order_zendesk_data['billing_city'] = array(
					'title' => isset( $all_order_fields['billing_city'] ) ? $all_order_fields['billing_city'] : '',
					'value' => $single_order_data['billing']['city']
				);
			}
			if( in_array( 'billing_state', $selected_order_fields ) ) {

				$single_order_zendesk_data['billing_state'] = array(
					'title' => isset( $all_order_fields['billing_state'] ) ? $all_order_fields['billing_state'] : '',
					'value' => $single_order_data['billing']['state']
				);
			}
			if( in_array( 'billing_postcode', $selected_order_fields ) ) {

				$single_order_zendesk_data['billing_postcode'] = array(
					'title' => isset( $all_order_fields['billing_postcode'] ) ? $all_order_fields['billing_postcode'] : '',
					'value' => $single_order_data['billing']['postcode']
				);
			}
			if( in_array( 'billing_country', $selected_order_fields ) ) {

				$single_order_zendesk_data['billing_country'] = array(
					'title' => isset( $all_order_fields['billing_country'] ) ? $all_order_fields['billing_country'] : '',
					'value' => $single_order_data['billing']['country']
				);
			}
			if( in_array( 'billing_phone', $selected_order_fields ) ) {

				$single_order_zendesk_data['billing_phone'] = array(
					'title' => isset( $all_order_fields['billing_phone'] ) ? $all_order_fields['billing_phone'] : '',
					'value' => $single_order_data['billing']['phone']
				);
			}
			if( in_array( 'billing_company', $selected_order_fields ) ) {

				$single_order_zendesk_data['billing_company'] = array(
					'title' => isset( $all_order_fields['billing_company'] ) ? $all_order_fields['billing_company'] : '',
					'value' => $single_order_data['billing']['company']
				);
			}

			// Order Shipping details.
			if( in_array( 'shipping_customer_name', $selected_order_fields ) ) {

				$single_order_zendesk_data['shipping_customer_name'] = array(
					'title' => isset( $all_order_fields['shipping_customer_name'] ) ? $all_order_fields['shipping_customer_name'] : '',
					'value' => $single_order_data['shipping']['first_name'] . ' ' . $single_order_data['shipping']['last_name']
				);
			}
			if( in_array( 'shipping_address_1', $selected_order_fields ) ) {

				$single_order_zendesk_data['shipping_address_1'] = array(
					'title' => isset( $all_order_fields['shipping_address_1'] ) ? $all_order_fields['shipping_address_1'] : '',
					'value' => $single_order_data['shipping']['address_1']
				);
			}
			if( in_array( 'shipping_address_2', $selected_order_fields ) ) {

				$single_order_zendesk_data['shipping_address_2'] = array(
					'title' => isset( $all_order_fields['shipping_address_2'] ) ? $all_order_fields['shipping_address_2'] : '',
					'value' => $single_order_data['shipping']['address_2']
				);
			}
			if( in_array( 'shipping_city', $selected_order_fields ) ) {

				$single_order_zendesk_data['shipping_city'] = array(
					'title' => isset( $all_order_fields['shipping_city'] ) ? $all_order_fields['shipping_city'] : '',
					'value' => $single_order_data['shipping']['city']
				);
			}
			if( in_array( 'shipping_state', $selected_order_fields ) ) {

				$single_order_zendesk_data['shipping_state'] = array(
					'title' => isset( $all_order_fields['shipping_state'] ) ? $all_order_fields['shipping_state'] : '',
					'value' => $single_order_data['shipping']['state']
				);
			}
			if( in_array( 'shipping_postcode', $selected_order_fields ) ) {

				$single_order_zendesk_data['shipping_postcode'] = array(
					'title' => isset( $all_order_fields['shipping_postcode'] ) ? $all_order_fields['shipping_postcode'] : '',
					'value' => $single_order_data['shipping']['postcode']
				);
			}
			if( in_array( 'shipping_country', $selected_order_fields ) ) {

				$single_order_zendesk_data['shipping_country'] = array(
					'title' => isset( $all_order_fields['shipping_country'] ) ? $all_order_fields['shipping_country'] : '',
					'value' => $single_order_data['shipping']['country']
				);
			}
			if( in_array( 'shipping_phone', $selected_order_fields ) ) {

				$single_order_zendesk_data['shipping_phone'] = array(
					'title' => isset( $all_order_fields['shipping_phone'] ) ? $all_order_fields['shipping_phone'] : '',
					'value' => $single_order_data['shipping']['phone']
				);
			}
			if( in_array( 'shipping_company', $selected_order_fields ) ) {

				$single_order_zendesk_data['shipping_company'] = array(
					'title' => isset( $all_order_fields['shipping_company'] ) ? $all_order_fields['shipping_company'] : '',
					'value' => $single_order_data['shipping']['company']
				);
			}

			// Payment method.
			if( in_array( 'payment_method_title', $selected_order_fields ) ) {

				$single_order_zendesk_data['payment_method_title'] = array(
					'title' => isset( $all_order_fields['payment_method_title'] ) ? $all_order_fields['payment_method_title'] : '',
					'value' => $single_order_data['payment_method_title']
				);
			}

			// Payment Method.
			if( in_array( 'payment_method_title', $selected_order_fields ) ) {

				$single_order_zendesk_data['payment_method_title'] = array(
					'title' => isset( $all_order_fields['payment_method_title'] ) ? $all_order_fields['payment_method_title'] : '',
					'value' => $single_order_data['payment_method_title']
				);
			}

			// Customer IP Aaddress.
			if( in_array( 'customer_ip_address', $selected_order_fields ) ) {

				$single_order_zendesk_data['customer_ip_address'] = array(
					'title' => isset( $all_order_fields['customer_ip_address'] ) ? $all_order_fields['customer_ip_address'] : '',
					'value' => $single_order_data['customer_ip_address']
				);
			}

			// Currency.
			if( in_array( 'currency', $selected_order_fields ) ) {

				$single_order_zendesk_data['currency'] = array(
					'title' => isset( $all_order_fields['currency'] ) ? $all_order_fields['currency'] : '',
					'value' => $single_order_data['currency']
				);
			}

			// Discount.
			if( in_array( 'discount_total', $selected_order_fields ) ) {

				$single_order_zendesk_data['discount_total'] = array(
					'title' => isset( $all_order_fields['discount_total'] ) ? $all_order_fields['discount_total'] : '',
					'value' => $single_order_data['discount_total']
				);
			}

			// Discount Tax.
			if( in_array( 'discount_tax', $selected_order_fields ) ) {

				$single_order_zendesk_data['discount_tax'] = array(
					'title' => isset( $all_order_fields['discount_tax'] ) ? $all_order_fields['discount_tax'] : '',
					'value' => $single_order_data['discount_tax']
				);
			}

			// Shipping Amount.
			if( in_array( 'shipping_total', $selected_order_fields ) ) {

				$single_order_zendesk_data['shipping_total'] = array(
					'title' => isset( $all_order_fields['shipping_total'] ) ? $all_order_fields['shipping_total'] : '',
					'value' => $single_order_data['shipping_total']
				);
			}

			// Shipping Tax.
			if( in_array( 'shipping_tax', $selected_order_fields ) ) {

				$single_order_zendesk_data['shipping_tax'] = array(
					'title' => isset( $all_order_fields['shipping_tax'] ) ? $all_order_fields['shipping_tax'] : '',
					'value' => $single_order_data['shipping_tax']
				);
			}

			// Order Tax.
			if( in_array( 'total_tax', $selected_order_fields ) ) {

				$single_order_zendesk_data['total_tax'] = array(
					'title' => isset( $all_order_fields['total_tax'] ) ? $all_order_fields['total_tax'] : '',
					'value' => $single_order_data['total_tax']
				);
			}

			// Order Total.
			if( in_array( 'total', $selected_order_fields ) ) {

				$single_order_zendesk_data['total'] = array(
					'title' => isset( $all_order_fields['total'] ) ? $all_order_fields['total'] : '',
					'value' => $single_order_data['total']
				);
			}

			$zendesk_order_fields_array[] = $single_order_zendesk_data;
		}
	}

	return $zendesk_order_fields_array;
}

