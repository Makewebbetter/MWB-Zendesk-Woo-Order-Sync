<?php
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * This file includes Zendesk Order configuration settings.
 *
 * @link       https://makewebbbetter.com/
 * @since      2.0.2
 *
 * @package    mwb-zendesk-woo-order-sync
 * @subpackage mwb-zendesk-woo-order-sync/admin-templates
 */

$handled_order_config_options = mwb_zndskwoo_get_order_config_options();

$latest_orders_count = $handled_order_config_options['latest_orders_count'];
$source_fields = $handled_order_config_options['source_fields'];
$default_selected_fields = $handled_order_config_options['default_selected_fields'];
$selected_source_fields = $handled_order_config_options['selected_source_fields'];

// All Order Fields.
$single_order_fields = array();

$single_order_fields['order_date_created']           = esc_html__( 'Order date', 'zndskwoo' );

$single_order_fields['billing_customer_name']  		 = esc_html__( 'Billing Customer Name', 'zndskwoo' );
$single_order_fields['billing_address_1']      		 = esc_html__( 'Billing Address 1', 'zndskwoo' );
$single_order_fields['billing_address_2']      		 = esc_html__( 'Billing Address 2', 'zndskwoo' );
$single_order_fields['billing_city']           		 = esc_html__( 'Billing City', 'zndskwoo' );
$single_order_fields['billing_state']          		 = esc_html__( 'Billing State', 'zndskwoo' );
$single_order_fields['billing_postcode']       		 = esc_html__( 'Billing Post Code', 'zndskwoo' );
$single_order_fields['billing_country']        		 = esc_html__( 'Billing Country', 'zndskwoo' );
$single_order_fields['billing_phone']          		 = esc_html__( 'Billing Phone', 'zndskwoo' );
$single_order_fields['billing_company']        		 = esc_html__( 'Billing Company', 'zndskwoo' );

$single_order_fields['shipping_customer_name'] 		 = esc_html__( 'Shipping Customer Name', 'zndskwoo' );
$single_order_fields['shipping_address_1']     		 = esc_html__( 'Shipping Address 2', 'zndskwoo' );
$single_order_fields['shipping_address_2']     		 = esc_html__( 'Shipping Address 1', 'zndskwoo' );
$single_order_fields['shipping_city']          		 = esc_html__( 'Shipping City', 'zndskwoo' );
$single_order_fields['shipping_state']         		 = esc_html__( 'Shipping State', 'zndskwoo' );
$single_order_fields['shipping_postcode']      		 = esc_html__( 'Shipping Postcode', 'zndskwoo' );
$single_order_fields['shipping_country']       		 = esc_html__( 'Shipping Country', 'zndskwoo' );
$single_order_fields['shipping_company']       		 = esc_html__( 'Shipping Company', 'zndskwoo' );

// Exact field keys from Order Data.
$single_order_fields['payment_method_title']         = esc_html__( 'Payment Method', 'zndskwoo' );
$single_order_fields['customer_ip_address']    		 = esc_html__( 'Customer IP Aaddress', 'zndskwoo' );
$single_order_fields['currency']         			 = esc_html__( 'Currency', 'zndskwoo' );
$single_order_fields['discount_total']          		 = esc_html__( 'Discount', 'zndskwoo' );
$single_order_fields['discount_tax']      		 = esc_html__( 'Discount Tax', 'zndskwoo' );
$single_order_fields['shipping_total']         		 = esc_html__( 'Shipping amount', 'zndskwoo' );
$single_order_fields['shipping_tax']     		 = esc_html__( 'Shipping tax', 'zndskwoo' );
$single_order_fields['total_tax']              		 = esc_html__( 'Order Tax', 'zndskwoo' );
$single_order_fields['total']            		 = esc_html__( 'Order Total', 'zndskwoo' );

?>
<div class="zndsk_setting_ticket_wrapper">
	<div class="zndsk_setting_wrapper mwb-zndsk-order-config-options">
		<h2><?php esc_html_e( __( 'Order Configuration Settings', 'zndskwoo' ) ); ?></h2>
		<form id="mwb-zndsk-order-config-form" method="POST">
		<table class="zndsk_setting_table">
			<tbody>
				<tr>
					<td class="zendesk-column zendesk-col-left  zendesk-url-column"><strong><?php esc_html_e( 'Latest Orders Count', 'zndskwoo' ); ?></strong></td>
					<td class="zendesk-column zendesk-col-right"><input required="required" type="number" min="1" max="100" id="mwb-zndsk-latest-orders-count" value="<?php echo $latest_orders_count; ?>"></td>
				</tr>

				<tr>
					<td class="zendesk-column zendesk-col-left  zendesk-url-column"><strong><?php esc_html_e( 'Order Fields', 'zndskwoo' ); ?></strong></td>
					<td class="zendesk-column zendesk-col-right">
							
						<div class="mwb-zndsk-order-config-fields__drag-drop mwb-zndsk-clearfix">
	                        <div class="mwb-zndsk-order-config-fields__drag mwb-zndsk-clearfix">

	                            <ul id="mwb-zndsk-dvsource" class="mwb-zndsk-field-drop">

	                                <?php 

	                                if( ! empty( $source_fields ) && is_array( $source_fields ) ) {

	                                    foreach ( $single_order_fields as $field_key => $field_name ) {

	                                        if( in_array( $field_key, $source_fields ) ) : ?>

	                                        	<li class="mwb-zndsk-field-drag" data-name='<?php echo $field_key; ?>'><?php echo $field_name;?></li>

	                                        <?php endif;
	                                    }
	                                }

	                                // Show all fields ( except default selected fields ) when source fields array is empty.
	                                else {

	                                    foreach ( $single_order_fields as $field_key => $field_name ) {

	                                        if( ! in_array( $field_key, $default_selected_fields ) ) : ?>

	                                        	<li class="mwb-zndsk-field-drag" data-name='<?php echo $field_key; ?>'><?php echo $field_name;?></li>

	                                        <?php endif;
	                                    }
	                                }

	                                ?>
	                            </ul>
	                        </div>
	                        <div class="mwb-zndsk-order-config-fields__drop mwb-zndsk-clearfix">

	                            <ul id="mwb-zndsk-dvdest" class="mwb-zndsk-field-drop">
	                                
	                                <?php 

	                                if( ! empty( $selected_source_fields ) && is_array( $selected_source_fields ) ) {

	                                    foreach ( $single_order_fields as $field_key => $field_name ) {

	                                        if( in_array( $field_key, $selected_source_fields ) ) : ?>

	                                         <li class="mwb-zndsk-field-drag" data-name='<?php echo $field_key; ?>'><?php echo $field_name;?></li>

	                                        <?php endif;
	                                    }
	                                }

	                                ?>
	                            </ul>
	                        </div>
	                        <img src="<?php echo MWB_ZENDESK_DIR_URL . 'assets/images/switch.png'?>" alt="" class="mwb-zndsk-switch-icon">
	                    </div> 

					</td>
				</tr>

				<tr>
					<td colspan="2" class="zendesk-submit">
						<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Options', 'zndskwoo' ); ?></button>
					</td>
				</tr>
			</tbody>
		</table></form>
	</div>
</div>
<?php
