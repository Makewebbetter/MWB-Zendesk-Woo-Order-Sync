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

// KPI Fields - Start.
$source_kpi_fields = $handled_order_config_options['source_kpi_fields'];
$selected_kpi_fields = $handled_order_config_options['selected_kpi_fields'];
$default_selected_kpi_fields = $handled_order_config_options['default_selected_kpi_fields'];

$kpi_fields = array(); // All KPI Fields.

$kpi_fields['average_order_value']         		     = esc_html__( 'Average Order Value', 'zndskwoo' );
$kpi_fields['total_spend']         		     		 = esc_html__( 'Total Spend', 'zndskwoo' );
$kpi_fields['customer_lifetime_value']         		 = esc_html__( 'Customer Lifetime Value', 'zndskwoo' );
$kpi_fields['last_purchase']         		    	 = esc_html__( 'Last Purchase', 'zndskwoo' );
$kpi_fields['first_purchase']         		     	 = esc_html__( 'First Purchase', 'zndskwoo' );
$kpi_fields['average_days_bw_purchase']         	 = esc_html__( 'Average Days between Purchase', 'zndskwoo' );
// KPI Fields - End.

// Order Fields - Start.
$source_order_fields = $handled_order_config_options['source_order_fields'];
$selected_order_fields = $handled_order_config_options['selected_order_fields'];
$default_selected_order_fields = $handled_order_config_options['default_selected_order_fields'];

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
// Order Fields - End.
?>

<div class="zndsk_setting_ticket_wrapper">
	<div class="zndsk_setting_wrapper mwb-zndsk-order-config-options">
		<h2><?php esc_html_e( __( 'Order Configuration Settings', 'zndskwoo' ) ); ?></h2>
		
		<!-- Settings saved notice. -->
		<div class="mwb-zndsk-order-config-notice settings-saved notice notice-success is-dismissible" style="display: none;"> 
			<p><?php _e( 'Selected Options Saved.', 'zndskwoo' ); ?></p>
		</div>
		<!-- Settings not saved notice. -->
		<div class="mwb-zndsk-order-config-notice settings-not-saved notice notice-warning is-dismissible" style="display: none;"> 
			<p><?php _e( 'No Changes.', 'zndskwoo' ); ?></p>
		</div>

		<form id="mwb-zndsk-order-config-form" method="POST">
		<table class="zndsk_setting_table">
			<tbody>
				<tr>
					<td class="zendesk-column zendesk-col-left  zendesk-url-column"><strong><?php esc_html_e( 'Latest Orders Count', 'zndskwoo' ); ?></strong></td>
					<td class="zendesk-column zendesk-col-right"><input required="required" type="number" min="1" max="100" id="mwb-zndsk-latest-orders-count" value="<?php echo $latest_orders_count; ?>"></td>
				</tr>

				<tr>
					<td class="zendesk-column zendesk-col-left  zendesk-url-column"><strong><?php esc_html_e( 'KPI Fields', 'zndskwoo' ); ?></strong></td>
					<td class="zendesk-column zendesk-col-right">
							
						<div class="mwb-zndsk-order-config-fields__drag-drop mwb-zndsk-clearfix">
	                        <div class="mwb-zndsk-order-config-fields__drag mwb-zndsk-clearfix">

	                            <ul id="mwb-zndsk-kpi-fields-dvsource" class="mwb-zndsk-field-drop kpi-fields">

	                                <?php 

	                                if( ! empty( $source_kpi_fields ) && is_array( $source_kpi_fields ) ) {

	                                    foreach ( $kpi_fields as $field_key => $field_name ) {

	                                        if( in_array( $field_key, $source_kpi_fields ) ) : ?>

	                                        	<li class="mwb-zndsk-field-drag kpi-fields" data-name='<?php echo $field_key; ?>'><?php echo $field_name;?></li>

	                                        <?php endif;
	                                    }
	                                }

	                                // Show all fields ( except default selected fields ) initially when not saved.
	                                elseif( empty( $selected_kpi_fields ) ) {

	                                    foreach ( $kpi_fields as $field_key => $field_name ) {

	                                        if( ! in_array( $field_key, $default_selected_kpi_fields ) ) : ?>

	                                        	<li class="mwb-zndsk-field-drag kpi-fields" data-name='<?php echo $field_key; ?>'><?php echo $field_name;?></li>

	                                        <?php endif;
	                                    }
	                                }

	                                ?>
	                            </ul>
	                        </div>
	                        <div class="mwb-zndsk-order-config-fields__drop mwb-zndsk-clearfix">

	                            <ul id="mwb-zndsk-kpi-fields-dvdest" class="mwb-zndsk-field-drop kpi-fields">
	                                
	                                <?php 

	                                if( ! empty( $selected_kpi_fields ) && is_array( $selected_kpi_fields ) ) {

	                                    foreach ( $kpi_fields as $field_key => $field_name ) {

	                                        if( in_array( $field_key, $selected_kpi_fields ) ) : ?>

	                                         <li class="mwb-zndsk-field-drag kpi-fields" data-name='<?php echo $field_key; ?>'><?php echo $field_name;?></li>

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
					<td class="zendesk-column zendesk-col-left  zendesk-url-column"><strong><?php esc_html_e( 'Order Fields', 'zndskwoo' ); ?></strong></td>
					<td class="zendesk-column zendesk-col-right">
							
						<div class="mwb-zndsk-order-config-fields__drag-drop mwb-zndsk-clearfix">
	                        <div class="mwb-zndsk-order-config-fields__drag mwb-zndsk-clearfix">

	                            <ul id="mwb-zndsk-order-fields-dvsource" class="mwb-zndsk-field-drop order-fields">

	                                <?php 

	                                if( ! empty( $source_order_fields ) && is_array( $source_order_fields ) ) {

	                                    foreach ( $order_fields as $field_key => $field_name ) {

	                                        if( in_array( $field_key, $source_order_fields ) ) : ?>

	                                        	<li class="mwb-zndsk-field-drag order-fields" data-name='<?php echo $field_key; ?>'><?php echo $field_name;?></li>

	                                        <?php endif;
	                                    }
	                                }

	                                // Show all fields ( except default selected fields ) initially when not saved.
	                               elseif( empty( $selected_order_fields ) ) {

	                                    foreach ( $order_fields as $field_key => $field_name ) {

	                                        if( ! in_array( $field_key, $default_selected_order_fields ) ) : ?>

	                                        	<li class="mwb-zndsk-field-drag order-fields" data-name='<?php echo $field_key; ?>'><?php echo $field_name;?></li>

	                                        <?php endif;
	                                    }
	                                }

	                                ?>
	                            </ul>
	                        </div>
	                        <div class="mwb-zndsk-order-config-fields__drop mwb-zndsk-clearfix">

	                            <ul id="mwb-zndsk-order-fields-dvdest" class="mwb-zndsk-field-drop order-fields">
	                                
	                                <?php 

	                                if( ! empty( $selected_order_fields ) && is_array( $selected_order_fields ) ) {

	                                    foreach ( $order_fields as $field_key => $field_name ) {

	                                        if( in_array( $field_key, $selected_order_fields ) ) : ?>

	                                         <li class="mwb-zndsk-field-drag order-fields" data-name='<?php echo $field_key; ?>'><?php echo $field_name;?></li>

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
