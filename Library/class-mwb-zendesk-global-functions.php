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
 * Get Zendesk Order Configuration options.
 * Saved/Default options.
 *
 * @since    2.0.2
 */
function mwb_zndskwoo_get_order_config_options() {

	$order_config_options = get_option( 'mwb_zndsk_order_config_options', 'not_saved' );

	$handled_order_config_options = array();

	if( 'not_saved' == $order_config_options ) {

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
		'average_order_value', 
		'total_spend', 
		'customer_lifetime_value' 
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

	$kpi_fields['average_order_value']         		     = esc_html__( 'Average Order Value', 'zndskwoo' );
	$kpi_fields['total_spend']         		     		 = esc_html__( 'Total Spend', 'zndskwoo' );
	$kpi_fields['customer_lifetime_value']         		 = esc_html__( 'Customer Lifetime Value', 'zndskwoo' );
	$kpi_fields['last_purchase']         		    	 = esc_html__( 'Last Purchase', 'zndskwoo' );
	$kpi_fields['first_purchase']         		     	 = esc_html__( 'First Purchase', 'zndskwoo' );
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
	$order_fields['shipping_address_1']     		 	 = esc_html__( 'Shipping Address 2', 'zndskwoo' );
	$order_fields['shipping_address_2']     		 	 = esc_html__( 'Shipping Address 1', 'zndskwoo' );
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
	$order_fields['shipping_total']         		     = esc_html__( 'Shipping amount', 'zndskwoo' );
	$order_fields['shipping_tax']     		 			 = esc_html__( 'Shipping tax', 'zndskwoo' );
	$order_fields['total_tax']              		 	 = esc_html__( 'Order Tax', 'zndskwoo' );
	$order_fields['total']            		 		 	 = esc_html__( 'Order Total', 'zndskwoo' );

	return $order_fields;
}
