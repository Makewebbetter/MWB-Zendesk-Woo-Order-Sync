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
 * Get Zendesk Order Configurations options.
 * Saved/Default options.
 *
 * @since    2.0.2
 */
function mwb_zndskwoo_get_order_config_options() {

	$order_config_options = get_option( 'mwb_zndsk_order_config_options', array() );

	$latest_orders_count = ! empty( $order_config_options['latest_orders_count'] ) ? $order_config_options['latest_orders_count'] : '20';

	$source_order_fields = ! empty( $order_config_options['source_order_fields'] ) ? $order_config_options['source_order_fields'] : array();
	$default_selected_order_fields = array( 'order_date_created', 'payment_method_title', 'total' );
	$selected_order_fields = ! empty( $order_config_options['selected_order_fields'] ) ? $order_config_options['selected_order_fields'] : $default_selected_order_fields;

	$source_kpi_fields = ! empty( $order_config_options['source_kpi_fields'] ) ? $order_config_options['source_kpi_fields'] : array();
	$default_selected_kpi_fields = array( 'average_order_value', 'total_spend', 'customer_lifetime_value' );
	$selected_kpi_fields = ! empty( $order_config_options['selected_kpi_fields'] ) ? $order_config_options['selected_kpi_fields'] : $default_selected_kpi_fields;

	$handled_order_config_options = array();

	$handled_order_config_options['latest_orders_count'] = $latest_orders_count;
	$handled_order_config_options['source_kpi_fields'] = $source_kpi_fields;
	$handled_order_config_options['selected_kpi_fields'] = $selected_kpi_fields;
	$handled_order_config_options['default_selected_kpi_fields'] = $default_selected_kpi_fields;
	$handled_order_config_options['source_order_fields'] = $source_order_fields;
	$handled_order_config_options['selected_order_fields'] = $selected_order_fields;
	$handled_order_config_options['default_selected_order_fields'] = $default_selected_order_fields;
	
	return $handled_order_config_options;
}
